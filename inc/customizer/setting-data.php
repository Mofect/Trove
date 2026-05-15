<?php
/**
 * Loads bundled Customizer defaults (setting-data.json).
 *
 * @package Trove
 */

namespace Trove\Customizer;

defined( 'ABSPATH' ) || exit;

/**
 * @return array<string, mixed> Keys: `defaults`, `color_scheme_presets`.
 */
function customizer_setting_data() {
	static $cache = null;
	if ( null !== $cache ) {
		return $cache;
	}

	$path = __DIR__ . '/data/setting-data.json';
	if ( ! is_readable( $path ) ) {
		$cache = array(
			'defaults'               => array(),
			'color_scheme_presets'   => array(),
		);
		return $cache;
	}

	$raw = file_get_contents( $path );
	if ( false === $raw ) {
		$cache = array(
			'defaults'               => array(),
			'color_scheme_presets'   => array(),
		);
		return $cache;
	}

	$data = json_decode( $raw, true );
	if ( ! is_array( $data ) ) {
		$cache = array(
			'defaults'               => array(),
			'color_scheme_presets'   => array(),
		);
		return $cache;
	}

	if ( ! isset( $data['defaults'] ) || ! is_array( $data['defaults'] ) ) {
		$data['defaults'] = array();
	}
	if ( ! isset( $data['color_scheme_presets'] ) || ! is_array( $data['color_scheme_presets'] ) ) {
		$data['color_scheme_presets'] = array();
	}

	$cache = $data;
	return $cache;
}

/**
 * Default value for a theme_mod / Customizer setting (from setting-data.json).
 *
 * @param string     $key      Setting id (matches JSON `defaults` key).
 * @param mixed|null $fallback Used when the key is absent from JSON.
 * @return mixed
 */
function customizer_default( $key, $fallback = null ) {
	$key = (string) $key;
	$data = customizer_setting_data();
	$defs = isset( $data['defaults'] ) && is_array( $data['defaults'] ) ? $data['defaults'] : array();
	return array_key_exists( $key, $defs ) ? $defs[ $key ] : $fallback;
}

/**
 * Preset color schemes (colors only); labels are applied in colors.php for i18n.
 *
 * @return list<array{colors: array<string, string>}>
 */
function customizer_color_scheme_presets_raw() {
	$data   = customizer_setting_data();
	$presets = isset( $data['color_scheme_presets'] ) && is_array( $data['color_scheme_presets'] ) ? $data['color_scheme_presets'] : array();
	$out     = array();
	foreach ( $presets as $row ) {
		if ( is_array( $row ) && ! empty( $row['colors'] ) && is_array( $row['colors'] ) ) {
			$out[] = array( 'colors' => $row['colors'] );
		}
	}
	return $out;
}
