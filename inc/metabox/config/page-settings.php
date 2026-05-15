<?php
/**
 * Registers the default “Page settings” metabox for posts, pages, and products.
 *
 * Path: `inc/metabox/config/page-settings.php`. Add or change fields here, or call
 * `Registry::register()` from another file for additional metaboxes (different id +
 * post_types + fields).
 *
 * Registration runs on `init` so strings load after `load_theme_textdomain` (WP 6.7+).
 *
 * @package Trove
 */

namespace Trove\Metabox;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the Page settings metabox configuration.
 *
 * @return void
 */
function register_page_settings_metabox() {
	$post_types = apply_filters( 'trove_page_settings_metabox_post_types', array( 'page' ) );

	$vert_placeholder  = (string) \Trove\Customizer\get_content_vertical_spacing_px();
	$horiz_placeholder = (string) \Trove\Customizer\get_content_horizontal_spacing_px();

	$color_defs          = \Trove\Customizer\color_theme_mod_definitions();
	$header_text_d       = isset( $color_defs['trove_header_text_color'] ) ? (string) $color_defs['trove_header_text_color']['default'] : '#111111';
	$header_link_d       = isset( $color_defs['trove_header_link_color'] ) ? (string) $color_defs['trove_header_link_color']['default'] : '#000000';
	$header_link_hover_d = isset( $color_defs['trove_header_link_hover_color'] ) ? (string) $color_defs['trove_header_link_hover_color']['default'] : '#666666';
	$header_text_sample        = \Trove\Customizer\color_theme_mod( 'trove_header_text_color', $header_text_d );
	$header_link_sample        = \Trove\Customizer\color_theme_mod( 'trove_header_link_color', $header_link_d );
	$header_link_hover_sample  = \Trove\Customizer\color_theme_mod( 'trove_header_link_hover_color', $header_link_hover_d );
	$header_border_sample      = metabox_header_border_theme_mod_resolved();
	$header_border_default_hex = '' !== $header_border_sample ? $header_border_sample : '';
	$header_bg_d               = isset( $color_defs['trove_header_background'] ) ? (string) $color_defs['trove_header_background']['default'] : '#ffffff';
	$header_bg_sample          = \Trove\Customizer\color_theme_mod( 'trove_header_background', $header_bg_d );

	$banner_bg_sample = $header_bg_sample;

	Registry::register(
		'page_settings',
		array(
			'title'      => __( 'Page settings', 'trove' ),
			'post_types' => $post_types,
			'context'    => apply_filters( 'trove_page_settings_metabox_context', 'side' ),
			'priority'   => 'high',
			'fields'     => array(
				array(
					'id'              => 'enable_page_banner',
					'type'            => 'switch',
					'name'            => __( 'Enable page banner', 'trove' ),
					'description'     => __( 'Show a banner area for this page (layout depends on the theme).', 'trove' ),
					'default'         => false,
					'tab'             => 'page_banner',
				),
				array(
					'id'                => 'enable_page_banner_breadcrumb',
					'type'              => 'switch',
					'name'              => __( 'Enable breadcrumb', 'trove' ),
					'description'       => __( 'When enabled, the breadcrumb trail appears in the top-left corner of the page banner.', 'trove' ),
					'default'           => false,
					'only_when_switch'  => 'enable_page_banner',
					'tab'               => 'page_banner',
				),
				array(
					'id'            => 'hide_page_title',
					'type'          => 'switch',
					'name'          => __( 'Hide page title', 'trove' ),
					'description'   => __( 'Hide the page title on this page.', 'trove' ),
					'default'       => false,
					'tab'           => 'page_banner',
				),
				array(
					'id'                 => 'page_title_alignment',
					'type'               => 'alignment',
					'name'               => __( 'Page title alignment', 'trove' ),
					'description'        => __( 'Align the page title within the banner.', 'trove' ),
					'default'            => 'left',
					'options'            => array(
						'left'   => __( 'Left', 'trove' ),
						'center' => __( 'Center', 'trove' ),
						'right'  => __( 'Right', 'trove' ),
					),
					'only_when_switch'   => 'enable_page_banner',
					'unless_switch'      => 'hide_page_title',
					'sanitize_callback'  => __NAMESPACE__ . '\\sanitize_page_settings_page_title_alignment',
					'tab'                => 'page_banner',
				),
				array(
					'id'                 => 'page_title_size',
					'type'               => 'select',
					'name'               => __( 'Page title size', 'trove' ),
					'description'        => __( 'Typography size for the page title in the banner.', 'trove' ),
					'default'            => 'medium',
					'options'            => array(
						'small'  => __( 'Small', 'trove' ),
						'medium' => __( 'Medium', 'trove' ),
						'large'  => __( 'Large', 'trove' ),
					),
					'only_when_switch'   => 'enable_page_banner',
					'unless_switch'      => 'hide_page_title',
					'sanitize_callback'  => __NAMESPACE__ . '\\sanitize_page_settings_page_title_size',
					'tab'                => 'page_banner',
				),
				array(
					'id'                 => 'page_title_color',
					'type'               => 'color',
					'name'               => __( 'Page title color', 'trove' ),
					'description'        => __( 'Text color for the page title in the banner. Leave empty for the theme default.', 'trove' ),
					'default'            => null,
					'default_color'      => $header_text_sample,
					'swatch_hint'        => __( 'Title', 'trove' ),
					'only_when_switch'   => 'enable_page_banner',
					'unless_switch'      => 'hide_page_title',
					'sanitize_callback'  => __NAMESPACE__ . '\\sanitize_page_settings_page_title_color',
					'tab'                => 'page_banner',
				),
				array(
					'id'                => 'page_banner_height',
					'type'              => 'range',
					'name'              => __( 'Page banner height', 'trove' ),
					'description'       => __( 'Height of the banner area in pixels.', 'trove' ),
					'default'           => null,
					'min'               => 200,
					'max'               => 600,
					'step'              => 1,
					'placeholder'       => '200',
					'show_number'       => true,
					'number_unit'       => 'px',
					'only_when_switch'  => 'enable_page_banner',
					'sanitize_callback' => __NAMESPACE__ . '\\sanitize_page_settings_page_banner_height',
					'tab'               => 'page_banner',
				),
				array(
					'id'                 => 'page_banner_background_image',
					'type'               => 'image_attachment',
					'name'               => __( 'Banner for desktop', 'trove' ),
					'description'        => __( 'Desktop and tablet banner image.', 'trove' ),
					'default'            => null,
					'only_when_switch'   => 'enable_page_banner',
					'tab'                => 'page_banner',
				),
				array(
					'id'                 => 'page_banner_background_image_mobile',
					'type'               => 'image_attachment',
					'name'               => __( 'Banner for mobile', 'trove' ),
					'description'        => __( 'Optional image used on small screens; falls back to the main banner image if empty.', 'trove' ),
					'default'            => null,
					'only_when_switch'   => 'enable_page_banner',
					'tab'                => 'page_banner',
				),
				array(
					'id'                 => 'page_banner_background_color',
					'type'               => 'color',
					'name'               => __( 'Background color', 'trove' ),
					'description'        => __( 'Fills the banner behind the image. Leave empty for no solid fill.', 'trove' ),
					'default'            => null,
					'default_color'      => $banner_bg_sample,
					'swatch_hint'        => __( 'Banner', 'trove' ),
					'only_when_switch'   => 'enable_page_banner',
					'sanitize_callback'  => __NAMESPACE__ . '\\sanitize_page_settings_page_banner_background_color',
					'tab'                => 'page_banner',
				),
				array(
					'id'                 => 'page_banner_overlay_color',
					'type'               => 'color',
					'name'               => __( 'Overlay color', 'trove' ),
					'description'        => __( 'Semi-transparent tint over the banner (on top of the image). Leave empty for no overlay.', 'trove' ),
					'default'            => null,
					'default_color'      => 'rgba(0, 0, 0, 0.35)',
					'swatch_hint'        => __( 'Overlay', 'trove' ),
					'only_when_switch'   => 'enable_page_banner',
					'sanitize_callback'  => __NAMESPACE__ . '\\sanitize_page_settings_page_banner_overlay_color',
					'tab'                => 'page_banner',
				),
				array(
					'id'                => 'content_vertical_spacing',
					'type'              => 'range',
					'name'              => __( 'Content vertical spacing', 'trove' ),
					'description'       => __( 'Drag to override top and bottom padding for the main content on this page. ', 'trove' ),
					'default'           => null,
					'min'               => 0,
					'max'               => 200,
					'step'              => 1,
					'placeholder'       => $vert_placeholder,
					'show_number'       => true,
					'number_unit'       => 'px',
					'sanitize_callback' => __NAMESPACE__ . '\\sanitize_page_settings_content_vertical_spacing',
					'tab'               => 'content',
				),
				array(
					'id'                => 'content_horizontal_spacing',
					'type'              => 'range',
					'name'              => __( 'Content horizontal spacing', 'trove' ),
					'description'       => __( 'Drag to override left and right padding for the main content on this page. ', 'trove' ),
					'default'           => null,
					'min'               => 0,
					'max'               => 200,
					'step'              => 1,
					'placeholder'       => $horiz_placeholder,
					'show_number'       => true,
					'number_unit'       => 'px',
					'sanitize_callback' => __NAMESPACE__ . '\\sanitize_page_settings_content_horizontal_spacing',
					'tab'               => 'content',
				),
				array(
					'id'            => 'transparent_header',
					'type'          => 'switch',
					'name'          => __( 'Transparent header', 'trove' ),
					'description'   => __( 'Use a transparent header bar on this page.', 'trove' ),
					'default'       => false,
					'tab'           => 'header',
				),
				array(
					'id'                => 'header_background_color',
					'type'              => 'color',
					'name'              => __( 'Header background', 'trove' ),
					'description'       => __( 'Leave empty to use Customizer → General → Colors. Only used when transparent header is off.', 'trove' ),
					'default'           => null,
					'default_color'     => $header_bg_sample,
					'unless_switch'     => 'transparent_header',
					'sanitize_callback' => __NAMESPACE__ . '\\sanitize_page_settings_header_background_color',
					'tab'               => 'header',
				),
				array(
					'id'                => 'header_text_color',
					'type'              => 'color',
					'name'              => __( 'Header text', 'trove' ),
					'description'       => __( 'Leave empty to use Customizer → General → Colors. ', 'trove' ),
					'default'           => null,
					'default_color'     => $header_text_sample,
					'swatch_hint'       => __( 'Text', 'trove' ),
					'sanitize_callback' => __NAMESPACE__ . '\\sanitize_page_settings_header_text_color',
					'tab'               => 'header',
				),
				array(
					'id'            => 'pair_header_links',
					'type'          => 'color_pair',
					'name'          => __( 'Header links', 'trove' ),
					'description'   => __( 'Leave empty to use Customizer → General → Colors. ', 'trove' ),
					'swatch_hints'  => array(
						'left'  => __( 'Link', 'trove' ),
						'right' => __( 'Hover', 'trove' ),
					),
					'pair'          => array(
						'left'  => array(
							'id'                => 'header_link_color',
							'default'           => null,
							'default_color'     => $header_link_sample,
							'sanitize_callback' => __NAMESPACE__ . '\\sanitize_page_settings_header_link_color',
						),
						'right' => array(
							'id'                => 'header_link_hover_color',
							'default'           => null,
							'default_color'     => $header_link_hover_sample,
							'sanitize_callback' => __NAMESPACE__ . '\\sanitize_page_settings_header_link_hover_color',
						),
					),
					'tab'           => 'header',
				),
				array(
					'id'                => 'header_border_color',
					'type'              => 'color',
					'name'              => __( 'Header border', 'trove' ),
					'description'       => __( 'Leave empty to use Customizer → General → Colors.', 'trove' ),
					'default'           => null,
					'default_color'     => $header_border_default_hex,
					'swatch_hint'       => __( 'Border', 'trove' ),
					'sanitize_callback' => __NAMESPACE__ . '\\sanitize_page_settings_header_border_color',
					'tab'               => 'header',
				),
			),
		)
	);
}

