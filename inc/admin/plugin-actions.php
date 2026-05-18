<?php
/**
 * Plugin install / activate actions for TroveWP recommended plugins.
 *
 * @package Trove
 */

namespace Trove\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Get a registered TGMPA plugin by slug.
 *
 * @param string $slug Plugin slug.
 * @return array<string, mixed>|null
 */
function get_trove_plugin_by_slug( $slug ) {
	if ( ! isset( $GLOBALS['tgmpa'] ) ) {
		return null;
	}

	$slug = sanitize_key( $slug );

	if ( isset( \TGM_Plugin_Activation::$instance->plugins[ $slug ] ) ) {
		return \TGM_Plugin_Activation::$instance->plugins[ $slug ];
	}

	return null;
}

/**
 * Render HTML for one plugin card.
 *
 * @param array<string, mixed> $plugin Plugin data.
 * @return string
 */
function render_trove_plugin_card( $plugin ) {
	if ( empty( $plugin ) || ! is_array( $plugin ) ) {
		return '';
	}

	$registered = get_trove_recommended_plugins();

	if ( empty( $plugin['slug'] ) || ! array_key_exists( $plugin['slug'], $registered ) ) {
		return '';
	}

	ob_start();
	include \TROVE_DIR . '/inc/admin/ui/plugin-card.php';
	return (string) ob_get_clean();
}

/**
 * URL for the Recommended Plugins tab.
 *
 * @return string
 */
function get_trovewp_recommended_plugins_url() {
	return add_query_arg(
		array(
			'page' => 'trovewp',
			'tab'  => 'recommended-plugins',
		),
		admin_url( 'admin.php' )
	);
}

/**
 * Unmet plugin dependencies (WordPress RequiresPlugins header).
 *
 * @param string $file_path Plugin basename path.
 * @return array{inactive: array<string, string>, not_installed: array<string, string>}
 */
function trovewp_get_unmet_plugin_dependencies( $file_path ) {
	$empty = array(
		'inactive'      => array(),
		'not_installed' => array(),
	);

	if ( ! class_exists( 'WP_Plugin_Dependencies' ) ) {
		return $empty;
	}

	require_once ABSPATH . 'wp-admin/includes/plugin.php';

	\WP_Plugin_Dependencies::initialize();

	if ( ! \WP_Plugin_Dependencies::has_unmet_dependencies( $file_path ) ) {
		return $empty;
	}

	$dependency_names = \WP_Plugin_Dependencies::get_dependency_names( $file_path );
	$unmet            = $empty;

	foreach ( $dependency_names as $dependency => $dependency_name ) {
		$dependency_file = \WP_Plugin_Dependencies::get_dependency_filepath( $dependency );

		if ( false === $dependency_file ) {
			$unmet['not_installed'][ $dependency ] = $dependency_name;
		} elseif ( is_plugin_inactive( $dependency_file ) ) {
			$unmet['inactive'][ $dependency ] = $dependency_name;
		}
	}

	return $unmet;
}

/**
 * Plain-text notice for unmet dependencies on a plugin card.
 *
 * @param string $file_path Plugin basename path.
 * @return string
 */
function trovewp_get_unmet_dependency_notice( $file_path ) {
	$unmet = trovewp_get_unmet_plugin_dependencies( $file_path );

	$names = array_merge( $unmet['not_installed'], $unmet['inactive'] );

	if ( empty( $names ) ) {
		return '';
	}

	return sprintf(
		/* translators: %s: comma-separated list of plugin names */
		__( 'Requires %s to be installed and activated first.', 'trove' ),
		implode( ', ', $names )
	);
}

/**
 * Convert a WP_Error from plugin activation into a plain-text AJAX message.
 *
 * @param \WP_Error $error Error object.
 * @return string
 */
