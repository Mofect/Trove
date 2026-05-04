<?php
/**
 * Theme hooks.
 *
 * @package Trove
 */

namespace Trove;

defined( 'ABSPATH' ) || exit;

/**
 * Loads the header template part for the current Customizer layout.
 *
 * @return void
 */
function render_header() {
	$layout = get_theme_mod( 'trove_header_layout', (string) \Trove\Customizer\trove_customizer_default( 'trove_header_layout', 'horizontal-left-logo' ) );
	$layout = is_string( $layout ) ? sanitize_key( $layout ) : 'horizontal-left-logo';

	if ( 'vertical-centered-logo' === $layout || 'vertical-left-logo' === $layout ) {
		get_template_part( 'template-parts/header-vertical' );
	} else {
		get_template_part( 'template-parts/header-horizontal' );
	}
}
add_action( 'trove_header', __NAMESPACE__ . '\\render_header' );

/**
 * Renders the announcement bar (Customizer lines) above the main header.
 *
 * @return void
 */
function render_announcement_bar() {
	if ( ! is_announcement_bar_enabled() ) {
		return;
	}
	$lines = get_announcement_lines();
	if ( empty( $lines ) ) {
		return;
	}
	$n        = count( $lines );
	$nav_hide = $n <= 1;
	$anim     = get_announcement_bar_animation();
	$autoplay = get_announcement_bar_autoplay();
	$interval = get_announcement_bar_autoplay_interval_seconds();
	$classes  = 'trove-announcement-bar';
	if ( 'fade' === $anim ) {
		$classes .= ' trove-announcement-bar--fade';
	}
	?>
	<div
		class="<?php echo esc_attr( $classes ); ?>"
		role="region"
		aria-label="<?php esc_attr_e( 'Announcement', 'trove' ); ?>"
		data-trove-announcement-carousel
		data-slide-count="<?php echo esc_attr( (string) $n ); ?>"
		data-animation="<?php echo esc_attr( $anim ); ?>"
		data-autoplay="<?php echo esc_attr( $autoplay ? 'true' : 'false' ); ?>"
		data-autoplay-interval-ms="<?php echo esc_attr( (string) ( $interval * 1000 ) ); ?>"
		style="<?php echo esc_attr( '--trove-announcement-total: ' . (int) $n . '; --trove-announcement-index: 0;' ); ?>"
		tabindex="0"
	>
		<div class="trove-announcement-bar__inner">
			<button
				type="button"
				class="trove-announcement-bar__nav trove-announcement-bar__nav--prev"
				aria-label="<?php esc_attr_e( 'Previous announcement', 'trove' ); ?>"
				<?php echo $nav_hide ? 'hidden' : ''; ?>
			>
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</button>
			<div class="trove-announcement-bar__viewport">
				<div class="trove-announcement-bar__track">
					<?php
					$i = 0;
					foreach ( $lines as $line ) {
						$active = 0 === $i;
						$slide_classes = 'trove-announcement-bar__slide';
						if ( 'fade' === $anim && $active ) {
							$slide_classes .= ' is-active';
						}
						echo '<div class="' . esc_attr( $slide_classes ) . '" aria-hidden="' . esc_attr( $active ? 'false' : 'true' ) . '">' . esc_html( $line ) . '</div>';
						++$i;
					}
					?>
				</div>
			</div>
			<button
				type="button"
				class="trove-announcement-bar__nav trove-announcement-bar__nav--next"
				aria-label="<?php esc_attr_e( 'Next announcement', 'trove' ); ?>"
				<?php echo $nav_hide ? 'hidden' : ''; ?>
			>
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</button>
		</div>
	</div>
	<?php
}
add_action( 'trove_announcement_bar', __NAMESPACE__ . '\\render_announcement_bar' );

/**
 * Front-end script for announcement carousel (only when lines exist).
 *
 * @return void
 */
