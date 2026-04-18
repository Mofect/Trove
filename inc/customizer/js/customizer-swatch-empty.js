/**
 * When a Customizer color input has no value, mark the swatch button so CSS can show a red diagonal slash.
 */
( function ( $ ) {
	'use strict';

	function isEmptyColor( val ) {
		if ( val == null ) {
			return true;
		}
		return String( val ).trim() === '';
	}

	function syncContainer( $container ) {
		if ( ! $container || ! $container.length ) {
			return;
		}
		var $in = $container.find( '.wp-color-picker' ).first();
		var $btn = $container.find( '.wp-color-result' ).first();
		if ( ! $in.length || ! $btn.length ) {
			return;
		}
		if ( isEmptyColor( $in.val() ) ) {
			$btn.addClass( 'trove-cz-swatch-empty' );
		} else {
			$btn.removeClass( 'trove-cz-swatch-empty' );
		}
	}

	function scanAll() {
		$( '#customize-theme-controls .wp-picker-container' ).each( function () {
			syncContainer( $( this ) );
		} );
	}

	var debounceTimer;
	function debouncedScan() {
		clearTimeout( debounceTimer );
		debounceTimer = setTimeout( scanAll, 80 );
	}

	if ( typeof wp !== 'undefined' && wp.customize ) {
		wp.customize.bind( 'ready', function () {
			setTimeout( scanAll, 50 );
			setTimeout( scanAll, 400 );
			setTimeout( scanAll, 1000 );

			var root = document.getElementById( 'customize-theme-controls' );
			if ( root && typeof MutationObserver !== 'undefined' ) {
				var mo = new MutationObserver( debouncedScan );
				mo.observe( root, { childList: true, subtree: true } );
			}
		} );
	}

	$( document ).on( 'change input', '#customize-theme-controls .wp-color-picker', function () {
		syncContainer( $( this ).closest( '.wp-picker-container' ) );
	} );

	$( document ).on( 'irischange', '#customize-theme-controls .wp-color-picker', function () {
		syncContainer( $( this ).closest( '.wp-picker-container' ) );
	} );

	$( document ).on( 'click', '#customize-theme-controls .wp-picker-clear', function () {
		var $c = $( this ).closest( '.wp-picker-container' );
		setTimeout( function () {
			syncContainer( $c );
		}, 0 );
		setTimeout( function () {
			syncContainer( $c );
		}, 50 );
	} );

	$( document ).on( 'trove:colorPickerReady', function ( e, $input ) {
		if ( $input && $input.length ) {
			syncContainer( $input.closest( '.wp-picker-container' ) );
		}
	} );
}( jQuery ) );
