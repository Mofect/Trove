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

	function getHeaderEl() {
		return document.querySelector( 'header.site-header' );
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

	function bindHeaderSettings() {
		if ( typeof wp === 'undefined' || ! wp.customize ) {
			return;
		}

		wp.customize( 'trove_header_layout', function ( value ) {
			function onChange( newVal ) {
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
	}

	/*
	 * Run after customize-preview.js has run its jQuery ready handler (same tick, later registration),
	 * so theme_mod settings exist before we attach listeners.
	 */
	if ( typeof jQuery !== 'undefined' ) {
		jQuery( function () {
			bindGlobalColors();
			bindHeaderSettings();
		} );
	} else {
		bindGlobalColors();
		bindHeaderSettings();
	}
} )();
