<?php
/**
 * Customizer fields: Footer section.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_footer_credit_text' => array(
		'label'             => __( 'Extra footer line (optional)', 'trove' ),
		'description'       => __( 'Shown below the default copyright if not empty.', 'trove' ),
		'type'              => 'text',
		'responsive'        => true,
		'default'           => array(
			'pc'     => '',
			'tablet' => '',
			'phone'  => '',
		),
		'sanitize_callback' => 'sanitize_text_field',
	),
);
