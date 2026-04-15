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
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			get_template_part( 'template-parts/content', get_post_type() );
		}
		the_posts_pagination(
			array(
				'mid_size'  => 2,
				'prev_text' => __( 'Previous', 'trove' ),
				'next_text' => __( 'Next', 'trove' ),
			)
		);
	} else {
		get_template_part( 'template-parts/content', 'none' );
	}
	?>
</main>
<?php
get_footer();
