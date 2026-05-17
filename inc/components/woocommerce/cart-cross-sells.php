<?php
/**
 * Classic cart cross-sells: carousel (2 visible) and Customizer toggle.
 *
 * @package Trove
 */

namespace Trove;

defined( 'ABSPATH' ) || exit;

/**
 * @return bool
 */
function woo_cart_enable_cross_sells() {
	return wp_validate_boolean(
		get_theme_mod(
			'trove_woo_cart_enable_cross_sells',
			(bool) \Trove\Customizer\customizer_default( 'trove_woo_cart_enable_cross_sells', true )
		)
	);
}

/**
 * Max cross-sell products loaded into the carousel track.
 *
 * @return int
 */
function woo_cart_cross_sells_total() {
	return max( 2, (int) apply_filters( 'trove_woo_cart_cross_sells_total', 8 ) );
}

/**
 * Products visible in the carousel viewport at once.
 *
 * @return int
 */
function woo_cart_cross_sells_visible() {
	$visible = (int) apply_filters( 'trove_woo_cart_cross_sells_visible', 2 );
	return max( 1, min( 4, $visible ) );
}

/**
 * @return void
 */
function control_cart_cross_sells() {
	if ( ! function_exists( 'is_cart' ) || ! is_cart() ) {
		return;
	}
	if ( woo_cart_enable_cross_sells() ) {
		return;
	}
	remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display', 10 );
}
add_action( 'wp', __NAMESPACE__ . '\\control_cart_cross_sells' );

/**
 * @param int $limit Product limit passed to display().
 * @return int
 */
function filter_cart_cross_sells_total( $limit ) {
	if ( ! function_exists( 'is_cart' ) || ! is_cart() ) {
		return $limit;
	}
	return woo_cart_cross_sells_total();
}
add_filter( 'woocommerce_cross_sells_total', __NAMESPACE__ . '\\filter_cart_cross_sells_total' );

/**
 * Columns are overridden by carousel CSS; keep Woo loop markup compatible.
 *
 * @param int $columns Column count.
 * @return int
 */
function filter_cart_cross_sells_columns( $columns ) {
	if ( ! function_exists( 'is_cart' ) || ! is_cart() ) {
		return $columns;
	}
	return woo_cart_cross_sells_visible();
}
add_filter( 'woocommerce_cross_sells_columns', __NAMESPACE__ . '\\filter_cart_cross_sells_columns' );
