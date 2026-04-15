<?php
/**
 * Customizer fields: Performance section.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_perf_placeholder' => array(
		'label'       => __( 'Options', 'trove' ),
		'description' => __( 'Add performance-related settings here as needed.', 'trove' ),
		'type'        => 'text',
		'default'     => '',
	),
);
