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
	$lines = \Trove\Customizer\trove_customizer_default( 'trove_announcement_lines_default', array( 'Announcement 1', 'Announcement 2' ) );
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
 * Announcement bar lines from Customizer (JSON theme_mod), at most 10 non-empty strings.
 *
 * @return array<int, string>
 */
function get_announcement_lines() {
	$raw = get_theme_mod( 'trove_announcement_lines', get_default_announcement_lines_json() );
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
 * Whether the announcement bar is enabled for at least one breakpoint (Customizer).
 *
 * @return bool
 */
function is_announcement_bar_enabled() {
	$fallback = array(
		'pc'     => true,
		'tablet' => true,
		'phone'  => true,
	);
	$defaults = \Trove\Customizer\trove_customizer_default( 'trove_announcement_enabled', $fallback );
	if ( ! is_array( $defaults ) ) {
		$defaults = $fallback;
	}
	$v = \Trove\Customizer\get_responsive_mod_values( 'trove_announcement_enabled', $defaults );
	return wp_validate_boolean( $v['pc'] )
		|| wp_validate_boolean( $v['tablet'] )
		|| wp_validate_boolean( $v['phone'] );
}

/**
 * Announcement transition: slide or fade.
 *
 * @return string 'slide'|'fade'
 */
function get_announcement_bar_animation() {
	$v = get_theme_mod( 'trove_announcement_animation', (string) \Trove\Customizer\trove_customizer_default( 'trove_announcement_animation', 'slide' ) );
	$v = is_string( $v ) ? sanitize_key( $v ) : 'slide';
	return in_array( $v, array( 'slide', 'fade' ), true ) ? $v : 'slide';
}

/**
 * Whether announcement carousel autoplay is on.
 *
 * @return bool
 */
function get_announcement_bar_autoplay() {
	return wp_validate_boolean( get_theme_mod( 'trove_announcement_autoplay', (bool) \Trove\Customizer\trove_customizer_default( 'trove_announcement_autoplay', false ) ) );
}

/**
 * Autoplay interval in seconds (2–60).
 *
 * @return int
 */
function get_announcement_bar_autoplay_interval_seconds() {
	$s = absint( get_theme_mod( 'trove_announcement_autoplay_interval', (int) \Trove\Customizer\trove_customizer_default( 'trove_announcement_autoplay_interval', 5 ) ) );
	return max( 2, min( 60, $s ) );
}

/**
 * CSS `font-size` for announcement text from a size slug (Customizer).
 *
 * @param string $slug small|medium|big|large.
 * @return string Pixel value with `px` suffix.
 */
function trove_announcement_text_size_slug_to_css( $slug ) {
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
	if ( ! is_announcement_bar_enabled() ) {
		return;
	}
	$lines = get_announcement_lines();
	if ( empty( $lines ) ) {
		return;
	}
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
 * Front-end script for announcement carousel (only when lines exist).
 *
 * @return void
 */
function enqueue_announcement_carousel_script() {
	if ( is_customize_preview() ) {
		wp_enqueue_script(
			'trove-announcement-carousel',
			\Trove\TroveTheme::asset_url( 'assets/js/announcement-carousel.js' ),
			array(),
			defined( 'TROVE_THEME_VERSION' ) ? TROVE_THEME_VERSION : '1.0.0',
			true
		);
		return;
	}
	if ( ! is_announcement_bar_enabled() || empty( get_announcement_lines() ) ) {
		return;
	}
	wp_enqueue_script(
		'trove-announcement-carousel',
		\Trove\TroveTheme::asset_url( 'assets/js/announcement-carousel.js' ),
		array(),
		defined( 'TROVE_THEME_VERSION' ) ? TROVE_THEME_VERSION : '1.0.0',
		true
	);
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_announcement_carousel_script', 20 );
