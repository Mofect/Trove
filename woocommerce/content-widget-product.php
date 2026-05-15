<?php
/**
 * Product widget list item (overrides WooCommerce default).
 *
 * Price (and rating) are output inside the permalink so the whole row is one click target.
 *
 * @package Trove
 * @see woocommerce/templates/content-widget-product.php
 * @version 3.5.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! is_a( $product, 'WC_Product' ) ) {
	return;
}

?>
<li>
	<?php do_action( 'woocommerce_widget_product_item_start', $args ); ?>

	<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
		<?php echo $product->get_image(); // PHPCS:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<div class="product-content">
			<span class="product-title"><?php echo wp_kses_post( $product->get_name() ); ?></span>
			<?php if ( ! empty( $show_rating ) ) : ?>
				<?php echo wc_get_rating_html( $product->get_average_rating() ); // PHPCS:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php endif; ?>
			<div class="product-price">
				<?php echo $product->get_price_html(); // PHPCS:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
	</a>

	<?php do_action( 'woocommerce_widget_product_item_end', $args ); ?>
</li>
