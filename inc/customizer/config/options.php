<?php
/**
 * Declarative Customizer tree (Blocksy-style arrays: group titles + sections + options).
 *
 * General panel: Layout only. Colors and Typography are root-level sections (siblings of General, Header, …).
 *
 * Panel with nested sections (Header → Layout, Style):
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
 *     'container'   => [ 'priority' => 10 ],
 *     'options'     => require __DIR__ . '/section-file.php', // returns field array
 *   ],
 *
 * Section with tabs (each tab has its own options array; tab bar appears at top of section):
 *   'section_key' => [
 *     'title'     => __( '…', 'trove' ),
 *     'container' => [ 'priority' => 10 ],
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
			'container' => array( 'priority' => 106 ),
			'options'   => require __DIR__ . '/general/colors.php',
		),

		'typography' => array(
			'title'     => __( 'Typography', 'trove' ),
			'container' => array( 'priority' => 107 ),
			'options'   => require __DIR__ . '/general/typography.php',
		),

		'header' => array(
			'title'       => __( 'Header', 'trove' ),
			'container'   => array( 'priority' => 110 ),
			'tabs'        => array(
				'layout' => array(
					'label'   => __( 'Layout', 'trove' ),
					'options' => require __DIR__ . '/header/layout.php',
				),
				'search' => array(
					'label'   => __( 'Search', 'trove' ),
					'options' => require __DIR__ . '/header/search.php',
				),
				'advanced' => array(
					'label'   => __( 'Advanced', 'trove' ),
					'options' => require __DIR__ . '/header/advanced.php',
				)
			),
		),

		'footer' => array(
			'title'     => __( 'Footer', 'trove' ),
			'container' => array( 'priority' => 115 ),
			'options'   => require __DIR__ . '/general/footer.php',
		),

		'performance' => array(
			'title'     => __( 'Performance', 'trove' ),
			'container' => array( 'priority' => 116 ),
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
			'options'   => require __DIR__ . '/blog.php',
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

/**
 * @param string $value Raw font key.
 * @return string
 */
function sanitize_font_stack( $value ) {
	$allowed = array( 'inter', 'system' );
	return in_array( $value, $allowed, true ) ? $value : 'inter';
}
