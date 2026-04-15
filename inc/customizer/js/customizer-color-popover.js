/**
 * Float Iris below the swatch (fixed), move hex/Default into the holder, keep layout from shifting.
 */
( function ( $ ) {
	'use strict';

	var active = null;
	var z = 600100;
	var edgePad = 8;

	/**
	 * Left sidebar bounds (viewport) — keep the floating picker inside the Customizer panel, not over the preview.
	 */
	function getSidebarRect() {
		var el = document.getElementById( 'customize-controls' );
		return el ? el.getBoundingClientRect() : null;
	}

	/**
	 * After layout, correct fixed coordinates using real box sizes (Iris width, transform ancestors).
	 * Recomputes caret so it still aims at the swatch as far as bounds allow.
	 */
	function squeezeHolderIntoSidebar( $holder, $btn ) {
		var el = $holder[ 0 ];
		var btnEl = $btn[ 0 ];
		if ( ! el || ! btnEl ) {
			return;
		}
		var sb = getSidebarRect();
		if ( ! sb ) {
			return;
		}
		var hr = el.getBoundingClientRect();
		var pad = edgePad;
		var left = hr.left;
		var top = hr.top;
		if ( hr.right > sb.right - pad ) {
			left -= hr.right - ( sb.right - pad );
		}
		if ( left < sb.left + pad ) {
			left = sb.left + pad;
		}
		if ( hr.bottom > sb.bottom - pad ) {
			top -= hr.bottom - ( sb.bottom - pad );
		}
		if ( top < sb.top + pad ) {
			top = sb.top + pad;
		}
		$holder.css( {
			left: Math.round( left ),
			top: Math.round( top ),
		} );
		var btnRect = btnEl.getBoundingClientRect();
		var swatchCx = btnRect.left + btnRect.width / 2;
		var w = $holder.outerWidth() || hr.width;
		var triHalf = 9;
		var arrowX = swatchCx - left;
		arrowX = Math.max(
			triHalf + 4,
			Math.min( w - triHalf - 4, arrowX )
		);
		$holder.css( '--trove-cz-arrow-x', Math.round( arrowX ) + 'px' );
	}

	function relocateInputWrap( $container ) {
		var el = $container[ 0 ];
		if ( ! el ) {
			return;
		}
		var $holder = $container.find( '.wp-picker-holder' );
		if ( ! $holder.length ) {
			return;
		}

		/* Any wrap still outside the holder (WP may not keep it as a direct child). */
		var $wrap = $container
			.find( '.wp-picker-input-wrap' )
			.filter( function () {
				return $( this ).closest( '.wp-picker-holder' ).length === 0;
			} )
			.first();

		if ( ! $wrap.length ) {
			if ( $holder.find( '.wp-picker-input-wrap' ).length ) {
				el.dataset.troveCzInputRelocated = '1';
				$container.addClass( 'trove-cz-input-in-holder' );
			}
			return;
		}

		$wrap.appendTo( $holder );
		el.dataset.troveCzInputRelocated = '1';
		$container.addClass( 'trove-cz-input-in-holder' );
		if ( active && active.length && active[ 0 ] === el ) {
			positionHolder( $container );
		}
	}

	function relocateAll() {
		$( '#customize-theme-controls .wp-picker-container' ).each( function () {
			relocateInputWrap( $( this ) );
		} );
	}

	function positionHolder( $container ) {
		var $holder = $container.find( '.wp-picker-holder' );
		var $btn = $container.find( '.wp-color-result' ).first();
		if ( ! $holder.length || ! $btn.length ) {
			return;
		}
		if ( ! $container.hasClass( 'wp-picker-active' ) && ! $container.hasClass( 'wp-picker-open' ) ) {
			return;
		}
		if ( ! $holder.is( ':visible' ) ) {
			return;
		}

		var rect = $btn[ 0 ].getBoundingClientRect();
		var w = $holder.outerWidth() || 280;
		var h = $holder.outerHeight() || 320;
		var swatchCx = rect.left + rect.width / 2;

		var left = swatchCx - w / 2;
		var sb = getSidebarRect();
		if ( sb ) {
			var minLeft = sb.left + edgePad;
			var maxLeft = sb.right - w - edgePad;
			if ( maxLeft < minLeft ) {
				maxLeft = minLeft;
			}
			left = Math.max( minLeft, Math.min( left, maxLeft ) );
		} else {
			left = Math.max( edgePad, Math.min( left, window.innerWidth - w - edgePad ) );
		}

		var gap = 10;
		var placeAbove = false;
		var top = rect.bottom + gap;
		var sbBottom = sb ? sb.bottom : window.innerHeight - 12;
		var sbTop = sb ? sb.top : edgePad;
		if ( top + h > sbBottom - edgePad ) {
			placeAbove = true;
			top = rect.top - h - gap;
			if ( top < sbTop + edgePad ) {
				top = sbTop + edgePad;
			}
		}

		if ( top + h > sbBottom - edgePad ) {
			top = Math.max( sbTop + edgePad, sbBottom - edgePad - h );
		}

		/* Horizontal offset so the caret points at this picker’s swatch (viewport vs holder). */
		var triHalf = 9;
		var arrowX = swatchCx - left;
		arrowX = Math.max(
			triHalf + 4,
			Math.min( w - triHalf - 4, arrowX )
		);

		$holder
			.toggleClass( 'trove-cz-float-holder--above', placeAbove )
			.addClass( 'trove-cz-float-holder' )
			.css( {
				position: 'fixed',
				left: Math.round( left ),
				top: Math.round( top ),
				right: 'auto',
				bottom: 'auto',
				zIndex: z,
				margin: 0,
				transform: 'none',
				display: 'block',
				visibility: 'visible',
				'--trove-cz-arrow-x': Math.round( arrowX ) + 'px',
			} );

		/* Iris/layout can change width after first paint; fixed + transform can offset vs math. */
		requestAnimationFrame( function () {
			squeezeHolderIntoSidebar( $holder, $btn );
			requestAnimationFrame( function () {
				squeezeHolderIntoSidebar( $holder, $btn );
			} );
		} );
	}

	function clearHolder( $container ) {
		$container
			.find( '.wp-picker-holder' )
			.removeClass( 'trove-cz-float-holder trove-cz-float-holder--above' )
			.css( {
				position: '',
				left: '',
				top: '',
				right: '',
				bottom: '',
				zIndex: '',
				margin: '',
				transform: '',
				display: '',
				visibility: '',
				'--trove-cz-arrow-x': '',
			} );
	}

	function bindContainer( el ) {
		if ( el.dataset.troveCzPopoverBound ) {
			return;
		}
		el.dataset.troveCzPopoverBound = '1';

		var $c = $( el );
		var mo = new MutationObserver( function () {
			var open = $c.hasClass( 'wp-picker-active' ) || $c.hasClass( 'wp-picker-open' );
			if ( open ) {
				active = $c;
				relocateInputWrap( $c );
				requestAnimationFrame( function () {
					requestAnimationFrame( function () {
						positionHolder( $c );
					} );
				} );
			} else {
				if ( active && active[ 0 ] === el ) {
					active = null;
				}
				clearHolder( $c );
			}
		} );
		mo.observe( el, { attributes: true, attributeFilter: [ 'class' ] } );
	}

	function scan() {
		document.querySelectorAll( '#customize-theme-controls .wp-picker-container' ).forEach( function ( el ) {
			relocateInputWrap( $( el ) );
			bindContainer( el );
		} );
	}

	function syncActive() {
		if ( active && active.length ) {
			positionHolder( active );
		}
	}

	function init() {
		relocateAll();
		scan();
		$( window ).on( 'resize.troveCzPopover', syncActive );
		var $sb = $( '#customize-controls .wp-full-overlay-sidebar-content' );
		if ( $sb.length ) {
			$sb.on( 'scroll.troveCzPopover', syncActive );
		}

		$( document ).on( 'click', '#customize-theme-controls .wp-color-result', function () {
			var $c = $( this ).closest( '.wp-picker-container' );
			active = $c;
			relocateInputWrap( $c );
			setTimeout( function () {
				positionHolder( $c );
			}, 50 );
		} );

		$( document ).on( 'trove:colorPickerReady', function ( _e, $input ) {
			if ( ! $input || ! $input.closest ) {
				return;
			}
			relocateInputWrap( $input.closest( '.wp-picker-container' ) );
		} );

		$( document ).on(
			'wpcolorpickerinit',
			'#customize-theme-controls input.wp-color-picker',
			function () {
				relocateInputWrap( $( this ).closest( '.wp-picker-container' ) );
			}
		);
	}

	if ( typeof wp !== 'undefined' && wp.customize ) {
		wp.customize.bind( 'ready', function () {
			init();
			[ 100, 300, 800, 1500 ].forEach( function ( ms ) {
				setTimeout( function () {
					relocateAll();
					scan();
				}, ms );
			} );
		} );
	} else {
		$( init );
	}
}( jQuery ) );
