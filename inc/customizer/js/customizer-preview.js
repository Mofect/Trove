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

	function bindHeaderSettings() {
		if ( typeof wp === 'undefined' || ! wp.customize ) {
			return;
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
	}
} )();
