<?php
/**
 * Customizer fields: Header → advanced features.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_header_sticky' => array(
		'label'             => __( 'Sticky header', 'trove' ),
		'type'              => 'select',
		'default'           => 'none',
		'transport'         => 'postMessage',
		'choices'           => array(
			'none'         => __( 'None', 'trove' ),
			'on_scroll_up' => __( 'On scroll up', 'trove' ),
			'always'       => __( 'Always Sticky', 'trove' ),
		),
		'sanitize_callback' => function ( $value ) {
			$allowed = array( 'none', 'on_scroll_up', 'always' );
			$value   = sanitize_key( (string) $value );
			return in_array( $value, $allowed, true ) ? $value : 'none';
		},
	),
);
