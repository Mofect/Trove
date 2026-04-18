/**
 * Customizer: repeater text control (JSON array of strings).
 */
( function ( $ ) {
	'use strict';

	function parseLines( raw ) {
		try {
			var arr = JSON.parse( raw || '[]' );
			return Array.isArray( arr ) ? arr : [];
		} catch ( e ) {
			return [];
		}
	}

	function serializeFromInputs( $wrap ) {
		var out = [];
		$wrap.find( '.trove-cz-repeater-text__input' ).each( function () {
			var v = String( $( this ).val() || '' ).trim();
			if ( v !== '' ) {
				out.push( v );
			}
		} );
		return JSON.stringify( out );
	}

	function setHiddenAndNotify( $wrap, json ) {
		var $h = $wrap.find( 'input[type="hidden"][data-customize-setting-link]' );
		if ( ! $h.length ) {
			return;
		}
		$h.val( json ).trigger( 'change' );
	}

	function removeLabel() {
		return typeof troveRepeaterTextI18n !== 'undefined' && troveRepeaterTextI18n.remove
			? troveRepeaterTextI18n.remove
			: 'Remove';
	}

	function appendRow( $list, index, value ) {
		var id =
			'trove-repeater-' +
			Math.random().toString( 36 ).slice( 2 ) +
			'-' +
			index;
		var $row = $( '<li class="trove-cz-repeater-text__row" />' );
		var $input = $( '<input type="text" class="trove-cz-repeater-text__input widefat" />' )
			.attr( 'id', id )
			.val( value == null ? '' : String( value ) );
		var $rm = $( '<button type="button" class="button-link trove-cz-repeater-text__remove" />' )
			.attr( 'aria-label', removeLabel() )
			.html( '&times;' );
		$row.append( $input, $rm );
		$list.append( $row );
	}

	function syncAddButtonState( $wrap ) {
		var max = parseInt( $wrap.data( 'max' ), 10 ) || 10;
		var n = $wrap.find( '.trove-cz-repeater-text__row' ).length;
		var $btn = $wrap.find( '.trove-cz-repeater-text__add' );
		$btn.prop( 'disabled', n >= max );
	}

	function rebuildRows( $wrap, lines ) {
		var $list = $wrap.find( '.trove-cz-repeater-text__list' );
		$list.empty();
		var i;
		for ( i = 0; i < lines.length; i++ ) {
			appendRow( $list, i, lines[ i ] );
		}
		syncAddButtonState( $wrap );
	}

	function initWrap( $wrap ) {
		if ( $wrap.data( 'troveRepeaterInit' ) ) {
			return;
		}
		$wrap.data( 'troveRepeaterInit', true );

		var $hidden = $wrap.find( 'input[type="hidden"][data-customize-setting-link]' );
		if ( ! $hidden.length ) {
			return;
		}

		var settingId = $hidden.data( 'customizeSettingLink' );
		rebuildRows( $wrap, parseLines( $hidden.val() ) );

		$wrap.on( 'input change', '.trove-cz-repeater-text__input', function () {
			setHiddenAndNotify( $wrap, serializeFromInputs( $wrap ) );
		} );

		$wrap.on( 'click', '.trove-cz-repeater-text__add', function ( e ) {
			e.preventDefault();
			var max = parseInt( $wrap.data( 'max' ), 10 ) || 10;
			var n = $wrap.find( '.trove-cz-repeater-text__row' ).length;
			if ( n >= max ) {
				return;
			}
			var $list = $wrap.find( '.trove-cz-repeater-text__list' );
			appendRow( $list, n, '' );
			syncAddButtonState( $wrap );
			var $last = $list.find( '.trove-cz-repeater-text__input' ).last();
			$last.trigger( 'focus' );
			setHiddenAndNotify( $wrap, serializeFromInputs( $wrap ) );
		} );

		$wrap.on( 'click', '.trove-cz-repeater-text__remove', function ( e ) {
			e.preventDefault();
			$( this ).closest( '.trove-cz-repeater-text__row' ).remove();
			syncAddButtonState( $wrap );
			setHiddenAndNotify( $wrap, serializeFromInputs( $wrap ) );
		} );

		if ( typeof wp !== 'undefined' && wp.customize && settingId ) {
			wp.customize( settingId, function ( value ) {
				value.bind( function ( newVal ) {
					rebuildRows( $wrap, parseLines( newVal ) );
				} );
			} );
		}
	}

	function scan() {
		$( '#customize-controls .trove-cz-repeater-text' ).each( function () {
			initWrap( $( this ) );
		} );
	}

	$( function () {
		if ( typeof wp === 'undefined' || ! wp.customize ) {
			return;
		}
		wp.customize.bind( 'ready', function () {
			scan();
		} );
		scan();
	} );
} )( jQuery );
