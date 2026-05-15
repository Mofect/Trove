<?php
/**
 * Customizer fields: Typography section (root-level).
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

use function \Trove\Customizer\customizer_default;

defined( 'ABSPATH' ) || exit;

$fields = array();
$default_letter_spacing = array(
	'body'       => array( 'value' => 0, 'unit' => 'em' ),
	'menu'       => array( 'value' => -0.02, 'unit' => 'em' ),
	'h1'         => array( 'value' => -0.15, 'unit' => 'rem' ),
	'h2'         => array( 'value' => -0.07, 'unit' => 'rem' ),
	'h3'         => array( 'value' => -0.02, 'unit' => 'rem' ),
	'h4'         => array( 'value' => -0.01, 'unit' => 'rem' ),
	'h5'         => array( 'value' => -0.01, 'unit' => 'rem' ),
	'h6'         => array( 'value' => -0.01, 'unit' => 'rem' ),
	'blockquote' => array( 'value' => 0, 'unit' => 'em' ),
	'accent'     => array( 'value' => 0, 'unit' => 'em' ),
);

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
		'weight_sanitize'     => '\Trove\Customizer\sanitize_font_base_weight',
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
		'weight_sanitize'     => '\Trove\Customizer\sanitize_font_weight_loose',
	),
	'h1' => array(
		'group'          => __( 'H1-H6 typography', 'trove' ),
		'label'          => 'H1',
		'size'           => 4.0,
		'size_unit'      => 'rem',
		'weight'         => 600,
		'line'           => 1.1,
		'line_unit'      => 'unitless',
		'family_default' => 'Instrument Sans',
	),
	'h2' => array(
		'label'          => 'H2',
		'size'           => 3.0,
		'size_unit'      => 'rem',
		'weight'         => 600,
		'line'           => 1.2,
		'line_unit'      => 'unitless',
		'family_default' => 'Instrument Sans',
	),
	'h3' => array(
		'label'          => 'H3',
		'size'           => 2.4,
		'size_unit'      => 'rem',
		'weight'         => 600,
		'line'           => 1.2,
		'line_unit'      => 'unitless',
		'family_default' => 'Instrument Sans',
	),
	'h4' => array(
		'label'          => 'H4',
		'size'           => 2.2,
		'size_unit'      => 'rem',
		'weight'         => 600,
		'line'           => 1.2,
		'line_unit'      => 'unitless',
		'family_default' => 'Instrument Sans',
	),
	'h5' => array(
		'label'          => 'H5',
		'size'           => 2.0,
		'size_unit'      => 'rem',
		'weight'         => 600,
		'line'           => 1.2,
		'line_unit'      => 'unitless',
		'family_default' => 'Instrument Sans',
	),
	'h6' => array(
		'label'          => 'H6',
		'size'           => 1.8,
		'size_unit'      => 'rem',
		'weight'         => 600,
		'line'           => 1.2,
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

	if ( 'h1' !== $id && 'accent' !== $id && 'blockquote' !== $id && 'body' !== $id && 'menu' !== $id ) {
		$fields['trove_typography_divider_' . sanitize_key( $id ) ] = array(
			'type' => 'divider',
		);
	}

	$label = isset( $meta['label'] ) ? (string) $meta['label'] : strtoupper( $id );
	$family_setting = isset( $meta['family_setting'] ) ? (string) $meta['family_setting'] : 'trove_' . $id . '_google_font_family';
	$size_setting   = isset( $meta['size_setting'] ) ? (string) $meta['size_setting'] : 'trove_' . $id . '_font_size';
	$size_unit      = isset( $meta['size_unit_setting'] ) ? (string) $meta['size_unit_setting'] : 'trove_' . $id . '_font_size_unit';
	$weight_setting = isset( $meta['weight_setting'] ) ? (string) $meta['weight_setting'] : 'trove_' . $id . '_font_weight';
	$line_setting   = isset( $meta['line_setting'] ) ? (string) $meta['line_setting'] : 'trove_' . $id . '_font_line_height';
	$line_unit      = isset( $meta['line_unit_setting'] ) ? (string) $meta['line_unit_setting'] : 'trove_' . $id . '_font_line_height_unit';
	$letter_setting = isset( $meta['letter_setting'] ) ? (string) $meta['letter_setting'] : 'trove_' . $id . '_font_letter_spacing';
	$letter_unit    = isset( $meta['letter_unit_setting'] ) ? (string) $meta['letter_unit_setting'] : 'trove_' . $id . '_font_letter_spacing_unit';
	$weight_sanitize = isset( $meta['weight_sanitize'] ) ? (string) $meta['weight_sanitize'] : '\Trove\Customizer\sanitize_font_weight_loose';
	$letter_default = isset( $default_letter_spacing[ $id ]['value'] ) ? $default_letter_spacing[ $id ]['value'] : 0;
	$letter_unit_default = isset( $default_letter_spacing[ $id ]['unit'] ) ? $default_letter_spacing[ $id ]['unit'] : 'em';
	$is_heading_scope = in_array( $id, array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ), true );
	$family_label = $is_heading_scope ? sprintf( __( '%s Font family', 'trove' ), $label ) : __( 'Font family', 'trove' );
	$weight_label = $is_heading_scope ? sprintf( __( '%s Font weight', 'trove' ), $label ) : __( 'Font weight', 'trove' );
	$size_label = $is_heading_scope ? sprintf( __( '%s Font size', 'trove' ), $label ) : __( 'Font size', 'trove' );
	$line_label = $is_heading_scope ? sprintf( __( '%s Line height', 'trove' ), $label ) : __( 'Line height', 'trove' );
	$letter_label = $is_heading_scope ? sprintf( __( '%s Letter spacing', 'trove' ), $label ) : __( 'Letter spacing', 'trove' );

	$fields[ $family_setting ] = array(
		'label'       => $family_label,
		'description' => isset( $meta['family_description'] ) ? (string) $meta['family_description'] : '',
		'type'        => 'google_font',
		'default'     => (string) customizer_default(
			$family_setting,
			isset( $meta['family_default'] ) ? (string) $meta['family_default'] : 'system'
		),
		'transport'   => 'postMessage',
	);
	$fields[ $weight_setting ] = array(
		'label'             => $weight_label,
		'description'       => isset( $meta['weight_description'] ) ? (string) $meta['weight_description'] : '',
		'type'              => 'select',
		'default'           => (string) customizer_default( $weight_setting, (string) $meta['weight'] ),
		'transport'         => 'postMessage',
		'choices'           => array( '400' => __( '400 — Regular', 'trove' ) ),
		'sanitize_callback' => $weight_sanitize,
	);
	$fields[ $size_setting ] = array(
		'label'                  => $size_label,
		'description'            => isset( $meta['size_description'] ) ? (string) $meta['size_description'] : '',
		'type'                   => 'number',
		'responsive'             => true,
		'unit_setting'           => $size_unit,
		'default'                => array(
			'pc'     => customizer_default( $size_setting . '_pc', customizer_default( $size_setting, $meta['size'] ) ),
			'tablet' => customizer_default( $size_setting . '_tablet', customizer_default( $size_setting, $meta['size'] ) ),
			'phone'  => customizer_default( $size_setting . '_phone', customizer_default( $size_setting, $meta['size'] ) ),
		),
		'unit_default'           => (string) customizer_default( $size_unit, $meta['size_unit'] ),
		'transport'              => 'postMessage',
		'input_attrs'            => array( 'min' => 0.5, 'max' => 32, 'step' => 0.0625 ),
		'unit_choices'           => array( 'px' => 'px', 'rem' => 'rem', 'em' => 'em' ),
		'sanitize_callback'      => '\Trove\Customizer\sanitize_font_base_size',
		'unit_sanitize_callback' => '\Trove\Customizer\sanitize_font_base_size_unit',
	);
	$fields[ $line_setting ] = array(
		'label'                  => $line_label,
		'description'            => isset( $meta['line_description'] ) ? (string) $meta['line_description'] : '',
		'type'                   => 'number',
		'responsive'             => true,
		'unit_setting'           => $line_unit,
		'default'                => array(
			'pc'     => customizer_default( $line_setting . '_pc', customizer_default( $line_setting, $meta['line'] ) ),
			'tablet' => customizer_default( $line_setting . '_tablet', customizer_default( $line_setting, $meta['line'] ) ),
			'phone'  => customizer_default( $line_setting . '_phone', customizer_default( $line_setting, $meta['line'] ) ),
		),
		'unit_default'           => (string) customizer_default( $line_unit, $meta['line_unit'] ),
		'transport'              => 'postMessage',
		'input_attrs'            => array( 'min' => 0.5, 'max' => 250, 'step' => 0.05 ),
		'unit_choices'           => array(
			'unitless' => __( 'Unitless', 'trove' ),
			'px'       => 'px',
			'rem'      => 'rem',
			'em'       => 'em',
			'%'        => '%',
		),
		'sanitize_callback'      => '\Trove\Customizer\sanitize_font_base_line_height',
		'unit_sanitize_callback' => '\Trove\Customizer\sanitize_font_base_line_height_unit',
	);
	$fields[ $letter_setting ] = array(
		'label'                  => $letter_label,
		'type'                   => 'number',
		'responsive'             => true,
		'unit_setting'           => $letter_unit,
		'default'                => array(
			'pc'     => customizer_default( $letter_setting . '_pc', customizer_default( $letter_setting, $letter_default ) ),
			'tablet' => customizer_default( $letter_setting . '_tablet', customizer_default( $letter_setting, $letter_default ) ),
			'phone'  => customizer_default( $letter_setting . '_phone', customizer_default( $letter_setting, $letter_default ) ),
		),
		'unit_default'           => (string) customizer_default( $letter_unit, $letter_unit_default ),
		'transport'              => 'postMessage',
		'input_attrs'            => array( 'min' => -10, 'max' => 10, 'step' => 0.01 ),
		'unit_choices'           => array( 'px' => 'px', 'rem' => 'rem', 'em' => 'em' ),
		'sanitize_callback'      => '\Trove\Customizer\sanitize_font_letter_spacing',
		'unit_sanitize_callback' => '\Trove\Customizer\sanitize_font_letter_spacing_unit',
	);
}

return $fields;
