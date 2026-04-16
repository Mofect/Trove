<?php
/**
 * Customizer fields: Typography section (root-level).
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

defined( 'ABSPATH' ) || exit;

$fields = array();

$scopes = apply_filters('trove_typography_options', array(
	'body'       => array(
		'group'               => __( 'Body typography', 'trove' ),
		'label'               => __( 'Body', 'trove' ),
		'family_setting'      => 'trove_google_font_family',
		'size_setting'        => 'trove_font_base_size',
		'size_unit_setting'   => 'trove_font_base_size_unit',
		'weight_setting'      => 'trove_font_base_weight',
		'line_setting'        => 'trove_font_base_line_height',
		'line_unit_setting'   => 'trove_font_base_line_height_unit',
		'family_default'      => 'Instrument Sans',
		'size'                => 16,
		'size_unit'           => 'px',
		'weight'              => 400,
		'line'                => 1.5,
		'line_unit'           => 'unitless',
		'family_description'  => __( 'Choose a Google Font or System UI. Fonts load from Google\'s CDN.', 'trove' ),
		'size_description'    => __( 'Set the value and unit on the same row.', 'trove' ),
		'weight_description'  => __( 'Choices match the weights available for the selected body font.', 'trove' ),
		'line_description'    => __( 'Set the value and unit on the same row.', 'trove' ),
		'weight_sanitize'     => '\Trove\Customizer\trove_sanitize_font_base_weight',
	),
	'menu'       => array(
		'group'               => __( 'Menu typography', 'trove' ),
		'label'               => __( 'Menu', 'trove' ),
		'family_setting'      => 'trove_menu_google_font_family',
		'size_setting'        => 'trove_menu_font_size',
		'size_unit_setting'   => 'trove_menu_font_size_unit',
		'weight_setting'      => 'trove_menu_font_weight',
		'line_setting'        => 'trove_menu_font_line_height',
		'line_unit_setting'   => 'trove_menu_font_line_height_unit',
		'family_default'      => 'Instrument Sans',
		'size'                => 16,
		'size_unit'           => 'px',
		'weight'              => 500,
		'line'                => 1.5,
		'line_unit'           => 'unitless',
		'weight_sanitize'     => '\Trove\Customizer\trove_sanitize_font_weight_loose',
	),
	'h1' => array(
		'group'          => __( 'H1-H6 typography', 'trove' ),
		'label'          => 'H1',
		'size'           => 2.5,
		'size_unit'      => 'rem',
		'weight'         => 600,
		'line'           => 1.2,
		'line_unit'      => 'unitless',
		'family_default' => 'Instrument Sans',
	),
	'h2' => array(
		'label'          => 'H2',
		'size'           => 2.0,
		'size_unit'      => 'rem',
		'weight'         => 600,
		'line'           => 1.2,
		'line_unit'      => 'unitless',
		'family_default' => 'Instrument Sans',
	),
	'h3' => array(
		'label'          => 'H3',
		'size'           => 1.75,
		'size_unit'      => 'rem',
		'weight'         => 600,
		'line'           => 1.2,
		'line_unit'      => 'unitless',
		'family_default' => 'Instrument Sans',
	),
	'h4' => array(
		'label'          => 'H4',
		'size'           => 1.5,
		'size_unit'      => 'rem',
		'weight'         => 600,
		'line'           => 1.2,
		'line_unit'      => 'unitless',
		'family_default' => 'Instrument Sans',
	),
	'h5' => array(
		'label'          => 'H5',
		'size'           => 1.25,
		'size_unit'      => 'rem',
		'weight'         => 600,
		'line'           => 1.3,
		'line_unit'      => 'unitless',
		'family_default' => 'Instrument Sans',
	),
	'h6' => array(
		'label'          => 'H6',
		'size'           => 1.0,
		'size_unit'      => 'rem',
		'weight'         => 600,
		'line'           => 1.3,
		'line_unit'      => 'unitless',
		'family_default' => 'Instrument Sans',
	),
	'blockquote' => array(
		'group'          => __( 'Blockquote typography', 'trove' ),
		'label'          => 'Blockquote',
		'size'           => 1.125,
		'size_unit'      => 'rem',
		'weight'         => 500,
		'line'           => 1.6,
		'line_unit'      => 'unitless',
		'family_default' => 'Playfair Display',
	),
	'accent' => array(
		'group'          => __( 'Accent Text', 'trove' ),
		'label'          => 'Accent text',
		'size'           => 1.125,
		'size_unit'      => 'rem',
		'weight'         => 500,
		'line'           => 1.6,
		'line_unit'      => 'unitless',
		'family_default' => 'Playfair Display',
	),
));
$current_group = '';
foreach ( $scopes as $id => $meta ) {
	$group = isset( $meta['group'] ) ? (string) $meta['group'] : $current_group;
	if ( '' !== $group && $group !== $current_group ) {
		$fields[ 'trove_typography_divider_' . sanitize_key( $id ) ] = array(
			'type'  => 'heading',
			'label' => $group,
		);
		$current_group = $group;
	}

	$label = isset( $meta['label'] ) ? (string) $meta['label'] : strtoupper( $id );
	$family_setting = isset( $meta['family_setting'] ) ? (string) $meta['family_setting'] : 'trove_' . $id . '_google_font_family';
	$size_setting   = isset( $meta['size_setting'] ) ? (string) $meta['size_setting'] : 'trove_' . $id . '_font_size';
	$size_unit      = isset( $meta['size_unit_setting'] ) ? (string) $meta['size_unit_setting'] : 'trove_' . $id . '_font_size_unit';
	$weight_setting = isset( $meta['weight_setting'] ) ? (string) $meta['weight_setting'] : 'trove_' . $id . '_font_weight';
	$line_setting   = isset( $meta['line_setting'] ) ? (string) $meta['line_setting'] : 'trove_' . $id . '_font_line_height';
	$line_unit      = isset( $meta['line_unit_setting'] ) ? (string) $meta['line_unit_setting'] : 'trove_' . $id . '_font_line_height_unit';
	$weight_sanitize = isset( $meta['weight_sanitize'] ) ? (string) $meta['weight_sanitize'] : '\Trove\Customizer\trove_sanitize_font_weight_loose';

	$fields[ $family_setting ] = array(
		'label'       => sprintf( __( '%s font family', 'trove' ), $label ),
		'description' => isset( $meta['family_description'] ) ? (string) $meta['family_description'] : '',
		'type'        => 'google_font',
		'default'     => isset( $meta['family_default'] ) ? (string) $meta['family_default'] : 'system',
		'transport'   => 'postMessage',
	);
	$fields[ $size_setting ] = array(
		'label'                  => sprintf( __( '%s font size', 'trove' ), $label ),
		'description'            => isset( $meta['size_description'] ) ? (string) $meta['size_description'] : '',
		'type'                   => 'number_unit',
		'unit_setting'           => $size_unit,
		'number_default'         => $meta['size'],
		'unit_default'           => $meta['size_unit'],
		'transport'              => 'postMessage',
		'input_attrs'            => array( 'min' => 0.5, 'max' => 32, 'step' => 0.0625 ),
		'unit_choices'           => array( 'px' => 'px', 'rem' => 'rem', 'em' => 'em' ),
		'sanitize_callback'      => '\Trove\Customizer\trove_sanitize_font_base_size',
		'unit_sanitize_callback' => '\Trove\Customizer\trove_sanitize_font_base_size_unit',
	);
	$fields[ $weight_setting ] = array(
		'label'             => sprintf( __( '%s font weight', 'trove' ), $label ),
		'description'       => isset( $meta['weight_description'] ) ? (string) $meta['weight_description'] : '',
		'type'              => 'select',
		'default'           => (string) $meta['weight'],
		'transport'         => 'postMessage',
		'choices'           => array( '400' => __( '400 — Regular', 'trove' ) ),
		'sanitize_callback' => $weight_sanitize,
	);
	$fields[ $line_setting ] = array(
		'label'                  => sprintf( __( '%s line height', 'trove' ), $label ),
		'description'            => isset( $meta['line_description'] ) ? (string) $meta['line_description'] : '',
		'type'                   => 'number_unit',
		'unit_setting'           => $line_unit,
		'number_default'         => $meta['line'],
		'unit_default'           => $meta['line_unit'],
		'transport'              => 'postMessage',
		'input_attrs'            => array( 'min' => 0.5, 'max' => 250, 'step' => 0.05 ),
		'unit_choices'           => array(
			'unitless' => __( 'Unitless', 'trove' ),
			'px'       => 'px',
			'rem'      => 'rem',
			'em'       => 'em',
			'%'        => '%',
		),
		'sanitize_callback'      => '\Trove\Customizer\trove_sanitize_font_base_line_height',
		'unit_sanitize_callback' => '\Trove\Customizer\trove_sanitize_font_base_line_height_unit',
	);
}

return $fields;
