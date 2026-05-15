<?php
/**
 * The Template for displaying product archives, including the main shop page.
 *
 * @package Trove
 */

namespace Trove;

defined( 'ABSPATH' ) || exit;

$show_sidebar = is_woo_shop_filter_sidebar_enabled();

get_header( 'shop' );

// Move breadcrumb above the .woocommerce wrapper so it sits outside the main content area.
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
if ( function_exists( 'woocommerce_breadcrumb' ) ) {
	woocommerce_breadcrumb();
}

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action( 'woocommerce_before_main_content' );

/**
 * Hook: woocommerce_shop_loop_header.
 *
 * @since 8.6.0
 *
 * @hooked woocommerce_product_taxonomy_archive_header - 10
 */
do_action( 'woocommerce_shop_loop_header' );

if ( woocommerce_product_loop() ) {

	/**
	 * Hook: woocommerce_before_shop_loop.
	 *
	 * @hooked woocommerce_output_all_notices - 10
	 * @hooked woocommerce_result_count - 20
	 * @hooked woocommerce_catalog_ordering - 30
	 */
	do_action( 'woocommerce_before_shop_loop' );

	if ( $show_sidebar ) {
		echo '<div class="shop-main">';
		if ( is_active_sidebar( 'woocommerce-sidebar' ) ) {
			/**
			 * Hook: woocommerce_sidebar.
			 *
			 * @hooked woocommerce_get_sidebar - 10
			 */
			do_action( 'woocommerce_sidebar' );
		} else {
			echo '<aside class="widget-area widget-area--woocommerce-sidebar trove-shop-filters" role="complementary" aria-label="' . esc_attr__( 'Shop filters', 'trove' ) . '">';
			if ( class_exists( 'WC_Widget_Price_Filter' ) ) {
				the_widget(
					'WC_Widget_Price_Filter',
					array( 'title' => __( 'Filter by price', 'trove' ) ),
					array(
						'before_widget' => '<div class="widget trove-shop-widget trove-shop-widget--price">',
						'after_widget'  => '</div>',
						'before_title'  => '<h3 class="widget-title">',
						'after_title'   => '</h3>',
					)
				);
			}
			if ( class_exists( 'WC_Widget_Product_Categories' ) ) {
				the_widget(
					'WC_Widget_Product_Categories',
					array( 'title' => __( 'Categories', 'trove' ) ),
					array(
						'before_widget' => '<div class="widget trove-shop-widget trove-shop-widget--categories">',
						'after_widget'  => '</div>',
						'before_title'  => '<h3 class="widget-title">',
						'after_title'   => '</h3>',
					)
				);
			}
		    echo '</aside>';
		}
		echo '<div class="trove-products">';
	}

	woocommerce_product_loop_start();

	if ( wc_get_loop_prop( 'total' ) ) {
		while ( have_posts() ) {
			the_post();

			/**
			 * Hook: woocommerce_shop_loop.
			 */
			do_action( 'woocommerce_shop_loop' );

			wc_get_template_part( 'content', 'product' );
		}
	}

	woocommerce_product_loop_end();

	/**
	 * Hook: woocommerce_after_shop_loop.
	 *
	 * @hooked woocommerce_pagination - 10
	 */
	do_action( 'woocommerce_after_shop_loop' );

	if ( $show_sidebar ) {
		echo '</div>'; // close .trove-products
		echo '</div>'; // close .shop-main
	}
} else {
	/**
	 * Hook: woocommerce_no_products_found.
	 *
	 * @hooked wc_no_products_found - 10
	 */
	do_action( 'woocommerce_no_products_found' );
}

/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );
