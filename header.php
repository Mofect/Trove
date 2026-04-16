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
	<?php
	/*
	 * Body typography uses --trove-body-font-family and --trove-font-base-* from
	 * style.css defaults plus wp_add_inline_style( 'trove-style' ) in apply.php.
	 * Avoid duplicating :root here so Customizer preview postMessage is not overridden.
	 */
	wp_head();
	?>
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