<?php
/**
 * Customizer fields: Global Colors (root-level section).
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

use function \Trove\Customizer\customizer_color_scheme_presets_raw;
use function \Trove\Customizer\customizer_default;

defined( 'ABSPATH' ) || exit;

$scheme_labels = array(
	__( 'Preset: Light', 'trove' ),
	__( 'Preset: Dark', 'trove' ),
);
$color_schemes = array();
foreach ( customizer_color_scheme_presets_raw() as $i => $preset_row ) {
	$color_schemes[] = array(
		'label'  => isset( $scheme_labels[ $i ] ) ? $scheme_labels[ $i ] : '',
		'colors' => $preset_row['colors'],
	);
}

$colors = array(
	'trove_cz_heading_global' => array(
		'type'            => 'heading',
		'label'           => __( 'Color Schemes', 'trove' ),
		'priority'        => 0,
		'color_schemes'   => $color_schemes,
	),

	'trove_color_page_background' => array(
		'label'             => __( 'Page background', 'trove' ),
		'type'              => 'color',
		'default'           => customizer_default( 'trove_color_page_background', '#ffffff' ),
		'priority'          => 1,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Body background', 'trove' ),
	),

	'trove_color_base_text' => array(
		'label'             => __( 'Base Text', 'trove' ),
		'type'              => 'color',
		'default'           => customizer_default( 'trove_color_base_text', '#111111' ),
		'priority'          => 2,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Body text', 'trove' ),
	),

	'trove_color_headings' => array(
		'label'             => __( 'Headings', 'trove' ),
		'type'              => 'color',
		'default'           => customizer_default( 'trove_color_headings', '#000000' ),
		'priority'          => 2,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'H1-H6', 'trove' ),
	),

	'trove_pair_links'    => array(
		'type'         => 'color_pair',
		'label'        => __( 'Links', 'trove' ),
		'priority'     => 2,
		'transport'    => 'postMessage',
		'swatch_hints' => array(
			'left'  => __( 'Initial', 'trove' ),
			'right' => __( 'Hover', 'trove' ),
		),
		'pair'         => array(
			'left'  => array(
				'setting' => 'trove_color_link',
				'default' => customizer_default( 'trove_color_link', '#333333' ),
			),
			'right' => array(
				'setting' => 'trove_color_link_hover',
				'default' => customizer_default( 'trove_color_link_hover', '#1daa9e' ),
			),
		),
	),

	'trove_pair_selection' => array(
		'type'         => 'color_pair',
		'label'        => __( 'Text Selection', 'trove' ),
		'priority'     => 4,
		'transport'    => 'postMessage',
		'swatch_hints' => array(
			'left'  => __( 'Text', 'trove' ),
			'right' => __( 'Background', 'trove' ),
		),
		'pair'         => array(
			'left'  => array(
				'setting' => 'trove_color_selection_text',
				'default' => customizer_default( 'trove_color_selection_text', '#ffffff' ),
			),
			'right' => array(
				'setting' => 'trove_color_selection_bg',
				'default' => customizer_default( 'trove_color_selection_bg', '#38BDF8' ),
			),
		),
	),

	'trove_color_border'  => array(
		'label'             => __( 'Borders', 'trove' ),
		'type'              => 'color',
		'default'           => customizer_default( 'trove_color_border', '#eeeeee' ),
		'priority'          => 5,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Border lines', 'trove' ),
	),

	'trove_color_accent'  => array(
		'label'             => __( 'Accent', 'trove' ),
		'type'              => 'color',
		'default'           => customizer_default( 'trove_color_accent', '#1daa9e' ),
		'priority'          => 6,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Focus & highlights', 'trove' ),
	),

	'trove_cz_heading_announcement' => array(
		'type'     => 'heading',
		'label'    => __( 'Announcement bar', 'trove' ),
		'priority' => 7,
	),

	'trove_announcement_background' => array(
		'label'             => __( 'Background', 'trove' ),
		'type'              => 'color',
		'default'           => customizer_default( 'trove_announcement_background', '#1daa9e' ),
		'priority'          => 8,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Announcement bar', 'trove' ),
	),

	'trove_announcement_text_color' => array(
		'label'             => __( 'Text', 'trove' ),
		'type'              => 'color',
		'default'           => customizer_default( 'trove_announcement_text_color', 'rgba(255, 255, 255, 0.8)' ),
		'priority'          => 9,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Announcement text', 'trove' ),
	),

	'trove_pair_announcement_links' => array(
		'type'         => 'color_pair',
		'label'        => __( 'Announcement links', 'trove' ),
		'priority'     => 10,
		'transport'    => 'postMessage',
		'swatch_hints' => array(
			'left'  => __( 'Link', 'trove' ),
			'right' => __( 'Hover', 'trove' ),
		),
		'pair'         => array(
			'left'  => array(
				'setting' => 'trove_announcement_link_color',
				'default' => customizer_default( 'trove_announcement_link_color', 'rgba(255, 255, 255, 1)' ),
			),
			'right' => array(
				'setting' => 'trove_announcement_link_hover_color',
				'default' => customizer_default( 'trove_announcement_link_hover_color', 'rgba(255, 255, 255, 0.8)' ),
			),
		),
	),

	'trove_cz_heading_header' => array(
		'type'     => 'heading',
		'label'    => __( 'Header', 'trove' ),
		'priority' => 11,
	),

	'trove_header_background' => array(
		'label'             => __( 'Header background', 'trove' ),
		'type'              => 'color',
		'default'           => customizer_default( 'trove_header_background', '#ffffff' ),
		'priority'          => 12,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Header area', 'trove' ),
	),

	'trove_header_text_color' => array(
		'label'             => __( 'Header text', 'trove' ),
		'type'              => 'color',
		'default'           => customizer_default( 'trove_header_text_color', '#111111' ),
		'priority'          => 13,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Header text', 'trove' ),
	),

	'trove_pair_header_links' => array(
		'type'         => 'color_pair',
		'label'        => __( 'Header links', 'trove' ),
		'priority'     => 14,
		'transport'    => 'postMessage',
		'swatch_hints' => array(
			'left'  => __( 'Link', 'trove' ),
			'right' => __( 'Hover', 'trove' ),
		),
		'pair'         => array(
			'left'  => array(
				'setting' => 'trove_header_link_color',
				'default' => customizer_default( 'trove_header_link_color', '#000000' ),
			),
			'right' => array(
				'setting' => 'trove_header_link_hover_color',
				'default' => customizer_default( 'trove_header_link_hover_color', '#666666' ),
			),
		),
	),

	'trove_cz_heading_footer' => array(
		'type'     => 'heading',
		'label'    => __( 'Footer', 'trove' ),
		'priority' => 15,
	),

	'trove_footer_background' => array(
		'label'             => __( 'Footer background', 'trove' ),
		'type'              => 'color',
		'default'           => customizer_default( 'trove_footer_background', '#eeeeee' ),
		'priority'          => 16,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Footer area', 'trove' ),
	),

	'trove_footer_text_color' => array(
		'label'             => __( 'Footer text', 'trove' ),
		'type'              => 'color',
		'default'           => customizer_default( 'trove_footer_text_color', '#111111' ),
		'priority'          => 17,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Footer text', 'trove' ),
	),

	'trove_pair_footer_links' => array(
		'type'         => 'color_pair',
		'label'        => __( 'Footer links', 'trove' ),
		'priority'     => 18,
		'transport'    => 'postMessage',
		'swatch_hints' => array(
			'left'  => __( 'Link', 'trove' ),
			'right' => __( 'Hover', 'trove' ),
		),
		'pair'         => array(
			'left'  => array(
				'setting' => 'trove_footer_link_color',
				'default' => customizer_default( 'trove_footer_link_color', '#000000' ),
			),
			'right' => array(
				'setting' => 'trove_footer_link_hover_color',
				'default' => customizer_default( 'trove_footer_link_hover_color', '#666666' ),
			),
		),
	),

	'trove_footer_border_color' => array(
		'label'             => __( 'Footer border', 'trove' ),
		'type'              => 'color',
		'default'           => customizer_default( 'trove_footer_border_color', '#dddddd' ),
		'priority'          => 19,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Footer top border', 'trove' ),
	),
);

if ( class_exists( 'WooCommerce' ) ) {
	$colors = array_merge(
		$colors,
		array(
			'trove_cz_heading_woocommerce_colors' => array(
				'type'     => 'heading',
				'label'    => __( 'WooCommerce', 'trove' ),
				'priority' => 19,
			),
			'trove_header_cart_badge_color' => array(
				'label'             => __( 'Cart badge', 'trove' ),
				'description'       => __( 'Background color of the cart item count badge next to the mini cart icon.', 'trove' ),
				'type'              => 'color',
				'default'           => customizer_default( 'trove_header_cart_badge_color', '#ff0000' ),
				'priority'          => 20,
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sanitize_hex_color',
				'swatch_hint'       => __( 'Cart badge', 'trove' ),
			),
			'trove_header_cart_count_color' => array(
				'label'             => __( 'Cart count', 'trove' ),
				'description'       => __( 'Text color for the number inside the cart badge.', 'trove' ),
				'type'              => 'color',
				'default'           => customizer_default( 'trove_header_cart_count_color', '#ffffff' ),
				'priority'          => 21,
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sanitize_hex_color',
				'swatch_hint'       => __( 'Cart count', 'trove' ),
			),
			'trove_cz_heading_store_notice' => array(
				'type'     => 'heading',
				'label'    => __( 'Store notice', 'trove' ),
				'priority' => 22,
			),
			'trove_store_notice_background' => array(
				'label'             => __( 'Notice bar background', 'trove' ),
				'description'       => __( 'Background for the WooCommerce store notice bar.', 'trove' ),
				'type'              => 'color',
				'default'           => customizer_default( 'trove_store_notice_background', '#720eec' ),
				'priority'          => 23,
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sanitize_hex_color',
				'swatch_hint'       => __( 'Store notice background', 'trove' ),
			),
			'trove_store_notice_text_color' => array(
				'label'             => __( 'Notice bar text', 'trove' ),
				'description'       => __( 'Text color for the store notice message.', 'trove' ),
				'type'              => 'color',
				'default'           => customizer_default( 'trove_store_notice_text_color', '#ffffff' ),
				'priority'          => 24,
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sanitize_hex_color',
				'swatch_hint'       => __( 'Store notice text', 'trove' ),
			),
			'trove_pair_store_notice_links' => array(
				'type'         => 'color_pair',
				'label'        => __( 'Notice bar links', 'trove' ),
				'description'  => __( 'Link and hover colors for links in the store notice (including dismiss).', 'trove' ),
				'priority'     => 25,
				'transport'    => 'postMessage',
				'swatch_hints' => array(
					'left'  => __( 'Link', 'trove' ),
					'right' => __( 'Hover', 'trove' ),
				),
				'pair'         => array(
					'left'  => array(
						'setting' => 'trove_store_notice_link_color',
						'default' => customizer_default( 'trove_store_notice_link_color', '#ffffff' ),
					),
					'right' => array(
						'setting' => 'trove_store_notice_link_hover_color',
						'default' => customizer_default( 'trove_store_notice_link_hover_color', '#e8defc' ),
					),
				),
			),
		)
	);
}

return $colors;
