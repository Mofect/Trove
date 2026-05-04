/**
 * Customizer: repeater key-value control (JSON array of {title, content} objects).
 */
( function ( $ ) {
	'use strict';

	function parseRows( raw ) {
		try {
			var arr = JSON.parse( raw || '[]' );
			return Array.isArray( arr ) ? arr : [];
		} catch ( e ) {
			return [];
		}
	}

	function serializeFromInputs( $wrap ) {
		var out = [];
		$wrap.find( '.trove-cz-repeater-kv__row' ).each( function () {
			var $row = $( this );
			var title = String( $row.find( '.trove-cz-repeater-kv__input-key' ).val() || '' ).trim();
			var content = String( $row.find( '.trove-cz-repeater-kv__input-value' ).val() || '' ).trim();
			if ( title !== '' || content !== '' ) {
				out.push( { title: title, content: content } );
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

	function updateToggleLabel( $row ) {
		var title = String( $row.find( '.trove-cz-repeater-kv__input-key' ).val() || '' ).trim();
		var $label = $row.find( '.trove-cz-repeater-kv__toggle-label' );
		$label.text( title || $( '#tmpl-trove-repeater-kv-row' ).data( 'untitled' ) || 'Untitled' );
	}

	function updateRowSaveState( $row ) {
		var $save = $row.find( '.trove-cz-repeater-kv__save' );
		var $wrap = $row.closest( '.trove-cz-repeater-kv' );
		var current = serializeFromInputs( $wrap );
		var saved = $wrap.data( 'troveRepeaterKvSaved' ) || '[]';
		$save.prop( 'disabled', current === saved );
	}

	function appendRow( $list, data ) {
		var tmpl = $( '#tmpl-trove-repeater-kv-row' ).html();
		if ( ! tmpl ) {
			return;
		}
		var $row = $( tmpl.trim() );
		$row.find( '.trove-cz-repeater-kv__input-key' ).val( data && data.title ? data.title : '' );
		$row.find( '.trove-cz-repeater-kv__input-value' ).val( data && data.content ? data.content : '' );
		// Default collapsed.
		$row.addClass( 'is-collapsed' );
		$row.find( '.trove-cz-repeater-kv__toggle' ).attr( 'aria-expanded', 'false' );
		updateToggleLabel( $row );
		updateRowSaveState( $row );
		$list.append( $row );
	}

	function syncAddButtonState( $wrap ) {
		var max = parseInt( $wrap.data( 'max' ), 10 ) || 10;
		var n = $wrap.find( '.trove-cz-repeater-kv__row' ).length;
		var $btn = $wrap.find( '.trove-cz-repeater-kv__add' );
		$btn.prop( 'disabled', n >= max );
	}

	function rebuildRows( $wrap, rows ) {
		var $list = $wrap.find( '.trove-cz-repeater-kv__list' );
		$list.empty();
		var i;
		for ( i = 0; i < rows.length; i++ ) {
			appendRow( $list, rows[ i ] );
		}
		syncAddButtonState( $wrap );
	}

	function syncMoveButtons( $wrap ) {
		var $rows = $wrap.find( '.trove-cz-repeater-kv__row' );
		$rows.each( function ( index ) {
			var $row = $( this );
			$row.find( '.trove-cz-repeater-kv__move-up' ).prop( 'disabled', index === 0 );
			$row.find( '.trove-cz-repeater-kv__move-down' ).prop( 'disabled', index === $rows.length - 1 );
		} );
	}

	function initDragAndDrop( $wrap ) {
		var $list = $wrap.find( '.trove-cz-repeater-kv__list' );
		var draggedRow = null;

		$list.on( 'dragstart', '.trove-cz-repeater-kv__row', function ( e ) {
			draggedRow = this;
			$( this ).addClass( 'is-dragging' );
			if ( e.originalEvent && e.originalEvent.dataTransfer ) {
				e.originalEvent.dataTransfer.effectAllowed = 'move';
			}
		} );

		$list.on( 'dragend', '.trove-cz-repeater-kv__row', function () {
			$( this ).removeClass( 'is-dragging' );
			$list.find( '.trove-cz-repeater-kv__row' ).removeClass( 'is-drag-over' );
			draggedRow = null;
		} );

		$list.on( 'dragover', '.trove-cz-repeater-kv__row', function ( e ) {
			e.preventDefault();
			if ( this !== draggedRow ) {
				$( this ).addClass( 'is-drag-over' );
			}
		} );

		$list.on( 'dragleave', '.trove-cz-repeater-kv__row', function () {
			$( this ).removeClass( 'is-drag-over' );
		} );

		$list.on( 'drop', '.trove-cz-repeater-kv__row', function ( e ) {
			e.preventDefault();
			$( this ).removeClass( 'is-drag-over' );
			if ( ! draggedRow || draggedRow === this ) {
				return;
			}
			$( draggedRow ).insertAfter( this );
			syncMoveButtons( $wrap );
			var json = serializeFromInputs( $wrap );
			$wrap.data( 'troveRepeaterKvSaved', json );
			setHiddenAndNotify( $wrap, json );
			$wrap.find( '.trove-cz-repeater-kv__row' ).each( function () {
				updateRowSaveState( $( this ) );
			} );
		} );
	}

	function initWrap( $wrap ) {
		if ( $wrap.data( 'troveRepeaterKvInit' ) ) {
			return;
		}
		$wrap.data( 'troveRepeaterKvInit', true );

		var $hidden = $wrap.find( 'input[type="hidden"][data-customize-setting-link]' );
		if ( ! $hidden.length ) {
			return;
		}

		var settingId = $hidden.data( 'customizeSettingLink' );
		var savedValue = $hidden.val() || '[]';
		$wrap.data( 'troveRepeaterKvSaved', savedValue );
		rebuildRows( $wrap, parseRows( savedValue ) );
		syncMoveButtons( $wrap );
		initDragAndDrop( $wrap );

		// Update toggle label and save button state on input.
		$wrap.on( 'input', '.trove-cz-repeater-kv__input-key, .trove-cz-repeater-kv__input-value', function () {
			var $row = $( this ).closest( '.trove-cz-repeater-kv__row' );
			updateToggleLabel( $row );
			updateRowSaveState( $row );
		} );

		// Toggle collapse/expand.
		$wrap.on( 'click', '.trove-cz-repeater-kv__toggle', function ( e ) {
			e.preventDefault();
			var $toggle = $( this );
			var $row = $toggle.closest( '.trove-cz-repeater-kv__row' );
			var expanded = $toggle.attr( 'aria-expanded' ) === 'true';
			$toggle.attr( 'aria-expanded', ! expanded );
			$row.toggleClass( 'is-collapsed', expanded );
		} );

		// Add new row.
		$wrap.on( 'click', '.trove-cz-repeater-kv__add', function ( e ) {
			e.preventDefault();
			var max = parseInt( $wrap.data( 'max' ), 10 ) || 10;
			var n = $wrap.find( '.trove-cz-repeater-kv__row' ).length;
			if ( n >= max ) {
				return;
			}
			var $list = $wrap.find( '.trove-cz-repeater-kv__list' );
			appendRow( $list, null );
			syncAddButtonState( $wrap );
			syncMoveButtons( $wrap );
			var $lastRow = $list.find( '.trove-cz-repeater-kv__row' ).last();
			// Newly added row is expanded for editing.
			$lastRow.find( '.trove-cz-repeater-kv__toggle' ).attr( 'aria-expanded', 'true' );
			$lastRow.removeClass( 'is-collapsed' );
			$lastRow.find( '.trove-cz-repeater-kv__input-key' ).trigger( 'focus' );
			updateRowSaveState( $lastRow );
		} );

		// Remove row.
		$wrap.on( 'click', '.trove-cz-repeater-kv__remove', function ( e ) {
			e.preventDefault();
			$( this ).closest( '.trove-cz-repeater-kv__row' ).remove();
			syncAddButtonState( $wrap );
			syncMoveButtons( $wrap );
			var json = serializeFromInputs( $wrap );
			$wrap.data( 'troveRepeaterKvSaved', json );
			setHiddenAndNotify( $wrap, json );
			var $firstRow = $wrap.find( '.trove-cz-repeater-kv__row' ).first();
			if ( $firstRow.length ) {
				updateRowSaveState( $firstRow );
			}
		} );

		// Move up.
		$wrap.on( 'click', '.trove-cz-repeater-kv__move-up', function ( e ) {
			e.preventDefault();
			var $row = $( this ).closest( '.trove-cz-repeater-kv__row' );
			var $prev = $row.prev( '.trove-cz-repeater-kv__row' );
			if ( $prev.length ) {
				$row.insertBefore( $prev );
				syncMoveButtons( $wrap );
				var json = serializeFromInputs( $wrap );
				$wrap.data( 'troveRepeaterKvSaved', json );
				setHiddenAndNotify( $wrap, json );
				$wrap.find( '.trove-cz-repeater-kv__row' ).each( function () {
					updateRowSaveState( $( this ) );
				} );
			}
		} );

		// Move down.
		$wrap.on( 'click', '.trove-cz-repeater-kv__move-down', function ( e ) {
			e.preventDefault();
			var $row = $( this ).closest( '.trove-cz-repeater-kv__row' );
			var $next = $row.next( '.trove-cz-repeater-kv__row' );
			if ( $next.length ) {
				$row.insertAfter( $next );
				syncMoveButtons( $wrap );
				var json = serializeFromInputs( $wrap );
				$wrap.data( 'troveRepeaterKvSaved', json );
				setHiddenAndNotify( $wrap, json );
				$wrap.find( '.trove-cz-repeater-kv__row' ).each( function () {
					updateRowSaveState( $( this ) );
				} );
			}
		} );

		// Save button: commit to hidden input, trigger preview refresh, and collapse the row.
		$wrap.on( 'click', '.trove-cz-repeater-kv__save', function ( e ) {
			e.preventDefault();
			var json = serializeFromInputs( $wrap );
			$wrap.data( 'troveRepeaterKvSaved', json );
			setHiddenAndNotify( $wrap, json );
			var $row = $( this ).closest( '.trove-cz-repeater-kv__row' );
			$row.addClass( 'is-collapsed' );
			$row.find( '.trove-cz-repeater-kv__toggle' ).attr( 'aria-expanded', 'false' );
			$wrap.find( '.trove-cz-repeater-kv__row' ).each( function () {
				updateRowSaveState( $( this ) );
			} );
		} );

		if ( typeof wp !== 'undefined' && wp.customize && settingId ) {
			wp.customize( settingId, function ( value ) {
				value.bind( function ( newVal ) {
					$wrap.data( 'troveRepeaterKvSaved', newVal || '[]' );
					// Preserve collapse state of existing rows before rebuild.
					var collapseState = {};
					$wrap.find( '.trove-cz-repeater-kv__row' ).each( function () {
						var $r = $( this );
						var title = String( $r.find( '.trove-cz-repeater-kv__input-key' ).val() || '' ).trim();
						var content = String( $r.find( '.trove-cz-repeater-kv__input-value' ).val() || '' ).trim();
						if ( title || content ) {
							collapseState[ title + '::' + content ] = $r.hasClass( 'is-collapsed' );
						}
					} );
					rebuildRows( $wrap, parseRows( newVal ) );
					syncMoveButtons( $wrap );
					$wrap.find( '.trove-cz-repeater-kv__row' ).each( function () {
						var $r = $( this );
						var title = String( $r.find( '.trove-cz-repeater-kv__input-key' ).val() || '' ).trim();
						var content = String( $r.find( '.trove-cz-repeater-kv__input-value' ).val() || '' ).trim();
						var key = title + '::' + content;
						if ( collapseState.hasOwnProperty( key ) ) {
							$r.toggleClass( 'is-collapsed', collapseState[ key ] );
							$r.find( '.trove-cz-repeater-kv__toggle' ).attr( 'aria-expanded', ! collapseState[ key ] );
						}
						updateRowSaveState( $r );
					} );
				} );
			} );
		}
	}

	function scan() {
		$( '#customize-controls .trove-cz-repeater-kv' ).each( function () {
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
