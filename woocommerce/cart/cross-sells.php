<?php
/**
 * Cross-sells carousel (classic cart).
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.6.0
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $cross_sells ) ) {
	return;
}

$heading = apply_filters( 'woocommerce_product_cross_sells_products_heading', __( 'You may be interested in&hellip;', 'woocommerce' ) );

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in template.
echo \Trove\render_woo_product_carousel_html(
	$cross_sells,
	is_string( $heading ) ? $heading : '',
	'cross-sells trove-cart-cross-sells',
	'cross-sells'
);
