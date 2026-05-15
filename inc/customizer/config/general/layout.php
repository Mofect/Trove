<?php
/**
 * Customizer fields: Layout subsection (under General panel).
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

use function \Trove\Customizer\customizer_default;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_content_wide_width' => array(
		'label'             => __( 'Wide Content width', 'trove' ),
		'description'       => __( 'Max width for wide layouts (header, footer, blog grid/masonry, announcement bar).', 'trove' ),
		'type'              => 'range_number',
		'transport'         => 'postMessage',
		'unit'              => 'px',
		'default'           => (int) customizer_default( 'trove_content_wide_width', 1200 ),
		'input_attrs'       => array(
			'min'  => 800,
			'max'  => 1900,
			'step' => 1,
		),
		'sanitize_callback' => 'absint',
	),
	'trove_content_width' => array(
		'label'             => __( 'Narrow Content width', 'trove' ),
		'description'       => __( 'Max width for the main reading column (single posts, default pages).', 'trove' ),
		'type'              => 'range_number',
		'transport'         => 'postMessage',
		'unit'              => 'px',
		'default'           => (int) customizer_default( 'trove_content_width', 800 ),
		'input_attrs'       => array(
			'min'  => 600,
			'max'  => 1000,
			'step' => 1,
		),
		'sanitize_callback' => 'absint',
	),
	'trove_content_vertical_spacing' => array(
		'label'             => __( 'Content area vertical spacing', 'trove' ),
		'description'       => __( 'Top and bottom padding for the main content area.', 'trove' ),
		'type'              => 'range_number',
		'transport'         => 'postMessage',
		'unit'              => 'px',
		'default'           => (int) customizer_default( 'trove_content_vertical_spacing', 48 ),
		'input_attrs'       => array(
			'min'  => 0,
			'max'  => 200,
			'step' => 1,
		),
		'sanitize_callback' => 'absint',
	),
	'trove_content_horizontal_spacing' => array(
		'label'             => __( 'Content area horizontal spacing', 'trove' ),
		'description'       => __( 'Left and right padding for the main content area.', 'trove' ),
		'type'              => 'range_number',
		'transport'         => 'postMessage',
		'unit'              => 'px',
		'default'           => (int) customizer_default( 'trove_content_horizontal_spacing', 20 ),
		'input_attrs'       => array(
			'min'  => 0,
			'max'  => 100,
			'step' => 1,
		),
		'sanitize_callback' => 'absint',
	),
);
