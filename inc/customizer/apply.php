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
		'trove_color_page_background'  => array(
			'var'     => '--trove-page-bg',
			'default' => '#ffffff',
		),
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
		'trove_header_background'      => array(
			'var'     => '--trove-header-background',
			'default' => '#ffffff',
		),
		'trove_header_text_color'      => array(
			'var'     => '--trove-header-text',
			'default' => '#111111',
		),
		'trove_header_link_color'      => array(
			'var'     => '--trove-header-link',
			'default' => '#064fff',
		),
		'trove_header_link_hover_color' => array(
			'var'     => '--trove-header-link-hover',
			'default' => '#003dcc',
		),
		'trove_footer_background'      => array(
			'var'     => '--trove-footer-background',
			'default' => '#eeeeee',
		),
		'trove_footer_text_color'      => array(
			'var'     => '--trove-footer-text',
			'default' => '#111111',
		),
		'trove_footer_link_color'      => array(
			'var'     => '--trove-footer-link',
			'default' => '#064fff',
		),
		'trove_footer_link_hover_color' => array(
			'var'     => '--trove-footer-link-hover',
			'default' => '#003dcc',
		),
		'trove_header_cart_badge_color' => array(
			'var'     => '--trove-header-cart-badge-bg',
			'default' => '#064fff',
		),
		'trove_header_cart_count_color' => array(
			'var'     => '--trove-header-cart-count',
			'default' => '#ffffff',
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
 * Typography theme mods mapped to CSS variables.
 *
 * @return array<string, array{var:string, default:string, mode:string}>
 */
function trove_typography_theme_mod_definitions() {
	$defs = array();
	foreach ( trove_typography_font_scope_meta() as $scope => $meta ) {
		$weight_setting = isset( $meta['weight_setting'] ) ? (string) $meta['weight_setting'] : 'trove_' . $scope . '_font_weight';
		$weight_var     = isset( $meta['weight_var'] ) ? (string) $meta['weight_var'] : '--trove-' . $scope . '-font-weight';

		$defs[ $weight_setting ] = array(
			'var'     => $weight_var,
			'default' => (string) $meta['default_weight'],
			'mode'    => 'weight',
		);
	}
	return $defs;
}

/**
 * Typography scopes used by CSS output + preview wiring.
 *
 * @return array<string, array<string, mixed>>
 */
function trove_typography_font_scope_meta() {
	return array(
		'body'       => array(
			'selector'            => 'body',
			'family_setting'      => 'trove_google_font_family',
			'size_setting'        => 'trove_font_base_size',
			'size_unit_setting'   => 'trove_font_base_size_unit',
			'weight_setting'      => 'trove_font_base_weight',
			'line_setting'        => 'trove_font_base_line_height',
			'line_unit_setting'   => 'trove_font_base_line_height_unit',
			'family_var'          => '--trove-body-font-family',
			'size_var'            => '--trove-font-base-size',
			'weight_var'          => '--trove-font-base-weight',
			'line_var'            => '--trove-font-base-line-height',
			'default_family'      => 'Instrument Sans',
			'default_size'        => 16,
			'default_size_unit'   => 'px',
			'default_weight'      => 400,
			'default_line_height' => 1.5,
			'default_line_unit'   => 'unitless',
		),
		'menu'       => array(
			'selector'            => '.site-header__nav .primary-menu',
			'family_setting'      => 'trove_menu_google_font_family',
			'size_setting'        => 'trove_menu_font_size',
			'size_unit_setting'   => 'trove_menu_font_size_unit',
			'weight_setting'      => 'trove_menu_font_weight',
			'line_setting'        => 'trove_menu_font_line_height',
			'line_unit_setting'   => 'trove_menu_font_line_height_unit',
			'family_var'          => '--trove-menu-font-family',
			'size_var'            => '--trove-menu-font-size',
			'weight_var'          => '--trove-menu-font-weight',
			'line_var'            => '--trove-menu-font-line-height',
			'default_family'      => 'Instrument Sans',
			'default_size'        => 16,
			'default_size_unit'   => 'px',
			'default_weight'      => 500,
			'default_line_height' => 1.5,
			'default_line_unit'   => 'unitless',
		),
		'h1'         => array( 'selector' => 'h1', 'family_setting' => 'trove_h1_google_font_family', 'size_setting' => 'trove_h1_font_size', 'size_unit_setting' => 'trove_h1_font_size_unit', 'weight_setting' => 'trove_h1_font_weight', 'line_setting' => 'trove_h1_font_line_height', 'line_unit_setting' => 'trove_h1_font_line_height_unit', 'family_var' => '--trove-h1-font-family', 'size_var' => '--trove-h1-font-size', 'weight_var' => '--trove-h1-font-weight', 'line_var' => '--trove-h1-font-line-height', 'default_size' => 4.0, 'default_size_unit' => 'rem', 'default_weight' => 600, 'default_line_height' => 1.2, 'default_line_unit' => 'unitless', 'default_family' => 'Instrument Sans' ),
		'h2'         => array( 'selector' => 'h2', 'family_setting' => 'trove_h2_google_font_family', 'size_setting' => 'trove_h2_font_size', 'size_unit_setting' => 'trove_h2_font_size_unit', 'weight_setting' => 'trove_h2_font_weight', 'line_setting' => 'trove_h2_font_line_height', 'line_unit_setting' => 'trove_h2_font_line_height_unit', 'family_var' => '--trove-h2-font-family', 'size_var' => '--trove-h2-font-size', 'weight_var' => '--trove-h2-font-weight', 'line_var' => '--trove-h2-font-line-height', 'default_size' => 3.0, 'default_size_unit' => 'rem', 'default_weight' => 600, 'default_line_height' => 1.2, 'default_line_unit' => 'unitless', 'default_family' => 'Instrument Sans' ),
		'h3'         => array( 'selector' => 'h3', 'family_setting' => 'trove_h3_google_font_family', 'size_setting' => 'trove_h3_font_size', 'size_unit_setting' => 'trove_h3_font_size_unit', 'weight_setting' => 'trove_h3_font_weight', 'line_setting' => 'trove_h3_font_line_height', 'line_unit_setting' => 'trove_h3_font_line_height_unit', 'family_var' => '--trove-h3-font-family', 'size_var' => '--trove-h3-font-size', 'weight_var' => '--trove-h3-font-weight', 'line_var' => '--trove-h3-font-line-height', 'default_size' => 2.4, 'default_size_unit' => 'rem', 'default_weight' => 600, 'default_line_height' => 1.2, 'default_line_unit' => 'unitless', 'default_family' => 'Instrument Sans' ),
		'h4'         => array( 'selector' => 'h4', 'family_setting' => 'trove_h4_google_font_family', 'size_setting' => 'trove_h4_font_size', 'size_unit_setting' => 'trove_h4_font_size_unit', 'weight_setting' => 'trove_h4_font_weight', 'line_setting' => 'trove_h4_font_line_height', 'line_unit_setting' => 'trove_h4_font_line_height_unit', 'family_var' => '--trove-h4-font-family', 'size_var' => '--trove-h4-font-size', 'weight_var' => '--trove-h4-font-weight', 'line_var' => '--trove-h4-font-line-height', 'default_size' => 2.0, 'default_size_unit' => 'rem', 'default_weight' => 600, 'default_line_height' => 1.2, 'default_line_unit' => 'unitless', 'default_family' => 'Instrument Sans' ),
		'h5'         => array( 'selector' => 'h5', 'family_setting' => 'trove_h5_google_font_family', 'size_setting' => 'trove_h5_font_size', 'size_unit_setting' => 'trove_h5_font_size_unit', 'weight_setting' => 'trove_h5_font_weight', 'line_setting' => 'trove_h5_font_line_height', 'line_unit_setting' => 'trove_h5_font_line_height_unit', 'family_var' => '--trove-h5-font-family', 'size_var' => '--trove-h5-font-size', 'weight_var' => '--trove-h5-font-weight', 'line_var' => '--trove-h5-font-line-height', 'default_size' => 1.8, 'default_size_unit' => 'rem', 'default_weight' => 600, 'default_line_height' => 1.3, 'default_line_unit' => 'unitless', 'default_family' => 'Instrument Sans' ),
		'h6'         => array( 'selector' => 'h6', 'family_setting' => 'trove_h6_google_font_family', 'size_setting' => 'trove_h6_font_size', 'size_unit_setting' => 'trove_h6_font_size_unit', 'weight_setting' => 'trove_h6_font_weight', 'line_setting' => 'trove_h6_font_line_height', 'line_unit_setting' => 'trove_h6_font_line_height_unit', 'family_var' => '--trove-h6-font-family', 'size_var' => '--trove-h6-font-size', 'weight_var' => '--trove-h6-font-weight', 'line_var' => '--trove-h6-font-line-height', 'default_size' => 1.6, 'default_size_unit' => 'rem', 'default_weight' => 600, 'default_line_height' => 1.3, 'default_line_unit' => 'unitless', 'default_family' => 'Instrument Sans' ),
		'blockquote' => array( 'selector' => 'blockquote', 'family_setting' => 'trove_blockquote_google_font_family', 'size_setting' => 'trove_blockquote_font_size', 'size_unit_setting' => 'trove_blockquote_font_size_unit', 'weight_setting' => 'trove_blockquote_font_weight', 'line_setting' => 'trove_blockquote_font_line_height', 'line_unit_setting' => 'trove_blockquote_font_line_height_unit', 'family_var' => '--trove-blockquote-font-family', 'size_var' => '--trove-blockquote-font-size', 'weight_var' => '--trove-blockquote-font-weight', 'line_var' => '--trove-blockquote-font-line-height', 'default_size' => 1.125, 'default_size_unit' => 'rem', 'default_weight' => 500, 'default_line_height' => 1.6, 'default_line_unit' => 'unitless', 'default_family' => 'Playfair Display' ),
		'accent'     => array(
			'selector'            => '',
			'family_setting'      => 'trove_accent_google_font_family',
			'size_setting'        => 'trove_accent_font_size',
			'size_unit_setting'   => 'trove_accent_font_size_unit',
			'weight_setting'      => 'trove_accent_font_weight',
			'line_setting'        => 'trove_accent_font_line_height',
			'line_unit_setting'   => 'trove_accent_font_line_height_unit',
			'family_var'          => '--trove-accent-font-family',
			'size_var'            => '--trove-accent-font-size',
			'weight_var'          => '--trove-accent-font-weight',
			'line_var'            => '--trove-accent-font-line-height',
			'default_family'      => 'Playfair Display',
			'default_size'        => 1.125,
			'default_size_unit'   => 'rem',
			'default_weight'      => 500,
			'default_line_height' => 1.6,
			'default_line_unit'   => 'unitless',
		),
	);
}

/**
 * Customizer family setting -> corresponding weight setting ids.
 *
 * @return array<string, string>
 */
function trove_font_weight_setting_pairs() {
	$pairs = array();
	foreach ( trove_typography_font_scope_meta() as $scope => $meta ) {
		$family_setting = isset( $meta['family_setting'] ) ? (string) $meta['family_setting'] : 'trove_' . $scope . '_google_font_family';
		$weight_setting = isset( $meta['weight_setting'] ) ? (string) $meta['weight_setting'] : 'trove_' . $scope . '_font_weight';
		$pairs[ $family_setting ] = $weight_setting;
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
		$n = max( 1, min( 500, $n ) );
		return $n . 'px';
	}
	$f = max( 0.25, min( 32, $v ) );
	return trove_format_css_float_string( $f ) . $unit;
}

/**
 * Min-height CSS for announcement bar from a raw number + unit (matches Customizer input range).
 *
 * @param mixed  $raw  Raw number.
 * @param string $unit `px` or `rem`.
 * @return string
 */
function trove_announcement_height_raw_to_css( $raw, $unit ) {
	$unit = trove_sanitize_font_base_size_unit( (string) $unit );
	$v    = is_numeric( $raw ) ? (float) $raw : 48.0;
	$v    = trove_sanitize_font_base_size( $v );
	if ( 'px' === $unit ) {
		$n = (int) round( $v );
		$n = max( 24, min( 120, $n ) );
		return $n . 'px';
	}
	$f = max( 0.25, min( 32, $v ) );
	return trove_format_css_float_string( $f ) . $unit;
}

/**
 * Hide `.trove-announcement-bar` per breakpoint when that device’s “show” toggle is off.
 *
 * @param array<string, mixed> $enabled_by_device Keys pc, tablet, phone (booleans).
 * @param array{tablet:int,phone:int} $bp Breakpoints (max-width).
 * @return string CSS fragment.
 */
function trove_announcement_bar_responsive_visibility_css( array $enabled_by_device, array $bp ) {
	$e_pc  = wp_validate_boolean( isset( $enabled_by_device['pc'] ) ? $enabled_by_device['pc'] : true );
	$e_tab = wp_validate_boolean( isset( $enabled_by_device['tablet'] ) ? $enabled_by_device['tablet'] : true );
	$e_ph  = wp_validate_boolean( isset( $enabled_by_device['phone'] ) ? $enabled_by_device['phone'] : true );
	if ( $e_pc && $e_tab && $e_ph ) {
		return '';
	}
	$tablet = isset( $bp['tablet'] ) ? (int) $bp['tablet'] : 1024;
	$phone  = isset( $bp['phone'] ) ? (int) $bp['phone'] : 781;
	$css    = '';
	if ( ! $e_pc ) {
		$css .= '@media (min-width:' . ( $tablet + 1 ) . 'px){.trove-announcement-bar{display:none!important;}}';
	}
	if ( ! $e_tab ) {
		$css .= '@media (max-width:' . $tablet . 'px) and (min-width:' . ( $phone + 1 ) . 'px){.trove-announcement-bar{display:none!important;}}';
	}
	if ( ! $e_ph ) {
		$css .= '@media (max-width:' . $phone . 'px){.trove-announcement-bar{display:none!important;}}';
	}
	return $css;
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
 * Inline CSS for accent, content width, and header border.
 */
function trove_build_theme_variable_css( $selector = ':root', $include_responsive_content = true ) {
	$font_family = get_font_family_setting();

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

	$footer_color_mods = array(
		'trove_footer_border_color' => '--trove-footer-border',
	);
	foreach ( $footer_color_mods as $mod_key => $css_var ) {
		$c = trove_sanitize_css_color( (string) get_theme_mod( $mod_key, '' ) );
		if ( '' !== $c ) {
			$root_parts[] = $css_var . ':' . esc_attr( $c );
		}
	}

	$header_color_mods = array(
		'trove_header_border_color' => '--trove-header-border',
	);
	foreach ( $header_color_mods as $mod_key => $css_var ) {
		$c = trove_sanitize_css_color( (string) get_theme_mod( $mod_key, '' ) );
		if ( '' !== $c ) {
			$root_parts[] = $css_var . ':' . esc_attr( $c );
		}
	}

	$ann_h_vals = get_responsive_mod_values(
		'trove_announcement_height',
		array(
			'pc'     => 48,
			'tablet' => 48,
			'phone'  => 48,
		)
	);
	$ann_h_unit = (string) get_theme_mod( 'trove_announcement_height_unit', 'px' );
	$fz_vals    = get_responsive_mod_values(
		'trove_announcement_text_size',
		array(
			'pc'     => 'medium',
			'tablet' => 'medium',
			'phone'  => 'medium',
		)
	);
	$root_parts[] = '--trove-announcement-min-height:' . esc_attr( trove_announcement_height_raw_to_css( $ann_h_vals['pc'], $ann_h_unit ) );
	$root_parts[] = '--trove-announcement-font-size:' . esc_attr( \Trove\trove_announcement_text_size_slug_to_css( $fz_vals['pc'] ) );

	$announcement_color_mods = array(
		'trove_announcement_background'       => '--trove-announcement-background',
		'trove_announcement_text_color'       => '--trove-announcement-text',
		'trove_announcement_link_color'     => '--trove-announcement-link',
		'trove_announcement_link_hover_color' => '--trove-announcement-link-hover',
	);
	foreach ( $announcement_color_mods as $mod_key => $css_var ) {
		$c = trove_sanitize_css_color( (string) get_theme_mod( $mod_key, '' ) );
		if ( '' !== $c ) {
			$root_parts[] = $css_var . ':' . esc_attr( $c );
		}
	}

	$header_logo_w = \Trove\get_header_logo_width();
	$root_parts[]  = '--trove-header-logo-max-width:' . (int) $header_logo_w . 'px';

	$root_parts[] = '--trove-wide:1200px';
	$root_parts[] = '--trove-content:' . (int) $w_pc . 'px';

	$boxed_radius = absint( get_theme_mod( 'trove_blog_boxed_border_radius', 10 ) );
	$boxed_radius = max( 0, min( 20, $boxed_radius ) );
	$root_parts[] = '--trove-blog-boxed-radius:' . (int) $boxed_radius . 'px';
	$root_parts[] = '--trove-font-body-scale:1';
	foreach ( trove_typography_font_scope_meta() as $scope => $meta ) {
		$prefix = 'trove_' . $scope;
		$family_setting = isset( $meta['family_setting'] ) ? (string) $meta['family_setting'] : $prefix . '_google_font_family';
		$size_setting   = isset( $meta['size_setting'] ) ? (string) $meta['size_setting'] : $prefix . '_font_size';
		$size_unit      = isset( $meta['size_unit_setting'] ) ? (string) $meta['size_unit_setting'] : $prefix . '_font_size_unit';
		$weight_setting = isset( $meta['weight_setting'] ) ? (string) $meta['weight_setting'] : $prefix . '_font_weight';
		$line_setting   = isset( $meta['line_setting'] ) ? (string) $meta['line_setting'] : $prefix . '_font_line_height';
		$line_unit      = isset( $meta['line_unit_setting'] ) ? (string) $meta['line_unit_setting'] : $prefix . '_font_line_height_unit';
		$family_var     = isset( $meta['family_var'] ) ? (string) $meta['family_var'] : '--trove-' . $scope . '-font-family';
		$size_var       = isset( $meta['size_var'] ) ? (string) $meta['size_var'] : '--trove-' . $scope . '-font-size';
		$weight_var     = isset( $meta['weight_var'] ) ? (string) $meta['weight_var'] : '--trove-' . $scope . '-font-weight';
		$line_var       = isset( $meta['line_var'] ) ? (string) $meta['line_var'] : '--trove-' . $scope . '-font-line-height';
		$family_default = isset( $meta['default_family'] ) ? (string) $meta['default_family'] : 'system';
		$size_default   = isset( $meta['default_size'] ) ? (float) $meta['default_size'] : 1;
		$size_unit_def  = isset( $meta['default_size_unit'] ) ? (string) $meta['default_size_unit'] : 'rem';
		$line_default   = isset( $meta['default_line_height'] ) ? (float) $meta['default_line_height'] : 1.5;
		$line_unit_def  = isset( $meta['default_line_unit'] ) ? (string) $meta['default_line_unit'] : 'unitless';
		$weight_default = isset( $meta['default_weight'] ) ? (int) $meta['default_weight'] : 400;

		$family = sanitize_google_font_family( get_theme_mod( $family_setting, $family_default ) );
		$root_parts[] = $family_var . ':' . get_body_font_family_css_value( $family );
		$root_parts[] = $size_var . ':' . esc_attr( trove_number_unit_css_value( $size_setting, $size_unit, $size_default, $size_unit_def ) );
		$root_parts[] = $weight_var . ':' . esc_attr( trove_font_weight_css_value_for_settings( $weight_setting, $family_setting, $weight_default ) );
		$root_parts[] = $line_var . ':' . esc_attr( trove_number_unit_css_value( $line_setting, $line_unit, $line_default, $line_unit_def ) );
	}

	$css  = $selector . '{' . implode( ';', $root_parts ) . '}';
	$css .= '.site-main{max-width:var(--trove-content);}';
	$css .= '.site-footer__extra-responsive .trove-fe--tablet,.site-footer__extra-responsive .trove-fe--phone{display:none;}';
	$css .= '.site-footer__extra-responsive .trove-fe--pc{display:block;}';
	if ( $include_responsive_content ) {
		$css .= '@media (max-width:' . (int) $bp['tablet'] . 'px){' . $selector . '{--trove-content:' . (int) $w_tablet . 'px;--trove-announcement-min-height:' . esc_attr( trove_announcement_height_raw_to_css( $ann_h_vals['tablet'], $ann_h_unit ) ) . ';--trove-announcement-font-size:' . esc_attr( \Trove\trove_announcement_text_size_slug_to_css( $fz_vals['tablet'] ) ) . ';}}';
	}
	$css .= '@media (max-width:' . (int) $bp['tablet'] . 'px){.site-footer__extra-responsive .trove-fe--pc{display:none;}.site-footer__extra-responsive .trove-fe--tablet{display:block;}}';
	if ( $include_responsive_content ) {
		$css .= '@media (max-width:' . (int) $bp['phone'] . 'px){' . $selector . '{--trove-content:' . (int) $w_phone . 'px;--trove-announcement-min-height:' . esc_attr( trove_announcement_height_raw_to_css( $ann_h_vals['phone'], $ann_h_unit ) ) . ';--trove-announcement-font-size:' . esc_attr( \Trove\trove_announcement_text_size_slug_to_css( $fz_vals['phone'] ) ) . ';}.site-footer__extra-responsive .trove-fe--tablet{display:none;}.site-footer__extra-responsive .trove-fe--phone{display:block;}}';
	} else {
		$css .= '@media (max-width:' . (int) $bp['phone'] . 'px){.site-footer__extra-responsive .trove-fe--tablet{display:none;}.site-footer__extra-responsive .trove-fe--phone{display:block;}}';
	}

	$header_min = absint( get_theme_mod( 'trove_header_min_height', 0 ) );
	if ( $header_min > 0 && $header_min <= 400 ) {
		$css .= '.site-header{min-height:' . $header_min . 'px;}';
	}

	$ann_enabled = get_responsive_mod_values(
		'trove_announcement_enabled',
		array(
			'pc'     => true,
			'tablet' => true,
			'phone'  => true,
		)
	);
	$css .= trove_announcement_bar_responsive_visibility_css( $ann_enabled, $bp );

	return $css;
}

/**
 * Inline CSS for accent, content width, and header border.
 */
function output_customizer_css() {
	if ( ! wp_style_is( 'trove-style', 'enqueued' ) ) {
		return;
	}

	wp_add_inline_style( 'trove-style', trove_build_theme_variable_css( ':root', true ) );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\output_customizer_css', 20 );

/**
 * Injects the same CSS variables into the block editor.
 *
 * @return void
 */
function enqueue_editor_customizer_css() {
	if ( ! is_admin() ) {
		return;
	}

	$handle = 'trove-editor-vars';
	if ( ! wp_style_is( $handle, 'registered' ) ) {
		wp_register_style( $handle, false, array(), defined( 'TROVE_THEME_VERSION' ) ? TROVE_THEME_VERSION : '1.0.0' );
	}

	wp_enqueue_style( $handle );
	wp_add_inline_style( $handle, trove_build_theme_variable_css( ':root, .editor-styles-wrapper', false ) );
}
add_action( 'enqueue_block_assets', __NAMESPACE__ . '\\enqueue_editor_customizer_css', 20 );


/**
 * Preview map: family setting id -> CSS variable name.
 *
 * @return array<string, string>
 */
function trove_preview_font_family_var_map() {
	$map = array();
	foreach ( trove_typography_font_scope_meta() as $scope => $meta ) {
		$family_setting         = isset( $meta['family_setting'] ) ? (string) $meta['family_setting'] : 'trove_' . $scope . '_google_font_family';
		$map[ $family_setting ] = isset( $meta['family_var'] ) ? (string) $meta['family_var'] : '--trove-' . $scope . '-font-family';
	}
	return $map;
}

/**
 * Preview map for number+unit controls -> css variable and mode.
 *
 * @return array<string, array{unit:string,var:string,mode:string}>
 */
function trove_preview_number_unit_pairs() {
	$pairs = array();
	foreach ( trove_typography_font_scope_meta() as $scope => $meta ) {
		$prefix        = 'trove_' . $scope;
		$size_setting  = isset( $meta['size_setting'] ) ? (string) $meta['size_setting'] : $prefix . '_font_size';
		$size_unit     = isset( $meta['size_unit_setting'] ) ? (string) $meta['size_unit_setting'] : $prefix . '_font_size_unit';
		$line_setting  = isset( $meta['line_setting'] ) ? (string) $meta['line_setting'] : $prefix . '_font_line_height';
		$line_unit     = isset( $meta['line_unit_setting'] ) ? (string) $meta['line_unit_setting'] : $prefix . '_font_line_height_unit';
		$size_var      = isset( $meta['size_var'] ) ? (string) $meta['size_var'] : '--trove-' . $scope . '-font-size';
		$line_var      = isset( $meta['line_var'] ) ? (string) $meta['line_var'] : '--trove-' . $scope . '-font-line-height';

		$pairs[ $size_setting ] = array( 'unit' => $size_unit, 'var' => $size_var, 'mode' => 'size' );
		$pairs[ $line_setting ] = array( 'unit' => $line_unit, 'var' => $line_var, 'mode' => 'line_height' );
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
	$preview_color_exclude = array(
		'trove_header_cart_badge_color',
		'trove_header_cart_count_color',
	);
	foreach ( trove_color_theme_mod_definitions() as $mod => $row ) {
		if ( in_array( $mod, $preview_color_exclude, true ) ) {
			continue;
		}
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
			'footer'          => array(
				'allRightsReserved'           => __( 'All rights reserved.', 'trove' ),
				'newsletterTitleDefault'      => __( 'Subscribe to our emails', 'trove' ),
				'newsletterDescriptionDefault' => __( 'Subscribe to our newsletter to get the latest news and updates.', 'trove' ),
			),
			'woocommerceHeaderCart'        => class_exists( 'WooCommerce' ),
			'defaultAnnouncementLinesJson' => \Trove\get_default_announcement_lines_json(),
			'responsiveBreakpoints'          => get_responsive_breakpoints_max(),
		)
	);
}
add_action( 'customize_preview_init', __NAMESPACE__ . '\\enqueue_customizer_preview_scripts' );

/**
 * Registers selective refresh for all footer theme_mods (no full preview iframe reload).
 *
 * @param \WP_Customize_Manager $wp_customize Customizer instance.
 * @return void
 */
function register_footer_content_preview_partial( $wp_customize ) {
	if ( ! $wp_customize instanceof \WP_Customize_Manager || ! isset( $wp_customize->selective_refresh ) ) {
		return;
	}

	// Layout/logo/menus: selective refresh. Color overrides: postMessage in customizer-preview.js only — avoids preview iframe full reload.
	$settings = array(
		'trove_footer_layout',
		'trove_footer_logo',
	);
	for ( $i = 1; $i <= 5; $i++ ) {
		$settings[] = 'trove_footer_menu_' . $i . '_id';
	}

	$wp_customize->selective_refresh->add_partial(
		'trove_footer',
		array(
			'selector'        => 'footer.site-footer',
			'settings'        => $settings,
			'render_callback' => __NAMESPACE__ . '\\render_footer_content_preview_partial',
		)
	);
}
add_action( 'customize_register', __NAMESPACE__ . '\\register_footer_content_preview_partial', 100 );

/**
 * @return string
 */
function render_footer_content_preview_partial() {
	if ( ! function_exists( '\Trove\render_footer' ) ) {
		return '';
	}
	ob_start();
	\Trove\render_footer();
	return ob_get_clean();
}

/**
 * Class names for the theme `<header>` (always includes `site-header`).
 *
 * @return string[]
 */
function get_header_classnames() {
	$classes = array( 'site-header' );
	$classes[] = 'light-scheme';
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
