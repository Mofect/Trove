<?php
/**
 * Customizer fields: Footer → styles.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_footer_dark_mode' => array(
		'label'             => __( 'Dark mode', 'trove' ),
		'type'              => 'switch',
		'default'           => false,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'wp_validate_boolean',
	),
	'trove_footer_background' => array(
		'label'             => __( 'Footer background', 'trove' ),
		'description'       => __( 'Leave empty to use the default background for dark or light footer.', 'trove' ),
		'type'              => 'color',
		'default'           => '',
		'transport'         => 'postMessage',
		'swatch_hint'       => __( 'Footer area', 'trove' ),
	),
	'trove_footer_text_color' => array(
		'label'             => __( 'Footer text', 'trove' ),
		'description'       => __( 'Main text color in the footer. Leave empty for theme defaults.', 'trove' ),
		'type'              => 'color',
		'default'           => '',
		'transport'         => 'postMessage',
		'swatch_hint'       => __( 'Footer text', 'trove' ),
	),
	'trove_footer_links'      => array(
		'type'            => 'color_pair',
		'label'           => __( 'Footer links', 'trove' ),
		'description'     => __( 'Link and hover colors for footer menus and social icons. Leave empty for theme defaults.', 'trove' ),
		'transport'       => 'postMessage',
		'swatch_hints'    => array(
			'left'  => __( 'Link', 'trove' ),
			'right' => __( 'Hover', 'trove' ),
		),
		'pair'            => array(
			'left'  => array(
				'setting' => 'trove_footer_link_color',
				'default' => '',
			),
			'right' => array(
				'setting' => 'trove_footer_link_hover_color',
				'default' => '',
			),
		),
	),
	'trove_footer_border_color' => array(
		'label'             => __( 'Footer border', 'trove' ),
		'description'       => __( 'Borders between footer regions and inputs. Leave empty for theme defaults.', 'trove' ),
		'type'              => 'color',
		'default'           => '',
		'transport'         => 'postMessage',
		'swatch_hint'       => __( 'Footer borders', 'trove' ),
	),
);
