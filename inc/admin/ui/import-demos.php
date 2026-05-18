<?php
/**
 * Import Demos tab — demo grid (OCDI-compatible).
 *
 * @package Trove
 */

namespace Trove\Admin;

defined( 'ABSPATH' ) || exit;

$demos       = get_trove_demo_import_files();
$ocdi_active = is_ocdi_active();
?>

<div class="trovewp-admin__box">
	<?php if ( ! $ocdi_active ) : ?>
		<p>
			<?php esc_html_e( 'One Click Demo Import is required to import demo content. Install and activate the plugin, then return to this page.', 'trove' ); ?>
		</p>
		<p class="trovewp-admin__actions">
			<a class="button button-primary" href="<?php echo esc_url( get_ocdi_install_url() ); ?>">
				<?php esc_html_e( 'Install One Click Demo Import', 'trove' ); ?>
			</a>
		</p>
	<?php else : ?>
		<p>
			<?php esc_html_e( 'Choose a demo below to import pages, posts, widgets, menus, and customizer settings. We recommend installing WooCommerce and Elementor first.', 'trove' ); ?>
		</p>
	<?php endif; ?>
</div>

<?php if ( empty( $demos ) ) : ?>
	<div class="trovewp-admin__box">
		<p><?php esc_html_e( 'No demos are registered yet. Add import files via the trove_demo_import_files filter.', 'trove' ); ?></p>
	</div>
<?php else : ?>
	<div class="trovewp-demos-grid" id="trovewp-demos-list">
		<?php foreach ( $demos as $index => $demo ) : ?>
			<?php
			$has_content = ! empty( $demo['local_import_file'] ) && file_exists( $demo['local_import_file'] );
			$has_content = $has_content || ! empty( $demo['import_file_url'] );

			$preview_image = ! empty( $demo['import_preview_image_url'] )
				? $demo['import_preview_image_url']
				: \TROVE_URI . '/inc/demos/default/screenshot.jpg';

			$categories = ! empty( $demo['categories'] ) && is_array( $demo['categories'] )
				? implode( ', ', $demo['categories'] )
				: '';
			?>
			<article class="trovewp-demo-card<?php echo $has_content ? '' : ' trovewp-demo-card--unavailable'; ?>">
				<div class="trovewp-demo-card__image">
					<img src="<?php echo esc_url( $preview_image ); ?>" alt="" loading="lazy" />
				</div>
				<div class="trovewp-demo-card__body">
					<h3 class="trovewp-demo-card__title"><?php echo esc_html( $demo['import_file_name'] ); ?></h3>
					<?php if ( $categories ) : ?>
						<p class="trovewp-demo-card__categories"><?php echo esc_html( $categories ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $demo['import_notice'] ) ) : ?>
						<p class="trovewp-demo-card__notice"><?php echo esc_html( $demo['import_notice'] ); ?></p>
					<?php endif; ?>
					<?php if ( ! $has_content ) : ?>
						<p class="trovewp-demo-card__status">
							<?php esc_html_e( 'Demo package not found. Add content.xml to inc/demos/default/ or set import_file_url.', 'trove' ); ?>
						</p>
					<?php endif; ?>
					<div class="trovewp-demo-card__actions">
						<?php if ( ! empty( $demo['preview_url'] ) ) : ?>
							<a class="button" href="<?php echo esc_url( $demo['preview_url'] ); ?>" target="_blank" rel="noopener noreferrer">
								<?php esc_html_e( 'Preview', 'trove' ); ?>
							</a>
						<?php endif; ?>
						<?php if ( $ocdi_active && $has_content ) : ?>
							<a class="button button-primary" href="<?php echo esc_url( get_trove_demo_import_url( $index ) ); ?>">
								<?php esc_html_e( 'Import Demo', 'trove' ); ?>
							</a>
						<?php elseif ( ! $ocdi_active ) : ?>
							<span class="button button-primary disabled"><?php esc_html_e( 'Import Demo', 'trove' ); ?></span>
						<?php else : ?>
							<span class="button disabled"><?php esc_html_e( 'Unavailable', 'trove' ); ?></span>
						<?php endif; ?>
					</div>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