function enqueue_announcement_carousel_script() {
	if ( is_customize_preview() ) {
		wp_enqueue_script(
			'trove-announcement-carousel',
			\Trove\TroveTheme::asset_url( 'assets/js/announcement-carousel.js' ),
			array(),
			defined( 'TROVE_THEME_VERSION' ) ? TROVE_THEME_VERSION : '1.0.0',
			true
		);
		return;
	}
	if ( ! is_announcement_bar_enabled() || empty( get_announcement_lines() ) ) {
		return;
	}
	wp_enqueue_script(
		'trove-announcement-carousel',
		\Trove\TroveTheme::asset_url( 'assets/js/announcement-carousel.js' ),
		array(),
		defined( 'TROVE_THEME_VERSION' ) ? TROVE_THEME_VERSION : '1.0.0',
		true
	);
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_announcement_carousel_script', 20 );

/**
 * Renders the theme footer markup.
 *
 * @return void
 */
function render_footer() {
	$footer_layout = get_theme_mod( 'trove_footer_layout', (string) \Trove\Customizer\trove_customizer_default( 'trove_footer_layout', 'footer-1' ) );
	$footer_classes   = array( 'site-footer', 'light-scheme' );
	if ( wp_validate_boolean( get_theme_mod( 'trove_footer_top_border', (bool) \Trove\Customizer\trove_customizer_default( 'trove_footer_top_border', true ) ) ) ) {
		$footer_classes[] = 'trove-footer-has-top-border';
	}

	?>
	<footer class="<?php echo esc_attr( implode( ' ', $footer_classes ) ); ?>" role="contentinfo">
		<?php get_template_part( 'template-parts/' . $footer_layout, null ); ?>
	</footer>
	<?php
}
add_action( 'trove_footer', __NAMESPACE__ . '\\render_footer' );

/**
 * Blog posts loop (index + archives). Layout comes from Customizer → Blog Posts → Layout.
 *
 * @return void
 */
function render_blog_loop() {
	$layout          = is_blog_listing_view() ? get_blog_layout() : 'list';
	$pagination_type = is_blog_listing_view() ? get_blog_pagination_type() : 'paged';
	global $wp_query;
	$wp_max        = isset( $wp_query ) ? (int) $wp_query->max_num_pages : 0;
	$had_posts     = isset( $wp_query ) && $wp_query->post_count > 0;
	$paged_current = get_blog_list_paged();
	$use_infinite  = $had_posts && 'infinite_scroll' === $pagination_type && $wp_max > 1;
	$next_link     = $use_infinite ? get_next_posts_page_link() : '';

	$pagination_args = array(
		'mid_size'  => 2,
		'prev_text' => __( 'Previous', 'trove' ),
		'next_text' => __( 'Next', 'trove' ),
	);
	?>
	<div class="trove-blog-loop trove-blog-loop--<?php echo esc_attr( $layout ); ?>"
	<?php
	if ( $use_infinite ) {
		echo ' data-trove-infinite="1"';
		echo ' data-trove-max-pages="' . esc_attr( (string) $wp_max ) . '"';
		echo ' data-trove-current-page="' . esc_attr( (string) $paged_current ) . '"';
		echo ' data-trove-next-url="' . ( $next_link ? esc_url( $next_link ) : '' ) . '"';
	}
	?>
	>
		<?php
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				get_template_part( 'template-parts/content', get_post_type() );
			}
		} else {
			get_template_part( 'template-parts/content', 'none' );
		}
		?>
	</div>
	<?php
	if ( $had_posts ) {
		if ( $use_infinite && $next_link ) {
			echo '<div class="trove-blog-infinite-sentinel" id="trove-blog-infinite-sentinel" aria-hidden="true"></div>';
			echo '<p class="trove-blog-infinite-status screen-reader-text" role="status" aria-live="polite"></p>';
		} else {
			the_posts_pagination( $pagination_args );
		}
	}
}
add_action( 'trove_blog_loop', __NAMESPACE__ . '\\render_blog_loop' );

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

/**
 * Suppress the global “X has been added to your cart” success bar; the block mini-cart drawer is used instead.
 *
 * @param string     $message  Message HTML.
 * @param int|array  $products Product ids.
 * @param bool       $show_qty Whether quantities are shown.
 * @return string Empty string skips {@see wc_add_notice()} for this event.
 */
function suppress_wc_add_to_cart_success_message_html( $message, $products, $show_qty ) {
	return '';
}
add_filter( 'wc_add_to_cart_message_html', __NAMESPACE__ . '\\suppress_wc_add_to_cart_success_message_html', 10, 3 );

/**
 * Suppress known false-positive variation error notice.
 *
 * On some AJAX variation add-to-cart flows, Woo may enqueue:
 * "Please choose product options by visiting …" even though add-to-cart succeeds.
 * Prevent storing this message in notices/session so it won't flash on refresh.
 *
 * @param string $message Error message text.
 * @return string Filtered message.
 */
function suppress_false_variation_choose_options_error_notice( $message ) {
	$text = is_string( $message ) ? wp_strip_all_tags( $message ) : '';
	if ( '' !== $text && false !== stripos( $text, 'Please choose product options by visiting' ) ) {
		return '';
	}
	return $message;
}
add_filter( 'woocommerce_add_error', __NAMESPACE__ . '\\suppress_false_variation_choose_options_error_notice', 10, 1 );

/**
 * Replace default product thumbnail with hover image swap markup.
 *
 * @param string     $html  Original thumbnail HTML.
 * @param int        $post_id Product ID.
 * @return string
 */
