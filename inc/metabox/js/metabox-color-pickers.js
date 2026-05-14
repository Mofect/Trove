/**
 * wpColorPicker (alpha) for Page settings color fields — mirrors trove-cz-swatch-picker in Customizer.
 */
( function ( $ ) {
	'use strict';

	function isEmptySwatchVal( val ) {
		if ( val == null ) {
			return true;
		}
		var s = String( val ).trim();
		return s === '' || /^transparent$/i.test( s );
	}

	function syncSwatchEmptyClass( $input ) {
		var $c = $input.closest( '.wp-picker-container' );
		var $btn = $c.find( '.wp-color-result' ).first();
		if ( ! $btn.length ) {
			return;
		}
		if ( isEmptySwatchVal( $input.val() ) ) {
			$btn.addClass( 'trove-cz-swatch-empty' );
		} else {
			$btn.removeClass( 'trove-cz-swatch-empty' );
		}
	}

	/**
	 * Native tooltip on the visible swatch button (wpColorPicker wraps the text input).
	 * Hint is read from the closest .trove-dual-color__pill[data-trove-swatch-tooltip].
	 */
	function syncSwatchTooltip( $input ) {
		function apply() {
			var $pill = $input.closest( '.trove-dual-color__pill' );
			var tip = ( $pill.attr( 'data-trove-swatch-tooltip' ) || '' ).trim();
			var $btn = $input.closest( '.wp-picker-container' ).find( '.wp-color-result' ).first();
			if ( ! $btn.length ) {
				return false;
			}
			if ( tip ) {
				$btn.attr( 'title', tip ).attr( 'aria-label', tip );
			} else {
				$btn.removeAttr( 'title' ).removeAttr( 'aria-label' );
			}
			return true;
		}
		if ( ! apply() ) {
			window.setTimeout( apply, 0 );
		}
	}

	function bindPicker( el ) {
		var $input = $( el );
		if ( $input.hasClass( 'trove-mb-cz-picker-inited' ) ) {
			return;
		}
		$input.addClass( 'trove-mb-cz-picker-inited' );
		$input.wpColorPicker( {
			palettes: false,
			alpha: true,
			change: function ( e, ui ) {
				$input.val( ui.color.toString() );
				syncSwatchEmptyClass( $input );
				syncSwatchTooltip( $input );
			},
			clear: function () {
				$input.val( '' );
				syncSwatchEmptyClass( $input );
				syncSwatchTooltip( $input );
			},
		} );
		syncSwatchEmptyClass( $input );
		syncSwatchTooltip( $input );
		$( document ).trigger( 'trove:metaboxColorPickerReady', [ $input ] );
	}

	function initPickers( $root ) {
		$root.find( '.trove-cz-swatch-picker' ).each( function () {
			bindPicker( this );
		} );
	}

	$( function () {
		var $wrap = $( '.trove-mb-cz-layout' );
		if ( $wrap.length ) {
			initPickers( $wrap );
		}
	} );

	$( document ).on(
		'change input irischange',
		'.trove-mb-cz-layout .trove-cz-swatch-picker',
		function () {
			var $in = $( this );
			syncSwatchEmptyClass( $in );
			syncSwatchTooltip( $in );
		}
	);

	$( document ).on( 'click', '.trove-mb-cz-layout .trove-mb-cz-color-clear--single', function ( e ) {
		e.preventDefault();
		var $in = $( this ).closest( '.trove-mb-cz-color-single__row' ).find( '.trove-cz-swatch-picker' ).first();
		$in.val( '' );
		try {
			$in.wpColorPicker( 'color', '' );
		} catch ( err ) {}
		syncSwatchEmptyClass( $in );
		syncSwatchTooltip( $in );
	} );

	$( document ).on( 'click', '.trove-mb-cz-layout .trove-mb-cz-color-clear--pair', function ( e ) {
		e.preventDefault();
		$( this ).closest( '.trove-mb-cz-color-pair-wrap' ).find( '.trove-cz-swatch-picker' ).each( function () {
			var $in = $( this );
			$in.val( '' );
			try {
				$in.wpColorPicker( 'color', '' );
			} catch ( err ) {}
			syncSwatchEmptyClass( $in );
			syncSwatchTooltip( $in );
		} );
	} );
}( jQuery ) );
