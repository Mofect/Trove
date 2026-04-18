<?php
/**
 * Customizer fields: Announcement Bar → settings.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_announcement_enabled' => array(
		'label'             => __( 'Show bar', 'trove' ),
		'description'       => __( 'Show or hide the bar for each breakpoint.', 'trove' ),
		'type'              => 'switch',
		'responsive'        => true,
		'default'           => array(
			'pc'     => true,
			'tablet' => true,
			'phone'  => true,
		),
		'transport'         => 'postMessage',
		'sanitize_callback' => 'wp_validate_boolean',
		'priority'          => 10,
	),
	'trove_announcement_autoplay' => array(
		'label'             => __( 'Auto play', 'trove' ),
		'type'              => 'switch',
		'default'           => false,
		'transport'         => 'postMessage',
		'priority'          => 30,
		'sanitize_callback' => 'wp_validate_boolean',
	),
	'trove_announcement_autoplay_interval' => array(
		'label'             => __( 'Autoplay interval', 'trove' ),
		'description'       => __( 'Seconds between slides when auto play is on.', 'trove' ),
		'type'              => 'number',
		'default'           => 5,
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
		'default'           => 'slide',
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
