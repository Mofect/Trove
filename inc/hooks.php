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
	$layout = get_theme_mod( 'trove_header_layout', (string) \Trove\Customizer\trove_customizer_default( 'trove_header_layout', 'horizontal-left-logo' ) );
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
			get_template_directory_uri() . '/assets/js/announcement-carousel.js',
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
	$footer_layout = get_theme_mod( 'trove_footer_layout', (string) \Trove\Customizer\trove_customizer_default( 'trove_footer_layout', 'footer-1' ) );
	$footer_classes   = array( 'site-footer', 'light-scheme' );
	if ( wp_validate_boolean( get_theme_mod( 'trove_footer_top_border', (bool) \Trove\Customizer\trove_customizer_default( 'trove_footer_top_border', true ) ) ) ) {
		$footer_classes[] = 'trove-footer-has-top-border';
	}

	?>
	<footer class="<?php echo esc_attr( implode( ' ', $footer_classes ) ); ?>" role="contentinfo">
		<?php get_template_part( 'template-parts/' . $footer_layout, null ); ?>
	</footer>
	<?php
}
add_action( 'trove_footer', __NAMESPACE__ . '\\render_footer' );

/**
 * Blog posts loop (index + archives). Layout comes from Customizer → Blog Posts → Layout.
 *
 * @return void
 */
function render_blog_loop() {
	$layout          = is_blog_listing_view() ? get_blog_layout() : 'list';
	$pagination_type = is_blog_listing_view() ? get_blog_pagination_type() : 'paged';
	global $wp_query;
	$wp_max        = isset( $wp_query ) ? (int) $wp_query->max_num_pages : 0;
	$had_posts     = isset( $wp_query ) && $wp_query->post_count > 0;
	$paged_current = get_blog_list_paged();
	$use_infinite  = $had_posts && 'infinite_scroll' === $pagination_type && $wp_max > 1;
	$next_link     = $use_infinite ? get_next_posts_page_link() : '';

	$pagination_args = array(
		'mid_size'  => 2,
		'prev_text' => __( 'Previous', 'trove' ),
		'next_text' => __( 'Next', 'trove' ),
	);
	?>
	<div class="trove-blog-loop trove-blog-loop--<?php echo esc_attr( $layout ); ?>"
	<?php
	if ( $use_infinite ) {
		echo ' data-trove-infinite="1"';
		echo ' data-trove-max-pages="' . esc_attr( (string) $wp_max ) . '"';
		echo ' data-trove-current-page="' . esc_attr( (string) $paged_current ) . '"';
		echo ' data-trove-next-url="' . ( $next_link ? esc_url( $next_link ) : '' ) . '"';
	}
	?>
	>
		<?php
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				get_template_part( 'template-parts/content', get_post_type() );
			}
		} else {
			get_template_part( 'template-parts/content', 'none' );
		}
		?>
	</div>
	<?php
	if ( $had_posts ) {
		if ( $use_infinite && $next_link ) {
			echo '<div class="trove-blog-infinite-sentinel" id="trove-blog-infinite-sentinel" aria-hidden="true"></div>';
			echo '<p class="trove-blog-infinite-status screen-reader-text" role="status" aria-live="polite"></p>';
		} else {
			the_posts_pagination( $pagination_args );
		}
	}
}
add_action( 'trove_blog_loop', __NAMESPACE__ . '\\render_blog_loop' );
