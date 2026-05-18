<?php
/**
 * Product loop carousel (Swiper).
 *
 * @package Trove
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $products ) ) {
	return;
}

$heading          = isset( $heading ) ? (string) $heading : '';
$section_class    = isset( $section_class ) ? (string) $section_class : 'products';
$visible          = isset( $visible ) ? max( 1, (int) $visible ) : 4;
$carousel_context = isset( $carousel_context ) ? (string) $carousel_context : 'related';
$visible_mobile   = in_array( $carousel_context, array( 'related', 'upsells' ), true ) ? 2 : $visible;

$GLOBALS['trove_rendering_product_carousel'] = true;

wc_set_loop_prop( 'columns', $visible );
?>
<section class="<?php echo esc_attr( $section_class ); ?> trove-woo-product-carousel-section trove-woo-product-carousel-section--<?php echo esc_attr( sanitize_html_class( $carousel_context ) ); ?>">
	<?php if ( '' !== $heading ) : ?>
		<h2><?php echo esc_html( $heading ); ?></h2>
	<?php endif; ?>

	<div
		class="trove-woo-product-carousel swiper"
		data-trove-woo-product-carousel
		data-visible="<?php echo esc_attr( (string) $visible ); ?>"
		data-visible-mobile="<?php echo esc_attr( (string) $visible_mobile ); ?>"
		data-space-between="20"
		data-carousel-context="<?php echo esc_attr( $carousel_context ); ?>"
		style="--trove-woo-product-carousel-gap: 20px;"
	>
		<?php woocommerce_product_loop_start(); ?>

		<?php foreach ( $products as $product ) : ?>
			<?php
			if ( ! $product instanceof \WC_Product ) {
				continue;
			}
			$post_object = get_post( $product->get_id() );
			if ( ! $post_object ) {
				continue;
			}

			setup_postdata( $GLOBALS['post'] = $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

			wc_get_template_part( 'content', 'product' );
			?>
		<?php endforeach; ?>

		<?php woocommerce_product_loop_end(); ?>

		<button
			type="button"
			class="trove-woo-product-carousel__nav trove-woo-product-carousel__nav--prev"
			aria-label="<?php esc_attr_e( 'Previous products', 'trove' ); ?>"
		>
			<span aria-hidden="true">‹</span>
		</button>
		<button
			type="button"
			class="trove-woo-product-carousel__nav trove-woo-product-carousel__nav--next"
			aria-label="<?php esc_attr_e( 'Next products', 'trove' ); ?>"
		>
			<span aria-hidden="true">›</span>
		</button>
	</div>
</section>
<?php
unset( $GLOBALS['trove_rendering_product_carousel'] );
wp_reset_postdata();
