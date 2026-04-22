<?php
/**
 * Main template (blog index, fallback).
 *
 * @package Trove
 */

get_header();
?>
<main id="primary" class="site-main">
	<?php 
		/*
			Child themes or plugins can override or extend behavior with:
			remove_action( 'trove_blog_loop', [ \Trove\render_blog_loop, ... ] ); // adjust if needed
			add_action( 'trove_blog_loop', 'my_custom_blog_loop' );
			Or append output with add_action( 'trove_blog_loop', 'my_extra_markup', 20 ); (default priority is 10).
		*/
		do_action( 'trove_blog_loop' ); 
	?>
</main>
<?php
get_footer();