function trove_product_loop_thumbnail_with_hover( $html, $post_id ) {
	$enabled = wp_validate_boolean(
		get_theme_mod(
			'trove_woo_show_second_photo_on_hover',
			(bool) \Trove\Customizer\trove_customizer_default( 'trove_woo_show_second_photo_on_hover', true )
		)
	);
	if ( ! $enabled ) {
		return $html;
	}

	$product = wc_get_product( $post_id );
	if ( ! $product ) {
		return $html;
	}

	$image_id  = $product->get_image_id();
	$gallery   = $product->get_gallery_image_ids();
	$hover_id  = ! empty( $gallery ) ? $gallery[0] : 0;

	if ( ! $hover_id ) {
		return $html;
	}

	$size      = 'woocommerce_thumbnail';
	$main_img  = wp_get_attachment_image( $image_id, $size, false, array( 'class' => 'trove-product-img trove-product-img--main', 'loading' => 'lazy' ) );
	$hover_img = wp_get_attachment_image( $hover_id, $size, false, array( 'class' => 'trove-product-img trove-product-img--hover', 'loading' => 'lazy' ) );

	if ( ! $main_img || ! $hover_img ) {
		return $html;
	}

	return '<span class="trove-product-thumbnail">' . $main_img . $hover_img . '</span>';
}
add_filter( 'woocommerce_product_get_image', __NAMESPACE__ . '\\trove_product_loop_thumbnail_with_hover', 20, 2 );

/**
 * Add custom extra tabs to single product tabs.
 *
 * @param array<string, array<string, mixed>> $tabs Product tabs.
 * @return array<string, array<string, mixed>>
 */
function trove_add_single_product_extra_tabs( $tabs ) {
	$raw = get_theme_mod(
		'trove_woo_single_product_extra_tabs',
		(string) \Trove\Customizer\trove_customizer_default( 'trove_woo_single_product_extra_tabs', '[]' )
	);
	$rows = json_decode( $raw, true );
	if ( ! is_array( $rows ) || empty( $rows ) ) {
		return $tabs;
	}

	$priority = 25;
	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) || empty( $row['title'] ) ) {
			continue;
		}
		$slug = 'trove_extra_' . sanitize_title( $row['title'] );
		$tabs[ $slug ] = array(
			'title'    => sanitize_text_field( $row['title'] ),
			'priority' => $priority,
			'callback' => function () use ( $row ) {
				echo wp_kses_post( $row['content'] );
			},
		);
		$priority += 5;
	}

	return $tabs;
}
add_filter( 'woocommerce_product_tabs', __NAMESPACE__ . '\\trove_add_single_product_extra_tabs', 98 );

/**
 * Control display of related products on single product page.
 *
 * @return void
 */
function trove_control_related_products() {
	$enabled = wp_validate_boolean(
		get_theme_mod(
			'trove_woo_single_product_enable_related_products',
			(bool) \Trove\Customizer\trove_customizer_default( 'trove_woo_single_product_enable_related_products', true )
		)
	);
	if ( ! $enabled ) {
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
	}
}
add_action( 'wp', __NAMESPACE__ . '\\trove_control_related_products' );

/**
 * Check whether lazy loading for images is enabled.
 *
 * @return bool
 */
function is_lazy_load_images_enabled() {
	return wp_validate_boolean(
		get_theme_mod(
			'trove_perf_lazy_load_images',
			(bool) \Trove\Customizer\trove_customizer_default( 'trove_perf_lazy_load_images', true )
		)
	);
}

/**
 * Force lazy loading on all images by injecting loading="lazy" into <img> tags.
 *
 * @param string $html Image HTML.
 * @return string
 */
function filter_force_lazy_loading_on_images( $html ) {
	if ( ! is_lazy_load_images_enabled() ) {
		return $html;
	}
	if ( ! is_string( $html ) || false === strpos( $html, '<img' ) ) {
		return $html;
	}
	// Add or replace loading attribute with "lazy" on all img tags.
	$html = preg_replace( '/<img([^>]*?)\s+loading=["\'][^"\']*["\']([^>]*?)>/i', '<img\1 loading="lazy"\2>', $html );
	$html = preg_replace( '/<img((?![^>]*\sloading=)[^>]*)>/i', '<img\1 loading="lazy">', $html );
	return $html;
}
add_filter( 'wp_get_attachment_image', __NAMESPACE__ . '\\filter_force_lazy_loading_on_images', 999 );
add_filter( 'the_content', __NAMESPACE__ . '\\filter_force_lazy_loading_on_images', 999 );
add_filter( 'post_thumbnail_html', __NAMESPACE__ . '\\filter_force_lazy_loading_on_images', 999 );
add_filter( 'woocommerce_product_get_image', __NAMESPACE__ . '\\filter_force_lazy_loading_on_images', 999 );
