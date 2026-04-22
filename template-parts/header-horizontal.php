<?php
/**
 * Header template: horizontal.
 *
 * @package Trove
 */
?>
<header class="<?php \Trove\Customizer\the_header_class_attr(); ?>" role="banner">
	<div class="site-header__inner">
		<div class="site-header__brand">
			<?php get_template_part( 'template-parts/header', 'brand' ); ?>
		</div>
		<nav class="<?php \Trove\Customizer\the_header_nav_class_attr(); ?>" aria-label="<?php esc_attr_e( 'Primary menu', 'trove' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'menu_class'     => 'primary-menu',
						'container'      => false,
					)
				);
			} else {
				wp_page_menu(
					array(
						'menu_class' => 'primary-menu',
					)
				);
			}
			?>
		</nav>
		<div class="site-header__searchbox" id="trove-header-searchbox" hidden>
			<form class="site-header__search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label class="screen-reader-text" for="trove-header-search-input"><?php esc_html_e( 'Search', 'trove' ); ?></label>
				<input
					id="trove-header-search-input"
					type="search"
					name="s"
					value="<?php echo esc_attr( get_search_query() ); ?>"
					placeholder="<?php esc_attr_e( 'Search', 'trove' ); ?>"
				/>
				<button type="button" class="site-header__search-close" aria-label="<?php esc_attr_e( 'Close search', 'trove' ); ?>">
					<span aria-hidden="true">&times;</span>
				</button>
			</form>
		</div>
		<?php if ( class_exists( 'WooCommerce' ) ) : ?>
			<div class="site-header__buttons">
				<?php
				?>
				<button type="button" class="site-header__search site-header__search-toggle" aria-expanded="false" aria-controls="trove-header-searchbox">
					<span class="screen-reader-text"><?php esc_html_e( 'Search', 'trove' ); ?></span>
					<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="M20 20l-4.3-4.3"></path></svg>
				</button>
				<?php
				echo do_blocks( '<!-- wp:woocommerce/customer-account {"iconClass":"wc-block-customer-account__account-icon","displayStyle":"icon_only"} /-->' );
				echo do_blocks( '<!-- wp:woocommerce/mini-cart /-->' );
				?>
			</div>
		<?php endif; ?>
	</div>
</header>