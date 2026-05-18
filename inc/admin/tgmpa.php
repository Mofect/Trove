<?php
/**
 * TGM Plugin Activation bootstrap for TroveWP.
 *
 * @package Trove
 */

namespace Trove\Admin;

defined( 'ABSPATH' ) || exit;

if ( defined( 'TROVEWP_TGMPA_BOOTSTRAPPED' ) ) {
	return;
}

define( 'TROVEWP_TGMPA_BOOTSTRAPPED', true );

/**
 * Load TGMPA during our plugin-management AJAX requests (TGMPA skips AJAX by default).
 *
 * @param bool $load Whether TGMPA should load.
 * @return bool
 */
function trovewp_filter_tgmpa_load_for_ajax( $load ) {
	if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
		return $load;
	}

	$action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( 'trovewp_plugin_action' === $action ) {
		return true;
	}

	return $load;
}
add_filter( 'tgmpa_load', __NAMESPACE__ . '\\trovewp_filter_tgmpa_load_for_ajax', 5 );

// Register tgmpa_register callback before TGM instantiates (constructor fires that hook).
require_once \TROVE_DIR . '/inc/admin/plugins.php';
require_once \TROVE_DIR . '/inc/admin/class-tgm-plugin-activation.php';
require_once \TROVE_DIR . '/inc/admin/plugin-actions.php';
require_once \TROVE_DIR . '/inc/admin/demo-importer.php';

/**
 * Hide the TGMPA submenu; plugins UI lives on the Recommended Plugins tab.
 *
 * @return void
 */
function hide_trove_tgmpa_submenu() {
	remove_submenu_page( 'trovewp', 'tgmpa-install-plugins' );
}
add_action( 'admin_menu', __NAMESPACE__ . '\\hide_trove_tgmpa_submenu', 999 );
