<?php
/**
 * Customizer fields: Global Colors (root-level section).
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

defined( 'ABSPATH' ) || exit;

$colors = array(
	'trove_cz_heading_global' => array(
		'type'     => 'heading',
		'label'    => __( 'Color Schemes', 'trove' ),
		'priority' => 0,
		'color_schemes' => array(
			array(
				'label'  => __( 'Preset: Light', 'trove' ),
				'colors' => array(
					'trove_color_page_background' => '#ffffff',
					'trove_color_base_text'       => '#111111',
					'trove_color_headings'        => '#000000',
					'trove_color_link'            => '#064fff',
					'trove_color_link_hover'      => '#003dcc',
					'trove_color_selection_text'  => '#ffffff',
					'trove_color_selection_bg'    => '#22c55e',
					'trove_color_border'          => '#eeeeee',
					'trove_color_accent'          => '#064fff',
					'trove_header_background'     => '#ffffff',
					'trove_header_text_color'     => '#111111',
					'trove_header_link_color'     => '#064fff',
					'trove_header_link_hover_color' => '#003dcc',
					'trove_footer_background'     => '#eeeeee',
					'trove_footer_text_color'     => '#111111',
					'trove_footer_link_color'     => '#064fff',
					'trove_footer_link_hover_color' => '#003dcc',
					'trove_header_cart_badge_color' => '#064fff',
					'trove_header_cart_count_color' => '#ffffff',
				),
			),
			array(
				'label'  => __( 'Preset: Dark', 'trove' ),
				'colors' => array(
					'trove_color_page_background' => '#0f172a',
					'trove_color_base_text'       => '#e2e8f0',
					'trove_color_headings'        => '#f8fafc',
					'trove_color_link'            => '#38bdf8',
					'trove_color_link_hover'      => '#7dd3fc',
					'trove_color_selection_text'  => '#0f172a',
					'trove_color_selection_bg'    => '#4ade80',
					'trove_color_border'          => '#334155',
					'trove_color_accent'          => '#38bdf8',
					'trove_header_background'     => '#0f172a',
					'trove_header_text_color'     => '#e2e8f0',
					'trove_header_link_color'     => '#38bdf8',
					'trove_header_link_hover_color' => '#7dd3fc',
					'trove_footer_background'     => '#1e293b',
					'trove_footer_text_color'     => '#e2e8f0',
					'trove_footer_link_color'     => '#38bdf8',
					'trove_footer_link_hover_color' => '#7dd3fc',
					'trove_header_cart_badge_color' => '#38bdf8',
					'trove_header_cart_count_color' => '#ffffff',
				),
			),
		),
	),

	'trove_color_page_background' => array(
		'label'             => __( 'Page background', 'trove' ),
		'type'              => 'color',
		'default'           => '#ffffff',
		'priority'          => 1,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Body background', 'trove' ),
	),

	'trove_color_base_text' => array(
		'label'             => __( 'Base Text', 'trove' ),
		'type'              => 'color',
		'default'           => '#111111',
		'priority'          => 2,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Body text', 'trove' ),
	),

	'trove_color_headings' => array(
		'label'             => __( 'Headings', 'trove' ),
		'type'              => 'color',
		'default'           => '#000000',
		'priority'          => 2,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'H1-H6', 'trove' ),
	),

	'trove_pair_links'    => array(
		'type'        => 'color_pair',
		'label'       => __( 'Links', 'trove' ),
		'priority'    => 2,
		'transport'   => 'postMessage',
		'swatch_hints' => array(
			'left'  => __( 'Initial', 'trove' ),
			'right' => __( 'Hover', 'trove' ),
		),
		'pair'        => array(
			'left'  => array(
				'setting' => 'trove_color_link',
				'default' => '#064fff',
			),
			'right' => array(
				'setting' => 'trove_color_link_hover',
				'default' => '#003dcc',
			),
		),
	),

	'trove_pair_selection' => array(
		'type'        => 'color_pair',
		'label'       => __( 'Text Selection', 'trove' ),
		'priority'    => 4,
		'transport'   => 'postMessage',
		'swatch_hints' => array(
			'left'  => __( 'Text', 'trove' ),
			'right' => __( 'Background', 'trove' ),
		),
		'pair'        => array(
			'left'  => array(
				'setting' => 'trove_color_selection_text',
				'default' => '#ffffff',
			),
			'right' => array(
				'setting' => 'trove_color_selection_bg',
				'default' => '#22c55e',
			),
		),
	),

	'trove_color_border'  => array(
		'label'             => __( 'Borders', 'trove' ),
		'type'              => 'color',
		'default'           => '#eeeeee',
		'priority'          => 5,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Border lines', 'trove' ),
	),

	'trove_color_accent'  => array(
		'label'             => __( 'Accent', 'trove' ),
		'type'              => 'color',
		'default'           => '#064fff',
		'priority'          => 6,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Focus & highlights', 'trove' ),
	),

	'trove_cz_heading_header' => array(
		'type'     => 'heading',
		'label'    => __( 'Header', 'trove' ),
		'priority' => 7,
	),

	'trove_header_background' => array(
		'label'             => __( 'Header background', 'trove' ),
		'type'              => 'color',
		'default'           => '#ffffff',
		'priority'          => 8,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Header area', 'trove' ),
	),

	'trove_header_text_color' => array(
		'label'             => __( 'Header text', 'trove' ),
		'type'              => 'color',
		'default'           => '#111111',
		'priority'          => 9,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Header text', 'trove' ),
	),

	'trove_pair_header_links' => array(
		'type'         => 'color_pair',
		'label'        => __( 'Header links', 'trove' ),
		'priority'     => 10,
		'transport'    => 'postMessage',
		'swatch_hints' => array(
			'left'  => __( 'Link', 'trove' ),
			'right' => __( 'Hover', 'trove' ),
		),
		'pair'         => array(
			'left'  => array(
				'setting' => 'trove_header_link_color',
				'default' => '#064fff',
			),
			'right' => array(
				'setting' => 'trove_header_link_hover_color',
				'default' => '#003dcc',
			),
		),
	),

	'trove_cz_heading_footer' => array(
		'type'     => 'heading',
		'label'    => __( 'Footer', 'trove' ),
		'priority' => 11,
	),

	'trove_footer_background' => array(
		'label'             => __( 'Footer background', 'trove' ),
		'type'              => 'color',
		'default'           => '#eeeeee',
		'priority'          => 12,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Footer area', 'trove' ),
	),

	'trove_footer_text_color' => array(
		'label'             => __( 'Footer text', 'trove' ),
		'type'              => 'color',
		'default'           => '#111111',
		'priority'          => 13,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Footer text', 'trove' ),
	),

	'trove_pair_footer_links' => array(
		'type'         => 'color_pair',
		'label'        => __( 'Footer links', 'trove' ),
		'priority'     => 14,
		'transport'    => 'postMessage',
		'swatch_hints' => array(
			'left'  => __( 'Link', 'trove' ),
			'right' => __( 'Hover', 'trove' ),
		),
		'pair'         => array(
			'left'  => array(
				'setting' => 'trove_footer_link_color',
				'default' => '#064fff',
			),
			'right' => array(
				'setting' => 'trove_footer_link_hover_color',
				'default' => '#003dcc',
			),
		),
	),
);

if ( class_exists( 'WooCommerce' ) ) {
	$colors = array_merge(
		$colors,
		array(
			'trove_cz_heading_woocommerce_colors' => array(
				'type'     => 'heading',
				'label'    => __( 'WooCommerce', 'trove' ),
				'priority' => 14,
			),
			'trove_header_cart_badge_color' => array(
				'label'             => __( 'Cart badge', 'trove' ),
				'description'       => __( 'Background color of the cart item count badge next to the mini cart icon.', 'trove' ),
				'type'              => 'color',
				'default'           => '#064fff',
				'priority'          => 15,
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sanitize_hex_color',
				'swatch_hint'       => __( 'Cart badge', 'trove' ),
			),
			'trove_header_cart_count_color' => array(
				'label'             => __( 'Cart count', 'trove' ),
				'description'       => __( 'Text color for the number inside the cart badge.', 'trove' ),
				'type'              => 'color',
				'default'           => '#ffffff',
				'priority'          => 16,
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sanitize_hex_color',
				'swatch_hint'       => __( 'Cart count', 'trove' ),
			),
		)
	);
}

return $colors;
