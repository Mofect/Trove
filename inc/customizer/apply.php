<?php
/**
 * Maps Customizer theme_mods to the front end (extend alongside new settings).
 *
 * @package Trove
 */

namespace Trove\Customizer;

defined( 'ABSPATH' ) || exit;

/**
 * Theme mod keys used for Global Colors → :root CSS variables (single source of truth).
 *
 * @return array<string, array{var:string, default:string}>
 */
function trove_color_theme_mod_definitions() {
	return array(
		'trove_color_base_text'        => array(
			'var'     => '--trove-text',
			'default' => '#111111',
		),
		'trove_color_headings'         => array(
			'var'     => '--trove-heading',
			'default' => '#000000',
		),
		'trove_color_link'             => array(
			'var'     => '--trove-link',
			'default' => '#064fff',
		),
		'trove_color_link_hover'       => array(
			'var'     => '--trove-link-hover',
			'default' => '#003dcc',
		),
		'trove_color_selection_text'   => array(
			'var'     => '--trove-selection-color',
			'default' => '#ffffff',
		),
		'trove_color_selection_bg'     => array(
			'var'     => '--trove-selection-bg',
			'default' => '#22c55e',
		),
		'trove_color_border'           => array(
			'var'     => '--trove-border',
			'default' => '#eeeeee',
		),
		'trove_color_accent'           => array(
			'var'     => '--trove-accent',
			'default' => '#064fff',
		),
	);
}

/**
 * Theme mod keys included in global color scheme presets (order = swatch strip left → right).
 *
 * @return list<string>
 */
function trove_global_color_scheme_setting_keys() {
	return array_keys( trove_color_theme_mod_definitions() );
}

/**
 * Sanitizes a CSS color token for :root custom properties.
 * Supports hex and rgb/rgba for alpha-enabled pickers.
 *
 * @param mixed $value Raw candidate value.
 * @return string Sanitized color or empty string.
 */
function trove_sanitize_css_color( $value ) {
	$raw = is_string( $value ) ? trim( $value ) : trim( (string) $value );
	if ( '' === $raw ) {
		return '';
	}

	$hex = sanitize_hex_color( $raw );
	if ( $hex ) {
		return $hex;
	}

	if ( preg_match( '/^rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})(?:\s*,\s*(0|0?\.\d+|1(?:\.0+)?)\s*)?\)$/i', $raw, $m ) ) {
		$r = min( 255, max( 0, (int) $m[1] ) );
		$g = min( 255, max( 0, (int) $m[2] ) );
		$b = min( 255, max( 0, (int) $m[3] ) );
		if ( isset( $m[4] ) && '' !== $m[4] ) {
			$a = min( 1, max( 0, (float) $m[4] ) );
			$alpha = rtrim( rtrim( sprintf( '%.3F', $a ), '0' ), '.' );
			if ( '' === $alpha ) {
				$alpha = '0';
			}
			return sprintf( 'rgba(%d,%d,%d,%s)', $r, $g, $b, $alpha );
		}
		return sprintf( 'rgb(%d,%d,%d)', $r, $g, $b );
	}

	return '';
}

/**
 * @param string $key     Theme mod key.
 * @param string $default Valid fallback color.
 * @return string
 */
function trove_color_theme_mod( $key, $default ) {
	$raw = get_theme_mod( $key );
	if ( null === $raw || false === $raw || '' === $raw ) {
		return $default;
	}
	$san = trove_sanitize_css_color( is_string( $raw ) ? $raw : (string) $raw );
	return '' !== $san ? $san : $default;
}

/**
 * Clamps content width to the same bounds as the Customizer number control.
 *
 * @param int $width Raw width in px.
 * @return int
 */
function trove_clamp_content_width( $width ) {
	$w = absint( $width );
	if ( $w < 600 || $w > 1400 ) {
		return 800;
	}
	return $w;
}

/**
 * Excerpt length from Customizer (Blog section).
 *
 * @param int $length Default length.
 * @return int
 */
