/**
 * Free shipping progress bar: sync with cart subtotal in the mini-cart drawer.
 */
( function () {
	'use strict';

	var cfg = window.troveFreeShippingBar || {};
	var threshold = parseFloat( cfg.threshold, 10 );
	if ( ! threshold || threshold <= 0 ) {
		return;
	}

	var i18n = cfg.i18n || {};
	var pricesIncludeTax = !! cfg.pricesIncludeTax;
	var storeCartUrl = cfg.storeCartUrl || '';
	var fallbackSubtotal = parseFloat( cfg.subtotal, 10 );
	if ( isNaN( fallbackSubtotal ) ) {
		fallbackSubtotal = 0;
	}

	var lastCartSignature = '';
	var refreshTimer = null;

	/**
	 * Mini-cart may mount inside open shadow roots.
	 *
	 * @param {ParentNode} root
	 * @param {string} selector
	 * @param {Element[]} acc
	 * @return {Element[]}
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

	function findDrawer() {
		var drawers = queryAllInShadow( document, '.wc-block-mini-cart__drawer', [] );
		return drawers.length ? drawers[ 0 ] : null;
	}

	function minorUnitDivisor( minor ) {
		var unit = parseInt( minor, 10 );
		if ( isNaN( unit ) ) {
			unit = 2;
		}
		return Math.pow( 10, unit );
	}

	function subtotalFromTotals( totals ) {
		if ( ! totals ) {
			return null;
		}
		var divisor = minorUnitDivisor( totals.currency_minor_unit );
		var items = parseInt( totals.total_items, 10 );
		if ( isNaN( items ) ) {
			return null;
		}
		var subtotal = items / divisor;
		if ( pricesIncludeTax ) {
			var tax = parseInt( totals.total_items_tax, 10 );
			if ( ! isNaN( tax ) ) {
				subtotal += tax / divisor;
			}
		}
		return subtotal;
	}

	function getCartFromStore() {
		if ( ! window.wp || ! wp.data || ! wp.data.select ) {
			return null;
		}
		var select;
		try {
			select = wp.data.select( 'wc/store/cart' );
		} catch ( e ) {
			return null;
		}
		if ( ! select || typeof select.getCartData !== 'function' ) {
			return null;
		}
		return select.getCartData();
	}

	function getCartSignature( cart ) {
		if ( ! cart ) {
			return '';
		}
		var parts = [];
		var subtotal = subtotalFromTotals( cart.totals );
		if ( subtotal !== null && ! isNaN( subtotal ) ) {
			parts.push( 's:' + subtotal.toFixed( 4 ) );
		}
		if ( cart.items && cart.items.length ) {
			cart.items.forEach( function ( item ) {
				parts.push( ( item.key || '' ) + 'x' + ( item.quantity || 0 ) );
			} );
		} else {
			parts.push( 'empty' );
		}
		return parts.join( '|' );
	}

	function getSubtotalFromStore() {
		var cart = getCartFromStore();
		if ( ! cart || ! cart.totals ) {
			return null;
		}
		return subtotalFromTotals( cart.totals );
	}

	function fetchSubtotalFromApi() {
		var url = storeCartUrl || window.location.origin + '/wp-json/wc/store/v1/cart';
		if ( window.wp && wp.apiFetch ) {
			return wp.apiFetch( { path: '/wc/store/v1/cart', method: 'GET' } ).then( function ( cart ) {
				return cart && cart.totals ? subtotalFromTotals( cart.totals ) : null;
			} );
		}
		if ( ! window.fetch ) {
			return Promise.resolve( null );
		}
		return window
			.fetch( url, {
				method: 'GET',
				credentials: 'same-origin',
				cache: 'no-store',
				headers: { 'X-Requested-With': 'XMLHttpRequest' },
			} )
			.then( function ( response ) {
				if ( ! response.ok ) {
					throw new Error( 'Cart read failed' );
				}
				return response.json();
			} )
			.then( function ( cart ) {
				return cart && cart.totals ? subtotalFromTotals( cart.totals ) : null;
			} )
			.catch( function () {
				return null;
			} );
	}

	function getSubtotal() {
		var storeSubtotal = getSubtotalFromStore();
		if ( storeSubtotal !== null && ! isNaN( storeSubtotal ) ) {
			return storeSubtotal;
		}
		return fallbackSubtotal;
	}

	function formatMoney( amount ) {
		var cart = getCartFromStore();
		if ( cart && cart.totals ) {
			var minor = parseInt( cart.totals.currency_minor_unit, 10 );
			if ( isNaN( minor ) ) {
				minor = 2;
			}
			var symbol = cart.totals.currency_symbol || '';
			var prefix = cart.totals.currency_prefix || '';
			var suffix = cart.totals.currency_suffix || '';
			var value = amount.toFixed( minor );
			if ( prefix ) {
				return prefix + value + suffix;
			}
			return symbol + value + suffix;
		}
		return amount.toFixed( 2 );
	}

	function buildBarHtml( subtotal ) {
		subtotal = Math.max( 0, subtotal );
		var qualified = subtotal >= threshold;
		var percent = Math.min( 100, ( subtotal / threshold ) * 100 );
		var remaining = Math.max( 0, threshold - subtotal );
		var message = qualified
			? i18n.qualified || 'You qualify for free shipping!'
			: ( i18n.addMore || 'Add %s more for free shipping' ).replace( '%s', formatMoney( remaining ) );

		return (
			'<div class="trove-free-shipping-bar" data-threshold="' +
			threshold +
			'" data-subtotal="' +
			subtotal +
			'" role="status" aria-live="polite">' +
			'<p class="trove-free-shipping-bar__message">' +
			message +
			'</p>' +
			'<div class="trove-free-shipping-bar__track" aria-hidden="true">' +
			'<span class="trove-free-shipping-bar__fill" style="width:' +
			percent.toFixed( 2 ) +
			'%;"></span>' +
			'</div>' +
			'</div>'
		);
	}

	function updateBarElement( bar, subtotalOverride ) {
		if ( ! bar ) {
			return;
		}
		var subtotal =
			typeof subtotalOverride === 'number' && ! isNaN( subtotalOverride )
				? subtotalOverride
				: getSubtotal();
		var qualified = subtotal >= threshold;
		var percent = Math.min( 100, ( subtotal / threshold ) * 100 );
		var remaining = Math.max( 0, threshold - subtotal );
		var messageEl = bar.querySelector( '.trove-free-shipping-bar__message' );
		var fillEl = bar.querySelector( '.trove-free-shipping-bar__fill' );
		if ( messageEl ) {
			messageEl.textContent = qualified
				? i18n.qualified || 'You qualify for free shipping!'
				: ( i18n.addMore || 'Add %s more for free shipping' ).replace( '%s', formatMoney( remaining ) );
		}
		if ( fillEl ) {
			fillEl.style.width = percent.toFixed( 2 ) + '%';
		}
		bar.setAttribute( 'data-subtotal', String( subtotal ) );
	}

	function isOnCartPageBlock( bar ) {
		return !! (
			bar &&
			bar.closest &&
			bar.closest( '.wp-block-woocommerce-cart' )
		);
	}

	function removeCartPageBars() {
		getAllBars().forEach( function ( bar ) {
			if ( isOnCartPageBlock( bar ) ) {
				bar.remove();
			}
		} );
	}

	function getAllBars() {
		return queryAllInShadow( document, '.trove-free-shipping-bar', [] );
	}

	function getDrawerBarInsertionParent( drawer ) {
		if ( ! drawer ) {
			return null;
		}
		var itemsBlock = drawer.querySelector( '.wp-block-woocommerce-mini-cart-items-block' );
		if ( itemsBlock ) {
			return itemsBlock;
		}
		var table = drawer.querySelector( '.wc-block-cart-items' );
		if ( table && table.parentNode ) {
			return table.parentNode;
		}
		return null;
	}

	function placeBarInDrawer( bar, drawer ) {
		var parent = getDrawerBarInsertionParent( drawer );
		if ( ! parent || ! bar ) {
			return false;
		}
		var first = parent.firstElementChild;
		if ( first && first.classList.contains( 'trove-free-shipping-bar' ) ) {
			return true;
		}
		parent.insertBefore( bar, parent.firstChild );
		return true;
	}

	function ensureDrawerBar( subtotal ) {
		var drawer = findDrawer();
		if ( ! drawer ) {
			return;
		}

		var hasItems =
			drawer.querySelector( '.wc-block-cart-item' ) ||
			drawer.querySelector( '.wc-block-cart-items__row' );
		if ( ! hasItems ) {
			queryAllInShadow( drawer, '.trove-free-shipping-bar', [] ).forEach( function ( bar ) {
				bar.remove();
			} );
			return;
		}

		if ( typeof subtotal !== 'number' || isNaN( subtotal ) ) {
			subtotal = getSubtotal();
		}
		if ( subtotal <= 0 ) {
			return;
		}

		var existing = queryAllInShadow( drawer, '.trove-free-shipping-bar', [] )[ 0 ];
		if ( existing ) {
			placeBarInDrawer( existing, drawer );
			updateBarElement( existing, subtotal );
			return;
		}

		var temp = document.createElement( 'div' );
		temp.innerHTML = buildBarHtml( subtotal );
		var bar = temp.firstElementChild;
		if ( bar && placeBarInDrawer( bar, drawer ) ) {
			updateBarElement( bar, subtotal );
		}
	}

	function refreshAllBars( subtotalOverride ) {
		var subtotal =
			typeof subtotalOverride === 'number' && ! isNaN( subtotalOverride )
				? subtotalOverride
				: getSubtotal();
		removeCartPageBars();
		getAllBars().forEach( function ( bar ) {
			if ( isOnCartPageBlock( bar ) ) {
				return;
			}
			updateBarElement( bar, subtotal );
		} );
		ensureDrawerBar( subtotal );
	}

	function scheduleRefresh( delay ) {
		window.clearTimeout( refreshTimer );
		refreshTimer = window.setTimeout( function () {
			refreshTimer = null;
			var cart = getCartFromStore();
			var sig = getCartSignature( cart );
			if ( sig && sig === lastCartSignature && cart ) {
				refreshAllBars( subtotalFromTotals( cart.totals ) );
				return;
			}
			fetchSubtotalFromApi().then( function ( apiSubtotal ) {
				if ( apiSubtotal !== null && ! isNaN( apiSubtotal ) ) {
					refreshAllBars( apiSubtotal );
					return;
				}
				refreshAllBars();
			} );
		}, typeof delay === 'number' ? delay : 80 );
	}

	function onCartStoreChange() {
		var cart = getCartFromStore();
		var sig = getCartSignature( cart );
		if ( ! sig || sig === lastCartSignature ) {
			return;
		}
		lastCartSignature = sig;
		var subtotal = cart && cart.totals ? subtotalFromTotals( cart.totals ) : null;
		refreshAllBars( subtotal !== null ? subtotal : undefined );
	}

	function init() {
		removeCartPageBars();
		var cart = getCartFromStore();
		lastCartSignature = getCartSignature( cart );
		refreshAllBars();

		if ( window.jQuery ) {
			window.jQuery( document.body ).on(
				'added_to_cart removed_from_cart updated_wc_div updated_cart_totals wc_fragments_refreshed wc_fragments_loaded',
				function () {
					scheduleRefresh( 0 );
				}
			);
		}

		[ 'wc-blocks_added_to_cart', 'wc-blocks_removed_from_cart' ].forEach( function ( eventName ) {
			document.body.addEventListener( eventName, function () {
				scheduleRefresh( 50 );
			} );
		} );

		document.body.addEventListener(
			'change',
			function ( event ) {
				var target = event.target;
				if (
					target &&
					target.matches &&
					target.matches( '.wc-block-components-quantity-selector__input' )
				) {
					scheduleRefresh( 350 );
				}
			},
			true
		);

		document.body.addEventListener(
			'click',
			function ( event ) {
				var target = event.target;
				if (
					target &&
					target.closest &&
					target.closest(
						'.wc-block-components-quantity-selector__button--plus, .wc-block-components-quantity-selector__button--minus'
					)
				) {
					scheduleRefresh( 350 );
				}
			},
			true
		);

		if ( window.wp && wp.data && wp.data.subscribe ) {
			wp.data.subscribe( onCartStoreChange );
		}

		if ( window.MutationObserver && document.body ) {
			var scheduled = false;
			new MutationObserver( function () {
				if ( scheduled ) {
					return;
				}
				scheduled = true;
				window.requestAnimationFrame( function () {
					scheduled = false;
					scheduleRefresh( 0 );
				} );
			} ).observe( document.body, { childList: true, subtree: true } );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
