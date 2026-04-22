<?php
/**
 * Customizer fields: Header → Layout subsection.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

use function \Trove\Customizer\trove_customizer_default;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_header_layout' => array(
		'label'       => __( 'Header layout', 'trove' ),
		'description' => __( 'Image radio control — pick a layout thumbnail.', 'trove' ),
		'type'        => 'image_radio',
		'default'     => (string) trove_customizer_default( 'trove_header_layout', 'horizontal-left-logo' ),
		'transport'   => 'postMessage',
		'choices'     => array(
			'horizontal-left-logo' => array(
				'label' => __( 'Horizontal Left logo', 'trove' ),
				'url'   => get_template_directory_uri() . '/inc/customizer/img/header-1.png',
			),
			'horizontal-centered-logo' => array(
				'label' => __( 'Horizontal Centered logo', 'trove' ),
				'url'   => get_template_directory_uri() . '/inc/customizer/img/header-2.png',
			),
			'horizontal-right-logo' => array(
				'label' => __( 'Horizontal Right logo', 'trove' ),
				'url'   => get_template_directory_uri() . '/inc/customizer/img/header-3.png',
			),
			'vertical-centered-logo' => array(
				'label' => __( 'Vertical centered logo', 'trove' ),
				'url'   => get_template_directory_uri() . '/inc/customizer/img/header-4.png',
			),
			'vertical-left-logo' => array(
				'label' => __( 'Vertical left logo', 'trove' ),
				'url'   => get_template_directory_uri() . '/inc/customizer/img/header-5.png',
			),

		),
		'sanitize_callback' => 'sanitize_text_field',
	),
	'trove_header_menu_alignment' => array(
		'label'             => __( 'Menu alignment', 'trove' ),
		'type'              => 'alignment',
		'default'           => (string) trove_customizer_default( 'trove_header_menu_alignment', 'right' ),
		'transport'         => 'postMessage',
		'choices'           => array(
			'left'   => __( 'Left', 'trove' ),
			'center' => __( 'Center', 'trove' ),
			'right'  => __( 'Right', 'trove' ),
		),
		'sanitize_callback' => 'sanitize_text_field',
	),
);
