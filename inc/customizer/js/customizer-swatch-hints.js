/**
 * Hover tooltips on color swatches (Global Colors).
 */
( function ( $ ) {
	'use strict';

	function appendTooltip( $container, text ) {
		if ( ! text || ! $container.length ) {
			return;
		}
		if ( $container.find( '.trove-cz-swatch-tooltip' ).length ) {
			return;
		}
		$container.append(
			$( '<span class="trove-cz-swatch-tooltip" role="tooltip" />' ).text( text )
		);
		var $btn = $container.find( '.wp-color-result' ).first();
		$btn.attr( 'aria-label', text );
	}

	function applyHints( hints ) {
		Object.keys( hints ).forEach( function ( controlId ) {
			var hint = hints[ controlId ];
			wp.customize.control( controlId, function ( control ) {
				if ( typeof hint === 'string' && hint ) {
					control.container.find( '.wp-picker-container' ).each( function () {
						appendTooltip( $( this ), hint );
					} );
					return;
				}
				if ( hint && typeof hint === 'object' ) {
					var $pickers = control.container.find( '.wp-picker-container' );
					if ( hint.left ) {
						appendTooltip( $pickers.eq( 0 ), hint.left );
					}
					if ( hint.right ) {
						appendTooltip( $pickers.eq( 1 ), hint.right );
					}
				}
			} );
		} );
	}

	function init() {
		var hints =
			typeof troveCzSwatchHints !== 'undefined' && troveCzSwatchHints.hints
				? troveCzSwatchHints.hints
				: {};
		applyHints( hints );
	}

	wp.customize.bind( 'ready', function () {
		/* After wpColorPicker runs (customizer-color-pair.js). */
		setTimeout( init, 120 );
	} );
}( jQuery ) );
