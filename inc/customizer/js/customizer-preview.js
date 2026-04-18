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

		wp.customize( 'trove_header_dark_mode', function ( value ) {
			function applyHeaderThemeLogoForDarkMode( isDark ) {
				var img = document.querySelector(
					'header.site-header .site-header__brand img.site-header__logo'
				);
				if ( ! img || typeof wp === 'undefined' || ! wp.customize || ! wp.customize.instance ) {
					return;
				}
				var light = '';
				var dark = '';
				try {
					if ( wp.customize.instance( 'trove_header_logo' ) ) {
						light = String( wp.customize.instance( 'trove_header_logo' ).get() || '' ).trim();
					}
					if ( wp.customize.instance( 'trove_header_logo_dark' ) ) {
						dark = String( wp.customize.instance( 'trove_header_logo_dark' ).get() || '' ).trim();
					}
				} catch ( err ) {
					return;
				}
				if ( ! light && ! dark ) {
					return;
				}
				var next = isDark ? dark || light : light || '';
				if ( next && img.getAttribute( 'src' ) !== next ) {
					img.setAttribute( 'src', next );
				}
			}

			function onChange( newVal ) {
				var header = getHeaderRoot();
				if ( ! header ) {
					return;
				}
				var isDark = troveCustomizeBool( newVal );
				header.classList.toggle( 'light-scheme', ! isDark );
				applyHeaderThemeLogoForDarkMode( isDark );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_header_background', function ( value ) {
			function onChange( newVal ) {
				applyRootCssColorVar( '--trove-header-background', newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_header_text_color', function ( value ) {
			function onChange( newVal ) {
				applyRootCssColorVar( '--trove-header-text', newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_header_link_color', function ( value ) {
			function onChange( newVal ) {
				applyRootCssColorVar( '--trove-header-link', newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_header_link_hover_color', function ( value ) {
			function onChange( newVal ) {
				applyRootCssColorVar( '--trove-header-link-hover', newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_header_border_color', function ( value ) {
			function onChange( newVal ) {
				applyRootCssColorVar( '--trove-header-border', newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

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

		wp.customize( 'trove_footer_dark_mode', function ( value ) {
			function onChange( newVal ) {
				var footer = getFooterRoot();
				if ( ! footer ) {
					return;
				}
				var isDark = troveCustomizeBool( newVal );
				footer.classList.toggle( 'light-scheme', ! isDark );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_footer_background', function ( value ) {
			function onChange( newVal ) {
				applyRootCssColorVar( '--trove-footer-background', newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_footer_text_color', function ( value ) {
			function onChange( newVal ) {
				applyRootCssColorVar( '--trove-footer-text', newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_footer_link_color', function ( value ) {
			function onChange( newVal ) {
				applyRootCssColorVar( '--trove-footer-link', newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_footer_link_hover_color', function ( value ) {
			function onChange( newVal ) {
				applyRootCssColorVar( '--trove-footer-link-hover', newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_footer_border_color', function ( value ) {
			function onChange( newVal ) {
				applyRootCssColorVar( '--trove-footer-border', newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

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
		bindFooterSettings();
	}
} )();
