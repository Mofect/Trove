<?php
/**
 * My Account: recent orders on dashboard and “Items” column on orders list.
 *
 * @package Trove
 */

namespace Trove;

defined( 'ABSPATH' ) || exit;

/**
 * Displays the latest 5 orders on My Account dashboard.
 *
 * @return void
 */
function render_my_account_recent_orders() {
	if ( ! function_exists( 'wc_get_orders' ) || ! is_user_logged_in() ) {
		return;
	}

	$orders = wc_get_orders(
		array(
			'customer_id' => get_current_user_id(),
			'limit'       => 5,
			'orderby'     => 'date',
			'order'       => 'DESC',
			'status'      => array_keys( wc_get_order_statuses() ),
			'return'      => 'objects',
		)
	);

	if ( empty( $orders ) ) {
		return;
	}
	?>
	<div class="trove-my-account-recent-orders">
		<h3><?php esc_html_e( 'Recent orders', 'trove' ); ?></h3>
		<table class="shop_table shop_table_responsive my_account_orders account-orders-table trove-my-account-recent-orders__table">
			<thead>
				<tr>
					<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number">
						<span class="nobr"><?php esc_html_e( 'Order', 'woocommerce' ); ?></span>
					</th>
					<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-items">
						<span class="nobr"><?php esc_html_e( 'Items', 'woocommerce' ); ?></span>
					</th>
					<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date">
						<span class="nobr"><?php esc_html_e( 'Date', 'woocommerce' ); ?></span>
					</th>
					<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status">
						<span class="nobr"><?php esc_html_e( 'Status', 'woocommerce' ); ?></span>
					</th>
					<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-total">
						<span class="nobr"><?php esc_html_e( 'Total', 'woocommerce' ); ?></span>
					</th>
					<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-actions">
						<span class="nobr"><?php esc_html_e( 'Actions', 'woocommerce' ); ?></span>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $orders as $order ) : ?>
					<tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-<?php echo esc_attr( $order->get_status() ); ?> order">
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number" data-title="<?php esc_attr_e( 'Order', 'woocommerce' ); ?>">
							<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
								<?php echo esc_html( '#' . $order->get_order_number() ); ?>
							</a>
						</td>
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-items" data-title="<?php esc_attr_e( 'Items', 'woocommerce' ); ?>">
							<?php
							$line_items = $order->get_items( 'line_item' );
							$thumbs     = array();
							foreach ( $line_items as $line_item ) {
								$product = $line_item->get_product();
								if ( ! $product ) {
									continue;
								}
								$image_id = $product->get_image_id();
								if ( ! $image_id ) {
									continue;
								}
								$product_url = $product->get_permalink();
								$thumb = wp_get_attachment_image(
									$image_id,
									'thumbnail',
									false,
									array(
										'loading' => 'lazy',
										'alt'     => $product->get_name(),
										'style'   => 'width:36px;height:36px;object-fit:cover;border-radius:4px;margin-right:6px;margin-top:6px;',
									)
								);
								if ( $thumb ) {
									if ( $product_url ) {
										$thumbs[] = sprintf(
											'<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
											esc_url( $product_url ),
											$thumb
										);
									} else {
										$thumbs[] = $thumb;
									}
								}
								if ( count( $thumbs ) >= 4 ) {
									break;
								}
							}
							if ( ! empty( $thumbs ) ) {
								echo wp_kses_post( implode( '', $thumbs ) );
							} else {
								echo '&mdash;';
							}
							?>
						</td>
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date" data-title="<?php esc_attr_e( 'Date', 'woocommerce' ); ?>">
							<time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>">
								<?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?>
							</time>
						</td>
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status" data-title="<?php esc_attr_e( 'Status', 'woocommerce' ); ?>">
							<span><?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></span>
						</td>
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total" data-title="<?php esc_attr_e( 'Total', 'woocommerce' ); ?>">
							<?php echo wp_kses_post( $order->get_formatted_order_total() ); ?>
						</td>
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-actions" data-title="<?php esc_attr_e( 'Actions', 'woocommerce' ); ?>">
							<?php
							$actions = wc_get_account_orders_actions( $order );
							if ( ! empty( $actions ) ) :
								foreach ( $actions as $key => $action ) :
									?>
									<a href="<?php echo esc_url( $action['url'] ); ?>" class="woocommerce-button button <?php echo esc_attr( sanitize_html_class( $key ) ); ?>">
										<?php echo esc_html( $action['name'] ); ?>
									</a>
									<?php
								endforeach;
							endif;
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
}
add_action( 'woocommerce_account_dashboard', __NAMESPACE__ . '\\render_my_account_recent_orders', 30 );

/**
 * Adds an "Items" column to My Account orders table.
 *
 * @param array<string, string> $columns Existing order table columns.
 * @return array<string, string>
 */
function my_account_orders_add_items_column( $columns ) {
	if ( ! is_array( $columns ) ) {
		return $columns;
	}

	$updated = array();
	foreach ( $columns as $key => $label ) {
		$updated[ $key ] = $label;
		if ( 'order-number' === $key ) {
			$updated['order-items'] = __( 'Items', 'woocommerce' );
		}
	}

	return $updated;
}
add_filter( 'woocommerce_account_orders_columns', __NAMESPACE__ . '\\my_account_orders_add_items_column' );

/**
 * Renders product thumbnails in My Account orders "Items" column.
 *
 * @param \WC_Order $order WooCommerce order instance.
 * @return void
 */
function my_account_orders_render_items_column( $order ) {
	if ( ! $order instanceof \WC_Order ) {
		echo '&mdash;';
		return;
	}

	$line_items = $order->get_items( 'line_item' );
	$thumbs     = array();

	foreach ( $line_items as $line_item ) {
		$product = $line_item->get_product();
		if ( ! $product ) {
			continue;
		}
		$image_id = $product->get_image_id();
		if ( ! $image_id ) {
			continue;
		}
		$product_url = $product->get_permalink();
		$thumb = wp_get_attachment_image(
			$image_id,
			'thumbnail',
			false,
			array(
				'class'   => 'trove-my-account-order-item-thumb',
				'loading' => 'lazy',
				'alt'     => $product->get_name(),
			)
		);
		if ( $thumb ) {
			if ( $product_url ) {
				$thumbs[] = sprintf(
					'<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
					esc_url( $product_url ),
					$thumb
				);
			} else {
				$thumbs[] = $thumb;
			}
		}
		if ( count( $thumbs ) >= 4 ) {
			break;
		}
	}

	if ( empty( $thumbs ) ) {
		echo '&mdash;';
		return;
	}

	echo wp_kses_post( implode( '', $thumbs ) );
}
add_action( 'woocommerce_my_account_my_orders_column_order-items', __NAMESPACE__ . '\\my_account_orders_render_items_column' );
