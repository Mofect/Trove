<?php
/**
 * Responsive Customizer helpers (pc / tablet / phone theme_mod suffixes).
 *
 * @package Trove
 */

namespace Trove\Customizer;

defined( 'ABSPATH' ) || exit;

/**
 * Device keys used for suffixed theme_mods: {base}_pc, {base}_tablet, {base}_phone.
 */
const RESPONSIVE_DEVICES = array( 'pc', 'tablet', 'phone' );

/**
 * Max-width breakpoints (px) for desktop-first CSS overrides.
 * At or below `tablet`, tablet values apply; at or below `phone`, phone values apply.
 *
 * @return array{tablet:int,phone:int}
 */
function get_responsive_breakpoints_max() {
	$bp = array(
		'tablet' => 1024,
		'phone'  => 781,
	);
	/**
	 * Filter max-width breakpoints used when applying responsive theme_mods to CSS.
	 *
	 * @param array{tablet:int,phone:int} $bp Breakpoints in pixels.
	 */
	return apply_filters( 'trove_responsive_breakpoints_max', $bp );
}

/**
 * Returns theme_mod values for each device. If the legacy unsuffixed key exists and `_pc`
 * was never saved, the legacy value is used for `pc` (migration from pre-responsive options).
 *
 * @param string               $base_setting_id Base id (e.g. trove_content_width), without _pc/_tablet/_phone.
 * @param array<string, mixed> $defaults_by_device Keys: pc, tablet, phone.
 * @return array<string, mixed> Keys: pc, tablet, phone.
 */
function get_responsive_mod_values( $base_setting_id, array $defaults_by_device ) {
	$out = array();
	foreach ( RESPONSIVE_DEVICES as $dev ) {
		$def = array_key_exists( $dev, $defaults_by_device ) ? $defaults_by_device[ $dev ] : '';
		$key = $base_setting_id . '_' . $dev;
		$val = get_theme_mod( $key, null );
		if ( null === $val && 'pc' === $dev ) {
			$legacy = get_theme_mod( $base_setting_id, null );
			if ( null !== $legacy ) {
				$out[ $dev ] = $legacy;
				continue;
			}
		}
		if ( null === $val ) {
			$val = $def;
		}
		$out[ $dev ] = $val;
	}
	return $out;
}
