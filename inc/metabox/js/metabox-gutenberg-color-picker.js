/**
 * Mount Gutenberg (components) ColorPicker inside Page settings wpColorPicker holders.
 * Same pattern as inc/customizer/js/customizer-gutenberg-color-picker.js, without wp.customize.
 */
( function ( $, wp, w ) {
	'use strict';

	if ( ! wp || ! wp.element ) {
		return;
	}

	var el = wp.element.createElement;
	var roots = new WeakMap();
	var retryTimer = null;

	function getColorPickerComponent() {
		if ( wp.components ) {
			return wp.components.ColorPicker || wp.components.__experimentalColorPicker || null;
		}
		if ( wp.blockEditor ) {
			return wp.blockEditor.ColorPicker || wp.blockEditor.__experimentalColorPicker || null;
		}
		return null;
	}

	function renderInto( node, vnode ) {
		if ( wp.element.createRoot ) {
			var root = roots.get( node );
			if ( ! root ) {
				root = wp.element.createRoot( node );
				roots.set( node, root );
			}
			root.render( vnode );
			return true;
		}
		if ( wp.element.render ) {
			wp.element.render( vnode, node );
			return true;
		}
		return false;
	}

	function sanitizeColor( value, fallback ) {
		var v = value && String( value ).trim() ? String( value ).trim() : '';
		return v || fallback || '#000000';
	}

	function coercePickerOnChangeValue( next ) {
		if ( next == null ) {
			return '';
		}
		if ( typeof next === 'object' && typeof next.hex === 'string' ) {
			return String( next.hex ).trim();
		}
		return String( next ).trim();
	}

	function mountPicker( input ) {
		var $input = $( input );
		if ( $input.hasClass( 'trove-cz-gb-picker-inited' ) ) {
			return;
		}
		if ( ! $input.closest( '.trove-mb-cz-layout' ).length ) {
			return;
		}
		var $container = $input.closest( '.wp-picker-container' );
		var $holder = $container.find( '.wp-picker-holder' );
		if ( ! $holder.length ) {
			return;
		}

		var $mount = $( '<div class="trove-cz-gb-picker"></div>' );
		var $wrap = $holder.find( '.wp-picker-input-wrap' ).first();
		if ( $wrap.length ) {
			$mount.insertBefore( $wrap );
		} else {
			$holder.prepend( $mount );
		}

		var updating = false;
		var current = sanitizeColor(
			$input.val(),
			$input.attr( 'data-default-color' ) || ''
		);

		function commit( next ) {
			var raw = coercePickerOnChangeValue( next );
			var color = sanitizeColor( raw, current );
			if ( color === current ) {
				renderPicker();
				return;
			}
			current = color;
			updating = true;
			$input.val( color ).trigger( 'change' );
			try {
				if ( $.fn.wpColorPicker && typeof $input.wpColorPicker === 'function' ) {
					$input.wpColorPicker( 'color', color );
				}
			} catch ( _sync ) {}
			updating = false;
			renderPicker();
		}

		function renderPicker() {
			try {
				var ColorPicker = getColorPickerComponent();
				if ( ! ColorPicker ) {
					return false;
				}
				var ok = renderInto(
					$mount[ 0 ],
					el( ColorPicker, {
						color: current,
						enableAlpha: true,
						onChange: commit,
					} )
				);
				return !! ok;
			} catch ( _err ) {
				return false;
			}
		}

		if ( ! renderPicker() ) {
			$mount.remove();
			return;
		}

		$input.addClass( 'trove-cz-gb-picker-inited' );
		$holder.addClass( 'trove-cz-gb-mounted' );
		$( document ).trigger( 'trove:metaboxPopoverRelocate', [ $input ] );

		$input.on( 'change.troveMbGbSync', function () {
			if ( updating ) {
				return;
			}
			current = sanitizeColor( $input.val(), current );
			if ( ! renderPicker() ) {
				$holder.removeClass( 'trove-cz-gb-mounted' );
				$mount.remove();
				$input.removeClass( 'trove-cz-gb-picker-inited' );
				$input.off( 'change.troveMbGbSync' );
			}
		} );
	}

	function scan() {
		$( '.trove-mb-cz-layout input.trove-cz-swatch-picker' ).each( function () {
			mountPicker( this );
		} );
	}

	function scheduleRetry() {
		if ( retryTimer ) {
			return;
		}
		retryTimer = w.setTimeout( function () {
			retryTimer = null;
			scan();
		}, 300 );
	}

	function boot() {
		scan();
		[ 100, 400, 900, 1600 ].forEach( function ( ms ) {
			setTimeout( scan, ms );
		} );
		[ 2500, 4000 ].forEach( function ( ms ) {
			setTimeout( scheduleRetry, ms );
		} );
	}

	$( function () {
		if ( $( '.trove-mb-cz-layout' ).length ) {
			boot();
		}
	} );

	$( document ).on( 'trove:metaboxColorPickerReady', function ( _e, $input ) {
		if ( $input && $input.length ) {
			mountPicker( $input[ 0 ] );
		}
	} );
}( jQuery, window.wp, window ) );
