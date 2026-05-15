<?php
/**
 * Customizer fields: Announcement Bar → settings (visibility, height, text size, animation, autoplay).
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

use function \Trove\Customizer\customizer_default;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_announcement_enabled' => array(
		'label'             => __( 'Show bar', 'trove' ),
		'description'       => __( 'Show or hide the bar for each breakpoint.', 'trove' ),
		'type'              => 'switch',
		'responsive'        => true,
		'default'           => customizer_default(
			'trove_announcement_enabled',
			array(
				'pc'     => true,
				'tablet' => true,
				'phone'  => true,
			)
		),
		'transport'         => 'postMessage',
		'sanitize_callback' => 'wp_validate_boolean',
		'priority'          => 10,
	),
	'trove_announcement_height' => array(
		'label'             => __( 'Height', 'trove' ),
		'description'       => __( 'Minimum height of the announcement bar per device. Unit applies to all breakpoints.', 'trove' ),
		'type'              => 'number',
		'responsive'        => true,
		'default'           => customizer_default(
			'trove_announcement_height',
			array(
				'pc'     => 48,
				'tablet' => 48,
				'phone'  => 48,
			)
		),
		'unit_setting'      => 'trove_announcement_height_unit',
		'unit_default'      => (string) customizer_default( 'trove_announcement_height_unit', 'px' ),
		'unit_choices'      => array(
			'px'  => 'px',
			'rem' => 'rem',
		),
		'unit_sanitize_callback' => function ( $value ) {
			$value = sanitize_key( (string) $value );
			return in_array( $value, array( 'px', 'rem' ), true ) ? $value : 'px';
		},
		'transport'         => 'postMessage',
		'priority'          => 12,
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
		'default'           => customizer_default(
			'trove_announcement_text_size',
			array(
				'pc'     => 'medium',
				'tablet' => 'medium',
				'phone'  => 'medium',
			)
		),
		'transport'         => 'postMessage',
		'priority'          => 14,
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
	'trove_announcement_divider' => array(
		'type' => 'heading',
		'label' => __( 'Carousel settings', 'trove' ),
		'priority' => 22,
	),
	'trove_announcement_autoplay' => array(
		'label'             => __( 'Auto play', 'trove' ),
		'type'              => 'switch',
		'default'           => (bool) customizer_default( 'trove_announcement_autoplay', false ),
		'transport'         => 'postMessage',
		'priority'          => 30,
		'sanitize_callback' => 'wp_validate_boolean',
	),
	'trove_announcement_autoplay_interval' => array(
		'label'             => __( 'Autoplay interval', 'trove' ),
		'description'       => __( 'Seconds between slides when auto play is on.', 'trove' ),
		'type'              => 'number',
		'default'           => (int) customizer_default( 'trove_announcement_autoplay_interval', 5 ),
		'transport'         => 'postMessage',
		'priority'          => 40,
		'input_attrs'       => array(
			'min'  => 2,
			'max'  => 60,
			'step' => 1,
		),
		'sanitize_callback' => function ( $value ) {
			$v = absint( $value );
			return max( 2, min( 60, $v ) );
		},
	),
	'trove_announcement_animation' => array(
		'label'             => __( 'Animation', 'trove' ),
		'type'              => 'select',
		'default'           => (string) customizer_default( 'trove_announcement_animation', 'slide' ),
		'transport'         => 'postMessage',
		'priority'          => 20,
		'choices'           => array(
			'slide' => __( 'Slide', 'trove' ),
			'fade'  => __( 'Fade', 'trove' ),
		),
		'sanitize_callback' => function ( $value ) {
			$value = sanitize_key( (string) $value );
			return in_array( $value, array( 'slide', 'fade' ), true ) ? $value : 'slide';
		},
	)
);
