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
		'label'       => __( 'Footer layout', 'trove' ),
		'type'        => 'image_radio',
		'default'     => 'footer-1',
		'transport'   => 'postMessage',
		'choices'     => array(
			'footer-1' => array(
				'label' => __( 'Footer 1', 'trove' ),
				'url'   => get_template_directory_uri() . '/inc/customizer/img/footer-1.png',
			),
			'footer-2' => array(
				'label' => __( 'Footer 2', 'trove' ),
				'url'   => get_template_directory_uri() . '/inc/customizer/img/footer-2.png',
			),
			'footer-3' => array(
				'label' => __( 'Footer 3', 'trove' ),
				'url'   => get_template_directory_uri() . '/inc/customizer/img/footer-3.png',
			),
			'footer-4' => array(
				'label' => __( 'Footer 4', 'trove' ),
				'url'   => get_template_directory_uri() . '/inc/customizer/img/footer-4.png',
			),
			
			
		),
		'sanitize_callback' => 'sanitize_text_field',
	)
);
