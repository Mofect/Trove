<?php
/**
 * Header template: vertical.
 *
 * @package Trove
 */
?>
<header class="<?php \Trove\Customizer\the_header_class_attr(); ?>" role="banner">
	<div class="site-header__inner">
		<div class="site-header__brand">
			<?php
			if ( has_custom_logo() ) {
				the_custom_logo();
			} else {
			?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-header__title-link"><?php bloginfo( 'name' ); ?></a>
			<?php } ?>
		</div>
		
		<?php if ( class_exists( 'WooCommerce' ) ) : ?>
			<div class="site-header__woo">
				<?php
				echo do_blocks( '<!-- wp:woocommerce/customer-account {"iconClass":"wc-block-customer-account__account-icon","displayStyle":"icon_only"} /-->' );
				echo do_blocks( '<!-- wp:woocommerce/mini-cart /-->' );
				?>
			</div>
		<?php endif; ?>
	</div>
    <nav class="site-header__nav" aria-label="<?php esc_attr_e( 'Primary menu', 'trove' ); ?>">
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
</header>