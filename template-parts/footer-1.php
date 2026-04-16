<?php
/**
 * Footer layout 1.
 *
 * @package Trove
 */

$args = wp_parse_args(
	$args,
	array(
		'year'        => gmdate( 'Y' ),
		'site_name'   => get_bloginfo( 'name' ),
		'copy_text'   => __( 'All rights reserved.', 'trove' ),
		'credit_html' => '',
		'extra_lines' => array(),
		'has_extra'   => false,
	)
);
?>
<div class="site-footer__inner site-footer__inner--footer-1">
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
