<?php
/**
 * WooCommerce theme integrations (notices, product loop, tabs, related).
 *
 * @package Trove
 */

namespace Trove;

defined( 'ABSPATH' ) || exit;

/**
 * Whether the shop / product taxonomy archives use the WooCommerce filter sidebar (see archive-product.php).
 *
 * @return bool
 */
function is_woo_shop_filter_sidebar_enabled() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return false;
	}
	if ( ! ( is_shop() || is_product_taxonomy() ) ) {
		return false;
	}
	return wp_validate_boolean(
		get_theme_mod(
			'trove_woo_enable_sidebar',
			(bool) \Trove\Customizer\customizer_default( 'trove_woo_enable_sidebar', true )
		)
	);
}

/**
 * Whether woo.js should load AJAX mini-cart sync (single product, shop, product archives).
 *
 * @return bool
 */
function should_enqueue_woo_ajax_cart_assets() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return false;
	}
	if ( function_exists( 'is_product' ) && is_product() ) {
		return true;
	}
	return ( function_exists( 'is_shop' ) && is_shop() )
		|| ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() );
}

/**
 * Whether checkout.css should load (WooCommerce checkout or [woocommerce_checkout] shortcode page).
 *
 * @return bool
 */
function should_enqueue_checkout_styles() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return false;
	}
	if ( function_exists( 'is_checkout' ) && is_checkout() ) {
		if ( function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url( 'order-received' ) ) {
			return false;
		}
		return true;
	}
	if ( ! is_singular() ) {
		return false;
	}
	$post = get_post();
	if ( ! $post instanceof \WP_Post ) {
		return false;
	}
	return has_shortcode( $post->post_content, 'woocommerce_checkout' );
}

/**
 * Whether the current single product page shows reviews (matches single-product-reviews.php).
 *
 * @return bool
 */
function is_single_product_reviews_enabled() {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return false;
	}

	$product_id = get_queried_object_id();
	if ( ! $product_id ) {
		return false;
	}

	$product = wc_get_product( $product_id );
	if ( ! $product ) {
		return false;
	}

	return $product->get_reviews_allowed() && comments_open( $product_id );
}

/**
 * One-time: trove_woo_enable_filter → trove_woo_enable_sidebar.
 *
 * @return void
 */
function migrate_woo_enable_filter_theme_mod() {
	$mods = get_theme_mods();
	if ( ! is_array( $mods ) ) {
		return;
	}
	if ( array_key_exists( 'trove_woo_enable_sidebar', $mods ) ) {
		return;
	}
	if ( ! array_key_exists( 'trove_woo_enable_filter', $mods ) ) {
		return;
	}
	set_theme_mod( 'trove_woo_enable_sidebar', $mods['trove_woo_enable_filter'] );
	remove_theme_mod( 'trove_woo_enable_filter' );
}

add_action( 'after_setup_theme', __NAMESPACE__ . '\\migrate_woo_enable_filter_theme_mod', 5 );

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
 * WooCommerce loop thumbnail <img> class list (includes trove-product-img for hover zoom).
 *
 * @param string $modifier Optional BEM modifier, e.g. `--main` or `--hover`.
 * @return string
 */
function get_loop_product_thumbnail_img_class( $modifier = '' ) {
	$base = 'attachment-woocommerce_thumbnail size-woocommerce_thumbnail trove-product-img';
	if ( is_string( $modifier ) && '' !== $modifier ) {
		$base = 'attachment-woocommerce_thumbnail size-woocommerce_thumbnail trove-product-img trove-product-img' . $modifier;
	}
	return $base;
}

/**
 * Add trove-product-img to thumbnail images that use the WC loop size classes.
 *
 * @param string $html Image HTML.
 * @return string
 */
function add_trove_product_img_class_to_thumbnail_html( $html ) {
	if ( ! is_string( $html ) || '' === $html ) {
		return $html;
	}
	if ( false === strpos( $html, 'attachment-woocommerce_thumbnail' )
		|| false === strpos( $html, 'size-woocommerce_thumbnail' ) ) {
		return $html;
	}
	if ( false !== strpos( $html, 'trove-product-img' ) ) {
		return $html;
	}

	$updated = preg_replace_callback(
		'/<img\b([^>]*)\bclass=(["\'])([^"\']*)\2/i',
		static function ( $matches ) {
			$classes = $matches[3];
			if ( false === strpos( $classes, 'attachment-woocommerce_thumbnail' )
				|| false === strpos( $classes, 'size-woocommerce_thumbnail' )
				|| false !== strpos( $classes, 'trove-product-img' ) ) {
				return $matches[0];
			}
			$new_classes = trim( $classes . ' trove-product-img' );
			return '<img' . $matches[1] . 'class=' . $matches[2] . $new_classes . $matches[2];
		},
		$html
	);

	return is_string( $updated ) ? $updated : $html;
}

