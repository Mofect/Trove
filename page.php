<?php
/**
 * Page template.
 *
 * @package Trove
 */

get_header();

$banner_covers_title = \Trove\Metabox\page_banner_shows_entry_title( (int) get_queried_object_id() );
?>
<main id="primary" class="site-main">
	<?php
	while ( have_posts() ) {
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--page' ); ?>>
			<?php if ( \Trove\Metabox\should_show_page_title() && ! $banner_covers_title ) : ?>
			<header class="entry-header">
				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
			</header>
			<?php endif; ?>
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="entry-featured">
					<?php the_post_thumbnail( 'large' ); ?>
				</div>
			<?php endif; ?>
			<div class="entry-content">
				<?php
				if ( function_exists( '\\Trove\\should_enqueue_checkout_styles' ) && \Trove\should_enqueue_checkout_styles() ) {
					do_action( 'trove_entry_content_top' );
				}
				the_content();
				?>
				<?php
				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'trove' ),
						'after'  => '</div>',
					)
				);
				?>
			</div>
		</article>
		<?php
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
	}
	?>
</main>
<?php
get_footer();
