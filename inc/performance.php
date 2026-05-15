<?php
/**
 * Performance-related theme hooks (e.g. image lazy loading).
 *
 * @package Trove
 */

namespace Trove;

defined( 'ABSPATH' ) || exit;

/**
 * Check whether lazy loading for images is enabled.
 *
 * @return bool
 */
function is_lazy_load_images_enabled() {
	return wp_validate_boolean(
		get_theme_mod(
			'trove_perf_lazy_load_images',
			(bool) \Trove\Customizer\customizer_default( 'trove_perf_lazy_load_images', true )
		)
	);
}

/**
 * Force lazy loading on all images by injecting loading="lazy" into <img> tags.
 *
 * @param string $html Image HTML.
 * @return string
 */
function filter_force_lazy_loading_on_images( $html ) {
	if ( ! is_lazy_load_images_enabled() ) {
		return $html;
	}
	if ( ! is_string( $html ) || false === strpos( $html, '<img' ) ) {
		return $html;
	}
	// Add or replace loading attribute with "lazy" on all img tags.
	$html = preg_replace( '/<img([^>]*?)\s+loading=["\'][^"\']*["\']([^>]*?)>/i', '<img\1 loading="lazy"\2>', $html );
	$html = preg_replace( '/<img((?![^>]*\sloading=)[^>]*)>/i', '<img\1 loading="lazy">', $html );
	return $html;
}
add_filter( 'wp_get_attachment_image', __NAMESPACE__ . '\\filter_force_lazy_loading_on_images', 999 );
add_filter( 'the_content', __NAMESPACE__ . '\\filter_force_lazy_loading_on_images', 999 );
add_filter( 'post_thumbnail_html', __NAMESPACE__ . '\\filter_force_lazy_loading_on_images', 999 );
add_filter( 'woocommerce_product_get_image', __NAMESPACE__ . '\\filter_force_lazy_loading_on_images', 999 );
