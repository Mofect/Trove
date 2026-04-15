<?php
/**
 * Search results template.
 *
 * @package Trove
 */

get_header();
?>
<main id="primary" class="site-main">
	<header class="archive-header">
		<h1 class="archive-title">
			<?php
			printf(
				/* translators: %s: search query */
				esc_html__( 'Search results for: %s', 'trove' ),
				'<span>' . esc_html( get_search_query() ) . '</span>'
			);
			?>
		</h1>
	</header>
	<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			get_template_part( 'template-parts/content', 'search' );
		}
		the_posts_pagination(
			array(
				'mid_size'  => 2,
				'prev_text' => __( 'Previous', 'trove' ),
				'next_text' => __( 'Next', 'trove' ),
			)
		);
	} else {
		get_template_part( 'template-parts/content', 'none' );
	}
	?>
</main>
<?php
get_footer();