function trovewp_format_plugin_error_message( $error ) {
	$message = trim( wp_strip_all_tags( $error->get_error_message() ) );

	if ( 'plugin_missing_dependencies' === $error->get_error_code() ) {
		$message .= ' ' . __(
			'Activate the required plugins listed above this card first (for example, WooCommerce).',
			'trove'
		);
	}

	return trim( preg_replace( '/\s+/', ' ', $message ) );
}

/**
 * Validate whether a plugin may be activated.
 *
 * @param string $file_path Plugin basename path.
 * @return true|\WP_Error
 */
function trovewp_validate_plugin_activation( $file_path ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';

	if ( ! function_exists( 'validate_plugin_requirements' ) ) {
		return true;
	}

	$validation = validate_plugin_requirements( $file_path );

	if ( is_wp_error( $validation ) ) {
		return $validation;
	}

	return true;
}

/**
 * Activate a plugin for TroveWP AJAX (requirements checked first).
 *
 * @param array<string, mixed> $plugin Plugin data.
 * @return true|\WP_Error
 */
function trovewp_ajax_activate_plugin( $plugin ) {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return new \WP_Error( 'capability', __( 'You do not have permission to activate plugins.', 'trove' ) );
	}

	trovewp_refresh_plugin_registry( $plugin['slug'] );
	$plugin = get_trove_plugin_by_slug( $plugin['slug'] );

	if ( empty( $plugin['file_path'] ) ) {
		return new \WP_Error( 'missing', __( 'Plugin file path could not be determined.', 'trove' ) );
	}

	$validation = trovewp_validate_plugin_activation( $plugin['file_path'] );

	if ( is_wp_error( $validation ) ) {
		return $validation;
	}

	return activate_plugin( $plugin['file_path'], '', false, true );
}

/**
 * Action buttons for a plugin card (AJAX-driven).
 *
 * @param array<string, mixed> $plugin Plugin data from TGMPA.
 * @return array<string, string>
 */
function get_trove_plugin_actions( $plugin ) {
	if ( ! isset( $GLOBALS['tgmpa'] ) || empty( $plugin['slug'] ) ) {
		return array();
	}

	$tgmpa = $GLOBALS['tgmpa'];

	// After install, file_path may still be the slug until refreshed.
	if ( ! empty( $plugin['slug'] ) && ( empty( $plugin['file_path'] ) || false === strpos( $plugin['file_path'], '/' ) ) ) {
		trovewp_refresh_plugin_registry( $plugin['slug'] );
		$plugin = get_trove_plugin_by_slug( $plugin['slug'] ) ?? $plugin;
	}

	if ( empty( $plugin['file_path'] ) ) {
		return array();
	}

	$installed_plugins = get_plugins();
	$file_path         = $plugin['file_path'];
	$name              = $plugin['name'];
	$slug              = $plugin['slug'];
	$actions           = array();

	if ( ! $plugin['version'] ) {
		$plugin['version'] = $tgmpa->does_plugin_have_update( $plugin['slug'] );
	}

	$button_attrs = sprintf(
		' type="button" data-slug="%1$s" data-plugin-name="%2$s"',
		esc_attr( $slug ),
		esc_attr( $name )
	);

	if ( ! isset( $installed_plugins[ $file_path ] ) ) {
		$actions['install'] = sprintf(
			'<button%1$s data-action="install" class="button button-primary trovewp-plugin-action">%2$s</button>',
			$button_attrs,
			esc_html__( 'Install', 'trove' )
		);
	} elseif ( is_plugin_inactive( $file_path ) ) {
		$unmet = trovewp_get_unmet_plugin_dependencies( $file_path );
		$needs = array_merge( $unmet['not_installed'], $unmet['inactive'] );

		if ( ! empty( $needs ) ) {
			$recommended = get_trove_recommended_plugins();

			foreach ( $needs as $dep_slug => $dep_name ) {
				if ( ! array_key_exists( $dep_slug, $recommended ) ) {
					continue;
				}

				$actions[ 'requires_' . $dep_slug ] = sprintf(
					'<a class="button button-primary trovewp-plugin-dependency-link" href="#trovewp-plugin-%1$s">%2$s</a>',
					esc_attr( $dep_slug ),
					esc_html(
						sprintf(
							/* translators: %s: required plugin name */
							__( 'Set up %s first', 'trove' ),
							$dep_name
						)
					)
				);
			}
		} else {
			$actions['activate'] = sprintf(
				'<button%1$s data-action="activate" class="button button-primary trovewp-plugin-action">%2$s</button>',
				$button_attrs,
				esc_html__( 'Activate', 'trove' )
			);
		}
	} elseif ( ! empty( $plugin['version'] ) && version_compare( $installed_plugins[ $file_path ]['Version'], $plugin['version'], '<' ) ) {
		$actions['update'] = sprintf(
			'<button%1$s data-action="update" class="button button-primary trovewp-plugin-action">%2$s</button>',
			$button_attrs,
			esc_html__( 'Update', 'trove' )
		);
	} elseif ( is_plugin_active( $file_path ) ) {
		$actions['deactivate'] = sprintf(
			'<button%1$s data-action="deactivate" class="button trovewp-plugin-action">%2$s</button>',
			$button_attrs,
			esc_html__( 'Deactivate', 'trove' )
		);
	}

	return $actions;
}

