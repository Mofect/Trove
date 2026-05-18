<?php
/**
 * Recommended plugins for TGMPA.
 *
 * @package Trove
 */

namespace Trove\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Base URI for TroveWP admin plugin images.
 *
 * @return string
 */
function get_trove_plugins_img_uri() {
	return \TROVE_URI . '/inc/admin/img/plugins/';
}

/**
 * Plugin icon URL from WordPress.org (alias).
 *
 * @param string $slug Plugin slug.
 * @return string
 */
function get_trove_plugin_icon_url( $slug ) {
	return get_trove_plugin_image_url( $slug );
}

/**
 * Plugin icon URL from a local asset or WordPress.org.
 *
 * @param string $slug     Plugin slug.
 * @param string $filename Optional local filename under inc/admin/img/plugins/.
 * @return string
 */
function get_trove_plugin_image_url( $slug, $filename = '' ) {
	if ( $filename ) {
		$path = \TROVE_DIR . '/inc/admin/img/plugins/' . $filename;
		if ( file_exists( $path ) ) {
			return get_trove_plugins_img_uri() . $filename;
		}
	}

	return sprintf( 'https://ps.w.org/%s/assets/icon-256x256.png', rawurlencode( $slug ) );
}

/**
 * Bundled plugins shipped with the theme (zip in inc/plugins/).
 *
 * @return array<string, array<string, string>>
 */
function get_trove_bundled_plugins() {
	$bundled = array(
		'trove-enhancer' => array(
			'name'    => __( 'Trove Enhancer', 'trove' ),
			'slug'    => 'trove-enhancer',
			'version' => '1.0.0',
			'file'    => 'trove-enhancer.zip',
		),
	);

	/**
	 * Filters bundled plugin metadata used for TGMPA sources.
	 *
	 * @param array<string, array<string, string>> $bundled Bundled plugin definitions.
	 */
	return apply_filters( 'trove_bundled_plugins', $bundled );
}

/**
 * Recommended and required plugins for TroveWP.
 *
 * @return array<string, array<string, mixed>>
 */
function get_trove_recommended_plugins() {
	$bundled_plugins = get_trove_bundled_plugins();

	$plugins = array(
		'woocommerce' => array(
			'name'      => __( 'WooCommerce', 'trove' ),
			'slug'      => 'woocommerce',
			'required'  => false,
			'desc'      => __( 'The ecommerce toolkit for WordPress. TroveWP is built for WooCommerce stores.', 'trove' ),
			'image_url' => get_trove_plugin_image_url( 'woocommerce' ),
		),
		'elementor'   => array(
			'name'      => __( 'Elementor', 'trove' ),
			'slug'      => 'elementor',
			'required'  => false,
			'desc'      => __( 'Elementor is the primary page builder we recommend for designing your storefront and landing pages.', 'trove' ),
			'image_url' => get_trove_plugin_image_url( 'elementor', 'elementor.svg' ),
		),
		'contact-form-7' => array(
			'name'      => __( 'Contact Form 7', 'trove' ),
			'slug'      => 'contact-form-7',
			'required'  => false,
			'desc'      => __( 'Just another contact form plugin. Simple but flexible.', 'trove' ),
			'image_url' => get_trove_plugin_image_url( 'contact-form-7' ),
		),
		'mailchimp-for-wp' => array(
			'name'      => __( 'Mailchimp for WordPress', 'trove' ),
			'slug'      => 'mailchimp-for-wp',
			'required'  => false,
			'desc'      => __( 'Create good-looking opt-in forms and integrate Mailchimp with forms on your site.', 'trove' ),
			'image_url' => get_trove_plugin_image_url( 'mailchimp-for-wp' ),
		),
		'yith-woocommerce-wishlist' => array(
			'name'      => __( 'YITH WooCommerce Wishlist', 'trove' ),
			'slug'      => 'yith-woocommerce-wishlist',
			'required'  => false,
			'desc'      => __( 'Let customers save products to a wishlist and return later to complete their purchase.', 'trove' ),
			'image_url' => 'https://ps.w.org/yith-woocommerce-wishlist/assets/icon-128x128.gif?rev=3129302',
		)
	);

	// Bundled Trove Enhancer (inc/plugins/trove-enhancer.zip).
	if ( isset( $bundled_plugins['trove-enhancer'] ) ) {
		$enhancer = $bundled_plugins['trove-enhancer'];
		$zip_path = \TROVE_DIR . '/inc/plugins/' . $enhancer['file'];

		if ( file_exists( $zip_path ) ) {
			$plugins[ $enhancer['slug'] ] = array(
				'name'               => $enhancer['name'],
				'slug'               => $enhancer['slug'],
				'source'             => $enhancer['file'],
				'required'           => true,
				'version'            => $enhancer['version'],
				'force_activation'   => false,
				'force_deactivation' => false,
				'external_url'       => '',
				'desc'               => __( 'Extends TroveWP with additional theme features, blocks, and WooCommerce enhancements.', 'trove' ),
				'image_url'          => get_trove_plugin_image_url( 'trove-enhancer' ),
			);
		}
	}

	/**
	 * Filters the TroveWP recommended plugins list.
	 *
	 * @param array<string, array<string, mixed>> $plugins Plugin slug => plugin data.
	 */
	return apply_filters( 'trove_recommended_plugins', $plugins );
}

/**
 * Register plugins with TGMPA.
 *
 * @return void
 */
function register_trove_tgmpa_plugins() {
	$plugins = get_trove_recommended_plugins();

	$config = apply_filters(
		'trove_tgmpa_config',
		array(
			'id'           => 'trovewp',
			'domain'       => 'trove',
			'default_path' => \TROVE_DIR . '/inc/plugins/',
			'menu'         => 'tgmpa-install-plugins',
			'parent_slug'  => 'trovewp',
			'capability'   => 'edit_theme_options',
			'has_notices'  => false,
			'dismissable'  => true,
			'is_automatic' => false,
			'message'      => '',
		)
	);

	if ( empty( $plugins ) ) {
		return;
	}

	\tgmpa( $plugins, $config );
}
add_action( 'tgmpa_register', __NAMESPACE__ . '\\register_trove_tgmpa_plugins' );

/**
 * @deprecated 1.0.0 Use {@see get_trove_recommended_plugins()} instead.
 *
 * @param array $plugins Plugins list.
 * @return array
 */
function filter_legacy_trove_tgmpa_plugins( $plugins ) {
	if ( ! empty( $plugins ) ) {
		return $plugins;
	}

	return get_trove_recommended_plugins();
}
add_filter( 'trove_tgmpa_plugins', __NAMESPACE__ . '\\filter_legacy_trove_tgmpa_plugins' );

add_filter( 'tgmpa_show_admin_notice_capability', __NAMESPACE__ . '\\trove_tgmpa_notice_capability' );

/**
 * Capability for TGMPA admin notices.
 *
 * @return string
 */
function trove_tgmpa_notice_capability() {
	return 'edit_theme_options';
}
