<?php
/**
 * Customizer fields: Blog Posts → Styles.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

use function \Trove\Customizer\customizer_default;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_blog_boxed_style' => array(
		'label'             => __( 'Boxed style', 'trove' ),
		'description'       => __( 'Adds an outer border around each post in blog listings.', 'trove' ),
		'type'              => 'switch',
		'default'           => (bool) customizer_default( 'trove_blog_boxed_style', false ),
		'transport'         => 'postMessage',
		'sanitize_callback' => 'wp_validate_boolean',
	),
	'trove_blog_boxed_border_radius' => array(
		'label'             => __( 'Border corner radius', 'trove' ),
		'description'       => __( 'Corner radius for boxed posts (0–20px). Applies when Boxed style is on.', 'trove' ),
		'type'              => 'range_number',
		'transport'         => 'postMessage',
		'unit'              => 'px',
		'default'           => (int) customizer_default( 'trove_blog_boxed_border_radius', 10 ),
		'input_attrs'       => array(
			'min'  => 0,
			'max'  => 20,
			'step' => 1,
		),
		'sanitize_callback' => 'absint',
	),
);