/**
 * Initialize WP_Filesystem for plugin upgrader operations.
 *
 * @return true|\WP_Error
 */
function trovewp_init_filesystem() {
	if ( ! function_exists( 'WP_Filesystem' ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
	}

	ob_start();
	$creds = request_filesystem_credentials( '' );
	ob_end_clean();

	if ( false === $creds ) {
		return new \WP_Error(
			'trovewp_filesystem',
			__( 'Unable to request filesystem credentials.', 'trove' )
		);
	}

	if ( ! \WP_Filesystem( $creds ) ) {
		return new \WP_Error(
			'trovewp_filesystem',
			__( 'Could not access the filesystem. Please check your server permissions.', 'trove' )
		);
	}

	return true;
}

/**
 * Refresh installed plugin paths in TGMPA after install/update.
 *
 * @param string $slug Plugin slug.
 * @return void
 */
function trovewp_refresh_plugin_registry( $slug ) {
	wp_clean_plugins_cache( false );

	if ( ! isset( $GLOBALS['tgmpa'] ) ) {
		return;
	}

	$tgmpa = $GLOBALS['tgmpa'];

	if ( method_exists( $tgmpa, 'populate_file_path' ) ) {
		$tgmpa->populate_file_path( $slug );
	}
}

/**
 * Run a callback while capturing (and discarding) upgrader skin output.
 *
 * @param callable $callback Callback that performs the upgrade.
 * @return mixed Return value of the callback.
 */
function trovewp_run_silent_upgrader( $callback ) {
	ob_start();

	try {
		$result = call_user_func( $callback );
	} finally {
		ob_end_clean();
	}

	return $result;
}

/**
 * Install a plugin via TGMPA download source.
 *
 * @param array<string, mixed> $plugin Plugin data.
 * @return true|\WP_Error
 */
function trovewp_ajax_install_plugin( $plugin ) {
	if ( ! current_user_can( 'install_plugins' ) ) {
		return new \WP_Error( 'capability', __( 'You do not have permission to install plugins.', 'trove' ) );
	}

	$tgmpa = $GLOBALS['tgmpa'];
	$slug  = $plugin['slug'];
	$source = $tgmpa->get_download_url( $slug );

	if ( empty( $source ) ) {
		return new \WP_Error( 'source', __( 'Plugin download source could not be determined.', 'trove' ) );
	}

	$filesystem = trovewp_init_filesystem();
	if ( is_wp_error( $filesystem ) ) {
		return $filesystem;
	}

	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';
	require_once ABSPATH . 'wp-admin/includes/plugin-install.php';

	$skin     = new \WP_Ajax_Upgrader_Skin();
	$upgrader = new \Plugin_Upgrader( $skin );

	add_filter( 'upgrader_source_selection', array( $tgmpa, 'maybe_adjust_source_dir' ), 1, 3 );

	$result = trovewp_run_silent_upgrader(
		static function () use ( $upgrader, $source ) {
			return $upgrader->install( $source );
		}
	);

	remove_filter( 'upgrader_source_selection', array( $tgmpa, 'maybe_adjust_source_dir' ), 1 );

	if ( is_wp_error( $result ) ) {
		return $result;
	}

	if ( ! $result ) {
		return new \WP_Error( 'install_failed', __( 'Plugin installation failed.', 'trove' ) );
	}

	trovewp_refresh_plugin_registry( $slug );

	return true;
}

/**
 * Update a plugin via Plugin_Upgrader.
 *
 * @param array<string, mixed> $plugin Plugin data.
 * @return true|\WP_Error
 */
function trovewp_ajax_update_plugin( $plugin ) {
	if ( ! current_user_can( 'update_plugins' ) ) {
		return new \WP_Error( 'capability', __( 'You do not have permission to update plugins.', 'trove' ) );
	}

	$tgmpa = $GLOBALS['tgmpa'];
	$slug  = $plugin['slug'];

	$filesystem = trovewp_init_filesystem();
	if ( is_wp_error( $filesystem ) ) {
		return $filesystem;
	}

	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';

	trovewp_refresh_plugin_registry( $slug );
	$plugin = get_trove_plugin_by_slug( $slug );

	if ( empty( $plugin['file_path'] ) ) {
		return new \WP_Error( 'update_failed', __( 'Plugin file path could not be determined.', 'trove' ) );
	}

	$skin     = new \WP_Ajax_Upgrader_Skin();
	$upgrader = new \Plugin_Upgrader( $skin );

	add_filter( 'upgrader_source_selection', array( $tgmpa, 'maybe_adjust_source_dir' ), 1, 3 );

	if ( 'repo' !== $plugin['source_type'] ) {
		$to_inject                    = array( $slug => $plugin );
		$to_inject[ $slug ]['source'] = $tgmpa->get_download_url( $slug );
		$tgmpa->inject_update_info( $to_inject );
	}

	$file_path = $plugin['file_path'];

	$result = trovewp_run_silent_upgrader(
		static function () use ( $upgrader, $file_path ) {
			return $upgrader->upgrade( $file_path );
		}
	);

	remove_filter( 'upgrader_source_selection', array( $tgmpa, 'maybe_adjust_source_dir' ), 1 );

	if ( is_wp_error( $result ) ) {
		return $result;
	}

	if ( false === $result ) {
		return new \WP_Error( 'update_failed', __( 'Plugin update failed.', 'trove' ) );
	}

	trovewp_refresh_plugin_registry( $slug );
	$plugin = get_trove_plugin_by_slug( $slug );

	require_once ABSPATH . 'wp-admin/includes/plugin.php';

	// WordPress deactivates plugins during upgrade — re-activate when update succeeds.
	if ( ! empty( $plugin['file_path'] ) && ! is_plugin_active( $plugin['file_path'] ) ) {
		$activate = trovewp_ajax_activate_plugin( $plugin );

		if ( is_wp_error( $activate ) ) {
			// Update succeeded; activation can be retried from the card.
			return true;
		}
	}

	return true;
}

/**
 * AJAX handler for plugin install / activate / update / deactivate.
 *
 * @return void
 */
function ajax_trovewp_plugin_action() {
	check_ajax_referer( 'trovewp-plugins', 'nonce' );

	if ( ! current_user_can( 'edit_theme_options' ) || ! isset( $GLOBALS['tgmpa'] ) ) {
		wp_send_json_error(
			array( 'message' => __( 'You do not have permission to manage plugins.', 'trove' ) ),
			403
		);
	}

	$slug   = isset( $_POST['slug'] ) ? sanitize_key( wp_unslash( $_POST['slug'] ) ) : '';
	$action = isset( $_POST['plugin_action'] ) ? sanitize_key( wp_unslash( $_POST['plugin_action'] ) ) : '';

	if ( ! $slug || ! $action ) {
		wp_send_json_error( array( 'message' => __( 'Invalid request.', 'trove' ) ), 400 );
	}

	$plugin = get_trove_plugin_by_slug( $slug );

	if ( ! $plugin ) {
		wp_send_json_error( array( 'message' => __( 'Plugin not found.', 'trove' ) ), 404 );
	}

	require_once ABSPATH . 'wp-admin/includes/plugin.php';

	$result = null;

	switch ( $action ) {
		case 'install':
			$result = trovewp_ajax_install_plugin( $plugin );
			break;
		case 'update':
			$result = trovewp_ajax_update_plugin( $plugin );
			break;
		case 'activate':
			$result = trovewp_ajax_activate_plugin( $plugin );
			break;
		case 'deactivate':
			if ( ! current_user_can( 'deactivate_plugins' ) ) {
				wp_send_json_error( array( 'message' => __( 'You do not have permission to deactivate plugins.', 'trove' ) ), 403 );
			}
			trovewp_refresh_plugin_registry( $slug );
			$plugin = get_trove_plugin_by_slug( $slug );
			deactivate_plugins( $plugin['file_path'] );
			$result = true;
			break;
		default:
			wp_send_json_error( array( 'message' => __( 'Unknown action.', 'trove' ) ), 400 );
	}

	if ( is_wp_error( $result ) ) {
		wp_send_json_error( array( 'message' => trovewp_format_plugin_error_message( $result ) ) );
	}

	$messages = array(
		'install'    => __( 'Plugin installed successfully.', 'trove' ),
		'activate'   => __( 'Plugin activated successfully.', 'trove' ),
		'update'     => __( 'Plugin updated and activated successfully.', 'trove' ),
		'deactivate' => __( 'Plugin deactivated successfully.', 'trove' ),
	);

	trovewp_refresh_plugin_registry( $slug );

	$plugin = get_trove_plugin_by_slug( $slug );
	$html   = render_trove_plugin_card( $plugin );

	if ( '' === $html && $plugin ) {
		wp_send_json_error(
			array(
				'message' => __( 'Plugin status was updated but the card could not be refreshed. Please reload the page.', 'trove' ),
			)
		);
	}

	wp_send_json_success(
		array(
			'message' => $messages[ $action ] ?? __( 'Done.', 'trove' ),
			'html'    => $html,
			'slug'    => $slug,
		)
	);
}
add_action( 'wp_ajax_trovewp_plugin_action', __NAMESPACE__ . '\\ajax_trovewp_plugin_action' );

/**
 * Enqueue scripts for AJAX plugin management.
 *
 * @return void
 */
function enqueue_trovewp_plugins_script() {
	wp_enqueue_script(
		'trovewp-plugins',
		\Trove\TroveTheme::asset_url( 'assets/js/trovewp-plugins.js' ),
		array( 'jquery' ),
		TROVE_THEME_VERSION,
		true
	);

	wp_localize_script(
		'trovewp-plugins',
		'trovewpPlugins',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'trovewp-plugins' ),
			'i18n'    => array(
				'waiting'  => __( 'Waiting…', 'trove' ),
				'error'    => __( 'Something went wrong. Please try again.', 'trove' ),
				'install'  => __( 'Install', 'trove' ),
				'activate' => __( 'Activate', 'trove' ),
				'update'   => __( 'Update', 'trove' ),
				'deactivate' => __( 'Deactivate', 'trove' ),
			),
		)
	);
}
