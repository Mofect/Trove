<?php
/**
 * Trove metabox framework bootstrap.
 *
 * @package Trove
 */

namespace Trove\Metabox;

defined( 'ABSPATH' ) || exit;

require_once get_template_directory() . '/inc/metabox/class-registry.php';
require_once get_template_directory() . '/inc/metabox/class-fields.php';
require_once get_template_directory() . '/inc/metabox/class-manager.php';
require_once get_template_directory() . '/inc/metabox/helpers.php';

Manager::init();

require_once get_template_directory() . '/inc/metabox/page-header-colors.php';
require_once get_template_directory() . '/inc/metabox/config/page-settings.php';
require_once get_template_directory() . '/inc/metabox/page-content-spacing.php';
require_once get_template_directory() . '/inc/components/page-banner.php';

add_action(
	'admin_enqueue_scripts',
	static function () {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || 'post' !== $screen->base ) {
			return;
		}

		$dir = \get_template_directory_uri() . '/inc/metabox';

		wp_register_style(
			'trove-metabox-admin',
			$dir . '/css/metabox-admin.css',
			array(),
			TROVE_THEME_VERSION
		);
		wp_enqueue_style( 'trove-metabox-admin' );

		wp_register_style(
			'trove-customizer-alignment-metabox',
			\get_template_directory_uri() . '/inc/customizer/css/customizer-alignment-control.css',
			array( 'trove-metabox-admin' ),
			TROVE_THEME_VERSION
		);
		wp_enqueue_style( 'trove-customizer-alignment-metabox' );

		wp_register_script(
			'trove-metabox-range-number',
			$dir . '/js/metabox-range-number.js',
			array(),
			TROVE_THEME_VERSION,
			true
		);
		wp_enqueue_script( 'trove-metabox-range-number' );

		wp_register_script(
			'trove-metabox-conditional-fields',
			$dir . '/js/metabox-conditional-fields.js',
			array( 'jquery' ),
			TROVE_THEME_VERSION,
			true
		);
		wp_enqueue_script( 'trove-metabox-conditional-fields' );

		wp_enqueue_media();

		wp_register_script(
			'trove-metabox-media-field',
			$dir . '/js/metabox-media-field.js',
			array( 'jquery', 'media-editor' ),
			TROVE_THEME_VERSION,
			true
		);
		wp_enqueue_script( 'trove-metabox-media-field' );
		wp_localize_script(
			'trove-metabox-media-field',
			'troveMbMediaL10n',
			array(
				'title'  => __( 'Choose image', 'trove' ),
				'button' => __( 'Use image', 'trove' ),
			)
		);

		wp_enqueue_style( 'wp-color-picker' );
		wp_register_style(
			'trove-metabox-color-pickers',
			$dir . '/css/metabox-color-pickers.css',
			array( 'wp-color-picker', 'wp-components', 'trove-metabox-admin' ),
			TROVE_THEME_VERSION
		);
		wp_enqueue_style( 'trove-metabox-color-pickers' );

		wp_enqueue_script( 'wp-color-picker' );
		wp_register_script(
			'trove-metabox-color-pickers',
			$dir . '/js/metabox-color-pickers.js',
			array( 'jquery', 'wp-color-picker' ),
			TROVE_THEME_VERSION,
			true
		);
		wp_enqueue_script( 'trove-metabox-color-pickers' );

		wp_enqueue_style( 'wp-components' );
		wp_enqueue_script( 'wp-element' );
		wp_enqueue_script( 'wp-components' );

		wp_register_script(
			'trove-metabox-color-popover',
			$dir . '/js/metabox-color-popover.js',
			array( 'jquery', 'trove-metabox-color-pickers' ),
			TROVE_THEME_VERSION,
			true
		);
		wp_enqueue_script( 'trove-metabox-color-popover' );

		wp_register_script(
			'trove-metabox-gutenberg-color-picker',
			$dir . '/js/metabox-gutenberg-color-picker.js',
			array( 'jquery', 'wp-element', 'wp-components', 'trove-metabox-color-pickers', 'trove-metabox-color-popover' ),
			TROVE_THEME_VERSION,
			true
		);
		wp_enqueue_script( 'trove-metabox-gutenberg-color-picker' );

		// After color picker / popover scripts so tab visibility can sync once DOM is stable.
		wp_register_script(
			'trove-metabox-tabs',
			$dir . '/js/metabox-tabs.js',
			array( 'jquery', 'trove-metabox-gutenberg-color-picker' ),
			TROVE_THEME_VERSION,
			true
		);
		wp_enqueue_script( 'trove-metabox-tabs' );
	},
	20
);

/**
 * Hides the block editor’s bottom “Meta Boxes” strip.
 *
 * WordPress shows this panel when normal, advanced, or side meta boxes exist; with only
 * document-sidebar (side) boxes the strip is often empty but still appears. Set the
 * filter to false if a plugin needs normal/advanced meta boxes in the block editor.
 *
 * @return void
 */
function enqueue_hide_block_editor_bottom_meta_boxes() {
	if ( ! apply_filters( 'trove_hide_block_editor_bottom_meta_boxes', true ) ) {
		return;
	}
	if ( ! wp_style_is( 'wp-edit-post', 'enqueued' ) ) {
		return;
	}
	wp_add_inline_style(
		'wp-edit-post',
		'.edit-post-meta-boxes-main{display:none!important}'
	);
}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\enqueue_hide_block_editor_bottom_meta_boxes', 100 );
