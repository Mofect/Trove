/**
 * When a Customizer color is empty / transparent / alpha 0, mark the swatch so CSS shows a checkerboard.
 */
( function ( $ ) {
	'use strict';

	function rgbaStringAlphaIsZero( s ) {
		var m = s.match( /^rgba\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*,\s*([0-9.]+)\s*\)$/i );
		if ( ! m ) {
			return false;
		}
		var a = parseFloat( m[ 1 ], 10 );
		return ! isNaN( a ) && a <= 0;
	}

	function hslaStringAlphaIsZero( s ) {
		if ( ! /^hsla\(/i.test( s ) ) {
			return false;
		}
		var m = s.match( /,\s*([0-9.]+)\s*\)\s*$/ );
		if ( ! m ) {
			return false;
		}
		var a = parseFloat( m[ 1 ], 10 );
		return ! isNaN( a ) && a <= 0;
	}

	function hex8AlphaIsZero( s ) {
		var m = s.match( /^#([0-9a-f]{8})$/i );
		if ( ! m ) {
			return false;
		}
		return parseInt( m[ 1 ].slice( 6, 8 ), 16 ) === 0;
	}

	function hex4ShorthandAlphaIsZero( s ) {
		var m = s.match( /^#([0-9a-f]{4})$/i );
		if ( ! m ) {
			return false;
		}
		return m[ 1 ].charAt( 3 ).toLowerCase() === '0';
	}

	function isIrisAlphaZero( $in ) {
		if ( ! $in || ! $in.length || typeof $in.iris !== 'function' ) {
			return false;
		}
		try {
			var tc = $in.iris( 'color' );
			if ( tc && typeof tc.getAlpha === 'function' ) {
				return tc.getAlpha() <= 0;
			}
		} catch ( _e ) {}
		return false;
	}

	function isTransparentSwatch( val, $in ) {
		var s;
		if ( val == null ) {
			return true;
		}
		s = String( val ).trim();
		if ( s === '' ) {
			return true;
		}
		if ( /^transparent$/i.test( s ) ) {
			return true;
		}
		if ( hex8AlphaIsZero( s ) || hex4ShorthandAlphaIsZero( s ) ) {
			return true;
		}
		if ( rgbaStringAlphaIsZero( s ) || hslaStringAlphaIsZero( s ) ) {
			return true;
		}
		if ( $in && $in.length && isIrisAlphaZero( $in ) ) {
			return true;
		}
		return false;
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
		if ( isTransparentSwatch( $in.val(), $in ) ) {
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
