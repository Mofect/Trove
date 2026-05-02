<?php
/**
 * Product reviews: optional photo uploads, stored as attachment IDs in comment meta `trove_review_media`.
 *
 * @package Trove
 */

namespace Trove;

defined( 'ABSPATH' ) || exit;

/** Comment meta key storing JSON array of attachment IDs. */
const REVIEW_MEDIA_META_KEY = 'trove_review_media';

/** Max images per review. */
const REVIEW_MEDIA_MAX_FILES = 6;

/**
 * Adds multipart encoding on the review form (core omits enctype).
 *
 * @return void
 */
function enqueue_review_media_scripts() {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}
	wp_enqueue_script(
		'trove-review-media',
		get_template_directory_uri() . '/assets/js/review-media.js',
		array(),
		TROVE_THEME_VERSION,
		true
	);
	wp_localize_script(
		'trove-review-media',
		'troveReviewMedia',
		array(
			'i18n' => array(
				'close'    => __( 'Close', 'trove' ),
				'lightbox' => __( 'Enlarged review image', 'trove' ),
				'remove'   => __( 'Remove selected photos', 'trove' ),
				'more'     => __( '+%d more', 'trove' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_review_media_scripts' );

/**
 * Wraps submit row with “Add photo” control (file input + label).
 *
 * @param array<string, mixed> $args Comment form args.
 * @return array<string, mixed>
 */
function filter_review_comment_form_args( $args ) {
	if ( ! is_array( $args ) ) {
		return $args;
	}

	$label = esc_html__( 'Add photo', 'trove' );

	// Photo-in-frame icon (fill follows button text color via currentColor).
	$icon = '<svg class="trove-review-add-photo__icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 92 92" fill="currentColor" aria-hidden="true" focusable="false">'
		. '<path d="M88,11H4c-2.2,0-4,1.8-4,4v62c0,2.2,1.8,4,4,4h84c2.2,0,4-1.8,4-4V15C92,12.8,90.2,11,88,11z M84,19v33.5 l-6.7-5.6c-1.5-1.2-3.6-1.2-5.1,0l-10.8,9L36.3,34.1c-1.5-1.3-3.6-1.3-5.1-0.1L8,52.7V19H84z M8,73V63l25.6-20.7l25.1,21.8 c1.5,1.3,3.7,1.3,5.2,0.1l10.8-9L84,63v10H8z M52.1,32.6c0-4,3.3-7.3,7.4-7.3c4.1,0,7.4,3.3,7.4,7.3c0,4-3.3,7.3-7.4,7.3 C55.4,39.9,52.1,36.6,52.1,32.6z"/>'
		. '</svg>';

	$args['submit_field'] = '<p class="form-submit trove-review-form-actions">'
		. '<span class="trove-review-form-actions__buttons">'
		. '%1$s'
		. '<span class="trove-review-add-photo-field">'
		. '<label for="trove_review_media_input" class="button trove-review-add-photo">'
		. $icon
		. '<span class="trove-review-add-photo__text" data-default="' . esc_attr( $label ) . '">' . $label . '</span>'
		. '</label>'
		. '<input id="trove_review_media_input" type="file" name="trove_review_media[]" accept="image/jpeg,image/png,image/gif,image/webp" multiple class="trove-review-media-input" />'
		. '<button type="button" class="trove-review-add-photo-clear" hidden aria-label="' . esc_attr__( 'Remove selected photos', 'trove' ) . '">'
		. '<span class="trove-review-add-photo-clear__icon" aria-hidden="true">&times;</span>'
		. '</button>'
		. '</span>'
		. '</span>'
		. '%2$s'
		. '</p>';

	return $args;
}
add_filter( 'woocommerce_product_review_comment_form_args', __NAMESPACE__ . '\\filter_review_comment_form_args', 20 );

/**
 * Processes uploaded images after a product review comment is saved.
 *
 * @param int        $comment_id       Comment ID.
 * @param int|string $comment_approved Approval status.
 * @param array<string, mixed> $commentdata Comment data.
 * @return void
 */
function save_review_media_on_comment_post( $comment_id, $comment_approved, $commentdata ) {
	if ( empty( $_FILES['trove_review_media'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Core comment POST handles nonce.
		return;
	}

	if ( empty( $commentdata['comment_type'] ) || 'review' !== $commentdata['comment_type'] ) {
		return;
	}

	$post_id = isset( $commentdata['comment_post_ID'] ) ? (int) $commentdata['comment_post_ID'] : 0;
	if ( ! $post_id || 'product' !== get_post_type( $post_id ) ) {
		return;
	}

	$files = $_FILES['trove_review_media']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Raw files passed to wp_handle_upload.

	if ( empty( $files['name'] ) ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$names     = $files['name'];
	$tmp_names = $files['tmp_name'];
	$errors    = $files['error'];
	$sizes     = $files['size'];
	$types     = isset( $files['type'] ) ? $files['type'] : array();

	if ( ! is_array( $names ) ) {
		$names     = array( $names );
		$tmp_names = array( $tmp_names );
		$errors    = array( $errors );
		$sizes     = array( $sizes );
		$types     = array( $types );
	}

	$max_upload       = wp_max_upload_size();
	$attachment_ids = array();
	$count          = min( count( $names ), REVIEW_MEDIA_MAX_FILES );

	for ( $i = 0; $i < $count; $i++ ) {
		if ( empty( $names[ $i ] ) || UPLOAD_ERR_OK !== (int) $errors[ $i ] ) {
			continue;
		}
		if ( ! empty( $sizes[ $i ] ) && (int) $sizes[ $i ] > $max_upload ) {
			continue;
		}

		$file = array(
			'name'     => $names[ $i ],
			'type'     => isset( $types[ $i ] ) ? $types[ $i ] : '',
			'tmp_name' => $tmp_names[ $i ],
			'error'    => $errors[ $i ],
			'size'     => isset( $sizes[ $i ] ) ? $sizes[ $i ] : 0,
		);

		$check = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'] );
		if ( empty( $check['ext'] ) || empty( $check['type'] ) || 0 !== strpos( (string) $check['type'], 'image/' ) ) {
			continue;
		}

		$upload = wp_handle_upload(
			$file,
			array(
				'test_form' => false,
				'mimes'     => array(
					'jpg|jpeg|jpe' => 'image/jpeg',
					'gif'          => 'image/gif',
					'png'          => 'image/png',
					'webp'         => 'image/webp',
				),
			)
		);

		if ( isset( $upload['error'] ) || empty( $upload['file'] ) ) {
			continue;
		}

		$filetype = wp_check_filetype( basename( $upload['file'] ), null );
		$attach_id = wp_insert_attachment(
			array(
				'post_mime_type' => $filetype['type'],
				'post_title'     => sanitize_file_name( pathinfo( $file['name'], PATHINFO_FILENAME ) ),
				'post_content'   => '',
				'post_status'    => 'inherit',
				'post_parent'    => $post_id,
			),
			$upload['file'],
			$post_id
		);

		if ( is_wp_error( $attach_id ) || ! $attach_id ) {
			continue;
		}

		$meta = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
		wp_update_attachment_metadata( $attach_id, $meta );

		$attachment_ids[] = (int) $attach_id;
	}

	if ( ! empty( $attachment_ids ) ) {
		update_comment_meta( $comment_id, REVIEW_MEDIA_META_KEY, array_map( 'absint', array_values( array_unique( $attachment_ids ) ) ) );
	}
}
add_action( 'comment_post', __NAMESPACE__ . '\\save_review_media_on_comment_post', 10, 3 );

/**
 * Attachment IDs stored for a review comment.
 *
 * @param int $comment_id Comment ID.
 * @return int[]
 */
function get_review_media_attachment_ids( $comment_id ) {
	$comment_id = (int) $comment_id;
	if ( $comment_id < 1 ) {
		return array();
	}
	$raw = get_comment_meta( $comment_id, REVIEW_MEDIA_META_KEY, true );
	if ( ! is_array( $raw ) ) {
		return array();
	}
	$ids = array_filter( array_map( 'absint', $raw ) );
	return array_values( array_unique( $ids ) );
}

/**
 * Replace core review text output with text + optional images (images on the right via CSS).
 *
 * @param \WP_Comment|null $comment Comment.
 * @return void
 */
function display_review_comment_text_and_media( $comment ) {
	if ( ! $comment instanceof \WP_Comment ) {
		return;
	}

	$ids = get_review_media_attachment_ids( $comment->comment_ID );

	if ( empty( $ids ) ) {
		echo '<div class="description">';
		comment_text();
		echo '</div>';
		return;
	}

	// WooCommerce registers `woocommerce_gallery_thumbnail`; falls back if missing.
	$thumb_size = apply_filters( 'trove_review_media_thumbnail_size', 'woocommerce_gallery_thumbnail' );

	echo '<div class="trove-review-body">';
	echo '<div class="description">';
	comment_text();
	echo '</div>';

	echo '<div class="trove-review-media" role="group" aria-label="' . esc_attr__( 'Review photos', 'trove' ) . '">';

	foreach ( $ids as $aid ) {
		if ( ! wp_attachment_is_image( $aid ) ) {
			continue;
		}
		$full = wp_get_attachment_image_url( $aid, 'full' );
		if ( ! $full ) {
			continue;
		}
		$alt = (string) get_post_meta( $aid, '_wp_attachment_image_alt', true );
		if ( '' === $alt ) {
			$alt = __( 'Review photo', 'trove' );
		}
		echo '<figure class="trove-review-media__item">';
		echo '<a class="trove-review-media__link" href="' . esc_url( $full ) . '" target="_blank" rel="noopener noreferrer">';
		echo wp_get_attachment_image(
			$aid,
			$thumb_size,
			false,
			array(
				'class'   => 'trove-review-media__img',
				'loading' => 'lazy',
				'alt'     => $alt,
			)
		);
		echo '</a>';
		echo '</figure>';
	}

	echo '</div>';
	echo '</div>';
}

/**
 * Registers custom review body markup on single product pages.
 *
 * @return void
 */
function register_review_comment_text_display() {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}
	remove_action( 'woocommerce_review_comment_text', 'woocommerce_review_display_comment_text', 10 );
	add_action( 'woocommerce_review_comment_text', __NAMESPACE__ . '\\display_review_comment_text_and_media', 10, 1 );
}
add_action( 'wp', __NAMESPACE__ . '\\register_review_comment_text_display', 20 );
