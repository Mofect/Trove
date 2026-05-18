<?php
/**
 * Shared WooCommerce product loop carousel (related, upsells, cross-sells).
 *
 * @package Trove
 */

namespace Trove;

defined( 'ABSPATH' ) || exit;

/**
 * @return bool
 */
function is_rendering_product_carousel() {
	return ! empty( $GLOBALS['trove_rendering_product_carousel'] );
}

/**
 * @return bool
 */
function should_load_product_carousel_assets() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return false;
	}
	if ( function_exists( 'is_product' ) && is_product() ) {
		return true;
	}
	if ( function_exists( 'is_cart' ) && is_cart() && woo_cart_enable_cross_sells() ) {
		return true;
	}
	return false;
}

/**
 * @return void
 */
function enqueue_product_carousel_assets() {
	if ( ! should_load_product_carousel_assets() ) {
		return;
	}

	$swiper_version = '11.2.10';

	wp_enqueue_style(
		'swiper',
		\Trove\TroveTheme::asset_url( 'assets/vendor/swiper/swiper-bundle.min.css' ),
		array(),
		$swiper_version
	);

	wp_enqueue_script(
		'swiper',
		\Trove\TroveTheme::asset_url( 'assets/vendor/swiper/swiper-bundle.min.js' ),
		array(),
		$swiper_version,
		true
	);

	wp_enqueue_script(
		'trove-product-carousel',
		\Trove\TroveTheme::asset_url( 'assets/js/product-carousel.js' ),
		array( 'swiper' ),
		TROVE_THEME_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_product_carousel_assets', 25 );

/**
 * @param string $html Loop start markup.
 * @return string
 */
function filter_product_loop_start_swiper( $html ) {
	if ( ! is_rendering_product_carousel() ) {
		return $html;
	}
	if ( false !== strpos( $html, 'swiper-wrapper' ) ) {
		return $html;
	}
	return (string) preg_replace(
		'/class="([^"]*\bproducts\b[^"]*)"/',
		'class="$1 swiper-wrapper"',
		$html,
		1
	);
}
add_filter( 'woocommerce_product_loop_start', __NAMESPACE__ . '\\filter_product_loop_start_swiper', 20 );

/**
 * @param string[]    $classes Product post classes.
 * @param \WC_Product $product Product object.
 * @return string[]
 */
function filter_product_carousel_slide_class( $classes, $product ) {
	unset( $product );
	if ( is_rendering_product_carousel() ) {
		$classes[] = 'swiper-slide';
	}
	return $classes;
}
add_filter( 'woocommerce_post_class', __NAMESPACE__ . '\\filter_product_carousel_slide_class', 20, 2 );

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
