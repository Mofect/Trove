<?php
/**
 * Main template (blog index, fallback).
 *
 * @package Trove
 */

get_header();
?>
<main id="primary" class="site-main">
	<?php get_template_part( 'template-parts/loop', 'blog' ); ?>
</main>
<?php
get_footer();