/**
 * Open/close markup for the loop product thumbnail wrapper.
 *
 * @param bool $close Whether to return the closing tag.
 * @return string
 */
function get_loop_product_thumbnail_wrapper_tag( $close = false ) {
	return $close ? '</span>' : '<span class="trove-product-thumbnail">';
}

/**
 * Whether product loop thumbnail markup should be wrapped (shop loops only).
 *
 * WooCommerce leaves `name` empty on the main catalog loop; only sub-loops set it (related, etc.).
 *
 * @return bool
 */
function should_wrap_product_loop_thumbnail() {
	if ( is_admin() && ! wp_doing_ajax() ) {
		return false;
	}

	$loop_name = wc_get_loop_prop( 'name' );
	if ( is_string( $loop_name ) && '' !== $loop_name ) {
		return true;
	}

	if ( wc_get_loop_prop( 'is_shortcode' ) ) {
		return true;
	}

	// Main shop / taxonomy archives (loop `name` is empty by default).
	if ( function_exists( 'is_woocommerce' ) && is_woocommerce() && ! is_product() ) {
		return true;
	}

	return false;
}

/**
 * Normalize the product passed into woocommerce_product_get_image filters.
 *
 * @param mixed $product Product object or ID.
 * @return \WC_Product|null
 */
function get_product_from_image_filter( $product ) {
	if ( $product instanceof \WC_Product ) {
		return $product;
	}
	if ( is_numeric( $product ) ) {
		return wc_get_product( (int) $product );
	}
	return null;
}

/**
 * Wrap loop thumbnails in .trove-product-thumbnail; optional second image on hover.
 *
 * @param string     $html    Original thumbnail HTML.
 * @param \WC_Product|int $product Product instance or ID.
 * @return string
 */
function product_loop_thumbnail_with_hover( $html, $product ) {
	if ( ! should_wrap_product_loop_thumbnail() ) {
		return $html;
	}

	$product = get_product_from_image_filter( $product );
	if ( ! $product ) {
		return $html;
	}

	$size     = 'woocommerce_thumbnail';
	$image_id = (int) $product->get_image_id();

	$show_hover = wp_validate_boolean(
		get_theme_mod(
			'trove_woo_show_second_photo_on_hover',
			(bool) \Trove\Customizer\customizer_default( 'trove_woo_show_second_photo_on_hover', true )
		)
	);
	$gallery  = $product->get_gallery_image_ids();
	$hover_id = ( $show_hover && ! empty( $gallery ) ) ? (int) $gallery[0] : 0;

	if ( $hover_id && $hover_id !== $image_id ) {
		$main_img  = wp_get_attachment_image( $image_id, $size, false, array( 'class' => get_loop_product_thumbnail_img_class( '--main' ), 'loading' => 'lazy' ) );
		$hover_img = wp_get_attachment_image( $hover_id, $size, false, array( 'class' => get_loop_product_thumbnail_img_class( '--hover' ), 'loading' => 'lazy' ) );

		if ( $main_img && $hover_img ) {
			return get_loop_product_thumbnail_wrapper_tag() . $main_img . $hover_img . get_loop_product_thumbnail_wrapper_tag( true );
		}
	}

	$img = '';
	if ( $image_id ) {
		$img = wp_get_attachment_image(
			$image_id,
			$size,
			false,
			array(
				'class'   => get_loop_product_thumbnail_img_class(),
				'loading' => 'lazy',
			)
		);
	}

	if ( ! $img ) {
		$img = wc_placeholder_img(
			$size,
			array(
				'class' => 'trove-product-img woocommerce-placeholder wp-post-image',
			)
		);
	}

	if ( ! $img && is_string( $html ) && '' !== $html ) {
		$html = add_trove_product_img_class_to_thumbnail_html( $html );
		return get_loop_product_thumbnail_wrapper_tag() . $html . get_loop_product_thumbnail_wrapper_tag( true );
	}

	if ( ! $img ) {
		return $html;
	}

	return get_loop_product_thumbnail_wrapper_tag() . $img . get_loop_product_thumbnail_wrapper_tag( true );
}

