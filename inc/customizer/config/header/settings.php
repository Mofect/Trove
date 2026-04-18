<?php
/**
 * Customizer fields: Header → Settings subsection.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_header_logo'            => array(
		'label'             => __( 'Logo', 'trove' ),
		'description'       => __( 'Optional. When set, this image is used in the header instead of the logo from Site Identity.', 'trove' ),
		'type'              => 'image',
		'default'           => '',
		'transport'         => 'refresh',
		'sanitize_callback' => 'esc_url_raw',
	),
	'trove_header_logo_width'      => array(
		'label'             => __( 'Logo width', 'trove' ),
		'description'       => __( 'Maximum width for the header logo.', 'trove' ),
		'type'              => 'range',
		'default'           => 200,
		'input_attrs'       => array(
			'min'  => 40,
			'max'  => 400,
			'step' => 1,
		),
		'transport'         => 'postMessage',
		'sanitize_callback' => 'Trove\\sanitize_header_logo_width',
	),
	'trove_header_sticky' => array(
		'label'             => __( 'Sticky header', 'trove' ),
		'type'              => 'select',
		'default'           => 'none',
		'transport'         => 'postMessage',
		'choices'           => array(
			'none'         => __( 'None', 'trove' ),
			'on_scroll_up' => __( 'On scroll up', 'trove' ),
			'always'       => __( 'Always Sticky', 'trove' ),
		),
		'sanitize_callback' => function ( $value ) {
			$allowed = array( 'none', 'on_scroll_up', 'always' );
			$value   = sanitize_key( (string) $value );
			return in_array( $value, $allowed, true ) ? $value : 'none';
		},
	),
	'trove_header_bottom_border' => array(
		'label'             => __( 'Show bottom border', 'trove' ),
		'type'              => 'switch',
		'default'           => false,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'wp_validate_boolean',
	),
);
