<?php
/**
 * Announcement bar (Customizer lines) above the main header.
 *
 * @package Trove
 */

namespace Trove;

defined( 'ABSPATH' ) || exit;

/**
 * Default JSON for `trove_announcement_lines` (two placeholder announcements).
 *
 * @return string
 */
function get_default_announcement_lines_json() {
	$lines = \Trove\Customizer\customizer_default( 'trove_announcement_lines_default', array( 'Announcement 1', 'Announcement 2' ) );
	if ( ! is_array( $lines ) ) {
		$lines = array( 'Announcement 1', 'Announcement 2' );
	}
	$out = array();
	foreach ( $lines as $line ) {
		$out[] = __( (string) $line, 'trove' );
	}
	return wp_json_encode( $out );
}

/**
 * Raw theme_mods array for the active stylesheet.
 *
 * @return array<string, mixed>
 */
function get_theme_mods_raw() {
	$mods = get_option( 'theme_mods_' . get_stylesheet(), array() );
	return is_array( $mods ) ? $mods : array();
}

/**
 * Whether a theme_mod key exists in the database (not just a get_theme_mod default).
 *
 * @param string $key Theme mod key.
 * @return bool
 */
function theme_mod_is_saved( $key ) {
	return array_key_exists( $key, get_theme_mods_raw() );
}

/**
 * Announcement bar lines from Customizer (JSON theme_mod), at most 10 non-empty strings.
 *
 * @return array<int, string>
 */
function get_announcement_lines() {
	$raw = theme_mod_is_saved( 'trove_announcement_lines' )
		? get_theme_mod( 'trove_announcement_lines', '' )
		: get_default_announcement_lines_json();

	if ( ! is_string( $raw ) ) {
		return array();
	}
	$decoded = json_decode( $raw, true );
	if ( ! is_array( $decoded ) ) {
		return array();
	}
	$out = array();
	foreach ( $decoded as $line ) {
		$line = sanitize_text_field( (string) $line );
		if ( '' !== $line ) {
			$out[] = $line;
		}
	}
	return array_slice( $out, 0, 10 );
}

/**
 * Coerce a Customizer “show bar” value to boolean (guards against legacy array mods).
 *
 * @param mixed $value Theme mod value.
 * @return bool
 */
function is_announcement_bar_enabled_value( $value ) {
	if ( is_array( $value ) ) {
		return false;
	}
	if ( is_string( $value ) ) {
		$value = strtolower( trim( $value ) );
		if ( in_array( $value, array( '', '0', 'false', 'off', 'no' ), true ) ) {
			return false;
		}
	}
	return wp_validate_boolean( $value );
}

/**
 * Whether any announcement “Show bar” theme_mod has been saved.
 *
 * @return bool
 */
