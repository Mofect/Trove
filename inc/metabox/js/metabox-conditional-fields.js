/**
 * Show / hide metabox fields based on peer switches:
 * - data-trove-mb-hide-when-checked: hidden when peer checkbox is checked.
 * - data-trove-mb-hide-when-unchecked: hidden when peer checkbox is not checked.
 *
 * Uses delegated events and delayed resync so meta boxes that mount after DOM ready
 * (e.g. block editor) still bind and update correctly.
 */
( function ( $ ) {
	'use strict';

	function syncRow( $row ) {
		var hide = false;
		var uidUnless = $row.attr( 'data-trove-mb-hide-when-checked' );
		if ( uidUnless ) {
			var $peer = $( document.getElementById( uidUnless ) );
			if ( $peer.length && $peer.is( ':checked' ) ) {
				hide = true;
			}
		}
		var uidOnly = $row.attr( 'data-trove-mb-hide-when-unchecked' );
		if ( uidOnly ) {
			var $peer2 = $( document.getElementById( uidOnly ) );
			if ( $peer2.length && ! $peer2.is( ':checked' ) ) {
				hide = true;
			}
		}
		$row.css( 'display', hide ? 'none' : '' );
	}

	function syncAllConditionalRows() {
		$( '[data-trove-mb-hide-when-checked], [data-trove-mb-hide-when-unchecked]' ).each( function () {
			syncRow( $( this ) );
		} );
	}

	$( document ).on(
		'change',
		'.trove-metabox-fields input[type="checkbox"]',
		function () {
			var id = this.id;
			if ( ! id || id.indexOf( 'trove-mb-' ) !== 0 ) {
				return;
			}
			$( document )
				.find( '[data-trove-mb-hide-when-checked="' + id + '"], [data-trove-mb-hide-when-unchecked="' + id + '"]' )
				.each( function () {
					syncRow( $( this ) );
				} );
		}
	);

	$( function () {
		syncAllConditionalRows();
		[ 0, 50, 100, 200, 400, 800, 1500, 2500, 4000 ].forEach( function ( ms ) {
			window.setTimeout( syncAllConditionalRows, ms );
		} );
	} );

	$( document ).on( 'trove:metaboxColorPickerReady trove:metaboxConditionalSync', function () {
		syncAllConditionalRows();
	} );
}( jQuery ) );
