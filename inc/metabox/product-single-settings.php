<?php
/**
 * Per-product single product settings (metabox-backed).
 *
 * @package Trove
 */

namespace Trove\Metabox;

defined( 'ABSPATH' ) || exit;

/** Metabox registry id. */
const PRODUCT_SETTINGS_METABOX_ID = 'product_settings';

/** Select value: use theme default (no per-product override). */
const PRODUCT_SETTINGS_THEME_DEFAULT = '';

/**
 * Default values (formerly Customizer → WooCommerce → Single product).
 *
 * @return array<string, mixed>
 */
function product_settings_defaults() {
	static $defaults = null;
	if ( null !== $defaults ) {
		return $defaults;
	}

	$fallbacks = array(
		'gallery_type'            => 'stacked',
		'gallery_stacked_columns' => '4',
		'gallery_slider_position' => 'bottom',
		'tabs_layout'             => 'horizontal',
		'enable_related_products' => true,
		'related_products_count'  => '4',
	);

	$defaults = array();
	foreach ( product_settings_theme_mod_map() as $field_id => $mod_key ) {
		$fallback = $fallbacks[ $field_id ] ?? '';
		if ( 'enable_related_products' === $field_id ) {
			$defaults[ $field_id ] = wp_validate_boolean(
				get_theme_mod(
					$mod_key,
					(bool) \Trove\Customizer\customizer_default( $mod_key, $fallback )
				)
			);
			continue;
		}
		$defaults[ $field_id ] = get_theme_mod(
			$mod_key,
			\Trove\Customizer\customizer_default( $mod_key, $fallback )
		);
	}

	/**
	 * @param array<string, mixed> $defaults Theme default values.
	 */
	$defaults = apply_filters( 'trove_product_settings_defaults', $defaults );

	return $defaults;
}

/**
 * Whether a stored select value means “use theme default”.
 *
 * @param mixed $value Raw stored value.
 * @return bool
 */
function is_product_setting_theme_default( $value ) {
	return '' === $value || null === $value || false === $value;
}

/**
 * Resolve a stored product setting to its effective value.
 *
 * @param string $field_id Field id.
 * @param mixed  $stored   Value from post meta (may be theme-default sentinel).
 * @return mixed
 */
function resolve_product_setting_value( $field_id, $stored ) {
	$field_id = sanitize_key( (string) $field_id );
	if ( is_product_setting_theme_default( $stored ) ) {
		$defaults = product_settings_defaults();
		return array_key_exists( $field_id, $defaults ) ? $defaults[ $field_id ] : $stored;
	}
	return $stored;
}

/**
 * Post ID for the product whose single-product settings should apply.
 *
 * @return int
 */
function get_product_settings_post_id() {
	static $resolved = null;
	if ( null !== $resolved ) {
		return (int) $resolved;
	}

	$resolved = 0;

	if ( is_admin() ) {
		global $post;
		if ( $post instanceof \WP_Post && 'product' === $post->post_type ) {
			$resolved = (int) $post->ID;
			return (int) $resolved;
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['post'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$candidate = absint( $_GET['post'] );
			if ( $candidate > 0 && 'product' === get_post_type( $candidate ) ) {
				$resolved = $candidate;
				return (int) $resolved;
			}
		}
	}

	if ( function_exists( 'is_product' ) && is_product() ) {
		$resolved = (int) get_queried_object_id();
	}

	return (int) $resolved;
}

/**
 * Read a product setting for the current (or given) product.
 *
 * @param string   $field_id Field id (metabox field).
 * @param int      $post_id  Product post ID (0 = resolve automatically).
 * @param mixed    $fallback Optional fallback when meta and defaults are empty.
 * @return mixed
 */
function get_product_setting( $field_id, $post_id = 0, $fallback = null ) {
	$field_id = sanitize_key( (string) $field_id );
	$defaults = product_settings_defaults();
	$def      = array_key_exists( $field_id, $defaults ) ? $defaults[ $field_id ] : $fallback;

	$post_id = (int) $post_id;
	if ( $post_id < 1 ) {
		$post_id = get_product_settings_post_id();
	}

	if ( $post_id < 1 ) {
		return null !== $fallback ? $fallback : $def;
	}

	$key = Fields::meta_key( PRODUCT_SETTINGS_METABOX_ID, $field_id );
	if ( ! metadata_exists( 'post', $post_id, $key ) ) {
		return null !== $fallback ? $fallback : $def;
	}

	$value = get_post_meta( $post_id, $key, true );
	if ( is_product_setting_theme_default( $value ) ) {
		return null !== $fallback ? $fallback : $def;
	}

	return $value;
}

/**
 * Read a boolean product setting (distinguishes “unset” from explicitly off).
 *
 * @param string $field_id Field id.
 * @param int    $post_id  Product post ID (0 = resolve automatically).
 * @return bool
 */
function get_product_setting_bool( $field_id, $post_id = 0 ) {
	$field_id = sanitize_key( (string) $field_id );
	$defaults = product_settings_defaults();
	$def      = wp_validate_boolean( $defaults[ $field_id ] ?? false );

	$post_id = (int) $post_id;
	if ( $post_id < 1 ) {
		$post_id = get_product_settings_post_id();
	}
	if ( $post_id < 1 ) {
		return $def;
	}

	$key = Fields::meta_key( PRODUCT_SETTINGS_METABOX_ID, $field_id );
	if ( ! metadata_exists( 'post', $post_id, $key ) ) {
		return $def;
	}

	return wp_validate_boolean( get_post_meta( $post_id, $key, true ) );
}

/**
 * Legacy theme_mod keys → metabox field ids.
 *
 * @return array<string, string>
 */
function product_settings_theme_mod_map() {
	return array(
		'gallery_type'            => 'trove_woo_single_product_gallery_type',
		'gallery_stacked_columns' => 'trove_woo_single_product_gallery_stacked_columns',
		'gallery_slider_position' => 'trove_woo_single_product_gallery_slider_position',
		'tabs_layout'             => 'trove_woo_single_product_tabs_layout',
		'enable_related_products' => 'trove_woo_single_product_enable_related_products',
		'related_products_count'  => 'trove_woo_single_product_related_products_count',
	);
}

/**
 * Copy former Customizer single-product theme mods into product meta once (empty meta only).
 *
 * @param int $post_id Product post ID.
 * @return void
 */
function migrate_product_settings_from_theme_mods( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id < 1 || 'product' !== get_post_type( $post_id ) ) {
		return;
	}

	$mods = get_theme_mods();
	if ( ! is_array( $mods ) ) {
		return;
	}

	foreach ( product_settings_theme_mod_map() as $field_id => $mod_key ) {
		$meta_key = Fields::meta_key( PRODUCT_SETTINGS_METABOX_ID, $field_id );
		$existing = get_post_meta( $post_id, $meta_key, true );
		if ( '' !== $existing && false !== $existing ) {
			continue;
		}
		if ( ! array_key_exists( $mod_key, $mods ) ) {
			continue;
		}
		$value = $mods[ $mod_key ];
		if ( is_bool( $value ) ) {
			$value = $value ? '1' : '0';
		}
		update_post_meta( $post_id, $meta_key, $value );
	}
}
