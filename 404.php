<?php
/**
 * 404 template.
 *
 * @package Trove
 */

get_header();
?>
<main id="primary" class="site-main">
	<section class="error-404">
		<h1 class="entry-title"><?php esc_html_e( 'Page not found', 'trove' ); ?></h1>
		<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try a search?', 'trove' ); ?></p>
		<?php get_search_form(); ?>
	</section>
</main>
<?php
get_footer();
