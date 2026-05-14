<?php
/**
 * Per-post header color overrides (Page settings → header background, text, links, border).
 *
 * @package Trove
 */

namespace Trove\Metabox;

defined( 'ABSPATH' ) || exit;

/**
 * Effective header border color from the Customizer (empty if not set or invalid).
 *
 * @return string
 */
function trove_metabox_header_border_theme_mod_resolved() {
	$raw = get_theme_mod( 'trove_header_border_color', '' );
	if ( null === $raw || false === $raw || '' === $raw ) {
		return '';
	}
	return \Trove\Customizer\trove_sanitize_css_color( is_string( $raw ) ? $raw : (string) $raw );
}

/**
 * @param mixed $raw Raw POST value.
 * @param array $field Field definition (unused).
 * @return string Empty string clears override; otherwise sanitized CSS color.
 */
function sanitize_page_settings_header_text_color( $raw, $field ) {
	unset( $field );
	$san = \Trove\Customizer\trove_sanitize_css_color( $raw );
	if ( '' === $san ) {
		return '';
	}
	$defs   = \Trove\Customizer\trove_color_theme_mod_definitions();
	$def    = isset( $defs['trove_header_text_color'] ) ? (string) $defs['trove_header_text_color']['default'] : '#111111';
	$global = \Trove\Customizer\trove_color_theme_mod( 'trove_header_text_color', $def );
	if ( \Trove\Customizer\trove_normalize_color_for_scheme_dedupe( $san ) === \Trove\Customizer\trove_normalize_color_for_scheme_dedupe( $global ) ) {
		return '';
	}
	return $san;
}

/**
 * @param mixed $raw Raw POST value.
 * @param array $field Field definition (unused).
 * @return string Empty string clears override; otherwise sanitized CSS color.
 */
function sanitize_page_settings_header_link_color( $raw, $field ) {
	unset( $field );
	$san = \Trove\Customizer\trove_sanitize_css_color( $raw );
	if ( '' === $san ) {
		return '';
	}
	$defs   = \Trove\Customizer\trove_color_theme_mod_definitions();
	$def    = isset( $defs['trove_header_link_color'] ) ? (string) $defs['trove_header_link_color']['default'] : '#000000';
	$global = \Trove\Customizer\trove_color_theme_mod( 'trove_header_link_color', $def );
	if ( \Trove\Customizer\trove_normalize_color_for_scheme_dedupe( $san ) === \Trove\Customizer\trove_normalize_color_for_scheme_dedupe( $global ) ) {
		return '';
	}
	return $san;
}

/**
 * @param mixed $raw Raw POST value.
 * @param array $field Field definition (unused).
 * @return string Empty string clears override; otherwise sanitized CSS color.
 */
function sanitize_page_settings_header_link_hover_color( $raw, $field ) {
	unset( $field );
	$san = \Trove\Customizer\trove_sanitize_css_color( $raw );
	if ( '' === $san ) {
		return '';
	}
	$defs   = \Trove\Customizer\trove_color_theme_mod_definitions();
	$def    = isset( $defs['trove_header_link_hover_color'] ) ? (string) $defs['trove_header_link_hover_color']['default'] : '#666666';
	$global = \Trove\Customizer\trove_color_theme_mod( 'trove_header_link_hover_color', $def );
	if ( \Trove\Customizer\trove_normalize_color_for_scheme_dedupe( $san ) === \Trove\Customizer\trove_normalize_color_for_scheme_dedupe( $global ) ) {
		return '';
	}
	return $san;
}

/**
 * @param mixed $raw Raw POST value.
 * @param array $field Field definition (unused).
 * @return string Empty string clears override; otherwise sanitized CSS color.
 */
function sanitize_page_settings_header_border_color( $raw, $field ) {
	unset( $field );
	$san = \Trove\Customizer\trove_sanitize_css_color( $raw );
	if ( '' === $san ) {
		return '';
	}
	$global = trove_metabox_header_border_theme_mod_resolved();
	if ( \Trove\Customizer\trove_normalize_color_for_scheme_dedupe( $san ) === \Trove\Customizer\trove_normalize_color_for_scheme_dedupe( $global ) ) {
		return '';
	}
	return $san;
}

/**
 * @param mixed $raw Raw POST value.
 * @param array $field Field definition (unused).
 * @return string Empty string clears override; otherwise sanitized CSS color.
 */
