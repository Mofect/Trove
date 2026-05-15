<?php
/**
 * Customizer fields: Woocommerce > Message bar.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

use function \Trove\Customizer\customizer_default;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_cz_heading_woo_message_info' => array(
		'type'     => 'heading',
		'label'    => __( 'Info message', 'trove' ),
		'priority' => 1,
	),
	'trove_woo_info_message_background' => array(
		'label'             => __( 'Background', 'trove' ),
		'type'              => 'color',
		'default'           => customizer_default( 'trove_woo_info_message_background', '#eef4ff' ),
		'priority'          => 2,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	),
	'trove_woo_info_message_text' => array(
		'label'             => __( 'Text', 'trove' ),
		'type'              => 'color',
		'default'           => customizer_default( 'trove_woo_info_message_text', '#1f3f8f' ),
		'priority'          => 3,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	),
	'trove_cz_heading_woo_message_failed' => array(
		'type'     => 'heading',
		'label'    => __( 'Failed message', 'trove' ),
		'priority' => 4,
	),
	'trove_woo_failed_message_background' => array(
		'label'             => __( 'Background', 'trove' ),
		'type'              => 'color',
		'default'           => customizer_default( 'trove_woo_failed_message_background', '#ffe8ee' ),
		'priority'          => 5,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	),
	'trove_woo_failed_message_text' => array(
		'label'             => __( 'Text', 'trove' ),
		'type'              => 'color',
		'default'           => customizer_default( 'trove_woo_failed_message_text', '#8b1f43' ),
		'priority'          => 6,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	),
	'trove_cz_heading_woo_message_success' => array(
		'type'     => 'heading',
		'label'    => __( 'Success message', 'trove' ),
		'priority' => 7,
	),
	'trove_woo_success_message_background' => array(
		'label'             => __( 'Background', 'trove' ),
		'type'              => 'color',
		'default'           => customizer_default( 'trove_woo_success_message_background', '#e9f8ec' ),
		'priority'          => 8,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	),
	'trove_woo_success_message_text' => array(
		'label'             => __( 'Text', 'trove' ),
		'type'              => 'color',
		'default'           => customizer_default( 'trove_woo_success_message_text', '#216e39' ),
		'priority'          => 9,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	),
);
