/**
 * Announcement bar: slide carousel with prev/next (cycles).
 */
( function () {
	'use strict';

	var stateMap = new WeakMap();

	function getState( root ) {
		if ( ! stateMap.has( root ) ) {
			stateMap.set( root, { index: 0 } );
		}
		return stateMap.get( root );
	}

	function getTotal( root ) {
		var n = parseInt( root.getAttribute( 'data-slide-count' ), 10 );
		return n > 0 ? n : 0;
	}

	function applyTransform( root ) {
		var total = getTotal( root );
		var st = getState( root );
		if ( total <= 0 ) {
			return;
		}
		st.index = ( ( st.index % total ) + total ) % total;
		root.style.setProperty( '--trove-announcement-total', String( total ) );
		root.style.setProperty( '--trove-announcement-index', String( st.index ) );

		var slides = root.querySelectorAll( '.trove-announcement-bar__slide' );
		var i;
		for ( i = 0; i < slides.length; i++ ) {
			slides[ i ].setAttribute( 'aria-hidden', i === st.index ? 'false' : 'true' );
		}
	}

	function bindRoot( root ) {
		if ( root.dataset.troveAnnouncementCarouselBound === '1' ) {
			return;
		}
		root.dataset.troveAnnouncementCarouselBound = '1';
		root.addEventListener( 'click', function ( e ) {
			var prevBtn = e.target.closest( '.trove-announcement-bar__nav--prev' );
			var nextBtn = e.target.closest( '.trove-announcement-bar__nav--next' );
			if ( ! prevBtn && ! nextBtn ) {
				return;
			}
			var total = getTotal( root );
			if ( total <= 1 ) {
				return;
			}
			var st = getState( root );
			if ( prevBtn ) {
				st.index = ( st.index - 1 + total ) % total;
			} else {
				st.index = ( st.index + 1 ) % total;
			}
			applyTransform( root );
		} );

		root.addEventListener( 'keydown', function ( e ) {
			if ( e.key !== 'ArrowLeft' && e.key !== 'ArrowRight' ) {
				return;
			}
			if ( ! root.contains( document.activeElement ) ) {
				return;
			}
			var total = getTotal( root );
			if ( total <= 1 ) {
				return;
			}
			var st = getState( root );
			if ( e.key === 'ArrowLeft' ) {
				st.index = ( st.index - 1 + total ) % total;
			} else {
				st.index = ( st.index + 1 ) % total;
			}
			applyTransform( root );
			e.preventDefault();
		} );
	}

	/**
	 * @param {HTMLElement} root .trove-announcement-bar
	 */
	function init( root ) {
		/* Boolean data attributes have no value; getAttribute returns "" which is falsy. */
		if ( ! root || ! root.hasAttribute( 'data-trove-announcement-carousel' ) ) {
			return;
		}
		bindRoot( root );
		getState( root ).index = 0;
		applyTransform( root );
	}

	function scan() {
		document.querySelectorAll( '[data-trove-announcement-carousel]' ).forEach( function ( el ) {
			init( el );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', scan );
	} else {
		scan();
	}

	window.TroveAnnouncementCarousel = { init: init };
} )();
