<?php
/**
 * TroveWP admin page (admin.php).
 *
 * @package Trove
 */

namespace Trove\Admin;

use Trove\TroveTheme;

defined( 'ABSPATH' ) || exit;

if ( defined( 'TROVEWP_ADMIN_BOOTSTRAPPED' ) ) {
	return;
}

define( 'TROVEWP_ADMIN_BOOTSTRAPPED', true );

require_once \TROVE_DIR . '/inc/admin/tgmpa.php';

/**
 * TroveWP admin screen hook suffix.
 *
 * @return string
 */
function get_trovewp_admin_page_hook() {
	return 'toplevel_page_trovewp';
}

/**
 * Register TroveWP as a top-level admin menu page.
 *
 * @return void
 */
function register_trovewp_admin_page() {
	add_menu_page(
		__( 'TroveWP', 'trove' ),
		__( 'TroveWP', 'trove' ),
		'edit_theme_options',
		'trovewp',
		__NAMESPACE__ . '\\render_trovewp_admin_page',
		'dashicons-admin-customizer',
		59
	);
}
add_action( 'admin_menu', __NAMESPACE__ . '\\register_trovewp_admin_page' );

/**
 * Set admin screen title before admin-header.php (avoids strip_tags( null ) on PHP 8.1+).
 *
 * @return void
 */
function set_trovewp_admin_title() {
	global $title;

	$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( 'trovewp' === $page ) {
		$tabs    = get_trovewp_admin_tabs();
		$current = get_current_trovewp_admin_tab();
		$title   = $tabs[ $current ] ?? __( 'TroveWP', 'trove' );
		return;
	}

	if ( 'trovewp-import-demos' === $page ) {
		$title = __( 'Import Demo Data', 'trove' );
		return;
	}

	if ( 'tgmpa-install-plugins' === $page ) {
		$title = __( 'Recommended Plugins', 'trove' );
	}
}
add_action( 'admin_init', __NAMESPACE__ . '\\set_trovewp_admin_title', 1 );
add_action( 'load-' . get_trovewp_admin_page_hook(), __NAMESPACE__ . '\\set_trovewp_admin_title', 5 );
add_action( 'load-trovewp_page_tgmpa-install-plugins', __NAMESPACE__ . '\\set_trovewp_admin_title', 5 );
add_action( 'load-trovewp_page_trovewp-import-demos', __NAMESPACE__ . '\\set_trovewp_admin_title', 5 );

/**
 * Admin tabs for the TroveWP page.
 *
 * @return array<string, string> Tab slug => label.
 */
function get_trovewp_admin_tabs() {
	$tabs = array(
		'general'             => __( 'General', 'trove' ),
		'recommended-plugins' => __( 'Recommended Plugins', 'trove' ),
		'import-demos'        => __( 'Import Demos', 'trove' ),
	);

	/**
	 * Filters TroveWP admin page tabs.
	 *
	 * @param array<string, string> $tabs Tab slug => label.
	 */
	return apply_filters( 'trovewp_admin_tabs', $tabs );
}

/**
 * Current admin tab slug.
 *
 * @return string
 */
function get_current_trovewp_admin_tab() {
	$tabs    = get_trovewp_admin_tabs();
	$current = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'general'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( ! isset( $tabs[ $current ] ) ) {
		$current = 'general';
	}

	return $current;
}

/**
 * URL for a TroveWP admin tab.
 *
 * @param string $tab Tab slug.
 * @return string
 */
function get_trovewp_admin_tab_url( $tab ) {
	return add_query_arg(
		array(
			'page' => 'trovewp',
			'tab'  => $tab,
		),
		admin_url( 'admin.php' )
	);
}

/**
 * Render the TroveWP admin page.
 *
 * @return void
 */
