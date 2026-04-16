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
 * Body typography → :root CSS variables (`--trove-font-base-*`).
 *
 * @return array<string, array{var:string, default:string, mode:string}>
 */
function trove_typography_theme_mod_definitions() {
	$defs = array(
		'trove_font_base_weight' => array(
			'var'     => '--trove-font-base-weight',
			'default' => '400',
			'mode'    => 'weight',
		),
		'trove_menu_font_weight' => array(
			'var'     => '--trove-menu-font-weight',
			'default' => '500',
			'mode'    => 'weight',
		),
	);
	foreach ( trove_typography_font_scope_meta() as $scope => $_meta ) {
		$defs[ 'trove_' . $scope . '_font_weight' ] = array(
			'var'     => '--trove_' . $scope . '_font_weight',
			'default' => '400',
			'mode'    => 'weight',
		);
	}
	return $defs;
}

/**
 * Per-element typography scopes (used by CSS output + preview wiring).
 *
 * @return array<string, array{selector:string, default_size:string, default_weight:int, default_line_height:string}>
 */
function trove_typography_font_scope_meta() {
	return array(
		'h1'         => array( 'selector' => 'h1',         'default_size' => 2.5,  'default_weight' => 600, 'default_line_height' => 1.2 ),
		'h2'         => array( 'selector' => 'h2',         'default_size' => 2.0,    'default_weight' => 600, 'default_line_height' => 1.2 ),
		'h3'         => array( 'selector' => 'h3',         'default_size' => 1.75, 'default_weight' => 600, 'default_line_height' => 1.2 ),
		'h4'         => array( 'selector' => 'h4',         'default_size' => 1.5,  'default_weight' => 600, 'default_line_height' => 1.2 ),
		'h5'         => array( 'selector' => 'h5',         'default_size' => 1.25, 'default_weight' => 600, 'default_line_height' => 1.3 ),
		'h6'         => array( 'selector' => 'h6',         'default_size' => 1.0,    'default_weight' => 600, 'default_line_height' => 1.3 ),
		'blockquote' => array( 'selector' => 'blockquote', 'default_size' => 1.125,'default_weight' => 500, 'default_line_height' => 1.6 ),
	);
}

/**
 * Customizer family setting -> corresponding weight setting ids.
 *
 * @return array<string, string>
 */
function trove_font_weight_setting_pairs() {
	$pairs = array(
		'trove_google_font_family'      => 'trove_font_base_weight',
		'trove_menu_google_font_family' => 'trove_menu_font_weight',
	);
	foreach ( array_keys( trove_typography_font_scope_meta() ) as $scope ) {
		$pairs[ 'trove_' . $scope . '_google_font_family' ] = 'trove_' . $scope . '_font_weight';
	}
	return $pairs;
}

/**
 * Weight setting id -> family setting id map.
 *
 * @return array<string, string>
 */
