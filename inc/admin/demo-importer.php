<?php
/**
 * Demo importer — One Click Demo Import integration for TroveWP.
 *
 * @package Trove
 * @see https://github.com/awesomemotive/one-click-demo-import
 */

namespace Trove\Admin;

defined( 'ABSPATH' ) || exit;

require_once \TROVE_DIR . '/inc/admin/demos.php';

/**
 * Whether One Click Demo Import is active.
 *
 * @return bool
 */
function is_ocdi_active() {
	return class_exists( '\OCDI\OneClickDemoImport' ) || class_exists( 'OCDI_Plugin' );
}

/**
 * OCDI admin page slug under TroveWP.
 *
 * @return string
 */
function get_trove_ocdi_menu_slug() {
	return 'trovewp-import-demos';
}

/**
 * Register demo files with OCDI (current and legacy filter names).
 *
 * @param array $files Existing imports.
 * @return array
 */
function register_ocdi_import_files( $files ) {
	$demos = filter_trove_demo_import_files_with_content( get_trove_demo_import_files() );

	if ( empty( $demos ) ) {
		return $files;
	}

	return array_merge( $files, $demos );
}
add_filter( 'ocdi/import_files', __NAMESPACE__ . '\\register_ocdi_import_files' );
add_filter( 'pt-ocdi/import_files', __NAMESPACE__ . '\\register_ocdi_import_files' );

/**
 * Place OCDI under TroveWP and use a dedicated menu slug.
 *
 * @param array $settings OCDI page settings.
 * @return array
 */
function filter_ocdi_plugin_page_setup( $settings ) {
	$settings['parent_slug'] = 'trovewp';
	$settings['page_title']  = __( 'Import Demo Data', 'trove' );
	$settings['menu_title']  = __( 'Import Demos', 'trove' );
	$settings['capability']  = 'import';
	$settings['menu_slug']   = get_trove_ocdi_menu_slug();

	return $settings;
}
add_filter( 'ocdi/plugin_page_setup', __NAMESPACE__ . '\\filter_ocdi_plugin_page_setup' );
add_filter( 'pt-ocdi/plugin_page_setup', __NAMESPACE__ . '\\filter_ocdi_plugin_page_setup' );

/**
 * Hide duplicate OCDI submenu; demos are accessed from the TroveWP tab.
 *
 * @return void
 */
function hide_trove_ocdi_submenu() {
	remove_submenu_page( 'trovewp', get_trove_ocdi_menu_slug() );
}
add_action( 'admin_menu', __NAMESPACE__ . '\\hide_trove_ocdi_submenu', 999 );

/**
 * Disable OCDI branding footer on import success.
 *
 * @return bool
 */
function disable_ocdi_branding() {
	return true;
}
add_filter( 'ocdi/disable_pt_branding', __NAMESPACE__ . '\\disable_ocdi_branding' );
add_filter( 'pt-ocdi/disable_pt_branding', __NAMESPACE__ . '\\disable_ocdi_branding' );

/**
 * Custom intro on the OCDI screen when opened directly.
 *
 * @param string $text Default intro HTML.
 * @return string
 */
function filter_ocdi_plugin_intro_text( $text ) {
	$text .= '<div class="trovewp-admin__box"><p>';
	$text .= esc_html__( 'Import a TroveWP demo to load pages, posts, widgets, menus, and customizer settings.', 'trove' );
	$text .= '</p></div>';

	return $text;
}
add_filter( 'ocdi/plugin_intro_text', __NAMESPACE__ . '\\filter_ocdi_plugin_intro_text' );

/**
 * After demo import: assign menus and front page.
 *
 * @param array $selected_import Selected import data.
 * @return void
 */
function trove_ocdi_after_import( $selected_import ) {
	$menu = get_term_by( 'name', 'Primary Menu', 'nav_menu' );

	if ( ! $menu ) {
		$menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );
	}

	if ( $menu && ! is_wp_error( $menu ) ) {
		set_theme_mod(
			'nav_menu_locations',
			array(
				'primary' => $menu->term_id,
			)
		);
	}

	$front_page = get_page_by_title( 'Home' );

	if ( ! $front_page ) {
		$front_page = get_page_by_title( 'Homepage' );
	}

	if ( $front_page instanceof \WP_Post ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front_page->ID );
	}

	$blog_page = get_page_by_title( 'Blog' );

	if ( $blog_page instanceof \WP_Post ) {
		update_option( 'page_for_posts', $blog_page->ID );
	}

	/**
	 * Fires after a TroveWP demo import completes.
	 *
	 * @param array $selected_import OCDI selected import array.
	 */
	do_action( 'trove_after_demo_import', $selected_import );
}
add_action( 'ocdi/after_import', __NAMESPACE__ . '\\trove_ocdi_after_import' );
add_action( 'pt-ocdi/after_import', __NAMESPACE__ . '\\trove_ocdi_after_import' );

/**
 * URL to start importing a predefined demo via OCDI.
 *
 * @param int $index Demo index in the import files array.
 * @return string
 */
function get_trove_demo_import_url( $index ) {
	return add_query_arg(
		array(
			'page'   => get_trove_ocdi_menu_slug(),
			'import' => absint( $index ),
		),
		admin_url( 'admin.php' )
	);
}

/**
 * Install One Click Demo Import plugin URL.
 *
 * @return string
 */
function get_ocdi_install_url() {
	return wp_nonce_url(
		self_admin_url( 'update.php?action=install-plugin&plugin=one-click-demo-import' ),
		'install-plugin_one-click-demo-import'
	);
}

/**
 * Enqueue OCDI assets on the TroveWP Import Demos tab.
 *
 * @param string $hook_suffix Admin page hook.
 * @return void
 */
function enqueue_ocdi_assets_on_trovewp_tab( $hook_suffix ) {
	if ( get_trovewp_admin_page_hook() !== $hook_suffix ) {
		return;
	}

	if ( 'import-demos' !== get_current_trovewp_admin_tab() ) {
		return;
	}

	if ( ! defined( 'OCDI_URL' ) || ! defined( 'OCDI_VERSION' ) ) {
		return;
	}

	wp_enqueue_style( 'ocdi-main-css', OCDI_URL . 'assets/css/main.css', array(), OCDI_VERSION );
	wp_enqueue_script( 'ocdi-main-js', OCDI_URL . 'assets/js/main.js', array( 'jquery' ), OCDI_VERSION, true );

	if ( class_exists( '\OCDI\OneClickDemoImport' ) ) {
		$ocdi = \OCDI\OneClickDemoImport::get_instance();

		if ( method_exists( $ocdi, 'admin_enqueue_scripts' ) ) {
			$ocdi->admin_enqueue_scripts( get_trove_ocdi_menu_slug() );
		}
	}
}
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_ocdi_assets_on_trovewp_tab', 20 );
