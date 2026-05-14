<?php
/**
 * Footer newsletter block from Customizer.
 *
 * @package Trove
 */

namespace Trove;

defined( 'ABSPATH' ) || exit;

/**
 * Whether the footer newsletter subscribe block should be rendered.
 *
 * @return bool
 */
function should_render_footer_newsletter() {
	return wp_validate_boolean( get_theme_mod( 'trove_footer_show_newsletter', (bool) \Trove\Customizer\trove_customizer_default( 'trove_footer_show_newsletter', false ) ) );
}

/**
 * Returns footer newsletter title with a default when the theme mod is empty.
 *
 * @return string
 */
function get_footer_newsletter_title() {
	$fallback = __( (string) \Trove\Customizer\trove_customizer_default( 'trove_footer_newsletter_title', 'Subscribe to our emails' ), 'trove' );
	$title    = get_theme_mod( 'trove_footer_newsletter_title', $fallback );
	return '' !== trim( (string) $title ) ? $title : $fallback;
}

/**
 * Returns footer newsletter description HTML/text with a default when the mod is empty.
 *
 * @return string
 */
function get_footer_newsletter_description() {
	$fallback = __( (string) \Trove\Customizer\trove_customizer_default( 'trove_footer_newsletter_description', 'Subscribe to our newsletter to get the latest news and updates.' ), 'trove' );
	$description = get_theme_mod( 'trove_footer_newsletter_description', $fallback );
	if ( '' !== trim( wp_strip_all_tags( (string) $description ) ) ) {
		return $description;
	}
	return $fallback;
}

/**
 * Outputs the footer newsletter subscribe form (placeholder action).
 *
 * @return void
 */
function render_footer_subscribe_form() {
?>
	<form class="site-footer__subscribe-form" action="#" method="post">
		<label class="screen-reader-text" for="trove-footer-email"><?php esc_html_e( 'Email address', 'trove' ); ?></label>
		<input
			id="trove-footer-email"
			class="site-footer__subscribe-input"
			type="email"
			name="email"
			placeholder="<?php esc_attr_e( 'Email address', 'trove' ); ?>"
		/>
		<button class="site-footer__subscribe-button" type="submit">
			<span class="screen-reader-text"><?php esc_html_e( 'Subscribe', 'trove' ); ?></span>
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
				<path d="M16.172 10.997 11.515 6.34a1 1 0 0 1 1.414-1.414L20 11.997l-7.071 7.071a1 1 0 0 1-1.414-1.414l4.657-4.657H5a1 1 0 1 1 0-2z" fill="currentColor"></path>
			</svg>
		</button>
	</form>
<?php
}
