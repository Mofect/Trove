<?php
/**
 * Blog listing loop output and Customizer-driven listing helpers.
 *
 * @package Trove
 */

namespace Trove;

defined( 'ABSPATH' ) || exit;

/**
 * Whether the current template is the blog index or a standard post archive (category, tag, author, date).
 *
 * @return bool
 */
function is_blog_listing_view() {
	if ( is_search() ) {
		return true;
	}
	if ( is_home() ) {
		return true;
	}
	if ( is_category() || is_tag() || is_author() || is_date() ) {
		return true;
	}
	return false;
}

/**
 * Whether blog.css should load on the current request.
 *
 * @return bool
 */
function should_enqueue_blog_styles() {
	if ( is_search() || is_home() || is_category() || is_author() ) {
		return true;
	}
	if ( is_singular( 'post' ) ) {
		return true;
	}
	return is_page();
}

/**
 * Listing context key used by Blog/Search/Author/Archive page options.
 *
 * @return string blog|search_page|author_page|archive_page
 */
function get_posts_listing_customizer_context() {
	if ( is_search() ) {
		return 'search_page';
	}
	if ( is_author() ) {
		return 'author_page';
	}
	if ( is_category() || is_tag() || is_date() ) {
		return 'archive_page';
	}
	return 'blog';
}

/**
 * Resolves setting id by listing context.
 *
 * @param string $suffix Suffix after the namespace prefix.
 * @return string
 */
function get_posts_listing_setting_id( $suffix ) {
	$suffix = sanitize_key( (string) $suffix );
	$ctx    = get_posts_listing_customizer_context();
	if ( 'blog' === $ctx ) {
		return 'trove_blog_' . $suffix;
	}
	return 'trove_' . $ctx . '_' . $suffix;
}

/**
 * Whether the Blog Sidebar should render for the current posts listing (default homepage or category/tag/date archives only).
 *
 * @return bool
 */
function is_posts_listing_sidebar_enabled() {
	if ( ! is_blog_listing_view() ) {
		return false;
	}
	$ctx = get_posts_listing_customizer_context();
	if ( 'blog' !== $ctx && 'archive_page' !== $ctx ) {
		return false;
	}
	$id = get_posts_listing_setting_id( 'enable_sidebar' );
	return wp_validate_boolean(
		get_theme_mod(
			$id,
			(bool) \Trove\Customizer\customizer_default( $id, (bool) \Trove\Customizer\customizer_default( 'trove_blog_enable_sidebar', false ) )
		)
	);
}

/**
 * Blog/Search/Author/Archive listing layout from Customizer: list, grid, masonry.
 *
 * @return string list|grid|masonry
 */
function get_blog_layout() {
	$id = get_posts_listing_setting_id( 'layout' );
	$v  = get_theme_mod( $id, (string) \Trove\Customizer\customizer_default( $id, (string) \Trove\Customizer\customizer_default( 'trove_blog_layout', 'list' ) ) );
	$v = is_string( $v ) ? sanitize_key( $v ) : 'list';
	$allowed = array( 'list', 'grid', 'masonry' );
	return in_array( $v, $allowed, true ) ? $v : 'list';
}

/**
 * Current page number for the main query on the blog index and post archives.
 *
 * @return int
 */
function get_blog_list_paged() {
	$paged = (int) get_query_var( 'paged' );
	if ( $paged < 1 ) {
		$paged = (int) get_query_var( 'page' );
	}
	return max( 1, $paged );
}

/**
 * Blog listing pagination: numbered links or infinite scroll (Customizer → Blog Posts → Settings).
 *
 * @return string paged|infinite_scroll
 */
function get_blog_pagination_type() {
	$id = get_posts_listing_setting_id( 'pagination_type' );
	$v  = get_theme_mod( $id, (string) \Trove\Customizer\customizer_default( $id, (string) \Trove\Customizer\customizer_default( 'trove_blog_pagination_type', 'paged' ) ) );
	$v = is_string( $v ) ? sanitize_key( $v ) : 'paged';
	$allowed = array( 'paged', 'infinite_scroll' );
	return in_array( $v, $allowed, true ) ? $v : 'paged';
}

/**
 * Featured image placement for the List blog layout (Customizer → Blog Posts → Settings).
 *
 * @return string top|left|right
 */
