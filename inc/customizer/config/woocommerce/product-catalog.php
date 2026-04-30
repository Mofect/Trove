<?php
/**
 * Customizer fields: WooCommerce → Product catalog.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

use function \Trove\Customizer\trove_customizer_default;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_woo_enable_filter' => array(
		'label'             => __( 'Enable Filter Sidebar', 'trove' ),
		'type'              => 'switch',
		'default'           => (bool) trove_customizer_default( 'trove_woo_enable_filter', true ),
		'transport'         => 'refresh',
		'sanitize_callback' => 'wp_validate_boolean',
	),
);
