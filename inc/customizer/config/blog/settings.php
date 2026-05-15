<?php
/**
 * Customizer fields: Blog Posts section.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

use function \Trove\Customizer\customizer_default;

defined( 'ABSPATH' ) || exit;

/**
 * @param mixed $value Raw value.
 * @return string top|left|right
 */
function sanitize_blog_list_featured_position( $value ) {
	$v = sanitize_key( (string) $value );
	return in_array( $v, array( 'top', 'left', 'right' ), true ) ? $v : 'top';
}

/**
 * @param mixed $value Raw value.
 * @return string paged|infinite_scroll
 */
function sanitize_blog_pagination_type( $value ) {
	$v = sanitize_key( (string) $value );
	return in_array( $v, array( 'paged', 'infinite_scroll' ), true ) ? $v : 'paged';
}

return array(
	'trove_blog_pagination_type' => array(
		'label'             => __( 'Pagination type', 'trove' ),
		'description'       => __( 'Paged shows numbered links. Infinite scroll loads the next page of posts as you scroll down.', 'trove' ),
		'type'              => 'select',
		'default'           => (string) customizer_default( 'trove_blog_pagination_type', 'paged' ),
		'transport'         => 'refresh',
		'choices'           => array(
			'paged'           => __( 'Paged', 'trove' ),
			'infinite_scroll' => __( 'Infinite scroll', 'trove' ),
		),
		'sanitize_callback' => __NAMESPACE__ . '\\sanitize_blog_pagination_type',
	),
	'trove_blog_list_featured_position' => array(
		'label'             => __( 'Featured image position', 'trove' ),
		'description'       => __( 'Applies when Blog layout is set to List.', 'trove' ),
		'type'              => 'select',
		'default'           => (string) customizer_default( 'trove_blog_list_featured_position', 'top' ),
		'transport'         => 'postMessage',
		'choices'           => array(
			'top'   => __( 'Top', 'trove' ),
			'left'  => __( 'Left', 'trove' ),
			'right' => __( 'Right', 'trove' ),
		),
		'sanitize_callback' => __NAMESPACE__ . '\\sanitize_blog_list_featured_position',
	),
	'trove_blog_show_featured_image'    => array(
		'label'             => __( 'Featured image', 'trove' ),
		'description'       => __( 'Show the featured image in blog listings.', 'trove' ),
		'type'              => 'switch',
		'default'           => (bool) customizer_default( 'trove_blog_show_featured_image', true ),
		'transport'         => 'postMessage',
		'sanitize_callback' => 'wp_validate_boolean',
	),
	'trove_blog_show_date'              => array(
		'label'             => __( 'Date', 'trove' ),
		'description'       => __( 'Show the publication date in blog listings.', 'trove' ),
		'type'              => 'switch',
		'default'           => (bool) customizer_default( 'trove_blog_show_date', true ),
		'transport'         => 'postMessage',
		'sanitize_callback' => 'wp_validate_boolean',
	),
	'trove_blog_show_author'          => array(
		'label'             => __( 'Author', 'trove' ),
		'description'       => __( 'Show the author name in blog listings.', 'trove' ),
		'type'              => 'switch',
		'default'           => (bool) customizer_default( 'trove_blog_show_author', true ),
		'transport'         => 'postMessage',
		'sanitize_callback' => 'wp_validate_boolean',
	),
	'trove_blog_show_continue_reading' => array(
		'label'             => __( 'Continue reading', 'trove' ),
		'description'       => __( 'Show the “Continue reading” link in blog listings.', 'trove' ),
		'type'              => 'switch',
		'default'           => (bool) customizer_default( 'trove_blog_show_continue_reading', true ),
		'transport'         => 'postMessage',
		'sanitize_callback' => 'wp_validate_boolean',
	),
);
