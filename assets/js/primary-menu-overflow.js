/**
 * Primary nav: show at most N top-level links; move the rest under a "more" (⋯) control.
 */
( function () {
	'use strict';

	function init() {
		var cfg =
			typeof trovePrimaryNav !== 'undefined' && trovePrimaryNav
				? trovePrimaryNav
				: { maxVisible: 8, i18n: { more: 'More' } };
		var max =
			typeof cfg.maxVisible === 'number' && cfg.maxVisible > 0
				? Math.floor( cfg.maxVisible )
				: 8;
		var moreLabel =
			cfg.i18n && cfg.i18n.more ? String( cfg.i18n.more ) : 'More';

		var nav = document.querySelector( '.site-header__nav' );
		if ( ! nav ) {
			return;
		}
		var menu = nav.querySelector( 'ul.primary-menu' );
		if ( ! menu ) {
			return;
		}

		var topLevel = [].filter.call( menu.children, function ( el ) {
			return el.tagName === 'LI';
		} );
		if ( topLevel.length <= max ) {
			return;
		}

		var overflowItems = topLevel.slice( max );
		var moreLi = document.createElement( 'li' );
		moreLi.className =
			'menu-item primary-menu__more menu-item-has-children';

		var btn = document.createElement( 'button' );
		btn.type = 'button';
		btn.className = 'primary-menu__more-toggle';
		btn.setAttribute( 'aria-expanded', 'false' );
		btn.setAttribute( 'aria-haspopup', 'true' );
		btn.setAttribute( 'aria-label', moreLabel );
		btn.innerHTML =
			'<svg class="primary-menu__more-icon" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="6" cy="12" r="2" fill="currentColor"/><circle cx="12" cy="12" r="2" fill="currentColor"/><circle cx="18" cy="12" r="2" fill="currentColor"/></svg>';

		var sub = document.createElement( 'ul' );
		sub.className = 'sub-menu primary-menu__overflow';

		overflowItems.forEach( function ( li ) {
			sub.appendChild( li );
		} );

		moreLi.appendChild( btn );
		moreLi.appendChild( sub );
		menu.appendChild( moreLi );

		function closeMore() {
			moreLi.classList.remove( 'is-open' );
			btn.setAttribute( 'aria-expanded', 'false' );
		}

		function openMore() {
			moreLi.classList.add( 'is-open' );
			btn.setAttribute( 'aria-expanded', 'true' );
		}

		btn.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			e.stopPropagation();
			if ( moreLi.classList.contains( 'is-open' ) ) {
				closeMore();
			} else {
				openMore();
			}
		} );

		document.addEventListener( 'click', function ( e ) {
			if ( ! moreLi.contains( e.target ) ) {
				closeMore();
			}
		} );

		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' ) {
				closeMore();
				btn.focus();
			}
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
