/**
 * Trove Customizer live preview (postMessage).
 */
( function () {
	'use strict';

	function applyVar( entry, newVal ) {
		var fallback = entry.default || '';
		var v =
			newVal && String( newVal ).trim() !== ''
				? String( newVal ).trim()
				: fallback;
		document.documentElement.style.setProperty( entry.var, v );
	}

	function escapeHtmlStr( str ) {
		var d = document.createElement( 'div' );
		d.textContent = str == null ? '' : String( str );
		return d.innerHTML;
	}

	function bindGlobalColors() {
		if ( typeof wp === 'undefined' || ! wp.customize ) {
			return;
		}

		var cfg =
			typeof troveCustomizerPreview !== 'undefined' && troveCustomizerPreview.colors
				? troveCustomizerPreview.colors
				: {};

		Object.keys( cfg ).forEach( function ( sid ) {
			var entry = cfg[ sid ];
			if ( ! entry || ! entry.var ) {
				return;
			}

			wp.customize( sid, function ( value ) {
				function onChange( newVal ) {
					applyVar( entry, newVal );
				}
				value.bind( onChange );
				/* Bind does not replay the current value; apply once so the iframe matches saved state. */
				onChange( value.get() );
			} );
		} );
	}

	function formatCssFloatStr( num ) {
		var s = String( Math.round( num * 10000 ) / 10000 );
		if ( s.indexOf( '.' ) !== -1 ) {
			s = s.replace( /\.?0+$/, '' );
		}
		return s;
	}

	function fontBaseSizeCss( sizeVal, unitVal ) {
		var unit = unitVal ? String( unitVal ).toLowerCase() : 'px';
		if ( unit !== 'px' && unit !== 'rem' && unit !== 'em' ) {
			unit = 'px';
		}
		var v = parseFloat( sizeVal );
		if ( isNaN( v ) ) {
			v = 16;
		}
		if ( unit === 'px' ) {
			var n = Math.round( v );
		n = Math.max( 1, Math.min( 500, n ) );
			return n + 'px';
		}
	var f = Math.max( 0.25, Math.min( 32, v ) );
		return formatCssFloatStr( f ) + unit;
	}

	function fontBaseLineHeightCss( lhVal, unitVal ) {
		var unit = unitVal ? String( unitVal ) : 'unitless';
		var v = parseFloat( lhVal );
		if ( isNaN( v ) ) {
			v = 1.5;
		}
		if ( unit === 'unitless' ) {
			var u = Math.max( 1, Math.min( 3, v ) );
			return formatCssFloatStr( u );
		}
		if ( unit === 'px' ) {
			var px = Math.round( v );
			px = Math.max( 12, Math.min( 80, px ) );
			return px + 'px';
		}
		if ( unit === 'rem' || unit === 'em' ) {
			var r = Math.max( 0.5, Math.min( 4, v ) );
			return formatCssFloatStr( r ) + unit;
		}
		if ( unit === '%' ) {
			var p = Math.round( v );
			p = Math.max( 80, Math.min( 250, p ) );
			return p + '%';
		}
		return '1.5';
	}

	function getFontWeightsMap() {
		return typeof troveCustomizerPreview !== 'undefined' &&
			troveCustomizerPreview.fontWeights
			? troveCustomizerPreview.fontWeights
			: {};
	}

	function weightsForFamily( family ) {
		var map = getFontWeightsMap();
		var s = family ? String( family ).trim() : '';
		if ( ! s ) {
			s = 'Instrument Sans';
		}
		if ( map[ s ] ) {
			return map[ s ];
		}
		var keys = Object.keys( map );
		var k;
		for ( k = 0; k < keys.length; k++ ) {
			if ( keys[ k ].toLowerCase() === s.toLowerCase() ) {
				return map[ keys[ k ] ];
			}
		}
		return map.system || [ 300, 400, 500, 600, 700 ];
	}

	function nearestWeight( w, weights ) {
		if ( ! weights || ! weights.length ) {
			return 400;
		}
		var i;
		var best = weights[ 0 ];
		var bestd = Math.abs( best - w );
		for ( i = 1; i < weights.length; i++ ) {
			var d = Math.abs( weights[ i ] - w );
			if ( d < bestd ) {
				bestd = d;
				best = weights[ i ];
			}
		}
		return best;
	}

	function applyTypographyVar( settingId, entry, newVal ) {
		var mode = entry.mode || 'raw';
		var v = '';
		if ( mode === 'weight' ) {
			var w = parseInt( newVal, 10 );
			if ( isNaN( w ) ) {
				w = 400;
			}
			var fam = '';
			var familyMap =
				typeof troveCustomizerPreview !== 'undefined' && troveCustomizerPreview.weightFamilies
					? troveCustomizerPreview.weightFamilies
					: {};
			var familySetting = familyMap[ settingId ] || 'trove_google_font_family';
			if ( typeof wp !== 'undefined' && wp.customize && wp.customize( familySetting ) ) {
				fam = wp.customize( familySetting ).get();
			}
			w = nearestWeight( w, weightsForFamily( fam ) );
			v = String( w );
		} else {
			v =
				newVal !== undefined && newVal !== null && String( newVal ).trim() !== ''
					? String( newVal ).trim()
					: entry.default || '';
		}
		document.documentElement.style.setProperty( entry.var, v );
	}

	function bindTypography() {
		if ( typeof wp === 'undefined' || ! wp.customize ) {
			return;
		}

		var cfg =
			typeof troveCustomizerPreview !== 'undefined' && troveCustomizerPreview.typography
				? troveCustomizerPreview.typography
				: {};

		Object.keys( cfg ).forEach( function ( sid ) {
			var entry = cfg[ sid ];
			if ( ! entry || ! entry.var ) {
				return;
			}

			wp.customize( sid, function ( value ) {
				function onChange( newVal ) {
					applyTypographyVar( sid, entry, newVal );
				}
				value.bind( onChange );
				onChange( value.get() );
			} );
		} );

		function applyNumberUnitVar( numberSetting, pair ) {
			var numberVal = wp.customize( numberSetting ).get();
			var unitVal = wp.customize( pair.unit ).get();
			var css = pair.mode === 'line_height' ? fontBaseLineHeightCss( numberVal, unitVal ) : fontBaseSizeCss( numberVal, unitVal );
			document.documentElement.style.setProperty( pair.var, css );
		}

		var numberUnitPairs =
			typeof troveCustomizerPreview !== 'undefined' && troveCustomizerPreview.numberUnitPairs
				? troveCustomizerPreview.numberUnitPairs
				: {};
		Object.keys( numberUnitPairs ).forEach( function ( numberSetting ) {
			var pair = numberUnitPairs[ numberSetting ];
			wp.customize( numberSetting, function ( value ) {
				value.bind( function () {
					applyNumberUnitVar( numberSetting, pair );
				} );
				wp.customize( pair.unit, function ( unitValue ) {
					unitValue.bind( function () {
						applyNumberUnitVar( numberSetting, pair );
					} );
					applyNumberUnitVar( numberSetting, pair );
				} );
			} );
		} );

		var weightFamilyMap =
			typeof troveCustomizerPreview !== 'undefined' && troveCustomizerPreview.weightFamilies
				? troveCustomizerPreview.weightFamilies
				: {};
		Object.keys( weightFamilyMap ).forEach( function ( weightSetting ) {
			var familySetting = weightFamilyMap[ weightSetting ];
			if ( ! cfg[ weightSetting ] ) {
				return;
			}
			wp.customize( weightSetting, function ( weightValue ) {
				wp.customize( familySetting, function ( familyValue ) {
					familyValue.bind( function () {
						applyTypographyVar( weightSetting, cfg[ weightSetting ], weightValue.get() );
					} );
				} );
			} );
		} );
	}

	function getHeaderEl() {
		return document.querySelector( 'header.site-header' );
	}

	function isVerticalHeaderLayout( layout ) {
		var l = layout ? String( layout ).trim() : '';
		return (
			l === 'vertical-centered-logo' || l === 'vertical-left-logo'
		);
	}

	function applyHeaderLayout( newVal ) {
		var header = getHeaderEl();
		var layout = newVal ? String( newVal ).trim() : '';
		if ( ! header ) {
			return;
		}

		/* Clear current layout classes (legacy + current naming). */
		Array.prototype.slice.call( header.classList ).forEach( function ( cls ) {
			if (
				cls.indexOf( 'trove-header__horizontal-' ) === 0 ||
				cls.indexOf( 'trove-header__vertical-' ) === 0 ||
				cls.indexOf( 'trove-header-layout-horizontal-' ) === 0
			) {
				header.classList.remove( cls );
			}
		} );

		if ( layout ) {
			header.classList.add( 'trove-header__' + layout );
		}
	}

	function applyHeaderBorder( newVal ) {
		var header = getHeaderEl();
		var enabled =
			newVal === true ||
			newVal === 1 ||
			newVal === '1' ||
			newVal === 'true';

		if ( ! header ) {
			return;
		}

		header.classList.toggle( 'trove-header-has-border', !! enabled );
	}

	function applyHeaderMenuAlignment( newVal ) {
		var header = getHeaderEl();
		var nav = header ? header.querySelector( '.site-header__nav' ) : null;
		var alignment = newVal ? String( newVal ).trim() : 'right';
		var allowed = { left: true, center: true, right: true };
		if ( ! nav ) {
			return;
		}
		Array.prototype.slice.call( nav.classList ).forEach( function ( cls ) {
			if ( cls.indexOf( 'site-header__nav-' ) === 0 ) {
				nav.classList.remove( cls );
			}
		} );
		if ( !allowed[ alignment ] ) {
			alignment = 'right';
		}
		nav.classList.add( 'site-header__nav-' + alignment );
	}

	function applyHeaderSticky( newVal ) {
		var header = getHeaderEl();
		var sticky = newVal ? String( newVal ).trim() : 'none';
		var allowed = { none: true, on_scroll_up: true, always: true };
		if ( ! header ) {
			return;
		}

		Array.prototype.slice.call( header.classList ).forEach( function ( cls ) {
			if ( cls.indexOf( 'trove-header-sticky-' ) === 0 ) {
				header.classList.remove( cls );
			}
		} );

		if ( !allowed[ sticky ] ) {
			sticky = 'none';
		}
		header.classList.add( 'trove-header-sticky-' + sticky );

		/*
		 * Keep preview consistent with front-end runtime:
		 * - on_scroll_up starts visible at top
		 * - hide only after scrolling down (runtime script toggles classes on scroll)
		 */
		header.classList.remove( 'is-at-top', 'is-sticky-visible' );
		if ( sticky === 'on_scroll_up' ) {
			header.classList.add( 'is-at-top' );
		}
	}

	function applyGoogleFont( slug ) {
		var fonts =
			typeof troveCustomizerPreview !== 'undefined' &&
			troveCustomizerPreview.fonts
				? troveCustomizerPreview.fonts
				: {};
		var s = slug ? String( slug ).trim() : 'Instrument Sans';
		var entry = fonts[ s ];
		if ( ! entry && fonts[ 'Instrument Sans' ] ) {
			var keys = Object.keys( fonts );
			var k;
			for ( k = 0; k < keys.length; k++ ) {
				if (
					keys[ k ].toLowerCase() === s.toLowerCase()
				) {
					entry = fonts[ keys[ k ] ];
					break;
				}
			}
		}
		if ( ! entry ) {
			entry = fonts[ 'Instrument Sans' ];
		}
		if ( ! entry ) {
			return;
		}
		var id = 'trove-preview-google-font';
		var link = document.getElementById( id );
		if ( entry.cssUrl ) {
			if ( ! link ) {
				link = document.createElement( 'link' );
				link.id = id;
				link.rel = 'stylesheet';
				document.head.appendChild( link );
			}
			link.href = entry.cssUrl;
		} else if ( link && link.parentNode ) {
			link.parentNode.removeChild( link );
		}
		document.documentElement.style.setProperty(
			'--trove-body-font-family',
			entry.stack
		);
	}

	function bindGoogleFont() {
		if ( typeof wp === 'undefined' || ! wp.customize ) {
			return;
		}
		var map =
			typeof troveCustomizerPreview !== 'undefined' && troveCustomizerPreview.fontFamilies
				? troveCustomizerPreview.fontFamilies
				: { trove_google_font_family: '--trove-body-font-family' };
		Object.keys( map ).forEach( function ( settingId ) {
			wp.customize( settingId, function ( value ) {
				function onChange( newVal ) {
					var fonts =
						typeof troveCustomizerPreview !== 'undefined' && troveCustomizerPreview.fonts
							? troveCustomizerPreview.fonts
							: {};
					var s = newVal ? String( newVal ).trim() : 'Instrument Sans';
					var entry = fonts[ s ] || fonts.system || fonts[ 'Instrument Sans' ];
					if ( ! entry ) {
						return;
					}
					var id = 'trove-preview-google-font-' + settingId.replace( /[^a-z0-9_-]/gi, '-' );
					var link = document.getElementById( id );
					if ( entry.cssUrl ) {
						if ( ! link ) {
							link = document.createElement( 'link' );
							link.id = id;
							link.rel = 'stylesheet';
							document.head.appendChild( link );
						}
						link.href = entry.cssUrl;
					} else if ( link && link.parentNode ) {
						link.parentNode.removeChild( link );
					}
					document.documentElement.style.setProperty( map[ settingId ], entry.stack );
				}
				value.bind( onChange );
				onChange( value.get() );
			} );
		} );
	}

	/** Coerce Customizer checkbox / theme_mod values (avoids !!'0' === true). */
	function troveCustomizeBool( v ) {
		if ( v === true || v === 1 || v === '1' ) {
			return true;
		}
		if ( v === false || v === 0 || v === '0' || v === '' || v === null || typeof v === 'undefined' ) {
			return false;
		}
		return Boolean( v );
	}

	function applyRootCssColorVar( prop, newVal ) {
		var raw = newVal == null ? '' : String( newVal ).trim();
		if ( raw === '' ) {
			document.documentElement.style.removeProperty( prop );
			return;
		}
		document.documentElement.style.setProperty( prop, raw );
	}

	/**
	 * Mini-cart badge: WooCommerce Interactivity often sets inline colors that beat :root variables.
	 * Sync theme_mod colors to :root and to each badge node with !important so preview updates immediately.
	 *
	 * @param {string} badgeBg  Badge background (hex/rgb or empty).
	 * @param {string} countCol Count text color (hex/rgb or empty).
	 * @return {void}
	 */
	function applyHeaderCartBadgePreview( badgeBg, countCol ) {
		var bg = badgeBg == null ? '' : String( badgeBg ).trim();
		var fg = countCol == null ? '' : String( countCol ).trim();
		applyRootCssColorVar( '--trove-header-cart-badge-bg', bg );
		applyRootCssColorVar( '--trove-header-cart-count', fg );
		var badges = document.querySelectorAll(
			'.site-header .wc-block-mini-cart .wc-block-mini-cart__badge'
		);
		var i;
		for ( i = 0; i < badges.length; i++ ) {
			if ( bg === '' ) {
				badges[ i ].style.removeProperty( 'background-color' );
			} else {
				badges[ i ].style.setProperty( 'background-color', bg, 'important' );
			}
			if ( fg === '' ) {
				badges[ i ].style.removeProperty( 'color' );
			} else {
				badges[ i ].style.setProperty( 'color', fg, 'important' );
			}
		}
	}

	function bindHeaderSettings() {
		if ( typeof wp === 'undefined' || ! wp.customize ) {
			return;
		}

		function getHeaderRoot() {
			return document.querySelector( 'header.site-header' );
		}

		wp.customize( 'trove_header_layout', function ( value ) {
			var prevLayout = value.get();

			function onChange( newVal ) {
				/*
				 * Horizontal and vertical layouts use different template parts
				 * (header-horizontal vs header-vertical); postMessage cannot swap DOM.
				 */
				var crossFamily =
					isVerticalHeaderLayout( newVal ) !==
					isVerticalHeaderLayout( prevLayout );
				if ( crossFamily && typeof wp.customize.preview !== 'undefined' ) {
					prevLayout = newVal;
					wp.customize.preview.send( 'refresh' );
					return;
				}
				prevLayout = newVal;
				applyHeaderLayout( newVal );
			}

			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_header_bottom_border', function ( value ) {
			function onChange( newVal ) {
				applyHeaderBorder( newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_header_sticky', function ( value ) {
			function onChange( newVal ) {
				applyHeaderSticky( newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_header_menu_alignment', function ( value ) {
			function onChange( newVal ) {
				applyHeaderMenuAlignment( newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_announcement_background', function ( value ) {
			function onChange( newVal ) {
				applyRootCssColorVar( '--trove-announcement-background', newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_announcement_text_color', function ( value ) {
			function onChange( newVal ) {
				applyRootCssColorVar( '--trove-announcement-text', newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_announcement_link_color', function ( value ) {
			function onChange( newVal ) {
				applyRootCssColorVar( '--trove-announcement-link', newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_announcement_link_hover_color', function ( value ) {
			function onChange( newVal ) {
				applyRootCssColorVar( '--trove-announcement-link-hover', newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		( function bindAnnouncementResponsivePreview() {
			function announcementMinHeightPreviewCss( raw, unit ) {
				var u = unit ? String( unit ).toLowerCase() : 'px';
				if ( u !== 'px' && u !== 'rem' ) {
					u = 'px';
				}
				var v = parseFloat( raw, 10 );
				if ( isNaN( v ) ) {
					v = 48;
				}
				if ( u === 'px' ) {
					var n = Math.round( v );
					n = Math.max( 24, Math.min( 120, n ) );
					return n + 'px';
				}
				var f = Math.max( 0.25, Math.min( 32, v ) );
				return formatCssFloatStr( f ) + 'rem';
			}

			function announcementTextSizeSlugToCss( slug ) {
				var k = String( slug == null ? 'medium' : slug ).trim();
				var map = {
					small: '14px',
					medium: '16px',
					big: '18px',
					large: '20px',
				};
				return map[ k ] || map.medium;
			}

			function getAnnouncementPreviewDevice() {
				var cfg =
					typeof troveCustomizerPreview !== 'undefined' &&
					troveCustomizerPreview.responsiveBreakpoints
						? troveCustomizerPreview.responsiveBreakpoints
						: { tablet: 1024, phone: 781 };
				var tablet = parseInt( cfg.tablet, 10 ) || 1024;
				var phone = parseInt( cfg.phone, 10 ) || 781;
				var w = window.innerWidth || document.documentElement.clientWidth || tablet + 1;
				if ( w <= phone ) {
					return 'phone';
				}
				if ( w <= tablet ) {
					return 'tablet';
				}
				return 'pc';
			}

			function applyAnnouncementResponsivePreviewVars() {
				if ( typeof wp === 'undefined' || ! wp.customize || ! wp.customize.instance ) {
					return;
				}
				try {
					var unitInst = wp.customize.instance( 'trove_announcement_height_unit' );
					var unit = unitInst ? unitInst.get() : 'px';
					var dev = getAnnouncementPreviewDevice();
					var hId = 'trove_announcement_height_' + dev;
					var fzId = 'trove_announcement_text_size_' + dev;
					var hInst = wp.customize.instance( hId );
					var fzInst = wp.customize.instance( fzId );
					var rawH = hInst ? hInst.get() : 48;
					var rawFz = fzInst ? fzInst.get() : 'medium';
					document.documentElement.style.setProperty(
						'--trove-announcement-min-height',
						announcementMinHeightPreviewCss( rawH, unit )
					);
					document.documentElement.style.setProperty(
						'--trove-announcement-font-size',
						announcementTextSizeSlugToCss( rawFz )
					);
				} catch ( err ) {
					return;
				}
			}

			var annResponsiveIds = [
				'trove_announcement_height_pc',
				'trove_announcement_height_tablet',
				'trove_announcement_height_phone',
				'trove_announcement_height_unit',
				'trove_announcement_text_size_pc',
				'trove_announcement_text_size_tablet',
				'trove_announcement_text_size_phone',
			];
			annResponsiveIds.forEach( function ( id ) {
				wp.customize( id, function ( value ) {
					value.bind( function () {
						applyAnnouncementResponsivePreviewVars();
					} );
				} );
			} );
			applyAnnouncementResponsivePreviewVars();
			window.addEventListener( 'resize', function () {
				applyAnnouncementResponsivePreviewVars();
			} );
		} )();

		wp.customize( 'trove_header_logo_width', function ( value ) {
			function onChange( newVal ) {
				var w = parseInt( newVal, 10 );
				if ( isNaN( w ) ) {
					w = 200;
				}
				w = Math.max( 40, Math.min( 400, w ) );
				document.documentElement.style.setProperty( '--trove-header-logo-max-width', w + 'px' );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		if (
			typeof troveCustomizerPreview !== 'undefined' &&
			troveCustomizerPreview.woocommerceHeaderCart
		) {
			wp.customize( 'trove_header_cart_badge_color', function ( value ) {
				function getCountColor() {
					try {
						var s = wp.customize.instance( 'trove_header_cart_count_color' );
						return s ? s.get() : '';
					} catch ( err ) {
						return '';
					}
				}
				function onChange( newVal ) {
					applyHeaderCartBadgePreview( newVal, getCountColor() );
				}
				value.bind( onChange );
				onChange( value.get() );
			} );

			wp.customize( 'trove_header_cart_count_color', function ( value ) {
				function getBadgeColor() {
					try {
						var s = wp.customize.instance( 'trove_header_cart_badge_color' );
						return s ? s.get() : '';
					} catch ( err ) {
						return '';
					}
				}
				function onChange( newVal ) {
					applyHeaderCartBadgePreview( getBadgeColor(), newVal );
				}
				value.bind( onChange );
				onChange( value.get() );
			} );

			(function observeHeaderCartBadgeForPreview() {
				if ( typeof MutationObserver === 'undefined' ) {
					return;
				}
				var woo = document.querySelector( 'header.site-header .site-header__woo' );
				if ( ! woo ) {
					return;
				}
				var t = null;
				function syncFromCustomizer() {
					if ( t ) {
						clearTimeout( t );
					}
					t = setTimeout( function () {
						t = null;
						var bg = '';
						var fg = '';
						var sb = null;
						var sf = null;
						try {
							sb = wp.customize.instance( 'trove_header_cart_badge_color' );
							sf = wp.customize.instance( 'trove_header_cart_count_color' );
							if ( sb ) {
								bg = sb.get();
							}
							if ( sf ) {
								fg = sf.get();
							}
						} catch ( err ) {
							return;
						}
						if ( ! sb && ! sf ) {
							return;
						}
						applyHeaderCartBadgePreview( bg, fg );
					}, 0 );
				}
				var observer = new MutationObserver( syncFromCustomizer );
				observer.observe( woo, { childList: true, subtree: true } );
			} )();
		}

		( function bindAnnouncementBarPreview() {
			function getDefaultAnnouncementLinesJson() {
				if (
					typeof troveCustomizerPreview !== 'undefined' &&
					troveCustomizerPreview.defaultAnnouncementLinesJson
				) {
					return troveCustomizerPreview.defaultAnnouncementLinesJson;
				}
				return '[]';
			}

			function getAnnSetting( id, fallback ) {
				try {
					if ( wp.customize.instance && wp.customize.instance( id ) ) {
						var v = wp.customize.instance( id ).get();
						return v == null ? fallback : v;
					}
				} catch ( err ) {
					// Setting not registered in this preview context.
				}
				return fallback;
			}

			function buildAnnouncementCarouselMarkup( lines, animation ) {
				var n = lines.length;
				var navHide = n <= 1 ? ' hidden' : '';
				var isFade = animation === 'fade';
				var prevSvg =
					'<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
				var nextSvg =
					'<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
				var inner = '';
				inner +=
					'<div class="trove-announcement-bar__inner">' +
					'<button type="button" class="trove-announcement-bar__nav trove-announcement-bar__nav--prev"' +
					navHide +
					' aria-label="Previous announcement">' +
					prevSvg +
					'</button>' +
					'<div class="trove-announcement-bar__viewport">' +
					'<div class="trove-announcement-bar__track">';
				var i;
				for ( i = 0; i < n; i++ ) {
					var activeCls = isFade && i === 0 ? ' trove-announcement-bar__slide is-active' : ' trove-announcement-bar__slide';
					inner +=
						'<div class="' +
						activeCls.trim() +
						'" aria-hidden="' +
						( i === 0 ? 'false' : 'true' ) +
						'">' +
						escapeHtmlStr( lines[ i ] ) +
						'</div>';
				}
				inner +=
					'</div></div>' +
					'<button type="button" class="trove-announcement-bar__nav trove-announcement-bar__nav--next"' +
					navHide +
					' aria-label="Next announcement">' +
					nextSvg +
					'</button></div>';
				return inner;
			}

			function applyAnnouncementResponsiveVisibilityStyle() {
				var enPc = troveCustomizeBool(
					getAnnSetting( 'trove_announcement_enabled_pc', true )
				);
				var enTab = troveCustomizeBool(
					getAnnSetting( 'trove_announcement_enabled_tablet', true )
				);
				var enPh = troveCustomizeBool(
					getAnnSetting( 'trove_announcement_enabled_phone', true )
				);
				var cfg =
					typeof troveCustomizerPreview !== 'undefined' &&
					troveCustomizerPreview.responsiveBreakpoints
						? troveCustomizerPreview.responsiveBreakpoints
						: { tablet: 1024, phone: 781 };
				var tablet = parseInt( cfg.tablet, 10 ) || 1024;
				var phone = parseInt( cfg.phone, 10 ) || 781;
				var el = document.getElementById( 'trove-cz-announcement-enabled-visibility' );
				if ( ! el ) {
					el = document.createElement( 'style' );
					el.id = 'trove-cz-announcement-enabled-visibility';
					document.head.appendChild( el );
				}
				if ( enPc && enTab && enPh ) {
					el.textContent = '';
					return;
				}
				var css = '';
				if ( ! enPc ) {
					css +=
						'@media (min-width:' +
						( tablet + 1 ) +
						'px){.trove-announcement-bar{display:none!important;}}';
				}
				if ( ! enTab ) {
					css +=
						'@media (max-width:' +
						tablet +
						'px) and (min-width:' +
						( phone + 1 ) +
						'px){.trove-announcement-bar{display:none!important;}}';
				}
				if ( ! enPh ) {
					css +=
						'@media (max-width:' +
						phone +
						'px){.trove-announcement-bar{display:none!important;}}';
				}
				el.textContent = css;
			}

			function applyAnnouncementBar() {
				applyAnnouncementResponsiveVisibilityStyle();
				var defaultLinesJson = getDefaultAnnouncementLinesJson();
				var enPc = troveCustomizeBool(
					getAnnSetting( 'trove_announcement_enabled_pc', true )
				);
				var enTab = troveCustomizeBool(
					getAnnSetting( 'trove_announcement_enabled_tablet', true )
				);
				var enPh = troveCustomizeBool(
					getAnnSetting( 'trove_announcement_enabled_phone', true )
				);
				var anyEnabled = enPc || enTab || enPh;
				var rawLines = getAnnSetting( 'trove_announcement_lines', defaultLinesJson );
				var lines = [];
				try {
					var parsed = JSON.parse( rawLines || defaultLinesJson );
					if ( Array.isArray( parsed ) ) {
						var j;
						for ( j = 0; j < parsed.length; j++ ) {
							var t = String( parsed[ j ] == null ? '' : parsed[ j ] ).trim();
							if ( t !== '' ) {
								lines.push( t );
							}
						}
					}
				} catch ( err ) {
					lines = [];
				}

				var header = document.querySelector( 'header.site-header' );
				var bar = document.querySelector( '.trove-announcement-bar' );

				if ( ! anyEnabled || lines.length === 0 ) {
					if ( bar && window.TroveAnnouncementCarousel && window.TroveAnnouncementCarousel.destroy ) {
						window.TroveAnnouncementCarousel.destroy( bar );
					}
					if ( bar ) {
						bar.remove();
					}
					return;
				}

				var anim = String( getAnnSetting( 'trove_announcement_animation', 'slide' ) || 'slide' );
				if ( anim !== 'fade' ) {
					anim = 'slide';
				}
				var autoplay = troveCustomizeBool( getAnnSetting( 'trove_announcement_autoplay', false ) );
				var intervalSec = parseInt( getAnnSetting( 'trove_announcement_autoplay_interval', 5 ), 10 );
				if ( isNaN( intervalSec ) ) {
					intervalSec = 5;
				}
				intervalSec = Math.max( 2, Math.min( 60, intervalSec ) );
				var intervalMs = intervalSec * 1000;

				var n = lines.length;
				if ( ! bar ) {
					bar = document.createElement( 'div' );
					if ( header && header.parentNode ) {
						header.parentNode.insertBefore( bar, header );
					} else {
						document.body.insertBefore( bar, document.body.firstChild );
					}
				}
				bar.className =
					'trove-announcement-bar' + ( anim === 'fade' ? ' trove-announcement-bar--fade' : '' );
				bar.setAttribute( 'role', 'region' );
				bar.setAttribute( 'aria-label', 'Announcement' );
				bar.setAttribute( 'data-trove-announcement-carousel', '' );
				bar.setAttribute( 'data-slide-count', String( n ) );
				bar.setAttribute( 'data-animation', anim );
				bar.setAttribute( 'data-autoplay', autoplay ? 'true' : 'false' );
				bar.setAttribute( 'data-autoplay-interval-ms', String( intervalMs ) );
				bar.setAttribute( 'tabindex', '0' );
				bar.style.cssText =
					'--trove-announcement-total: ' +
					n +
					'; --trove-announcement-index: 0;';
				bar.innerHTML = buildAnnouncementCarouselMarkup( lines, anim );
				if (
					typeof window.TroveAnnouncementCarousel !== 'undefined' &&
					window.TroveAnnouncementCarousel.init
				) {
					window.TroveAnnouncementCarousel.init( bar );
				}
			}

			var annIds = [
				'trove_announcement_lines',
				'trove_announcement_enabled_pc',
				'trove_announcement_enabled_tablet',
				'trove_announcement_enabled_phone',
				'trove_announcement_animation',
				'trove_announcement_autoplay',
				'trove_announcement_autoplay_interval',
			];
			annIds.forEach( function ( id ) {
				wp.customize( id, function ( value ) {
					value.bind( function () {
						applyAnnouncementBar();
					} );
				} );
			} );
			applyAnnouncementBar();
		} )();
	}

	function bindBlogLayout() {
		if ( typeof wp === 'undefined' || ! wp.customize ) {
			return;
		}

		var posKeys = [ 'top', 'left', 'right' ];

		function getBlogListFeaturedPos() {
			var pos = 'top';
			try {
				if ( wp.customize( 'trove_blog_list_featured_position' ) ) {
					pos = wp.customize( 'trove_blog_list_featured_position' ).get() || 'top';
				}
			} catch ( err ) {
				pos = 'top';
			}
			return posKeys.indexOf( pos ) !== -1 ? pos : 'top';
		}

		function stripFeaturedPosClasses( el ) {
			posKeys.forEach( function ( p ) {
				el.classList.remove( 'entry--featured-pos-' + p );
			} );
		}

		function applyBlogListFeaturedPosition( slug ) {
			var pos = slug && posKeys.indexOf( slug ) !== -1 ? slug : 'top';
			var entries = document.querySelectorAll( '.trove-blog-loop .entry--list' );
			var i;
			for ( i = 0; i < entries.length; i++ ) {
				var el = entries[ i ];
				stripFeaturedPosClasses( el );
				el.classList.add( 'entry--featured-pos-' + pos );
			}
		}

		function moveBlogPaginationOutsideLoop() {
			var loop = document.querySelector( '.trove-blog-loop' );
			if ( ! loop || ! loop.parentNode ) {
				return;
			}
			var nav = loop.querySelector( ':scope > nav.navigation.pagination' );
			if ( nav ) {
				loop.parentNode.insertBefore( nav, loop.nextSibling );
			}
		}

		function applyBlogLayout( slug ) {
			var allowed = [ 'list', 'grid', 'masonry' ];
			var v = slug && allowed.indexOf( slug ) !== -1 ? slug : 'list';
			var loop = document.querySelector( '.trove-blog-loop' );
			if ( loop ) {
				allowed.forEach( function ( l ) {
					loop.classList.remove( 'trove-blog-loop--' + l );
				} );
				loop.classList.add( 'trove-blog-loop--' + v );
			}
			allowed.forEach( function ( l ) {
				document.body.classList.remove( 'trove-blog-layout--' + l );
			} );
			document.body.classList.add( 'trove-blog-layout--' + v );
			var entries = document.querySelectorAll( '.trove-blog-loop .entry' );
			var i;
			for ( i = 0; i < entries.length; i++ ) {
				var el = entries[ i ];
				el.classList.remove( 'entry--list', 'entry--card' );
				el.classList.add( v === 'list' ? 'entry--list' : 'entry--card' );
				stripFeaturedPosClasses( el );
				if ( v === 'list' ) {
					el.classList.add( 'entry--featured-pos-' + getBlogListFeaturedPos() );
				}
			}
			moveBlogPaginationOutsideLoop();
		}

		wp.customize( 'trove_blog_layout', function ( value ) {
			function onChange( newVal ) {
				applyBlogLayout( newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_blog_list_featured_position', function ( value ) {
			function onChange( newVal ) {
				applyBlogListFeaturedPosition( newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		function applyBlogBoxed( val ) {
			var on =
				val === true ||
				val === 1 ||
				val === '1' ||
				val === 'true';
			document.body.classList.toggle( 'trove-blog-boxed', on );
		}

		wp.customize( 'trove_blog_boxed_style', function ( value ) {
			function onChange( newVal ) {
				applyBlogBoxed( newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		function applyBlogBoxedRadius( raw ) {
			var n = parseInt( raw, 10 );
			if ( isNaN( n ) ) {
				n = 10;
			}
			n = Math.max( 0, Math.min( 20, n ) );
			document.documentElement.style.setProperty( '--trove-blog-boxed-radius', n + 'px' );
		}

		wp.customize( 'trove_blog_boxed_border_radius', function ( value ) {
			function onChange( newVal ) {
				applyBlogBoxedRadius( newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		function parseBool( val ) {
			if ( val === true || val === 1 || val === '1' || val === 'true' ) {
				return true;
			}
			if ( val === false || val === 0 || val === '0' || val === 'false' ) {
				return false;
			}
			return !! val;
		}

		function getBlogPartSetting( id, fallback ) {
			try {
				if ( wp.customize( id ) ) {
					return parseBool( wp.customize( id ).get() );
				}
			} catch ( err ) {}
			return fallback;
		}

		function applyBlogLoopPartVisibility() {
			var showImg = getBlogPartSetting( 'trove_blog_show_featured_image', true );
			var showD = getBlogPartSetting( 'trove_blog_show_date', true );
			var showA = getBlogPartSetting( 'trove_blog_show_author', true );
			var showR = getBlogPartSetting( 'trove_blog_show_continue_reading', true );

			document.querySelectorAll( '.trove-blog-loop .entry-card__media--blog' ).forEach( function ( el ) {
				el.hidden = ! showImg;
			} );

			document.querySelectorAll( '.trove-blog-loop .entry-meta--blog' ).forEach( function ( meta ) {
				var t = meta.querySelector( 'time' );
				var sep = meta.querySelector( '.entry-meta__sep' );
				var auth = meta.querySelector( '.entry-meta__author' );
				if ( t ) {
					t.hidden = ! showD;
				}
				if ( auth ) {
					auth.hidden = ! showA;
				}
				if ( sep ) {
					sep.hidden = ! ( showD && showA );
				}
				meta.hidden = ! showD && ! showA;
			} );

			document.querySelectorAll( '.trove-blog-loop .read-more--blog' ).forEach( function ( el ) {
				el.hidden = ! showR;
			} );
		}

		[
			'trove_blog_show_featured_image',
			'trove_blog_show_date',
			'trove_blog_show_author',
			'trove_blog_show_continue_reading',
		].forEach( function ( sid ) {
			wp.customize( sid, function ( value ) {
				value.bind( applyBlogLoopPartVisibility );
			} );
		} );
		applyBlogLoopPartVisibility();
	}

	function bindFooterSettings() {
		if ( typeof wp === 'undefined' || ! wp.customize ) {
			return;
		}

		function getFooterRoot() {
			return document.querySelector( 'footer.site-footer' );
		}

		function escapeHtml( str ) {
			var d = document.createElement( 'div' );
			d.textContent = str == null ? '' : String( str );
			return d.innerHTML;
		}

		function applyFooterBrandInfoVisibility( show ) {
			var el = document.querySelector( 'footer.site-footer .site-footer__brand' );
			if ( ! el ) {
				return;
			}
			el.hidden = ! show;
		}

		function applyFooterSocialVisibility( show ) {
			var el = document.querySelector( 'footer.site-footer .site-footer__socials' );
			if ( ! el ) {
				return;
			}
			el.hidden = ! show;
		}

		function applyFooterNewsletterVisibility( show ) {
			var el = document.querySelector( 'footer.site-footer .site-footer__subscribe' );
			if ( ! el ) {
				return;
			}
			el.hidden = ! show;
		}

		function getCustomizeSettingValue( id, fallback ) {
			try {
				if ( wp.customize.instance && wp.customize.instance( id ) ) {
					var v = wp.customize.instance( id ).get();
					return v == null ? fallback : v;
				}
			} catch ( err ) {
				// Setting not registered in this preview context.
			}
			return fallback;
		}

		/** Matches footer-1.php: omit .site-footer__top when brand and social are both off. */
		function syncFooterTopVisibility() {
			var top = document.querySelector( 'footer.site-footer .site-footer__top' );
			if ( ! top ) {
				return;
			}
			var showBrand = troveCustomizeBool( getCustomizeSettingValue( 'trove_footer_show_brand_info', true ) );
			var showSocial = troveCustomizeBool( getCustomizeSettingValue( 'trove_footer_show_social_links', true ) );
			top.hidden = ! showBrand && ! showSocial;
		}

		/** Matches PHP Trove\get_footer_brand_name() (empty mod => site name). */
		function applyFooterBrandTitle( modVal ) {
			var el = document.querySelector( 'footer.site-footer .site-footer__title' );
			if ( ! el ) {
				return;
			}
			var raw = modVal == null ? '' : String( modVal ).trim();
			var text = raw !== '' ? raw : String( getCustomizeSettingValue( 'blogname', '' ) || '' );
			el.textContent = text;
		}

		/** Matches PHP Trove\get_footer_brand_description() (empty mod => tagline). */
		function applyFooterBrandDescription( modVal ) {
			var el = document.querySelector( 'footer.site-footer .site-footer__description' );
			if ( ! el ) {
				return;
			}
			var raw = modVal == null ? '' : String( modVal ).trim();
			var text = raw !== '' ? raw : String( getCustomizeSettingValue( 'blogdescription', '' ) || '' );
			el.textContent = text;
		}

		function getFooterPreviewConfig() {
			return typeof troveCustomizerPreview !== 'undefined' && troveCustomizerPreview.footer
				? troveCustomizerPreview.footer
				: {};
		}

		/** Matches PHP Trove\get_footer_newsletter_title() (empty mod => translated default). */
		function applyFooterNewsletterTitle( modVal ) {
			var el = document.querySelector( 'footer.site-footer .site-footer__subscribe-title' );
			if ( ! el ) {
				return;
			}
			var raw = modVal == null ? '' : String( modVal ).trim();
			var cfg = getFooterPreviewConfig();
			var fallback = cfg.newsletterTitleDefault || 'Subscribe to our emails';
			el.textContent = raw !== '' ? raw : String( fallback );
		}

		/** Matches PHP Trove\get_footer_newsletter_description() (empty mod => translated default). */
		function applyFooterNewsletterDescription( modVal ) {
			var el = document.querySelector( 'footer.site-footer .site-footer__subscribe-description' );
			if ( ! el ) {
				return;
			}
			var raw = modVal == null ? '' : String( modVal ).trim();
			var cfg = getFooterPreviewConfig();
			var fallback = cfg.newsletterDescriptionDefault || '';
			// Strip tags to detect empty rich text; mirrors PHP wp_strip_all_tags trim check.
			var tmp = document.createElement( 'div' );
			tmp.innerHTML = raw;
			if ( ( tmp.textContent || '' ).trim() !== '' ) {
				el.innerHTML = raw;
				return;
			}
			el.innerHTML = escapeHtml( fallback );
		}

		function applyFooterCopyrightText( customCopy ) {
			var inner = document.querySelector( 'footer.site-footer .site-footer__copy-inner' );
			if ( ! inner ) {
				return;
			}
			var raw = customCopy == null ? '' : String( customCopy ).trim();
			if ( raw !== '' ) {
				inner.textContent = raw;
				return;
			}
			var cfg =
				typeof troveCustomizerPreview !== 'undefined' && troveCustomizerPreview.footer
					? troveCustomizerPreview.footer
					: {};
			var rights = cfg.allRightsReserved || 'All rights reserved.';
			var year = new Date().getUTCFullYear();
			var siteName = '';
			try {
				if ( wp.customize.instance && wp.customize.instance( 'blogname' ) ) {
					siteName = wp.customize.instance( 'blogname' ).get() || '';
				}
			} catch ( err ) {
				siteName = '';
			}
			inner.innerHTML =
				'&copy; ' +
				escapeHtml( year ) +
				' ' +
				escapeHtml( siteName ) +
				'. ' +
				escapeHtml( rights );
		}

		wp.customize( 'trove_footer_show_brand_info', function ( value ) {
			function onChange( newVal ) {
				applyFooterBrandInfoVisibility( troveCustomizeBool( newVal ) );
				syncFooterTopVisibility();
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_footer_show_social_links', function ( value ) {
			function onChange( newVal ) {
				applyFooterSocialVisibility( troveCustomizeBool( newVal ) );
				syncFooterTopVisibility();
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_footer_show_newsletter', function ( value ) {
			function onChange( newVal ) {
				applyFooterNewsletterVisibility( troveCustomizeBool( newVal ) );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_footer_newsletter_title', function ( value ) {
			function onChange( newVal ) {
				applyFooterNewsletterTitle( newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_footer_newsletter_description', function ( value ) {
			function onChange( newVal ) {
				applyFooterNewsletterDescription( newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_footer_brand_name', function ( value ) {
			function onChange( newVal ) {
				applyFooterBrandTitle( newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_footer_brand_description', function ( value ) {
			function onChange( newVal ) {
				applyFooterBrandDescription( newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_footer_copy_text', function ( value ) {
			function onChange( newVal ) {
				applyFooterCopyrightText( newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'blogname', function ( value ) {
			function onChange() {
				var copySetting = wp.customize.instance( 'trove_footer_copy_text' );
				if ( copySetting && ! String( copySetting.get() || '' ).trim() ) {
					applyFooterCopyrightText( '' );
				}
				var brandNameSetting = wp.customize.instance( 'trove_footer_brand_name' );
				if ( brandNameSetting && ! String( brandNameSetting.get() || '' ).trim() ) {
					applyFooterBrandTitle( '' );
				}
			}
			value.bind( onChange );
		} );

		wp.customize( 'blogdescription', function ( value ) {
			function onChange() {
				var brandDescSetting = wp.customize.instance( 'trove_footer_brand_description' );
				if ( brandDescSetting && ! String( brandDescSetting.get() || '' ).trim() ) {
					applyFooterBrandDescription( '' );
				}
			}
			value.bind( onChange );
		} );
	}

	/*
	 * Run immediately in the preview iframe. WordPress does not fire
	 * wp.customize.bind( 'ready' ) here — the core script triggers
	 * 'preview-ready' instead, and our file loads after that, so
	 * binding('ready') never runs and body font / colors never updated.
	 * wp.customize( 'setting_id', fn ) defers until each setting exists.
	 */
	if ( typeof wp !== 'undefined' && wp.customize ) {
		bindGlobalColors();
		bindTypography();
		bindGoogleFont();
		bindHeaderSettings();
		bindBlogLayout();
		bindFooterSettings();
	}
} )();
