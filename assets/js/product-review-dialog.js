/**
 * Product reviews: open/close native dialog for the review form.
 */
( function () {
	'use strict';

	var dialog = document.getElementById( 'trove-woo-product-review-dialog' );
	if ( ! dialog || typeof dialog.showModal !== 'function' ) {
		return;
	}

	var openBtn = document.querySelector( '.trove-woo-review-dialog-open' );
	var closeBtn = dialog.querySelector( '.trove-woo-review-dialog-close' );

	if ( openBtn ) {
		openBtn.addEventListener( 'click', function () {
			dialog.showModal();
			var first =
				dialog.querySelector(
					'input:not([type="hidden"]):not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not(.trove-woo-review-dialog-close)'
				) || closeBtn;
			if ( first && typeof first.focus === 'function' ) {
				first.focus();
			}
		} );
	}

	if ( closeBtn ) {
		closeBtn.addEventListener( 'click', function () {
			dialog.close();
		} );
	}

	dialog.addEventListener( 'close', function () {
		if ( openBtn && typeof openBtn.focus === 'function' ) {
			openBtn.focus();
		}
	} );
} )();
