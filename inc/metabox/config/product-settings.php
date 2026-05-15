<?php
/**
 * Product settings metabox (single product layout options per product).
 *
 * @package Trove
 */

namespace Trove\Metabox;

defined( 'ABSPATH' ) || exit;

/**
 * Prepends “Theme default” to product settings select options.
 *
 * @param array<string, string> $options Option value => label (without theme default).
 * @return array<string, string>
 */
function product_settings_select_options( array $options ) {
	return array_merge(
		array( PRODUCT_SETTINGS_THEME_DEFAULT => __( 'Theme default', 'trove' ) ),
		$options
	);
}

/**
 * Registers the Product settings metabox for the product post type.
 *
 * @return void
 */
function register_product_settings_metabox() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	$defaults = product_settings_defaults();

	Registry::register(
		PRODUCT_SETTINGS_METABOX_ID,
		array(
			'title'      => __( 'Product settings', 'trove' ),
			'post_types' => array( 'product' ),
			'context'    => 'normal',
			'priority'   => 'default',
			'fields'     => array(
				array(
					'id'      => 'gallery_type',
					'type'    => 'select',
					'name'    => __( 'Product gallery type', 'trove' ),
					'default' => PRODUCT_SETTINGS_THEME_DEFAULT,
					'tab'     => 'gallery',
					'options' => product_settings_select_options(
						array(
							'stacked' => __( 'Stacked', 'trove' ),
							'slider'  => __( 'Slider', 'trove' ),
						)
					),
				),
				array(
					'id'               => 'gallery_stacked_columns',
					'type'             => 'select',
					'name'             => __( 'Stacked gallery columns (from 2nd image)', 'trove' ),
					'description'      => __( 'Only applies when Product gallery type is Stacked. The first image stays full width; additional images use this column count.', 'trove' ),
					'default'          => PRODUCT_SETTINGS_THEME_DEFAULT,
					'tab'              => 'gallery',
					'only_when_select' => array(
						'field' => 'gallery_type',
						'value' => 'stacked',
					),
					'options'          => product_settings_select_options(
						array(
							'1' => __( '1 column', 'trove' ),
							'2' => __( '2 columns', 'trove' ),
							'3' => __( '3 columns', 'trove' ),
							'4' => __( '4 columns', 'trove' ),
							'5' => __( '5 columns', 'trove' ),
							'6' => __( '6 columns', 'trove' ),
						)
					),
				),
				array(
					'id'               => 'gallery_slider_position',
					'type'             => 'select',
					'name'             => __( 'Image carousel position', 'trove' ),
					'description'      => __( 'Only applies when Product gallery type is Slider.', 'trove' ),
					'default'          => PRODUCT_SETTINGS_THEME_DEFAULT,
					'tab'              => 'gallery',
					'only_when_select' => array(
						'field' => 'gallery_type',
						'value' => 'slider',
					),
					'options'          => product_settings_select_options(
						array(
							'left'   => __( 'Left', 'trove' ),
							'right'  => __( 'Right', 'trove' ),
							'bottom' => __( 'Bottom', 'trove' ),
						)
					),
				),
				array(
					'id'          => 'product_video_url',
					'type'        => 'product_video',
					'name'        => __( 'Product video', 'trove' ),
					'description' => __( 'Upload a video or paste a URL (YouTube, Vimeo, or direct video file).', 'trove' ),
					'default'     => '',
					'tab'         => 'gallery',
				),
				array(
					'id'      => 'tabs_layout',
					'type'    => 'select',
					'name'    => __( 'Product tabs layout', 'trove' ),
					'default' => PRODUCT_SETTINGS_THEME_DEFAULT,
					'tab'     => 'content',
					'options' => product_settings_select_options(
						array(
							'horizontal'  => __( 'Horizontal', 'trove' ),
							'vertical'    => __( 'Vertical', 'trove' ),
							'collapsible' => __( 'Collapsible', 'trove' ),
						)
					),
				),
				array(
					'id'      => 'enable_related_products',
					'type'    => 'switch',
					'name'    => __( 'Enable related products', 'trove' ),
					'default' => $defaults['enable_related_products'],
					'tab'     => 'content',
				),
				array(
					'id'               => 'related_products_count',
					'type'             => 'select',
					'name'             => __( 'Number of related products', 'trove' ),
					'default'          => PRODUCT_SETTINGS_THEME_DEFAULT,
					'tab'              => 'content',
					'only_when_switch' => 'enable_related_products',
					'options'          => product_settings_select_options(
						array(
							'4'  => __( '4 products', 'trove' ),
							'8'  => __( '8 products', 'trove' ),
							'12' => __( '12 products', 'trove' ),
							'16' => __( '16 products', 'trove' ),
							'20' => __( '20 products', 'trove' ),
						)
					),
				),
			),
		)
	);
}

/**
 * Migrate legacy Customizer theme mods when opening a product in admin.
 *
 * @return void
 */
function maybe_migrate_product_settings_on_edit() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'product' !== $screen->post_type || 'post' !== $screen->base ) {
		return;
	}
	global $post;
	if ( ! $post instanceof \WP_Post ) {
		return;
	}
	migrate_product_settings_from_theme_mods( (int) $post->ID );
}

/**
 * Copy legacy Customizer values into product meta on first single-product view.
 *
 * @return void
 */
function maybe_migrate_product_settings_on_frontend() {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}
	$post_id = (int) get_queried_object_id();
	if ( $post_id < 1 ) {
		return;
	}
	migrate_product_settings_from_theme_mods( $post_id );
}

add_action( 'init', __NAMESPACE__ . '\\register_product_settings_metabox', 10 );
add_action( 'load-post.php', __NAMESPACE__ . '\\maybe_migrate_product_settings_on_edit' );
add_action( 'wp', __NAMESPACE__ . '\\maybe_migrate_product_settings_on_frontend', 1 );
