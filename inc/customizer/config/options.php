<?php
/**
 * Declarative Customizer tree (Blocksy-style arrays: group titles + sections + options).
 *
 * General panel: Layout, Button. Colors and Typography are root-level sections (siblings of General, Header, …).
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
 * Responsive TRBL padding (JSON string per device: top, right, bottom, left, linked):
 *   'trove_bar_padding' => [
 *     'label'    => __( 'Padding', 'trove' ),
 *     'type'     => 'responsive_spacing',
 *     'default'  => [ 'pc' => [ 'top' => 5, 'right' => 20, 'bottom' => 5, 'left' => 20 ], 'tablet' => [...], 'phone' => [...] ],
 *     'input_attrs' => [ 'min' => 0, 'max' => 60, 'step' => 1 ],
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
 *   'trove_divider_id' => [
 *     'type' => 'divider', // horizontal rule; no label / no saved value
 *   ],
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
 * Clone a settings array with prefixed setting ids.
 *
 * @param array<string, array<string, mixed>> $options Options keyed by setting id.
 * @param string                              $from    Prefix to replace.
 * @param string                              $to      New prefix.
 * @return array<string, array<string, mixed>>
 */
function prefixed_options( array $options, $from, $to ) {
	$out = array();
	foreach ( $options as $setting_id => $field ) {
		$new_id = str_replace( (string) $from, (string) $to, (string) $setting_id );
		if ( is_array( $field ) && array_key_exists( 'default', $field ) ) {
			$field['default'] = \Trove\Customizer\customizer_default( $new_id, $field['default'] );
		}
		$out[ $new_id ] = $field;
	}
	return $out;
}

