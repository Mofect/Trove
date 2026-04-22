<?php
/**
 * Single post template.
 *
 * @package Trove
 */

get_header();
?>
<main id="primary" class="site-main">
	<?php
	while ( have_posts() ) {
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="entry-featured">
					<?php the_post_thumbnail( 'large' ); ?>
				</div>
			<?php endif; ?>
			<header class="entry-header">
				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				<div class="entry-meta">
					<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
					<span class="entry-meta__sep" aria-hidden="true">·</span>
					<span class="entry-meta__author"><?php the_author_posts_link(); ?></span>
					<?php
					$cats = get_the_category_list( ', ' );
					if ( $cats ) {
						echo '<span class="entry-meta__sep" aria-hidden="true">·</span>';
						echo '<span class="entry-meta__posted">' . esc_html__( 'Posted in:', 'trove' ) . ' </span>';
						echo '<span class="entry-meta__cats">' . $cats . '</span>';
					}
					?>
				</div>
			</header>
			<div class="entry-content">
				<?php the_content(); ?>
				<?php
				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'trove' ),
						'after'  => '</div>',
					)
				);
				?>
			</div>
			<?php if ( has_tag() ) : ?>
				<footer class="entry-footer">
					<div class="entry-tags">
						<?php the_tags( '<span class="entry-tags__label">' . esc_html__( 'Tags: ', 'trove' ) . '</span>', ', ' ); ?>
					</div>
				</footer>
			<?php endif; ?>
			<nav class="post-nav" aria-label="<?php esc_attr_e( 'Post navigation', 'trove' ); ?>">
				<?php
				the_post_navigation(
					array(
						'prev_text' => __( '← Previous', 'trove' ),
						'next_text' => __( 'Next →', 'trove' ),
					)
				);
				?>
			</nav>
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
