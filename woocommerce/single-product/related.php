<?php
/**
 * Related Products — 4-column carousel.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.3.0
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $related_products ) ) {
	return;
}

if ( function_exists( 'wp_increase_content_media_count' ) ) {
	$content_media_count = wp_increase_content_media_count( 0 );
	if ( $content_media_count < wp_omit_loading_attr_threshold() ) {
		wp_increase_content_media_count( wp_omit_loading_attr_threshold() - $content_media_count );
	}
}

$heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'Related products', 'woocommerce' ) );

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in template.
echo \Trove\render_woo_product_carousel_html(
	$related_products,
	is_string( $heading ) ? $heading : '',
	'related products',
	'related'
);
