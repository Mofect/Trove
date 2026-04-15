<?php
/**
 * Default post excerpt in loops.
 *
 * @package Trove
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--list' ); ?>>
	<header class="entry-header">
		<?php
		the_title(
			'<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">',
			'</a></h2>'
		);
		?>
		<div class="entry-meta">
			<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
			<span class="entry-meta__sep" aria-hidden="true">·</span>
			<span class="entry-meta__author"><?php the_author_posts_link(); ?></span>
		</div>
	</header>
	<div class="entry-summary">
		<?php the_excerpt(); ?>
		<p class="read-more"><a href="<?php the_permalink(); ?>"><?php esc_html_e( 'Continue reading', 'trove' ); ?></a></p>
	</div>
</article>
