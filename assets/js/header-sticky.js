/**
 * Header sticky behavior:
 * - .trove-header-sticky-always: CSS-only sticky
 * - .trove-header-sticky-on_scroll_up: reveal when scrolling up
 */
( function () {
	'use strict';

	function measureScrollAwayThreshold( header ) {
		var rect = header.getBoundingClientRect();
		return Math.round( ( window.scrollY || 0 ) + rect.top + rect.height );
	}

	function initOnScrollUp( header ) {
		var lastY = window.scrollY || 0;
		var topThreshold = 24;
		var scrollAwayThreshold = measureScrollAwayThreshold( header );

		function hideHeader() {
			header.classList.remove( 'is-sticky-visible' );
		}

		function setNaturalScroll( on ) {
			if ( on ) {
				header.classList.add( 'is-natural-scroll' );
			} else {
				header.classList.remove( 'is-natural-scroll' );
			}
		}

		function update() {
			var y = window.scrollY || 0;
			var delta = y - lastY;

			if ( y <= topThreshold ) {
				header.classList.add( 'is-at-top' );
				header.classList.remove( 'is-sticky-visible', 'is-header-fixed' );
				setNaturalScroll( true );
			} else if ( y < scrollAwayThreshold ) {
				header.classList.remove( 'is-at-top', 'is-header-fixed' );
				header.classList.remove( 'is-sticky-visible' );
				setNaturalScroll( true );
			} else {
				header.classList.remove( 'is-at-top' );
				setNaturalScroll( false );

				if ( header.classList.contains( 'is-header-fixed' ) ) {
					if ( delta < -2 ) {
						header.classList.add( 'is-sticky-visible' );
					} else if ( delta > 0 && header.classList.contains( 'is-sticky-visible' ) ) {
						hideHeader();
					}
				} else if ( delta < -2 ) {
					header.classList.add( 'is-header-fixed', 'is-sticky-visible' );
				} else if ( delta > 0 ) {
					header.classList.remove( 'is-sticky-visible' );
				}
			}

			lastY = y;
		}

		function remeasure() {
			scrollAwayThreshold = measureScrollAwayThreshold( header );
			update();
		}

		update();
		window.addEventListener( 'scroll', update, { passive: true } );
		window.addEventListener( 'resize', remeasure, { passive: true } );
	}

	function initAtTop( header ) {
		var topThreshold = 24;

		function update() {
			var y = window.scrollY || 0;
			if ( y <= topThreshold ) {
				header.classList.add( 'is-at-top' );
			} else {
				header.classList.remove( 'is-at-top' );
			}
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
		} else if ( header.classList.contains( 'trove-header-sticky-always' ) ) {
			initAtTop( header );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