/**
 * Ensure loop thumbnail HTML includes trove-product-img on WC size classes.
 *
 * @param string          $html    Image HTML.
 * @param \WC_Product|int $product Product instance or ID.
 * @return string
 */
function add_trove_product_img_class_to_loop_thumbnail( $html, $product ) {
	if ( ! should_wrap_product_loop_thumbnail() ) {
		return $html;
	}
	return add_trove_product_img_class_to_thumbnail_html( $html );
}
add_filter( 'woocommerce_product_get_image', __NAMESPACE__ . '\\product_loop_thumbnail_with_hover', 20, 2 );
add_filter( 'woocommerce_product_get_image', __NAMESPACE__ . '\\add_trove_product_img_class_to_loop_thumbnail', 25, 2 );

/**
 * WooCommerce → Customizer → Product Images → Thumbnail cropping mode.
 *
 * @return string `1:1`, `custom`, or `uncropped`.
 */
function get_woocommerce_thumbnail_cropping_mode() {
	return (string) get_option( 'woocommerce_thumbnail_cropping', '1:1' );
}

/**
 * CSS aspect-ratio value matching WooCommerce thumbnail cropping (Product Images customizer section).
 *
 * @return string e.g. `1 / 1`, `4 / 3`, or `auto` when uncropped.
 */
function get_woocommerce_product_thumbnail_aspect_ratio_css() {
	$cropping = get_woocommerce_thumbnail_cropping_mode();

	if ( 'uncropped' === $cropping ) {
		return 'auto';
	}

	if ( 'custom' === $cropping ) {
		$width  = max( 1, (float) get_option( 'woocommerce_thumbnail_cropping_custom_width', 4 ) );
		$height = max( 1, (float) get_option( 'woocommerce_thumbnail_cropping_custom_height', 3 ) );
		return $width . ' / ' . $height;
	}

	$parts = array_map( 'floatval', explode( ':', $cropping ) );
	if ( count( $parts ) >= 2 && $parts[0] > 0 && $parts[1] > 0 ) {
		return $parts[0] . ' / ' . $parts[1];
	}

	return '1 / 1';
}

/**
 * Body class for thumbnail cropping (cart/catalog layout hooks).
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function filter_body_class_woocommerce_thumbnail_cropping( $classes ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return $classes;
	}

	$cropping = get_woocommerce_thumbnail_cropping_mode();
	if ( 'uncropped' === $cropping ) {
		$classes[] = 'trove-woo-thumbnail-crop-uncropped';
	} elseif ( 'custom' === $cropping ) {
		$classes[] = 'trove-woo-thumbnail-crop-custom';
	} else {
		$classes[] = 'trove-woo-thumbnail-crop-fixed';
	}

	return $classes;
}
add_filter( 'body_class', __NAMESPACE__ . '\\filter_body_class_woocommerce_thumbnail_cropping' );

/**
 * Add custom extra tabs to single product tabs.
 *
 * @param array<string, array<string, mixed>> $tabs Product tabs.
 * @return array<string, array<string, mixed>>
 */
