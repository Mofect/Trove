<?php
/**
 * No results template.
 *
 * @package Trove
 */
?>
<section class="no-results">
	<h1 class="entry-title"><?php esc_html_e( 'Nothing here', 'trove' ); ?></h1>
	<p><?php esc_html_e( 'It seems we can’t find what you’re looking for.', 'trove' ); ?></p>
	<?php get_search_form(); ?>
</section>
