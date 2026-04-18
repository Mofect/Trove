<?php
/**
 * Theme hooks.
 *
 * @package Trove
 */

namespace Trove;

defined( 'ABSPATH' ) || exit;

/**
 * Loads the header template part for the current Customizer layout.
 *
 * @return void
 */
function render_header() {
	$layout = get_theme_mod( 'trove_header_layout', 'horizontal-left-logo' );
	$layout = is_string( $layout ) ? sanitize_key( $layout ) : 'horizontal-left-logo';

	if ( 'vertical-centered-logo' === $layout || 'vertical-left-logo' === $layout ) {
		get_template_part( 'template-parts/header-vertical' );
	} else {
		get_template_part( 'template-parts/header-horizontal' );
	}
}
add_action( 'trove_header', __NAMESPACE__ . '\\render_header' );

/**
 * Renders the theme footer markup.
 *
 * @return void
 */
function render_footer() {
	$footer_dark_mode = wp_validate_boolean( get_theme_mod( 'trove_footer_dark_mode', false ) );
	$footer_layout = get_theme_mod( 'trove_footer_layout', 'footer-1' );
	$footer_classes   = 'site-footer' . ( $footer_dark_mode ? '' : ' light-scheme' );

	?>
	<footer class="<?php echo esc_attr( $footer_classes ); ?>" role="contentinfo">
		<?php get_template_part( 'template-parts/' . $footer_layout, null ); ?>
	</footer>
	<?php
}
add_action( 'trove_footer', __NAMESPACE__ . '\\render_footer' );
