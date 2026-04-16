<?php
/**
 * Customizer fields: Typography section (root-level).
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_google_font_family' => array(
		'label'       => __( 'Body font', 'trove' ),
		'description' => __( 'Choose a Google Font or System UI. Fonts load from Google’s CDN.', 'trove' ),
		'type'        => 'google_font',
		'default'     => 'Instrument Sans',
		'transport'   => 'postMessage',
	),
	'trove_font_base_size'     => array(
		'label'                   => __( 'Base font size', 'trove' ),
		'description'             => __( 'Set the value and unit on the same row.', 'trove' ),
		'type'                    => 'number_unit',
		'unit_setting'            => 'trove_font_base_size_unit',
		'number_default'          => 16,
		'unit_default'            => 'px',
		'transport'               => 'postMessage',
		'input_attrs'             => array(
			'min'  => 0.5,
			'max'  => 32,
			'step' => 0.0625,
		),
		'unit_choices'            => array(
			'px'  => 'px',
			'rem' => 'rem',
			'em'  => 'em',
		),
		'sanitize_callback'       => '\Trove\Customizer\trove_sanitize_font_base_size',
		'unit_sanitize_callback'  => '\Trove\Customizer\trove_sanitize_font_base_size_unit',
	),
	'trove_font_base_weight'   => array(
		'label'             => __( 'Base font weight', 'trove' ),
		'description'       => __( 'Choices match the weights available for the selected body font.', 'trove' ),
		'type'              => 'select',
		'default'           => '400',
		'transport'         => 'postMessage',
		/* Populated dynamically in the Customizer by customizer-font-weight.js from Google Fonts metadata. */
		'choices'           => array(
			'400' => __( '400 — Regular', 'trove' ),
		),
		'sanitize_callback' => '\Trove\Customizer\trove_sanitize_font_base_weight',
	),
	'trove_font_base_line_height' => array(
		'label'                  => __( 'Base line height', 'trove' ),
		'description'            => __( 'Set the value and unit on the same row.', 'trove' ),
		'type'                   => 'number_unit',
		'unit_setting'           => 'trove_font_base_line_height_unit',
		'number_default'         => 1.5,
		'unit_default'           => 'unitless',
		'transport'              => 'postMessage',
		'input_attrs'            => array(
			'min'  => 0.5,
			'max'  => 250,
			'step' => 0.05,
		),
		'unit_choices'           => array(
			'unitless' => __( 'Unitless', 'trove' ),
			'px'       => 'px',
			'rem'      => 'rem',
			'em'       => 'em',
			'%'        => '%',
		),
		'sanitize_callback'      => '\Trove\Customizer\trove_sanitize_font_base_line_height',
		'unit_sanitize_callback' => '\Trove\Customizer\trove_sanitize_font_base_line_height_unit',
	),
);
