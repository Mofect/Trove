<?php
/**
 * Grouped product list: image + title + price in label column; no separate price column.
 *
 * @package Trove
 */

namespace Trove;

defined( 'ABSPATH' ) || exit;

/**
 * @param array<int, string> $columns Column IDs.
 * @param \WC_Product        $product Grouped parent product.
 * @return array<int, string>
 */
function filter_grouped_product_columns( $columns, $product ) {
	unset( $product );
	return array( 'label', 'quantity' );
}
add_filter( 'woocommerce_grouped_product_columns', __NAMESPACE__ . '\\filter_grouped_product_columns', 10, 2 );

/**
 * Label column: thumbnail, title, and price stacked.
 *
 * @param string      $value               Default column HTML.
 * @param \WC_Product $grouped_product_child Child product.
 * @return string
 */
function filter_grouped_product_list_column_label( $value, $grouped_product_child ) {
	unset( $value );

	if ( ! $grouped_product_child instanceof \WC_Product ) {
		return '';
	}

	$product_id = $grouped_product_child->get_id();

	if ( $grouped_product_child->is_visible() ) {
		$title = sprintf(
			'<a href="%s" class="trove-grouped-product__title-link">%s</a>',
			esc_url(
				apply_filters(
					'woocommerce_grouped_product_list_link',
					$grouped_product_child->get_permalink(),
					$product_id
				)
			),
			esc_html( $grouped_product_child->get_name() )
		);
	} else {
		$title = sprintf(
			'<span class="trove-grouped-product__title-text">%s</span>',
			esc_html( $grouped_product_child->get_name() )
		);
	}

	$thumb = $grouped_product_child->get_image(
		'woocommerce_thumbnail',
		array(
			'class' => 'trove-grouped-product__thumb',
			'alt'   => esc_attr( $grouped_product_child->get_name() ),
		)
	);

	$price_html = $grouped_product_child->get_price_html();
	if ( $price_html ) {
		$price_html .= wc_get_stock_html( $grouped_product_child );
	}

	ob_start();
	?>
	<div class="trove-grouped-product__info">
		<?php if ( $thumb ) : ?>
			<div class="trove-grouped-product__thumb-wrap">
				<?php echo $thumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		<?php endif; ?>
		<div class="trove-grouped-product__meta">
			<div class="trove-grouped-product__title"><?php echo $title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<?php if ( $price_html ) : ?>
				<div class="trove-grouped-product__price"><?php echo wp_kses_post( $price_html ); ?></div>
			<?php endif; ?>
		</div>
	</div>
	<?php
	return (string) ob_get_clean();
}
add_filter( 'woocommerce_grouped_product_list_column_label', __NAMESPACE__ . '\\filter_grouped_product_list_column_label', 10, 2 );
