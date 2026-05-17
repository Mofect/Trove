/**
 * Single product AJAX add-to-cart + sync block mini-cart + open drawer.
 */
( function () {
	'use strict';

	/**
	 * Sticksy clones siblings for layout; strip painted UI (e.g. jQuery UI slider handles) from dummies.
	 *
	 * @param {Element|Document} root Container to search within.
	 */
	function neutralizeSticksyDummies( root ) {
		var scope = root || document;
		if ( ! scope.querySelectorAll ) {
			return;
		}
		scope.querySelectorAll( '.sticksy-dummy-node' ).forEach( function ( dummy ) {
			dummy.setAttribute( 'aria-hidden', 'true' );
			dummy.style.setProperty( 'display', 'none', 'important' );
			dummy.style.setProperty( 'height', '0', 'important' );
			dummy.style.setProperty( 'overflow', 'hidden', 'important' );
			dummy.style.setProperty( 'opacity', '0', 'important' );
			dummy.style.setProperty( 'visibility', 'hidden', 'important' );
			dummy.style.setProperty( 'pointer-events', 'none', 'important' );
		} );
	}

	/* pushCartToBlocksStore() uses a short 0ms burst, then a few rAF, then 2ms polls. */

	function getConfig() {
		return window.troveProductAjaxCart || {};
	}

	function getAjaxUrl() {
		if ( getConfig().ajaxUrl ) {
			return getConfig().ajaxUrl;
		}
		if (
			window.wc_add_to_cart_params &&
			typeof window.wc_add_to_cart_params.wc_ajax_url === 'string'
		) {
			return window.wc_add_to_cart_params.wc_ajax_url.replace( '%%endpoint%%', 'add_to_cart' );
		}
		return window.location.origin + '/?wc-ajax=add_to_cart';
	}

	function getStoreCartUrl() {
		if ( getConfig().storeCartUrl ) {
			return getConfig().storeCartUrl;
		}
		return window.location.origin + '/wp-json/wc/store/v1/cart';
	}

	function getStoreApiHeaders() {
		var headers = {
			'X-Requested-With': 'XMLHttpRequest',
		};
		if ( getConfig().storeApiNonce ) {
			/* WooCommerce Store API: cookie session + optional nonce. */
			headers.Nonce = getConfig().storeApiNonce;
			headers[ 'X-WC-Store-API-Nonce' ] = getConfig().storeApiNonce;
		}
		return headers;
	}

	/**
	 * `response.json()` throws on empty or whitespace-only bodies; Woo sometimes returns 200 with no body.
	 */
	function readAddToCartResponseAsJson( response ) {
		return response.text().then( function ( text ) {
			var raw = ( text == null ? '' : String( text ) )
				.replace( /^\uFEFF/, '' )
				.trim();
			if ( ! response.ok ) {
				var hint = raw ? raw.replace( /[\r\n]+/g, ' ' ).slice( 0, 200 ) : '';
				throw new Error(
					'Add to cart failed (HTTP ' + response.status + ')' + ( hint ? ' ' + hint : '' )
				);
			}
			if ( ! raw ) {
				return {};
			}
			try {
				return JSON.parse( raw );
			} catch ( err ) {
				throw new Error(
					'Add to cart: response was not valid JSON. First bytes: ' + raw.slice( 0, 80 )
				);
			}
		} );
	}

	/**
	 * Block mini-cart may mount inside open shadow roots.
	 */
	function queryAllInShadow( root, selector, acc ) {
		if ( ! acc ) {
			acc = [];
		}
		if ( ! root || ! root.querySelectorAll ) {
			return acc;
		}
		var direct = root.querySelectorAll( selector );
		if ( direct && direct.length ) {
			direct.forEach( function ( el ) {
				acc.push( el );
			} );
		}
		var all = root.querySelectorAll( '*' );
		if ( all ) {
			all.forEach( function ( el ) {
				if ( el.shadowRoot ) {
					queryAllInShadow( el.shadowRoot, selector, acc );
				}
			} );
		}
		return acc;
	}

	function findAll( selector ) {
		return queryAllInShadow( document, selector, [] );
	}

	function getFormLineQuantity( form ) {
		if ( ! form ) {
			return 1;
		}
		var qtyInput = form.querySelector( 'input.qty' );
		if ( ! qtyInput || ! qtyInput.value ) {
			return 1;
		}
		var n = parseInt( qtyInput.value, 10 );
		return isNaN( n ) || n < 1 ? 1 : n;
	}

	function parseNumberOrDefault( value, fallback ) {
		var n = parseFloat( value );
		return isNaN( n ) ? fallback : n;
	}

	function clampQtyByBounds( next, input ) {
		var min = parseNumberOrDefault( input.getAttribute( 'min' ), 1 );
		var maxAttr = input.getAttribute( 'max' );
		var max = maxAttr && maxAttr !== '' ? parseNumberOrDefault( maxAttr, null ) : null;
		var v = Math.max( min, next );
		if ( max !== null ) {
			v = Math.min( v, max );
		}
		return v;
	}

	function formatQtyValue( value, step ) {
		var s = String( step || 1 );
		var dot = s.indexOf( '.' );
		if ( dot === -1 ) {
			return String( Math.round( value ) );
		}
		var decimals = s.length - dot - 1;
		return value.toFixed( decimals );
	}

	function updateSingleProductQtyInput( input, deltaSign ) {
		if ( ! input ) {
			return;
		}
		var step = parseNumberOrDefault( input.getAttribute( 'step' ), 1 );
		if ( step <= 0 ) {
			step = 1;
		}
		var current = parseNumberOrDefault( input.value, parseNumberOrDefault( input.getAttribute( 'min' ), 1 ) );
		var next = current + step * deltaSign;
		next = clampQtyByBounds( next, input );
		input.value = formatQtyValue( next, step );
		input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
		input.dispatchEvent( new Event( 'input', { bubbles: true } ) );
	}

	function enhanceSingleProductQtyField( qtyWrap ) {
		if ( ! qtyWrap || qtyWrap.classList.contains( 'trove-qty-enhanced' ) ) {
			return;
		}
		var input = qtyWrap.querySelector( 'input.qty' );
		if ( ! input ) {
			return;
		}
		var minus = document.createElement( 'button' );
		minus.type = 'button';
		minus.className = 'trove-qty-btn trove-qty-btn--minus';
		minus.setAttribute( 'aria-label', 'Decrease quantity' );

		var plus = document.createElement( 'button' );
		plus.type = 'button';
		plus.className = 'trove-qty-btn trove-qty-btn--plus';
		plus.setAttribute( 'aria-label', 'Increase quantity' );

		qtyWrap.insertBefore( minus, input );
		qtyWrap.appendChild( plus );
		qtyWrap.classList.add( 'trove-qty-enhanced' );

		minus.addEventListener( 'click', function () {
			updateSingleProductQtyInput( input, -1 );
		} );
		plus.addEventListener( 'click', function () {
			updateSingleProductQtyInput( input, 1 );
		} );
	}

	function initSingleProductQtyButtons() {
		document.querySelectorAll(
			'body.single-product form.cart .quantity, body.single-product form.cart.grouped_form .quantity'
		).forEach( enhanceSingleProductQtyField );
	}

	function initSingleProductSummarySticksy() {
		if ( ! document.body.classList.contains( 'single-product' ) ) {
			return;
		}
		var summary =
			document.querySelector( '.single-product div.product .summary.entry-summary' ) ||
			document.querySelector( '.single-product div.product .summary' );
		if ( ! summary ) {
			return;
		}
		if ( summary.getAttribute( 'data-trove-sticksy-init' ) === '1' ) {
			return;
		}
		var Sticksy = window.Sticksy;
		if ( typeof Sticksy !== 'function' ) {
			return;
		}
		/* Matches single-product responsive rules in assets/css/shop.css */
		var mqSummaryMobile = window.matchMedia( '(max-width: 782px)' );
		function isProductSummaryMobileViewport() {
			return mqSummaryMobile.matches === true;
		}
		function bindMediaQueryChange( mq, handler ) {
			if ( mq.addEventListener ) {
				mq.addEventListener( 'change', handler );
				return;
			}
			if ( mq.addListener ) {
				mq.addListener( handler );
			}
		}
		var inner = summary.querySelector( ':scope > .trove-product-summary-sticky-inner' );
		if ( ! inner ) {
			inner = document.createElement( 'div' );
			inner.className = 'trove-product-summary-sticky-inner';
			while ( summary.firstChild ) {
				inner.appendChild( summary.firstChild );
			}
			summary.appendChild( inner );
		}

		var productRoot = summary.closest( 'div.product' );
		function syncSummaryStickyRange() {
			if ( isProductSummaryMobileViewport() ) {
				summary.style.minHeight = '';
				return;
			}
			if ( ! productRoot ) {
				return;
			}
			var gallery =
				productRoot.querySelector( '.woocommerce-product-gallery' ) ||
				productRoot.querySelector( 'div.images' );
			if ( ! gallery ) {
				return;
			}
			var galleryHeight = Math.ceil( gallery.getBoundingClientRect().height );
			var innerHeight = Math.ceil( inner.getBoundingClientRect().height );
			var minHeight = Math.max( galleryHeight, innerHeight );
			if ( minHeight > 0 ) {
				summary.style.minHeight = minHeight + 'px';
			}
		}
		syncSummaryStickyRange();

		var sticky = new Sticksy( inner, {
			topSpacing: 80,
			listen: true,
		} );
		neutralizeSticksyDummies( summary );
		var summaryStickyOn = ! isProductSummaryMobileViewport();
		/* Constructor always starts enabled ({ enabled:false } is ignored due to sticksy internals). */
		if ( ! summaryStickyOn ) {
			sticky.disable();
		}
		summary._troveSticksy = sticky;
		summary.setAttribute( 'data-trove-sticksy-init', '1' );

		function applyProductSummaryStickyForViewport() {
			var on = ! isProductSummaryMobileViewport();
			if ( on !== summaryStickyOn ) {
				summaryStickyOn = on;
				if ( on ) {
					syncSummaryStickyRange();
					sticky.enable();
				} else {
					sticky.disable();
					summary.style.minHeight = '';
					summary.classList.remove( 'trove-product-summary-sticky--fixed' );
				}
				return;
			}
			if ( on ) {
				syncSummaryStickyRange();
				sticky.hardRefresh();
				neutralizeSticksyDummies( summary );
			}
		}
		applyProductSummaryStickyForViewport();
		bindMediaQueryChange( mqSummaryMobile, applyProductSummaryStickyForViewport );

		sticky.onStateChanged = function ( state ) {
			if ( isProductSummaryMobileViewport() ) {
				return;
			}
			summary.classList.toggle( 'trove-product-summary-sticky--fixed', state === 'fixed' );
			neutralizeSticksyDummies( summary );
		};
		window.addEventListener( 'resize', syncSummaryStickyRange );
		window.addEventListener( 'load', syncSummaryStickyRange );
		if ( window.jQuery ) {
			window.jQuery( document.body ).on( 'found_variation reset_image', function () {
				window.setTimeout( syncSummaryStickyRange, 0 );
			} );
		}
	}

	/**
	 * Sticky WooCommerce shop filter sidebar (.widget-area--woocommerce-sidebar inside .shop-main).
	 */
	function initWooShopSidebarSticksy() {
		var widgetArea = document.querySelector(
			'.shop-main .widget-area.widget-area--woocommerce-sidebar'
		);
		var products = document.querySelector( '.shop-main .trove-products' );
		if ( ! widgetArea || ! products ) {
			return;
		}
		if ( widgetArea.getAttribute( 'data-trove-sticksy-sidebar-init' ) === '1' ) {
			return;
		}
		var Sticksy = window.Sticksy;
		if ( typeof Sticksy !== 'function' ) {
			return;
		}
		var mqSidebarStack = window.matchMedia( '(max-width: 900px)' );
		function isShopSidebarStackedViewport() {
			return mqSidebarStack.matches === true;
		}
		function bindMediaQueryChange( mq, handler ) {
			if ( mq.addEventListener ) {
				mq.addEventListener( 'change', handler );
				return;
			}
			if ( mq.addListener ) {
				mq.addListener( handler );
			}
		}
		var inner = widgetArea.querySelector( ':scope > .trove-woo-sidebar-sticky-inner' );
		if ( ! inner ) {
			inner = document.createElement( 'div' );
			inner.className = 'trove-woo-sidebar-sticky-inner';
			while ( widgetArea.firstChild ) {
				inner.appendChild( widgetArea.firstChild );
			}
			widgetArea.appendChild( inner );
		}

		function syncShopSidebarStickyRange() {
			if ( isShopSidebarStackedViewport() ) {
				widgetArea.style.minHeight = '';
				return;
			}
			var productsHeight = Math.ceil( products.offsetHeight );
			var innerHeight = Math.ceil( inner.offsetHeight );
			var minHeight = Math.max( productsHeight, innerHeight );
			if ( minHeight > 0 ) {
				widgetArea.style.minHeight = minHeight + 'px';
			}
		}
		syncShopSidebarStickyRange();

		var sticky = new Sticksy( inner, {
			topSpacing: 80,
			listen: true,
		} );
		neutralizeSticksyDummies( widgetArea );
		var sidebarStickyOn = ! isShopSidebarStackedViewport();
		if ( ! sidebarStickyOn ) {
			sticky.disable();
		}
		widgetArea._troveSticksySidebar = sticky;
		widgetArea.setAttribute( 'data-trove-sticksy-sidebar-init', '1' );

		function applyShopSidebarStickyForViewport() {
			var on = ! isShopSidebarStackedViewport();
			if ( on !== sidebarStickyOn ) {
				sidebarStickyOn = on;
				if ( on ) {
					syncShopSidebarStickyRange();
					sticky.enable();
				} else {
					sticky.disable();
					widgetArea.style.minHeight = '';
					widgetArea.classList.remove( 'trove-woo-sidebar-sticky--fixed' );
				}
				return;
			}
			if ( on ) {
				syncShopSidebarStickyRange();
				sticky.hardRefresh();
				neutralizeSticksyDummies( widgetArea );
			}
		}
		applyShopSidebarStickyForViewport();
		bindMediaQueryChange( mqSidebarStack, applyShopSidebarStickyForViewport );

		function refreshSidebarPriceSliders() {
			if ( ! window.jQuery ) {
				return;
			}
			window.jQuery( widgetArea ).find( '.price_slider' ).each( function () {
				var $slider = window.jQuery( this );
				if ( $slider.hasClass( 'ui-slider' ) && $slider.slider ) {
					$slider.slider( 'option', 'refresh' );
				}
			} );
		}

		sticky.onStateChanged = function ( state ) {
			if ( isShopSidebarStackedViewport() ) {
				return;
			}
			widgetArea.classList.toggle( 'trove-woo-sidebar-sticky--fixed', state === 'fixed' );
			neutralizeSticksyDummies( widgetArea );
			window.requestAnimationFrame( refreshSidebarPriceSliders );
		};
		window.addEventListener( 'resize', syncShopSidebarStickyRange );
		window.addEventListener( 'load', syncShopSidebarStickyRange );

		if ( typeof ResizeObserver !== 'undefined' ) {
			var ro = new ResizeObserver( function () {
				window.requestAnimationFrame( syncShopSidebarStickyRange );
			} );
			ro.observe( products );
		}
	}

	function initSingleProductSliderThumbCarousel( scope ) {
		if ( ! document.body.classList.contains( 'single-product' ) ) {
			return;
		}

		var roots;
		if ( scope && scope.querySelectorAll ) {
			roots = [ scope ];
		} else {
			roots = [ document ];
		}

		roots.forEach( function ( root ) {
			root
				.querySelectorAll( '.woocommerce-product-gallery.trove-woo-gallery--slider' )
				.forEach( function ( gallery ) {
					var thumbs =
						gallery.querySelector( 'ol.flex-control-thumbs' ) ||
						gallery.querySelector( 'ul.flex-control-thumbs' );
					if ( ! thumbs ) {
						return;
					}

					var wrap = thumbs.parentElement;
					if ( ! wrap || ! wrap.classList.contains( 'trove-woo-slider-thumbs-wrap' ) ) {
						wrap = document.createElement( 'div' );
						wrap.className = 'trove-woo-slider-thumbs-wrap';
						thumbs.parentNode.insertBefore( wrap, thumbs );
						wrap.appendChild( thumbs );
					}

					var prev = wrap.querySelector( '.trove-woo-slider-thumbs-nav--prev' );
					var next = wrap.querySelector( '.trove-woo-slider-thumbs-nav--next' );
					if ( ! prev ) {
						prev = document.createElement( 'button' );
						prev.type = 'button';
						prev.className = 'trove-woo-slider-thumbs-nav trove-woo-slider-thumbs-nav--prev';
						prev.setAttribute( 'aria-label', 'Scroll thumbnails left' );
						prev.textContent = '‹';
						wrap.appendChild( prev );
					}
					if ( ! next ) {
						next = document.createElement( 'button' );
						next.type = 'button';
						next.className = 'trove-woo-slider-thumbs-nav trove-woo-slider-thumbs-nav--next';
						next.setAttribute( 'aria-label', 'Scroll thumbnails right' );
						next.textContent = '›';
						wrap.appendChild( next );
					}

					if ( wrap.getAttribute( 'data-trove-thumb-carousel-init' ) === '1' ) {
						return;
					}
					wrap.setAttribute( 'data-trove-thumb-carousel-init', '1' );

					function syncThumbWrapHeightToViewport() {
						if (
							! gallery.classList.contains( 'trove-woo-slider-pos-left' ) &&
							! gallery.classList.contains( 'trove-woo-slider-pos-right' )
						) {
							wrap.style.height = '';
							wrap.style.minHeight = '';
							wrap.style.maxHeight = '';
							return;
						}
						var viewport = gallery.querySelector( '.flex-viewport' );
						if ( ! viewport ) {
							return;
						}
						var viewportHeight = Math.ceil( viewport.getBoundingClientRect().height );
						var activeImg = gallery.querySelector( '.flex-active-slide img' );
						var imageHeight = activeImg
							? Math.ceil( activeImg.getBoundingClientRect().height )
							: 0;
						if ( imageHeight <= 0 && viewportHeight <= 0 ) {
							return;
						}
						var h =
							imageHeight > 0 && viewportHeight > 0
								? Math.min( imageHeight, viewportHeight )
								: Math.max( imageHeight, viewportHeight );
						if ( h <= 0 ) {
							return;
						}
						/* Hard cap to avoid transient oversized measurements. */
						h = Math.min( h, Math.max( 260, Math.floor( window.innerHeight * 0.8 ) ) );
						wrap.style.height = h + 'px';
						wrap.style.minHeight = h + 'px';
						wrap.style.maxHeight = h + 'px';
					}

					function getStep() {
						var first = thumbs.querySelector( 'li' );
						if ( ! first ) {
							return Math.max( 1, Math.round( thumbs.clientWidth / 2 ) );
						}
						var w = first.getBoundingClientRect().width;
						return w > 0 ? w * 4 : Math.max( 1, Math.round( thumbs.clientWidth / 2 ) );
					}

					function syncNav() {
						var max = Math.max( 0, thumbs.scrollWidth - thumbs.clientWidth );
						var atStart = thumbs.scrollLeft <= 1;
						var atEnd = thumbs.scrollLeft >= max - 1;
						var noOverflow = max <= 1;
						prev.hidden = noOverflow;
						next.hidden = noOverflow;
						prev.disabled = atStart || noOverflow;
						next.disabled = atEnd || noOverflow;
					}

					function nudgeThumbRailForEdgeClick( li ) {
						if ( ! li ) {
							return;
						}
						var first = thumbs.querySelector( 'li' );
						if ( ! first ) {
							return;
						}
						var firstRect = first.getBoundingClientRect();
						var itemWidth = firstRect.width;
						var itemHeight = firstRect.height;
						if ( itemWidth <= 0 || itemHeight <= 0 ) {
							return;
						}
						var railRect = thumbs.getBoundingClientRect();
						var liRect = li.getBoundingClientRect();
						var isVertical =
							thumbs.scrollHeight > thumbs.clientHeight + 1 &&
							thumbs.clientHeight >= thumbs.clientWidth;

						if ( isVertical ) {
							var bottomEdgeThreshold = railRect.bottom - itemHeight * 0.35;
							var topEdgeThreshold = railRect.top + itemHeight * 0.35;
							var nudgeY = itemHeight * 1.5;
							if ( liRect.bottom > bottomEdgeThreshold ) {
								thumbs.scrollBy( { top: nudgeY, behavior: 'smooth' } );
								return;
							}
							if ( liRect.top < topEdgeThreshold ) {
								thumbs.scrollBy( { top: -nudgeY, behavior: 'smooth' } );
							}
							return;
						}

						var rightEdgeThreshold = railRect.right - itemWidth * 0.35;
						var leftEdgeThreshold = railRect.left + itemWidth * 0.35;
						var nudgeX = itemWidth * 1.5;
						if ( liRect.right > rightEdgeThreshold ) {
							thumbs.scrollBy( { left: nudgeX, behavior: 'smooth' } );
							return;
						}
						if ( liRect.left < leftEdgeThreshold ) {
							thumbs.scrollBy( { left: -nudgeX, behavior: 'smooth' } );
						}
					}

					prev.addEventListener( 'click', function () {
						thumbs.scrollBy( { left: -getStep(), behavior: 'smooth' } );
					} );
					next.addEventListener( 'click', function () {
						thumbs.scrollBy( { left: getStep(), behavior: 'smooth' } );
					} );
					thumbs.addEventListener( 'click', function ( event ) {
						var target = event.target;
						if ( ! target || ! target.closest ) {
							return;
						}
						var li = target.closest( 'li' );
						if ( ! li || ! thumbs.contains( li ) ) {
							return;
						}
						window.setTimeout( function () {
							nudgeThumbRailForEdgeClick( li );
						}, 0 );
					} );
					thumbs.addEventListener( 'scroll', syncNav, { passive: true } );
					window.addEventListener( 'resize', syncNav );
					window.setTimeout( syncNav, 0 );
					window.addEventListener( 'resize', syncThumbWrapHeightToViewport );
					window.addEventListener( 'load', syncThumbWrapHeightToViewport );
					window.setTimeout( syncThumbWrapHeightToViewport, 0 );
					window.setTimeout( syncThumbWrapHeightToViewport, 120 );
					window.setTimeout( syncThumbWrapHeightToViewport, 320 );
					gallery
						.querySelectorAll( '.woocommerce-product-gallery__image img' )
						.forEach( function ( img ) {
							img.addEventListener( 'load', syncThumbWrapHeightToViewport );
						} );
					if ( typeof ResizeObserver !== 'undefined' ) {
						var viewport = gallery.querySelector( '.flex-viewport' );
						if ( viewport ) {
							var ro = new ResizeObserver( syncThumbWrapHeightToViewport );
							ro.observe( viewport );
						}
					}
					if ( window.jQuery ) {
						window.jQuery( gallery ).on(
							'woocommerce_gallery_init_zoom woocommerce_gallery_reset_slide_position',
							function () {
								window.setTimeout( syncThumbWrapHeightToViewport, 0 );
							}
						);
					}
				} );
		} );
	}

	function readBadgeCountFromDom() {
		var badges = findAll( '.wc-block-mini-cart__badge' );
		if ( ! badges.length ) {
			return null;
		}
		var t = ( badges[ 0 ].textContent || '' ).replace( /[^\d]/g, '' );
		if ( ! t ) {
			return 0;
		}
		var num = parseInt( t, 10 );
		return isNaN( num ) ? null : num;
	}

	var CART_SNAPSHOT_KEY = 'trove_cart_snapshot';

	function persistCartSnapshot( count, cartHash ) {
		try {
			window.localStorage.setItem(
				CART_SNAPSHOT_KEY,
				JSON.stringify( {
					count: Math.max( 0, Math.floor( count || 0 ) ),
					cartHash: cartHash || '',
					ts: Date.now(),
				} )
			);
		} catch ( e ) {
			/* localStorage blocked/private mode: ignore */
		}
	}

	function readCartSnapshot() {
		try {
			var raw = window.localStorage.getItem( CART_SNAPSHOT_KEY );
			if ( ! raw ) {
				return null;
			}
			var parsed = JSON.parse( raw );
			if ( ! parsed || typeof parsed !== 'object' ) {
				return null;
			}
			var count = parseInt( parsed.count, 10 );
			if ( isNaN( count ) || count < 0 ) {
				return null;
			}
			return { count: count, cartHash: parsed.cartHash || '' };
		} catch ( e ) {
			return null;
		}
	}

	function readCountFromStore() {
		if ( ! window.wp || ! wp.data || ! wp.data.select ) {
			return null;
		}
		try {
			var sel = wp.data.select( 'wc/store/cart' );
			if ( ! sel ) {
				return null;
			}
			var cart = null;
			if ( typeof sel.getCartData === 'function' ) {
				cart = sel.getCartData();
			} else if ( typeof sel.getCart === 'function' ) {
				cart = sel.getCart();
			}
			if ( ! cart || typeof cart !== 'object' ) {
				return null;
			}
			if ( typeof cart.items_count === 'number' ) {
				return cart.items_count;
			}
			if ( typeof cart.itemsCount === 'number' ) {
				return cart.itemsCount;
			}
			if ( Array.isArray( cart.items ) ) {
				return cart.items.reduce( function ( sum, it ) {
					return sum + ( it && it.quantity ? Number( it.quantity ) : 0 );
				}, 0 );
			}
		} catch ( e ) {
			return null;
		}
		return null;
	}

	function readBestKnownCartCount() {
		var fromStore = readCountFromStore();
		if ( fromStore !== null && ! isNaN( fromStore ) ) {
			return Math.max( 0, Math.floor( fromStore ) );
		}
		var fromDom = readBadgeCountFromDom();
		if ( fromDom !== null && ! isNaN( fromDom ) ) {
			return Math.max( 0, Math.floor( fromDom ) );
		}
		var snap = readCartSnapshot();
		if ( snap && typeof snap.count === 'number' ) {
			return Math.max( 0, Math.floor( snap.count ) );
		}
		return 0;
	}

	function hydrateBadgeFromCartSnapshot() {
		var fromStore = readCountFromStore();
		if ( fromStore !== null && ! isNaN( fromStore ) ) {
			setBadgeCount( fromStore );
			persistCartSnapshot( fromStore, '' );
			return;
		}
		var snap = readCartSnapshot();
		if ( ! snap ) {
			return;
		}
		setBadgeCount( snap.count );
	}

	function syncSnapshotFromCurrentBadge() {
		var n = readBadgeCountFromDom();
		if ( n === null || isNaN( n ) ) {
			return;
		}
		persistCartSnapshot( n, '' );
	}

	function setBadgeAndSnapshot( count, cartHash ) {
		var n = Math.max( 0, Math.floor( count || 0 ) );
		setBadgeCount( n );
		persistCartSnapshot( n, cartHash || '' );
	}

	function reconcileBadgeFromLiveState() {
		var fromStore = readCountFromStore();
		if ( fromStore !== null && ! isNaN( fromStore ) ) {
			setBadgeAndSnapshot( fromStore, '' );
			return;
		}
		var fromDom = readBadgeCountFromDom();
		if ( fromDom !== null && ! isNaN( fromDom ) ) {
			setBadgeAndSnapshot( fromDom, '' );
		}
	}

	function applyOptimisticBadgeDelta( delta ) {
		var base = readBestKnownCartCount();
		setBadgeAndSnapshot( base + delta, '' );
	}

	function initMiniCartRemoveOptimisticBadge() {
		document.addEventListener(
			'click',
			function ( event ) {
				if ( ! event.isTrusted ) {
					return;
				}
				var target = event.target;
				if ( ! target || ! target.closest ) {
					return;
				}
				var removeBtn = target.closest( '.wc-block-cart-item__remove-link, .remove_from_cart_button' );
				if ( ! removeBtn ) {
					return;
				}
				applyOptimisticBadgeDelta( -1 );
			},
			true
		);
	}

	function setBadgeCount( count ) {
		var n = Math.max( 0, Math.floor( count ) );
		findAll( '.wc-block-mini-cart__badge' ).forEach( function ( badge ) {
			badge.textContent = String( n );
			badge.style.display = n > 0 ? '' : 'none';
			if ( 'removeAttribute' in badge ) {
				badge.removeAttribute( 'hidden' );
			}
		} );
	}

	/**
	 * Before Store API returns: nudge count + open drawer (blocks theme often has no useful fragments).
	 */
	function onAddToCartResponseImmediate( data, form ) {
		var qty = getFormLineQuantity( form );
		var prevStore = readCountFromStore();
		var hasFragments = !! (
			data &&
			data.fragments &&
			typeof data.fragments === 'object' &&
			Object.keys( data.fragments ).length
		);
		replaceFragments( data && data.fragments ? data.fragments : null );
		var after = readBadgeCountFromDom();
		if ( after === null || isNaN( after ) ) {
			after = 0;
		}
		/*
		 * Never base math on stale snapshot value.
		 * Prefer live wp.data cart count; if unavailable, use a safe floor (qty) to avoid runaway increments.
		 */
		if ( ! hasFragments || ( prevStore !== null && ! isNaN( prevStore ) && after === prevStore ) ) {
			var next = prevStore !== null && ! isNaN( prevStore ) ? ( prevStore + qty ) : Math.max( qty, after );
			setBadgeAndSnapshot( next, data && data.cart_hash ? data.cart_hash : '' );
		} else {
			setBadgeAndSnapshot( after, data && data.cart_hash ? data.cart_hash : '' );
		}
		/* Do not open the drawer here: without Store API + receiveCart, panel shows stale data. */
	}

	function getItemsCountFromCart( cart ) {
		if ( ! cart || typeof cart !== 'object' ) {
			return 0;
		}
		if ( typeof cart.items_count === 'number' ) {
			return cart.items_count;
		}
		if ( typeof cart.itemsCount === 'number' ) {
			return cart.itemsCount;
		}
		if ( Array.isArray( cart.items ) ) {
			return cart.items.reduce( function ( sum, it ) {
				return sum + ( it && it.quantity ? Number( it.quantity ) : 0 );
			}, 0 );
		}
		return 0;
	}

	/**
	 * Push Store API response into `wc/store/cart` (when wp.data is ready).
	 */
	function tryReceiveCartOnce( cart ) {
		if ( ! window.wp || ! wp.data || ! wp.data.dispatch ) {
			return false;
		}
		var dispatch;
		try {
			dispatch = wp.data.dispatch( 'wc/store/cart' );
		} catch ( e ) {
			return false;
		}
		if ( ! dispatch ) {
			return false;
		}
		/* Do not use invalidateResolutionForStore() here: it re-fetches async and lags the drawer. */
		if ( typeof dispatch.receiveCart === 'function' ) {
			dispatch.receiveCart( cart );
			return true;
		}
		return false;
	}

	/**
	 * Push full Store API cart into wp.data; blocks mini-cart reads this for drawer list.
	 * Fast path: many same-tick follow-ups, then a few rAF, then 2ms poll.
	 */
	function pushCartToBlocksStore( cart, done ) {
		if ( tryReceiveCartOnce( cart ) ) {
			done( true );
			return;
		}
		var t = 0;
		function sameTickBurst() {
			if ( tryReceiveCartOnce( cart ) ) {
				done( true );
				return;
			}
			t += 1;
			if ( t < 12 ) {
				setTimeout( sameTickBurst, 0 );
			} else {
				rafPhases( cart, done );
			}
		}
		sameTickBurst();
	}

	function rafPhases( cart, done ) {
		var f = 0;
		function onFrame() {
			if ( tryReceiveCartOnce( cart ) ) {
				done( true );
				return;
			}
			f += 1;
			if ( f < 6 ) {
				requestAnimationFrame( onFrame );
			} else {
				pollPhases( cart, done );
			}
		}
		requestAnimationFrame( onFrame );
	}

	function pollPhases( cart, done ) {
		var p = 0;
		function poll() {
			if ( tryReceiveCartOnce( cart ) ) {
				done( true );
				return;
			}
			p += 1;
			if ( p < 20 ) {
				setTimeout( poll, 2 );
			} else {
				/* Last resort: nudge a server refetch (slower, but can recover if receiveCart never bound). */
				if ( window.wp && wp.data && wp.data.dispatch ) {
					try {
						var d = wp.data.dispatch( 'wc/store/cart' );
						if ( d && typeof d.invalidateResolutionForStore === 'function' ) {
							d.invalidateResolutionForStore();
						}
					} catch ( e2 ) {
						/* ignore */
					}
				}
				done( false );
			}
		}
		poll();
	}

	function getMiniCartBlockRoot( button ) {
		if ( ! button ) {
			return null;
		}
		return button.closest( '.wp-block-woocommerce-mini-cart' ) || button.closest( '.wc-block-mini-cart' );
	}

	/**
	 * Move mini-cart quantity selector into the right-side total column.
	 * Woo Blocks may re-render rows, so this is safe to run repeatedly.
	 */
	function moveMiniCartQuantitySelectorIntoTotal( scopeRoot ) {
		var root = scopeRoot || document;
		var rows = queryAllInShadow( root, '.wc-block-mini-cart__drawer .wc-block-cart-item', [] );
		if ( ! rows.length ) {
			return;
		}
		rows.forEach( function ( row ) {
			if ( ! row || ! row.querySelector ) {
				return;
			}
			var qty = row.querySelector( '.wc-block-components-quantity-selector' );
			var total = row.querySelector( '.wc-block-cart-item__total' );
			if ( ! qty || ! total ) {
				return;
			}
			if ( ! total.contains( qty ) ) {
				total.appendChild( qty );
			}
		} );
	}

	function initMiniCartQuantitySelectorIntoTotal() {
		moveMiniCartQuantitySelectorIntoTotal();
		if ( ! window.MutationObserver || ! document.body ) {
			return;
		}
		var scheduled = false;
		var observer = new MutationObserver( function () {
			if ( scheduled ) {
				return;
			}
			scheduled = true;
			window.requestAnimationFrame( function () {
				scheduled = false;
				moveMiniCartQuantitySelectorIntoTotal();
			} );
		} );
		observer.observe( document.body, { childList: true, subtree: true } );
	}

	function openMiniCartDrawer() {
		var controls = findAll( '.wc-block-mini-cart__button' );
		var miniCartButton = controls.length ? controls[ 0 ] : null;
		if ( ! miniCartButton ) {
			return;
		}
		var root = getMiniCartBlockRoot( miniCartButton );
		if ( miniCartButton.getAttribute( 'aria-expanded' ) === 'true' ) {
			return;
		}
		miniCartButton.click();
		moveMiniCartQuantitySelectorIntoTotal( root || document );
	}

	function replaceFragments( fragments ) {
		if ( ! fragments || typeof fragments !== 'object' ) {
			return;
		}
		Object.keys( fragments ).forEach( function ( selector ) {
			var html = fragments[ selector ];
			if ( typeof html !== 'string' ) {
				return;
			}
			var targets = document.querySelectorAll( selector );
			targets.forEach( function ( target ) {
				target.outerHTML = html;
			} );
		} );
	}

	/**
	 * WooCommerce core injects a "View cart" link and `.added` (check) on the button; we use the mini-cart instead.
	 */
	function stripWooAddSuccessUi( form ) {
		if ( ! form ) {
			return;
		}
		var scope = form.closest( '.summary' ) || form;
		/* Classic success bar (“… has been added to your cart”) if a fragment/plugin still injects it. */
		document.querySelectorAll( 'body.single-product .woocommerce-notices-wrapper .woocommerce-message' ).forEach( function ( el ) {
			el.remove();
		} );
		document.querySelectorAll( 'body.single-product .woocommerce-notices-wrapper .woocommerce-error' ).forEach( function ( el ) {
			var text = ( el.textContent || '' ).toLowerCase();
			if ( text.indexOf( 'please choose product options by visiting' ) !== -1 ) {
				el.remove();
			}
		} );
		/* Block Mini Cart / Store API notices (drawer-level). */
		document
			.querySelectorAll(
				'.wc-block-components-notice-banner.is-error, .wc-block-components-notice-banner[role="alert"], .wc-block-store-notice'
			)
			.forEach( function ( el ) {
				var text = ( el.textContent || '' ).toLowerCase();
				if ( text.indexOf( 'please choose product options by visiting' ) !== -1 ) {
					el.remove();
				}
			} );
		scope.querySelectorAll( 'a.added_to_cart' ).forEach( function ( el ) {
			el.remove();
		} );
		document
			.querySelectorAll( 'body.single-product .woocommerce a.added_to_cart' )
			.forEach( function ( el ) {
				el.remove();
			} );
		var addBtn = form.querySelector( '.single_add_to_cart_button' );
		if ( addBtn ) {
			addBtn.classList.remove( 'added' );
			addBtn
				.querySelectorAll( 'svg' )
				.forEach( function ( svg ) {
					svg.remove();
				} );
		}
	}

	function clearKnownFalseVariationErrorNotices() {
		var selectors = [
			'body.single-product .woocommerce-notices-wrapper .woocommerce-error',
			'.wc-block-components-notice-banner.is-error',
			'.wc-block-components-notice-banner[role="alert"]',
			'.wc-block-store-notice',
		];
		document.querySelectorAll( selectors.join( ', ' ) ).forEach( function ( el ) {
			var text = ( el.textContent || '' ).toLowerCase();
			if ( text.indexOf( 'please choose product options by visiting' ) !== -1 ) {
				el.remove();
			}
		} );
	}

	function scheduleStripWooAddSuccess( form ) {
		[ 0, 32, 120 ].forEach( function ( delay ) {
			window.setTimeout( function () {
				stripWooAddSuccessUi( form );
			}, delay );
		} );
	}

	/**
	 * jQuery + native: Blocks bridge listens to jQuery and mirrors to wc-blocks_*.
	 */
	function triggerWooAddToCartEvents( fragments, cartHash, button, preserveCartData ) {
		if ( window.jQuery ) {
			window.jQuery( document.body ).trigger( 'added_to_cart', [ fragments, cartHash, window.jQuery( button ) ] );
			window.jQuery( document.body ).trigger( 'wc_fragment_refresh' );
		}
		var detail = { preserveCartData: !! preserveCartData };
		if ( fragments && typeof fragments === 'object' ) {
			detail.fragments = fragments;
		}
		if ( cartHash ) {
			detail.cartHash = cartHash;
		}
		document.body.dispatchEvent(
			new CustomEvent( 'wc-blocks_added_to_cart', {
				bubbles: true,
				detail: detail,
			} )
		);
	}

	function ensureRequiredPayload( form, formData, button ) {
		var qtyInput = form.querySelector( 'input.qty' );
		var addToCartInput = form.querySelector( 'input[name="add-to-cart"]' );
		var isVariationForm = form.classList && form.classList.contains( 'variations_form' );
		if ( ! formData.has( 'quantity' ) && qtyInput && qtyInput.value ) {
			formData.append( 'quantity', qtyInput.value );
		}
		if ( formData.has( 'add-to-cart' ) ) {
			/* keep going: variation payload may still need normalization */
		} else if ( addToCartInput && addToCartInput.value ) {
			formData.append( 'add-to-cart', addToCartInput.value );
		} else if ( button && button.value ) {
			formData.append( 'add-to-cart', button.value );
		}

		if ( ! isVariationForm ) {
			return;
		}

		/*
		 * Variable products need parent product id, variation id and selected attributes.
		 * Some themes/plugins mutate the form and these can be missing in FormData.
		 */
		var productIdInput = form.querySelector( 'input[name="product_id"]' );
		if ( productIdInput && productIdInput.value ) {
			if ( ! formData.has( 'product_id' ) ) {
				formData.append( 'product_id', productIdInput.value );
			}
			if ( ! formData.has( 'add-to-cart' ) ) {
				formData.append( 'add-to-cart', productIdInput.value );
			}
		}

		var variationIdInput = form.querySelector( 'input[name="variation_id"]' );
		if ( variationIdInput && variationIdInput.value && ! formData.has( 'variation_id' ) ) {
			formData.append( 'variation_id', variationIdInput.value );
		}

		form.querySelectorAll( 'select[name^="attribute_"]' ).forEach( function ( select ) {
			if ( ! select || ! select.name ) {
				return;
			}
			var value = select.value || '';
			if ( ! formData.has( select.name ) ) {
				formData.append( select.name, value );
			}
		} );
	}

	function isVariationMissingSelection( form, data ) {
		if ( ! form || ! ( form.classList && form.classList.contains( 'variations_form' ) ) ) {
			return false;
		}
		var vId = form.querySelector( 'input[name="variation_id"]' );
		var noVariationId = ! vId || ! vId.value || parseInt( vId.value, 10 ) <= 0;
		if ( noVariationId ) {
			return true;
		}
		var hasEmptyAttr = false;
		form.querySelectorAll( 'select[name^="attribute_"]' ).forEach( function ( select ) {
			if ( ! select ) {
				return;
			}
			if ( ! select.value ) {
				hasEmptyAttr = true;
			}
		} );
		if ( hasEmptyAttr ) {
			return true;
		}
		return false;
	}

	/**
	 * After add_to_cart: update UI immediately, then correct via Store API (second request).
	 */
	function afterAddSuccess( data, button, form ) {
		var fragments = data && data.fragments ? data.fragments : {};
		var hash = data && data.cart_hash ? data.cart_hash : '';
		onAddToCartResponseImmediate( data, form );

		return fetch( getStoreCartUrl(), {
			method: 'GET',
			credentials: 'same-origin',
			cache: 'no-store',
			headers: getStoreApiHeaders(),
		} )
			.then( function ( response ) {
				if ( ! response.ok ) {
					throw new Error( 'Store cart read failed' );
				}
				return response.json();
			} )
			.then( function ( cart ) {
				var count = getItemsCountFromCart( cart );
				setBadgeAndSnapshot( count, hash );
				pushCartToBlocksStore( cart, function ( hydrated ) {
					/* true: blocks will not re-fetch the cart (avoids a second, slow request). */
					triggerWooAddToCartEvents( fragments, hash, button, !! hydrated );
					scheduleStripWooAddSuccess( form );
					openMiniCartDrawer();
				} );
			} )
			.catch( function ( err ) {
				window.console.error( '[Trove] Store cart sync:', err );
				triggerWooAddToCartEvents( fragments, hash, button, false );
				scheduleStripWooAddSuccess( form );
				openMiniCartDrawer();
			} );
	}

	function onFormSubmit( event ) {
		var form = event.currentTarget;
		var button = form.querySelector( '.single_add_to_cart_button' );
		if ( ! button ) {
			return;
		}
		event.preventDefault();
		if ( window.jQuery ) {
			window.jQuery( document.body ).trigger( 'adding_to_cart', [ window.jQuery( button ) ] );
		}

		var formData = new FormData( form );
		ensureRequiredPayload( form, formData, button );
		button.classList.add( 'loading' );
		button.setAttribute( 'aria-disabled', 'true' );
		button.disabled = true;

		fetch( getAjaxUrl(), {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'X-Requested-With': 'XMLHttpRequest',
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
			},
			body: new URLSearchParams( formData ),
		} )
			.then( readAddToCartResponseAsJson )
			.then( function ( data ) {
				if ( isVariationMissingSelection( form, data ) ) {
					/* Keep user on page; Woo variation script will surface inline guidance. */
					return;
				}
				if ( form.classList && form.classList.contains( 'variations_form' ) && data && data.error && data.product_url ) {
					data.error = false;
				}
				if ( data && data.error && data.product_url ) {
					window.location.href = data.product_url;
					return;
				}
				afterAddSuccess( data, button, form );
			} )
			.catch( function ( error ) {
				window.console.error( '[Trove] AJAX add-to-cart failed:', error );
			} )
			.finally( function () {
				button.classList.remove( 'loading' );
				button.removeAttribute( 'aria-disabled' );
				button.disabled = false;
			} );
	}

	function init() {
		clearKnownFalseVariationErrorNotices();
		hydrateBadgeFromCartSnapshot();
		initMiniCartRemoveOptimisticBadge();
		initMiniCartQuantitySelectorIntoTotal();
		initSingleProductQtyButtons();
		initSingleProductSummarySticksy();
		initWooShopSidebarSticksy();
		initSingleProductSliderThumbCarousel();
		window.addEventListener( 'load', initSingleProductSummarySticksy, { once: true } );
		window.addEventListener( 'load', initWooShopSidebarSticksy, { once: true } );
		window.addEventListener(
			'load',
			function () {
				initSingleProductSliderThumbCarousel();
			},
			{ once: true }
		);
		if ( window.jQuery ) {
			window.jQuery( document.body ).on(
				'added_to_cart removed_from_cart wc_fragments_refreshed wc_fragments_loaded',
				function () {
					clearKnownFalseVariationErrorNotices();
					window.setTimeout( reconcileBadgeFromLiveState, 0 );
				}
			);
			window.jQuery( '.woocommerce-product-gallery' ).on(
				'wc-product-gallery-after-init',
				function () {
					initSingleProductSliderThumbCarousel( this );
				}
			);
		}
		var forms = document.querySelectorAll( '.single-product form.cart' );
		if ( ! forms.length ) {
			return;
		}
		forms.forEach( function ( form ) {
			form.addEventListener( 'submit', onFormSubmit );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
