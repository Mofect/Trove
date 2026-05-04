<?php
/**
 * Customizer fields: Performance section.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

use function \Trove\Customizer\trove_customizer_default;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_perf_minify_assets' => array(
		'label'             => __( 'Load minified theme CSS/JS', 'trove' ),
		'type'              => 'switch',
		'default'           => (bool) trove_customizer_default( 'trove_perf_minify_assets', true ),
		'transport'         => 'postMessage',
		'sanitize_callback' => 'wp_validate_boolean',
	),
	'trove_perf_lazy_load_images' => array(
		'label'             => __( 'Lazy load images', 'trove' ),
		'description'       => __( 'Enable lazy loading for post and product images.', 'trove' ),
		'type'              => 'switch',
		'default'           => (bool) trove_customizer_default( 'trove_perf_lazy_load_images', true ),
		'transport'         => 'refresh',
		'sanitize_callback' => 'wp_validate_boolean',
	),
);