/**
 * Sanitize page banner height (px).
 *
 * @param mixed                $raw   POST value.
 * @param array<string, mixed> $field Field definition.
 * @return string|null Stored as string; empty clears meta.
 */
function sanitize_page_settings_page_banner_height( $raw, $field ) {
	if ( '' === $raw || null === $raw ) {
		return null;
	}
	$v = (int) $raw;
	$v = max( 0, min( 600, $v ) );
	return (string) $v;
}

/**
 * Sanitize page title alignment in banner.
 *
 * @param mixed                $raw   POST value.
 * @param array<string, mixed> $field Field definition.
 * @return string
 */
function sanitize_page_settings_page_title_alignment( $raw, $field ) {
	$v = isset( $raw ) ? sanitize_key( (string) wp_unslash( $raw ) ) : 'left';
	return in_array( $v, array( 'left', 'center', 'right' ), true ) ? $v : 'left';
}

/**
 * Sanitize page title size in banner.
 *
 * @param mixed                $raw   POST value.
 * @param array<string, mixed> $field Field definition.
 * @return string
 */
function sanitize_page_settings_page_title_size( $raw, $field ) {
	$v = isset( $raw ) ? sanitize_key( (string) wp_unslash( $raw ) ) : 'medium';
	return in_array( $v, array( 'small', 'medium', 'large' ), true ) ? $v : 'medium';
}

