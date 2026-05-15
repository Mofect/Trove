<?php
/**
 * Footer layout 1.
 *
 * @package Trove
 */

$trove_footer_menus = \Trove\get_footer_menus( \Trove\get_footer_menu_slot_ids() );
$trove_footer_logo  = \Trove\get_footer_logo_url();
$show_brand         = \Trove\should_render_footer_brand_info();
$show_social        = \Trove\should_render_footer_social_links();
?>
<div class="site-footer__inner site-footer__inner--footer-1">
	<div class="site-footer__top"<?php echo ( $show_brand || $show_social ) ? '' : ' hidden'; ?>>
		<div class="site-footer__brand"<?php echo $show_brand ? '' : ' hidden'; ?>>
			<?php if ( '' !== $trove_footer_logo ) : ?>
				<img class="site-footer__logo" src="<?php echo esc_url( $trove_footer_logo ); ?>" alt="<?php echo esc_attr( \Trove\get_footer_brand_name() ); ?>" />
			<?php endif; ?>
			<div class="site-footer__brand-info">
				<h2 class="site-footer__title"><?php echo esc_html( \Trove\get_footer_brand_name() ); ?></h2>
				<p class="site-footer__description"><?php echo esc_html( \Trove\get_footer_brand_description() ); ?></p>
			</div>
		</div>

		<div class="site-footer__socials"<?php echo $show_social ? '' : ' hidden'; ?>>
			<?php \Trove\social_links(); ?>
		</div>
	</div>

	<?php if ( ! empty( $trove_footer_menus ) || \Trove\should_render_footer_newsletter() ) : ?>
	<div class="site-footer__content">
	    <?php if ( ! empty( $trove_footer_menus ) ) : ?>
			<div class="site-footer__menus">
				<?php foreach ( $trove_footer_menus as $menu_obj ) : ?>
					<?php $menu_list_id = 'site-footer-menu-' . (string) $menu_obj->term_id; ?>
					<nav class="site-footer__menu-col" aria-label="<?php echo esc_attr( $menu_obj->name ); ?>">
						<h3 class="site-footer__menu-title">
							<button
								class="site-footer__menu-toggle"
								type="button"
								aria-expanded="true"
								aria-controls="<?php echo esc_attr( $menu_list_id ); ?>"
							>
								<span class="site-footer__menu-toggle-text"><?php echo esc_html( $menu_obj->name ); ?></span>
							</button>
						</h3>
						<?php
						wp_nav_menu(
							array(
								'menu'        => $menu_obj->term_id,
								'menu_id'     => $menu_list_id,
								'container'   => false,
								'menu_class'  => 'site-footer__menu-list',
								'depth'       => 1,
								'fallback_cb' => false,
							)
						);
						?>
					</nav>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="site-footer__subscribe"<?php echo \Trove\should_render_footer_newsletter() ? '' : ' hidden'; ?>>
			<h3 class="site-footer__subscribe-title"><?php echo esc_html( \Trove\get_footer_newsletter_title() ); ?></h3>
			<div class="site-footer__subscribe-description"><?php echo wp_kses_post( \Trove\get_footer_newsletter_description() ); ?></div>
			<?php \Trove\render_footer_subscribe_form(); ?>
		</div>
	</div>
	<?php endif; ?>

	<div class="site-footer__bottom<?php echo ( empty( $trove_footer_menus ) && ! \Trove\should_render_footer_newsletter() ) ? ' site-footer__bottom--no-content' : ''; ?>">
		<div class="site-footer__bottom-left">
			<p class="site-footer__copy">
				<span class="site-footer__copy-inner">
				<?php
					$copy = get_theme_mod( 'trove_footer_copy_text', (string) \Trove\Customizer\customizer_default( 'trove_footer_copy_text', '' ) );
					if ( ! empty( $copy ) ) {
						echo wp_kses_post( $copy );
					} else {
						echo wp_kses_post( '&copy; ' . esc_html( gmdate( 'Y' ) ) . ' ' . esc_html( get_bloginfo( 'name' ) ) . ' ' . esc_html__( 'All rights reserved.', 'trove' ) );
					}
				?>
				</span>
			</p>
		</div>
	</div>
</div>
