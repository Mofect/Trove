<?php
/**
 * Theme footer.
 *
 * @package Trove
 */
?>
<footer class="site-footer" role="contentinfo">
	<?php
	$footer_layout = get_theme_mod( 'trove_footer_layout', 'footer-1' );
	$footer_layout = is_string( $footer_layout ) ? sanitize_key( $footer_layout ) : 'footer-1';

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

	$footer_args = array(
		'year'          => gmdate( 'Y' ),
		'site_name'     => get_bloginfo( 'name' ),
		'tagline'       => get_bloginfo( 'description' ),
		'has_tagline'   => '' !== trim( (string) get_bloginfo( 'description' ) ),
		'copy_text'     => __( 'All rights reserved.', 'trove' ),
		'credit_html'   => sprintf(
			/* translators: %s: TroveWP URL */
			wp_kses_post( __( 'Designed by <a href="%s" target="_blank" rel="noopener noreferrer">TroveWP</a>', 'trove' ) ),
			esc_url( 'https://trovewp.com' )
		),
		'extra_lines'   => $footer_vals,
		'has_extra'     => $has_extra,
		'has_menu'      => has_nav_menu( 'footer' ),
		'footer_layout' => $footer_layout,
	);

	if ( ! in_array( $footer_layout, array( 'footer-1', 'footer-2', 'footer-3', 'footer-4' ), true ) ) {
		$footer_layout = 'footer-1';
	}

	get_template_part( 'template-parts/' . $footer_layout, null, $footer_args );
	?>
</footer>
<?php wp_footer(); ?>
</body>
</html>
