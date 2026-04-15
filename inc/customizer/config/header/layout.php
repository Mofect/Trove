<?php
/**
 * Customizer fields: Header → Layout subsection.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_header_layout' => array(
		'label'       => __( 'Header layout', 'trove' ),
		'description' => __( 'Image radio control — pick a layout thumbnail.', 'trove' ),
		'type'        => 'image_radio',
		'default'     => 'horizontal-left-logo',
		'choices'     => array(
			'horizontal-left-logo' => array(
				'label' => __( 'Left logo', 'trove' ),
				'url'   => get_template_directory_uri() . '/inc/customizer/img/header-1.png',
			),
			'horizontal-centered-logo' => array(
				'label' => __( 'Centered logo', 'trove' ),
				'url'   => get_template_directory_uri() . '/inc/customizer/img/header-2.png',
			),
			'horizontal-right-logo' => array(
				'label' => __( 'Right logo', 'trove' ),
				'url'   => get_template_directory_uri() . '/inc/customizer/img/header-3.png',
			)
		),
		'sanitize_callback' => 'sanitize_text_field',
	),
	'trove_header_bottom_border' => array(
		'label'             => __( 'Show bottom border', 'trove' ),
		'type'              => 'switch',
		'default'           => false,
		'sanitize_callback' => 'wp_validate_boolean',
	),
);
