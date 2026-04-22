<?php
/**
 * Google Fonts metadata, CSS URLs, and theme_mod helpers.
 *
 * @package Trove
 */

namespace Trove\Customizer;

defined( 'ABSPATH' ) || exit;

/**
 * Path to slim metadata JSON (family, fonts, axes per family).
 *
 * @return string
 */
function get_google_font_metadata_path() {
	return __DIR__ . '/data/google-fonts-metadata.json';
}

/**
 * Family name => metadata row (cached per request).
 *
 * @return array<string, array<string, mixed>>
 */
function get_google_font_metadata_map() {
	static $map = null;
	if ( null !== $map ) {
		return $map;
	}
	$path = get_google_font_metadata_path();
	if ( ! is_readable( $path ) ) {
		$map = array();
		return $map;
	}
	$raw = file_get_contents( $path );
	$data = json_decode( $raw, true );
	if ( ! is_array( $data ) || empty( $data['familyMetadataList'] ) ) {
		$map = array();
		return $map;
	}
	$map = array();
	foreach ( $data['familyMetadataList'] as $row ) {
		if ( ! empty( $row['family'] ) && is_string( $row['family'] ) ) {
			$map[ $row['family'] ] = $row;
		}
	}
	return $map;
}

/**
 * Sorted list of all Google Font family names (for Select2).
 *
 * @return list<string>
 */
function get_google_font_family_names() {
	$names = array_keys( get_google_font_metadata_map() );
	sort( $names, SORT_STRING | SORT_FLAG_CASE );
	return $names;
}

/**
 * Builds a css2.googleapis.com URL from metadata.
 *
 * @param array<string, mixed> $entry Metadata row.
 * @return string
 */
function build_google_font_css_url_from_metadata( array $entry ) {
	$family = isset( $entry['family'] ) ? $entry['family'] : '';
	if ( '' === $family ) {
		return '';
	}
	$name = str_replace( ' ', '+', $family );
	$fonts = isset( $entry['fonts'] ) && is_array( $entry['fonts'] ) ? $entry['fonts'] : array();
	$axes  = isset( $entry['axes'] ) && is_array( $entry['axes'] ) ? $entry['axes'] : array();

	$wght_axis = null;
	foreach ( $axes as $ax ) {
		if ( is_array( $ax ) && isset( $ax['tag'] ) && 'wght' === $ax['tag'] ) {
			$wght_axis = $ax;
			break;
		}
	}

	$has_italic = false;
	$weights    = array();
	foreach ( array_keys( $fonts ) as $k ) {
		if ( ! is_string( $k ) ) {
			continue;
		}
		if ( preg_match( '/^(\d+)i$/', $k ) ) {
			$has_italic = true;
		}
		if ( preg_match( '/^(\d+)i?$/', $k, $m ) ) {
			$weights[] = (int) $m[1];
		}
	}
	$weights = array_values( array_unique( $weights ) );
	sort( $weights );

	if ( $wght_axis && isset( $wght_axis['min'], $wght_axis['max'] ) ) {
		$min = (int) round( (float) $wght_axis['min'] );
		$max = (int) round( (float) $wght_axis['max'] );
		if ( $max > $min ) {
			if ( $has_italic ) {
				return 'https://fonts.googleapis.com/css2?family=' . $name . ':ital,wght@0,' . $min . '..' . $max . ';1,' . $min . '..' . $max . '&display=swap';
			}
			return 'https://fonts.googleapis.com/css2?family=' . $name . ':wght@' . $min . '..' . $max . '&display=swap';
		}
	}

	if ( ! empty( $weights ) ) {
		return 'https://fonts.googleapis.com/css2?family=' . $name . ':wght@' . implode( ';', $weights ) . '&display=swap';
	}

	return 'https://fonts.googleapis.com/css2?family=' . $name . '&display=swap';
}

/**
 * @param string $family Google family name or system.
 * @return string Empty for system / unknown.
 */