function get_blog_list_featured_image_position() {
	$id = get_posts_listing_setting_id( 'list_featured_position' );
	$v  = get_theme_mod( $id, (string) \Trove\Customizer\customizer_default( $id, (string) \Trove\Customizer\customizer_default( 'trove_blog_list_featured_position', 'top' ) ) );
	$v = is_string( $v ) ? sanitize_key( $v ) : 'top';
	$allowed = array( 'top', 'left', 'right' );
	return in_array( $v, $allowed, true ) ? $v : 'top';
}

/**
 * Whether “Boxed style” is on (Customizer → Blog Posts → Styles).
 *
 * @return bool
 */
function is_blog_boxed_style() {
	$id = get_posts_listing_setting_id( 'boxed_style' );
	return wp_validate_boolean( get_theme_mod( $id, (bool) \Trove\Customizer\customizer_default( $id, (bool) \Trove\Customizer\customizer_default( 'trove_blog_boxed_style', false ) ) ) );
}

/**
 * Blog listing: show featured image (Customizer → Blog Posts → Settings).
 *
 * @return bool
 */
function blog_loop_show_featured_image() {
	$id = get_posts_listing_setting_id( 'show_featured_image' );
	return wp_validate_boolean( get_theme_mod( $id, (bool) \Trove\Customizer\customizer_default( $id, (bool) \Trove\Customizer\customizer_default( 'trove_blog_show_featured_image', true ) ) ) );
}

/**
 * Blog listing: show publication date.
 *
 * @return bool
 */
function blog_loop_show_date() {
	$id = get_posts_listing_setting_id( 'show_date' );
	return wp_validate_boolean( get_theme_mod( $id, (bool) \Trove\Customizer\customizer_default( $id, (bool) \Trove\Customizer\customizer_default( 'trove_blog_show_date', true ) ) ) );
}

/**
 * Blog listing: show author.
 *
 * @return bool
 */
function blog_loop_show_author() {
	$id = get_posts_listing_setting_id( 'show_author' );
	return wp_validate_boolean( get_theme_mod( $id, (bool) \Trove\Customizer\customizer_default( $id, (bool) \Trove\Customizer\customizer_default( 'trove_blog_show_author', true ) ) ) );
}

/**
 * Blog listing: show “Continue reading” link.
 *
 * @return bool
 */
function blog_loop_show_continue_reading() {
	$id = get_posts_listing_setting_id( 'show_continue_reading' );
	return wp_validate_boolean( get_theme_mod( $id, (bool) \Trove\Customizer\customizer_default( $id, (bool) \Trove\Customizer\customizer_default( 'trove_blog_show_continue_reading', true ) ) ) );
}

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

/**
 * Adds `trove-blog-layout--{list|grid|masonry}` on blog index and post archives.
 * Adds `trove-blog-layout--list__top-image` when layout is list and featured image position is Top.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function filter_body_class_blog_layout( $classes ) {
	if ( ! is_array( $classes ) ) {
		$classes = array();
	}
	if ( ! is_blog_listing_view() ) {
		return $classes;
	}
	$layout    = get_blog_layout();
	$classes[] = 'trove-blog-layout--' . $layout;
	if ( 'list' === $layout && 'top' === get_blog_list_featured_image_position() ) {
		$classes[] = 'trove-blog-layout--list__top-image';
	}
	return $classes;
}
add_filter( 'body_class', __NAMESPACE__ . '\\filter_body_class_blog_layout' );

/**
 * Adds `trove-blog-boxed` when Boxed style is enabled (blog index/archives only).
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function filter_body_class_blog_boxed( $classes ) {
	if ( ! is_array( $classes ) ) {
		$classes = array();
	}
	if ( ! is_blog_listing_view() || ! is_blog_boxed_style() ) {
		return $classes;
	}
	$classes[] = 'trove-blog-boxed';
	return $classes;
}
add_filter( 'body_class', __NAMESPACE__ . '\\filter_body_class_blog_boxed' );

/**
 * Adds `trove-blog-pagination--paged` or `trove-blog-pagination--infinite` on blog listings.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function filter_body_class_blog_pagination( $classes ) {
	if ( ! is_array( $classes ) ) {
		$classes = array();
	}
	if ( ! is_blog_listing_view() ) {
		return $classes;
	}
	$classes[] = 'infinite_scroll' === get_blog_pagination_type()
		? 'trove-blog-pagination--infinite'
		: 'trove-blog-pagination--paged';
	return $classes;
}
add_filter( 'body_class', __NAMESPACE__ . '\\filter_body_class_blog_pagination' );
