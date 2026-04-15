<?php
/**
 * Customizer fields: Blog Posts section.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_excerpt_length' => array(
		'label'             => __( 'Excerpt word count', 'trove' ),
		'type'              => 'number',
		'default'           => 40,
		'input_attrs'       => array(
			'min'  => 10,
			'max'  => 200,
			'step' => 1,
		),
		'sanitize_callback' => 'absint',
	),
);
