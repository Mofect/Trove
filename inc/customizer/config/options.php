<?php
/**
 * Declarative Customizer tree (Blocksy-style arrays: group titles + sections + options).
 *
 * General panel: Layout only. Colors and Typography are root-level sections (siblings of General, Header, …).
 *
 * Panel with nested sections (Header → Layout, Settings):
 *   'header' => [
 *     'type' => 'trove-panel',
 *     ...
 *   ],
 *
 * Schema (aligned with Blocksy’s `$options` shape):
 *
 * Group label / divider:
 *   'my_group_title' => [
 *     'type'     => 'trove-group-title',
 *     'title'    => __( '…', 'trove' ),
 *     'priority' => 1,
 *     'kind'     => 'divider', // optional
 *   ],
 *
 * Section with controls:
 *   'section_key' => [
 *     'title'       => __( '…', 'trove' ),
 *     'description' => __( '…', 'trove' ), // optional
 *     'container'   => [ 'priority' => 10, 'color_swatches_layout' => true ], // optional: swatch row layout
 *     'options'     => require __DIR__ . '/section-file.php', // returns field array
 *   ],
 *
 * Section with tabs (each tab has its own options array; tab bar appears at top of section):
 *   'section_key' => [
 *     'title'     => __( '…', 'trove' ),
 *     'container' => [ 'priority' => 10, 'color_swatches_layout' => true ], // optional
 *     'tabs'      => [
 *       'general' => [
 *         'label'   => __( 'General', 'trove' ),
 *         'options' => require __DIR__ . '/section-general.php',
 *       ],
 *       'design'  => [
 *         'label'   => __( 'Design', 'trove' ),
 *         'options' => require __DIR__ . '/section-design.php',
 *       ],
 *     ],
 *   ],
 *
 * Responsive field (three theme_mods: {id}_pc, {id}_tablet, {id}_phone):
 *   'trove_foo' => [
 *     'label'       => __( 'Foo', 'trove' ),
 *     'type'        => 'number',
 *     'responsive'  => true,
 *     'default'     => 12, // or [ 'pc' => 12, 'tablet' => 10, 'phone' => 8 ]
 *     // optional: 'responsive_labels' => [ 'pc' => '…', 'tablet' => '…', 'phone' => '…' ],
 *   ],
 *
 * Switch (boolean theme_mod, pill UI instead of checkbox):
 *   'trove_toggle' => [
 *     'label'   => __( 'Hover effect', 'trove' ),
 *     'type'    => 'switch',
 *     'default' => false,
 *     'sanitize_callback' => 'wp_validate_boolean',
 *   ],
 *
 * Image radio (2×2 card grid; each choice: image URL, pattern slug, or svg markup):
 * Number + unit on one row (two theme_mods: value + unit_setting):
 *   'trove_font_size' => [
 *     'label' => __( 'Base font size', 'trove' ),
 *     'type' => 'number_unit',
 *     'unit_setting' => 'trove_font_base_size_unit',
 *     'number_default' => 16,
 *     'unit_default' => 'px',
 *     'unit_choices' => [ 'px' => 'px', 'rem' => 'rem' ],
 *     'sanitize_callback' => '...',
 *     'unit_sanitize_callback' => '...',
 *     'transport' => 'postMessage',
 *   ],
 *
 * Google Font (Select2 + full catalog from bundled metadata; theme_mod = family name or system):
 *   'trove_font' => [
 *     'label'   => __( 'Body font', 'trove' ),
 *     'type'    => 'google_font',
 *     'default' => 'Instrument Sans',
 *     'transport' => 'postMessage',
 *   ],
 *
 *   'trove_foo' => [
 *     'label'   => __( 'Layout', 'trove' ),
 *     'type'    => 'image_radio',
 *     'default' => 'right_narrow',
 *     'choices' => [
 *       'right_narrow' => [
 *         'label' => __( '…', 'trove' ),
 *         'image' => get_theme_file_uri( 'assets/images/layout-narrow.png' ), // or 'url' => 'https://…'
 *       ],
 *       'alt' => [ 'label' => __( '…', 'trove' ), 'pattern' => 'right_medium' ],
 *     ],
 *   ],
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

defined( 'ABSPATH' ) || exit;

/**
 * Returns the merged options array for the Customizer registry.
 *
 * Each segment is filterable before merge; the full tree is filterable last:
 *
 * - `trove_customizer_options_general` — General Options heading, General (Layout), Colors, Typography, Header, Footer, Performance.
 * - `trove_customizer_options_post_types` — Post types group title and post-type sections (e.g. Blog).
 * - `trove_customizer_options_shop` — Shop group title and shop-related sections (WooCommerce active).
 * - `trove_customizer_options_site` — Site group title and related entries.
 * - `trove_customizer_options_extensions` — Extra sections/panels (defaults to empty).
 * - `trove_customizer_options` — Final merged config (general + post_types + shop + extensions + site).
 *
 * @return array<string, array<string, mixed>>
 */
