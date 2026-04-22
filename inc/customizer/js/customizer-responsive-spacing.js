/**
 * Trove Customizer: responsive spacing box (TRBL JSON per device + link).
 */
( function ( api ) {
	"use strict";

	function troveDeviceToPreviewDevice( trove ) {
		var map = { pc: "desktop", tablet: "tablet", phone: "mobile" };
		return map[ trove ] || null;
	}

	function setCustomizerPreviewDevice( trove ) {
		var previewKey = troveDeviceToPreviewDevice( trove );
		if (
			! previewKey ||
			! api.previewedDevice ||
			typeof api.previewedDevice.get !== "function" ||
			typeof api.previewedDevice.set !== "function"
		) {
			return;
		}
		var list = api.settings && api.settings.previewableDevices;
		if ( list && ! Object.prototype.hasOwnProperty.call( list, previewKey ) ) {
			return;
		}
		try {
			if ( api.previewedDevice.get() !== previewKey ) {
				api.previewedDevice.set( previewKey );
			}
		} catch ( err ) {}
	}

	function parseSettings( root ) {
		var raw = root.getAttribute( "data-settings" );
		if ( ! raw ) {
			return null;
		}
		try {
			return JSON.parse( raw );
		} catch ( e ) {
			return null;
		}
	}

	function parseJson( str, fallback ) {
		if ( ! str || typeof str !== "string" ) {
			return fallback;
		}
		try {
			var o = JSON.parse( str );
			return typeof o === "object" && o !== null ? o : fallback;
		} catch ( e ) {
			return fallback;
		}
	}

	function clamp( n, min, max ) {
		var v = parseInt( n, 10 );
		if ( isNaN( v ) ) {
			v = 0;
		}
		return Math.max( min, Math.min( max, v ) );
	}

	function buildJson( top, right, bottom, left, linked, min, max ) {
		return JSON.stringify( {
			top: clamp( top, min, max ),
			right: clamp( right, min, max ),
			bottom: clamp( bottom, min, max ),
			left: clamp( left, min, max ),
			linked: !! linked,
		} );
	}

	function initOne( root ) {
		if ( ! root || root.getAttribute( "data-trove-spacing-wired" ) === "1" ) {
			return;
		}

		var settingsMap = parseSettings( root );
		if ( ! settingsMap || ! settingsMap.pc || ! settingsMap.tablet || ! settingsMap.phone ) {
			return;
		}

		var min = parseInt( root.getAttribute( "data-min" ), 10 );
		if ( isNaN( min ) ) {
			min = 0;
		}
		var max = parseInt( root.getAttribute( "data-max" ), 10 );
		if ( isNaN( max ) ) {
			max = 60;
		}

		var fb = { top: 5, right: 20, bottom: 5, left: 20, linked: false };

		var topI = root.querySelector( ".trove-sb-top" );
		var rightI = root.querySelector( ".trove-sb-right" );
		var bottomI = root.querySelector( ".trove-sb-bottom" );
		var leftI = root.querySelector( ".trove-sb-left" );
		var linkBtn = root.querySelector( ".trove-sb-link-toggle" );
		var tabs = root.querySelectorAll( ".trove-r-tab" );
		if ( ! topI || ! rightI || ! bottomI || ! leftI || ! linkBtn ) {
			return;
		}

		var active = "pc";
		var linked = false;
		var settings = {};
		var remaining = 3;

		function readInputs() {
			return {
				top: topI.value,
				right: rightI.value,
				bottom: bottomI.value,
				left: leftI.value,
			};
		}

		function writeFromObj( o ) {
			topI.value = String( o.top );
			rightI.value = String( o.right );
			bottomI.value = String( o.bottom );
			leftI.value = String( o.left );
			linked = !! o.linked;
			updateLinkUi();
		}

		function updateLinkUi() {
			var icon = linkBtn.querySelector( ".dashicons" );
			if ( icon ) {
				icon.className = "dashicons " + ( linked ? "dashicons-admin-links" : "dashicons-editor-unlink" );
			}
			linkBtn.setAttribute( "aria-pressed", linked ? "true" : "false" );
		}

		function numOr( o, key, fallback ) {
			if ( o[ key ] === undefined || o[ key ] === null || o[ key ] === "" ) {
				return fallback;
			}
			return clamp( o[ key ], min, max );
		}

		function load( device ) {
			var s = settings[ device ];
			if ( ! s ) {
				return;
			}
			var o = parseJson( s.get(), fb );
			writeFromObj( {
				top: numOr( o, "top", fb.top ),
				right: numOr( o, "right", fb.right ),
				bottom: numOr( o, "bottom", fb.bottom ),
				left: numOr( o, "left", fb.left ),
				linked: !! o.linked,
			} );
		}

		function commit() {
			var s = settings[ active ];
			if ( ! s ) {
				return;
			}
			var vals = readInputs();
			if ( linked ) {
				var one = vals.top;
				vals.right = one;
				vals.bottom = one;
				vals.left = one;
				writeFromObj( {
					top: one,
					right: one,
					bottom: one,
					left: one,
					linked: true,
				} );
			}
			s.set(
				buildJson( vals.top, vals.right, vals.bottom, vals.left, linked, min, max )
			);
		}

		function activate( device ) {
			if ( device === active ) {
				return;
			}
			commit();
			active = device;
			tabs.forEach( function ( tab ) {
				var on = tab.getAttribute( "data-device" ) === device;
				tab.classList.toggle( "is-active", on );
				tab.setAttribute( "aria-selected", on ? "true" : "false" );
			} );
			load( device );
			setCustomizerPreviewDevice( device );
		}

		function onInputChange( e ) {
			if ( linked ) {
				var v = e && e.target ? e.target.value : topI.value;
				topI.value = v;
				rightI.value = v;
				bottomI.value = v;
				leftI.value = v;
			}
			commit();
		}

		function wireAfterSettingsReady() {
			tabs.forEach( function ( tab ) {
				tab.addEventListener( "click", function ( e ) {
					e.preventDefault();
					var d = tab.getAttribute( "data-device" );
					if ( d ) {
						activate( d );
					}
				} );
			} );

			linkBtn.addEventListener( "click", function ( e ) {
				e.preventDefault();
				linked = ! linked;
				if ( linked ) {
					var v = topI.value;
					rightI.value = v;
					bottomI.value = v;
					leftI.value = v;
				}
				updateLinkUi();
				commit();
			} );

			[ topI, rightI, bottomI, leftI ].forEach( function ( inp ) {
				inp.addEventListener( "input", onInputChange );
				inp.addEventListener( "change", onInputChange );
			} );

			[ "pc", "tablet", "phone" ].forEach( function ( d ) {
				settings[ d ].bind(
					( function ( dev ) {
						return function () {
							if ( dev === active ) {
								load( dev );
							}
						};
					} )( d )
				);
			} );
			load( "pc" );
			root.setAttribute( "data-trove-spacing-wired", "1" );
		}

		[ "pc", "tablet", "phone" ].forEach( function ( dev ) {
			api( settingsMap[ dev ], function ( setting ) {
				settings[ dev ] = setting;
				remaining -= 1;
				if ( remaining === 0 ) {
					wireAfterSettingsReady();
				}
			} );
		} );
	}

	function scan() {
		document.querySelectorAll( ".trove-responsive-spacing-control" ).forEach( initOne );
	}

	if ( api && typeof api.bind === "function" ) {
		api.bind( "ready", function () {
			scan();
		} );
	}

	if ( document.readyState === "loading" ) {
		document.addEventListener( "DOMContentLoaded", scan );
	} else {
		scan();
	}
} )( wp.customize );