function filter_excerpt_length( $length ) {
	$custom = get_theme_mod( 'trove_excerpt_length', 40 );
	return absint( $custom ) > 0 ? absint( $custom ) : $length;
}
add_filter( 'excerpt_length', __NAMESPACE__ . '\\filter_excerpt_length', 20 );

/**
 * Inline CSS for accent, content width, and header border.
 */
function output_customizer_css() {
	if ( ! wp_style_is( 'trove-style', 'enqueued' ) ) {
		return;
	}
	$font = get_theme_mod( 'trove_font_base_stack', 'inter' );

	$widths = get_responsive_mod_values(
		'trove_content_width',
		array(
			'pc'     => 800,
			'tablet' => 720,
			'phone'  => 640,
		)
	);
	$w_pc     = trove_clamp_content_width( $widths['pc'] );
	$w_tablet = trove_clamp_content_width( $widths['tablet'] );
	$w_phone  = trove_clamp_content_width( $widths['phone'] );

	$bp = get_responsive_breakpoints_max();

	$root_parts = array();
	foreach ( trove_color_theme_mod_definitions() as $mod_key => $row ) {
		$root_parts[] = $row['var'] . ':' . esc_attr( trove_color_theme_mod( $mod_key, $row['default'] ) );
	}
	$root_parts[] = '--trove-content:' . (int) $w_pc . 'px';

	$css  = ':root{' . implode( ';', $root_parts ) . '}';
	$css .= '.site-main{max-width:var(--trove-content);}';
	$css .= '@media (max-width:' . (int) $bp['tablet'] . 'px){:root{--trove-content:' . (int) $w_tablet . 'px;}}';
	$css .= '@media (max-width:' . (int) $bp['phone'] . 'px){:root{--trove-content:' . (int) $w_phone . 'px;}}';

	if ( 'system' === $font ) {
		$css .= 'body{font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;}';
	}

	$header_min = absint( get_theme_mod( 'trove_header_min_height', 0 ) );
	if ( $header_min > 0 && $header_min <= 400 ) {
		$css .= '.site-header{min-height:' . $header_min . 'px;}';
	}

	wp_add_inline_style( 'trove-style', $css );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\output_customizer_css', 20 );

/**
 * Live preview: Global Colors update :root variables without refreshing the iframe.
 */
function enqueue_customizer_preview_scripts() {
	$ver = defined( 'TROVE_THEME_VERSION' ) ? TROVE_THEME_VERSION : '1.0.0';
	$uri = get_template_directory_uri();

	$preview_colors = array();
	foreach ( trove_color_theme_mod_definitions() as $mod => $row ) {
		$preview_colors[ $mod ] = array(
			'var'     => $row['var'],
			'default' => $row['default'],
		);
	}

	wp_enqueue_script(
		'trove-customizer-preview',
		$uri . '/assets/js/customizer-preview.js',
		array( 'jquery', 'customize-preview' ),
		$ver,
		true
	);

	wp_localize_script(
		'trove-customizer-preview',
		'troveCustomizerPreview',
		array(
			'colors' => $preview_colors,
		)
	);
}
add_action( 'customize_preview_init', __NAMESPACE__ . '\\enqueue_customizer_preview_scripts' );

/**
 * Class names for the theme `<header>` (always includes `site-header`).
 *
 * @return string[]
 */
function get_header_classnames() {
	$classes = array( 'site-header' );
	if ( get_theme_mod( 'trove_header_bottom_border', false ) ) {
		$classes[] = 'trove-header-has-border';
	}
	$allowed = array(
		'horizontal-left-logo',
		'horizontal-centered-logo',
		'horizontal-right-logo',
	);
	$layout = get_theme_mod( 'trove_header_layout', 'horizontal-left-logo' );
	$layout = is_string( $layout ) ? sanitize_key( $layout ) : '';
	if ( '' !== $layout && in_array( $layout, $allowed, true ) ) {
		$classes[] = 'trove-header__' . $layout;
	}
	return apply_filters( 'trove_header_classnames', $classes );
}

/**
 * Echoes the `class` attribute value for the theme header (escaped).
 *
 * @return void
 */
function the_header_class_attr() {
	$classes = get_header_classnames();
	echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $classes ) ) );
}
