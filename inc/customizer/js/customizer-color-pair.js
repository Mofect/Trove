/**
 * Init wpColorPicker on Trove dual-swatch inputs.
 */
( function ( $ ) {
	'use strict';

	function bindPicker( el ) {
		var $input = $( el );
		if ( $input.hasClass( 'trove-cz-picker-inited' ) ) {
			return;
		}
		var sid = el.getAttribute( 'data-trove-setting' );
		if ( ! sid || typeof wp === 'undefined' || ! wp.customize ) {
			return;
		}
		$input.addClass( 'trove-cz-picker-inited' );
		var defHex = el.getAttribute( 'data-default-color' ) || '';
		/* Same guard as WP ColorControl: skip bind while the swatch itself pushed the update (avoids Iris feedback / stuck preview). */
		var updating = false;

		$input.wpColorPicker( {
			palettes: false,
			alpha: true,
			change: function ( e, ui ) {
				updating = true;
				wp.customize( sid ).set( ui.color.toString() );
				updating = false;
			},
			clear: function () {
				updating = true;
				wp.customize( sid ).set( defHex );
				updating = false;
			},
		} );

		$( document ).trigger( 'trove:colorPickerReady', [ $input ] );

		wp.customize( sid, function ( setting ) {
			setting.bind( function ( v ) {
				if ( updating ) {
					return;
				}
				if ( v && String( v ).trim() !== '' ) {
					$input.val( v );
					$input.wpColorPicker( 'color', v );
				} else if ( defHex ) {
					$input.val( defHex );
					$input.wpColorPicker( 'color', defHex );
				} else {
					$input.val( '' );
					$input.wpColorPicker( 'color', '' );
				}
			} );
		} );
	}

	function initDualPickers( $ctx ) {
		$ctx.find( '.trove-cz-swatch-picker' ).each( function () {
			bindPicker( this );
		} );
	}

	wp.customize.bind( 'ready', function () {
		wp.customize.control.each( function ( control ) {
			if ( control.params.type !== 'trove_dual_color' ) {
				return;
			}
			initDualPickers( control.container );
		} );
	} );
}( jQuery ) );