function get_options() {
	/**
	 * General Options
	 */
	$general = array(
		'general_options_title' => array(
			'type'     => 'trove-group-title',
			'title'    => __( 'General Options', 'trove' ),
			'priority' => 100,
		),

		'general' => array(
			'type'        => 'trove-panel',
			'title'       => __( 'General', 'trove' ),
			'container'   => array( 'priority' => 105 ),
			'sections'    => array(
				'layout' => array(
					'title'     => __( 'Layout', 'trove' ),
					'container' => array( 'priority' => 1 ),
					'options'   => require __DIR__ . '/general/layout.php',
				),
			),
		),

		'color' => array(
			'title'     => __( 'Colors', 'trove' ),
			'container' => array(
				'priority'              => 106,
				'color_swatches_layout' => true,
			),
			'options'   => require __DIR__ . '/general/colors.php',
		),

		'typography' => array(
			'title'     => __( 'Typography', 'trove' ),
			'container' => array( 'priority' => 107 ),
			'options'   => require __DIR__ . '/general/typography.php',
		),

		'announcement_bar' => array(
			'title'       => __( 'Announcement Bar', 'trove' ),
			'container'   => array(
				'priority'              => 108,
				'color_swatches_layout' => true,
			),
			'tabs'        => array(
				'content' => array(
					'label'   => __( 'Content', 'trove' ),
					'options' => require __DIR__ . '/announcement/content.php',
				),
				'styles' => array(
					'label'   => __( 'Styles', 'trove' ),
					'options' => require __DIR__ . '/announcement/styles.php',
				),
				'settings' => array(
					'label'   => __( 'Settings', 'trove' ),
					'options' => require __DIR__ . '/announcement/settings.php',
				)
			),
		),

		'header' => array(
			'title'       => __( 'Header', 'trove' ),
			'container'   => array(
				'priority'              => 110,
				'color_swatches_layout' => true,
			),
			'tabs'        => array(
				'layout' => array(
					'label'   => __( 'Layout', 'trove' ),
					'options' => require __DIR__ . '/header/layout.php',
				),
				'settings' => array(
					'label'   => __( 'Settings', 'trove' ),
					'options' => require __DIR__ . '/header/settings.php',
				)
			),
		),

		'footer' => array(
			'title'     => __( 'Footer', 'trove' ),
			'container' => array(
				'priority'              => 115,
				'color_swatches_layout' => true,
			),
			'tabs'        => array(
				'layout' => array(
					'label'   => __( 'Layout', 'trove' ),
					'options' => require __DIR__ . '/footer/layout.php',
				),
				'content' => array(
					'label'   => __( 'Content', 'trove' ),
					'options' => require __DIR__ . '/footer/content.php',
				)
			)
		),

		'social_profile' => array(
			'title'     => __( 'Social Profiles', 'trove' ),
			'container' => array( 'priority' => 116 ),
			'options'   => require __DIR__ . '/general/social-profiles.php',
		),

		'performance' => array(
			'title'     => __( 'Performance', 'trove' ),
			'container' => array( 'priority' => 117 ),
			'options'   => require __DIR__ . '/general/performance.php',
		),
	);

	$general = apply_filters( 'trove_customizer_options_general', $general );

	/**
	 * Post types
	 */
	$post_types = array(
		'post_types_title' => array(
			'type'     => 'trove-group-title',
			'title'    => __( 'Post types', 'trove' ),
			'priority' => 130,
		),

		'blog' => array(
			'title'     => __( 'Blog Posts', 'trove' ),
			'container' => array( 'priority' => 135 ),
			'tabs'        => array(
				'layout' => array(
					'label'   => __( 'Layout', 'trove' ),
					'options' => require __DIR__ . '/blog/layout.php',
				),
				'styles' => array(
					'label'   => __( 'Styles', 'trove' ),
					'options' => require __DIR__ . '/blog/styles.php',
				),
				'settings' => array(
					'label'   => __( 'Settings', 'trove' ),
					'options' => require __DIR__ . '/blog/settings.php',
				)
			),
		),
	);

	$post_types = apply_filters( 'trove_customizer_options_post_types', $post_types );

	$woocommerce = array();
	if( class_exists( 'woocommerce' ) ) {
		$woocommerce = array(
			'shop_group_title' => array(
				'type'     => 'trove-group-title',
				'title'    => __( 'WooCommerce', 'trove' ),
				'priority' => 139,
			),
		);
	}

	$woocommerce = apply_filters( 'trove_customizer_options_shop', $woocommerce );

	/**
	 * Site
	 */
	$site = array(
		'site_group_title' => array(
			'type'     => 'trove-group-title',
			'title'    => __( 'Site', 'trove' ),
			'priority' => 140,
		),
	);

	$site = apply_filters( 'trove_customizer_options_site', $site );

	/**
	 * Extensions
	 */
	$extensions = apply_filters( 'trove_customizer_options_extensions', array() );

	$merged = array_merge( $general, $post_types, $woocommerce, $extensions, $site );

	/**
	 * Filter the full declarative Customizer config (Blocksy-style).
	 *
	 * @param array $merged Options tree.
	 */
	return apply_filters( 'trove_customizer_options', $merged );
}
