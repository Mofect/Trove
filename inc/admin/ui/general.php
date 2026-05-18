<?php
/**
 * General tab — TroveWP dashboard overview.
 *
 * @package Trove
 */

namespace Trove\Admin;

defined( 'ABSPATH' ) || exit;

$theme         = wp_get_theme( get_template() );
$theme_name    = $theme->get( 'Name' );
$theme_version = defined( 'TROVE_THEME_VERSION' ) ? TROVE_THEME_VERSION : $theme->get( 'Version' );
$theme_uri     = $theme->get( 'ThemeURI' );
$description   = $theme->get( 'Description' );

$customize_url = admin_url( 'customize.php' );
$plugins_url   = get_trovewp_admin_tab_url( 'recommended-plugins' );
$demos_url     = get_trovewp_admin_tab_url( 'import-demos' );
$docs_url      = $theme_uri ? $theme_uri : 'https://trovewp.com';
?>

<div class="trovewp-general">
	<div class="trovewp-admin__box trovewp-general__intro">
		<div class="trovewp-general__intro-header">
			<h2 class="trovewp-general__title"><?php echo esc_html( $theme_name ); ?></h2>
			<?php if ( $theme_version ) : ?>
				<span class="trovewp-general__version">
					<?php
					printf(
						/* translators: %s: theme version number */
						esc_html__( 'Version %s', 'trove' ),
						esc_html( $theme_version )
					);
					?>
				</span>
			<?php endif; ?>
		</div>
		<?php if ( $description ) : ?>
			<p><?php echo esc_html( $description ); ?></p>
		<?php endif; ?>
		<p>
			<?php
			esc_html_e(
				'Use the tabs above to install recommended plugins, import demo content, and customize your storefront. Start with the steps below when setting up a new site.',
				'trove'
			);
			?>
		</p>
		<p class="trovewp-admin__actions">
			<a class="button button-primary" href="<?php echo esc_url( $customize_url ); ?>">
				<?php esc_html_e( 'Open Customizer', 'trove' ); ?>
			</a>
			<?php if ( $theme_uri ) : ?>
				<a class="button" href="<?php echo esc_url( $docs_url ); ?>" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Documentation', 'trove' ); ?>
				</a>
			<?php endif; ?>
		</p>
	</div>

	<div class="trovewp-general__grid">
		<div class="trovewp-admin__box trovewp-general__card">
			<h3><?php esc_html_e( '1. Install plugins', 'trove' ); ?></h3>
			<p>
				<?php
				esc_html_e(
					'WooCommerce and Elementor are required for the full TroveWP experience. Install and activate them from the Recommended Plugins tab.',
					'trove'
				);
				?>
			</p>
			<p class="trovewp-admin__actions">
				<a class="button button-primary" href="<?php echo esc_url( $plugins_url ); ?>">
					<?php esc_html_e( 'Recommended Plugins', 'trove' ); ?>
				</a>
			</p>
		</div>

		<div class="trovewp-admin__box trovewp-general__card">
			<h3><?php esc_html_e( '2. Import a demo', 'trove' ); ?></h3>
			<p>
				<?php
				esc_html_e(
					'Import pages, posts, widgets, menus, and customizer settings in one click with One Click Demo Import.',
					'trove'
				);
				?>
			</p>
			<p class="trovewp-admin__actions">
				<a class="button button-primary" href="<?php echo esc_url( $demos_url ); ?>">
					<?php esc_html_e( 'Import Demos', 'trove' ); ?>
				</a>
			</p>
		</div>

		<div class="trovewp-admin__box trovewp-general__card">
			<h3><?php esc_html_e( '3. Customize your store', 'trove' ); ?></h3>
			<p>
				<?php
				esc_html_e(
					'Adjust layout, colors, typography, header, footer, and WooCommerce options in the WordPress Customizer.',
					'trove'
				);
				?>
			</p>
			<p class="trovewp-admin__actions">
				<a class="button button-primary" href="<?php echo esc_url( $customize_url ); ?>">
					<?php esc_html_e( 'Customize Theme', 'trove' ); ?>
				</a>
			</p>
		</div>
	</div>
</div>
