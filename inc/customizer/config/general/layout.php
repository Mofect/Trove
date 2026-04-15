<?php
/**
 * Customizer fields: Layout subsection (under General panel).
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_content_width' => array(
		'label'             => __( 'Content width (px)', 'trove' ),
		'description'       => __( 'Max width for main content area. Separate values for PC, tablet, and phone.', 'trove' ),
		'type'              => 'number',
		'responsive'        => true,
		'default'           => array(
			'pc'     => 800,
			'tablet' => 720,
			'phone'  => 640,
		),
		'input_attrs'       => array(
			'min'  => 600,
			'max'  => 1400,
			'step' => 10,
		),
		'sanitize_callback' => 'absint',
	),
);
