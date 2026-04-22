/**
 * Mount Gutenberg ColorPicker inside Customizer color popovers.
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

	function getSettingId( $input ) {
		var containerId = $input.closest( '.customize-control' ).attr( 'id' ) || '';
		var fromControlId = '';
		if ( containerId.indexOf( 'customize-control-' ) === 0 ) {
			fromControlId = containerId.replace( /^customize-control-/, '' );
		}
		return (
			$input.attr( 'data-trove-setting' ) ||
			$input.attr( 'data-customize-setting-link' ) ||
			fromControlId ||
			''
		);
	}

	function sanitizeColor( value, fallback ) {
		var v = value && String( value ).trim() ? String( value ).trim() : '';
		return v || fallback || '#000000';
	}

	/**
	 * @param {*} next Value from ColorPicker onChange (string or rare legacy object).
	 * @return {string}
	 */
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
		var sid = getSettingId( $input );
		var $container = $input.closest( '.wp-picker-container' );
		var $holder = $container.find( '.wp-picker-holder' );
		if ( ! $holder.length ) {
			return;
		}
		var $iris = $holder.find( '.iris-picker' ).first();

		var $mount = $( '<div class="trove-cz-gb-picker"></div>' );
		/* jQuery insertBefore( empty ) is a no-op — mount must be in the document or React paints nothing. */
		var $wrap = $holder.find( '.wp-picker-input-wrap' ).first();
		if ( $wrap.length ) {
			$mount.insertBefore( $wrap );
		} else {
			$holder.prepend( $mount );
		}

		var updating = false;
		var settingValue = sid && wp.customize( sid ) ? wp.customize( sid )() : $input.val();
		var current = sanitizeColor(
			settingValue,
			$input.attr( 'data-default-color' ) || $input.val()
		);

		function commit( next ) {
			var raw = coercePickerOnChangeValue( next );
			var color = sanitizeColor( raw, current );
			/* Keep controlled ColorPicker in sync: props only update when we re-render the root. */
			if ( color === current ) {
				renderPicker();
				return;
			}
			current = color;
			updating = true;
			$input.val( color ).trigger( 'change' );
			if ( sid && wp.customize( sid ) ) {
				wp.customize( sid ).set( color );
			}
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

		/* Only mark mounted after successful render; otherwise keep native iris visible. */
		$input.addClass( 'trove-cz-gb-picker-inited' );
		$holder.addClass( 'trove-cz-gb-mounted' );

		if ( sid && wp.customize && wp.customize( sid ) ) {
			wp.customize( sid, function ( setting ) {
				setting.bind( function ( value ) {
					if ( updating ) {
						return;
					}
					current = sanitizeColor( value, current );
					if ( ! renderPicker() ) {
						$holder.removeClass( 'trove-cz-gb-mounted' );
						$mount.remove();
						$input.removeClass( 'trove-cz-gb-picker-inited' );
					}
				} );
			} );
		}
	}

	function scan() {
		$(
			'#customize-theme-controls input.wp-color-picker, #customize-theme-controls input.color-picker-hex, #customize-theme-controls input.trove-cz-swatch-picker'
		).each( function () {
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

	if ( wp.customize ) {
		wp.customize.bind( 'ready', function () {
			scan();
			[ 100, 400, 900, 1600 ].forEach( function ( ms ) {
				setTimeout( scan, ms );
			} );
			/* If components package is late, keep trying briefly. */
			[ 2500, 4000 ].forEach( function ( ms ) {
				setTimeout( scheduleRetry, ms );
			} );
		} );
	}

	/* Core single-color controls announce this when wpColorPicker init finishes. */
	$( document ).on(
		'wpcolorpickerinit',
		'#customize-theme-controls input.wp-color-picker, #customize-theme-controls input.color-picker-hex',
		function () {
			mountPicker( this );
		}
	);

	/* Trove dual control hook (emitted after each dual swatch picker init). */
	$( document ).on( 'trove:colorPickerReady', function ( _e, $input ) {
		if ( $input && $input.length ) {
			mountPicker( $input[ 0 ] );
		}
	} );

}( jQuery, window.wp, window ) );

