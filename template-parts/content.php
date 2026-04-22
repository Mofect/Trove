<?php
/**
 * Default post excerpt in loops.
 *
 * @package Trove
 */

$blog_listing = \Trove\is_blog_listing_view();

$blog_layout = $blog_listing ? \Trove\get_blog_layout() : 'list';
$is_card     = 'list' !== $blog_layout;
$entry_extra = $is_card ? 'entry--card' : 'entry--list';

$post_classes = array( 'entry', $entry_extra );
if ( $blog_listing && 'list' === $blog_layout ) {
	$post_classes[] = 'entry--featured-pos-' . \Trove\get_blog_list_featured_image_position();
}

if ( $blog_listing ) {
	$show_featured  = \Trove\blog_loop_show_featured_image();
	$show_date      = \Trove\blog_loop_show_date();
	$show_author    = \Trove\blog_loop_show_author();
	$show_read_more = \Trove\blog_loop_show_continue_reading();
} else {
	$show_featured  = true;
	$show_date      = true;
	$show_author    = true;
	$show_read_more = true;
}

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $post_classes ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="entry-card__media<?php echo $blog_listing ? ' entry-card__media--blog' : ''; ?>"<?php echo ( $blog_listing && ! $show_featured ) ? ' hidden' : ''; ?>>
			<a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
				<?php the_post_thumbnail( 'large', array( 'class' => 'entry-card__thumb-img' ) ); ?>
			</a>
		</div>
	<?php endif; ?>
	<div class="entry-card__body">
		<header class="entry-header">
			<?php if ( has_category() ) : ?>
				<p class="entry-meta__cats"><?php echo wp_kses_post( get_the_category_list( ', ' ) ); ?></p>
			<?php endif; ?>
			<?php
			the_title(
				'<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">',
				'</a></h2>'
			);
			?>
			<?php if ( $blog_listing ) : ?>
				<div class="entry-meta entry-meta--blog"<?php echo ( ! $show_date && ! $show_author ) ? ' hidden' : ''; ?>>
					<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"<?php echo ! $show_date ? ' hidden' : ''; ?>><?php echo esc_html( get_the_date() ); ?></time>
					<span class="entry-meta__sep" aria-hidden="true"<?php echo ( $show_date && $show_author ) ? '' : ' hidden'; ?>>·</span>
					<span class="entry-meta__author"<?php echo ! $show_author ? ' hidden' : ''; ?>><?php the_author_posts_link(); ?></span>
				</div>
			<?php else : ?>
				<div class="entry-meta">
					<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
					<span class="entry-meta__sep" aria-hidden="true">·</span>
					<span class="entry-meta__author"><?php the_author_posts_link(); ?></span>
				</div>
			<?php endif; ?>
		</header>
		<div class="entry-summary">
			<div class="entry-summary__text">
				<?php the_excerpt(); ?>
			</div>
			<?php if ( $blog_listing ) : ?>
				<p class="read-more read-more--blog"<?php echo ! $show_read_more ? ' hidden' : ''; ?>>
					<a href="<?php the_permalink(); ?>"><?php esc_html_e( 'Continue reading', 'trove' ); ?></a>
				</p>
			<?php else : ?>
				<p class="read-more">
					<a href="<?php the_permalink(); ?>"><?php esc_html_e( 'Continue reading', 'trove' ); ?></a>
				</p>
			<?php endif; ?>
		</div>
	</div>
</article>
