/**
 * Header search toggle: open inline search form in header.
 */
( function () {
	'use strict';

	function initHeader( header ) {
		var toggle = header.querySelector( '.site-header__search-toggle' );
		var box = header.querySelector( '.site-header__searchbox' );
		if ( ! toggle || ! box ) {
			return;
		}
		var closeBtn = box.querySelector( '.site-header__search-close' );
		var input = box.querySelector( 'input[type="search"]' );

		function openSearch() {
			header.classList.add( 'is-search-open' );
			box.hidden = false;
			toggle.setAttribute( 'aria-expanded', 'true' );
			if ( input ) {
				setTimeout( function () {
					input.focus();
				}, 0 );
			}
		}

		function closeSearch() {
			header.classList.remove( 'is-search-open' );
			box.hidden = true;
			toggle.setAttribute( 'aria-expanded', 'false' );
			toggle.focus();
		}

		toggle.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			if ( header.classList.contains( 'is-search-open' ) ) {
				closeSearch();
			} else {
				openSearch();
			}
		} );

		if ( closeBtn ) {
			closeBtn.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				closeSearch();
			} );
		}

		header.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' && header.classList.contains( 'is-search-open' ) ) {
				e.preventDefault();
				closeSearch();
			}
		} );
	}

	function init() {
		document.querySelectorAll( 'header.site-header' ).forEach( initHeader );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();

