/**
 * Ensures the empty mini-cart bag icon stays in the drawer after Woo block re-renders.
 */
( function () {
	'use strict';

	var ICON_SELECTOR = '.trove-mini-cart-empty__icon';

	function getIconHtml() {
		return window.troveMiniCartEmpty && window.troveMiniCartEmpty.iconHtml
			? window.troveMiniCartEmpty.iconHtml
			: '';
	}

	function queryAllInShadow( root, selector, acc ) {
		if ( ! acc ) {
			acc = [];
		}
		if ( ! root || ! root.querySelectorAll ) {
			return acc;
		}
		var direct = root.querySelectorAll( selector );
		if ( direct && direct.length ) {
			direct.forEach( function ( el ) {
				acc.push( el );
			} );
		}
		var all = root.querySelectorAll( '*' );
		if ( all ) {
			all.forEach( function ( el ) {
				if ( el.shadowRoot ) {
					queryAllInShadow( el.shadowRoot, selector, acc );
				}
			} );
		}
		return acc;
	}

	function isEmptyBlockVisible( block ) {
		if ( ! block ) {
			return false;
		}
		if ( block.hasAttribute( 'hidden' ) || block.hidden ) {
			return false;
		}
		var ariaHidden = block.getAttribute( 'aria-hidden' );
		return ariaHidden !== 'true';
	}

	function ensureIcons() {
		var html = getIconHtml();
		if ( ! html ) {
			return;
		}

		queryAllInShadow(
			document,
			'.wp-block-woocommerce-empty-mini-cart-contents-block'
		).forEach( function ( block ) {
			if ( ! isEmptyBlockVisible( block ) ) {
				return;
			}

			var target =
				block.querySelector( '.wc-block-mini-cart__empty-cart-wrapper' ) || block;

			if ( target.querySelector( ICON_SELECTOR ) ) {
				return;
			}

			target.insertAdjacentHTML( 'afterbegin', html );
		} );
	}

	function init() {
		ensureIcons();

		if ( ! window.MutationObserver || ! document.body ) {
			return;
		}

		var scheduled = false;
		var observer = new MutationObserver( function () {
			if ( scheduled ) {
				return;
			}
			scheduled = true;
			window.requestAnimationFrame( function () {
				scheduled = false;
				ensureIcons();
			} );
		} );

		observer.observe( document.body, {
			childList: true,
			subtree: true,
			attributes: true,
			attributeFilter: [ 'hidden', 'aria-hidden' ],
		} );

		if ( window.jQuery ) {
			window.jQuery( document.body ).on(
				'added_to_cart removed_from_cart wc_fragments_refreshed wc_fragments_loaded',
				function () {
					window.setTimeout( ensureIcons, 0 );
				}
			);
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
