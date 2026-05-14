<?php
/**
 * Single product title.
 *
 * @package Trove
 * @see WooCommerce templates/single-product/title.php
 */

defined( 'ABSPATH' ) || exit;

if ( ! \Trove\Metabox\should_show_page_title() ) {
	return;
}

the_title( '<h1 class="product_title entry-title">', '</h1>' );