function sanitize_page_settings_header_background_color( $raw, $field ) {
	unset( $field );
	$san = \Trove\Customizer\trove_sanitize_css_color( $raw );
	if ( '' === $san ) {
		return '';
	}
	$defs   = \Trove\Customizer\trove_color_theme_mod_definitions();
	$def    = isset( $defs['trove_header_background'] ) ? (string) $defs['trove_header_background']['default'] : '#ffffff';
	$global = \Trove\Customizer\trove_color_theme_mod( 'trove_header_background', $def );
	if ( \Trove\Customizer\trove_normalize_color_for_scheme_dedupe( $san ) === \Trove\Customizer\trove_normalize_color_for_scheme_dedupe( $global ) ) {
		return '';
	}
	return $san;
}

/**
 * Build CSS that overrides header color variables for one post.
 *
 * @param int    $post_id  Post ID.
 * @param string $selector Root selector(s), comma-separated (e.g. `header.site-header,.trove-announcement-bar`).
 * @return string Non-empty CSS rule or empty string.
 */
function build_page_header_color_override_css( $post_id, $selector ) {
	$post_id = (int) $post_id;
	if ( $post_id < 1 ) {
		return '';
	}

	$config = Registry::get( 'page_settings' );
	if ( null === $config || empty( $config['post_types'] ) ) {
		return '';
	}
	$post_type = get_post_type( $post_id );
	if ( ! in_array( $post_type, $config['post_types'], true ) ) {
		return '';
	}

	$transparent = wp_validate_boolean( get_post_field_value( $post_id, 'page_settings', 'transparent_header', false ) );

	$decls = array();

	if ( ! $transparent ) {
		$bg_raw = get_post_field_value( $post_id, 'page_settings', 'header_background_color', null );
		if ( null !== $bg_raw && '' !== (string) $bg_raw ) {
			$c = \Trove\Customizer\trove_sanitize_css_color( $bg_raw );
			if ( '' !== $c ) {
				$decls[] = '--trove-header-background:' . esc_attr( $c );
			}
		}
	}

	$text_raw = get_post_field_value( $post_id, 'page_settings', 'header_text_color', null );
	if ( null !== $text_raw && '' !== (string) $text_raw ) {
		$c = \Trove\Customizer\trove_sanitize_css_color( $text_raw );
		if ( '' !== $c ) {
			$decls[] = '--trove-header-text:' . esc_attr( $c );
		}
	}

	$link_raw = get_post_field_value( $post_id, 'page_settings', 'header_link_color', null );
	if ( null !== $link_raw && '' !== (string) $link_raw ) {
		$c = \Trove\Customizer\trove_sanitize_css_color( $link_raw );
		if ( '' !== $c ) {
			$decls[] = '--trove-header-link:' . esc_attr( $c );
		}
	}

	$link_hover_raw = get_post_field_value( $post_id, 'page_settings', 'header_link_hover_color', null );
	if ( null !== $link_hover_raw && '' !== (string) $link_hover_raw ) {
		$c = \Trove\Customizer\trove_sanitize_css_color( $link_hover_raw );
		if ( '' !== $c ) {
			$decls[] = '--trove-header-link-hover:' . esc_attr( $c );
		}
	}

	$border_raw = get_post_field_value( $post_id, 'page_settings', 'header_border_color', null );
	if ( null !== $border_raw && '' !== (string) $border_raw ) {
		$c = \Trove\Customizer\trove_sanitize_css_color( $border_raw );
		if ( '' !== $c ) {
			$decls[] = '--trove-header-border:' . esc_attr( $c );
		}
	}

	if ( empty( $decls ) ) {
		return '';
	}

	$selector = trim( (string) $selector );
	if ( '' === $selector ) {
		return '';
	}

	return $selector . '{' . implode( ';', $decls ) . '}';
}

/**
 * @return void
 */
function enqueue_front_page_header_color_css() {
	if ( ! is_singular() ) {
		return;
	}
	if ( ! wp_style_is( 'trove-style', 'enqueued' ) ) {
		return;
	}

	$post_id = (int) get_queried_object_id();
	$css      = build_page_header_color_override_css( $post_id, 'header.site-header,.trove-announcement-bar' );
	if ( '' === $css ) {
		return;
	}

	wp_add_inline_style( 'trove-style', $css );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_front_page_header_color_css', 25 );
