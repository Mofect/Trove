<?php
/**
 * Archive template.
 *
 * @package Trove
 */

get_header();
?>
<main id="primary" class="site-main">
	<header class="archive-header">
		<?php the_archive_title( '<h1 class="archive-title">', '</h1>' ); ?>
		<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
	</header>
	<?php get_template_part( 'template-parts/loop', 'blog' ); ?>
</main>
<?php
get_footer();
