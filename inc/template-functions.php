<?php
/**
 * Template helper functions.
 *
 * @package Trove
 */

namespace Trove;

defined( 'ABSPATH' ) || exit;

/**
 * Page settings: transparent header class on singular posts/pages/products.
 *
 * @param string[] $classes Header class names.
 * @return string[]
 */
function filter_header_classnames_transparent( $classes ) {
	if ( ! is_array( $classes ) ) {
		$classes = array();
	}
	if ( ! is_singular() ) {
		return $classes;
	}
	$post_id = (int) get_queried_object_id();
	if ( $post_id < 1 ) {
		return $classes;
	}
	$on = \Trove\Metabox\get_post_field_value( $post_id, 'page_settings', 'transparent_header', false );
	if ( wp_validate_boolean( $on ) ) {
		$classes[] = 'trove-header--transparent';
	}
	return $classes;
}
add_filter( 'trove_header_classnames', __NAMESPACE__ . '\\filter_header_classnames_transparent', 15 );


/**
 * Header logo URL from Customizer (empty when not set).
 *
 * When set, it is shown instead of the Site Identity logo and site title.
 *
 * @return string
 */
function get_header_logo_url() {
	$logo_url = get_theme_mod( 'trove_header_logo', (string) \Trove\Customizer\customizer_default( 'trove_header_logo', '' ) );
	return is_string( $logo_url ) ? trim( $logo_url ) : '';
}

/**
 * Adds `has-custom-logo` when the Header → Layout logo is set (without Site Identity logo).
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function filter_body_class_header_logo( $classes ) {
	if ( ! is_array( $classes ) ) {
		$classes = array();
	}
	if (
		'' !== get_header_logo_url() &&
		! in_array( 'has-custom-logo', $classes, true )
	) {
		$classes[] = 'has-custom-logo';
	}
	return $classes;
}
add_filter( 'body_class', __NAMESPACE__ . '\\filter_body_class_header_logo' );

/**
 * Resolved theme header logo URL, or null to use Site Identity logo or title.
 *
 * @return array{type: 'single', url: string}|null
 */
function get_header_brand_logo_config() {
	$light = get_header_logo_url();

	if ( '' !== $light ) {
		return array(
			'type' => 'single',
			'url'  => $light,
		);
	}

	return null;
}

/**
 * Header logo max width in pixels (clamped to Customizer range).
 *
 * @return int
 */
function get_header_logo_width() {
	$w = absint( get_theme_mod( 'trove_header_logo_width', (int) \Trove\Customizer\customizer_default( 'trove_header_logo_width', 200 ) ) );
	return min( 400, max( 40, $w ) );
}

/**
 * Sanitizes header logo width theme_mod (40–400 px).
 *
 * @param mixed $value Raw value.
 * @return int
 */
function sanitize_header_logo_width( $value ) {
	$w = absint( $value );
	return min( 400, max( 40, $w ) );
}

/**
 * Loads the header template part for the current Customizer layout.
 *
 * @return void
 */
function render_header() {
	$layout = get_theme_mod( 'trove_header_layout', (string) \Trove\Customizer\customizer_default( 'trove_header_layout', 'horizontal-left-logo' ) );
	$layout = is_string( $layout ) ? sanitize_key( $layout ) : 'horizontal-left-logo';

	if ( 'vertical-centered-logo' === $layout || 'vertical-left-logo' === $layout ) {
		get_template_part( 'template-parts/header-vertical' );
	} else {
		get_template_part( 'template-parts/header-horizontal' );
	}
}
add_action( 'trove_header', __NAMESPACE__ . '\\render_header' );

/**
 * Footer menu term IDs from Customizer slots (1–5), in order.
 *
 * @return array<int> Menu term IDs (0 = empty slot).
 */
function get_footer_menu_slot_ids() {
	$menu_ids = array();
	for ( $i = 1; $i <= 5; $i++ ) {
		$menu_ids[] = absint( get_theme_mod( 'trove_footer_menu_' . $i . '_id', (int) \Trove\Customizer\customizer_default( 'trove_footer_menu_' . $i . '_id', 0 ) ) );
	}
	return $menu_ids;
}

/**
 * Resolves footer menu IDs to nav menu objects (preserves order, allows duplicates).
 *
 * @param array<int|string|mixed> $menu_ids Raw menu term IDs.
 * @return array<\WP_Term> Valid menu objects.
 */
function get_footer_menus( $menu_ids ) {
	$raw_ids = is_array( $menu_ids ) ? $menu_ids : array();
	$menus   = array();
	foreach ( $raw_ids as $maybe_id ) {
		$id       = absint( $maybe_id );
		$menu_obj = $id > 0 ? wp_get_nav_menu_object( $id ) : null;
		if ( $menu_obj && isset( $menu_obj->term_id, $menu_obj->name ) ) {
			$menus[] = $menu_obj;
		}
	}
	return $menus;
}

/**
 * Returns footer brand name with site-name fallback.
 *
 * @return string
 */
function get_footer_brand_name() {
	$brand_name = get_theme_mod( 'trove_footer_brand_name', (string) \Trove\Customizer\customizer_default( 'trove_footer_brand_name', '' ) );
	return '' !== trim( $brand_name ) ? $brand_name : get_bloginfo( 'name' );
}

/**
 * Returns footer brand description with site-tagline fallback.
 *
 * @return string
 */
function get_footer_brand_description() {
	$brand_description = get_theme_mod( 'trove_footer_brand_description', (string) \Trove\Customizer\customizer_default( 'trove_footer_brand_description', '' ) );
	return '' !== trim( $brand_description ) ? $brand_description : get_bloginfo( 'description' );
}

/**
 * Whether footer brand text information should be rendered.
 *
 * @return bool
 */
function should_render_footer_brand_info() {
	return wp_validate_boolean( get_theme_mod( 'trove_footer_show_brand_info', (bool) \Trove\Customizer\customizer_default( 'trove_footer_show_brand_info', false ) ) );
}

/**
 * Returns footer logo URL (empty when not set).
 *
 * @return string
 */
function get_footer_logo_url() {
	$logo_url = get_theme_mod( 'trove_footer_logo', (string) \Trove\Customizer\customizer_default( 'trove_footer_logo', '' ) );
	return is_string( $logo_url ) ? trim( $logo_url ) : '';
}

/**
 * Renders the theme footer markup.
 *
 * @return void
 */
function render_footer() {
	$footer_layout = get_theme_mod( 'trove_footer_layout', (string) \Trove\Customizer\customizer_default( 'trove_footer_layout', 'footer-1' ) );
	$footer_classes   = array( 'site-footer', 'light-scheme' );
	if ( wp_validate_boolean( get_theme_mod( 'trove_footer_top_border', (bool) \Trove\Customizer\customizer_default( 'trove_footer_top_border', true ) ) ) ) {
		$footer_classes[] = 'trove-footer-has-top-border';
	}

	?>
	<footer class="<?php echo esc_attr( implode( ' ', $footer_classes ) ); ?>" role="contentinfo">
		<?php get_template_part( 'template-parts/' . $footer_layout, null ); ?>
	</footer>
	<?php
}
add_action( 'trove_footer', __NAMESPACE__ . '\\render_footer' );