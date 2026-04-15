<?php
/**
 * Customizer fields: Header → Layout subsection.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_header_layout' => array(
		'label'       => __( 'Header layout', 'trove' ),
		'description' => __( 'Image radio control — pick a layout thumbnail.', 'trove' ),
		'type'        => 'image_radio',
		'default'     => 'horizontal-left-logo',
		'choices'     => array(
			'horizontal-left-logo' => array(
				'label' => __( 'Left logo', 'trove' ),
				'url'   => 'https://lh3.googleusercontent.com/75MNgqxGh5M4JP_e5i5TxFrMvtYfK9x0vNuZj964jdMg2AfFkSmFteslLOrkELWgECnj5T1aYKBJ74I1lGlT-A',
			),
			'horizontal-centered-logo' => array(
				'label' => __( 'Centered logo', 'trove' ),
				'url'   => 'https://www.gstatic.com/acx/play/console//brt/play_console_ui_20260412_2240_RC00/main/play.console.ui.common.images/promotional_content/play_console_new_tools_image.png',
			),
			'horizontal-right-logo' => array(
				'label' => __( 'Right logo', 'trove' ),
				'url'   => 'https://www.gstatic.com/acx/play/console//brt/play_console_ui_20260412_2240_RC00/main/play.console.ui.common.images/promotional_content/play_console_new_tools_image.png',
			)
		),
		'sanitize_callback' => 'sanitize_text_field',
	),
	'trove_header_bottom_border' => array(
		'label'             => __( 'Show bottom border', 'trove' ),
		'type'              => 'switch',
		'default'           => false,
		'sanitize_callback' => 'wp_validate_boolean',
	),
);
