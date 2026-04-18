<?php
/**
 * Customizer fields: Announcement Bar → styles.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_announcement_height' => array(
		'label'             => __( 'Height', 'trove' ),
		'description'       => __( 'Minimum height of the announcement bar per device. Unit applies to all breakpoints.', 'trove' ),
		'type'              => 'number',
		'responsive'        => true,
		'default'           => array(
			'pc'     => 48,
			'tablet' => 48,
			'phone'  => 48,
		),
		'unit_setting'      => 'trove_announcement_height_unit',
		'unit_default'      => 'px',
		'unit_choices'      => array(
			'px'  => 'px',
			'rem' => 'rem',
		),
		'unit_sanitize_callback' => function ( $value ) {
			$value = sanitize_key( (string) $value );
			return in_array( $value, array( 'px', 'rem' ), true ) ? $value : 'px';
		},
		'transport'         => 'postMessage',
		'priority'          => 10,
		'input_attrs'       => array(
			'min'  => 24,
			'max'  => 120,
			'step' => 1,
		),
		'sanitize_callback' => function ( $value ) {
			$n = absint( $value );
			return max( 24, min( 120, $n ) );
		},
	),
	'trove_announcement_text_size' => array(
		'label'             => __( 'Text size', 'trove' ),
		'description'       => __( 'Font size in the announcement bar for each breakpoint.', 'trove' ),
		'type'              => 'select',
		'responsive'        => true,
		'default'           => array(
			'pc'     => 'medium',
			'tablet' => 'medium',
			'phone'  => 'medium',
		),
		'transport'         => 'postMessage',
		'priority'          => 12,
		'choices'           => array(
			'small'  => __( 'Small', 'trove' ),
			'medium' => __( 'Medium', 'trove' ),
			'big'    => __( 'Big', 'trove' ),
			'large'  => __( 'Large', 'trove' ),
		),
		'sanitize_callback' => function ( $value ) {
			$value = sanitize_key( (string) $value );
			return in_array( $value, array( 'small', 'medium', 'big', 'large' ), true ) ? $value : 'medium';
		},
	),
	'trove_announcement_background' => array(
		'label'             => __( 'Background', 'trove' ),
		'description'       => __( 'Leave empty to use the default background for dark or light header.', 'trove' ),
		'type'              => 'color',
		'default'           => '',
		'transport'         => 'postMessage',
		'priority'          => 20,
		'swatch_hint'       => __( 'Announcement bar', 'trove' ),
	),
	'trove_announcement_text_color' => array(
		'label'             => __( 'Text color', 'trove' ),
		'description'       => __( 'Main text in the announcement bar. Leave empty for theme defaults.', 'trove' ),
		'type'              => 'color',
		'default'           => '',
		'transport'         => 'postMessage',
		'priority'          => 30,
		'swatch_hint'       => __( 'Announcement text', 'trove' ),
	),
	'trove_announcement_links'    => array(
		'type'          => 'color_pair',
		'label'         => __( 'Announcement links', 'trove' ),
		'description'   => __( 'Link and hover colors for links in the announcement bar. Leave empty for theme defaults.', 'trove' ),
		'transport'     => 'postMessage',
		'priority'      => 40,
		'swatch_hints'  => array(
			'left'  => __( 'Link', 'trove' ),
			'right' => __( 'Hover', 'trove' ),
		),
		'pair'          => array(
			'left'  => array(
				'setting' => 'trove_announcement_link_color',
				'default' => '',
			),
			'right' => array(
				'setting' => 'trove_announcement_link_hover_color',
				'default' => '',
			),
		),
	),
);