/**
 * Returns the merged options array for the Customizer registry.
 *
 * Each segment is filterable before merge; the full tree is filterable last:
 *
 * - `trove_customizer_options_general` — General Options heading, General (Layout, Button), Colors, Typography, Announcement Bar, Header, Footer, Performance.
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
				'button' => array(
					'title'     => __( 'Button', 'trove' ),
					'container' => array(
						'priority'              => 2,
						'color_swatches_layout' => true,
					),
					'options'   => require __DIR__ . '/general/button.php',
				),
				'text_field' => array(
					'title'     => __( 'Text Field', 'trove' ),
					'container' => array(
						'priority'              => 3,
						'color_swatches_layout' => true,
					),
					'options'   => require __DIR__ . '/general/text-field.php',
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
				'priority' => 108,
			),
			'tabs'        => array(
				'content' => array(
					'label'   => __( 'Content', 'trove' ),
					'options' => require __DIR__ . '/announcement/content.php',
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
	 * Pages
	 */
	$blog_layout_options   = require __DIR__ . '/blog/layout.php';
	$blog_styles_options   = require __DIR__ . '/blog/styles.php';
	$blog_settings_options = require __DIR__ . '/blog/settings.php';

	$blog_listing_sidebar_options = array(
		'trove_blog_enable_sidebar' => array(
			'label'             => __( 'Enable sidebar', 'trove' ),
			'description'       => __( 'Show the Blog Sidebar widget area beside the post listing.', 'trove' ),
			'type'              => 'switch',
			'default'           => (bool) \Trove\Customizer\customizer_default( 'trove_blog_enable_sidebar', false ),
			'transport'         => 'refresh',
			'sanitize_callback' => 'wp_validate_boolean',
		),
	);

	$pages = array(
		'post_types_title' => array(
			'type'     => 'trove-group-title',
			'title'    => __( 'Pages', 'trove' ),
			'priority' => 130,
		),
		'homepage' => array(
			'title'     => __( 'Default Homepage', 'trove' ),
			'container' => array( 'priority' => 135 ),
			'tabs'        => array(
				'layout' => array(
					'label'   => __( 'Layout', 'trove' ),
					'options' => $blog_layout_options,
				),
				'styles' => array(
					'label'   => __( 'Styles', 'trove' ),
					'options' => $blog_styles_options,
				),
				'settings' => array(
					'label'   => __( 'Settings', 'trove' ),
					'options' => array_merge( $blog_listing_sidebar_options, $blog_settings_options ),
				)
			),
		),
		'search_page' => array(
			'title'     => __( 'Search page', 'trove' ),
			'container' => array( 'priority' => 136 ),
			'tabs'      => array(
				'layout'   => array( 'label' => __( 'Layout', 'trove' ), 'options' => prefixed_options( $blog_layout_options, 'trove_blog_', 'trove_search_page_' ) ),
				'styles'   => array( 'label' => __( 'Styles', 'trove' ), 'options' => prefixed_options( $blog_styles_options, 'trove_blog_', 'trove_search_page_' ) ),
				'settings' => array( 'label' => __( 'Settings', 'trove' ), 'options' => prefixed_options( $blog_settings_options, 'trove_blog_', 'trove_search_page_' ) ),
			),
		),
		'author_page' => array(
			'title'     => __( 'Author page', 'trove' ),
			'container' => array( 'priority' => 137 ),
			'tabs'      => array(
				'layout'   => array( 'label' => __( 'Layout', 'trove' ), 'options' => prefixed_options( $blog_layout_options, 'trove_blog_', 'trove_author_page_' ) ),
				'styles'   => array( 'label' => __( 'Styles', 'trove' ), 'options' => prefixed_options( $blog_styles_options, 'trove_blog_', 'trove_author_page_' ) ),
				'settings' => array( 'label' => __( 'Settings', 'trove' ), 'options' => prefixed_options( $blog_settings_options, 'trove_blog_', 'trove_author_page_' ) ),
			),
		),
		'archive_page' => array(
			'title'     => __( 'Archive page', 'trove' ),
			'container' => array( 'priority' => 138 ),
			'tabs'      => array(
				'layout'   => array( 'label' => __( 'Layout', 'trove' ), 'options' => prefixed_options( $blog_layout_options, 'trove_blog_', 'trove_archive_page_' ) ),
				'styles'   => array( 'label' => __( 'Styles', 'trove' ), 'options' => prefixed_options( $blog_styles_options, 'trove_blog_', 'trove_archive_page_' ) ),
				'settings' => array(
					'label'   => __( 'Settings', 'trove' ),
					'options' => array_merge(
						prefixed_options( $blog_listing_sidebar_options, 'trove_blog_', 'trove_archive_page_' ),
						prefixed_options( $blog_settings_options, 'trove_blog_', 'trove_archive_page_' )
					),
				),
			),
		),
	);

	$pages = apply_filters( 'trove_customizer_options_pages', $pages );

	$woocommerce = array();
	if( class_exists( 'woocommerce' ) ) {
		$woocommerce = array(
			'shop_group_title' => array(
				'type'     => 'trove-group-title',
				'title'    => __( 'WooCommerce', 'trove' ),
				'priority' => 139,
			),
			'woocommerce_message_bar' => array(
				'title'     => __( 'Message bar', 'trove' ),
				'container' => array(
					'priority'              => 140,
					'color_swatches_layout' => true,
				),
				'options'   => require __DIR__ . '/woocommerce/message-bar.php',
			),
			'woocommerce_badges' => array(
				'title'     => __( 'Badge', 'trove' ),
				'container' => array(
					'priority'              => 141,
					'color_swatches_layout' => true,
				),
				'options'   => require __DIR__ . '/woocommerce/badges.php',
			),
			'woocommerce_product_catalog' => array(
				'title'     => __( 'Product catalog', 'trove' ),
				'container' => array(
					'priority'              => 142,
				),
				'options'   => require __DIR__ . '/woocommerce/product-catalog.php',
			),
			'woocommerce_single_product' => array(
				'title'     => __( 'Single Product', 'trove' ),
				'container' => array(
					'priority' => 143,
				),
				'options'   => require __DIR__ . '/woocommerce/single-product.php',
			),
			'woocommerce_cart' => array(
				'title'     => __( 'Cart', 'trove' ),
				'container' => array(
					'priority' => 144,
				),
				'options'   => require __DIR__ . '/woocommerce/cart.php',
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
			'priority' => 180,
		),
	);

	$site = apply_filters( 'trove_customizer_options_site', $site );

	/**
	 * Extensions
	 */
	$extensions = apply_filters( 'trove_customizer_options_extensions', array() );

	$merged = array_merge( $general, $pages, $woocommerce, $extensions, $site );

	/**
	 * Filter the full declarative Customizer config (Blocksy-style).
	 *
	 * @param array $merged Options tree.
	 */
	return apply_filters( 'trove_customizer_options', $merged );
}
