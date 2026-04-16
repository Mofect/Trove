/**
 * Rebuilds Base font weight <select> options from Google Fonts metadata for the active family.
 */
( function ( $, wp ) {
	'use strict';

	var SETTING_FAMILY = 'trove_google_font_family';
	var SETTING_WEIGHT = 'trove_font_base_weight';

	function cfg() {
		return typeof window.troveFontWeightMap !== 'undefined'
			? window.troveFontWeightMap
			: {};
	}

	function nearest( candidate, weights ) {
		var w = parseInt( candidate, 10 );
		if ( isNaN( w ) ) {
			w = 400;
		}
		if ( ! weights || ! weights.length ) {
			return 400;
		}
		var i;
		var best = weights[ 0 ];
		var bestd = Math.abs( best - w );
		for ( i = 1; i < weights.length; i++ ) {
			var d = Math.abs( weights[ i ] - w );
			if ( d < bestd ) {
				bestd = d;
				best = weights[ i ];
			}
		}
		return best;
	}

	function weightsForFamily( family ) {
		var map = cfg().map || {};
		var s = family ? String( family ).trim() : '';
		if ( ! s ) {
			s = 'Instrument Sans';
		}
		if ( map[ s ] ) {
			return map[ s ];
		}
		var keys = Object.keys( map );
		var k;
		for ( k = 0; k < keys.length; k++ ) {
			if ( keys[ k ].toLowerCase() === s.toLowerCase() ) {
				return map[ keys[ k ] ];
			}
		}
		return map.system || [ 300, 400, 500, 600, 700 ];
	}

	function optionLabel( num, labels ) {
		var key = String( num );
		if ( labels && labels[ key ] ) {
			return labels[ key ];
		}
		return key;
	}

	function refreshWeightSelect() {
		if ( typeof wp === 'undefined' || ! wp.customize ) {
			return;
		}
		var famInst = wp.customize( SETTING_FAMILY );
		var wInst = wp.customize( SETTING_WEIGHT );
		if ( ! famInst || ! wInst ) {
			return;
		}
		var family = famInst.get();
		var weights = weightsForFamily( family );
		var labels = cfg().labels || {};
		var control = wp.customize.control( SETTING_WEIGHT );
		if ( ! control || ! control.container ) {
			return;
		}
		var $select = control.container.find( 'select' );
		if ( ! $select.length ) {
			return;
		}

		var current = parseInt( wInst.get(), 10 );
		if ( isNaN( current ) ) {
			current = 400;
		}
		var snapped = nearest( current, weights );

		$select.empty();
		var i;
		for ( i = 0; i < weights.length; i++ ) {
			var n = weights[ i ];
			$(
				'<option/>',
				{
					value: String( n ),
					text: optionLabel( n, labels ),
				}
			).appendTo( $select );
		}

		if ( snapped !== current ) {
			wInst.set( String( snapped ) );
		} else {
			$select.val( String( snapped ) );
		}
	}

	wp.customize.bind( 'ready', function () {
		wp.customize( SETTING_FAMILY, function ( value ) {
			value.bind( function () {
				refreshWeightSelect();
			} );
		} );
		refreshWeightSelect();
	} );
} )( jQuery, wp );
