<?php
/**
 * Trove Customizer bootstrap — declarative config (Blocksy-style) + registry.
 *
 * @package Trove
 */

namespace Trove\Customizer;

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/config/options.php';
require_once __DIR__ . '/google-fonts.php';
require_once __DIR__ . '/responsive.php';
require_once __DIR__ . '/class-options-registry.php';
require_once __DIR__ . '/class-manager.php';

/**
 * Registers the Customizer tree on customize_register.
 */
function bootstrap_customizer() {
	$manager = new Manager();
	add_action( 'customize_register', array( $manager, 'register' ), 10 );
	add_action( 'customize_register', __NAMESPACE__ . '\\flatten_woocommerce_panel_to_root', 9997 );
	add_action( 'customize_register', __NAMESPACE__ . '\\position_woocommerce_below_blog_posts', 9998 );
	add_action( 'customize_register', __NAMESPACE__ . '\\reorder_core_sections_above_additional_css', 9999 );
	add_action( 'customize_controls_enqueue_scripts', __NAMESPACE__ . '\\enqueue_customizer_ui_assets' );
}
bootstrap_customizer();

/**
 * Places Site Identity, Menus, and Homepage Settings directly above Additional CSS.
 *
 * Menus is a Customizer panel (`nav_menus`), not a section — it must use get_panel().
 * Menus priority is always Site Identity + 1 so it sits directly below Site Identity.
 *
 * Relies on the Additional CSS section priority as an anchor so this stays correct across WP versions.
 *
 * @param \WP_Customize_Manager $wp_customize Customizer manager.
 * @return void
 */
function reorder_core_sections_above_additional_css( $wp_customize ) {
	if ( ! $wp_customize instanceof \WP_Customize_Manager ) {
		return;
	}

	$css = $wp_customize->get_section( 'custom_css' );
	if ( ! $css ) {
		return;
	}

	$base = (int) $css->priority;

	$title = $wp_customize->get_section( 'title_tagline' );
	if ( $title ) {
		$title->priority = $base - 3;
	}

	$menus_panel = $wp_customize->get_panel( 'nav_menus' );
	if ( $menus_panel && $title ) {
		$menus_panel->priority = (int) $title->priority + 1;
	} elseif ( $menus_panel ) {
		$menus_panel->priority = $base - 2;
	}

	$home = $wp_customize->get_section( 'static_front_page' );
	if ( $home ) {
		$home->priority = $base - 1;
	}
}

/**
 * Global: section ids that were under the WooCommerce panel, sorted by original priority (Customizer runs once per request).
 *
 * @var list<string>
 */
function set_woocommerce_flattened_section_ids( array $ids ) {
	$GLOBALS['trove_customizer_woo_root_section_ids'] = $ids;
}

/**
 * @return list<string>
 */
function get_woocommerce_flattened_section_ids() {
	return isset( $GLOBALS['trove_customizer_woo_root_section_ids'] )
		? (array) $GLOBALS['trove_customizer_woo_root_section_ids']
		: array();
}

/**
 * Moves every section from the WooCommerce panel to the root sidebar and removes the empty panel.
 *
 * Order is preserved via {@see get_woocommerce_flattened_section_ids()} for {@see position_woocommerce_below_blog_posts()}.
 *
 * @param \WP_Customize_Manager $wp_customize Customizer manager.
 * @return void
 */
function flatten_woocommerce_panel_to_root( $wp_customize ) {
	static $done = false;
	if ( $done ) {
		return;
	}

	if ( ! $wp_customize instanceof \WP_Customize_Manager ) {
		$done = true;
		return;
	}

	$panel = $wp_customize->get_panel( 'woocommerce' );
	if ( ! $panel ) {
		set_woocommerce_flattened_section_ids( array() );
		$done = true;
		return;
	}

	$priorities = array();
	foreach ( $wp_customize->sections() as $id => $section ) {
		if ( 'woocommerce' !== $section->panel ) {
			continue;
		}
		$priorities[ (string) $id ] = (int) $section->priority;
		$section->panel             = '';
	}

	if ( empty( $priorities ) ) {
		set_woocommerce_flattened_section_ids( array() );
		$wp_customize->remove_panel( 'woocommerce' );
		$done = true;
		return;
	}

	uasort(
		$priorities,
		static function ( $a, $b ) {
			if ( $a === $b ) {
				return 0;
			}
			return ( $a < $b ) ? -1 : 1;
		}
	);

	$ordered = array_keys( $priorities );
	/**
	 * Section ids moved from the WooCommerce panel to the root (already sorted by original priority).
	 *
	 * @param list<string> $ordered Section ids.
	 */
	$ordered = apply_filters( 'trove_customizer_woocommerce_flattened_section_ids', $ordered );

	set_woocommerce_flattened_section_ids( $ordered );

	$wp_customize->remove_panel( 'woocommerce' );
	$done = true;
}

