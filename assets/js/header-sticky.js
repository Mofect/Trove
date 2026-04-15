/**
 * Header sticky behavior:
 * - .trove-header-sticky-always: CSS-only sticky
 * - .trove-header-sticky-on_scroll_up: reveal when scrolling up
 */
( function () {
	'use strict';

	function initOnScrollUp( header ) {
		var lastY = window.scrollY || 0;
		var threshold = 24;

		function update() {
			var y = window.scrollY || 0;
			var delta = y - lastY;

			if ( y <= threshold ) {
				header.classList.add( 'is-at-top' );
				header.classList.remove( 'is-sticky-visible' );
			} else {
				header.classList.remove( 'is-at-top' );
				if ( delta > 2 ) {
					header.classList.remove( 'is-sticky-visible' );
				} else if ( delta < -2 ) {
					header.classList.add( 'is-sticky-visible' );
				}
			}

			lastY = y;
		}

		update();
		window.addEventListener( 'scroll', update, { passive: true } );
	}

	function init() {
		var header = document.querySelector( 'header.site-header' );
		if ( ! header ) {
			return;
		}
		if ( header.classList.contains( 'trove-header-sticky-on_scroll_up' ) ) {
			initOnScrollUp( header );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
