<?php
/**
 * Customizer fields: Typography section (root-level).
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_font_base_stack' => array(
		'label'             => __( 'Base font stack', 'trove' ),
		'type'              => 'select',
		'default'           => 'inter',
		'choices'           => array(
			'inter'  => __( 'Inter (theme)', 'trove' ),
			'system' => __( 'System UI', 'trove' ),
		),
		'sanitize_callback' => __NAMESPACE__ . '\\sanitize_font_stack',
	),
);