function add_single_product_extra_tabs( $tabs ) {
	$raw = get_theme_mod(
		'trove_woo_single_product_extra_tabs',
		(string) \Trove\Customizer\customizer_default( 'trove_woo_single_product_extra_tabs', '[]' )
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
		$slug          = 'trove_extra_' . sanitize_title( $row['title'] );
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
add_filter( 'woocommerce_product_tabs', __NAMESPACE__ . '\\add_single_product_extra_tabs', 98 );

/**
 * Control display of related products on single product page.
 *
 * @return void
 */
function control_related_products() {
	if ( ! is_product() ) {
		return;
	}
	if ( ! \Trove\Metabox\get_product_setting_bool( 'enable_related_products' ) ) {
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
	}
}
add_action( 'wp', __NAMESPACE__ . '\\control_related_products' );

/**
 * Per-product related products count.
 *
 * @param array<string, mixed> $args Related products query args.
 * @return array<string, mixed>
 */
function filter_related_products_count( $args ) {
	if ( ! is_product() ) {
		return $args;
	}
	$count = (int) \Trove\Metabox\get_product_setting( 'related_products_count', 0, '4' );
	$total = \Trove\woo_product_carousel_total( 'related' );
	if ( $count > 0 ) {
		$args['posts_per_page'] = max( $count, $total );
	} else {
		$args['posts_per_page'] = $total;
	}
	$args['columns'] = \Trove\woo_product_carousel_visible( 'related' );
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', __NAMESPACE__ . '\\filter_related_products_count' );

/**
 * Replaces the default WooCommerce customer account block icon with a custom SVG.
 *
 * @param string $block_content The block content.
 * @return string
 */
function replace_customer_account_block_icon( $block_content ) {
	$custom_icon = '<svg width="23" height="23" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 21C5 17.134 8.13401 14 12 14C15.866 14 19 17.134 19 21M16 7C16 9.20914 14.2091 11 12 11C9.79086 11 8 9.20914 8 7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

	$block_content = preg_replace(
		'/<svg[^>]*>.*?<\/svg>/s',
		$custom_icon,
		$block_content,
		1
	);

	return $block_content;
}
add_filter( 'render_block_woocommerce/customer-account', __NAMESPACE__ . '\\replace_customer_account_block_icon', 20 );

/**
 * Replaces the default WooCommerce mini-cart block icon with a custom SVG.
 *
 * @param string $block_content The block content.
 * @return string
 */
function replace_mini_cart_block_icon( $block_content ) {
	$custom_icon = '<svg class="wc-block-mini-cart__icon" width="25" height="25" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 11.01V11M16 11.01V11M8 8V7C8 5.93913 8.42143 4.92172 9.17157 4.17157C9.92172 3.42143 10.9391 3 12 3C13.0609 3 14.0783 3.42143 14.8284 4.17157C15.5786 4.92172 16 5.93913 16 7V8M8 8H6.84027C5.80009 8 4.93356 8.79732 4.84718 9.83391L4.18051 17.8339C4.08334 18.9999 5.00352 20 6.1736 20H17.8264C18.9965 20 19.9167 18.9999 19.8195 17.8339L19.1528 9.83391C19.0664 8.79732 18.1999 8 17.1597 8H16M8 8H16" transform="translate(0 1)" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

	$block_content = preg_replace(
		'/<svg[^>]*class="[^"]*wc-block-mini-cart__icon[^"]*"[^>]*>.*?<\/svg>/s',
		$custom_icon,
		$block_content,
		1
	);

	return $block_content;
}
add_filter( 'render_block_woocommerce/mini-cart', __NAMESPACE__ . '\\replace_mini_cart_block_icon', 20 );

/**
 * Shopping-bag SVG shown above the empty mini-cart message.
 *
 * @return string
 */
function get_mini_cart_empty_icon_markup() {
	return '<div class="trove-mini-cart-empty__icon" aria-hidden="true">'
		. '<svg class="trove-mini-cart-empty__icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 492.308 492.308" fill="currentColor" aria-hidden="true">'
		. '<path d="M447.082,125.24H345.361V99.212C345.361,44.51,300.856,0,246.154,0s-99.207,44.51-99.207,99.212v26.029H45.226 L11.75,492.308h468.808L447.082,125.24z M166.639,99.212c0-43.846,35.668-79.519,79.514-79.519 c43.846,0,79.514,35.673,79.514,79.519v26.029H166.639V99.212z M335.514,212.452c13.269,0,20.563,5.894,20.563,7.846 c0,1.952-7.293,7.856-20.563,7.856s-20.563-5.904-20.563-7.856C314.952,218.346,322.245,212.452,335.514,212.452z M156.793,212.452c13.269,0,20.563,5.894,20.563,7.846c0,1.952-7.293,7.856-20.563,7.856s-20.563-5.904-20.563-7.856 C136.231,218.346,143.524,212.452,156.793,212.452z M63.207,144.933h83.74v48.677c-17.845,2.865-30.409,13.341-30.409,26.689 c0,15.702,17.308,27.548,40.255,27.548c22.947,0,40.255-11.846,40.255-27.548c0-13.347-12.564-23.823-30.409-26.689v-48.677 h159.029v48.677c-17.845,2.865-30.409,13.341-30.409,26.689c0,15.702,17.308,27.548,40.255,27.548 c22.947,0,40.255-11.846,40.255-27.548c0-13.347-12.564-23.823-30.409-26.689v-48.677h83.74l29.889,327.683H33.317L63.207,144.933 z"/>'
		. '</svg></div>';
}

/**
 * Prepends the empty-cart icon to the mini-cart drawer empty state.
 *
 * @param string $block_content Block HTML.
 * @return string
 */
function prepend_empty_mini_cart_icon( $block_content ) {
	if ( $block_content === '' || strpos( $block_content, 'trove-mini-cart-empty__icon' ) !== false ) {
		return $block_content;
	}

	$icon = get_mini_cart_empty_icon_markup();

	if ( preg_match( '/(<div class="wc-block-mini-cart__empty-cart-wrapper"[^>]*>)/', $block_content, $matches ) ) {
		return preg_replace( '/' . preg_quote( $matches[0], '/' ) . '/', $matches[0] . $icon, $block_content, 1 );
	}

	if ( preg_match( '/^(<div[^>]*wp-block-woocommerce-empty-mini-cart-contents-block[^>]*>)/', $block_content, $matches ) ) {
		return preg_replace( '/' . preg_quote( $matches[0], '/' ) . '/', $matches[0] . $icon, $block_content, 1 );
	}

	return $icon . $block_content;
}
add_filter( 'render_block_woocommerce/empty-mini-cart-contents-block', __NAMESPACE__ . '\\prepend_empty_mini_cart_icon', 20 );

/**
 * Enqueues JS that re-applies the empty mini-cart icon after block re-renders.
 *
 * @return void
 */
function enqueue_mini_cart_empty_icon_script() {
	if ( is_admin() || ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	wp_enqueue_script(
		'trove-mini-cart-empty-icon',
		TroveTheme::asset_url( 'assets/js/mini-cart-empty-icon.js' ),
		array(),
		TROVE_THEME_VERSION,
		true
	);
	wp_localize_script(
		'trove-mini-cart-empty-icon',
		'troveMiniCartEmpty',
		array(
			'iconHtml' => get_mini_cart_empty_icon_markup(),
		)
	);
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_mini_cart_empty_icon_script', 25 );

/**
 * Opens #order_summary wrapper around classic checkout order review (heading + #order_review).
 *
 * @return void
 */
function checkout_order_summary_open() {
	if ( ! should_enqueue_checkout_styles() ) {
		return;
	}
	echo '<div id="order_summary" class="trove-checkout-order-summary">';
}

/**
 * Closes #order_summary wrapper.
 *
 * @return void
 */
function checkout_order_summary_close() {
	if ( ! should_enqueue_checkout_styles() ) {
		return;
	}
	echo '</div>';
}

add_action( 'woocommerce_checkout_before_order_review_heading', __NAMESPACE__ . '\\checkout_order_summary_open', 1 );
add_action( 'woocommerce_checkout_after_order_review', __NAMESPACE__ . '\\checkout_order_summary_close', 99 );

/**
 * Whether checkout coupon UI is enabled (Customizer + WooCommerce coupons).
 *
 * @return bool
 */
function is_checkout_coupon_enabled() {
	if ( ! wc_coupons_enabled() ) {
		return false;
	}
	return wp_validate_boolean(
		get_theme_mod(
			'trove_woo_cart_enable_coupon_code',
			(bool) \Trove\Customizer\customizer_default( 'trove_woo_cart_enable_coupon_code', true )
		)
	);
}

/**
 * Renders coupon form below the order review table (inside #order_review, before payment).
 *
 * @return void
 */
function render_checkout_coupon_below_order_table() {
	if ( ! should_enqueue_checkout_styles() || ! is_checkout_coupon_enabled() ) {
		return;
	}
	echo '<div class="trove-checkout-coupon">';
	wc_get_template( 'checkout/form-coupon.php', array( 'checkout' => WC()->checkout() ) );
	echo '</div>';
}

/**
 * Moves checkout coupon from top notice bar to below order review table.
 *
 * @return void
 */
function relocate_checkout_coupon_form() {
	remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
	if ( ! should_enqueue_checkout_styles() ) {
		return;
	}
	add_action( 'woocommerce_review_order_before_payment', __NAMESPACE__ . '\\render_checkout_coupon_below_order_table', 10 );
}
add_action( 'wp', __NAMESPACE__ . '\\relocate_checkout_coupon_form', 20 );

/**
 * Moves Stripe express checkout (Google Pay, Link, etc.) to the top of .entry-content.
 *
 * @return void
 */
function relocate_express_checkout_to_entry_content_top() {
	if ( ! should_enqueue_checkout_styles() ) {
		return;
	}
	if ( ! class_exists( '\WC_Stripe_Express_Checkout_Element' ) ) {
		return;
	}
	$stripe_ec = \WC_Stripe_Express_Checkout_Element::instance();
	if ( ! $stripe_ec ) {
		return;
	}
	remove_action( 'woocommerce_checkout_before_customer_details', [ $stripe_ec, 'display_express_checkout_button_html' ], 1 );
	add_action( 'trove_entry_content_top', [ $stripe_ec, 'display_express_checkout_button_html' ], 10 );
}
add_action( 'wp', __NAMESPACE__ . '\\relocate_express_checkout_to_entry_content_top', 25 );
