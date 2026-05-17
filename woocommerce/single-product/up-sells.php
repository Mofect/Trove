<?php
/**
 * Single Product Up-Sells — 4-column carousel.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.6.0
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $upsells ) ) {
	return;
}

$heading = apply_filters( 'woocommerce_product_upsells_products_heading', __( 'You may also like&hellip;', 'woocommerce' ) );

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in template.
echo \Trove\render_woo_product_carousel_html(
	$upsells,
	is_string( $heading ) ? $heading : '',
	'up-sells upsells products',
	'upsells'
);
