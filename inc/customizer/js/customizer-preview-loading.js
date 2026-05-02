/**
 * Customizer controls UI: top progress bar while preview iframe reloads.
 */
( function ( api ) {
	'use strict';

	var root = null;
	var bar = null;
	var timer = null;
	var current = 0;
	var loading = false;
	var boundIframe = null;
	var iframeObserver = null;

	function ensureUi() {
		if ( root && bar ) {
			return true;
		}
		var mount =
			document.querySelector( '.wp-full-overlay' ) ||
			document.getElementById( 'customize-controls' ) ||
			document.body;
		if ( ! mount ) {
			return false;
		}
		root = document.createElement( 'div' );
		root.className = 'trove-cz-preview-progress';
		bar = document.createElement( 'div' );
		bar.className = 'trove-cz-preview-progress__bar';
		root.appendChild( bar );
		mount.appendChild( root );
		return true;
	}

	function setProgress( n ) {
		if ( ! bar ) {
			return;
		}
		current = Math.max( 0, Math.min( 100, n ) );
		bar.style.width = current + '%';
	}

	function stopTimer() {
		if ( timer ) {
			window.clearInterval( timer );
			timer = null;
		}
	}

	function start() {
		if ( ! ensureUi() ) {
			return;
		}
		loading = true;
		stopTimer();
		root.classList.add( 'is-active' );
		setProgress( 8 );
		timer = window.setInterval( function () {
			if ( ! loading ) {
				stopTimer();
				return;
			}
			if ( current < 85 ) {
				setProgress( current + ( current < 45 ? 6 : 2.5 ) );
			}
		}, 180 );
	}

	function done() {
		if ( ! ensureUi() ) {
			return;
		}
		loading = false;
		stopTimer();
		setProgress( 100 );
		window.setTimeout( function () {
			if ( ! root ) {
				return;
			}
			root.classList.remove( 'is-active' );
			setProgress( 0 );
		}, 260 );
	}

	function bindPreviewerEvents() {
		if ( ! api.previewer || ! api.previewer.bind ) {
			return;
		}
		api.previewer.bind( 'loading-initiated', start );
		api.previewer.bind( 'synced', done );
		api.previewer.bind( 'ready', done );
		api.previewer.bind( 'loaded', done );
		if ( api.previewer.previewUrl && api.previewer.previewUrl.bind ) {
			api.previewer.previewUrl.bind( function () {
				start();
			} );
		}
	}

	function bindIframeLoadFallback() {
		var iframe = document.querySelector( '#customize-preview iframe' );
		if ( ! iframe ) {
			return;
		}
		if ( boundIframe === iframe ) {
			return;
		}
		boundIframe = iframe;
		iframe.addEventListener( 'load', done );
		if ( iframeObserver ) {
			iframeObserver.disconnect();
			iframeObserver = null;
		}
		if ( typeof MutationObserver !== 'undefined' ) {
			iframeObserver = new MutationObserver( function ( mutations ) {
				mutations.forEach( function ( mutation ) {
					if ( mutation.type === 'attributes' && mutation.attributeName === 'src' ) {
						start();
					}
				} );
			} );
			iframeObserver.observe( iframe, { attributes: true, attributeFilter: [ 'src' ] } );
		}
	}

	api.bind( 'ready', function () {
		ensureUi();
		bindPreviewerEvents();
		bindIframeLoadFallback();
		window.setInterval( bindIframeLoadFallback, 1000 );
	} );
} )( wp.customize );
