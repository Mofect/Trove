( function () {
	'use strict';

	var MOBILE_MEDIA = '(max-width: 782px)';
	var mq = window.matchMedia ? window.matchMedia( MOBILE_MEDIA ) : null;

	function isMobile() {
		return mq ? mq.matches : window.innerWidth <= 782;
	}

	function syncMenuState( menuCol, expanded ) {
		var toggle = menuCol.querySelector( '.site-footer__menu-toggle' );
		if ( ! toggle ) {
			return;
		}

		menuCol.classList.toggle( 'is-expanded', expanded );
		toggle.setAttribute( 'aria-expanded', expanded ? 'true' : 'false' );
	}

	function applyResponsiveState() {
		var menuCols = document.querySelectorAll( '.site-footer__menu-col' );
		if ( ! menuCols.length ) {
			return;
		}

		var mobile = isMobile();
		menuCols.forEach( function ( menuCol ) {
			syncMenuState( menuCol, ! mobile );
		} );
	}

	function onToggleClick( event ) {
		var toggle = event.target.closest( '.site-footer__menu-toggle' );
		if ( ! toggle || ! isMobile() ) {
			return;
		}

		var menuCol = toggle.closest( '.site-footer__menu-col' );
		if ( ! menuCol ) {
			return;
		}

		var expanded = toggle.getAttribute( 'aria-expanded' ) === 'true';
		syncMenuState( menuCol, ! expanded );
	}

	document.addEventListener( 'click', onToggleClick );
	window.addEventListener( 'resize', applyResponsiveState );
	document.addEventListener( 'DOMContentLoaded', applyResponsiveState );
} )();
