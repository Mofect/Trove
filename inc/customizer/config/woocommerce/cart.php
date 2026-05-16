<?php
/**
 * Customizer fields: WooCommerce → Cart.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

use function \Trove\Customizer\customizer_default;

defined( 'ABSPATH' ) || exit;

/**
 * @param \WP_Customize_Control $control Control instance.
 * @return bool
 */
function is_woo_cart_free_shipping_bar_enabled( $control ) {
	if ( ! $control || ! isset( $control->manager ) ) {
		return false;
	}
	$setting = $control->manager->get_setting( 'trove_woo_cart_enable_free_shipping_bar' );
	if ( ! $setting ) {
		return false;
	}
	return wp_validate_boolean( $setting->value() );
}

return array(
	'trove_woo_cart_open_drawer_after_add' => array(
		'label'             => __( 'Open cart drawer automatically', 'trove' ),
		'type'              => 'switch',
		'default'           => (bool) customizer_default( 'trove_woo_cart_open_drawer_after_add', true ),
		'transport'         => 'refresh',
		'sanitize_callback' => 'wp_validate_boolean',
	),
	'trove_woo_cart_enable_order_note' => array(
		'label'             => __( 'Enable order note', 'trove' ),
		'type'              => 'switch',
		'default'           => (bool) customizer_default( 'trove_woo_cart_enable_order_note', true ),
		'transport'         => 'refresh',
		'sanitize_callback' => 'wp_validate_boolean',
	),
	'trove_woo_cart_enable_coupon_code' => array(
		'label'             => __( 'Enable coupon code', 'trove' ),
		'type'              => 'switch',
		'default'           => (bool) customizer_default( 'trove_woo_cart_enable_coupon_code', true ),
		'transport'         => 'refresh',
		'sanitize_callback' => 'wp_validate_boolean',
	),
	'trove_woo_cart_enable_cross_sells' => array(
		'label'             => __( 'Enable cross-sells', 'trove' ),
		'type'              => 'switch',
		'default'           => (bool) customizer_default( 'trove_woo_cart_enable_cross_sells', true ),
		'transport'         => 'refresh',
		'sanitize_callback' => 'wp_validate_boolean',
	),
	'trove_woo_cart_enable_shipping_calculator' => array(
		'label'             => __( 'Enable shipping calculator', 'trove' ),
		'type'              => 'switch',
		'default'           => (bool) customizer_default( 'trove_woo_cart_enable_shipping_calculator', true ),
		'transport'         => 'refresh',
		'sanitize_callback' => 'wp_validate_boolean',
	),
	'trove_woo_cart_free_shipping_heading' => array(
		'type'     => 'heading',
		'label'    => __( 'Free shipping', 'trove' ),
		'priority' => 50,
	),
	'trove_woo_cart_enable_free_shipping_bar' => array(
		'label'             => __( 'Enable free shipping bar', 'trove' ),
		'type'              => 'switch',
		'default'           => (bool) customizer_default( 'trove_woo_cart_enable_free_shipping_bar', false ),
		'transport'         => 'refresh',
		'sanitize_callback' => 'wp_validate_boolean',
		'priority'          => 51,
	),
	'trove_woo_cart_free_shipping_threshold' => array(
		'label'             => __( 'Free shipping threshold', 'trove' ),
		'description'       => __( 'Order subtotal required to qualify for free shipping.', 'trove' ),
		'type'              => 'range_number',
		'default'           => (float) customizer_default( 'trove_woo_cart_free_shipping_threshold', 100 ),
		'transport'         => 'refresh',
		'active_callback'   => __NAMESPACE__ . '\\is_woo_cart_free_shipping_bar_enabled',
		'input_attrs'       => array(
			'min'  => 1,
			'max'  => 10000,
			'step' => 1,
		),
		'sanitize_callback' => function ( $value ) {
			$value = is_numeric( $value ) ? (float) $value : 0;
			return max( 0, $value );
		},
		'priority'          => 52,
	),
	'trove_woo_cart_free_shipping_bar_color' => array(
		'label'             => __( 'Free shipping bar color', 'trove' ),
		'type'              => 'color',
		'default'           => customizer_default( 'trove_woo_cart_free_shipping_bar_color', '#22c55e' ),
		'transport'         => 'postMessage',
		'active_callback'   => __NAMESPACE__ . '\\is_woo_cart_free_shipping_bar_enabled',
		'sanitize_callback' => 'sanitize_hex_color',
		'priority'          => 53,
	),
);
