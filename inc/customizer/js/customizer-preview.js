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

	function bindButtonThemeVars() {
		if ( typeof wp === 'undefined' || ! wp.customize ) {
			return;
		}

		function clampRadius( raw ) {
			var n = parseInt( raw, 10 );
			if ( isNaN( n ) ) {
				n = 6;
			}
			return Math.max( 0, Math.min( 32, n ) );
		}

		function clampWidth( raw ) {
			var n = parseInt( raw, 10 );
			if ( isNaN( n ) ) {
				n = 1;
			}
			return Math.max( 0, Math.min( 8, n ) );
		}

		function clampTextFontSize( raw ) {
			var n = parseInt( raw, 10 );
			if ( isNaN( n ) ) {
				n = 14;
			}
			return Math.max( 10, Math.min( 24, n ) );
		}

		wp.customize( 'trove_button_border_radius', function ( value ) {
			function onChange( newVal ) {
				document.documentElement.style.setProperty(
					'--trove-button-border-radius',
					clampRadius( newVal ) + 'px'
				);
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_button_border_width', function ( value ) {
			function onChange( newVal ) {
				document.documentElement.style.setProperty(
					'--trove-button-border-width',
					clampWidth( newVal ) + 'px'
				);
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_button_text_font_size', function ( value ) {
			function onChange( newVal ) {
				document.documentElement.style.setProperty(
					'--trove-button-font-size',
					clampTextFontSize( newVal ) + 'px'
				);
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_button_secondary_border_radius', function ( value ) {
			function onChange( newVal ) {
				document.documentElement.style.setProperty(
					'--trove-button-secondary-border-radius',
					clampRadius( newVal ) + 'px'
				);
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_button_secondary_border_width', function ( value ) {
			function onChange( newVal ) {
				document.documentElement.style.setProperty(
					'--trove-button-secondary-border-width',
					clampWidth( newVal ) + 'px'
				);
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_button_secondary_text_font_size', function ( value ) {
			function onChange( newVal ) {
				document.documentElement.style.setProperty(
					'--trove-button-secondary-font-size',
					clampTextFontSize( newVal ) + 'px'
				);
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		function parsePaddingJson( raw ) {
			var fb = { top: 5, right: 20, bottom: 5, left: 20 };
			var o;
			try {
				o = typeof raw === 'string' && raw ? JSON.parse( raw ) : {};
			} catch ( err ) {
				o = {};
			}
			function px( v, d ) {
				var n = parseInt( v, 10 );
				if ( isNaN( n ) ) {
					return d;
				}
				return Math.max( 0, Math.min( 60, n ) );
			}
			return {
				top: px( o.top, fb.top ),
				right: px( o.right, fb.right ),
				bottom: px( o.bottom, fb.bottom ),
				left: px( o.left, fb.left ),
			};
		}

		function paddingVarsDecl( sides ) {
			return (
				'--trove-button-padding-top:' +
				sides.top +
				'px;--trove-button-padding-right:' +
				sides.right +
				'px;--trove-button-padding-bottom:' +
				sides.bottom +
				'px;--trove-button-padding-left:' +
				sides.left +
				'px'
			);
		}

		function secondaryPaddingVarsDecl( sides ) {
			return (
				'--trove-button-secondary-padding-top:' +
				sides.top +
				'px;--trove-button-secondary-padding-right:' +
				sides.right +
				'px;--trove-button-secondary-padding-bottom:' +
				sides.bottom +
				'px;--trove-button-secondary-padding-left:' +
				sides.left +
				'px'
			);
		}

		function syncButtonPaddingPreview() {
			var bp =
				typeof troveCustomizerPreview !== 'undefined' && troveCustomizerPreview.responsiveBreakpoints
					? troveCustomizerPreview.responsiveBreakpoints
					: { tablet: 1024, phone: 781 };
			var pc = wp.customize( 'trove_button_padding_pc' ).get();
			var tb = wp.customize( 'trove_button_padding_tablet' ).get();
			var ph = wp.customize( 'trove_button_padding_phone' ).get();
			var css = ':root{' + paddingVarsDecl( parsePaddingJson( pc ) ) + '}';
			css +=
				'@media (max-width:' +
				bp.tablet +
				'px){:root{' +
				paddingVarsDecl( parsePaddingJson( tb ) ) +
				'}}';
			css +=
				'@media (max-width:' +
				bp.phone +
				'px){:root{' +
				paddingVarsDecl( parsePaddingJson( ph ) ) +
				'}}';
			var el = document.getElementById( 'trove-preview-button-padding' );
			if ( ! el ) {
				el = document.createElement( 'style' );
				el.id = 'trove-preview-button-padding';
				document.head.appendChild( el );
			}
			el.textContent = css;
		}

		function syncButtonSecondaryPaddingPreview() {
			var bp =
				typeof troveCustomizerPreview !== 'undefined' && troveCustomizerPreview.responsiveBreakpoints
					? troveCustomizerPreview.responsiveBreakpoints
					: { tablet: 1024, phone: 781 };
			var pc = wp.customize( 'trove_button_secondary_padding_pc' ).get();
			var tb = wp.customize( 'trove_button_secondary_padding_tablet' ).get();
			var ph = wp.customize( 'trove_button_secondary_padding_phone' ).get();
			var css = ':root{' + secondaryPaddingVarsDecl( parsePaddingJson( pc ) ) + '}';
			css +=
				'@media (max-width:' +
				bp.tablet +
				'px){:root{' +
				secondaryPaddingVarsDecl( parsePaddingJson( tb ) ) +
				'}}';
			css +=
				'@media (max-width:' +
				bp.phone +
				'px){:root{' +
				secondaryPaddingVarsDecl( parsePaddingJson( ph ) ) +
				'}}';
			var el = document.getElementById( 'trove-preview-button-secondary-padding' );
			if ( ! el ) {
				el = document.createElement( 'style' );
				el.id = 'trove-preview-button-secondary-padding';
				document.head.appendChild( el );
			}
			el.textContent = css;
		}

		var padIds = [
			'trove_button_padding_pc',
			'trove_button_padding_tablet',
			'trove_button_padding_phone',
		];
		var padReady = 0;
		padIds.forEach( function ( sid ) {
			wp.customize( sid, function ( value ) {
				value.bind( syncButtonPaddingPreview );
				padReady += 1;
				if ( padReady === padIds.length ) {
					syncButtonPaddingPreview();
				}
			} );
		} );

		var pad2Ids = [
			'trove_button_secondary_padding_pc',
			'trove_button_secondary_padding_tablet',
			'trove_button_secondary_padding_phone',
		];
		var pad2Ready = 0;
		pad2Ids.forEach( function ( sid ) {
			wp.customize( sid, function ( value ) {
				value.bind( syncButtonSecondaryPaddingPreview );
				pad2Ready += 1;
				if ( pad2Ready === pad2Ids.length ) {
					syncButtonSecondaryPaddingPreview();
				}
			} );
		} );
	}

	function bindTextFieldThemeVars() {
		if ( typeof wp === 'undefined' || ! wp.customize ) {
			return;
		}
		function clamp( raw, min, max, d ) {
			var n = parseInt( raw, 10 );
			if ( isNaN( n ) ) {
				n = d;
			}
			return Math.max( min, Math.min( max, n ) );
		}
		var map = [
			[ 'trove_input_field_height', '--trove-input-field-height', 28, 80, 42 ],
			[ 'trove_input_field_text_size', '--trove-input-field-text-size', 10, 24, 16 ],
			[ 'trove_input_field_border_width', '--trove-input-field-border-width', 0, 8, 1 ],
			[ 'trove_input_field_border_radius', '--trove-input-field-border-radius', 0, 32, 3 ],
			[ 'trove_textarea_field_height', '--trove-textarea-field-height', 60, 400, 120 ],
			[ 'trove_textarea_field_text_size', '--trove-textarea-field-text-size', 10, 24, 16 ],
			[ 'trove_textarea_field_border_width', '--trove-textarea-field-border-width', 0, 8, 1 ],
			[ 'trove_textarea_field_border_radius', '--trove-textarea-field-border-radius', 0, 32, 3 ],
		];
		map.forEach( function ( row ) {
			wp.customize( row[ 0 ], function ( value ) {
				function onChange( newVal ) {
					document.documentElement.style.setProperty(
						row[ 1 ],
						clamp( newVal, row[ 2 ], row[ 3 ], row[ 4 ] ) + 'px'
					);
				}
				value.bind( onChange );
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

	function fontLetterSpacingCss( lsVal, unitVal ) {
		var unit = unitVal ? String( unitVal ).toLowerCase() : 'em';
		if ( unit !== 'px' && unit !== 'rem' && unit !== 'em' ) {
			unit = 'em';
		}
		var v = parseFloat( lsVal );
		if ( isNaN( v ) ) {
			v = 0;
		}
		v = Math.max( -10, Math.min( 10, v ) );
		return formatCssFloatStr( v ) + unit;
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

		function getCustomizeValue( id, fallback ) {
			try {
				var inst = wp.customize.instance ? wp.customize.instance( id ) : null;
				if ( inst && typeof inst.get === 'function' ) {
					return inst.get();
				}
			} catch ( err ) {}
			return fallback;
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

		function applyNumberUnitVar( numberSetting, pair, changedDevice ) {
			var unitVal = getCustomizeValue( pair.unit, 'px' );
			var isLineHeight =
				pair.mode === 'line_height' ||
				/line-height/.test( String( pair.var || '' ) ) ||
				/line_height/.test( String( numberSetting || '' ) );
			var isLetterSpacing =
				pair.mode === 'letter_spacing' ||
				/letter-spacing/.test( String( pair.var || '' ) ) ||
				/letter_spacing/.test( String( numberSetting || '' ) );
			var mode = isLineHeight ? 'line_height' : ( isLetterSpacing ? 'letter_spacing' : 'size' );

			if ( pair.responsive ) {
				var cfg =
					typeof troveCustomizerPreview !== 'undefined' && troveCustomizerPreview.responsiveBreakpoints
						? troveCustomizerPreview.responsiveBreakpoints
						: { tablet: 1024, phone: 781 };
				var pcVal = getCustomizeValue( numberSetting + '_pc', 0 );
				var tbVal = getCustomizeValue( numberSetting + '_tablet', pcVal );
				var phVal = getCustomizeValue( numberSetting + '_phone', tbVal );
				var format = mode === 'line_height'
					? fontBaseLineHeightCss
					: ( mode === 'letter_spacing' ? fontLetterSpacingCss : fontBaseSizeCss );
				var css =
					':root{' +
					pair.var +
					':' +
					format( pcVal, unitVal ) +
					'}' +
					'@media (max-width:' +
					cfg.tablet +
					'px){:root{' +
					pair.var +
					':' +
					format( tbVal, unitVal ) +
					'}}' +
					'@media (max-width:' +
					cfg.phone +
					'px){:root{' +
					pair.var +
					':' +
					format( phVal, unitVal ) +
					'}}';
				var sid = numberSetting.replace( /[^a-z0-9_-]/gi, '-' );
				var el = document.getElementById( 'trove-preview-typography-' + sid );
				if ( ! el ) {
					el = document.createElement( 'style' );
					el.id = 'trove-preview-typography-' + sid;
					document.head.appendChild( el );
				}
				el.textContent = css;
				var activeDevice = changedDevice || 'pc';
				var currentVal = pcVal;
				if ( activeDevice === 'tablet' ) {
					currentVal = tbVal;
				} else if ( activeDevice === 'phone' ) {
					currentVal = phVal;
				}
				document.documentElement.style.setProperty( pair.var, format( currentVal, unitVal ) );
				return;
			}

			var numberVal = getCustomizeValue( numberSetting, 0 );
			var css = mode === 'line_height'
				? fontBaseLineHeightCss( numberVal, unitVal )
				: ( mode === 'letter_spacing' ? fontLetterSpacingCss( numberVal, unitVal ) : fontBaseSizeCss( numberVal, unitVal ) );
			document.documentElement.style.setProperty( pair.var, css );
		}

		var numberUnitPairs =
			typeof troveCustomizerPreview !== 'undefined' && troveCustomizerPreview.numberUnitPairs
				? troveCustomizerPreview.numberUnitPairs
				: {};
		Object.keys( numberUnitPairs ).forEach( function ( numberSetting ) {
			var pair = numberUnitPairs[ numberSetting ];
			var valueIds = pair.responsive
				? [ numberSetting + '_pc', numberSetting + '_tablet', numberSetting + '_phone' ]
				: [ numberSetting ];
			var lastChangedDevice = 'pc';
			var ready = 0;
			valueIds.forEach( function ( valueId ) {
				wp.customize( valueId, function ( value ) {
					var dev = 'pc';
					if ( pair.responsive ) {
						if ( /_tablet$/.test( valueId ) ) {
							dev = 'tablet';
						} else if ( /_phone$/.test( valueId ) ) {
							dev = 'phone';
						}
					}
					value.bind( function () {
						lastChangedDevice = dev;
						applyNumberUnitVar( numberSetting, pair, dev );
					} );
					ready += 1;
					if ( ready === valueIds.length ) {
						wp.customize( pair.unit, function ( unitValue ) {
							unitValue.bind( function () {
								applyNumberUnitVar( numberSetting, pair, lastChangedDevice );
							} );
							applyNumberUnitVar( numberSetting, pair, lastChangedDevice );
						} );
					}
				} );
			} );
		} );

		function deviceFromPreviewedDevice( previewDevice ) {
			if ( previewDevice === 'tablet' ) {
				return 'tablet';
			}
			if ( previewDevice === 'mobile' ) {
				return 'phone';
			}
			return 'pc';
		}

		function deviceFromFrameElementClass() {
			var frame = window.frameElement;
			if ( ! frame ) {
				return null;
			}
			var node = frame;
			while ( node ) {
				if ( node.classList ) {
					if ( node.classList.contains( 'preview-mobile' ) ) {
						return 'phone';
					}
					if ( node.classList.contains( 'preview-tablet' ) ) {
						return 'tablet';
					}
					if ( node.classList.contains( 'preview-desktop' ) ) {
						return 'pc';
					}
				}
				node = node.parentElement;
			}
			return null;
		}

		function deviceFromParentOverlayClass() {
			try {
				if ( !window.parent || !window.parent.document ) {
					return null;
				}
				var doc = window.parent.document;
				var targets = [
					doc.querySelector( '.wp-full-overlay' ),
					doc.body,
					doc.documentElement,
				];
				var i;
				for ( i = 0; i < targets.length; i++ ) {
					var el = targets[ i ];
					if ( !el || !el.classList ) {
						continue;
					}
					if ( el.classList.contains( 'preview-mobile' ) ) {
						return 'phone';
					}
					if ( el.classList.contains( 'preview-tablet' ) ) {
						return 'tablet';
					}
					if ( el.classList.contains( 'preview-desktop' ) ) {
						return 'pc';
					}
				}
			} catch ( err ) {}
			return null;
		}

		function detectResponsiveDevice() {
			var fromParent = deviceFromParentOverlayClass();
			if ( fromParent ) {
				return fromParent;
			}
			var fromFrame = deviceFromFrameElementClass();
			if ( fromFrame ) {
				return fromFrame;
			}
			return 'pc';
		}

		var currentResponsiveDevice = detectResponsiveDevice();

		function refreshResponsiveTypographyForDevice( device ) {
			var resolved = device;
			if ( resolved !== 'pc' && resolved !== 'tablet' && resolved !== 'phone' ) {
				resolved = detectResponsiveDevice();
			}
			currentResponsiveDevice = resolved;
			Object.keys( numberUnitPairs ).forEach( function ( numberSetting ) {
				var pair = numberUnitPairs[ numberSetting ];
				if ( pair && pair.responsive ) {
					applyNumberUnitVar( numberSetting, pair, resolved );
				}
			} );
		}

		if ( wp.customize && wp.customize.previewedDevice ) {
			wp.customize.previewedDevice.bind( function ( previewDevice ) {
				var device = deviceFromPreviewedDevice( previewDevice );
				currentResponsiveDevice = device;
				refreshResponsiveTypographyForDevice( device );
			} );
		}
		if ( wp.customize && wp.customize.preview && typeof wp.customize.preview.bind === 'function' ) {
			wp.customize.preview.bind( 'trove-responsive-device-change', function ( payload ) {
				var device = payload && payload.device ? String( payload.device ) : 'pc';
				if ( device !== 'pc' && device !== 'tablet' && device !== 'phone' ) {
					device = currentResponsiveDevice || 'pc';
				}
				currentResponsiveDevice = device;
				refreshResponsiveTypographyForDevice( device );
			} );
		}
		if ( typeof MutationObserver !== 'undefined' && window.frameElement ) {
			var observedNodes = [];
			var n = window.frameElement;
			while ( n ) {
				observedNodes.push( n );
				n = n.parentElement;
			}
			observedNodes.forEach( function ( node ) {
				var frameDeviceObserver = new MutationObserver( function () {
					refreshResponsiveTypographyForDevice( null );
				} );
				frameDeviceObserver.observe( node, { attributes: true, attributeFilter: [ 'class' ] } );
			} );
		}
		// Failsafe: if any device-switch event chain is missed, keep preview typography
		// in sync by polling current device and reapplying responsive vars on change.
		(function startResponsiveDeviceWatcher() {
			var lastDevice = currentResponsiveDevice || detectResponsiveDevice();
			window.setInterval( function () {
				var next = detectResponsiveDevice();
				if ( next !== lastDevice ) {
					lastDevice = next;
				}
				currentResponsiveDevice = next;
				// Always reapply in case device class changed but message chain was missed.
				refreshResponsiveTypographyForDevice( next );
			}, 220 );
		})();
		refreshResponsiveTypographyForDevice( null );

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

	function getFooterEl() {
		return document.querySelector( 'footer.site-footer' );
	}

	function applyFooterTopBorder( newVal ) {
		var footer = getFooterEl();
		var enabled =
			newVal === true ||
			newVal === 1 ||
			newVal === '1' ||
			newVal === 'true';

		if ( ! footer ) {
			return;
		}
		footer.classList.toggle( 'trove-footer-has-top-border', !! enabled );
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

		wp.customize( 'trove_footer_top_border', function ( value ) {
			function onChange( newVal ) {
				applyFooterTopBorder( newVal );
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
				var buttons = document.querySelector( 'header.site-header .site-header__buttons' );
				if ( ! buttons ) {
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
				observer.observe( buttons, { childList: true, subtree: true } );
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

	/**
	 * General → Layout live preview: :root vars + .site-main + wide chrome (postMessage).
	 */
	function trovePreviewSyncLayout() {
		if ( typeof wp === 'undefined' || ! wp.customize ) {
			return;
		}

		function clampContentWidth( raw ) {
			var n = parseInt( raw, 10 );
			if ( isNaN( n ) ) {
				return 800;
			}
			if ( n < 600 || n > 1400 ) {
				return 800;
			}
			return n;
		}

		function clampContentWideWidth( raw ) {
			var n = parseInt( raw, 10 );
			if ( isNaN( n ) ) {
				return 1200;
			}
			if ( n < 800 || n > 2400 ) {
				return 1200;
			}
			return n;
		}

		function clampVerticalSpacing( raw ) {
			var n = parseInt( raw, 10 );
			if ( isNaN( n ) ) {
				return 0;
			}
			return Math.min( 200, Math.max( 0, n ) );
		}

		function clampHorizontalSpacing( raw ) {
			var n = parseInt( raw, 10 );
			if ( isNaN( n ) ) {
				return 0;
			}
			return Math.min( 200, Math.max( 0, n ) );
		}

		function getLayoutSetting( id, fallback ) {
			try {
				var s = wp.customize( id );
				if ( s && typeof s.get === 'function' ) {
					return s.get();
				}
			} catch ( err ) {}
			return fallback;
		}

		var rawNarrow = getLayoutSetting( 'trove_content_width', 800 );
		var rawWide = getLayoutSetting( 'trove_content_wide_width', 1200 );
		var rawVert = getLayoutSetting( 'trove_content_vertical_spacing', 48 );
		var rawHoriz = getLayoutSetting( 'trove_content_horizontal_spacing', 20 );

		var narrowPx = clampContentWidth( rawNarrow ) + 'px';
		var widePx = clampContentWideWidth( rawWide ) + 'px';
		var vertPx = clampVerticalSpacing( rawVert ) + 'px';
		var horizPx = clampHorizontalSpacing( rawHoriz ) + 'px';

		var root = document.documentElement;
		root.style.setProperty( '--trove-content-width', narrowPx );
		root.style.setProperty( '--trove-content-wide-width', widePx );
		root.style.setProperty( '--trove-wide', widePx );
		root.style.setProperty( '--trove-content-vertical-spacing', vertPx );
		root.style.setProperty( '--trove-content-horizontal-spacing', horizPx );

		var main = document.querySelector( 'main.site-main' );
		if ( main ) {
			var useWide =
				document.body &&
				( document.body.classList.contains( 'trove-blog-layout--grid' ) ||
					document.body.classList.contains( 'trove-blog-layout--masonry' ) ||
					document.body.classList.contains( 'woocommerce' ) ||
					document.body.classList.contains( 'woocommerce-page' ) );
			main.style.maxWidth = useWide ? widePx : narrowPx;
			main.style.paddingTop = vertPx;
			main.style.paddingBottom = vertPx;
			main.style.paddingLeft = horizPx;
			main.style.paddingRight = horizPx;
		}

		document.querySelectorAll( '.site-header__inner, .site-footer__inner, .trove-announcement-bar__inner' ).forEach( function ( el ) {
			el.style.maxWidth = widePx;
			el.style.paddingLeft = horizPx;
			el.style.paddingRight = horizPx;
		} );
	}

	function bindBlogLayout() {
		if ( typeof wp === 'undefined' || ! wp.customize ) {
			return;
		}

		var posKeys = [ 'top', 'left', 'right' ];
		var postsPrefix = 'trove_blog_';

		if ( document.body ) {
			if ( document.body.classList.contains( 'search' ) ) {
				postsPrefix = 'trove_search_page_';
			} else if ( document.body.classList.contains( 'author' ) ) {
				postsPrefix = 'trove_author_page_';
			} else if (
				document.body.classList.contains( 'category' ) ||
				document.body.classList.contains( 'tag' ) ||
				document.body.classList.contains( 'date' ) ||
				document.body.classList.contains( 'archive' )
			) {
				postsPrefix = 'trove_archive_page_';
			}
		}

		function postsSettingId( suffix ) {
			return postsPrefix + suffix;
		}

		function getBlogListFeaturedPos() {
			var pos = 'top';
			try {
				if ( wp.customize( postsSettingId( 'list_featured_position' ) ) ) {
					pos = wp.customize( postsSettingId( 'list_featured_position' ) ).get() || 'top';
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
			trovePreviewSyncLayout();
		}

		wp.customize( postsSettingId( 'layout' ), function ( value ) {
			function onChange( newVal ) {
				applyBlogLayout( newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( postsSettingId( 'list_featured_position' ), function ( value ) {
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

		wp.customize( postsSettingId( 'boxed_style' ), function ( value ) {
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

		wp.customize( postsSettingId( 'boxed_border_radius' ), function ( value ) {
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
			var showImg = getBlogPartSetting( postsSettingId( 'show_featured_image' ), true );
			var showD = getBlogPartSetting( postsSettingId( 'show_date' ), true );
			var showA = getBlogPartSetting( postsSettingId( 'show_author' ), true );
			var showR = getBlogPartSetting( postsSettingId( 'show_continue_reading' ), true );

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
			postsSettingId( 'show_featured_image' ),
			postsSettingId( 'show_date' ),
			postsSettingId( 'show_author' ),
			postsSettingId( 'show_continue_reading' ),
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

	/**
	 * General → Layout: bind postMessage settings to trovePreviewSyncLayout().
	 */
	function bindLayout() {
		if ( typeof wp === 'undefined' || ! wp.customize ) {
			return;
		}

		[
			'trove_content_width',
			'trove_content_wide_width',
			'trove_content_vertical_spacing',
			'trove_content_horizontal_spacing',
		].forEach( function ( sid ) {
			wp.customize( sid, function ( value ) {
				value.bind( trovePreviewSyncLayout );
				trovePreviewSyncLayout();
			} );
		} );
	}

	function bindWooSingleProduct() {
		if ( typeof wp === 'undefined' || ! wp.customize ) {
			return;
		}

		function normalizeStackedCols( raw ) {
			var n = parseInt( raw, 10 );
			if ( isNaN( n ) ) {
				n = 4;
			}
			return Math.max( 1, Math.min( 6, n ) );
		}

		function applyWooStackedGalleryColumns( raw ) {
			var cols = normalizeStackedCols( raw );
			var galleries = document.querySelectorAll(
				'.single-product .woocommerce-product-gallery.trove-woo-gallery--stacked, .woocommerce-product-gallery.trove-woo-gallery--stacked'
			);
			galleries.forEach( function ( gallery ) {
				Array.prototype.slice.call( gallery.classList ).forEach( function ( cls ) {
					if ( cls.indexOf( 'trove-woo-gallery--stacked-cols-' ) === 0 ) {
						gallery.classList.remove( cls );
					}
				} );
				gallery.classList.add( 'trove-woo-gallery--stacked-cols-' + cols );
			} );
		}

		function syncWooSingleProductSummaryMinHeight() {
			var products = document.querySelectorAll(
				'.single-product div.product, .woocommerce.single-product div.product'
			);
			products.forEach( function ( product ) {
				var summary = product.querySelector( 'div.summary' );
				var gallery =
					product.querySelector( '.woocommerce-product-gallery' ) ||
					product.querySelector( 'div.images' );
				if ( ! summary || ! gallery ) {
					return;
				}
				if ( window.matchMedia && window.matchMedia( '(max-width: 782px)' ).matches ) {
					summary.style.minHeight = '';
					return;
				}
				var stickyInner = summary.querySelector( ':scope > .trove-product-summary-sticky-inner' );
				var summaryMeasureTarget = stickyInner || summary;
				var galleryHeight = Math.ceil( gallery.getBoundingClientRect().height );
				var summaryHeight = Math.ceil( summaryMeasureTarget.getBoundingClientRect().height );
				var minHeight = Math.max( galleryHeight, summaryHeight );
				if ( minHeight > 0 ) {
					summary.style.minHeight = minHeight + 'px';
				}
			} );
		}

		function normalizeSliderPos( raw ) {
			var s = raw ? String( raw ).trim() : '';
			if ( s === 'left' || s === 'right' || s === 'bottom' ) {
				return s;
			}
			return 'bottom';
		}

		function applyWooSliderGalleryPosition( raw ) {
			var pos = normalizeSliderPos( raw );
			var galleries = document.querySelectorAll(
				'.single-product .woocommerce-product-gallery.trove-woo-gallery--slider, .woocommerce-product-gallery.trove-woo-gallery--slider'
			);
			galleries.forEach( function ( gallery ) {
				gallery.classList.remove(
					'trove-woo-slider-pos-left',
					'trove-woo-slider-pos-right',
					'trove-woo-slider-pos-bottom'
				);
				gallery.classList.add( 'trove-woo-slider-pos-' + pos );
			} );
			syncWooSingleProductSummaryMinHeight();
			window.setTimeout( function () {
				if ( window.jQuery ) {
					window.jQuery( window ).trigger( 'resize' );
				} else {
					window.dispatchEvent( new Event( 'resize' ) );
				}
				syncWooSingleProductSummaryMinHeight();
			}, 50 );
			window.setTimeout( syncWooSingleProductSummaryMinHeight, 180 );
		}

		wp.customize( 'trove_woo_single_product_gallery_stacked_columns', function ( value ) {
			function onChange( newVal ) {
				applyWooStackedGalleryColumns( newVal );
			}
			value.bind( onChange );
			onChange( value.get() );
		} );

		wp.customize( 'trove_woo_single_product_gallery_slider_position', function ( value ) {
			function onChange( newVal ) {
				applyWooSliderGalleryPosition( newVal );
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
		bindButtonThemeVars();
		bindTextFieldThemeVars();
		bindTypography();
		bindGoogleFont();
		bindHeaderSettings();
		bindLayout();
		bindWooSingleProduct();
		bindBlogLayout();
		bindFooterSettings();
	}
} )();
