<?php
/**
 * Theme hooks.
 *
 * @package Trove
 */

namespace Trove;

defined( 'ABSPATH' ) || exit;

/**
 * Loads the header template part for the current Customizer layout.
 *
 * @return void
 */
function render_header() {
	$layout = get_theme_mod( 'trove_header_layout', 'horizontal-left-logo' );
	$layout = is_string( $layout ) ? sanitize_key( $layout ) : 'horizontal-left-logo';

	if ( 'vertical-centered-logo' === $layout || 'vertical-left-logo' === $layout ) {
		get_template_part( 'template-parts/header-vertical' );
	} else {
		get_template_part( 'template-parts/header-horizontal' );
	}
}
add_action( 'trove_header', __NAMESPACE__ . '\\render_header' );

/**
 * Renders the announcement bar (Customizer lines) above the main header.
 *
 * @return void
 */
function render_announcement_bar() {
	$lines = get_announcement_lines();
	if ( empty( $lines ) ) {
		return;
	}
	$n        = count( $lines );
	$nav_hide = $n <= 1;
	?>
	<div
		class="trove-announcement-bar"
		role="region"
		aria-label="<?php esc_attr_e( 'Announcement', 'trove' ); ?>"
		data-trove-announcement-carousel
		data-slide-count="<?php echo esc_attr( (string) $n ); ?>"
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
						echo '<div class="trove-announcement-bar__slide" aria-hidden="' . esc_attr( $active ? 'false' : 'true' ) . '">' . esc_html( $line ) . '</div>';
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
	if ( empty( get_announcement_lines() ) && ! is_customize_preview() ) {
		return;
	}
	wp_enqueue_script(
		'trove-announcement-carousel',
		get_template_directory_uri() . '/assets/js/announcement-carousel.js',
		array(),
		defined( 'TROVE_THEME_VERSION' ) ? TROVE_THEME_VERSION : '1.0.0',
		true
	);
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_announcement_carousel_script', 20 );

/**
 * Renders the theme footer markup.
 *
 * @return void
 */
function render_footer() {
	$footer_dark_mode = wp_validate_boolean( get_theme_mod( 'trove_footer_dark_mode', false ) );
	$footer_layout = get_theme_mod( 'trove_footer_layout', 'footer-1' );
	$footer_classes   = 'site-footer' . ( $footer_dark_mode ? '' : ' light-scheme' );

	?>
	<footer class="<?php echo esc_attr( $footer_classes ); ?>" role="contentinfo">
		<?php get_template_part( 'template-parts/' . $footer_layout, null ); ?>
	</footer>
	<?php
}
add_action( 'trove_footer', __NAMESPACE__ . '\\render_footer' );
