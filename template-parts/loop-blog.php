<?php
/**
 * Blog posts loop (index + archives). Layout comes from Customizer → Blog Posts → Layout.
 *
 * @package Trove
 */

defined( 'ABSPATH' ) || exit;

$layout            = \Trove\is_blog_listing_view() ? \Trove\get_blog_layout() : 'list';
$pagination_type   = \Trove\is_blog_listing_view() ? \Trove\get_blog_pagination_type() : 'paged';
global $wp_query;
$wp_max            = isset( $wp_query ) ? (int) $wp_query->max_num_pages : 0;
$had_posts         = isset( $wp_query ) && $wp_query->post_count > 0;
$paged_current     = \Trove\get_blog_list_paged();
$use_infinite      = $had_posts && 'infinite_scroll' === $pagination_type && $wp_max > 1;
$next_link         = $use_infinite ? get_next_posts_page_link() : '';

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
