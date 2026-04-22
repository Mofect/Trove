<?php
/**
 * Search results template.
 *
 * @package Trove
 */

get_header();
?>
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
<main id="primary" class="site-main">
	<?php do_action( 'trove_blog_loop' ); ?>
</main>
<?php
get_footer();