/**
 * Sanitize page title color in banner.
 *
 * @param mixed                $raw   POST value.
 * @param array<string, mixed> $field Field definition.
 * @return string|null
 */
function sanitize_page_settings_page_title_color( $raw, $field ) {
	$out = \Trove\Customizer\sanitize_css_color( isset( $raw ) ? wp_unslash( $raw ) : '' );
	return '' === $out ? null : $out;
}

/**
 * Sanitize page banner background color.
 *
 * @param mixed                $raw   POST value.
 * @param array<string, mixed> $field Field definition.
 * @return string|null
 */
function sanitize_page_settings_page_banner_background_color( $raw, $field ) {
	$out = \Trove\Customizer\sanitize_css_color( isset( $raw ) ? wp_unslash( $raw ) : '' );
	return '' === $out ? null : $out;
}

/**
 * Sanitize page banner overlay color (tint over image).
 *
 * @param mixed                $raw   POST value.
 * @param array<string, mixed> $field Field definition.
 * @return string|null
 */
function sanitize_page_settings_page_banner_overlay_color( $raw, $field ) {
	$out = \Trove\Customizer\sanitize_css_color( isset( $raw ) ? wp_unslash( $raw ) : '' );
	return '' === $out ? null : $out;
}

add_action( 'init', __NAMESPACE__ . '\\register_page_settings_metabox', 10 );
