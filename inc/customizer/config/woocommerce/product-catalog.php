<?php
/**
 * Customizer fields: WooCommerce → Product catalog.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

use function \Trove\Customizer\customizer_default;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_woo_enable_sidebar' => array(
		'label'             => __( 'Enable Sidebar', 'trove' ),
		'type'              => 'switch',
		'default'           => (bool) customizer_default( 'trove_woo_enable_sidebar', true ),
		'transport'         => 'refresh',
		'sanitize_callback' => 'wp_validate_boolean',
	),
	'trove_woo_show_second_photo_on_hover' => array(
		'label'             => __( 'Show the second photo on hover', 'trove' ),
		'type'              => 'switch',
		'default'           => (bool) customizer_default( 'trove_woo_show_second_photo_on_hover', true ),
		'transport'         => 'refresh',
		'sanitize_callback' => 'wp_validate_boolean',
	),
);
