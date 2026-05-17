<?php
/**
 * Cross-sells carousel (classic cart).
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.6.0
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $cross_sells ) ) {
	return;
}

$heading  = apply_filters( 'woocommerce_product_cross_sells_products_heading', __( 'You may be interested in&hellip;', 'woocommerce' ) );
$visible  = \Trove\woo_cart_cross_sells_visible();
$carousel = 'trove-cart-cross-sells-carousel';
?>
<div class="cross-sells trove-cart-cross-sells">
	<?php if ( $heading ) : ?>
		<h2><?php echo esc_html( $heading ); ?></h2>
	<?php endif; ?>

	<div
		class="<?php echo esc_attr( $carousel ); ?> trove-woo-product-carousel"
		data-trove-woo-product-carousel
		data-trove-cart-cross-sells-carousel
		data-visible="<?php echo esc_attr( (string) $visible ); ?>"
		data-carousel-context="cross-sells"
		style="<?php echo esc_attr( '--trove-cart-cross-sells-visible: ' . (int) $visible . ';' ); ?>"
	>
		<div class="trove-cart-cross-sells-carousel__viewport" tabindex="0">
			<?php woocommerce_product_loop_start(); ?>

			<?php foreach ( $cross_sells as $cross_sell ) : ?>
				<?php
				$post_object = get_post( $cross_sell->get_id() );

				setup_postdata( $GLOBALS['post'] = $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

				wc_get_template_part( 'content', 'product' );
				?>
			<?php endforeach; ?>

			<?php woocommerce_product_loop_end(); ?>
		</div>

		<button
			type="button"
			class="trove-cart-cross-sells-carousel__nav trove-cart-cross-sells-carousel__nav--prev"
			aria-label="<?php esc_attr_e( 'Previous products', 'trove' ); ?>"
			disabled
		>
			<span aria-hidden="true">‹</span>
		</button>
		<button
			type="button"
			class="trove-cart-cross-sells-carousel__nav trove-cart-cross-sells-carousel__nav--next"
			aria-label="<?php esc_attr_e( 'Next products', 'trove' ); ?>"
			disabled
		>
			<span aria-hidden="true">›</span>
		</button>
	</div>
</div>
<?php
wp_reset_postdata();
