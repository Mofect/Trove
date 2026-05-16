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
function color_theme_mod_definitions() {
	static $cached = null;
	if ( null !== $cached ) {
		return $cached;
	}
	$map = array(
		'trove_color_page_background'         => '--trove-page-bg',
		'trove_color_base_text'               => '--trove-text',
		'trove_color_headings'                => '--trove-heading',
		'trove_color_link'                    => '--trove-link',
		'trove_color_link_hover'              => '--trove-link-hover',
		'trove_color_selection_text'          => '--trove-selection-color',
		'trove_color_selection_bg'            => '--trove-selection-bg',
		'trove_color_border'                  => '--trove-border',
		'trove_color_accent'                  => '--trove-accent',
		'trove_announcement_background'       => '--trove-announcement-background',
		'trove_announcement_text_color'       => '--trove-announcement-text',
		'trove_announcement_link_color'       => '--trove-announcement-link',
		'trove_announcement_link_hover_color' => '--trove-announcement-link-hover',
		'trove_header_background'             => '--trove-header-background',
		'trove_header_text_color'             => '--trove-header-text',
		'trove_header_link_color'             => '--trove-header-link',
		'trove_header_link_hover_color'       => '--trove-header-link-hover',
		'trove_footer_background'             => '--trove-footer-background',
		'trove_footer_text_color'             => '--trove-footer-text',
		'trove_footer_link_color'             => '--trove-footer-link',
		'trove_footer_link_hover_color'       => '--trove-footer-link-hover',
		'trove_footer_border_color'           => '--trove-footer-border',
		'trove_header_cart_badge_color'       => '--trove-header-cart-badge-bg',
		'trove_header_cart_count_color'       => '--trove-header-cart-count',
		'trove_store_notice_background'       => '--trove-store-notice-background',
		'trove_store_notice_text_color'       => '--trove-store-notice-text',
		'trove_store_notice_link_color'       => '--trove-store-notice-link',
		'trove_store_notice_link_hover_color' => '--trove-store-notice-link-hover',
		'trove_woo_info_message_background'   => '--trove-woo-info-message-bg',
		'trove_woo_info_message_text'         => '--trove-woo-info-message-text',
		'trove_woo_failed_message_background' => '--trove-woo-failed-message-bg',
		'trove_woo_failed_message_text'       => '--trove-woo-failed-message-text',
		'trove_woo_success_message_background' => '--trove-woo-success-message-bg',
		'trove_woo_success_message_text'       => '--trove-woo-success-message-text',
		'trove_woo_cart_free_shipping_bar_color' => '--trove-woo-free-shipping-bar-color',
	);
	$cached = array();
	foreach ( $map as $key => $var ) {
		$cached[ $key ] = array(
			'var'     => $var,
			'default' => (string) customizer_default( $key ),
		);
	}
	return $cached;
}

/**
 * Theme mod keys included in global color scheme presets (order = swatch strip left → right).
 *
 * @return list<string>
 */
function global_color_scheme_setting_keys() {
	return array_keys( color_theme_mod_definitions() );
}

/**
 * Typography theme mods mapped to CSS variables.
 *
 * @return array<string, array{var:string, default:string, mode:string}>
 */