function get_google_font_stylesheet_url( $family ) {
	$family = sanitize_google_font_family( $family );
	if ( 'system' === $family ) {
		return '';
	}
	$map = get_google_font_metadata_map();
	if ( ! isset( $map[ $family ] ) ) {
		return '';
	}
	return build_google_font_css_url_from_metadata( $map[ $family ] );
}

/**
 * @param mixed $value Raw theme mod (system, family name, or legacy slug).
 * @return string system | exact Google family name.
 */
function sanitize_google_font_family( $value ) {
	if ( is_string( $value ) ) {
		$t = trim( $value );
		if ( '' === $t ) {
			return 'Instrument Sans';
		}
		if ( 0 === strcasecmp( $t, 'system' ) ) {
			return 'system';
		}
		$migrated = migrate_legacy_font_slug_to_family( $t );
		if ( null !== $migrated ) {
			$t = $migrated;
		}
		$map = get_google_font_metadata_map();
		if ( isset( $map[ $t ] ) ) {
			return $t;
		}
		foreach ( array_keys( $map ) as $name ) {
			if ( strcasecmp( $name, $t ) === 0 ) {
				return $name;
			}
		}
	}
	return 'Instrument Sans';
}

/**
 * Maps old underscore slugs to Google family names.
 *
 * @param string $t Raw value.
 * @return string|null New family name or null if not a legacy slug.
 */
function migrate_legacy_font_slug_to_family( $t ) {
	$legacy = array(
		'instrument_sans'   => 'Instrument Sans',
		'muli'              => 'Muli',
		'inter'             => 'Inter',
		'open_sans'         => 'Open Sans',
		'roboto'            => 'Roboto',
		'lato'              => 'Lato',
		'poppins'           => 'Poppins',
		'montserrat'        => 'Montserrat',
		'source_sans_3'     => 'Source Sans 3',
		'nunito'            => 'Nunito',
		'raleway'           => 'Raleway',
		'work_sans'         => 'Work Sans',
		'dm_sans'           => 'DM Sans',
		'manrope'           => 'Manrope',
		'outfit'            => 'Outfit',
		'plus_jakarta_sans' => 'Plus Jakarta Sans',
		'figtree'           => 'Figtree',
		'libre_baskerville' => 'Libre Baskerville',
		'merriweather'      => 'Merriweather',
		'lora'              => 'Lora',
		'playfair_display'  => 'Playfair Display',
	);
	$key = sanitize_key( $t );
	return isset( $legacy[ $key ] ) ? $legacy[ $key ] : null;
}

/**
 * Copies legacy trove_font_base_stack into trove_google_font_family once.
 *
 * @return void
 */
function migrate_legacy_font_theme_mod() {
	$mods = get_theme_mods();
	if ( ! is_array( $mods ) ) {
		return;
	}
	if ( array_key_exists( 'trove_google_font_family', $mods ) ) {
		return;
	}
	if ( ! array_key_exists( 'trove_font_base_stack', $mods ) ) {
		return;
	}
	$old = $mods['trove_font_base_stack'];
	set_theme_mod( 'trove_google_font_family', 'system' === $old ? 'system' : 'Muli' );
}

/**
 * One-time: old theme_mod stored underscore slugs (instrument_sans) → Google family names.
 *
 * @return void
 */
function migrate_stored_google_font_slug_to_family_name() {
	$mods = get_theme_mods();
	if ( ! is_array( $mods ) || ! isset( $mods['trove_google_font_family'] ) ) {
		return;
	}
	$v = $mods['trove_google_font_family'];
	if ( ! is_string( $v ) ) {
		return;
	}
	if ( strpos( $v, ' ' ) !== false ) {
		return;
	}
	if ( 0 === strcasecmp( $v, 'system' ) ) {
		return;
	}
	$m = migrate_legacy_font_slug_to_family( $v );
	if ( null !== $m ) {
		set_theme_mod( 'trove_google_font_family', $m );
	}
}

/**
 * @return string system | Google family name.
 */
function get_font_family_setting() {
	return sanitize_google_font_family(
		get_theme_mod(
			'trove_google_font_family',
			(string) \Trove\Customizer\trove_customizer_default( 'trove_google_font_family', 'Instrument Sans' )
		)
	);
}

