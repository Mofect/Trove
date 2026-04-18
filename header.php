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
	 * the inline CSS generated in apply.php.
	 * Avoid duplicating :root here so Customizer preview postMessage is not overridden.
	 */
	wp_head();
	?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php do_action( 'trove_announcement_bar' ); ?>
<?php do_action( 'trove_header' ); ?>