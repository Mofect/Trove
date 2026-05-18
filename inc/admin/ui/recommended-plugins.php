<?php
/**
 * Recommended Plugins tab — plugin card grid.
 *
 * @package Trove
 */

namespace Trove\Admin;

defined( 'ABSPATH' ) || exit;

if ( ! isset( $GLOBALS['tgmpa'] ) ) {
	echo '<p>' . esc_html__( 'Plugin installer is not available.', 'trove' ) . '</p>';
	return;
}

$plugins            = \TGM_Plugin_Activation::$instance->plugins;
$registered_plugins = get_trove_recommended_plugins();

add_thickbox();
?>

<div class="trovewp-admin__box">
	<p>
		<?php
		esc_html_e(
			'WooCommerce and Elementor are required plugins for TroveWP. Install the recommended plugins below to unlock the full storefront experience.',
			'trove'
		);
		?>
	</p>
</div>

<div class="trovewp-admin__section trovewp-plugins-grid">
	<div id="the-list">
		<?php foreach ( $plugins as $plugin ) : ?>
			<?php
			if ( ! array_key_exists( $plugin['slug'], $registered_plugins ) ) {
				continue;
			}

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in partial.
			echo render_trove_plugin_card( $plugin );
			?>
		<?php endforeach; ?>
		<div class="clear"></div>
	</div>
</div>
