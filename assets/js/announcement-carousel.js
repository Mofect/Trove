/**
 * Announcement bar: slide or fade carousel, optional autoplay.
 */
( function () {
	'use strict';

	var stateMap = new WeakMap();

	function getState( root ) {
		if ( ! stateMap.has( root ) ) {
			stateMap.set( root, { index: 0, timer: null } );
		}
		return stateMap.get( root );
	}

	function getTotal( root ) {
		var n = parseInt( root.getAttribute( 'data-slide-count' ), 10 );
		return n > 0 ? n : 0;
	}

	function getAnimationMode( root ) {
		var m = root.getAttribute( 'data-animation' ) || 'slide';
		return m === 'fade' ? 'fade' : 'slide';
	}

	function clearAutoplay( root ) {
		var st = getState( root );
		if ( st.timer ) {
			clearInterval( st.timer );
			st.timer = null;
		}
	}

	function setupAutoplay( root ) {
		clearAutoplay( root );
		var st = getState( root );
		if ( getTotal( root ) <= 1 ) {
			return;
		}
		if ( root.getAttribute( 'data-autoplay' ) !== 'true' ) {
			return;
		}
		var ms = parseInt( root.getAttribute( 'data-autoplay-interval-ms' ), 10 );
		if ( isNaN( ms ) || ms < 2000 ) {
			ms = 5000;
		}
		st.timer = setInterval( function () {
			st.index = ( st.index + 1 ) % getTotal( root );
			applyTransform( root );
		}, ms );
	}

	function restartAutoplay( root ) {
		clearAutoplay( root );
		setupAutoplay( root );
	}

	function applyTransform( root ) {
		var total = getTotal( root );
		var st = getState( root );
		if ( total <= 0 ) {
			return;
		}
		st.index = ( ( st.index % total ) + total ) % total;
		var mode = getAnimationMode( root );
		var slides = root.querySelectorAll( '.trove-announcement-bar__slide' );
		var i;

		if ( mode === 'fade' ) {
			root.classList.add( 'trove-announcement-bar--fade' );
			for ( i = 0; i < slides.length; i++ ) {
				slides[ i ].classList.toggle( 'is-active', i === st.index );
				slides[ i ].setAttribute( 'aria-hidden', i === st.index ? 'false' : 'true' );
			}
		} else {
			root.classList.remove( 'trove-announcement-bar--fade' );
			for ( i = 0; i < slides.length; i++ ) {
				slides[ i ].classList.remove( 'is-active' );
				slides[ i ].setAttribute( 'aria-hidden', i === st.index ? 'false' : 'true' );
			}
			root.style.setProperty( '--trove-announcement-total', String( total ) );
			root.style.setProperty( '--trove-announcement-index', String( st.index ) );
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
			restartAutoplay( root );
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
			restartAutoplay( root );
			e.preventDefault();
		} );
	}

	/**
	 * @param {HTMLElement} root .trove-announcement-bar
	 */
	function init( root ) {
		if ( ! root || ! root.hasAttribute( 'data-trove-announcement-carousel' ) ) {
			return;
		}
		clearAutoplay( root );
		bindRoot( root );
		getState( root ).index = 0;
		applyTransform( root );
		setupAutoplay( root );
	}

	/**
	 * @param {HTMLElement} root
	 */
	function destroy( root ) {
		if ( ! root ) {
			return;
		}
		clearAutoplay( root );
		root.removeAttribute( 'data-trove-announcement-carousel-bound' );
		stateMap.delete( root );
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

	window.TroveAnnouncementCarousel = { init: init, destroy: destroy };
} )();
