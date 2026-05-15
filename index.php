<?php
/**
 * Main template (blog index, fallback).
 *
 * @package Trove
 */

get_header();
$show_listing_sidebar = \Trove\is_posts_listing_sidebar_enabled();
?>
<main id="primary" class="site-main">
	<?php if ( $show_listing_sidebar ) : ?>
		<div class="trove-listing-with-sidebar">
			<div class="trove-listing-with-sidebar__content">
				<?php
				/*
					Child themes or plugins can override or extend behavior with:
					remove_action( 'trove_blog_loop', [ \Trove\render_blog_loop, ... ] ); // adjust if needed
					add_action( 'trove_blog_loop', 'my_custom_blog_loop' );
					Or append output with add_action( 'trove_blog_loop', 'my_extra_markup', 20 ); (default priority is 10).
				*/
				do_action( 'trove_blog_loop' );
				?>
			</div>
			<?php get_sidebar(); ?>
		</div>
	<?php else : ?>
		<?php
		/*
			Child themes or plugins can override or extend behavior with:
			remove_action( 'trove_blog_loop', [ \Trove\render_blog_loop, ... ] ); // adjust if needed
			add_action( 'trove_blog_loop', 'my_custom_blog_loop' );
			Or append output with add_action( 'trove_blog_loop', 'my_extra_markup', 20 ); (default priority is 10).
		*/
		do_action( 'trove_blog_loop' );
		?>
	<?php endif; ?>
</main>
<?php
get_footer();
