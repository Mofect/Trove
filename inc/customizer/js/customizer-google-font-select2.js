/**
 * Select2 + full Google Fonts list for Trove Customizer control.
 *
 * dropdownParent + z-index: the Customizer sidebar uses overflow; the default
 * Select2 dropdown is clipped or sits under the overlay unless attached here.
 */
( function ( $, wp ) {
	'use strict';

	function getDropdownParent() {
		/* Body avoids overflow:hidden on .accordion-section-content and sidebar panes. */
		return $( document.body );
	}

	function buildData( families, systemLabel ) {
		var data = [ { id: 'system', text: systemLabel } ];
		var i;
		for ( i = 0; i < families.length; i++ ) {
			data.push( { id: families[ i ], text: families[ i ] } );
		}
		return data;
	}

	function initSelect( $select ) {
		if ( ! $select.length || $select.data( 'troveGoogleFontSelect2' ) ) {
			return;
		}

		if ( typeof $.fn.select2 !== 'function' ) {
			return;
		}

		var cfg =
			typeof window.troveGoogleFontSelect2 !== 'undefined'
				? window.troveGoogleFontSelect2
				: {};
		var families = cfg.families || [];
		var i18n = cfg.i18n || {};
		var current = $select.val();
		var data = buildData( families, i18n.system || 'System UI' );
		var $parent = getDropdownParent();

		$select.empty();
		$select.select2( {
			data: data,
			width: '100%',
			allowClear: false,
			dropdownParent: $parent,
		} );

		if ( current ) {
			$select.val( current ).trigger( 'change' );
			$select.trigger( 'change.select2' );
		}

		/*
		 * Select2 must notify the native <select> so Customizer’s
		 * data-customize-setting-link picks up user selection (postMessage).
		 */
		$select.off( 'select2:select.troveCz' ).on( 'select2:select.troveCz', function () {
			$( this ).trigger( 'change' );
		} );

		$select.data( 'troveGoogleFontSelect2', true );
	}

	function initAll() {
		wp.customize.control.each( function ( control ) {
			var $sel = control.container.find( '.js-trove-google-font-select' );
			if ( ! $sel.length ) {
				return;
			}
			initSelect( $sel );
		} );
	}

	function scheduleInit() {
		initAll();
		window.setTimeout( initAll, 50 );
		window.setTimeout( initAll, 300 );
	}

	wp.customize.bind( 'ready', function () {
		scheduleInit();

		wp.customize.control.bind( 'add', function ( control ) {
			var $sel = control.container.find( '.js-trove-google-font-select' );
			if ( $sel.length ) {
				window.setTimeout( function () {
					initSelect( $sel );
				}, 0 );
			}
		} );

		wp.customize.section( 'trove_section_typography', function ( section ) {
			section.expanded.bind( function ( isOpen ) {
				if ( isOpen ) {
					scheduleInit();
				}
			} );
		} );
	} );
} )( jQuery, wp );
