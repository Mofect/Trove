/**
 * Sync range slider ↔ number input for trove_range_number controls.
 */
( function ( api ) {
	'use strict';

	function clamp( n, min, max ) {
		if ( isNaN( n ) ) {
			return min;
		}
		return Math.min( max, Math.max( min, n ) );
	}

	function bindControl( wrap ) {
		var sid = wrap.getAttribute( 'data-setting-id' );
		if ( ! sid ) {
			return;
		}
		var range = wrap.querySelector( '.trove-cz-range-number__range' );
		var num = wrap.querySelector( '.trove-cz-range-number__number' );
		if ( ! range || ! num ) {
			return;
		}

		api( sid, function ( setting ) {
			setting.bind( function ( val ) {
				var n = parseInt( val, 10 );
				if ( isNaN( n ) ) {
					return;
				}
				range.value = String( n );
			} );
		} );

		range.addEventListener( 'input', function () {
			var v = parseInt( this.value, 10 );
			if ( isNaN( v ) ) {
				return;
			}
			api( sid ).set( v );
		} );

		num.addEventListener( 'change', function () {
			var min = parseInt( range.getAttribute( 'min' ), 10 );
			var max = parseInt( range.getAttribute( 'max' ), 10 );
			var v = clamp( parseInt( this.value, 10 ), min, max );
			this.value = String( v );
			api( sid ).set( v );
		} );
	}

	api.bind( 'ready', function () {
		document.querySelectorAll( '.trove-cz-range-number' ).forEach( bindControl );
	} );
} )( wp.customize );
