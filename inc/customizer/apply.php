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
 * Body typography → :root CSS variables (--trove_font_base_*).
 *
 * @return array<string, array{var:string, default:string, mode:string}>
 */
function trove_typography_theme_mod_definitions() {
	return array(
		'trove_font_base_weight' => array(
			'var'     => '--trove_font_base_weight',
			'default' => '400',
			'mode'    => 'weight',
		),
	);
}

/**
 * @param mixed $value Raw numeric size.
 * @return float
 */
function trove_sanitize_font_base_size( $value ) {
	$f = is_numeric( $value ) ? (float) $value : 16;
	return max( 0.25, min( 500, round( $f, 4 ) ) );
}

/**
 * @param mixed $value Raw unit slug.
 * @return string px|rem|em
 */
function trove_sanitize_font_base_size_unit( $value ) {
	$allowed = array( 'px', 'rem', 'em' );
	$v       = is_string( $value ) ? strtolower( trim( $value ) ) : 'px';
	return in_array( $v, $allowed, true ) ? $v : 'px';
}

/**
 * @param mixed $value Raw weight.
 * @return int
 */
function trove_sanitize_font_base_weight( $value ) {
	$n       = absint( $value );
	$n       = max( 1, min( 1000, $n ) );
	$allowed = get_font_weight_values_for_family( trove_effective_google_font_family_during_save() );
	return trove_nearest_font_weight( $n, $allowed );
}

/**
 * @param mixed $value Raw line-height number.
 * @return float
 */
function trove_sanitize_font_base_line_height( $value ) {
	$f = is_numeric( $value ) ? (float) $value : 1.5;
	return max( 0.25, min( 500, round( $f, 4 ) ) );
}

/**
 * @param mixed $value Raw unit slug.
 * @return string
 */
function trove_sanitize_font_base_line_height_unit( $value ) {
	$allowed = array( 'unitless', 'px', 'rem', 'em', '%' );
	$v       = is_string( $value ) ? trim( $value ) : 'unitless';
	return in_array( $v, $allowed, true ) ? $v : 'unitless';
}

/**
 * @param float $f Numeric value.
 * @return string Trimmed for CSS (no trailing zeros).
 */
function trove_format_css_float_string( $f ) {
	$s = rtrim( rtrim( sprintf( '%.4F', (float) $f ), '0' ), '.' );
	return '' !== $s ? $s : '0';
}

/**
 * @return string e.g. 16px, 1rem
 */
function trove_font_base_size_css_value() {
	$unit = trove_sanitize_font_base_size_unit( get_theme_mod( 'trove_font_base_size_unit', 'px' ) );
	$raw  = get_theme_mod( 'trove_font_base_size', 16 );
	$v    = is_numeric( $raw ) ? (float) $raw : 16;
	$v    = trove_sanitize_font_base_size( $v );

	if ( 'px' === $unit ) {
		$n = (int) round( $v );
		$n = max( 10, min( 32, $n ) );
		return $n . 'px';
	}
	if ( 'rem' === $unit || 'em' === $unit ) {
		$f = max( 0.5, min( 2.5, $v ) );
		return trove_format_css_float_string( $f ) . $unit;
	}
	return '16px';
}

/**
 * @return string e.g. 400
 */
function trove_font_base_weight_css_value() {
	$v       = get_theme_mod( 'trove_font_base_weight', 400 );
	$n       = absint( $v );
	$allowed = get_font_weight_values_for_family( get_font_family_setting() );
	return (string) trove_nearest_font_weight( $n, $allowed );
}

/**
 * @return string e.g. 1.5, 24px, 150%
 */
function trove_font_base_line_height_css_value() {
	$unit = trove_sanitize_font_base_line_height_unit( get_theme_mod( 'trove_font_base_line_height_unit', 'unitless' ) );
	$raw  = get_theme_mod( 'trove_font_base_line_height', 1.5 );
	$v    = is_numeric( $raw ) ? (float) $raw : 1.5;
	$v    = trove_sanitize_font_base_line_height( $v );

	if ( 'unitless' === $unit ) {
		$f = max( 1, min( 3, $v ) );
		return trove_format_css_float_string( $f );
	}
	if ( 'px' === $unit ) {
		$n = max( 12, min( 80, (int) round( $v ) ) );
		return $n . 'px';
	}
	if ( 'rem' === $unit || 'em' === $unit ) {
		$f = max( 0.5, min( 4, $v ) );
		return trove_format_css_float_string( $f ) . $unit;
	}
	if ( '%' === $unit ) {
		$n = max( 80, min( 250, (int) round( $v ) ) );
		return $n . '%';
	}
	return '1.5';
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
	$font_family = get_font_family_setting();
	$body_font   = get_body_font_family_css_value( $font_family );

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
	$root_parts[] = '--trove-body-font-family:' . $body_font;
	$root_parts[] = '--trove_font_base_size:' . esc_attr( trove_font_base_size_css_value() );
	$root_parts[] = '--trove_font_base_weight:' . esc_attr( trove_font_base_weight_css_value() );
	$root_parts[] = '--trove_font_base_line_height:' . esc_attr( trove_font_base_line_height_css_value() );

	$css  = ':root{' . implode( ';', $root_parts ) . '}';
	$css .= '.site-main{max-width:var(--trove-content);}';
	$css .= '@media (max-width:' . (int) $bp['tablet'] . 'px){:root{--trove-content:' . (int) $w_tablet . 'px;}}';
	$css .= '@media (max-width:' . (int) $bp['phone'] . 'px){:root{--trove-content:' . (int) $w_phone . 'px;}}';

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

	$preview_typography = array();
	foreach ( trove_typography_theme_mod_definitions() as $mod => $row ) {
		$preview_typography[ $mod ] = array(
			'var'     => $row['var'],
			'default' => $row['default'],
			'mode'    => $row['mode'],
		);
	}

	wp_enqueue_script(
		'trove-customizer-preview',
		$uri . '/inc/customizer/js/customizer-preview.js',
		array( 'customize-preview' ),
		$ver,
		true
	);

	wp_localize_script(
		'trove-customizer-preview',
		'troveCustomizerPreview',
		array(
			'colors'       => $preview_colors,
			'fonts'        => get_google_font_preview_data(),
			'typography'   => $preview_typography,
			'fontWeights'  => get_font_weight_map_for_customizer(),
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
		'vertical-centered-logo',
		'vertical-left-logo',
	);
	$layout = get_theme_mod( 'trove_header_layout', 'horizontal-left-logo' );
	$layout = is_string( $layout ) ? sanitize_key( $layout ) : '';
	if ( '' !== $layout && in_array( $layout, $allowed, true ) ) {
		$classes[] = 'trove-header__' . $layout;
	}

	$sticky_allowed = array( 'none', 'on_scroll_up', 'always' );
	$sticky         = get_theme_mod( 'trove_header_sticky', 'none' );
	$sticky         = is_string( $sticky ) ? sanitize_key( $sticky ) : 'none';
	if ( ! in_array( $sticky, $sticky_allowed, true ) ) {
		$sticky = 'none';
	}
	$classes[] = 'trove-header-sticky-' . $sticky;

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
