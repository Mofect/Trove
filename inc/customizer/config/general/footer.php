<?php
/**
 * Customizer fields: Footer section.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_footer_layout' => array(
		'label'             => __( 'Footer layout', 'trove' ),
		'description'       => __( 'Choose one of the built-in footer templates.', 'trove' ),
		'type'              => 'select',
		'default'           => 'footer-1',
		'choices'           => array(
			'footer-1' => __( 'Footer 1', 'trove' ),
			'footer-2' => __( 'Footer 2', 'trove' ),
			'footer-3' => __( 'Footer 3', 'trove' ),
			'footer-4' => __( 'Footer 4', 'trove' ),
		),
		'sanitize_callback' => 'sanitize_text_field',
	),
	'trove_footer_credit_text' => array(
		'label'             => __( 'Extra footer line (optional)', 'trove' ),
		'description'       => __( 'Shown in the selected footer layout when not empty.', 'trove' ),
		'type'              => 'text',
		'responsive'        => true,
		'default'           => array(
			'pc'     => '',
			'tablet' => '',
			'phone'  => '',
		),
		'sanitize_callback' => 'sanitize_text_field',
	),
);
