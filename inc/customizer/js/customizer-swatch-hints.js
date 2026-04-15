/**
 * Hover tooltips on color swatches (Global Colors).
 */
( function () {
	'use strict';

	function appendTooltip( container, text ) {
		var existing;
		var btn;
		if ( ! text || ! container ) {
			return;
		}
		existing = container.querySelector( '.trove-cz-swatch-tooltip' );
		if ( existing ) {
			return;
		}
		existing = document.createElement( 'span' );
		existing.className = 'trove-cz-swatch-tooltip';
		existing.setAttribute( 'role', 'tooltip' );
		existing.textContent = text;
		container.appendChild( existing );
		btn = container.querySelector( '.wp-color-result' );
		if ( btn ) {
			btn.setAttribute( 'aria-label', text );
		}
	}

	function applyHints( hints ) {
		Object.keys( hints ).forEach( function ( controlId ) {
			var hint = hints[ controlId ];
			wp.customize.control( controlId, function ( control ) {
				if ( typeof hint === 'string' && hint ) {
					control.container[ 0 ].querySelectorAll( '.wp-picker-container' ).forEach( function ( el ) {
						appendTooltip( el, hint );
					} );
					return;
				}
				if ( hint && typeof hint === 'object' ) {
					var pickers = control.container[ 0 ].querySelectorAll( '.wp-picker-container' );
					if ( hint.left ) {
						appendTooltip( pickers[ 0 ], hint.left );
					}
					if ( hint.right ) {
						appendTooltip( pickers[ 1 ], hint.right );
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
} )();