function render_trovewp_admin_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to access this page.', 'trove' ) );
	}

	$tabs         = get_trovewp_admin_tabs();
	$current_tab  = get_current_trovewp_admin_tab();
	$current_label = $tabs[ $current_tab ];
	?>
	<div class="wrap woocommerce trovewp-admin">
		<form method="get" id="trovewp-settings-form" class="trovewp-admin__form" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
			<input type="hidden" name="page" value="trovewp" />
			<nav class="nav-tab-wrapper woo-nav-tab-wrapper" aria-label="<?php esc_attr_e( 'TroveWP settings sections', 'trove' ); ?>">
				<?php foreach ( $tabs as $slug => $label ) : ?>
					<a
						href="<?php echo esc_url( get_trovewp_admin_tab_url( $slug ) ); ?>"
						class="nav-tab <?php echo $current_tab === $slug ? 'nav-tab-active' : ''; ?>"
					>
						<?php echo esc_html( $label ); ?>
					</a>
				<?php endforeach; ?>
				<?php
				/**
				 * Fires after TroveWP admin tabs (same hook name pattern as WooCommerce settings).
				 */
				do_action( 'trovewp_settings_tabs' );
				?>
			</nav>

			<h1 class="screen-reader-text"><?php echo esc_html( $current_label ); ?></h1>

			<div class="trovewp-admin__content">
				<?php
				/**
				 * Fires before the active TroveWP admin tab panel.
				 *
				 * @param string $current_tab Current tab slug.
				 */
				do_action( 'trovewp_settings_before_' . $current_tab );

				render_trovewp_admin_tab_panel( $current_tab );

				/**
				 * Fires after the active TroveWP admin tab panel.
				 *
				 * @param string $current_tab Current tab slug.
				 */
				do_action( 'trovewp_settings_after_' . $current_tab );

				/**
				 * Fires when rendering a TroveWP admin tab (legacy-style hook).
				 *
				 * @param string $current_tab Current tab slug.
				 */
				do_action( 'trovewp_settings_' . $current_tab );
				?>
			</div>
		</form>
	</div>
	<?php
}

/**
 * Render tab panel content.
 *
 * @param string $tab Tab slug.
 * @return void
 */
function render_trovewp_admin_tab_panel( $tab ) {
	switch ( $tab ) {
		case 'recommended-plugins':
			render_trovewp_recommended_plugins_panel();
			break;
		case 'import-demos':
			render_trovewp_import_demos_panel();
			break;
		case 'general':
			render_trovewp_general_panel();
			break;
		default:
			break;
	}
}

/**
 * Render the General tab.
 *
 * @return void
 */
function render_trovewp_general_panel() {
	require \TROVE_DIR . '/inc/admin/ui/general.php';
}

/**
 * Render the Recommended Plugins tab (card grid or TGMPA installer).
 *
 * @return void
 */
function render_trovewp_recommended_plugins_panel() {
	require \TROVE_DIR . '/inc/admin/ui/recommended-plugins.php';
}

/**
 * Render the Import Demos tab.
 *
 * @return void
 */
function render_trovewp_import_demos_panel() {
	require \TROVE_DIR . '/inc/admin/ui/import-demos.php';
}

/**
 * Add body class on the TroveWP admin screen.
 *
 * @param string $classes Admin body classes.
 * @return string
 */
function trovewp_admin_body_class( $classes ) {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	$trovewp_screens = array(
		'toplevel_page_trovewp',
		'trovewp_page_tgmpa-install-plugins',
		'trovewp_page_trovewp-import-demos',
	);

	if ( $screen && in_array( $screen->id, $trovewp_screens, true ) ) {
		$classes .= ' trovewp-admin-page';
	}
	return $classes;
}
add_filter( 'admin_body_class', __NAMESPACE__ . '\\trovewp_admin_body_class' );

/**
 * Suppress admin notice bars on the TroveWP screen.
 *
 * @return void
 */
function suppress_trovewp_admin_notices() {
	remove_all_actions( 'admin_notices' );
	remove_all_actions( 'all_admin_notices' );
}
add_action( 'load-' . get_trovewp_admin_page_hook(), __NAMESPACE__ . '\\suppress_trovewp_admin_notices' );
add_action( 'load-trovewp_page_tgmpa-install-plugins', __NAMESPACE__ . '\\suppress_trovewp_admin_notices' );
add_action( 'load-trovewp_page_trovewp-import-demos', __NAMESPACE__ . '\\suppress_trovewp_admin_notices' );

/**
 * Admin styles for the TroveWP page.
 *
 * @param string $hook_suffix Current admin page hook.
 * @return void
 */
function enqueue_trovewp_admin_assets( $hook_suffix ) {
	$trovewp_hooks = array(
		get_trovewp_admin_page_hook(),
		'trovewp_page_tgmpa-install-plugins',
		'trovewp_page_trovewp-import-demos',
	);

	if ( ! in_array( $hook_suffix, $trovewp_hooks, true ) ) {
		return;
	}

	$style_deps = array();
	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_style( 'woocommerce_admin_styles' );
		$style_deps[] = 'woocommerce_admin_styles';
	}

	wp_enqueue_style(
		'trovewp-admin',
		TroveTheme::asset_url( 'assets/css/trovewp-admin.css' ),
		$style_deps,
		TROVE_THEME_VERSION
	);

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( $screen && 'toplevel_page_trovewp' === $screen->id ) {
		if ( 'recommended-plugins' === get_current_trovewp_admin_tab() ) {
			wp_enqueue_style( 'plugin-install' );
			wp_enqueue_script( 'thickbox' );
			wp_enqueue_style( 'thickbox' );
			enqueue_trovewp_plugins_script();
		}
	}
}
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_trovewp_admin_assets' );
