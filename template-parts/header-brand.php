<?php
/**
 * Header brand: theme logo, Customizer site logo, or site title.
 *
 * @package Trove
 */

defined( 'ABSPATH' ) || exit;

$config = \Trove\get_header_brand_logo_config();

if ( is_array( $config ) && isset( $config['type'], $config['url'] ) && 'single' === $config['type'] && '' !== $config['url'] ) {
	?>
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="custom-logo-link site-header__logo-link" rel="home">
		<img class="custom-logo site-header__logo" src="<?php echo esc_url( $config['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
	</a>
	<?php
} elseif ( has_custom_logo() ) {
	the_custom_logo();
} else {
	?>
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-header__title-link"><?php bloginfo( 'name' ); ?></a>
	<?php
}
