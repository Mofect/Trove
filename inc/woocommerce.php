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
 * Replace default product thumbnail with hover image swap markup.
 *
 * @param string     $html  Original thumbnail HTML.
 * @param int        $post_id Product ID.
 * @return string
 */
function product_loop_thumbnail_with_hover( $html, $post_id ) {
	$enabled = wp_validate_boolean(
		get_theme_mod(
			'trove_woo_show_second_photo_on_hover',
			(bool) \Trove\Customizer\customizer_default( 'trove_woo_show_second_photo_on_hover', true )
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
add_filter( 'woocommerce_product_get_image', __NAMESPACE__ . '\\product_loop_thumbnail_with_hover', 20, 2 );

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
