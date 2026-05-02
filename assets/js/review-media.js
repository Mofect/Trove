/**
 * Review form: multipart upload + optional selected-file hint.
 * Review list: lightbox for trove review photos.
 */
( function () {
	'use strict';

	var i18n =
		typeof troveReviewMedia !== 'undefined' && troveReviewMedia.i18n
			? troveReviewMedia.i18n
			: {
					close: 'Close',
					lightbox: 'Enlarged review image',
					remove: 'Remove selected photos',
					more: '+%d more',
			  };

	var lbState = {
		el: null,
		img: null,
		btn: null,
		backdrop: null,
		lastFocus: null,
	};

	function ensureLightbox() {
		if ( lbState.el ) {
			return lbState.el;
		}
		var root = document.createElement( 'div' );
		root.className = 'trove-review-lightbox';
		root.setAttribute( 'role', 'dialog' );
		root.setAttribute( 'aria-modal', 'true' );
		root.setAttribute( 'aria-label', i18n.lightbox );
		root.setAttribute( 'hidden', '' );
		root.setAttribute( 'aria-hidden', 'true' );

		var backdrop = document.createElement( 'button' );
		backdrop.type = 'button';
		backdrop.className = 'trove-review-lightbox__backdrop';
		backdrop.setAttribute( 'tabindex', '-1' );
		backdrop.setAttribute( 'aria-label', i18n.close );

		var inner = document.createElement( 'div' );
		inner.className = 'trove-review-lightbox__inner';

		var btn = document.createElement( 'button' );
		btn.type = 'button';
		btn.className = 'trove-review-lightbox__close';
		btn.setAttribute( 'aria-label', i18n.close );
		btn.innerHTML =
			'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

		var img = document.createElement( 'img' );
		img.className = 'trove-review-lightbox__img';
		img.alt = '';
		img.decoding = 'async';

		inner.appendChild( btn );
		inner.appendChild( img );
		root.appendChild( backdrop );
		root.appendChild( inner );
		document.body.appendChild( root );

		lbState.el = root;
		lbState.img = img;
		lbState.btn = btn;
		lbState.backdrop = backdrop;

		function close() {
			root.classList.remove( 'is-open' );
			root.setAttribute( 'hidden', '' );
			root.setAttribute( 'aria-hidden', 'true' );
			img.removeAttribute( 'src' );
			img.alt = '';
			document.body.style.overflow = '';
			document.removeEventListener( 'keydown', onKeydown );
			if ( lbState.lastFocus && typeof lbState.lastFocus.focus === 'function' ) {
				lbState.lastFocus.focus();
			}
			lbState.lastFocus = null;
		}

		function onKeydown( ev ) {
			if ( ev.key === 'Escape' ) {
				ev.preventDefault();
				close();
			}
		}

		btn.addEventListener( 'click', close );
		backdrop.addEventListener( 'click', close );

		root._troveCloseLightbox = close;
		root._troveOnKeydown = onKeydown;

		return root;
	}

	function openLightbox( anchor ) {
		var href = anchor.getAttribute( 'href' );
		if ( ! href ) {
			return;
		}
		var thumb = anchor.querySelector( 'img' );
		var alt = thumb && thumb.getAttribute( 'alt' ) ? thumb.getAttribute( 'alt' ) : '';

		var root = ensureLightbox();
		lbState.lastFocus = document.activeElement;
		lbState.img.src = href;
		lbState.img.alt = alt;

		document.removeEventListener( 'keydown', root._troveOnKeydown );
		root.removeAttribute( 'hidden' );
		root.setAttribute( 'aria-hidden', 'false' );
		root.classList.add( 'is-open' );
		document.body.style.overflow = 'hidden';
		document.addEventListener( 'keydown', root._troveOnKeydown );

		window.requestAnimationFrame( function () {
			if ( lbState.btn && typeof lbState.btn.focus === 'function' ) {
				lbState.btn.focus();
			}
		} );
	}

	document.addEventListener(
		'click',
		function ( e ) {
			var a = e.target && e.target.closest && e.target.closest( '#reviews a.trove-review-media__link' );
			if ( ! a ) {
				return;
			}
			e.preventDefault();
			openLightbox( a );
		},
		true
	);

	function setMultipart( root ) {
		var scope = root || document;
		var forms = scope.querySelectorAll( 'form#commentform' );
		forms.forEach( function ( form ) {
			form.setAttribute( 'enctype', 'multipart/form-data' );
		} );
	}

	setMultipart( document );

	document.addEventListener( 'DOMContentLoaded', function () {
		setMultipart( document );
	} );

	function formatSelectedFileNames( files ) {
		if ( ! files || ! files.length ) {
			return '';
		}
		if ( files.length === 1 ) {
			return files[ 0 ].name;
		}
		var tpl = i18n.more || '+%d more';
		var extra = tpl.replace( /%d/g, String( files.length - 1 ) );
		return files[ 0 ].name + ' ' + extra;
	}

	function syncAddPhotoField( input ) {
		var field = input.closest( '.trove-review-add-photo-field' );
		if ( ! field ) {
			return;
		}
		var textEl = field.querySelector( '.trove-review-add-photo__text' );
		var clearBtn = field.querySelector( '.trove-review-add-photo-clear' );
		if ( ! textEl || ! clearBtn ) {
			return;
		}
		var def = textEl.getAttribute( 'data-default' ) || '';
		var files = input.files;
		var n = files ? files.length : 0;
		if ( n < 1 ) {
			textEl.textContent = def;
			clearBtn.hidden = true;
			field.classList.remove( 'trove-review-add-photo-field--has-files' );
			return;
		}
		textEl.textContent = formatSelectedFileNames( files );
		clearBtn.hidden = false;
		field.classList.add( 'trove-review-add-photo-field--has-files' );
	}

	document.addEventListener( 'change', function ( e ) {
		var t = e.target;
		if ( ! t || ! t.matches || ! t.matches( '.trove-review-media-input' ) ) {
			return;
		}
		syncAddPhotoField( t );
	} );

	document.addEventListener(
		'click',
		function ( e ) {
			var btn = e.target.closest && e.target.closest( '.trove-review-add-photo-clear' );
			if ( ! btn ) {
				return;
			}
			var field = btn.closest( '.trove-review-add-photo-field' );
			if ( ! field ) {
				return;
			}
			var input = field.querySelector( '.trove-review-media-input' );
			if ( ! input ) {
				return;
			}
			e.preventDefault();
			e.stopPropagation();
			input.value = '';
			syncAddPhotoField( input );
		},
		true
	);
} )();