function announcement_enabled_settings_exist() {
	$keys = array(
		'trove_announcement_enabled',
		'trove_announcement_enabled_pc',
		'trove_announcement_enabled_tablet',
		'trove_announcement_enabled_phone',
	);
	foreach ( $keys as $key ) {
		if ( theme_mod_is_saved( $key ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Per-device “Show bar” values from saved theme_mods (matches Customizer preview logic).
 *
 * @return array{pc: mixed, tablet: mixed, phone: mixed}
 */
function get_announcement_enabled_by_device() {
	$devices  = array( 'pc', 'tablet', 'phone' );
	$fallback = array(
		'pc'     => true,
		'tablet' => true,
		'phone'  => true,
	);
	$defaults = \Trove\Customizer\customizer_default( 'trove_announcement_enabled', $fallback );
	if ( ! is_array( $defaults ) ) {
		$defaults = $fallback;
	}

	$mods       = get_theme_mods_raw();
	$legacy     = isset( $mods['trove_announcement_enabled'] ) ? $mods['trove_announcement_enabled'] : null;
	$legacy_map = array();
	if ( is_array( $legacy ) ) {
		foreach ( $devices as $dev ) {
			if ( array_key_exists( $dev, $legacy ) ) {
				$legacy_map[ $dev ] = $legacy[ $dev ];
			}
		}
	} elseif ( ! is_array( $legacy ) && null !== $legacy ) {
		$legacy_map['pc'] = $legacy;
	}

	$initialized = announcement_enabled_settings_exist();
	$out         = array();

	foreach ( $devices as $dev ) {
		$key = 'trove_announcement_enabled_' . $dev;
		if ( array_key_exists( $key, $mods ) ) {
			$val = $mods[ $key ];
		} elseif ( array_key_exists( $dev, $legacy_map ) ) {
			$val = $legacy_map[ $dev ];
		} elseif ( ! $initialized ) {
			// Fresh install: defaults on. After lines are saved but “Show bar” was never stored, treat as off.
			if ( theme_mod_is_saved( 'trove_announcement_lines' ) ) {
				$val = false;
			} else {
				$val = array_key_exists( $dev, $defaults ) ? $defaults[ $dev ] : true;
			}
		} else {
			// User has saved announcement settings; unsuffixed device defaults to visible.
			$val = true;
		}
		$out[ $dev ] = $val;
	}

	return $out;
}

/**
 * Whether the announcement bar is enabled for at least one breakpoint (Customizer).
 *
 * @return bool
 */
function is_announcement_bar_enabled() {
	foreach ( get_announcement_enabled_by_device() as $value ) {
		if ( is_announcement_bar_enabled_value( $value ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Whether announcement bar markup and assets should load on this request.
 *
 * @return bool
 */
function should_load_announcement_bar() {
	return is_announcement_bar_enabled() && ! empty( get_announcement_lines() );
}

/**
 * Announcement transition: slide or fade.
 *
 * @return string 'slide'|'fade'
 */
function get_announcement_bar_animation() {
	$v = get_theme_mod( 'trove_announcement_animation', (string) \Trove\Customizer\customizer_default( 'trove_announcement_animation', 'slide' ) );
	$v = is_string( $v ) ? sanitize_key( $v ) : 'slide';
	return in_array( $v, array( 'slide', 'fade' ), true ) ? $v : 'slide';
}

/**
 * Whether announcement carousel autoplay is on.
 *
 * @return bool
 */
function get_announcement_bar_autoplay() {
	return wp_validate_boolean( get_theme_mod( 'trove_announcement_autoplay', (bool) \Trove\Customizer\customizer_default( 'trove_announcement_autoplay', false ) ) );
}

/**
 * Autoplay interval in seconds (2–60).
 *
 * @return int
 */
function get_announcement_bar_autoplay_interval_seconds() {
	$s = absint( get_theme_mod( 'trove_announcement_autoplay_interval', (int) \Trove\Customizer\customizer_default( 'trove_announcement_autoplay_interval', 5 ) ) );
	return max( 2, min( 60, $s ) );
}

/**
 * CSS `font-size` for announcement text from a size slug (Customizer).
 *
 * @param string $slug small|medium|big|large.
 * @return string Pixel value with `px` suffix.
 */
function announcement_text_size_slug_to_css( $slug ) {
	$slug = sanitize_key( (string) $slug );
	if ( ! in_array( $slug, array( 'small', 'medium', 'big', 'large' ), true ) ) {
		$slug = 'medium';
	}
	$map = array(
		'small'  => '14px',
		'medium' => '16px',
		'big'    => '18px',
		'large'  => '20px',
	);
	return $map[ $slug ];
}

/**
 * Renders the announcement bar (Customizer lines) above the main header.
 *
 * @return void
 */
function render_announcement_bar() {
	if ( ! should_load_announcement_bar() ) {
		return;
	}
	$lines = get_announcement_lines();
	$n        = count( $lines );
	$nav_hide = $n <= 1;
	$anim     = get_announcement_bar_animation();
	$autoplay = get_announcement_bar_autoplay();
	$interval = get_announcement_bar_autoplay_interval_seconds();
	$classes  = 'trove-announcement-bar';
	if ( 'fade' === $anim ) {
		$classes .= ' trove-announcement-bar--fade';
	}
	?>
	<div
		class="<?php echo esc_attr( $classes ); ?>"
		role="region"
		aria-label="<?php esc_attr_e( 'Announcement', 'trove' ); ?>"
		data-trove-announcement-carousel
		data-slide-count="<?php echo esc_attr( (string) $n ); ?>"
		data-animation="<?php echo esc_attr( $anim ); ?>"
		data-autoplay="<?php echo esc_attr( $autoplay ? 'true' : 'false' ); ?>"
		data-autoplay-interval-ms="<?php echo esc_attr( (string) ( $interval * 1000 ) ); ?>"
		style="<?php echo esc_attr( '--trove-announcement-total: ' . (int) $n . '; --trove-announcement-index: 0;' ); ?>"
		tabindex="0"
	>
		<div class="trove-announcement-bar__inner">
			<button
				type="button"
				class="trove-announcement-bar__nav trove-announcement-bar__nav--prev"
				aria-label="<?php esc_attr_e( 'Previous announcement', 'trove' ); ?>"
				<?php echo $nav_hide ? 'hidden' : ''; ?>
			>
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</button>
			<div class="trove-announcement-bar__viewport">
				<div class="trove-announcement-bar__track">
					<?php
					$i = 0;
					foreach ( $lines as $line ) {
						$active = 0 === $i;
						$slide_classes = 'trove-announcement-bar__slide';
						if ( 'fade' === $anim && $active ) {
							$slide_classes .= ' is-active';
						}
						echo '<div class="' . esc_attr( $slide_classes ) . '" aria-hidden="' . esc_attr( $active ? 'false' : 'true' ) . '">' . esc_html( $line ) . '</div>';
						++$i;
					}
					?>
				</div>
			</div>
			<button
				type="button"
				class="trove-announcement-bar__nav trove-announcement-bar__nav--next"
				aria-label="<?php esc_attr_e( 'Next announcement', 'trove' ); ?>"
				<?php echo $nav_hide ? 'hidden' : ''; ?>
			>
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</button>
		</div>
	</div>
	<?php
}
add_action( 'trove_announcement_bar', __NAMESPACE__ . '\\render_announcement_bar' );

/**
 * Front-end assets for the announcement bar (when enabled and lines exist).
 *
 * @return void
 */
function enqueue_announcement_bar_assets() {
	if ( ! should_load_announcement_bar() ) {
		return;
	}

	$version = defined( 'TROVE_THEME_VERSION' ) ? TROVE_THEME_VERSION : '1.0.0';

	wp_enqueue_style(
		'trove-announcement-bar',
		\Trove\TroveTheme::asset_url( 'assets/css/announcement-bar.css' ),
		array( 'trove-style' ),
		$version
	);
	wp_enqueue_script(
		'trove-announcement-carousel',
		\Trove\TroveTheme::asset_url( 'assets/js/announcement-carousel.js' ),
		array(),
		$version,
		true
	);
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_announcement_bar_assets', 20 );

/**
 * Remove queued announcement assets when the bar should not load.
 *
 * @return void
 */
function dequeue_announcement_bar_assets_when_disabled() {
	if ( should_load_announcement_bar() ) {
		return;
	}

	wp_dequeue_style( 'trove-announcement-bar' );
	wp_deregister_style( 'trove-announcement-bar' );
	wp_dequeue_script( 'trove-announcement-carousel' );
	wp_deregister_script( 'trove-announcement-carousel' );
}

/**
 * Block announcement stylesheet URL when the bar is off.
 *
 * @param string|false $src    Style src.
 * @param string       $handle Style handle.
 * @return string|false
 */
function block_announcement_bar_style_when_disabled( $src, $handle ) {
	if ( 'trove-announcement-bar' === $handle && ! should_load_announcement_bar() ) {
		return false;
	}
	return $src;
}

/**
 * Block announcement script URL when the bar is off.
 *
 * @param string|false $src    Script src.
 * @param string       $handle Script handle.
 * @return string|false
 */
function block_announcement_bar_script_when_disabled( $src, $handle ) {
	if ( 'trove-announcement-carousel' === $handle && ! should_load_announcement_bar() ) {
		return false;
	}
	return $src;
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\dequeue_announcement_bar_assets_when_disabled', 999 );
add_action( 'wp_print_styles', __NAMESPACE__ . '\\dequeue_announcement_bar_assets_when_disabled', 0 );
add_action( 'wp_print_scripts', __NAMESPACE__ . '\\dequeue_announcement_bar_assets_when_disabled', 0 );
add_filter( 'style_loader_src', __NAMESPACE__ . '\\block_announcement_bar_style_when_disabled', 10, 2 );
add_filter( 'script_loader_src', __NAMESPACE__ . '\\block_announcement_bar_script_when_disabled', 10, 2 );
