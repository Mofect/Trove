<?php
/**
 * Sidebar template: outputs the Blog or WooCommerce widget area when active.
 *
 * @package Trove
 */

defined( 'ABSPATH' ) || exit;


$trove_sidebar_id = function_exists( 'is_woocommerce' ) && is_woocommerce() ? 'woocommerce-sidebar' : 'blog-sidebar';

if ( ! is_active_sidebar( $trove_sidebar_id ) ) {
	return;
}

?>

<aside class="widget-area widget-area--<?php echo esc_attr( $trove_sidebar_id ); ?> <?php echo 'woocommerce-sidebar' === $trove_sidebar_id ? 'trove-shop-filters' : ''; ?>" role="complementary" aria-label="<?php echo 'shop-sidebar' === $trove_sidebar_id ? esc_attr__( 'Shop sidebar', 'trove' ) : esc_attr__( 'Blog sidebar', 'trove' ); ?>">
	<?php dynamic_sidebar( $trove_sidebar_id ); ?>
</aside>
