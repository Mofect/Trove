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
			n = Math.max( 10, Math.min( 32, n ) );
			return n + 'px';
		}
		var f = Math.max( 0.5, Math.min( 2.5, v ) );
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

	function applyTypographyVar( entry, newVal ) {
		var mode = entry.mode || 'raw';
		var v = '';
		if ( mode === 'weight' ) {
			var w = parseInt( newVal, 10 );
			if ( isNaN( w ) ) {
				w = 400;
			}
			var fam = '';
			if ( typeof wp !== 'undefined' && wp.customize ) {
				fam = wp.customize( 'trove_google_font_family' ).get();
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
					applyTypographyVar( entry, newVal );
				}
				value.bind( onChange );
				onChange( value.get() );
			} );
		} );

		function applySizeVar() {
			var size = wp.customize( 'trove_font_base_size' ).get();
			var u = wp.customize( 'trove_font_base_size_unit' ).get();
			document.documentElement.style.setProperty(
				'--trove_font_base_size',
				fontBaseSizeCss( size, u )
			);
		}
		function applyLineVar() {
			var lh = wp.customize( 'trove_font_base_line_height' ).get();
			var u = wp.customize( 'trove_font_base_line_height_unit' ).get();
			document.documentElement.style.setProperty(
				'--trove_font_base_line_height',
				fontBaseLineHeightCss( lh, u )
			);
		}

		wp.customize( 'trove_font_base_size', function ( value ) {
			value.bind( applySizeVar );
		} );
		wp.customize( 'trove_font_base_size_unit', function ( value ) {
			value.bind( applySizeVar );
		} );
		wp.customize( 'trove_font_base_line_height', function ( value ) {
			value.bind( applyLineVar );
		} );
		wp.customize( 'trove_font_base_line_height_unit', function ( value ) {
			value.bind( applyLineVar );
		} );
		applySizeVar();
		applyLineVar();

		var weightEntry = cfg.trove_font_base_weight;
		if ( weightEntry && weightEntry.mode === 'weight' ) {
			wp.customize( 'trove_google_font_family', function ( value ) {
				value.bind( function () {
					var wv = wp.customize( 'trove_font_base_weight' ).get();
					applyTypographyVar( weightEntry, wv );
				} );
			} );
		}
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
		wp.customize( 'trove_google_font_family', function ( value ) {
			function onChange( newVal ) {
				applyGoogleFont( newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
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
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			bindGlobalColors();
			bindTypography();
			bindGoogleFont();
			bindHeaderSettings();
		} );
	} else {
		bindGlobalColors();
		bindTypography();
		bindGoogleFont();
		bindHeaderSettings();
	}
} )();