function typography_theme_mod_definitions() {
	$defs = array();
	foreach ( typography_font_scope_meta() as $scope => $meta ) {
		$weight_setting = isset( $meta['weight_setting'] ) ? (string) $meta['weight_setting'] : 'trove_' . $scope . '_font_weight';
		$weight_var     = isset( $meta['weight_var'] ) ? (string) $meta['weight_var'] : '--trove-' . $scope . '-font-weight';

		$defs[ $weight_setting ] = array(
			'var'     => $weight_var,
			'default' => (string) customizer_default( $weight_setting, (string) $meta['default_weight'] ),
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
function typography_font_scope_meta() {
	return array(
		'body'       => array(
			'selector'            => 'body',
			'family_setting'      => 'trove_google_font_family',
			'size_setting'        => 'trove_font_base_size',
			'size_unit_setting'   => 'trove_font_base_size_unit',
			'weight_setting'      => 'trove_font_base_weight',
			'line_setting'        => 'trove_font_base_line_height',
			'line_unit_setting'   => 'trove_font_base_line_height_unit',
			'letter_setting'      => 'trove_font_base_letter_spacing',
			'letter_unit_setting' => 'trove_font_base_letter_spacing_unit',
			'family_var'          => '--trove-body-font-family',
			'size_var'            => '--trove-font-base-size',
			'weight_var'          => '--trove-font-base-weight',
			'line_var'            => '--trove-font-base-line-height',
			'letter_var'          => '--trove-font-base-letter-spacing',
			'default_family'      => 'Instrument Sans',
			'default_size'        => 16,
			'default_size_unit'   => 'px',
			'default_weight'      => 400,
			'default_line_height' => 1.5,
			'default_line_unit'   => 'unitless',
			'default_letter_spacing'      => 0,
			'default_letter_spacing_unit' => 'em',
		),
		'menu'       => array(
			'selector'            => '.site-header__nav .primary-menu',
			'family_setting'      => 'trove_menu_google_font_family',
			'size_setting'        => 'trove_menu_font_size',
			'size_unit_setting'   => 'trove_menu_font_size_unit',
			'weight_setting'      => 'trove_menu_font_weight',
			'line_setting'        => 'trove_menu_font_line_height',
			'line_unit_setting'   => 'trove_menu_font_line_height_unit',
			'letter_setting'      => 'trove_menu_font_letter_spacing',
			'letter_unit_setting' => 'trove_menu_font_letter_spacing_unit',
			'family_var'          => '--trove-menu-font-family',
			'size_var'            => '--trove-menu-font-size',
			'weight_var'          => '--trove-menu-font-weight',
			'line_var'            => '--trove-menu-font-line-height',
			'letter_var'          => '--trove-menu-font-letter-spacing',
			'default_family'      => 'Instrument Sans',
			'default_size'        => 16,
			'default_size_unit'   => 'px',
			'default_weight'      => 500,
			'default_line_height' => 1.5,
			'default_line_unit'   => 'unitless',
			'default_letter_spacing'      => -0.02,
			'default_letter_spacing_unit' => 'em',
		),
		'h1'         => array( 'selector' => 'h1', 'family_setting' => 'trove_h1_google_font_family', 'size_setting' => 'trove_h1_font_size', 'size_unit_setting' => 'trove_h1_font_size_unit', 'weight_setting' => 'trove_h1_font_weight', 'line_setting' => 'trove_h1_font_line_height', 'line_unit_setting' => 'trove_h1_font_line_height_unit', 'family_var' => '--trove-h1-font-family', 'size_var' => '--trove-h1-font-size', 'weight_var' => '--trove-h1-font-weight', 'line_var' => '--trove-h1-font-line-height', 'letter_var' => '--trove-h1-font-letter-spacing', 'default_size' => 4.0, 'default_size_unit' => 'rem', 'default_weight' => 600, 'default_line_height' => 1.1, 'default_line_unit' => 'unitless', 'default_letter_spacing' => -0.15, 'default_letter_spacing_unit' => 'rem', 'default_family' => 'Instrument Sans' ),
		'h2'         => array( 'selector' => 'h2', 'family_setting' => 'trove_h2_google_font_family', 'size_setting' => 'trove_h2_font_size', 'size_unit_setting' => 'trove_h2_font_size_unit', 'weight_setting' => 'trove_h2_font_weight', 'line_setting' => 'trove_h2_font_line_height', 'line_unit_setting' => 'trove_h2_font_line_height_unit', 'family_var' => '--trove-h2-font-family', 'size_var' => '--trove-h2-font-size', 'weight_var' => '--trove-h2-font-weight', 'line_var' => '--trove-h2-font-line-height', 'letter_var' => '--trove-h2-font-letter-spacing', 'default_size' => 3.0, 'default_size_unit' => 'rem', 'default_weight' => 600, 'default_line_height' => 1.2, 'default_line_unit' => 'unitless', 'default_letter_spacing' => -0.07, 'default_letter_spacing_unit' => 'rem', 'default_family' => 'Instrument Sans' ),
		'h3'         => array( 'selector' => 'h3', 'family_setting' => 'trove_h3_google_font_family', 'size_setting' => 'trove_h3_font_size', 'size_unit_setting' => 'trove_h3_font_size_unit', 'weight_setting' => 'trove_h3_font_weight', 'line_setting' => 'trove_h3_font_line_height', 'line_unit_setting' => 'trove_h3_font_line_height_unit', 'family_var' => '--trove-h3-font-family', 'size_var' => '--trove-h3-font-size', 'weight_var' => '--trove-h3-font-weight', 'line_var' => '--trove-h3-font-line-height', 'letter_var' => '--trove-h3-font-letter-spacing', 'default_size' => 2.4, 'default_size_unit' => 'rem', 'default_weight' => 600, 'default_line_height' => 1.2, 'default_line_unit' => 'unitless', 'default_letter_spacing' => -0.02, 'default_letter_spacing_unit' => 'rem', 'default_family' => 'Instrument Sans' ),
		'h4'         => array( 'selector' => 'h4', 'family_setting' => 'trove_h4_google_font_family', 'size_setting' => 'trove_h4_font_size', 'size_unit_setting' => 'trove_h4_font_size_unit', 'weight_setting' => 'trove_h4_font_weight', 'line_setting' => 'trove_h4_font_line_height', 'line_unit_setting' => 'trove_h4_font_line_height_unit', 'family_var' => '--trove-h4-font-family', 'size_var' => '--trove-h4-font-size', 'weight_var' => '--trove-h4-font-weight', 'line_var' => '--trove-h4-font-line-height', 'letter_var' => '--trove-h4-font-letter-spacing', 'default_size' => 2.2, 'default_size_unit' => 'rem', 'default_weight' => 600, 'default_line_height' => 1.2, 'default_line_unit' => 'unitless', 'default_letter_spacing' => -0.01, 'default_letter_spacing_unit' => 'rem', 'default_family' => 'Instrument Sans' ),
		'h5'         => array( 'selector' => 'h5', 'family_setting' => 'trove_h5_google_font_family', 'size_setting' => 'trove_h5_font_size', 'size_unit_setting' => 'trove_h5_font_size_unit', 'weight_setting' => 'trove_h5_font_weight', 'line_setting' => 'trove_h5_font_line_height', 'line_unit_setting' => 'trove_h5_font_line_height_unit', 'family_var' => '--trove-h5-font-family', 'size_var' => '--trove-h5-font-size', 'weight_var' => '--trove-h5-font-weight', 'line_var' => '--trove-h5-font-line-height', 'letter_var' => '--trove-h5-font-letter-spacing', 'default_size' => 2.0, 'default_size_unit' => 'rem', 'default_weight' => 600, 'default_line_height' => 1.2, 'default_line_unit' => 'unitless', 'default_letter_spacing' => -0.01, 'default_letter_spacing_unit' => 'rem', 'default_family' => 'Instrument Sans' ),
		'h6'         => array( 'selector' => 'h6', 'family_setting' => 'trove_h6_google_font_family', 'size_setting' => 'trove_h6_font_size', 'size_unit_setting' => 'trove_h6_font_size_unit', 'weight_setting' => 'trove_h6_font_weight', 'line_setting' => 'trove_h6_font_line_height', 'line_unit_setting' => 'trove_h6_font_line_height_unit', 'family_var' => '--trove-h6-font-family', 'size_var' => '--trove-h6-font-size', 'weight_var' => '--trove-h6-font-weight', 'line_var' => '--trove-h6-font-line-height', 'letter_var' => '--trove-h6-font-letter-spacing', 'default_size' => 1.8, 'default_size_unit' => 'rem', 'default_weight' => 600, 'default_line_height' => 1.2, 'default_line_unit' => 'unitless', 'default_letter_spacing' => -0.01, 'default_letter_spacing_unit' => 'rem', 'default_family' => 'Instrument Sans' ),
		'blockquote' => array( 'selector' => 'blockquote', 'family_setting' => 'trove_blockquote_google_font_family', 'size_setting' => 'trove_blockquote_font_size', 'size_unit_setting' => 'trove_blockquote_font_size_unit', 'weight_setting' => 'trove_blockquote_font_weight', 'line_setting' => 'trove_blockquote_font_line_height', 'line_unit_setting' => 'trove_blockquote_font_line_height_unit', 'family_var' => '--trove-blockquote-font-family', 'size_var' => '--trove-blockquote-font-size', 'weight_var' => '--trove-blockquote-font-weight', 'line_var' => '--trove-blockquote-font-line-height', 'letter_var' => '--trove-blockquote-font-letter-spacing', 'default_size' => 1.125, 'default_size_unit' => 'rem', 'default_weight' => 500, 'default_line_height' => 1.6, 'default_line_unit' => 'unitless', 'default_letter_spacing' => 0, 'default_letter_spacing_unit' => 'em', 'default_family' => 'Playfair Display' ),
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
			'letter_var'          => '--trove-accent-font-letter-spacing',
			'default_family'      => 'Playfair Display',
			'default_size'        => 1.125,
			'default_size_unit'   => 'rem',
			'default_weight'      => 500,
			'default_line_height' => 1.6,
			'default_line_unit'   => 'unitless',
			'default_letter_spacing'      => 0,
			'default_letter_spacing_unit' => 'em',
		),
	);
}

/**
 * Customizer family setting -> corresponding weight setting ids.
 *
 * @return array<string, string>
 */
function font_weight_setting_pairs() {
	$pairs = array();
	foreach ( typography_font_scope_meta() as $scope => $meta ) {
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
function font_weight_family_map() {
	$out = array();
	foreach ( font_weight_setting_pairs() as $family_setting => $weight_setting ) {
		$out[ $weight_setting ] = $family_setting;
	}
	return $out;
}

/**
 * @param mixed $value Raw numeric size.
 * @return float
 */
function sanitize_font_base_size( $value ) {
	$f = is_numeric( $value ) ? (float) $value : 16;
	return max( 0.25, min( 500, round( $f, 4 ) ) );
}

/**
 * @param mixed $value Raw unit slug.
 * @return string px|rem|em
 */
function sanitize_font_base_size_unit( $value ) {
	$allowed = array( 'px', 'rem', 'em' );
	$v       = is_string( $value ) ? strtolower( trim( $value ) ) : 'px';
	return in_array( $v, $allowed, true ) ? $v : 'px';
}

/**
 * @param mixed $value Raw weight.
 * @return int
 */
function sanitize_font_base_weight( $value ) {
	$n       = absint( $value );
	$n       = max( 1, min( 1000, $n ) );
	$allowed = get_font_weight_values_for_family( effective_google_font_family_during_save() );
	return nearest_font_weight( $n, $allowed );
}

/**
 * @param mixed $value Raw weight.
 * @return int
 */
function sanitize_font_weight_loose( $value ) {
	$n = absint( $value );
	return max( 1, min( 1000, $n ) );
}

/**
 * @param mixed $value Raw line-height number.
 * @return float
 */
function sanitize_font_base_line_height( $value ) {
	$f = is_numeric( $value ) ? (float) $value : 1.5;
	return max( 0.25, min( 500, round( $f, 4 ) ) );
}

/**
 * @param mixed $value Raw unit slug.
 * @return string
 */
function sanitize_font_base_line_height_unit( $value ) {
	$allowed = array( 'unitless', 'px', 'rem', 'em', '%' );
	$v       = is_string( $value ) ? trim( $value ) : 'unitless';
	return in_array( $v, $allowed, true ) ? $v : 'unitless';
}

/**
 * @param mixed $value Raw letter-spacing number.
 * @return float
 */
function sanitize_font_letter_spacing( $value ) {
	$f = is_numeric( $value ) ? (float) $value : 0;
	return max( -10, min( 10, round( $f, 4 ) ) );
}

/**
 * @param mixed $value Raw unit slug.
 * @return string px|rem|em
 */
function sanitize_font_letter_spacing_unit( $value ) {
	$allowed = array( 'px', 'rem', 'em' );
	$v       = is_string( $value ) ? strtolower( trim( $value ) ) : 'em';
	return in_array( $v, $allowed, true ) ? $v : 'em';
}

/**
 * @param float $f Numeric value.
 * @return string Trimmed for CSS (no trailing zeros).
 */
function format_css_float_string( $f ) {
	$s = rtrim( rtrim( sprintf( '%.4F', (float) $f ), '0' ), '.' );
	return '' !== $s ? $s : '0';
}

/**
 * @return string e.g. 16px, 1rem
 */
function font_base_size_css_value() {
	$unit = sanitize_font_base_size_unit( get_theme_mod( 'trove_font_base_size_unit', (string) customizer_default( 'trove_font_base_size_unit', 'px' ) ) );
	$raw  = get_theme_mod( 'trove_font_base_size', customizer_default( 'trove_font_base_size', 16 ) );
	$v    = is_numeric( $raw ) ? (float) $raw : 16;
	$v    = sanitize_font_base_size( $v );

	if ( 'px' === $unit ) {
		$n = (int) round( $v );
		$n = max( 10, min( 32, $n ) );
		return $n . 'px';
	}
	if ( 'rem' === $unit || 'em' === $unit ) {
		$f = max( 0.5, min( 2.5, $v ) );
		return format_css_float_string( $f ) . $unit;
	}
	return '16px';
}

/**
 * @return string e.g. 400
 */
function font_base_weight_css_value() {
	$v       = get_theme_mod( 'trove_font_base_weight', customizer_default( 'trove_font_base_weight', 400 ) );
	$n       = absint( $v );
	$allowed = get_font_weight_values_for_family( get_font_family_setting() );
	return (string) nearest_font_weight( $n, $allowed );
}

/**
 * @return string e.g. 1.5, 24px, 150%
 */

function font_base_line_height_css_value() {
	$unit = sanitize_font_base_line_height_unit( get_theme_mod( 'trove_font_base_line_height_unit', (string) customizer_default( 'trove_font_base_line_height_unit', 'unitless' ) ) );
	$raw  = get_theme_mod( 'trove_font_base_line_height', customizer_default( 'trove_font_base_line_height', 1.5 ) );
	$v    = is_numeric( $raw ) ? (float) $raw : 1.5;
	$v    = sanitize_font_base_line_height( $v );

	if ( 'unitless' === $unit ) {
		$f = max( 1, min( 3, $v ) );
		return format_css_float_string( $f );
	}
	if ( 'px' === $unit ) {
		$n = max( 12, min( 80, (int) round( $v ) ) );
		return $n . 'px';
	}
	if ( 'rem' === $unit || 'em' === $unit ) {
		$f = max( 0.5, min( 4, $v ) );
		return format_css_float_string( $f ) . $unit;
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
function number_unit_css_value( $number_setting, $unit_setting, $default, $default_unit ) {
	$unit = get_theme_mod( $unit_setting, (string) customizer_default( $unit_setting, $default_unit ) );
	$raw  = get_theme_mod( $number_setting, customizer_default( $number_setting, $default ) );
	return number_unit_css_value_from_raw( $raw, $unit, $default, $default_unit );
}

/**
 * Convert raw number + unit into a sanitized CSS value.
 *
 * @param mixed  $raw Raw number.
 * @param mixed  $unit_raw Raw unit.
 * @param float  $default Default number.
 * @param string $default_unit Default unit.
 * @return string
 */
function number_unit_css_value_from_raw( $raw, $unit_raw, $default, $default_unit ) {
	if ( 'unitless' === $default_unit || '%' === $default_unit ) {
		$unit = sanitize_font_base_line_height_unit( (string) $unit_raw );
		$v    = is_numeric( $raw ) ? (float) $raw : (float) $default;
		$v    = sanitize_font_base_line_height( $v );
		if ( 'unitless' === $unit ) {
			$f = max( 1, min( 3, $v ) );
			return format_css_float_string( $f );
		}
		if ( 'px' === $unit ) {
			$n = max( 12, min( 80, (int) round( $v ) ) );
			return $n . 'px';
		}
		if ( 'rem' === $unit || 'em' === $unit ) {
			$f = max( 0.5, min( 4, $v ) );
			return format_css_float_string( $f ) . $unit;
		}
		if ( '%' === $unit ) {
			$n = max( 80, min( 250, (int) round( $v ) ) );
			return $n . '%';
		}
		return format_css_float_string( (float) $default );
	}

	$unit = sanitize_font_base_size_unit( (string) $unit_raw );
	$v    = is_numeric( $raw ) ? (float) $raw : (float) $default;
	$v    = sanitize_font_base_size( $v );
	if ( 'px' === $unit ) {
		$n = (int) round( $v );
		$n = max( 1, min( 500, $n ) );
		return $n . 'px';
	}
	$f = max( 0.25, min( 32, $v ) );
	return format_css_float_string( $f ) . $unit;
}

/**
 * Convert raw letter-spacing + unit into a sanitized CSS value.
 *
 * @param mixed  $raw Raw number.
 * @param mixed  $unit_raw Raw unit.
 * @param float  $default Default number.
 * @param string $default_unit Default unit.
 * @return string
 */
function letter_spacing_css_value_from_raw( $raw, $unit_raw, $default, $default_unit ) {
	$unit = sanitize_font_letter_spacing_unit( (string) $unit_raw );
	$v    = is_numeric( $raw ) ? (float) $raw : (float) $default;
	$v    = sanitize_font_letter_spacing( $v );
	return format_css_float_string( $v ) . $unit;
}

/**
 * Min-height CSS for announcement bar from a raw number + unit (matches Customizer input range).
 *
 * @param mixed  $raw  Raw number.
 * @param string $unit `px` or `rem`.
 * @return string
 */
function announcement_height_raw_to_css( $raw, $unit ) {
	$unit = sanitize_font_base_size_unit( (string) $unit );
	$v    = is_numeric( $raw ) ? (float) $raw : 48.0;
	$v    = sanitize_font_base_size( $v );
	if ( 'px' === $unit ) {
		$n = (int) round( $v );
		$n = max( 24, min( 120, $n ) );
		return $n . 'px';
	}
	$f = max( 0.25, min( 32, $v ) );
	return format_css_float_string( $f ) . $unit;
}

/**
 * Hide `.trove-announcement-bar` per breakpoint when that device’s “show” toggle is off.
 *
 * @param array<string, mixed> $enabled_by_device Keys pc, tablet, phone (booleans).
 * @param array{tablet:int,phone:int} $bp Breakpoints (max-width).
 * @return string CSS fragment.
 */
function announcement_bar_responsive_visibility_css( array $enabled_by_device, array $bp ) {
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
function font_weight_css_value_for_settings( $weight_setting, $family_setting, $default_weight ) {
	$n      = absint( get_theme_mod( $weight_setting, customizer_default( $weight_setting, $default_weight ) ) );
	$family = sanitize_google_font_family( get_theme_mod( $family_setting, (string) customizer_default( $family_setting, 'system' ) ) );
	return (string) nearest_font_weight( $n, get_font_weight_values_for_family( $family ) );
}

/**
 * Sanitizes a CSS color token for :root custom properties.
 * Supports hex and rgb/rgba for alpha-enabled pickers.
 *
 * @param mixed $value Raw candidate value.
 * @return string Sanitized color or empty string.
 */
function sanitize_css_color( $value ) {
	$raw = is_string( $value ) ? trim( $value ) : trim( (string) $value );
	if ( '' === $raw ) {
		return '';
	}

	if ( 'transparent' === strtolower( $raw ) ) {
		return 'transparent';
	}

	/* #rrggbbaa (hex + alpha). Alpha 00 → transparent; else rgba(). */
	if ( preg_match( '/^#([0-9a-f]{8})$/i', $raw, $m ) ) {
		$h = strtolower( $m[1] );
		$r  = hexdec( substr( $h, 0, 2 ) );
		$g  = hexdec( substr( $h, 2, 2 ) );
		$b  = hexdec( substr( $h, 4, 2 ) );
		$ai = hexdec( substr( $h, 6, 2 ) );
		if ( $ai <= 0 ) {
			return 'transparent';
		}
		$a = min( 1, max( 0, $ai / 255 ) );
		$alpha = rtrim( rtrim( sprintf( '%.3F', $a ), '0' ), '.' );
		if ( '' === $alpha ) {
			$alpha = '0';
		}
		return sprintf( 'rgba(%d,%d,%d,%s)', $r, $g, $b, $alpha );
	}

	/* #rgba CSS4 shorthand (each digit doubled). Last digit is the alpha nibble (0 → fully transparent). */
	if ( preg_match( '/^#([0-9a-f]{4})$/i', $raw, $m ) ) {
		$d = strtolower( $m[1] );
		$r = hexdec( $d[0] . $d[0] );
		$g = hexdec( $d[1] . $d[1] );
		$b = hexdec( $d[2] . $d[2] );
		$ai = hexdec( $d[3] . $d[3] );
		if ( $ai <= 0 ) {
			return 'transparent';
		}
		$a = min( 1, max( 0, $ai / 255 ) );
		$alpha = rtrim( rtrim( sprintf( '%.3F', $a ), '0' ), '.' );
		if ( '' === $alpha ) {
			$alpha = '0';
		}
		return sprintf( 'rgba(%d,%d,%d,%s)', $r, $g, $b, $alpha );
	}

	$hex = sanitize_hex_color( $raw );
	if ( $hex ) {
		return $hex;
	}

	if ( preg_match( '/^rgba\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*([0-9.]+)\s*\)\s*$/i', $raw, $m ) ) {
		$r = min( 255, max( 0, (int) $m[1] ) );
		$g = min( 255, max( 0, (int) $m[2] ) );
		$b = min( 255, max( 0, (int) $m[3] ) );
		$a = min( 1, max( 0, (float) $m[4] ) );
		if ( $a <= 0.0 ) {
			return 'transparent';
		}
		$alpha = rtrim( rtrim( sprintf( '%.3F', $a ), '0' ), '.' );
		if ( '' === $alpha ) {
			$alpha = '0';
		}
		return sprintf( 'rgba(%d,%d,%d,%s)', $r, $g, $b, $alpha );
	}

	if ( preg_match( '/^rgb\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*\)\s*$/i', $raw, $m ) ) {
		$r = min( 255, max( 0, (int) $m[1] ) );
		$g = min( 255, max( 0, (int) $m[2] ) );
		$b = min( 255, max( 0, (int) $m[3] ) );
		return sprintf( 'rgb(%d,%d,%d)', $r, $g, $b );
	}

	return '';
}

/**
 * Whether a CSS color should show the transparency checkerboard in the Customizer.
 *
 * @param mixed $value Raw color.
 * @return bool
 */
function color_is_transparent_swatch( $value ) {
	$raw = is_string( $value ) ? trim( $value ) : trim( (string) $value );
	if ( '' === $raw ) {
		return true;
	}
	if ( 'transparent' === strtolower( $raw ) ) {
		return true;
	}
	/* hsla(…, 0) before sanitize (sanitize_css_color may not accept hsla). */
	if ( preg_match( '/^hsla\(/i', $raw ) && preg_match( '/,\s*([0-9.]+)\s*\)\s*$/', $raw, $hm ) ) {
		if ( (float) $hm[1] <= 0.0 ) {
			return true;
		}
	}
	$san = sanitize_css_color( $value );
	if ( '' === $san ) {
		return true;
	}
	if ( 'transparent' === strtolower( $san ) ) {
		return true;
	}
	return false;
}

/**
 * Stable comparison key for two CSS colors (hex / rgb / rgba) so preset swatches can dedupe.
 *
 * @param mixed $value Raw color.
 * @return string Non-empty key, or empty string if invalid.
 */
function normalize_color_for_scheme_dedupe( $value ) {
	$san = sanitize_css_color( $value );
	if ( '' === $san ) {
		return '';
	}
	if ( '#' === substr( $san, 0, 1 ) ) {
		$hex = strtolower( ltrim( $san, '#' ) );
		if ( 3 === strlen( $hex ) ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
			return strtolower( $san );
		}
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );
		return sprintf( '%d|%d|%d|1.00000', $r, $g, $b );
	}
	if ( preg_match( '/^rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})(?:\s*,\s*(0|0?\.\d+|1(?:\.0+)?)\s*)?\)\s*$/i', $san, $m ) ) {
		$r = min( 255, max( 0, (int) $m[1] ) );
		$g = min( 255, max( 0, (int) $m[2] ) );
		$b = min( 255, max( 0, (int) $m[3] ) );
		$a = isset( $m[4] ) && '' !== $m[4] ? min( 1, max( 0, (float) $m[4] ) ) : 1.0;
		return sprintf( '%d|%d|%d|%s', $r, $g, $b, number_format( $a, 5, '.', '' ) );
	}

	return strtolower( $san );
}

/**
 * @param string $key     Theme mod key.
 * @param string $default Valid fallback color.
 * @return string
 */
function color_theme_mod( $key, $default ) {
	$raw = get_theme_mod( $key );
	if ( null === $raw || false === $raw || '' === $raw ) {
		return $default;
	}
	$san = sanitize_css_color( is_string( $raw ) ? $raw : (string) $raw );
	return '' !== $san ? $san : $default;
}

/**
 * Clamps content width to the same bounds as the Customizer number control.
 *
 * @param int $width Raw width in px.
 * @return int
 */
function clamp_content_width( $width ) {
	$w = absint( $width );
	if ( $w < 600 || $w > 1400 ) {
		return 800;
	}
	return $w;
}

/**
 * Clamps “wide” layout width to the same bounds as the Customizer number control.
 *
 * @param int $width Raw width in px.
 * @return int
 */
function clamp_content_wide_width( $width ) {
	$w = absint( $width );
	if ( $w < 800 || $w > 2400 ) {
		return 1200;
	}
	return $w;
}

/**
 * Effective content width (px), including migration from legacy responsive theme_mods.
 *
 * @return int
 */
function get_content_width_px() {
	$def = (int) customizer_default( 'trove_content_width', 800 );
	$raw = get_theme_mod( 'trove_content_width', null );
	if ( null !== $raw && false !== $raw && '' !== $raw && is_numeric( $raw ) ) {
		return clamp_content_width( $raw );
	}
	$legacy_pc = get_theme_mod( 'trove_content_width_pc', null );
	if ( null !== $legacy_pc && false !== $legacy_pc && '' !== $legacy_pc && is_numeric( $legacy_pc ) ) {
		return clamp_content_width( $legacy_pc );
	}
	return clamp_content_width( $def );
}

/**
 * Effective wide layout width (px) for --trove-content-wide-width.
 *
 * @return int
 */
function get_content_wide_width_px() {
	$def = (int) customizer_default( 'trove_content_wide_width', 1200 );
	$raw = get_theme_mod( 'trove_content_wide_width', null );
	if ( null !== $raw && false !== $raw && '' !== $raw && is_numeric( $raw ) ) {
		return clamp_content_wide_width( $raw );
	}
	return clamp_content_wide_width( $def );
}

/**
 * Clamps content vertical spacing (px) for --trove-content-vertical-spacing.
 *
 * @param int $px Raw value.
 * @return int
 */
function clamp_content_vertical_spacing_px( $px ) {
	$w = absint( $px );
	return min( 200, max( 0, $w ) );
}

/**
 * Effective vertical spacing (px) for the main content area.
 *
 * @return int
 */
function get_content_vertical_spacing_px() {
	$def = (int) customizer_default( 'trove_content_vertical_spacing', 48 );
	$raw = get_theme_mod( 'trove_content_vertical_spacing', null );
	if ( null !== $raw && false !== $raw && '' !== $raw && is_numeric( $raw ) ) {
		return clamp_content_vertical_spacing_px( $raw );
	}
	return clamp_content_vertical_spacing_px( $def );
}

/**
 * Clamps content horizontal spacing (px) for --trove-content-horizontal-spacing.
 *
 * @param int $px Raw value.
 * @return int
 */
function clamp_content_horizontal_spacing_px( $px ) {
	$w = absint( $px );
	return min( 200, max( 0, $w ) );
}

/**
 * Effective horizontal spacing (px) for the main content area.
 *
 * @return int
 */
function get_content_horizontal_spacing_px() {
	$def = (int) customizer_default( 'trove_content_horizontal_spacing', 20 );
	$raw = get_theme_mod( 'trove_content_horizontal_spacing', null );
	if ( null !== $raw && false !== $raw && '' !== $raw && is_numeric( $raw ) ) {
		return clamp_content_horizontal_spacing_px( $raw );
	}
	return clamp_content_horizontal_spacing_px( $def );
}

/**
 * Button colors → :root CSS variables (not part of global color scheme presets).
 *
 * @return array<string, array{var:string, default:string}>
 */
function button_color_theme_mod_definitions() {
	static $cached = null;
	if ( null !== $cached ) {
		return $cached;
	}
	$map = array(
		'trove_button_background'         => '--trove-button-bg',
		'trove_button_background_hover'   => '--trove-button-bg-hover',
		'trove_button_text_color'         => '--trove-button-text',
		'trove_button_text_color_hover'   => '--trove-button-text-hover',
		'trove_button_border_color'       => '--trove-button-border',
		'trove_button_border_color_hover' => '--trove-button-border-hover',
		'trove_button_secondary_background'       => '--trove-button-secondary-bg',
		'trove_button_secondary_background_hover' => '--trove-button-secondary-bg-hover',
		'trove_button_secondary_text_color'       => '--trove-button-secondary-text',
		'trove_button_secondary_text_color_hover' => '--trove-button-secondary-text-hover',
		'trove_button_secondary_border_color'     => '--trove-button-secondary-border',
		'trove_button_secondary_border_color_hover' => '--trove-button-secondary-border-hover',
	);
	$fallbacks = array(
		'trove_button_background'         => '#111111',
		'trove_button_background_hover'   => '#064FFF',
		'trove_button_text_color'         => '#ffffff',
		'trove_button_text_color_hover'   => '#ffffff',
		'trove_button_border_color'       => '#111111',
		'trove_button_border_color_hover' => '#333333',
		'trove_button_secondary_background'       => 'transparent',
		'trove_button_secondary_background_hover' => '#111111',
		'trove_button_secondary_text_color'       => '#111111',
		'trove_button_secondary_text_color_hover' => '#ffffff',
		'trove_button_secondary_border_color'     => '#111111',
		'trove_button_secondary_border_color_hover' => '#111111',
	);
	$out = array();
	foreach ( $map as $key => $var ) {
		$fb = isset( $fallbacks[ $key ] ) ? $fallbacks[ $key ] : '#111111';
		$out[ $key ] = array(
			'var'     => $var,
			'default' => (string) customizer_default( $key, $fb ),
		);
	}
	$cached = $out;
	return $cached;
}

/**
 * Text field colors → :root CSS variables.
 *
 * @return array<string, array{var:string, default:string}>
 */
function text_field_color_theme_mod_definitions() {
	static $cached = null;
	if ( null !== $cached ) {
		return $cached;
	}
	$map = array(
		'trove_input_field_border_color'    => '--trove-input-field-border-color',
		'trove_input_field_text_color'      => '--trove-input-field-text-color',
		'trove_textarea_field_border_color' => '--trove-textarea-field-border-color',
		'trove_textarea_field_text_color'   => '--trove-textarea-field-text-color',
	);
	$fallbacks = array(
		'trove_input_field_border_color'    => '#dddddd',
		'trove_input_field_text_color'      => '#111111',
		'trove_textarea_field_border_color' => '#eeeeee',
		'trove_textarea_field_text_color'   => '#111111',
	);
	$out = array();
	foreach ( $map as $key => $var ) {
		$fb = isset( $fallbacks[ $key ] ) ? $fallbacks[ $key ] : '#111111';
		$out[ $key ] = array(
			'var'     => $var,
			'default' => (string) customizer_default( $key, $fb ),
		);
	}
	$cached = $out;
	return $cached;
}

/**
 * Clamp/get integer text-field theme_mod in range.
 *
 * @param string $key Setting key.
 * @param int    $fallback Default.
 * @param int    $min Min.
 * @param int    $max Max.
 * @return int
 */
function get_int_theme_mod_in_range( $key, $fallback, $min, $max ) {
	$def = (int) customizer_default( (string) $key, (int) $fallback );
	$raw = get_theme_mod( (string) $key, null );
	$n   = $def;
	if ( null !== $raw && false !== $raw && '' !== $raw && is_numeric( $raw ) ) {
		$n = absint( $raw );
	}
	return min( (int) $max, max( (int) $min, (int) $n ) );
}

/**
 * @param int $px Raw border radius.
 * @return int
 */
function clamp_button_border_radius_px( $px ) {
	return min( 32, max( 0, absint( $px ) ) );
}

/**
 * @return int
 */
function get_button_border_radius_px() {
	$def = (int) customizer_default( 'trove_button_border_radius', 6 );
	$raw = get_theme_mod( 'trove_button_border_radius', null );
	if ( null !== $raw && false !== $raw && '' !== $raw && is_numeric( $raw ) ) {
		return clamp_button_border_radius_px( $raw );
	}
	return clamp_button_border_radius_px( $def );
}

/**
 * @param int $px Raw border width.
 * @return int
 */
function clamp_button_border_width_px( $px ) {
	return min( 8, max( 0, absint( $px ) ) );
}

/**
 * @return int
 */
function get_button_border_width_px() {
	$def = (int) customizer_default( 'trove_button_border_width', 1 );
	$raw = get_theme_mod( 'trove_button_border_width', null );
	if ( null !== $raw && false !== $raw && '' !== $raw && is_numeric( $raw ) ) {
		return clamp_button_border_width_px( $raw );
	}
	return clamp_button_border_width_px( $def );
}

/**
 * @param int $px Raw font size in px.
 * @return int
 */
function clamp_button_text_font_size_px( $px ) {
	return min( 24, max( 10, absint( $px ) ) );
}

/**
 * @return int
 */
function get_button_text_font_size_px() {
	$def = (int) customizer_default( 'trove_button_text_font_size', 16 );
	$raw = get_theme_mod( 'trove_button_text_font_size', null );
	if ( null !== $raw && false !== $raw && '' !== $raw && is_numeric( $raw ) ) {
		return clamp_button_text_font_size_px( $raw );
	}
	return clamp_button_text_font_size_px( $def );
}

/**
 * @return int
 */
function get_button_secondary_border_radius_px() {
	$def = (int) customizer_default( 'trove_button_secondary_border_radius', 6 );
	$raw = get_theme_mod( 'trove_button_secondary_border_radius', null );
	if ( null !== $raw && false !== $raw && '' !== $raw && is_numeric( $raw ) ) {
		return clamp_button_border_radius_px( $raw );
	}
	return clamp_button_border_radius_px( $def );
}

/**
 * @return int
 */
function get_button_secondary_border_width_px() {
	$def = (int) customizer_default( 'trove_button_secondary_border_width', 1 );
	$raw = get_theme_mod( 'trove_button_secondary_border_width', null );
	if ( null !== $raw && false !== $raw && '' !== $raw && is_numeric( $raw ) ) {
		return clamp_button_border_width_px( $raw );
	}
	return clamp_button_border_width_px( $def );
}

/**
 * @return int
 */
function get_button_secondary_text_font_size_px() {
	$def = (int) customizer_default( 'trove_button_secondary_text_font_size', 14 );
	$raw = get_theme_mod( 'trove_button_secondary_text_font_size', null );
	if ( null !== $raw && false !== $raw && '' !== $raw && is_numeric( $raw ) ) {
		return clamp_button_text_font_size_px( $raw );
	}
	return clamp_button_text_font_size_px( $def );
}

/**
 * Default button padding (px) for Customizer JSON spacing box.
 *
 * @return array{top:int,right:int,bottom:int,left:int}
 */
function default_button_padding_sides() {
	return array(
		'top'    => 5,
		'right'  => 20,
		'bottom' => 5,
		'left'   => 20,
	);
}

/**
 * Sanitize spacing box JSON (top/right/bottom/left px + optional linked flag).
 *
 * @param mixed                $value Raw value.
 * @param int                  $min   Min px.
 * @param int                  $max   Max px.
 * @param array<string, int>   $fallback Sides default.
 * @return string JSON string.
 */
function sanitize_spacing_box_json( $value, $min, $max, array $fallback ) {
	$min = max( 0, (int) $min );
	$max = max( $min, (int) $max );
	$data = null;
	if ( is_array( $value ) ) {
		$data = $value;
	} elseif ( is_string( $value ) && '' !== trim( $value ) ) {
		$data = json_decode( $value, true );
	}
	if ( ! is_array( $data ) ) {
		$data = array();
	}
	$out = array(
		'top'    => isset( $fallback['top'] ) ? (int) $fallback['top'] : 0,
		'right'  => isset( $fallback['right'] ) ? (int) $fallback['right'] : 0,
		'bottom' => isset( $fallback['bottom'] ) ? (int) $fallback['bottom'] : 0,
		'left'   => isset( $fallback['left'] ) ? (int) $fallback['left'] : 0,
		'linked' => ! empty( $data['linked'] ),
	);
	foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) {
		if ( ! array_key_exists( $side, $data ) ) {
			continue;
		}
		$out[ $side ] = min( $max, max( $min, absint( $data[ $side ] ) ) );
	}
	return (string) wp_json_encode( $out );
}

/**
 * Effective padding sides for one device theme_mod (JSON box).
 *
 * @param string $key Theme mod key (e.g. trove_button_padding_pc).
 * @return array{top:int,right:int,bottom:int,left:int}
 */
function get_button_padding_sides_for_mod( $key ) {
	$key = (string) $key;
	$fb  = default_button_padding_sides();
	$def = customizer_default( $key, '' );
	if ( '' === $def || null === $def ) {
		$def = sanitize_spacing_box_json( array_merge( $fb, array( 'linked' => false ) ), 0, 60, $fb );
	} elseif ( is_array( $def ) ) {
		$def = sanitize_spacing_box_json( $def, 0, 60, $fb );
	} else {
		$def = sanitize_spacing_box_json( (string) $def, 0, 60, $fb );
	}
	$raw = get_theme_mod( $key, null );
	if ( null === $raw || false === $raw || '' === $raw ) {
		$raw = $def;
	}
	$san = sanitize_spacing_box_json( is_array( $raw ) ? $raw : (string) $raw, 0, 60, $fb );
	$dec = json_decode( $san, true );
	if ( ! is_array( $dec ) ) {
		return $fb;
	}
	return array(
		'top'    => isset( $dec['top'] ) ? (int) $dec['top'] : $fb['top'],
		'right'  => isset( $dec['right'] ) ? (int) $dec['right'] : $fb['right'],
		'bottom' => isset( $dec['bottom'] ) ? (int) $dec['bottom'] : $fb['bottom'],
		'left'   => isset( $dec['left'] ) ? (int) $dec['left'] : $fb['left'],
	);
}

/**
 * CSS custom properties for button padding on a selector (one breakpoint).
 *
 * @param array{top:int,right:int,bottom:int,left:int} $sides Sides in px.
 * @return string
 */
function button_padding_vars_declaration( array $sides ) {
	return '--trove-button-padding-top:' . (int) $sides['top'] . 'px;'
		. '--trove-button-padding-right:' . (int) $sides['right'] . 'px;'
		. '--trove-button-padding-bottom:' . (int) $sides['bottom'] . 'px;'
		. '--trove-button-padding-left:' . (int) $sides['left'] . 'px';
}

/**
 * CSS custom properties for secondary button padding.
 *
 * @param array{top:int,right:int,bottom:int,left:int} $sides Sides in px.
 * @return string
 */
function button_secondary_padding_vars_declaration( array $sides ) {
	return '--trove-button-secondary-padding-top:' . (int) $sides['top'] . 'px;'
		. '--trove-button-secondary-padding-right:' . (int) $sides['right'] . 'px;'
		. '--trove-button-secondary-padding-bottom:' . (int) $sides['bottom'] . 'px;'
		. '--trove-button-secondary-padding-left:' . (int) $sides['left'] . 'px';
}

/**
 * Inline CSS for accent, content width, and header border.
 */
function build_theme_variable_css( $selector = ':root', $include_responsive_content = true ) {
	$font_family = get_font_family_setting();

	$w_content   = get_content_width_px();
	$w_wide      = get_content_wide_width_px();
	$v_spacing   = get_content_vertical_spacing_px();
	$h_spacing   = get_content_horizontal_spacing_px();

	$bp = get_responsive_breakpoints_max();

	$root_parts = array();
	foreach ( color_theme_mod_definitions() as $mod_key => $row ) {
		$root_parts[] = $row['var'] . ':' . esc_attr( color_theme_mod( $mod_key, $row['default'] ) );
	}

	foreach ( button_color_theme_mod_definitions() as $mod_key => $row ) {
		$root_parts[] = $row['var'] . ':' . esc_attr( color_theme_mod( $mod_key, $row['default'] ) );
	}
	foreach ( text_field_color_theme_mod_definitions() as $mod_key => $row ) {
		$root_parts[] = $row['var'] . ':' . esc_attr( color_theme_mod( $mod_key, $row['default'] ) );
	}

	$footer_color_mods = array(
		'trove_footer_border_color' => '--trove-footer-border',
	);
	foreach ( $footer_color_mods as $mod_key => $css_var ) {
		$c = sanitize_css_color( (string) get_theme_mod( $mod_key, '' ) );
		if ( '' !== $c ) {
			$root_parts[] = $css_var . ':' . esc_attr( $c );
		}
	}

	$header_color_mods = array(
		'trove_header_border_color' => '--trove-header-border',
	);
	foreach ( $header_color_mods as $mod_key => $css_var ) {
		$c = sanitize_css_color( (string) get_theme_mod( $mod_key, '' ) );
		if ( '' !== $c ) {
			$root_parts[] = $css_var . ':' . esc_attr( $c );
		}
	}

	$ann_h_vals = get_responsive_mod_values(
		'trove_announcement_height',
		array_replace(
			array( 'pc' => 48, 'tablet' => 48, 'phone' => 48 ),
			(array) customizer_default( 'trove_announcement_height', array() )
		)
	);
	$ann_h_unit = (string) get_theme_mod( 'trove_announcement_height_unit', (string) customizer_default( 'trove_announcement_height_unit', 'px' ) );
	$fz_vals    = get_responsive_mod_values(
		'trove_announcement_text_size',
		array_replace(
			array( 'pc' => 'medium', 'tablet' => 'medium', 'phone' => 'medium' ),
			(array) customizer_default( 'trove_announcement_text_size', array() )
		)
	);
	$root_parts[] = '--trove-announcement-min-height:' . esc_attr( announcement_height_raw_to_css( $ann_h_vals['pc'], $ann_h_unit ) );
	$root_parts[] = '--trove-announcement-font-size:' . esc_attr( \Trove\announcement_text_size_slug_to_css( $fz_vals['pc'] ) );

	$header_logo_w = \Trove\get_header_logo_width();
	$root_parts[]  = '--trove-header-logo-max-width:' . (int) $header_logo_w . 'px';

	$root_parts[] = '--trove-content-wide-width:' . (int) $w_wide . 'px';
	$root_parts[] = '--trove-wide:var(--trove-content-wide-width)';
	$root_parts[] = '--trove-content-width:' . (int) $w_content . 'px';
	$root_parts[] = '--trove-content-vertical-spacing:' . (int) $v_spacing . 'px';
	$root_parts[] = '--trove-content-horizontal-spacing:' . (int) $h_spacing . 'px';

	$btn_r = get_button_border_radius_px();
	$btn_w = get_button_border_width_px();
	$btn_fs = get_button_text_font_size_px();
	$btn2_r = get_button_secondary_border_radius_px();
	$btn2_w = get_button_secondary_border_width_px();
	$btn2_fs = get_button_secondary_text_font_size_px();
	$root_parts[] = '--trove-button-border-radius:' . (int) $btn_r . 'px';
	$root_parts[] = '--trove-button-border-width:' . (int) $btn_w . 'px';
	$root_parts[] = '--trove-button-font-size:' . (int) $btn_fs . 'px';
	$root_parts[] = '--trove-button-secondary-border-radius:' . (int) $btn2_r . 'px';
	$root_parts[] = '--trove-button-secondary-border-width:' . (int) $btn2_w . 'px';
	$root_parts[] = '--trove-button-secondary-font-size:' . (int) $btn2_fs . 'px';
	$root_parts[] = '--trove-input-field-height:' . get_int_theme_mod_in_range( 'trove_input_field_height', 42, 28, 80 ) . 'px';
	$root_parts[] = '--trove-input-field-text-size:' . get_int_theme_mod_in_range( 'trove_input_field_text_size', 16, 10, 24 ) . 'px';
	$root_parts[] = '--trove-input-field-border-width:' . get_int_theme_mod_in_range( 'trove_input_field_border_width', 1, 0, 8 ) . 'px';
	$root_parts[] = '--trove-input-field-border-radius:' . get_int_theme_mod_in_range( 'trove_input_field_border_radius', 3, 0, 32 ) . 'px';
	$root_parts[] = '--trove-textarea-field-height:' . get_int_theme_mod_in_range( 'trove_textarea_field_height', 120, 60, 400 ) . 'px';
	$root_parts[] = '--trove-textarea-field-text-size:' . get_int_theme_mod_in_range( 'trove_textarea_field_text_size', 16, 10, 24 ) . 'px';
	$root_parts[] = '--trove-textarea-field-border-width:' . get_int_theme_mod_in_range( 'trove_textarea_field_border_width', 1, 0, 8 ) . 'px';
	$root_parts[] = '--trove-textarea-field-border-radius:' . get_int_theme_mod_in_range( 'trove_textarea_field_border_radius', 3, 0, 32 ) . 'px';

	$pad_pc = get_button_padding_sides_for_mod( 'trove_button_padding_pc' );
	$root_parts[] = button_padding_vars_declaration( $pad_pc );
	$pad2_pc = get_button_padding_sides_for_mod( 'trove_button_secondary_padding_pc' );
	$root_parts[] = button_secondary_padding_vars_declaration( $pad2_pc );

	$boxed_radius = absint( get_theme_mod( 'trove_blog_boxed_border_radius', (int) customizer_default( 'trove_blog_boxed_border_radius', 10 ) ) );
	$boxed_radius = max( 0, min( 20, $boxed_radius ) );
	$root_parts[] = '--trove-blog-boxed-radius:' . (int) $boxed_radius . 'px';
	$root_parts[] = '--trove-font-body-scale:1';
	$typography_tablet_parts = array();
	$typography_phone_parts  = array();
	foreach ( typography_font_scope_meta() as $scope => $meta ) {
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
		$letter_setting = isset( $meta['letter_setting'] ) ? (string) $meta['letter_setting'] : $prefix . '_font_letter_spacing';
		$letter_unit    = isset( $meta['letter_unit_setting'] ) ? (string) $meta['letter_unit_setting'] : $prefix . '_font_letter_spacing_unit';
		$letter_var     = isset( $meta['letter_var'] ) ? (string) $meta['letter_var'] : '--trove-' . $scope . '-font-letter-spacing';
		$family_default = isset( $meta['default_family'] ) ? (string) $meta['default_family'] : 'system';
		$size_default   = isset( $meta['default_size'] ) ? (float) $meta['default_size'] : 1;
		$size_unit_def  = isset( $meta['default_size_unit'] ) ? (string) $meta['default_size_unit'] : 'rem';
		$line_default   = isset( $meta['default_line_height'] ) ? (float) $meta['default_line_height'] : 1.5;
		$line_unit_def  = isset( $meta['default_line_unit'] ) ? (string) $meta['default_line_unit'] : 'unitless';
		$letter_default = isset( $meta['default_letter_spacing'] ) ? (float) $meta['default_letter_spacing'] : 0;
		$letter_unit_def = isset( $meta['default_letter_spacing_unit'] ) ? (string) $meta['default_letter_spacing_unit'] : 'em';
		$weight_default = isset( $meta['default_weight'] ) ? (int) $meta['default_weight'] : 400;

		$family = sanitize_google_font_family( get_theme_mod( $family_setting, (string) customizer_default( $family_setting, $family_default ) ) );
		$size_defaults = array(
			'pc'     => customizer_default( $size_setting . '_pc', customizer_default( $size_setting, $size_default ) ),
			'tablet' => customizer_default( $size_setting . '_tablet', customizer_default( $size_setting, $size_default ) ),
			'phone'  => customizer_default( $size_setting . '_phone', customizer_default( $size_setting, $size_default ) ),
		);
		$line_defaults = array(
			'pc'     => customizer_default( $line_setting . '_pc', customizer_default( $line_setting, $line_default ) ),
			'tablet' => customizer_default( $line_setting . '_tablet', customizer_default( $line_setting, $line_default ) ),
			'phone'  => customizer_default( $line_setting . '_phone', customizer_default( $line_setting, $line_default ) ),
		);
		$size_values = get_responsive_mod_values( $size_setting, $size_defaults );
		$line_values = get_responsive_mod_values( $line_setting, $line_defaults );
		$letter_defaults = array(
			'pc'     => customizer_default( $letter_setting . '_pc', customizer_default( $letter_setting, $letter_default ) ),
			'tablet' => customizer_default( $letter_setting . '_tablet', customizer_default( $letter_setting, $letter_default ) ),
			'phone'  => customizer_default( $letter_setting . '_phone', customizer_default( $letter_setting, $letter_default ) ),
		);
		$letter_values = get_responsive_mod_values( $letter_setting, $letter_defaults );
		$size_unit_value = (string) get_theme_mod( $size_unit, (string) customizer_default( $size_unit, $size_unit_def ) );
		$line_unit_value = (string) get_theme_mod( $line_unit, (string) customizer_default( $line_unit, $line_unit_def ) );
		$letter_unit_value = (string) get_theme_mod( $letter_unit, (string) customizer_default( $letter_unit, $letter_unit_def ) );
		$size_css_pc = number_unit_css_value_from_raw( $size_values['pc'], $size_unit_value, $size_default, $size_unit_def );
		$line_css_pc = number_unit_css_value_from_raw( $line_values['pc'], $line_unit_value, $line_default, $line_unit_def );
		$letter_css_pc = letter_spacing_css_value_from_raw( $letter_values['pc'], $letter_unit_value, $letter_default, $letter_unit_def );
		$typography_tablet_parts[] = $size_var . ':' . esc_attr( number_unit_css_value_from_raw( $size_values['tablet'], $size_unit_value, $size_default, $size_unit_def ) );
		$typography_tablet_parts[] = $line_var . ':' . esc_attr( number_unit_css_value_from_raw( $line_values['tablet'], $line_unit_value, $line_default, $line_unit_def ) );
		$typography_tablet_parts[] = $letter_var . ':' . esc_attr( letter_spacing_css_value_from_raw( $letter_values['tablet'], $letter_unit_value, $letter_default, $letter_unit_def ) );
		$typography_phone_parts[] = $size_var . ':' . esc_attr( number_unit_css_value_from_raw( $size_values['phone'], $size_unit_value, $size_default, $size_unit_def ) );
		$typography_phone_parts[] = $line_var . ':' . esc_attr( number_unit_css_value_from_raw( $line_values['phone'], $line_unit_value, $line_default, $line_unit_def ) );
		$typography_phone_parts[] = $letter_var . ':' . esc_attr( letter_spacing_css_value_from_raw( $letter_values['phone'], $letter_unit_value, $letter_default, $letter_unit_def ) );
		$root_parts[] = $family_var . ':' . get_body_font_family_css_value( $family );
		$root_parts[] = $size_var . ':' . esc_attr( $size_css_pc );
		$root_parts[] = $weight_var . ':' . esc_attr( font_weight_css_value_for_settings( $weight_setting, $family_setting, (int) customizer_default( $weight_setting, $weight_default ) ) );
		$root_parts[] = $line_var . ':' . esc_attr( $line_css_pc );
		$root_parts[] = $letter_var . ':' . esc_attr( $letter_css_pc );
	}

	$css  = $selector . '{' . implode( ';', $root_parts ) . '}';

	$pad_tablet = get_button_padding_sides_for_mod( 'trove_button_padding_tablet' );
	$pad_phone  = get_button_padding_sides_for_mod( 'trove_button_padding_phone' );
	$pad2_tablet = get_button_padding_sides_for_mod( 'trove_button_secondary_padding_tablet' );
	$pad2_phone  = get_button_padding_sides_for_mod( 'trove_button_secondary_padding_phone' );
	$css       .= '@media (max-width:' . (int) $bp['tablet'] . 'px){' . $selector . '{' . button_padding_vars_declaration( $pad_tablet ) . '}}';
	$css       .= '@media (max-width:' . (int) $bp['phone'] . 'px){' . $selector . '{' . button_padding_vars_declaration( $pad_phone ) . '}}';
	$css       .= '@media (max-width:' . (int) $bp['tablet'] . 'px){' . $selector . '{' . button_secondary_padding_vars_declaration( $pad2_tablet ) . '}}';
	$css       .= '@media (max-width:' . (int) $bp['phone'] . 'px){' . $selector . '{' . button_secondary_padding_vars_declaration( $pad2_phone ) . '}}';
	if ( ! empty( $typography_tablet_parts ) ) {
		$css .= '@media (max-width:' . (int) $bp['tablet'] . 'px){' . $selector . '{' . implode( ';', $typography_tablet_parts ) . '}}';
	}
	if ( ! empty( $typography_phone_parts ) ) {
		$css .= '@media (max-width:' . (int) $bp['phone'] . 'px){' . $selector . '{' . implode( ';', $typography_phone_parts ) . '}}';
	}

	$css .= '.site-footer__extra-responsive .trove-fe--tablet,.site-footer__extra-responsive .trove-fe--phone{display:none;}';
	$css .= '.site-footer__extra-responsive .trove-fe--pc{display:block;}';
	if ( $include_responsive_content ) {
		$css .= '@media (max-width:' . (int) $bp['tablet'] . 'px){' . $selector . '{--trove-announcement-min-height:' . esc_attr( announcement_height_raw_to_css( $ann_h_vals['tablet'], $ann_h_unit ) ) . ';--trove-announcement-font-size:' . esc_attr( \Trove\announcement_text_size_slug_to_css( $fz_vals['tablet'] ) ) . ';}}';
	}
	$css .= '@media (max-width:' . (int) $bp['tablet'] . 'px){.site-footer__extra-responsive .trove-fe--pc{display:none;}.site-footer__extra-responsive .trove-fe--tablet{display:block;}}';
	if ( $include_responsive_content ) {
		$css .= '@media (max-width:' . (int) $bp['phone'] . 'px){' . $selector . '{--trove-announcement-min-height:' . esc_attr( announcement_height_raw_to_css( $ann_h_vals['phone'], $ann_h_unit ) ) . ';--trove-announcement-font-size:' . esc_attr( \Trove\announcement_text_size_slug_to_css( $fz_vals['phone'] ) ) . ';}.site-footer__extra-responsive .trove-fe--tablet{display:none;}.site-footer__extra-responsive .trove-fe--phone{display:block;}}';
	} else {
		$css .= '@media (max-width:' . (int) $bp['phone'] . 'px){.site-footer__extra-responsive .trove-fe--tablet{display:none;}.site-footer__extra-responsive .trove-fe--phone{display:block;}}';
	}

	$header_min = absint( get_theme_mod( 'trove_header_min_height', (int) customizer_default( 'trove_header_min_height', 0 ) ) );
	if ( $header_min > 0 && $header_min <= 400 ) {
		$css .= '.site-header{min-height:' . $header_min . 'px;}';
	}

	$ann_enabled = get_responsive_mod_values(
		'trove_announcement_enabled',
		array_replace(
			array( 'pc' => true, 'tablet' => true, 'phone' => true ),
			(array) customizer_default( 'trove_announcement_enabled', array() )
		)
	);
	$css .= announcement_bar_responsive_visibility_css( $ann_enabled, $bp );

	return $css;
}

/**
 * Inline CSS for accent, content width, and header border.
 */
function output_customizer_css() {
	if ( ! wp_style_is( 'trove-style', 'enqueued' ) ) {
		return;
	}

	wp_add_inline_style( 'trove-style', build_theme_variable_css( ':root', true ) );
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
	wp_add_inline_style( $handle, build_theme_variable_css( ':root, .editor-styles-wrapper', false ) );
}
add_action( 'enqueue_block_assets', __NAMESPACE__ . '\\enqueue_editor_customizer_css', 20 );


/**
 * Preview map: family setting id -> CSS variable name.
 *
 * @return array<string, string>
 */
function preview_font_family_var_map() {
	$map = array();
	foreach ( typography_font_scope_meta() as $scope => $meta ) {
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
function preview_number_unit_pairs() {
	$pairs = array();
	foreach ( typography_font_scope_meta() as $scope => $meta ) {
		$prefix        = 'trove_' . $scope;
		$size_setting  = isset( $meta['size_setting'] ) ? (string) $meta['size_setting'] : $prefix . '_font_size';
		$size_unit     = isset( $meta['size_unit_setting'] ) ? (string) $meta['size_unit_setting'] : $prefix . '_font_size_unit';
		$line_setting  = isset( $meta['line_setting'] ) ? (string) $meta['line_setting'] : $prefix . '_font_line_height';
		$line_unit     = isset( $meta['line_unit_setting'] ) ? (string) $meta['line_unit_setting'] : $prefix . '_font_line_height_unit';
		$size_var      = isset( $meta['size_var'] ) ? (string) $meta['size_var'] : '--trove-' . $scope . '-font-size';
		$line_var      = isset( $meta['line_var'] ) ? (string) $meta['line_var'] : '--trove-' . $scope . '-font-line-height';
		$letter_setting = isset( $meta['letter_setting'] ) ? (string) $meta['letter_setting'] : $prefix . '_font_letter_spacing';
		$letter_unit    = isset( $meta['letter_unit_setting'] ) ? (string) $meta['letter_unit_setting'] : $prefix . '_font_letter_spacing_unit';
		$letter_var      = isset( $meta['letter_var'] ) ? (string) $meta['letter_var'] : '--trove-' . $scope . '-font-letter-spacing';

		$pairs[ $size_setting ] = array( 'unit' => $size_unit, 'var' => $size_var, 'mode' => 'size', 'responsive' => true );
		$pairs[ $line_setting ] = array( 'unit' => $line_unit, 'var' => $line_var, 'mode' => 'line_height', 'responsive' => true );
		$pairs[ $letter_setting ] = array( 'unit' => $letter_unit, 'var' => $letter_var, 'mode' => 'letter_spacing', 'responsive' => true );
	}

	return $pairs;
}

/**
 * Live preview: Global Colors update :root variables without refreshing the iframe.
 */
function enqueue_customizer_preview_scripts() {
	$ver = defined( 'TROVE_THEME_VERSION' ) ? TROVE_THEME_VERSION : '1.0.0';
	$uri = \TROVE_URI;

	$preview_colors = array();
	$preview_color_exclude = array(
		'trove_header_cart_badge_color',
		'trove_header_cart_count_color',
	);
	foreach ( color_theme_mod_definitions() as $mod => $row ) {
		if ( in_array( $mod, $preview_color_exclude, true ) ) {
			continue;
		}
		$preview_colors[ $mod ] = array(
			'var'     => $row['var'],
			'default' => $row['default'],
		);
	}
	foreach ( button_color_theme_mod_definitions() as $mod => $row ) {
		$preview_colors[ $mod ] = array(
			'var'     => $row['var'],
			'default' => $row['default'],
		);
	}
	foreach ( text_field_color_theme_mod_definitions() as $mod => $row ) {
		$preview_colors[ $mod ] = array(
			'var'     => $row['var'],
			'default' => $row['default'],
		);
	}

	$preview_typography = array();
	foreach ( typography_theme_mod_definitions() as $mod => $row ) {
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
			'fontFamilies'    => preview_font_family_var_map(),
			'weightFamilies'  => font_weight_family_map(),
			'numberUnitPairs' => preview_number_unit_pairs(),
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
	if ( get_theme_mod( 'trove_header_bottom_border', (bool) customizer_default( 'trove_header_bottom_border', false ) ) ) {
		$classes[] = 'trove-header-has-border';
	}
	$allowed = array(
		'horizontal-left-logo',
		'horizontal-centered-logo',
		'horizontal-right-logo',
		'vertical-centered-logo',
		'vertical-left-logo',
	);
	$layout = get_theme_mod( 'trove_header_layout', (string) customizer_default( 'trove_header_layout', 'horizontal-left-logo' ) );
	$layout = is_string( $layout ) ? sanitize_key( $layout ) : '';
	if ( '' !== $layout && in_array( $layout, $allowed, true ) ) {
		$classes[] = 'trove-header__' . $layout;
	}

	$sticky_allowed = array( 'none', 'on_scroll_up', 'always' );
	$sticky         = get_theme_mod( 'trove_header_sticky', (string) customizer_default( 'trove_header_sticky', 'none' ) );
	$sticky         = is_string( $sticky ) ? sanitize_key( $sticky ) : 'none';
	if ( ! in_array( $sticky, $sticky_allowed, true ) ) {
		$sticky = 'none';
	}
	$classes[] = 'trove-header-sticky-' . $sticky;

	$align_allowed = array( 'left', 'center', 'right' );
	$align         = get_theme_mod( 'trove_header_menu_alignment', (string) customizer_default( 'trove_header_menu_alignment', 'right' ) );
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
	$align         = get_theme_mod( 'trove_header_menu_alignment', (string) customizer_default( 'trove_header_menu_alignment', 'right' ) );
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
