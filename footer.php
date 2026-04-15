<?php
/**
 * Theme footer.
 *
 * @package Trove
 */
?>
<footer class="site-footer" role="contentinfo">
	<div class="site-footer__inner">
		<p class="site-footer__copy">
			&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?>
			<?php bloginfo( 'name' ); ?>.
			<?php esc_html_e( 'All rights reserved.', 'trove' ); ?>
		</p>
		<p class="site-footer__credit">
			<?php
			printf(
				/* translators: %s: TroveWP URL */
				wp_kses_post( __( 'Designed by <a href="%s" target="_blank" rel="noopener noreferrer">TroveWP</a>', 'trove' ) ),
				esc_url( 'https://trovewp.com' )
			);
			?>
		</p>
		<?php
		$footer_vals = \Trove\Customizer\get_responsive_mod_values(
			'trove_footer_credit_text',
			array(
				'pc'     => '',
				'tablet' => '',
				'phone'  => '',
			)
		);
		$has_extra = false;
		foreach ( $footer_vals as $line ) {
			if ( is_string( $line ) && '' !== trim( $line ) ) {
				$has_extra = true;
				break;
			}
		}
		if ( $has_extra ) {
			$bp = \Trove\Customizer\get_responsive_breakpoints_max();
			?>
			<div class="site-footer__extra-responsive">
				<p class="site-footer__extra trove-fe--pc"><?php echo esc_html( $footer_vals['pc'] ); ?></p>
				<p class="site-footer__extra trove-fe--tablet"><?php echo esc_html( $footer_vals['tablet'] ); ?></p>
				<p class="site-footer__extra trove-fe--phone"><?php echo esc_html( $footer_vals['phone'] ); ?></p>
			</div>
			<style>
				.site-footer__extra-responsive .trove-fe--tablet,
				.site-footer__extra-responsive .trove-fe--phone{display:none;}
				.site-footer__extra-responsive .trove-fe--pc{display:block;}
				@media (max-width:<?php echo (int) $bp['tablet']; ?>px){
					.site-footer__extra-responsive .trove-fe--pc{display:none;}
					.site-footer__extra-responsive .trove-fe--tablet{display:block;}
				}
				@media (max-width:<?php echo (int) $bp['phone']; ?>px){
					.site-footer__extra-responsive .trove-fe--tablet{display:none;}
					.site-footer__extra-responsive .trove-fe--phone{display:block;}
				}
			</style>
			<?php
		}
		?>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
