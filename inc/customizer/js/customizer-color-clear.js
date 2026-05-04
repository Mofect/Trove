/**
 * Clear (×) control beside color / color_pair swatches in trove-cz-swatches sections.
 */
( function ( $, wp ) {
	'use strict';

	var i18n =
		typeof troveCzColorClear !== 'undefined' && troveCzColorClear
			? troveCzColorClear
			: { clearSingle: 'Clear', clearPair: 'Clear' };

	function parseIds( attr ) {
		if ( ! attr || typeof attr !== 'string' ) {
			return [];
		}
		return attr
			.split( ',' )
			.map( function ( s ) {
				return String( s ).trim();
			} )
			.filter( Boolean );
	}

	function syncPickersAfterClear( $li, ids ) {
		if ( ! $.fn.wpColorPicker ) {
			return;
		}
		$li.find( 'input[data-customize-setting-link]' ).each( function () {
			var id = this.getAttribute( 'data-customize-setting-link' );
			if ( ! id || ids.indexOf( id ) === -1 ) {
				return;
			}
			var $i = $( this );
			$i.val( '' );
			try {
				$i.wpColorPicker( 'color', '' );
			} catch ( _e ) {}
		} );
		$li.find( 'input.trove-cz-swatch-picker' ).each( function () {
			var id = this.getAttribute( 'data-trove-setting' );
			if ( ! id || ids.indexOf( id ) === -1 ) {
				return;
			}
			var $i = $( this );
			$i.val( '' );
			try {
				$i.wpColorPicker( 'color', '' );
			} catch ( _e ) {}
		} );
	}

	function ensureClearButtons() {
		var $root = $( '#customize-theme-controls' );
		if ( ! $root.length ) {
			return;
		}

		$root.find( '.trove-cz-swatches li.customize-control-color' ).each( function () {
			var $li = $( this );
			if ( $li.find( '> .trove-cz-color-clear' ).length ) {
				return;
			}
			var $input = $li.find( 'input[data-customize-setting-link]' ).first();
			var sid = $input.attr( 'data-customize-setting-link' );
			if ( ! sid ) {
				return;
			}
			var $btn = $( '<button type="button" class="trove-cz-color-clear trove-cz-color-clear--single" />' );
			$btn.attr( 'data-trove-clear-setting', sid );
			$btn.attr( 'aria-label', i18n.clearSingle || 'Clear' );
			$btn.attr( 'title', i18n.clearSingle || 'Clear' );
			$btn.html( '<span aria-hidden="true">&times;</span>' );
			var $content = $li.children( '.customize-control-content' ).first();
			if ( $content.length ) {
				$content.after( $btn );
			} else {
				$li.append( $btn );
			}
		} );

		$root.find( '.trove-cz-swatches li.customize-control-trove_dual_color' ).each( function () {
			var $li = $( this );
			if ( $li.find( '> .trove-cz-color-clear' ).length ) {
				return;
			}
			var ids = [];
			$li.find( 'input.trove-cz-swatch-picker' ).each( function () {
				var id = this.getAttribute( 'data-trove-setting' );
				if ( id ) {
					ids.push( id );
				}
			} );
			if ( ! ids.length ) {
				return;
			}
			var $btn = $( '<button type="button" class="trove-cz-color-clear trove-cz-color-clear--pair" />' );
			$btn.attr( 'data-trove-clear-settings', ids.join( ',' ) );
			$btn.attr( 'aria-label', i18n.clearPair || 'Clear' );
			$btn.attr( 'title', i18n.clearPair || 'Clear' );
			$btn.html( '<span aria-hidden="true">&times;</span>' );
			var $sw = $li.children( '.trove-dual-color__swatches' ).first();
			if ( $sw.length ) {
				$sw.after( $btn );
			} else {
				$li.append( $btn );
			}
		} );
	}

	var debounceTimer;
	function debouncedEnsure() {
		clearTimeout( debounceTimer );
		debounceTimer = setTimeout( ensureClearButtons, 60 );
	}

	if ( wp && wp.customize ) {
		wp.customize.bind( 'ready', function () {
			ensureClearButtons();
			setTimeout( ensureClearButtons, 200 );
			setTimeout( ensureClearButtons, 800 );

			var root = document.getElementById( 'customize-theme-controls' );
			if ( root && typeof MutationObserver !== 'undefined' ) {
				var mo = new MutationObserver( debouncedEnsure );
				mo.observe( root, { childList: true, subtree: true } );
			}
		} );
	}

	$( document ).on( 'click', '#customize-theme-controls .trove-cz-swatches .trove-cz-color-clear', function ( e ) {
		e.preventDefault();
		e.stopPropagation();
		var $btn = $( this );
		var ids = parseIds( $btn.attr( 'data-trove-clear-settings' ) );
		if ( ! ids.length ) {
			var one = $btn.attr( 'data-trove-clear-setting' );
			if ( one ) {
				ids = [ one ];
			}
		}
		if ( ! ids.length || ! wp || ! wp.customize ) {
			return;
		}
		var $li = $btn.closest( 'li.customize-control' );
		ids.forEach( function ( id ) {
			var s = wp.customize( id );
			if ( s ) {
				s.set( '' );
			}
		} );
		syncPickersAfterClear( $li, ids );
		$( document ).trigger( 'trove:colorCleared', [ $li, ids ] );
		setTimeout( function () {
			$li.find( 'input.wp-color-picker, input.trove-cz-swatch-picker' ).trigger( 'change' );
		}, 0 );
	} );
}( jQuery, window.wp ) );