/**
 * Places Shop group title and WooCommerce sections directly below Blog Posts (root-level WC sections after {@see flatten_woocommerce_panel_to_root()}).
 *
 * Order: Shop group title → legacy Trove WooCommerce heading (if any) → Trove WooCommerce section (if any) → former WC panel sections.
 *
 * @param \WP_Customize_Manager $wp_customize Customizer manager.
 * @return void
 */
function position_woocommerce_below_blog_posts( $wp_customize ) {
	if ( ! $wp_customize instanceof \WP_Customize_Manager ) {
		return;
	}

	$blog = $wp_customize->get_section( 'trove_section_blog' );
	if ( ! $blog ) {
		return;
	}

	$base = (int) $blog->priority + 1;
	$n    = 0;

	$shop_gt = $wp_customize->get_section( 'trove_gt_shop_group_title' );
	if ( $shop_gt ) {
		$shop_gt->priority = $base + $n;
		++$n;
	}

	$trove_gt = $wp_customize->get_section( 'trove_gt_woocommerce_group_title' );
	if ( $trove_gt ) {
		$trove_gt->priority = $base + $n;
		++$n;
	}

	$trove_wc = $wp_customize->get_section( 'trove_section_woocommerce_general' );
	if ( $trove_wc ) {
		$trove_wc->priority = $base + $n;
		++$n;
	}

	foreach ( get_woocommerce_flattened_section_ids() as $section_id ) {
		$sec = $wp_customize->get_section( $section_id );
		if ( $sec ) {
			$sec->priority = $base + $n;
			++$n;
		}
	}
}

/**
 * Swatch hover labels for color controls (Global Colors + e.g. Footer → Styles).
 *
 * @return array<string, string|array{left:string, right:string}>
 */
function get_color_swatch_hints_export() {
	$path = __DIR__ . '/config/general/colors.php';
	if ( ! is_readable( $path ) ) {
		return array();
	}
	$config = require $path;
	if ( ! is_array( $config ) ) {
		return array();
	}
	$out = array();
	foreach ( $config as $control_id => $field ) {
		if ( ! is_array( $field ) || empty( $field['type'] ) ) {
			continue;
		}
		if ( 'color' === $field['type'] && ! empty( $field['swatch_hint'] ) ) {
			$out[ $control_id ] = $field['swatch_hint'];
			continue;
		}
		if ( 'color_pair' === $field['type'] && ! empty( $field['swatch_hints'] ) && is_array( $field['swatch_hints'] ) ) {
			$out[ $control_id ] = array(
				'left'  => isset( $field['swatch_hints']['left'] ) ? $field['swatch_hints']['left'] : '',
				'right' => isset( $field['swatch_hints']['right'] ) ? $field['swatch_hints']['right'] : '',
			);
		}
	}

	$footer_styles_path = __DIR__ . '/config/footer/styles.php';
	if ( is_readable( $footer_styles_path ) ) {
		$footer_styles = require $footer_styles_path;
		if ( is_array( $footer_styles ) ) {
			foreach ( $footer_styles as $control_id => $field ) {
				if ( ! is_array( $field ) || empty( $field['type'] ) ) {
					continue;
				}
				if ( 'color' === $field['type'] && ! empty( $field['swatch_hint'] ) ) {
					$out[ $control_id ] = $field['swatch_hint'];
					continue;
				}
				if ( 'color_pair' === $field['type'] && ! empty( $field['swatch_hints'] ) && is_array( $field['swatch_hints'] ) ) {
					$out[ $control_id ] = array(
						'left'  => isset( $field['swatch_hints']['left'] ) ? $field['swatch_hints']['left'] : '',
						'right' => isset( $field['swatch_hints']['right'] ) ? $field['swatch_hints']['right'] : '',
					);
				}
			}
		}
	}

	$header_styles_path = __DIR__ . '/config/header/styles.php';
	if ( is_readable( $header_styles_path ) ) {
		$header_styles = require $header_styles_path;
		if ( is_array( $header_styles ) ) {
			foreach ( $header_styles as $control_id => $field ) {
				if ( ! is_array( $field ) || empty( $field['type'] ) ) {
					continue;
				}
				if ( 'color' === $field['type'] && ! empty( $field['swatch_hint'] ) ) {
					$out[ $control_id ] = $field['swatch_hint'];
					continue;
				}
				if ( 'color_pair' === $field['type'] && ! empty( $field['swatch_hints'] ) && is_array( $field['swatch_hints'] ) ) {
					$out[ $control_id ] = array(
						'left'  => isset( $field['swatch_hints']['left'] ) ? $field['swatch_hints']['left'] : '',
						'right' => isset( $field['swatch_hints']['right'] ) ? $field['swatch_hints']['right'] : '',
					);
				}
			}
		}
	}

	return $out;
}

