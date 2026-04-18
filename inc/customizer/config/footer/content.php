<?php
/**
 * Customizer fields: Footer → content.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

defined( 'ABSPATH' ) || exit;


// Build footer menu choices dynamically from registered menus.
$menus   = wp_get_nav_menus();
$choices = array(
	0 => __( 'None', 'trove' ),
);
if ( ! empty( $menus ) ) {
	foreach ( $menus as $menu ) {
		if ( isset( $menu->term_id, $menu->name ) ) {
			$choices[ (int) $menu->term_id ] = $menu->name;
		}
	}
}

$fields = array();

for ( $i = 1; $i <= 5; $i++ ) {
	$fields[ 'trove_footer_menu_' . $i . '_id' ] = array(
		/* translators: %d: Footer menu slot number. */
		'label'             => sprintf( __( 'Footer menu %d', 'trove' ), $i ),
		'description'       => 1 === $i ? __( 'Choose up to five menus to display as footer columns. Set to "None" to hide a column.', 'trove' ) : '',
		'type'              => 'select',
		'default'           => 0,
		'choices'           => $choices,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'absint',
	);
}

return $fields + array(
	'trove_footer_brand_heading_divider' => array(
		'label'             => __( 'Brand Information', 'trove' ),
		'type'              => 'heading',
		'transport'         => 'postMessage',
	),
	'trove_footer_show_brand_info' => array(
		'label'             => __( 'Show brand information', 'trove' ),
		'description'       => __( 'Show or hide the brand name and description block in the footer.', 'trove' ),
		'type'              => 'switch',
		'default'           => true,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'wp_validate_boolean',
	),
	'trove_footer_logo' => array(
		'label'             => __( 'Footer logo', 'trove' ),
		'description'       => __( 'Upload a logo shown above the footer brand text, replace the brand name text.', 'trove' ),
		'type'              => 'image',
		'default'           => '',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'esc_url_raw',
	),
	'trove_footer_brand_name' => array(
		'label'             => __( 'Brand name', 'trove' ),
		'description'       => __( 'Text shown as the footer brand title. Leave empty to use the site name.', 'trove' ),
		'type'              => 'text',
		'default'           => '',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
	),
	'trove_footer_brand_description' => array(
		'label'             => __( 'Brand description', 'trove' ),
		'description'       => __( 'Text shown below the footer brand title. Leave empty to use the site tagline.', 'trove' ),
		'type'              => 'textarea',
		'default'           => '',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'wp_kses_post',
	),
	'trove_footer_social_heading_divider' => array(
		'label'             => __( 'Social Links', 'trove' ),
		'type'              => 'heading',
		'transport'         => 'postMessage',
	),
	'trove_footer_show_social_links' => array(
		'label'             => __( 'Show social links', 'trove' ),
		'description'       => __( 'Show or hide the social links block in the footer.', 'trove' ),
		'type'              => 'switch',
		'default'           => true,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'wp_validate_boolean',
	),
	'trove_footer_newsletter_heading_divider' => array(
		'label'             => __( 'Newsletter', 'trove' ),
		'type'              => 'heading',
		'transport'         => 'postMessage',
	),
	'trove_footer_show_newsletter' => array(
		'label'             => __( 'Show Newsletter', 'trove' ),
		'description'       => __( 'Show or hide the newsletter block in the footer.', 'trove' ),
		'type'              => 'switch',
		'default'           => true,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'wp_validate_boolean',
	),
	'trove_footer_newsletter_title' => array(
		'label'             => __( 'Newsletter title', 'trove' ),
		'description'       => __( 'Text shown as the newsletter title.', 'trove' ),
		'type'              => 'text',
		'default'           => __('Subscribe to our emails', 'trove'),
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
	),
	'trove_footer_newsletter_description' => array(
		'label'             => __( 'Newsletter description', 'trove' ),
		'description'       => __( 'Text shown below the newsletter title.', 'trove' ),
		'type'              => 'textarea',
		'default'           => __('Subscribe to our newsletter to get the latest news and updates.', 'trove'),
		'transport'         => 'postMessage',
		'sanitize_callback' => 'wp_kses_post',
	),
	'trove_footer_newsletter_form' => array(
		'label'             => __( 'Newsletter form', 'trove' ),
		'description'       => __( 'Enter the form shortcode to display the newsletter form in the footer.', 'trove' ),
		'type'              => 'text',
		'default'           => '',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
	),
	'trove_footer_footer_bottom_heading_divider' => array(
		'label'             => __( 'Footer Bottom', 'trove' ),
		'type'              => 'heading',
		'transport'         => 'postMessage',
	),
	'trove_footer_copy_text' => array(
		'label'             => __( 'Copyright text', 'trove' ),
		'description'       => __( 'Text shown in the footer copyright line.', 'trove' ),
		'type'              => 'text',
		'default'           => '',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'sanitize_text_field',
	),
);