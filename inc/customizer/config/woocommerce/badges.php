<?php
/**
 * Customizer fields: WooCommerce → Badge.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

use function \Trove\Customizer\customizer_default;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_woo_sale_badge_color' => array(
		'label'             => __( 'Sale badge color', 'trove' ),
		'type'              => 'color',
		'default'           => customizer_default( 'trove_woo_sale_badge_color', '#ff0000' ),
		'priority'          => 1,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Sale', 'trove' ),
	),
	'trove_woo_featured_badge_color' => array(
		'label'             => __( 'Featured badge color', 'trove' ),
		'type'              => 'color',
		'default'           => customizer_default( 'trove_woo_featured_badge_color', '#22c55e' ),
		'priority'          => 2,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Featured', 'trove' ),
	),
	'trove_woo_new_badge_color' => array(
		'label'             => __( 'New badge color', 'trove' ),
		'type'              => 'color',
		'default'           => customizer_default( 'trove_woo_new_badge_color', '#000000' ),
		'priority'          => 3,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'New', 'trove' ),
	),
	'trove_woo_out_of_stock_badge_color' => array(
		'label'             => __( 'Out of stock badge color', 'trove' ),
		'type'              => 'color',
		'default'           => customizer_default( 'trove_woo_out_of_stock_badge_color', '#9ca3af' ),
		'priority'          => 4,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Out of stock', 'trove' ),
	),
);
