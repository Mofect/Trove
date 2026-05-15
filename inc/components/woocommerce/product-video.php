<?php
/**
 * Per-product video on single product (Product settings metabox).
 *
 * @package Trove
 */

namespace Trove;

defined( 'ABSPATH' ) || exit;

/**
 * @param int $post_id Product ID (0 = current).
 * @return string Video URL or empty.
 */
function get_product_video_url( $post_id = 0 ) {
	$url = (string) \Trove\Metabox\get_product_setting( 'product_video_url', (int) $post_id, '' );
	return '' !== $url ? esc_url( $url ) : '';
}

/**
 * @param string $url Video URL.
 * @return string HTML or empty.
 */
function render_product_video_markup( $url ) {
	$url = esc_url( $url );
	if ( '' === $url ) {
		return '';
	}

	$oembed = wp_oembed_get( $url, array( 'width' => 1280 ) );
	if ( $oembed ) {
		return '<div class="trove-product-video__embed">' . $oembed . '</div>';
	}

	$mime = wp_check_filetype( $url );
	$type = isset( $mime['type'] ) && is_string( $mime['type'] ) ? $mime['type'] : 'video/mp4';
	if ( 0 !== strpos( $type, 'video/' ) ) {
		$type = 'video/mp4';
	}

	return sprintf(
		'<video class="trove-product-video__player" controls playsinline preload="metadata" src="%1$s"><source src="%1$s" type="%2$s" /></video>',
		$url,
		esc_attr( $type )
	);
}

/**
 * @return void
 */
function output_single_product_video() {
	if ( ! is_product() ) {
		return;
	}

	$url = get_product_video_url();
	if ( '' === $url ) {
		return;
	}

	$markup = render_product_video_markup( $url );
	if ( '' === $markup ) {
		return;
	}

	echo '<div class="trove-product-video">';
	echo $markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- oembed or escaped video tag.
	echo '</div>';
}

add_action( 'woocommerce_before_single_product_summary', __NAMESPACE__ . '\\output_single_product_video', 19 );
