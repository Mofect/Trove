<?php
/**
 * Footer layout 3.
 *
 * @package Trove
 */

$args = wp_parse_args(
	$args,
	array(
		'year'        => gmdate( 'Y' ),
		'site_name'   => get_bloginfo( 'name' ),
		'tagline'     => get_bloginfo( 'description' ),
		'has_tagline' => false,
		'copy_text'   => __( 'All rights reserved.', 'trove' ),
		'credit_html' => '',
		'extra_lines' => array(),
		'has_extra'   => false,
		'has_menu'    => false,
	)
);
?>
<div class="site-footer__inner site-footer__inner--footer-3">
	<div class="site-footer__col site-footer__col--brand">
		<p class="site-footer__title"><?php echo esc_html( $args['site_name'] ); ?></p>
		<?php if ( ! empty( $args['has_tagline'] ) ) : ?>
			<p class="site-footer__tagline"><?php echo esc_html( $args['tagline'] ); ?></p>
		<?php endif; ?>
	</div>

	<?php if ( ! empty( $args['has_menu'] ) ) : ?>
		<div class="site-footer__col site-footer__col--menu">
			<nav class="site-footer__nav" aria-label="<?php esc_attr_e( 'Footer menu', 'trove' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'site-footer__menu site-footer__menu--stacked',
						'depth'          => 1,
					)
				);
				?>
			</nav>
		</div>
	<?php endif; ?>

	<div class="site-footer__col site-footer__col--meta">
		<p class="site-footer__copy">
			&copy; <?php echo esc_html( $args['year'] ); ?>
			<?php echo esc_html( $args['site_name'] ); ?>.
			<?php echo esc_html( $args['copy_text'] ); ?>
		</p>
		<p class="site-footer__credit"><?php echo wp_kses_post( $args['credit_html'] ); ?></p>
		<?php if ( ! empty( $args['has_extra'] ) ) : ?>
			<div class="site-footer__extra-responsive">
				<p class="site-footer__extra trove-fe--pc"><?php echo esc_html( $args['extra_lines']['pc'] ?? '' ); ?></p>
				<p class="site-footer__extra trove-fe--tablet"><?php echo esc_html( $args['extra_lines']['tablet'] ?? '' ); ?></p>
				<p class="site-footer__extra trove-fe--phone"><?php echo esc_html( $args['extra_lines']['phone'] ?? '' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</div>