function trove_font_weight_family_map() {
	$out = array();
	foreach ( trove_font_weight_setting_pairs() as $family_setting => $weight_setting ) {
		$out[ $weight_setting ] = $family_setting;
	}
	return $out;
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
 * @param mixed $value Raw weight.
 * @return int
 */
function trove_sanitize_font_weight_loose( $value ) {
	$n = absint( $value );
	return max( 1, min( 1000, $n ) );
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
 * @param string $number_setting Numeric value setting id.
 * @param string $unit_setting Unit setting id.
 * @param float  $default Default number value.
 * @param string $default_unit Default unit slug.
 * @return string
 */
function trove_number_unit_css_value( $number_setting, $unit_setting, $default, $default_unit ) {
	if ( 'unitless' === $default_unit || '%' === $default_unit ) {
		$unit = trove_sanitize_font_base_line_height_unit( get_theme_mod( $unit_setting, $default_unit ) );
		$raw  = get_theme_mod( $number_setting, $default );
		$v    = is_numeric( $raw ) ? (float) $raw : (float) $default;
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
		return trove_format_css_float_string( (float) $default );
	}

	$unit = trove_sanitize_font_base_size_unit( get_theme_mod( $unit_setting, $default_unit ) );
	$raw  = get_theme_mod( $number_setting, $default );
	$v    = is_numeric( $raw ) ? (float) $raw : (float) $default;
	$v    = trove_sanitize_font_base_size( $v );
	if ( 'px' === $unit ) {
		$n = (int) round( $v );
		$n = max( 10, min( 32, $n ) );
		return $n . 'px';
	}
	$f = max( 0.5, min( 2.5, $v ) );
	return trove_format_css_float_string( $f ) . $unit;
}

/**
 * @param string $weight_setting Weight setting id.
 * @param string $family_setting Family setting id.
 * @param int    $default_weight Default value.
 * @return string
 */
function trove_font_weight_css_value_for_settings( $weight_setting, $family_setting, $default_weight ) {
	$n      = absint( get_theme_mod( $weight_setting, $default_weight ) );
	$family = sanitize_google_font_family( get_theme_mod( $family_setting, 'system' ) );
	return (string) trove_nearest_font_weight( $n, get_font_weight_values_for_family( $family ) );
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
	$root_parts[] = '--trove-menu-font-family:' . get_body_font_family_css_value( sanitize_google_font_family( get_theme_mod( 'trove_menu_google_font_family', 'system' ) ) );
	$root_parts[] = '--trove-menu-font-size:' . esc_attr( trove_number_unit_css_value( 'trove_menu_font_size', 'trove_menu_font_size_unit', 16, 'px' ) );
	$root_parts[] = '--trove-menu-font-weight:' . esc_attr( trove_font_weight_css_value_for_settings( 'trove_menu_font_weight', 'trove_menu_google_font_family', 500 ) );
	$root_parts[] = '--trove-menu-font-line-height:' . esc_attr( trove_number_unit_css_value( 'trove_menu_font_line_height', 'trove_menu_font_line_height_unit', 1.5, 'unitless' ) );
	$root_parts[] = '--trove-font-base-size:' . esc_attr( trove_font_base_size_css_value() );
	$root_parts[] = '--trove-font-base-weight:' . esc_attr( trove_font_base_weight_css_value() );
	$root_parts[] = '--trove-font-base-line-height:' . esc_attr( trove_font_base_line_height_css_value() );

	foreach ( trove_typography_font_scope_meta() as $scope => $meta ) {
		$prefix = 'trove_' . $scope;
		$family = sanitize_google_font_family( get_theme_mod( $prefix . '_google_font_family', 'system' ) );
		$root_parts[] = '--trove-' . $scope . '-font-family:' . get_body_font_family_css_value( $family );
		$root_parts[] = '--trove-' . $scope . '-font-size:' . esc_attr( trove_number_unit_css_value( $prefix . '_font_size', $prefix . '_font_size_unit', (float) $meta['default_size'], 'rem' ) );
		$root_parts[] = '--trove-' . $scope . '-font-weight:' . esc_attr( trove_font_weight_css_value_for_settings( $prefix . '_font_weight', $prefix . '_google_font_family', (int) $meta['default_weight'] ) );
		$root_parts[] = '--trove-' . $scope . '-font-line-height:' . esc_attr( trove_number_unit_css_value( $prefix . '_font_line_height', $prefix . '_font_line_height_unit', (float) $meta['default_line_height'], 'unitless' ) );
	}

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
 * Preview map: family setting id -> CSS variable name.
 *
 * @return array<string, string>
 */
function trove_preview_font_family_var_map() {
	$map = array(
		'trove_google_font_family'      => '--trove-body-font-family',
		'trove_menu_google_font_family' => '--trove-menu-font-family',
	);
	foreach ( array_keys( trove_typography_font_scope_meta() ) as $scope ) {
		$map[ 'trove_' . $scope . '_google_font_family' ] = '--trove-' . $scope . '-font-family';
	}
	return $map;
}

/**
 * Preview map for number+unit controls -> css variable and mode.
 *
 * @return array<string, array{unit:string,var:string,mode:string}>
 */
function trove_preview_number_unit_pairs() {
	$pairs = array(
		'trove_font_base_size' => array( 'unit' => 'trove_font_base_size_unit', 'var' => '--trove-font-base-size', 'mode' => 'size' ),
		'trove_menu_font_size' => array( 'unit' => 'trove_menu_font_size_unit', 'var' => '--trove-menu-font-size', 'mode' => 'size' ),
		'trove_font_base_line_height' => array( 'unit' => 'trove_font_base_line_height_unit', 'var' => '--trove-font-base-line-height', 'mode' => 'line_height' ),
		'trove_menu_font_line_height' => array( 'unit' => 'trove_menu_font_line_height_unit', 'var' => '--trove-menu-font-line-height', 'mode' => 'line_height' ),
	);
	foreach ( array_keys( trove_typography_font_scope_meta() ) as $scope ) {
		$prefix = 'trove_' . $scope;
		$pairs[ $prefix . '_font_size' ] = array( 'unit' => $prefix . '_font_size_unit', 'var' => '--trove-' . $scope . '-font-size', 'mode' => 'size' );
		$pairs[ $prefix . '_font_line_height' ] = array( 'unit' => $prefix . '_font_line_height_unit', 'var' => '--trove-' . $scope . '-font-line-height', 'mode' => 'line_height' );
	}
	return $pairs;
}

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
			'colors'          => $preview_colors,
			'fonts'           => get_google_font_preview_data(),
			'typography'      => $preview_typography,
			'fontWeights'     => get_font_weight_map_for_customizer(),
			'fontFamilies'    => trove_preview_font_family_var_map(),
			'weightFamilies'  => trove_font_weight_family_map(),
			'numberUnitPairs' => trove_preview_number_unit_pairs(),
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

	$align_allowed = array( 'left', 'center', 'right' );
	$align         = get_theme_mod( 'trove_header_menu_alignment', 'right' );
	$align         = is_string( $align ) ? sanitize_key( $align ) : 'right';
	if ( ! in_array( $align, $align_allowed, true ) ) {
		$align = 'right';
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

/**
 * Class names for header nav element.
 *
 * @return string[]
 */
function get_header_nav_classnames() {
	$classes = array( 'site-header__nav' );
	$align_allowed = array( 'left', 'center', 'right' );
	$align         = get_theme_mod( 'trove_header_menu_alignment', 'right' );
	$align         = is_string( $align ) ? sanitize_key( $align ) : 'right';
	if ( ! in_array( $align, $align_allowed, true ) ) {
		$align = 'right';
	}
	$classes[] = 'site-header__nav-' . $align;
	return apply_filters( 'trove_header_nav_classnames', $classes );
}

/**
 * Echoes class attribute for header nav element.
 *
 * @return void
 */
function the_header_nav_class_attr() {
	$classes = get_header_nav_classnames();
	echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $classes ) ) );
}
