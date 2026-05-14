/**
 * Fixed-position floating popover for wpColorPicker in Page settings (Customizer popover pattern).
 */
( function ( $ ) {
	'use strict';

	var active = null;
	var z = 1001000;
	var edgePad = 8;

	function getClipRect() {
		var el = document.getElementById( 'wpwrap' );
		return el ? el.getBoundingClientRect() : null;
	}

	function squeezeHolderIntoClip( $holder, $btn, placeAbove ) {
		var el = $holder[ 0 ];
		var btnEl = $btn[ 0 ];
		if ( ! el || ! btnEl ) {
			return;
		}
		var sb = getClipRect();
		if ( ! sb ) {
			return;
		}
		var gap = 10;
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
		/* Do not clamp top to sb.top when opened above the swatch — that shoves the panel down onto the swatch. */
		if ( ! placeAbove && top < sb.top + pad ) {
			top = sb.top + pad;
		}
		$holder.css( {
			left: Math.round( left ),
			top: Math.round( top ),
		} );
		hr = el.getBoundingClientRect();
		var btnRect = btnEl.getBoundingClientRect();
		if ( placeAbove && hr.bottom > btnRect.top - gap + 0.5 ) {
			top -= hr.bottom - ( btnRect.top - gap );
			$holder.css( 'top', Math.round( top ) );
		}
		var swatchCx = btnRect.left + btnRect.width / 2;
		var w = $holder.outerWidth() || hr.width;
		var triHalf = 9;
		var arrowX = swatchCx - hr.left;
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

		var $wrap = $container
			.find( '.wp-picker-input-wrap' )
			.filter( function () {
				return $( this ).closest( '.wp-picker-holder' ).length === 0;
			} )
			.first();

		if ( ! $wrap.length ) {
			if ( $holder.find( '.wp-picker-input-wrap' ).length ) {
				el.dataset.troveMbInputRelocated = '1';
				$container.addClass( 'trove-cz-input-in-holder' );
			}
			return;
		}

		$wrap.appendTo( $holder );
		el.dataset.troveMbInputRelocated = '1';
		$container.addClass( 'trove-cz-input-in-holder' );
		if ( active && active.length && active[ 0 ] === el ) {
			positionHolder( $container );
		}
	}

	function relocateAll() {
		$( '.trove-mb-cz-layout .wp-picker-container' ).each( function () {
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
		var sb = getClipRect();
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
		var sbBottom = sb ? sb.bottom : window.innerHeight - 12;
		var sbTop = sb ? sb.top : edgePad;
		/* Prefer popover directly above the swatch (avoids covering description text below). */
		var placeAbove = true;
		var top = rect.top - h - gap;
		if ( top < sbTop + edgePad ) {
			placeAbove = false;
			top = rect.bottom + gap;
			if ( top + h > sbBottom - edgePad ) {
				top = Math.max( sbTop + edgePad, sbBottom - edgePad - h );
			}
		} else {
			top = Math.max( sbTop + edgePad, top );
		}

		if ( ! placeAbove && top + h > sbBottom - edgePad ) {
			top = Math.max( sbTop + edgePad, sbBottom - edgePad - h );
		}

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

		requestAnimationFrame( function () {
			squeezeHolderIntoClip( $holder, $btn, placeAbove );
			requestAnimationFrame( function () {
				squeezeHolderIntoClip( $holder, $btn, placeAbove );
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
		if ( el.dataset.troveMbPopoverBound ) {
			return;
		}
		el.dataset.troveMbPopoverBound = '1';

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
		document.querySelectorAll( '.trove-mb-cz-layout .wp-picker-container' ).forEach( function ( el ) {
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
		$( window ).on( 'resize.troveMbCzPopover', syncActive );
		var $scroll = $( '#wpbody-content' );
		if ( $scroll.length ) {
			$scroll.on( 'scroll.troveMbCzPopover', syncActive );
		}

		$( document ).on( 'click', '.trove-mb-cz-layout .wp-color-result', function () {
			var $c = $( this ).closest( '.wp-picker-container' );
			active = $c;
			relocateInputWrap( $c );
			setTimeout( function () {
				positionHolder( $c );
			}, 50 );
			setTimeout( function () {
				positionHolder( $c );
			}, 200 );
			setTimeout( function () {
				positionHolder( $c );
			}, 450 );
		} );

		$( document ).on( 'trove:metaboxPopoverRelocate', function ( _e, $input ) {
			if ( ! $input || ! $input.closest ) {
				return;
			}
			var $c = $input.closest( '.wp-picker-container' );
			relocateInputWrap( $c );
			if ( $c.hasClass( 'wp-picker-active' ) || $c.hasClass( 'wp-picker-open' ) ) {
				requestAnimationFrame( function () {
					positionHolder( $c );
				} );
			}
		} );

		$( document ).on(
			'wpcolorpickerinit',
			'.trove-mb-cz-layout input.trove-cz-swatch-picker',
			function () {
				relocateInputWrap( $( this ).closest( '.wp-picker-container' ) );
			}
		);
	}

	$( function () {
		if ( ! $( '.trove-mb-cz-layout' ).length ) {
			return;
		}
		init();
		[ 100, 300, 800, 1500 ].forEach( function ( ms ) {
			setTimeout( function () {
				relocateAll();
				scan();
			}, ms );
		} );
	} );
}( jQuery ) );
