<?php
/**
 * The Template for displaying all single products.
 *
 * @package Trove
 */

namespace Trove;

defined( 'ABSPATH' ) || exit;

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
 */
do_action( 'woocommerce_before_main_content' );

while ( have_posts() ) :
	the_post();

	wc_get_template_part( 'content', 'single-product' );

endwhile; // end of the loop.

/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );

/**
 * Hook: woocommerce_sidebar.
 *
 * @hooked woocommerce_get_sidebar - 10
 */
do_action( 'woocommerce_sidebar' );

get_footer( 'shop' );