/**
 * During a Customizer save, reads `trove_google_font_family` from the posted `customized` JSON
 * so other settings’ sanitizers (e.g. weight) see the new family when many mods save at once.
 *
 * @return string
 */
function trove_effective_google_font_family_during_save() {
	static $cached = null;
	if ( null !== $cached ) {
		return $cached;
	}
	if ( ! isset( $_POST['customized'] ) || ! is_string( $_POST['customized'] ) ) {
		$cached = get_font_family_setting();
		return $cached;
	}
	$decoded = json_decode( wp_unslash( $_POST['customized'] ), true );
	if ( ! is_array( $decoded ) || ! array_key_exists( 'trove_google_font_family', $decoded ) ) {
		$cached = get_font_family_setting();
		return $cached;
	}
	$cached = sanitize_google_font_family( $decoded['trove_google_font_family'] );
	return $cached;
}

/**
 * CSS value for body font-family (no rule wrapper).
 *
 * @param string $family system | Google family name.
 * @return string
 */
function get_body_font_family_css_value( $family ) {
	$family = sanitize_google_font_family( $family );
	if ( 'system' === $family ) {
		return 'system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif';
	}
	return '"' . esc_attr( $family ) . '",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif';
}

/**
 * Preview iframe: family => cssUrl + stack (full map for postMessage).
 *
 * @return array<string, array{cssUrl:string, stack:string}>
 */
function get_google_font_preview_data() {
	static $cache = null;
	if ( null !== $cache ) {
		return $cache;
	}
	$cache = array(
		'system' => array(
			'cssUrl' => '',
			'stack'  => get_body_font_family_css_value( 'system' ),
		),
	);
	foreach ( get_google_font_metadata_map() as $name => $entry ) {
		$cache[ $name ] = array(
			'cssUrl' => build_google_font_css_url_from_metadata( $entry ),
			'stack'  => get_body_font_family_css_value( $name ),
		);
	}
	return $cache;
}

/**
 * Parses Google metadata `fonts` keys (400, 400i, 500, …) into unique roman weights.
 *
 * @param array<string, mixed> $fonts fonts object from metadata.
 * @return list<int>
 */
function trove_extract_font_weights_from_fonts_object( $fonts ) {
	if ( ! is_array( $fonts ) ) {
		return array();
	}
	$weights = array();
	foreach ( array_keys( $fonts ) as $k ) {
		if ( ! is_string( $k ) ) {
			continue;
		}
		if ( preg_match( '/^(\d+)i?$/', $k, $m ) ) {
			$weights[] = (int) $m[1];
		}
	}
	$weights = array_values( array_unique( $weights ) );
	sort( $weights, SORT_NUMERIC );
	return $weights;
}

/**
 * Builds weight steps from a variable wght axis when static instances are missing.
 *
 * @param array<string, mixed> $axis Axis entry with min/max.
 * @return list<int>
 */
function trove_font_weights_from_wght_axis( array $axis ) {
	$min = isset( $axis['min'] ) ? (int) round( (float) $axis['min'] ) : 400;
	$max = isset( $axis['max'] ) ? (int) round( (float) $axis['max'] ) : 700;
	$min = max( 1, min( 1000, $min ) );
	$max = max( 1, min( 1000, $max ) );
	if ( $min > $max ) {
		return array( 400 );
	}
	$out = array();
	for ( $w = $min; $w <= $max; $w += 100 ) {
		$out[] = $w;
	}
	if ( ! empty( $out ) && end( $out ) !== $max ) {
		$out[] = $max;
	}
	$out = array_values( array_unique( $out ) );
	sort( $out, SORT_NUMERIC );
	if ( count( $out ) > 24 ) {
		$n      = count( $out );
		$stride = (int) ceil( $n / 20 );
		$thin   = array();
		for ( $i = 0; $i < $n; $i += $stride ) {
			$thin[] = $out[ $i ];
		}
		if ( $thin[ count( $thin ) - 1 ] !== $out[ $n - 1 ] ) {
			$thin[] = $out[ $n - 1 ];
		}
		$out = array_values( array_unique( $thin ) );
		sort( $out, SORT_NUMERIC );
	}
	return $out;
}

