<?php
/**
 * Customizer fields: Blog Posts → Layout.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

use function \Trove\Customizer\customizer_default;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_blog_layout' => array(
		'label'             => __( 'Blog layout', 'trove' ),
		'description'       => __( 'Layout for the blog home page and post archives (categories, tags, author, date).', 'trove' ),
		'type'              => 'image_radio',
		'default'           => (string) customizer_default( 'trove_blog_layout', 'list' ),
		'transport'         => 'postMessage',
		'choices'           => array(
			'list'    => array(
				'label' => __( 'List', 'trove' ),
				'url'   => \TROVE_URI . '/inc/customizer/img/blog-list.png',
			),
			'grid'    => array(
				'label' => __( '3-column grid', 'trove' ),
				'url'   => \TROVE_URI . '/inc/customizer/img/blog-grid.png',
			),
			'masonry' => array(
				'label' => __( '3-column masonry', 'trove' ),
				'url'   => \TROVE_URI . '/inc/customizer/img/blog-masonry.png',
			),
		),
		'sanitize_callback' => 'sanitize_text_field',
	),
);
