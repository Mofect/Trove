<?php
/**
 * Customizer fields: Header → styles.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

defined( 'ABSPATH' ) || exit;

$base = array(
	'trove_header_dark_mode'    => array(
		'label'             => __( 'Dark mode', 'trove' ),
		'type'              => 'switch',
		'default'           => false,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'wp_validate_boolean',
	),
	'trove_header_background'   => array(
		'label'             => __( 'Header background', 'trove' ),
		'description'       => __( 'Leave empty to use the default background for dark or light header.', 'trove' ),
		'type'              => 'color',
		'default'           => '',
		'transport'         => 'postMessage',
		'swatch_hint'       => __( 'Header area', 'trove' ),
	),
	'trove_header_text_color'     => array(
		'label'             => __( 'Header text', 'trove' ),
		'description'       => __( 'Main text color in the header (title, tagline). Leave empty for theme defaults.', 'trove' ),
		'type'              => 'color',
		'default'           => '',
		'transport'         => 'postMessage',
		'swatch_hint'       => __( 'Header text', 'trove' ),
	),
	'trove_header_links'          => array(
		'type'         => 'color_pair',
		'label'        => __( 'Header links', 'trove' ),
		'description'  => __( 'Link and hover colors for navigation and header utilities. Leave empty for theme defaults.', 'trove' ),
		'transport'    => 'postMessage',
		'swatch_hints' => array(
			'left'  => __( 'Link', 'trove' ),
			'right' => __( 'Hover', 'trove' ),
		),
		'pair'         => array(
			'left'  => array(
				'setting' => 'trove_header_link_color',
				'default' => '',
			),
			'right' => array(
				'setting' => 'trove_header_link_hover_color',
				'default' => '',
			),
		),
	),
	'trove_header_border_color'   => array(
		'label'             => __( 'Header border', 'trove' ),
		'description'       => __( 'Bottom border when enabled in Header → layout. Leave empty for theme defaults.', 'trove' ),
		'type'              => 'color',
		'default'           => '',
		'transport'         => 'postMessage',
		'swatch_hint'       => __( 'Header border', 'trove' ),
	),
);

$wc_styles = array();
if ( class_exists( 'WooCommerce' ) ) {
	$wc_path = __DIR__ . '/styles-woocommerce.php';
	if ( is_readable( $wc_path ) ) {
		$wc_styles = require $wc_path;
		if ( ! is_array( $wc_styles ) ) {
			$wc_styles = array();
		}
	}
}

return array_merge( $base, $wc_styles );
