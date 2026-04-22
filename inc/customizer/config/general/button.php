<?php
/**
 * Customizer fields: General panel → Button.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

use function \Trove\Customizer\trove_customizer_default;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_button_tip'   => array(
		'type'     => 'tip',
		'description' => __( 'The following button styles apply to all buttons which are built in the theme, but if the button is built with a block, the settings from the block will take precedence.', 'trove' ),
		'priority' => 0,
	),

	'trove_button_heading_primary'   => array(
		'type'     => 'heading',
		'label'    => __( 'Primary button', 'trove' ),
		'priority' => 1,
	),

	'trove_pair_button_background'   => array(
		'type'         => 'color_pair',
		'label'        => __( 'Background', 'trove' ),
		'priority'     => 2,
		'transport'    => 'postMessage',
		'swatch_hints' => array(
			'left'  => __( 'Default', 'trove' ),
			'right' => __( 'Hover', 'trove' ),
		),
		'pair'         => array(
			'left'  => array(
				'setting' => 'trove_button_background',
				'default' => (string) trove_customizer_default( 'trove_button_background', '#111111' ),
			),
			'right' => array(
				'setting' => 'trove_button_background_hover',
				'default' => (string) trove_customizer_default( 'trove_button_background_hover', '#064FFF' ),
			),
		),
	),

	'trove_pair_button_text'         => array(
		'type'         => 'color_pair',
		'label'        => __( 'Text', 'trove' ),
		'priority'     => 3,
		'transport'    => 'postMessage',
		'swatch_hints' => array(
			'left'  => __( 'Default', 'trove' ),
			'right' => __( 'Hover', 'trove' ),
		),
		'pair'         => array(
			'left'  => array(
				'setting' => 'trove_button_text_color',
				'default' => (string) trove_customizer_default( 'trove_button_text_color', '#ffffff' ),
			),
			'right' => array(
				'setting' => 'trove_button_text_color_hover',
				'default' => (string) trove_customizer_default( 'trove_button_text_color_hover', '#ffffff' ),
			),
		),
	),

	'trove_pair_button_border'       => array(
		'type'         => 'color_pair',
		'label'        => __( 'Border', 'trove' ),
		'priority'     => 4,
		'transport'    => 'postMessage',
		'swatch_hints' => array(
			'left'  => __( 'Default', 'trove' ),
			'right' => __( 'Hover', 'trove' ),
		),
		'pair'         => array(
			'left'  => array(
				'setting' => 'trove_button_border_color',
				'default' => (string) trove_customizer_default( 'trove_button_border_color', '#111111' ),
			),
			'right' => array(
				'setting' => 'trove_button_border_color_hover',
				'default' => (string) trove_customizer_default( 'trove_button_border_color_hover', '#333333' ),
			),
		),
	),

	'trove_button_text_font_size'    => array(
		'label'             => __( 'Text size', 'trove' ),
		'description'       => __( 'Font size for button labels.', 'trove' ),
		'type'              => 'range_number',
		'transport'         => 'postMessage',
		'unit'              => 'px',
		'default'           => (int) trove_customizer_default( 'trove_button_text_font_size', 16 ),
		'input_attrs'       => array(
			'min'  => 10,
			'max'  => 24,
			'step' => 1,
		),
		'sanitize_callback' => 'absint',
		'priority'          => 5,
	),

	'trove_button_border_radius'     => array(
		'label'             => __( 'Border radius', 'trove' ),
		'description'       => __( 'Corner rounding for buttons.', 'trove' ),
		'type'              => 'range_number',
		'transport'         => 'postMessage',
		'unit'              => 'px',
		'default'           => (int) trove_customizer_default( 'trove_button_border_radius', 6 ),
		'input_attrs'       => array(
			'min'  => 0,
			'max'  => 32,
			'step' => 1,
		),
		'sanitize_callback' => 'absint',
		'priority'          => 6,
	),

	'trove_button_border_width'      => array(
		'label'             => __( 'Border width', 'trove' ),
		'description'       => __( 'Set to 0 for no border.', 'trove' ),
		'type'              => 'range_number',
		'transport'         => 'postMessage',
		'unit'              => 'px',
		'default'           => (int) trove_customizer_default( 'trove_button_border_width', 1 ),
		'input_attrs'       => array(
			'min'  => 0,
			'max'  => 8,
			'step' => 1,
		),
		'sanitize_callback' => 'absint',
		'priority'          => 7,
	),

	'trove_button_padding'           => array(
		'type'               => 'responsive_spacing',
		'label'              => __( 'Padding', 'trove' ),
		'description'        => __( 'Inner spacing for buttons (per breakpoint).', 'trove' ),
		'transport'          => 'postMessage',
		'responsive_labels'  => array(
			'pc'     => __( 'Desktop', 'trove' ),
			'tablet' => __( 'Tablet', 'trove' ),
			'phone'  => __( 'Mobile', 'trove' ),
		),
		'default'            => array(
			'pc'     => array( 'top' => 10, 'right' => 15, 'bottom' => 10, 'left' => 15 ),
			'tablet' => array( 'top' => 10, 'right' => 15, 'bottom' => 10, 'left' => 15 ),
			'phone'  => array( 'top' => 10, 'right' => 15, 'bottom' => 10, 'left' => 15 ),
		),
		'input_attrs'        => array(
			'min'  => 0,
			'max'  => 60,
			'step' => 1,
		),
		'priority'           => 8,
	),

	'trove_button_heading_secondary' => array(
		'type'     => 'heading',
		'label'    => __( 'Secondary button', 'trove' ),
		'priority' => 20,
	),

	'trove_pair_button_secondary_background' => array(
		'type'         => 'color_pair',
		'label'        => __( 'Background', 'trove' ),
		'priority'     => 21,
		'transport'    => 'postMessage',
		'swatch_hints' => array(
			'left'  => __( 'Default', 'trove' ),
			'right' => __( 'Hover', 'trove' ),
		),
		'pair'         => array(
			'left'  => array(
				'setting' => 'trove_button_secondary_background',
				'default' => (string) trove_customizer_default( 'trove_button_secondary_background', 'transparent' ),
			),
			'right' => array(
				'setting' => 'trove_button_secondary_background_hover',
				'default' => (string) trove_customizer_default( 'trove_button_secondary_background_hover', '#111111' ),
			),
		),
	),

	'trove_pair_button_secondary_text'       => array(
		'type'         => 'color_pair',
		'label'        => __( 'Text', 'trove' ),
		'priority'     => 22,
		'transport'    => 'postMessage',
		'swatch_hints' => array(
			'left'  => __( 'Default', 'trove' ),
			'right' => __( 'Hover', 'trove' ),
		),
		'pair'         => array(
			'left'  => array(
				'setting' => 'trove_button_secondary_text_color',
				'default' => (string) trove_customizer_default( 'trove_button_secondary_text_color', '#111111' ),
			),
			'right' => array(
				'setting' => 'trove_button_secondary_text_color_hover',
				'default' => (string) trove_customizer_default( 'trove_button_secondary_text_color_hover', '#ffffff' ),
			),
		),
	),

	'trove_pair_button_secondary_border'     => array(
		'type'         => 'color_pair',
		'label'        => __( 'Border', 'trove' ),
		'priority'     => 23,
		'transport'    => 'postMessage',
		'swatch_hints' => array(
			'left'  => __( 'Default', 'trove' ),
			'right' => __( 'Hover', 'trove' ),
		),
		'pair'         => array(
			'left'  => array(
				'setting' => 'trove_button_secondary_border_color',
				'default' => (string) trove_customizer_default( 'trove_button_secondary_border_color', '#111111' ),
			),
			'right' => array(
				'setting' => 'trove_button_secondary_border_color_hover',
				'default' => (string) trove_customizer_default( 'trove_button_secondary_border_color_hover', '#111111' ),
			),
		),
	),

	'trove_button_secondary_text_font_size'  => array(
		'label'             => __( 'Text size', 'trove' ),
		'description'       => __( 'Font size for secondary button labels.', 'trove' ),
		'type'              => 'range_number',
		'transport'         => 'postMessage',
		'unit'              => 'px',
		'default'           => (int) trove_customizer_default( 'trove_button_secondary_text_font_size', 14 ),
		'input_attrs'       => array(
			'min'  => 10,
			'max'  => 24,
			'step' => 1,
		),
		'sanitize_callback' => 'absint',
		'priority'          => 24,
	),

	'trove_button_secondary_border_radius'   => array(
		'label'             => __( 'Border radius', 'trove' ),
		'description'       => __( 'Corner rounding for secondary buttons.', 'trove' ),
		'type'              => 'range_number',
		'transport'         => 'postMessage',
		'unit'              => 'px',
		'default'           => (int) trove_customizer_default( 'trove_button_secondary_border_radius', 6 ),
		'input_attrs'       => array(
			'min'  => 0,
			'max'  => 32,
			'step' => 1,
		),
		'sanitize_callback' => 'absint',
		'priority'          => 25,
	),

	'trove_button_secondary_border_width'    => array(
		'label'             => __( 'Border width', 'trove' ),
		'description'       => __( 'Set to 0 for no border.', 'trove' ),
		'type'              => 'range_number',
		'transport'         => 'postMessage',
		'unit'              => 'px',
		'default'           => (int) trove_customizer_default( 'trove_button_secondary_border_width', 1 ),
		'input_attrs'       => array(
			'min'  => 0,
			'max'  => 8,
			'step' => 1,
		),
		'sanitize_callback' => 'absint',
		'priority'          => 26,
	),

	'trove_button_secondary_padding'         => array(
		'type'               => 'responsive_spacing',
		'label'              => __( 'Padding', 'trove' ),
		'description'        => __( 'Inner spacing for secondary buttons (per breakpoint).', 'trove' ),
		'transport'          => 'postMessage',
		'responsive_labels'  => array(
			'pc'     => __( 'Desktop', 'trove' ),
			'tablet' => __( 'Tablet', 'trove' ),
			'phone'  => __( 'Mobile', 'trove' ),
		),
		'default'            => array(
			'pc'     => array( 'top' => 5, 'right' => 20, 'bottom' => 5, 'left' => 20 ),
			'tablet' => array( 'top' => 5, 'right' => 20, 'bottom' => 5, 'left' => 20 ),
			'phone'  => array( 'top' => 5, 'right' => 20, 'bottom' => 5, 'left' => 20 ),
		),
		'input_attrs'        => array(
			'min'  => 0,
			'max'  => 60,
			'step' => 1,
		),
		'priority'           => 27,
	),
);
