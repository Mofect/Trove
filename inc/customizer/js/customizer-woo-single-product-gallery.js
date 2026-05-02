/**
 * Single product: gallery control visibility + preview URL when section opens.
 */
( function ( wp ) {
	'use strict';

	var TYPE = 'trove_woo_single_product_gallery_type';
	var STACKED_COLS = 'trove_woo_single_product_gallery_stacked_columns';
	var SLIDER_POS = 'trove_woo_single_product_gallery_slider_position';
	var SINGLE_PRODUCT_SECTION = 'trove_section_woocommerce_single_product';
	var PRODUCT_CATALOG_SECTION = 'trove_section_woocommerce_product_catalog';

	function toggleControl( controlId, visible ) {
		wp.customize.control( controlId, function ( control ) {
			control.container.toggle( !! visible );
		} );
	}

	function syncPanels( galleryType ) {
		var v = galleryType ? String( galleryType ).trim() : '';
		toggleControl( STACKED_COLS, 'stacked' === v );
		toggleControl( SLIDER_POS, 'slider' === v );
	}

	wp.customize.bind( 'ready', function () {
		wp.customize( TYPE, function ( setting ) {
			function update( val ) {
				syncPanels( val );
			}
			setting.bind( update );
			update( setting.get() );
		} );

		var data = window.troveWooSingleProductCz || {};
		var singleUrl = data.previewProductUrl ? String( data.previewProductUrl ).trim() : '';
		var catalogUrl = data.previewCatalogUrl ? String( data.previewCatalogUrl ).trim() : '';
		if ( singleUrl ) {
			wp.customize.section( SINGLE_PRODUCT_SECTION, function ( section ) {
				section.expanded.bind( function ( isExpanded ) {
					if ( ! isExpanded ) {
						return;
					}
					if ( wp.customize.previewer && wp.customize.previewer.previewUrl ) {
						wp.customize.previewer.previewUrl.set( singleUrl );
					}
				} );
			} );
		}
		if ( catalogUrl ) {
			wp.customize.section( PRODUCT_CATALOG_SECTION, function ( section ) {
				section.expanded.bind( function ( isExpanded ) {
					if ( ! isExpanded ) {
						return;
					}
					if ( wp.customize.previewer && wp.customize.previewer.previewUrl ) {
						wp.customize.previewer.previewUrl.set( catalogUrl );
					}
				} );
			} );
		}
	} );
} )( wp );
