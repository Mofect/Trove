/**
 * Tabbed panels inside Trove metaboxes ([data-trove-mb-tabs]).
 *
 * wpColorPicker / popover scripts run after this file and can alter the DOM so inactive
 * panels lose their initial display:none — re-sync visibility after they finish.
 */
( function ( $ ) {
	'use strict';

	function applyActiveTab( $root, tabId ) {
		if ( ! tabId ) {
			return;
		}
		$root.find( '.trove-mb-tabs__btn' ).removeClass( 'is-active' ).attr( 'aria-selected', 'false' );
		$root.find( '.trove-mb-tab-panel' ).removeClass( 'is-active' ).attr( 'style', 'display:none !important' );
		var $btn = $root.find( '.trove-mb-tabs__btn[data-trove-mb-tab="' + tabId + '"]' ).first();
		if ( $btn.length ) {
			$btn.addClass( 'is-active' ).attr( 'aria-selected', 'true' );
		}
		$root.find( '[data-trove-mb-tab-panel="' + tabId + '"]' ).addClass( 'is-active' ).removeAttr( 'style' );
		$( document ).trigger( 'trove:metaboxConditionalSync' );
	}

	function syncOneRoot( $root ) {
		var $activeBtn = $root.find( '.trove-mb-tabs__btn.is-active' ).first();
		if ( ! $activeBtn.length ) {
			$activeBtn = $root.find( '.trove-mb-tabs__btn' ).first();
		}
		var tabId = $activeBtn.attr( 'data-trove-mb-tab' );
		applyActiveTab( $root, tabId );
	}

	function syncAllTabRoots() {
		$( '[data-trove-mb-tabs]' ).each( function () {
			syncOneRoot( $( this ) );
		} );
	}

	var resyncTimer = null;
	function scheduleSyncAllTabRoots() {
		if ( resyncTimer ) {
			window.clearTimeout( resyncTimer );
		}
		resyncTimer = window.setTimeout( function () {
			resyncTimer = null;
			syncAllTabRoots();
		}, 10 );
	}

	$( document ).on( 'click', '[data-trove-mb-tabs] .trove-mb-tabs__btn', function ( e ) {
		e.preventDefault();
		var $btn = $( this );
		var tabId = $btn.attr( 'data-trove-mb-tab' );
		if ( ! tabId ) {
			return;
		}
		var $root = $btn.closest( '[data-trove-mb-tabs]' );
		applyActiveTab( $root, tabId );
	} );

	$( function () {
		syncAllTabRoots();
		[ 0, 50, 100, 200, 400, 800, 1500, 2200, 3500 ].forEach( function ( ms ) {
			window.setTimeout( syncAllTabRoots, ms );
		} );
	} );

	$( document ).on( 'trove:metaboxColorPickerReady', function () {
		scheduleSyncAllTabRoots();
	} );
}( jQuery ) );