/**
 * Available numeric font-weights for the current family (system stack or Google font).
 *
 * @param string $family system | Google family name.
 * @return list<int>
 */
function get_font_weight_values_for_family( $family ) {
	$family = sanitize_google_font_family( $family );
	if ( 'system' === $family ) {
		return array( 300, 400, 500, 600, 700 );
	}
	$map = get_google_font_metadata_map();
	if ( ! isset( $map[ $family ] ) ) {
		foreach ( array_keys( $map ) as $name ) {
			if ( strcasecmp( $name, $family ) === 0 ) {
				$family = $name;
				break;
			}
		}
	}
	if ( ! isset( $map[ $family ] ) ) {
		return array( 300, 400, 500, 600, 700 );
	}
	$row     = $map[ $family ];
	$weights = trove_extract_font_weights_from_fonts_object( $row['fonts'] ?? array() );
	if ( ! empty( $weights ) ) {
		/**
		 * Filter discrete weights parsed from Google Fonts metadata for a family.
		 *
		 * @param list<int> $weights Weights.
		 * @param string    $family  Family name.
		 */
		return apply_filters( 'trove_font_weight_values_for_family', $weights, $family );
	}
	$axes = isset( $row['axes'] ) && is_array( $row['axes'] ) ? $row['axes'] : array();
	foreach ( $axes as $ax ) {
		if ( is_array( $ax ) && isset( $ax['tag'] ) && 'wght' === $ax['tag'] ) {
			$weights = trove_font_weights_from_wght_axis( $ax );
			return apply_filters( 'trove_font_weight_values_for_family', $weights, $family );
		}
	}
	$fallback = array( 400, 500, 600, 700 );
	return apply_filters( 'trove_font_weight_values_for_family', $fallback, $family );
}

/**
 * Picks the closest allowed weight to a candidate value.
 *
 * @param int        $candidate Raw weight.
 * @param list<int> $allowed    Allowed weights.
 * @return int
 */
function trove_nearest_font_weight( $candidate, array $allowed ) {
	$candidate = absint( $candidate );
	if ( empty( $allowed ) ) {
		return 400;
	}
	if ( in_array( $candidate, $allowed, true ) ) {
		return $candidate;
	}
	$best  = (int) $allowed[0];
	$bestd = PHP_INT_MAX;
	foreach ( $allowed as $w ) {
		$w = (int) $w;
		$d = abs( $w - $candidate );
		if ( $d < $bestd ) {
			$bestd = $d;
			$best  = $w;
		}
	}
	return $best;
}

/**
 * family => list of weights for Customizer JS (includes system).
 *
 * @return array<string, list<int>>
 */
function get_font_weight_map_for_customizer() {
	static $cache = null;
	if ( null !== $cache ) {
		return $cache;
	}
	$cache = array( 'system' => get_font_weight_values_for_family( 'system' ) );
	foreach ( get_google_font_metadata_map() as $name => $_row ) {
		$cache[ $name ] = get_font_weight_values_for_family( $name );
	}
	/**
	 * Filter the full weight map passed to the Customizer script.
	 *
	 * @param array<string, list<int>> $cache Family => weights.
	 */
	$cache = apply_filters( 'trove_font_weight_map_for_customizer', $cache );
	return $cache;
}

/**
 * Translated labels for common weights (optional display in select).
 *
 * @return array<string, string> weight string => label
 */
function trove_font_weight_choice_labels() {
	return array(
		'100' => __( '100 — Thin', 'trove' ),
		'200' => __( '200 — Extra Light', 'trove' ),
		'300' => __( '300 — Light', 'trove' ),
		'400' => __( '400 — Regular', 'trove' ),
		'500' => __( '500 — Medium', 'trove' ),
		'600' => __( '600 — Semibold', 'trove' ),
		'700' => __( '700 — Bold', 'trove' ),
		'800' => __( '800 — Extra Bold', 'trove' ),
		'900' => __( '900 — Black', 'trove' ),
	);
}
