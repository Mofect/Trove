<?php
/**
 * Customizer fields: Global Colors (root-level section).
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_cz_heading_global' => array(
		'type'     => 'heading',
		'label'    => __( 'Color Schemes', 'trove' ),
		'priority' => 0,
		'color_schemes' => array(
			array(
				'label'  => __( 'Preset: Default', 'trove' ),
				'colors' => array(
					'trove_color_base_text'      => '#111111',
					'trove_color_headings'       => '#000000',
					'trove_color_link'           => '#064fff',
					'trove_color_link_hover'     => '#003dcc',
					'trove_color_selection_text' => '#ffffff',
					'trove_color_selection_bg'   => '#22c55e',
					'trove_color_border'         => '#eeeeee',
					'trove_color_accent'         => '#064fff',
				),
			),
			array(
				'label'  => __( 'Preset: Ocean', 'trove' ),
				'colors' => array(
					'trove_color_base_text'      => '#0f172a',
					'trove_color_headings'       => '#0c4a6e',
					'trove_color_link'           => '#0284c7',
					'trove_color_link_hover'     => '#0369a1',
					'trove_color_selection_text' => '#ffffff',
					'trove_color_selection_bg'   => '#0ea5e9',
					'trove_color_border'         => '#e0f2fe',
					'trove_color_accent'         => '#0284c7',
				),
			),
			array(
				'label'  => __( 'Preset: Sunset', 'trove' ),
				'colors' => array(
					'trove_color_base_text'      => '#292524',
					'trove_color_headings'       => '#7c2d12',
					'trove_color_link'           => '#ea580c',
					'trove_color_link_hover'     => '#c2410c',
					'trove_color_selection_text' => '#fff7ed',
					'trove_color_selection_bg'   => '#f97316',
					'trove_color_border'         => '#fed7aa',
					'trove_color_accent'         => '#ea580c',
				),
			)
		),
	),

	'trove_color_base_text' => array(
		'label'             => __( 'Base Text', 'trove' ),
		'type'              => 'color',
		'default'           => '#111111',
		'priority'          => 1,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Body text', 'trove' ),
	),

	'trove_color_headings' => array(
		'label'             => __( 'Headings', 'trove' ),
		'type'              => 'color',
		'default'           => '#000000',
		'priority'          => 1,
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
		'priority'    => 3,
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
		'priority'          => 4,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Border lines', 'trove' ),
	),

	'trove_color_accent'  => array(
		'label'             => __( 'Accent', 'trove' ),
		'type'              => 'color',
		'default'           => '#064fff',
		'priority'          => 5,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Focus & highlights', 'trove' ),
	),
);
