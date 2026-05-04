<?php
/**
 * Single Product Meta
 *
 * @package Trove
 */

namespace Trove;

use Automattic\WooCommerce\Enums\ProductType;

defined( 'ABSPATH' ) || exit;

global $product;
?>
<div class="product_meta">

	<?php do_action( 'woocommerce_product_meta_start' ); ?>

	<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( ProductType::VARIABLE ) ) ) : ?>

		<div class="sku_wrapper"><?php esc_html_e( 'SKU:', 'woocommerce' ); ?> <span class="sku"><?php echo ( $sku = $product->get_sku() ) ? esc_html( $sku ) : esc_html__( 'N/A', 'woocommerce' ); ?></span></div>

	<?php endif; ?>

	<?php
	$categories = wc_get_product_category_list( $product->get_id(), ', ' );
	if ( $categories ) :
	?>
		<div class="posted_in"><?php echo _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'woocommerce' ) . ' ' . wp_kses_post( $categories ); ?></div>
	<?php endif; ?>

	<?php
	$tags = wc_get_product_tag_list( $product->get_id(), ', ' );
	if ( $tags ) :
	?>
		<div class="tagged_as"><?php echo _n( 'Tag:', 'Tags:', count( $product->get_tag_ids() ), 'woocommerce' ) . ' ' . wp_kses_post( $tags ); ?></div>
	<?php endif; ?>

	<?php do_action( 'woocommerce_product_meta_end' ); ?>

</div>
