<?php
/**
 * Per-post content padding overrides (Page settings → vertical / horizontal spacing).
 *
 * @package Trove
 */

namespace Trove\Metabox;

defined( 'ABSPATH' ) || exit;

/**
 * @param mixed $raw Raw POST value.
 * @param array $field Field definition (unused).
 * @return string Empty string clears override; otherwise clamped px as string. Same as Customizer default clears override.
 */
function sanitize_page_settings_content_vertical_spacing( $raw, $field ) {
	unset( $field );
	if ( null === $raw || '' === (string) $raw ) {
		return '';
	}
	$clamped = (int) \Trove\Customizer\clamp_content_vertical_spacing_px( absint( $raw ) );
	$global  = (int) \Trove\Customizer\get_content_vertical_spacing_px();
	if ( $clamped === $global ) {
		return '';
	}
	return (string) $clamped;
}

/**
 * @param mixed $raw Raw POST value.
 * @param array $field Field definition (unused).
 * @return string Empty string clears override; otherwise clamped px as string. Same as Customizer default clears override.
 */
function sanitize_page_settings_content_horizontal_spacing( $raw, $field ) {
	unset( $field );
	if ( null === $raw || '' === (string) $raw ) {
		return '';
	}
	$clamped = (int) \Trove\Customizer\clamp_content_horizontal_spacing_px( absint( $raw ) );
	$global  = (int) \Trove\Customizer\get_content_horizontal_spacing_px();
	if ( $clamped === $global ) {
		return '';
	}
	return (string) $clamped;
}

/**
 * Build CSS that overrides content spacing variables for one post.
 *
 * @param int    $post_id  Post ID.
 * @param string $selector Root selector (e.g. `.site-main` or `.editor-styles-wrapper`).
 * @return string Non-empty CSS rule or empty string.
 */
function build_page_content_spacing_override_css( $post_id, $selector ) {
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

	$decls = array();

	$v_raw = get_post_field_value( $post_id, 'page_settings', 'content_vertical_spacing', null );
	if ( null !== $v_raw && '' !== (string) $v_raw && is_numeric( $v_raw ) ) {
		$decls[] = '--trove-content-vertical-spacing:' . (int) \Trove\Customizer\clamp_content_vertical_spacing_px( (int) $v_raw ) . 'px';
	}

	$h_raw = get_post_field_value( $post_id, 'page_settings', 'content_horizontal_spacing', null );
	if ( null !== $h_raw && '' !== (string) $h_raw && is_numeric( $h_raw ) ) {
		$decls[] = '--trove-content-horizontal-spacing:' . (int) \Trove\Customizer\clamp_content_horizontal_spacing_px( (int) $h_raw ) . 'px';
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
function enqueue_front_page_content_spacing_css() {
	if ( ! is_singular() ) {
		return;
	}
	if ( ! wp_style_is( 'trove-style', 'enqueued' ) ) {
		return;
	}

	$post_id = (int) get_queried_object_id();
	$css      = build_page_content_spacing_override_css( $post_id, '.site-main' );
	if ( '' === $css ) {
		return;
	}

	wp_add_inline_style( 'trove-style', $css );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_front_page_content_spacing_css', 25 );

/**
 * @return void
 */
function enqueue_editor_page_content_spacing_css() {
	$post_id = 0;
	if ( isset( $_GET['post'] ) ) {
		$post_id = absint( wp_unslash( $_GET['post'] ) );
	}
	if ( $post_id < 1 ) {
		global $post;
		if ( isset( $post->ID ) ) {
			$post_id = (int) $post->ID;
		}
	}
	if ( $post_id < 1 ) {
		return;
	}

	$css = build_page_content_spacing_override_css( $post_id, '.editor-styles-wrapper' );
	if ( '' === $css ) {
		return;
	}

	$handle = 'trove-editor-vars';
	if ( ! wp_style_is( $handle, 'registered' ) ) {
		wp_register_style( $handle, false, array(), defined( 'TROVE_THEME_VERSION' ) ? TROVE_THEME_VERSION : '1.0.0' );
	}
	wp_enqueue_style( $handle );
	wp_add_inline_style( $handle, $css );
}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\enqueue_editor_page_content_spacing_css', 25 );
