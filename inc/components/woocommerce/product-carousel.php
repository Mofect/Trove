<?php
/**
 * Shared WooCommerce product loop carousel (related, upsells, cross-sells).
 *
 * @package Trove
 */

namespace Trove;

defined( 'ABSPATH' ) || exit;

/**
 * Products visible in the carousel viewport.
 *
 * @param string $context related|upsells|cross-sells.
 * @return int
 */
function woo_product_carousel_visible( $context = 'related' ) {
	$defaults = array(
		'related'     => 4,
		'upsells'     => 4,
		'cross-sells' => (int) woo_cart_cross_sells_visible(),
	);

	$default = $defaults[ $context ] ?? 4;
	$visible = (int) apply_filters( 'trove_woo_product_carousel_visible', $default, $context );

	return max( 1, min( 6, $visible ) );
}

/**
 * Max products in the carousel track.
 *
 * @param string $context related|upsells|cross-sells.
 * @return int
 */
function woo_product_carousel_total( $context = 'related' ) {
	$defaults = array(
		'related'     => 12,
		'upsells'     => 12,
		'cross-sells' => (int) woo_cart_cross_sells_total(),
	);

	$default = $defaults[ $context ] ?? 12;
	$total   = (int) apply_filters( 'trove_woo_product_carousel_total', $default, $context );

	return max( 2, $total );
}

/**
 * @param \WC_Product[]     $products       Products to render.
 * @param string            $heading        Section heading (empty to hide).
 * @param string            $section_class  Section wrapper classes.
 * @param string            $context        Carousel context key.
 * @return string
 */
function render_woo_product_carousel_html( $products, $heading, $section_class, $context = 'related' ) {
	if ( empty( $products ) ) {
		return '';
	}

	$visible = woo_product_carousel_visible( $context );

	ob_start();
	wc_get_template(
		'global/product-carousel.php',
		array(
			'products'       => $products,
			'heading'        => $heading,
			'section_class'  => $section_class,
			'visible'        => $visible,
			'carousel_context' => $context,
		)
	);
	return (string) ob_get_clean();
}

/**
 * @param int $columns Column count.
 * @return int
 */
function filter_upsells_carousel_columns( $columns ) {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return $columns;
	}
	return woo_product_carousel_visible( 'upsells' );
}
add_filter( 'woocommerce_upsells_columns', __NAMESPACE__ . '\\filter_upsells_carousel_columns' );

/**
 * @param int $limit Product limit.
 * @return int
 */
function filter_upsells_carousel_total( $limit ) {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return $limit;
	}
	return woo_product_carousel_total( 'upsells' );
}
add_filter( 'woocommerce_upsells_total', __NAMESPACE__ . '\\filter_upsells_carousel_total' );
