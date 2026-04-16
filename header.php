<?php
/**
 * Theme header.
 *
 * @package Trove
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style>
		:root {
			--trove-body-font-family: "<?php echo get_theme_mod( 'trove_font_base_stack', 'instrument_sans' ); ?>", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
			--trove-body-font-size: <?php echo get_theme_mod( 'trove_font_base_size', '16px' ); ?>;
			--trove-body-font-weight: <?php echo get_theme_mod( 'trove_font_base_weight', '400' ); ?>;
			--trove-body-line-height: <?php echo get_theme_mod( 'trove_font_base_line_height', '1.5' ); ?>;
		}
	</style>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php 
$layout = get_theme_mod( 'trove_header_layout', 'horizontal-left-logo' );
$layout = is_string( $layout ) ? sanitize_key( $layout ) : 'horizontal-left-logo';

if ( 'vertical-centered-logo' === $layout || 'vertical-left-logo' === $layout ) {
	get_template_part( 'template-parts/header-vertical' );
} else {
	get_template_part( 'template-parts/header-horizontal' );
}