<?php
/**
 * Customizer fields: General panel → Text Field.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

use function \Trove\Customizer\trove_customizer_default;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_text_field_tip' => array(
		'type'        => 'tip',
		'description' => __( 'These settings style theme input fields and textareas.', 'trove' ),
		'priority'    => 0,
	),

	'trove_text_field_heading_input' => array(
		'type'     => 'heading',
		'label'    => __( 'Input field', 'trove' ),
		'priority' => 1,
	),

	'trove_input_field_height' => array(
		'label'             => __( 'Height (px)', 'trove' ),
		'type'              => 'range_number',
		'transport'         => 'postMessage',
		'unit'              => 'px',
		'default'           => (int) trove_customizer_default( 'trove_input_field_height', 42 ),
		'input_attrs'       => array( 'min' => 28, 'max' => 80, 'step' => 1 ),
		'sanitize_callback' => 'absint',
		'priority'          => 2,
	),
	'trove_input_field_text_size' => array(
		'label'             => __( 'Text size (px)', 'trove' ),
		'type'              => 'range_number',
		'transport'         => 'postMessage',
		'unit'              => 'px',
		'default'           => (int) trove_customizer_default( 'trove_input_field_text_size', 16 ),
		'input_attrs'       => array( 'min' => 10, 'max' => 24, 'step' => 1 ),
		'sanitize_callback' => 'absint',
		'priority'          => 3,
	),
	'trove_input_field_border_width' => array(
		'label'             => __( 'Border width (px)', 'trove' ),
		'type'              => 'range_number',
		'transport'         => 'postMessage',
		'unit'              => 'px',
		'default'           => (int) trove_customizer_default( 'trove_input_field_border_width', 1 ),
		'input_attrs'       => array( 'min' => 0, 'max' => 8, 'step' => 1 ),
		'sanitize_callback' => 'absint',
		'priority'          => 4,
	),
	'trove_input_field_border_radius' => array(
		'label'             => __( 'Border radius (px)', 'trove' ),
		'type'              => 'range_number',
		'transport'         => 'postMessage',
		'unit'              => 'px',
		'default'           => (int) trove_customizer_default( 'trove_input_field_border_radius', 3 ),
		'input_attrs'       => array( 'min' => 0, 'max' => 32, 'step' => 1 ),
		'sanitize_callback' => 'absint',
		'priority'          => 5,
	),
	'trove_input_field_border_color' => array(
		'label'             => __( 'Border color', 'trove' ),
		'type'              => 'color',
		'default'           => (string) trove_customizer_default( 'trove_input_field_border_color', '#eeeeee' ),
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Input border', 'trove' ),
		'priority'          => 6,
	),
	'trove_input_field_text_color' => array(
		'label'             => __( 'Text color', 'trove' ),
		'type'              => 'color',
		'default'           => (string) trove_customizer_default( 'trove_input_field_text_color', '#111111' ),
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Input text', 'trove' ),
		'priority'          => 7,
	),

	'trove_text_field_heading_textarea' => array(
		'type'     => 'heading',
		'label'    => __( 'Textarea', 'trove' ),
		'priority' => 20,
	),

	'trove_textarea_field_height' => array(
		'label'             => __( 'Height (px)', 'trove' ),
		'type'              => 'range_number',
		'transport'         => 'postMessage',
		'unit'              => 'px',
		'default'           => (int) trove_customizer_default( 'trove_textarea_field_height', 120 ),
		'input_attrs'       => array( 'min' => 60, 'max' => 400, 'step' => 1 ),
		'sanitize_callback' => 'absint',
		'priority'          => 21,
	),
	'trove_textarea_field_text_size' => array(
		'label'             => __( 'Text size (px)', 'trove' ),
		'type'              => 'range_number',
		'transport'         => 'postMessage',
		'unit'              => 'px',
		'default'           => (int) trove_customizer_default( 'trove_textarea_field_text_size', 16 ),
		'input_attrs'       => array( 'min' => 10, 'max' => 24, 'step' => 1 ),
		'sanitize_callback' => 'absint',
		'priority'          => 22,
	),
	'trove_textarea_field_border_width' => array(
		'label'             => __( 'Border width (px)', 'trove' ),
		'type'              => 'range_number',
		'transport'         => 'postMessage',
		'unit'              => 'px',
		'default'           => (int) trove_customizer_default( 'trove_textarea_field_border_width', 1 ),
		'input_attrs'       => array( 'min' => 0, 'max' => 8, 'step' => 1 ),
		'sanitize_callback' => 'absint',
		'priority'          => 23,
	),
	'trove_textarea_field_border_radius' => array(
		'label'             => __( 'Border radius (px)', 'trove' ),
		'type'              => 'range_number',
		'transport'         => 'postMessage',
		'unit'              => 'px',
		'default'           => (int) trove_customizer_default( 'trove_textarea_field_border_radius', 3 ),
		'input_attrs'       => array( 'min' => 0, 'max' => 32, 'step' => 1 ),
		'sanitize_callback' => 'absint',
		'priority'          => 24,
	),
	'trove_textarea_field_border_color' => array(
		'label'             => __( 'Border color', 'trove' ),
		'type'              => 'color',
		'default'           => (string) trove_customizer_default( 'trove_textarea_field_border_color', '#eeeeee' ),
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Textarea border', 'trove' ),
		'priority'          => 25,
	),
	'trove_textarea_field_text_color' => array(
		'label'             => __( 'Text color', 'trove' ),
		'type'              => 'color',
		'default'           => (string) trove_customizer_default( 'trove_textarea_field_text_color', '#111111' ),
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
		'swatch_hint'       => __( 'Textarea text', 'trove' ),
		'priority'          => 26,
	),
);

