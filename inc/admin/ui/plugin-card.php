<?php
/**
 * Single recommended plugin card (included by render_trove_plugin_card).
 *
 * @package Trove
 *
 * @var array<string, mixed> $plugin Plugin data from TGMPA.
 */

namespace Trove\Admin;

defined( 'ABSPATH' ) || exit;

$class         = '';
$plugin_status = '';
$file_path     = $plugin['file_path'];
$plugin_action = get_trove_plugin_actions( $plugin );

if ( ! $plugin['version'] ) {
	$plugin['version'] = \TGM_Plugin_Activation::$instance->does_plugin_have_update( $plugin['slug'] );
}

if ( is_plugin_active( $file_path ) ) {
	$plugin_status = 'active';
	$class         = 'active';
}

$image_url = ! empty( $plugin['image_url'] )
	? $plugin['image_url']
	: get_trove_plugin_image_url( $plugin['slug'] );

$dependency_notice = trovewp_get_unmet_dependency_notice( $file_path );
?>
<div id="trovewp-plugin-<?php echo esc_attr( $plugin['slug'] ); ?>" class="plugin-card <?php echo esc_attr( $class ); ?>" data-plugin-slug="<?php echo esc_attr( $plugin['slug'] ); ?>">
	<div class="plugin-card-top">
		<?php if ( isset( $plugin_action['update'] ) ) : ?>
			<div class="update-message notice inline notice-warning notice-alt trovewp-plugin-update-notice">
				<p>
					<?php
					printf(
						/* translators: %s: plugin version number */
						esc_html__( 'New version available: %s', 'trove' ),
						esc_html( $plugin['version'] )
					);
					?>
				</p>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $plugin['required'] ) ) : ?>
			<div class="plugin-required">
				<?php esc_html_e( 'Required', 'trove' ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $dependency_notice && is_plugin_inactive( $file_path ) ) : ?>
			<div class="notice inline notice-warning notice-alt trovewp-plugin-dependency-notice">
				<p><?php echo esc_html( $dependency_notice ); ?></p>
			</div>
		<?php endif; ?>

		<div class="name column-name">
			<h3>
				<?php if ( 'active' === $plugin_status ) : ?>
					<span>
						<?php
						printf(
							/* translators: %s: plugin name */
							esc_html__( 'Active: %s', 'trove' ),
							esc_html( $plugin['name'] )
						);
						?>
					</span>
				<?php else : ?>
					<?php echo esc_html( $plugin['name'] ); ?>
				<?php endif; ?>
				<img src="<?php echo esc_url( $image_url ); ?>" class="plugin-icon" alt="" />
			</h3>
		</div>

		<div class="action-links">
			<ul class="plugin-action-buttons">
				<?php foreach ( $plugin_action as $action ) : ?>
					<li><?php echo $action; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></li>
				<?php endforeach; ?>
			</ul>
		</div>

		<?php if ( ! empty( $plugin['desc'] ) ) : ?>
			<div class="desc column-description">
				<p><?php echo esc_html( $plugin['desc'] ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</div>
