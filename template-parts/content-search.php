<?php
/**
 * Search result item.
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
			<?php
			$post_type_object = get_post_type_object( get_post_type() );
			if ( $post_type_object ) {
				echo '<span class="entry-meta__type">' . esc_html( $post_type_object->labels->singular_name ) . '</span>';
			}
			?>
		</div>
	</header>
	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div>
</article>