/**
 * Scripts and styles for Trove Customizer controls (responsive device tabs + section tabs).
 *
 * @return void
 */
function enqueue_customizer_ui_assets() {
	$ver = defined( 'TROVE_THEME_VERSION' ) ? TROVE_THEME_VERSION : '1.0.0';
	$dir = get_template_directory_uri();

	wp_enqueue_style(
		'trove-customizer-responsive-tabs',
		$dir . '/inc/customizer/css/customizer-responsive-tabs.css',
		array(),
		$ver
	);

	wp_enqueue_style(
		'trove-customizer-section-tabs',
		$dir . '/inc/customizer/css/customizer-section-tabs.css',
		array(),
		$ver
	);

	wp_enqueue_style(
		'trove-customizer-switch',
		$dir . '/inc/customizer/css/customizer-switch.css',
		array(),
		$ver
	);

	wp_enqueue_style(
		'trove-customizer-image-radio',
		$dir . '/inc/customizer/css/customizer-image-radio.css',
		array(),
		$ver
	);

	wp_enqueue_style(
		'trove-customizer-alignment-control',
		$dir . '/inc/customizer/css/customizer-alignment-control.css',
		array(),
		$ver
	);

	wp_enqueue_style(
		'trove-customizer-panel',
		$dir . '/inc/customizer/css/customizer-panel.css',
		array( 'customize-controls' ),
		$ver
	);

	wp_enqueue_style(
		'trove-customizer-inline-heading',
		$dir . '/inc/customizer/css/customizer-inline-heading.css',
		array( 'customize-controls' ),
		$ver
	);

	wp_enqueue_style(
		'trove-customizer-color-swatches',
		$dir . '/inc/customizer/css/customizer-color-swatches.css',
		array( 'customize-controls', 'wp-color-picker' ),
		$ver
	);

	wp_register_style(
		'select2',
		'https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css',
		array(),
		'4.0.13'
	);
	wp_enqueue_style( 'select2' );

	wp_enqueue_style(
		'trove-customizer-google-font',
		$dir . '/inc/customizer/css/customizer-google-font.css',
		array( 'customize-controls', 'select2' ),
		$ver
	);

	wp_enqueue_style(
		'trove-customizer-number-unit',
		$dir . '/inc/customizer/css/customizer-number-unit.css',
		array( 'customize-controls' ),
		$ver
	);

	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_style( 'wp-components' );
	wp_enqueue_script( 'wp-element' );
	wp_enqueue_script( 'wp-components' );

	wp_enqueue_script(
		'trove-customizer-responsive-tabs',
		$dir . '/inc/customizer/js/customizer-responsive-tabs.js',
		array( 'customize-controls', 'wp-color-picker' ),
		$ver,
		true
	);

	wp_enqueue_script(
		'trove-customizer-section-tabs',
		$dir . '/inc/customizer/js/customizer-section-tabs.js',
		array( 'customize-controls', 'trove-customizer-responsive-tabs' ),
		$ver,
		true
	);

	wp_enqueue_script(
		'trove-customizer-color-schemes-menu',
		$dir . '/inc/customizer/js/customizer-color-schemes-menu.js',
		array( 'customize-controls' ),
		$ver,
		true
	);

	wp_register_script(
		'select2',
		'https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js',
		array( 'jquery' ),
		'4.0.13',
		true
	);
	wp_enqueue_script( 'select2' );

	wp_enqueue_script(
		'trove-customizer-google-font-select2',
		$dir . '/inc/customizer/js/customizer-google-font-select2.js',
		array( 'jquery', 'customize-controls', 'select2' ),
		$ver,
		true
	);

	wp_localize_script(
		'trove-customizer-google-font-select2',
		'troveGoogleFontSelect2',
		array(
			'families' => \Trove\Customizer\get_google_font_family_names(),
			'i18n'     => array(
				'search' => __( 'Search…', 'trove' ),
				'system' => __( 'System UI', 'trove' ),
			),
		)
	);

	wp_enqueue_script(
		'trove-customizer-font-weight',
		$dir . '/inc/customizer/js/customizer-font-weight.js',
		array( 'jquery', 'customize-controls', 'trove-customizer-google-font-select2' ),
		$ver,
		true
	);

	wp_enqueue_script(
		'trove-customizer-header-menu-alignment',
		$dir . '/inc/customizer/js/customizer-header-menu-alignment.js',
		array( 'customize-controls' ),
		$ver,
		true
	);

	wp_localize_script(
		'trove-customizer-font-weight',
		'troveFontWeightMap',
		array(
			'map'    => \Trove\Customizer\get_font_weight_map_for_customizer(),
			'labels' => \Trove\Customizer\trove_font_weight_choice_labels(),
			'pairs'  => trove_font_weight_setting_pairs(),
		)
	);

	wp_localize_script(
		'trove-customizer-color-schemes-menu',
		'troveCzColorSchemes',
		array(
			'keys'       => trove_global_color_scheme_setting_keys(),
			'storageKey' => 'trove_wp_custom_color_schemes_u' . get_current_user_id(),
			'i18n'       => array(
				'promptName'    => __( 'Name this color scheme', 'trove' ),
				'emptyName'     => __( 'Please enter a name.', 'trove' ),
				'deleteScheme'  => __( 'Remove color scheme', 'trove' ),
				'confirmDelete' => __( 'Delete this color scheme?', 'trove' ),
			),
		)
	);

	wp_enqueue_script(
		'trove-customizer-color-pair',
		$dir . '/inc/customizer/js/customizer-color-pair.js',
		array( 'customize-controls', 'wp-color-picker' ),
		$ver,
		true
	);

	wp_enqueue_script(
		'trove-customizer-swatch-hints',
		$dir . '/inc/customizer/js/customizer-swatch-hints.js',
		array( 'customize-controls', 'trove-customizer-color-pair' ),
		$ver,
		true
	);

	wp_localize_script(
		'trove-customizer-swatch-hints',
		'troveCzSwatchHints',
		array(
			'hints' => get_color_swatch_hints_export(),
		)
	);

	wp_enqueue_script(
		'trove-customizer-color-popover',
		$dir . '/inc/customizer/js/customizer-color-popover.js',
		array( 'customize-controls', 'wp-color-picker', 'trove-customizer-swatch-hints' ),
		$ver,
		true
	);

	wp_enqueue_script(
		'trove-customizer-gutenberg-color-picker',
		$dir . '/inc/customizer/js/customizer-gutenberg-color-picker.js',
		array( 'customize-controls', 'wp-color-picker', 'wp-element', 'wp-components', 'trove-customizer-color-popover' ),
		$ver,
		true
	);

	wp_localize_script(
		'trove-customizer-section-tabs',
		'troveCzSectionTabs',
		Options_Registry::get_section_tabs_script_data()
	);
}

require_once __DIR__ . '/apply.php';

add_action( 'after_setup_theme', __NAMESPACE__ . '\\migrate_legacy_font_theme_mod', 5 );
add_action( 'after_setup_theme', __NAMESPACE__ . '\\migrate_stored_google_font_slug_to_family_name', 6 );
