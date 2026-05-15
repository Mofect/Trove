/**
 * Product video field: media library upload fills the URL input.
 */
( function ( $ ) {
	'use strict';

	function getUrlInput( $wrap ) {
		return $wrap.find( '.trove-metabox-field__product-video-url' );
	}

	$( document ).on( 'click', '.trove-metabox-fields .trove-mb-video-select', function ( e ) {
		e.preventDefault();
		if ( typeof wp === 'undefined' || ! wp.media ) {
			return;
		}

		var $wrap = $( this ).closest( '[data-trove-mb-product-video]' );
		var $input = getUrlInput( $wrap );
		var l10n = window.troveMbVideoL10n || {};

		var frame = wp.media( {
			title: l10n.title || 'Choose video',
			button: { text: l10n.button || 'Use video' },
			library: { type: 'video' },
			multiple: false,
		} );

		frame.on( 'select', function () {
			var att = frame.state().get( 'selection' ).first().toJSON();
			var url = att.url || '';
			if ( url ) {
				$input.val( url ).trigger( 'change' );
			}
		} );

		frame.open();
	} );

	$( document ).on( 'click', '.trove-metabox-fields .trove-mb-video-clear', function ( e ) {
		e.preventDefault();
		var $wrap = $( this ).closest( '[data-trove-mb-product-video]' );
		getUrlInput( $wrap ).val( '' ).trigger( 'change' );
	} );
}( jQuery ) );
