<?php
/**
 * Customizer fields: Footer section.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

use function \Trove\Customizer\customizer_default;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_footer_layout' => array(
		'label'       => __( 'Footer layout', 'trove' ),
		'type'        => 'image_radio',
		'default'     => (string) customizer_default( 'trove_footer_layout', 'footer-1' ),
		'transport'   => 'postMessage',
		'choices'     => array(
			'footer-1' => array(
				'label' => __( 'Footer 1', 'trove' ),
				'url'   => \TROVE_URI . '/inc/customizer/img/footer-1.png',
			),
			'footer-2' => array(
				'label' => __( 'Footer 2', 'trove' ),
				'url'   => \TROVE_URI . '/inc/customizer/img/footer-2.png',
			),
			'footer-3' => array(
				'label' => __( 'Footer 3', 'trove' ),
				'url'   => \TROVE_URI . '/inc/customizer/img/footer-3.png',
			),
			'footer-4' => array(
				'label' => __( 'Footer 4', 'trove' ),
				'url'   => \TROVE_URI . '/inc/customizer/img/footer-4.png',
			),
			
			
		),
		'sanitize_callback' => 'sanitize_text_field',
	),
	'trove_footer_top_border' => array(
		'label'             => __( 'Show top border', 'trove' ),
		'type'              => 'switch',
		'default'           => (bool) customizer_default( 'trove_footer_top_border', true ),
		'transport'         => 'postMessage',
		'sanitize_callback' => 'wp_validate_boolean',
	),
);
