<?php
/**
 * Registers Trove Customizer sections (root-level, alongside Site Identity and other core sections).
 *
 * @package Trove
 */

namespace Trove\Customizer;

use Trove\Customizer\Config;
use WP_Customize_Manager;

/**
 * Orchestrates Options_Registry.
 */
final class Manager {

	/**
	 * @param WP_Customize_Manager $wp_customize Customizer instance.
	 */
	public function register( $wp_customize ) {
		if ( ! $wp_customize instanceof WP_Customize_Manager ) {
			return;
		}

		// WP_Customize_Control is only loaded in Customizer context; defer this file until then.
		if ( ! class_exists( '\WP_Customize_Control', false ) ) {
			require_once ABSPATH . WPINC . '/class-wp-customize-control.php';
		}
		if ( ! class_exists( '\WP_Customize_Section', false ) ) {
			$section_class = ABSPATH . WPINC . '/customize/class-wp-customize-section.php';
			if ( is_readable( $section_class ) ) {
				require_once $section_class;
			} else {
				require_once ABSPATH . WPINC . '/class-wp-customize-section.php';
			}
		}
		require_once __DIR__ . '/class-group-title-section.php';
		$wp_customize->register_section_type( '\Trove\Customizer\Group_Title_Section' );

		require_once __DIR__ . '/controls/class-responsive-tabs-control.php';
		require_once __DIR__ . '/controls/class-section-tabs-control.php';
		require_once __DIR__ . '/controls/class-switch-control.php';
		require_once __DIR__ . '/controls/class-image-radio-control.php';
		require_once __DIR__ . '/controls/class-group-marker-control.php';
		require_once __DIR__ . '/controls/class-dual-color-control.php';
		require_once __DIR__ . '/controls/class-heading-control.php';

		// Do not call register_control_type( Responsive_Tabs_Control ): that prints an empty JS
		// tmpl and customize-controls.js replaces server-rendered HTML with it, hiding the UI.

		$config   = Config\get_options();
		$registry = new Options_Registry();
		$registry->register( $wp_customize, $config );
	}
}
