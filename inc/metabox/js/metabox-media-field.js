/**
 * Attachment image fields in Trove metaboxes ([data-trove-mb-media-field]).
 */
( function ( $ ) {
	'use strict';

	function setPreview( $row, url ) {
		var $pv = $row.find( '.trove-metabox-field__media-preview' );
		$pv.empty();
		if ( url ) {
			$( '<img width="60" height="60" alt="" loading="lazy" decoding="async" />' )
				.attr( 'src', url )
				.appendTo( $pv );
		}
	}

	$( document ).on( 'click', '.trove-metabox-fields .trove-mb-media-select', function ( e ) {
		e.preventDefault();
		if ( typeof wp === 'undefined' || ! wp.media ) {
			return;
		}
		var $row = $( this ).closest( '[data-trove-mb-media-field]' );
		var $input = $row.find( '.trove-metabox-field__media-input' );
		var frame = wp.media( {
			title: window.troveMbMediaL10n && troveMbMediaL10n.title ? troveMbMediaL10n.title : '',
			button: {
				text: window.troveMbMediaL10n && troveMbMediaL10n.button ? troveMbMediaL10n.button : '',
			},
			library: { type: 'image' },
			multiple: false,
		} );
		frame.on( 'select', function () {
			var att = frame.state().get( 'selection' ).first().toJSON();
			var id = att.id ? parseInt( att.id, 10 ) : 0;
			$input.val( id || '' ).trigger( 'change' );
			var url =
				att.sizes && att.sizes.thumbnail && att.sizes.thumbnail.url
					? att.sizes.thumbnail.url
					: att.url || '';
			setPreview( $row, url );
		} );
		frame.open();
	} );

	$( document ).on( 'click', '.trove-metabox-fields .trove-mb-media-remove', function ( e ) {
		e.preventDefault();
		var $row = $( this ).closest( '[data-trove-mb-media-field]' );
		$row.find( '.trove-metabox-field__media-input' ).val( '' ).trigger( 'change' );
		setPreview( $row, '' );
	} );
}( jQuery ) );
