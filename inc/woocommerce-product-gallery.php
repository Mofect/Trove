<?php
/**
 * WooCommerce single-product gallery: Customizer “Gallery type” (stacked vs slider).
 *
 * @package Trove
 */

namespace Trove;

defined( 'ABSPATH' ) || exit;

/**
 * Gallery mode: `stacked` (images in a column, no Flexslider) or `slider` (WooCommerce carousel + thumbnails).
 *
 * @return string `stacked`|`slider`
 */
function get_woo_single_product_gallery_type() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return 'stacked';
	}
	$def = (string) \Trove\Customizer\trove_customizer_default( 'trove_woo_single_product_gallery_type', 'stacked' );
	$v   = sanitize_key( (string) get_theme_mod( 'trove_woo_single_product_gallery_type', $def ) );

	return 'slider' === $v ? 'slider' : 'stacked';
}

/**
 * Column count for stacked gallery (1–6). Used from the second image onward; ignored when gallery type is Slider.
 *
 * @return int
 */
function get_woo_single_product_gallery_stacked_columns() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return 4;
	}
	$def = (string) \Trove\Customizer\trove_customizer_default( 'trove_woo_single_product_gallery_stacked_columns', '4' );
	$n   = (int) get_theme_mod( 'trove_woo_single_product_gallery_stacked_columns', $def );
	if ( $n < 1 ) {
		$n = 1;
	}
	if ( $n > 6 ) {
		$n = 6;
	}
	return $n;
}

/**
 * Thumbnail rail position for slider gallery.
 *
 * @return string `left`|`right`|`bottom`
 */
function get_woo_single_product_gallery_slider_position() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return 'bottom';
	}
	$def = (string) \Trove\Customizer\trove_customizer_default( 'trove_woo_single_product_gallery_slider_position', 'bottom' );
	$v   = sanitize_key( (string) get_theme_mod( 'trove_woo_single_product_gallery_slider_position', $def ) );
	if ( 'left' !== $v && 'right' !== $v && 'bottom' !== $v ) {
		$v = 'bottom';
	}
	return $v;
}

/**
 * Declares Flexslider support only when “Slider” is selected (enqueues wc-flexslider + enables carousel).
 *
 * Runs on `wp_loaded` (after Customizer preview filters) but before `wp` / enqueue, so WooCommerce reads correct support.
 *
 * @return void
 */
function register_woocommerce_single_product_gallery_theme_support() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}
	if ( 'slider' === get_woo_single_product_gallery_type() ) {
		add_theme_support( 'wc-product-gallery-slider' );
	}
}

/**
 * With Flexslider off, Woo uses thumbnail size for extra images; use single size for stacked layout.
 *
 * @param string|int[] $size Image size.
 * @return string|int[]
 */
function filter_woocommerce_gallery_image_size_stacked( $size ) {
	return 'stacked' === get_woo_single_product_gallery_type() ? 'woocommerce_single' : $size;
}

/**
 * @param string[]|mixed $classes Gallery wrapper classes.
 * @return string[]
 */
function filter_woocommerce_single_product_image_gallery_classes_stacked( $classes ) {
	if ( ! is_array( $classes ) ) {
		$classes = array();
	}
	if ( 'stacked' === get_woo_single_product_gallery_type() ) {
		$classes[] = 'trove-woo-gallery--stacked';
		$cols      = get_woo_single_product_gallery_stacked_columns();
		$classes[] = 'trove-woo-gallery--stacked-cols-' . $cols;
	} else {
		$classes[] = 'trove-woo-gallery--slider';
		$classes[] = 'trove-woo-slider-pos-' . get_woo_single_product_gallery_slider_position();
	}
	return $classes;
}

add_action( 'wp_loaded', __NAMESPACE__ . '\\register_woocommerce_single_product_gallery_theme_support', 20 );
add_filter( 'woocommerce_gallery_image_size', __NAMESPACE__ . '\\filter_woocommerce_gallery_image_size_stacked' );
add_filter( 'woocommerce_single_product_image_gallery_classes', __NAMESPACE__ . '\\filter_woocommerce_single_product_image_gallery_classes_stacked' );
