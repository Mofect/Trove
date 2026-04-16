/**
 * Header -> Layout: show Menu alignment only for horizontal left/right layouts.
 */
( function ( wp ) {
	'use strict';

	function shouldShow( layout ) {
		var v = layout ? String( layout ).trim() : '';
		return v === 'horizontal-left-logo' || v === 'horizontal-right-logo';
	}

	function setControlVisibility( visible ) {
		wp.customize.control( 'trove_header_menu_alignment', function ( control ) {
			control.container.toggle( !! visible );
		} );
	}

	wp.customize.bind( 'ready', function () {
		wp.customize( 'trove_header_layout', function ( layoutValue ) {
			function update( newVal ) {
				setControlVisibility( shouldShow( newVal ) );
			}
			layoutValue.bind( update );
			update( layoutValue.get() );
		} );
	} );
} )( wp );
