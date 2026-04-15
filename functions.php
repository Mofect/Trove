<?php
/**
 * Trove functions and definitions.
 *
 * @package Trove
 */

namespace Trove;

define( 'TROVE_THEME_VERSION', '1.0.0' );

require_once get_template_directory() . '/inc/customizer/customizer.php';

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function setup() {
	load_theme_textdomain( 'trove', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => esc_html__( 'Primary menu', 'trove' ),
		)
	);

	if ( class_exists( 'WooCommerce' ) ) {
		add_theme_support( 'woocommerce' );
	}

	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/fonts.css' );
	add_editor_style( 'style.css' );
}
add_action( 'after_setup_theme', __NAMESPACE__ . '\\setup' );

/**
 * Enqueues the theme stylesheet.
 */
function enqueue_styles() {
	wp_enqueue_style(
		'trove-fonts',
		get_template_directory_uri() . '/assets/css/fonts.css',
		array(),
		TROVE_THEME_VERSION
	);
	wp_enqueue_style(
		'trove-style',
		get_stylesheet_uri(),
		array( 'trove-fonts' ),
		TROVE_THEME_VERSION
	);
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_styles' );

/**
 * Registers custom block style variations (for block editor content).
 */
function register_block_styles() {
	register_block_style(
		'core/group',
		array(
			'name'  => 'shadow',
			'label' => esc_html__( 'Shadow', 'trove' ),
		)
	);

	register_block_style(
		'core/button',
		array(
			'name'  => 'outline',
			'label' => esc_html__( 'Outline', 'trove' ),
		)
	);
}
add_action( 'init', __NAMESPACE__ . '\\register_block_styles' );

/**
 * Replaces the default WooCommerce customer account icon with a custom SVG.
 *
 * @param string $block_content The block content.
 * @return string Modified block content.
 */
function replace_account_icon( $block_content ) {
	$custom_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 20 20" fill="none"><path d="M13.654 5.972a3.683 3.683 0 1 1-7.366 0 3.683 3.683 0 0 1 7.366 0Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"></path><path d="M10 12.352c2.18-.006 4.098.797 5.208 2.504.502.772.114 1.775-.743 2.109-1.37.532-2.88.75-4.465.746-1.584.004-3.095-.214-4.465-.746-.858-.334-1.245-1.337-.743-2.11C5.9 13.149 7.817 12.347 10 12.353Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"></path></svg>';

	$block_content = preg_replace(
		'/<svg[^>]*>.*?<\/svg>/s',
		$custom_icon,
		$block_content,
		1
	);

	return $block_content;
}
add_filter( 'render_block_woocommerce/customer-account', __NAMESPACE__ . '\\replace_account_icon', 20 );

/**
 * Replaces the default WooCommerce mini-cart icon with a custom SVG at 20x20.
 *
 * @param string $block_content The block content.
 * @return string Modified block content.
 */
function replace_mini_cart_icon( $block_content ) {
	$custom_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16" fill="none"><path d="M11.1322 4.15145C11.1322 2.41555 9.72495 1.00833 7.98905 1.00833C7.15314 1.00479 6.35025 1.33437 5.75792 1.9242C5.16559 2.51404 4.83261 3.31553 4.83262 4.15145M11.0322 14.9918H4.9591C2.72828 14.9918 1.01688 14.186 1.503 10.943L2.06903 6.54799C2.36869 4.92982 3.40086 4.31052 4.3065 4.31052H11.7115C12.6304 4.31052 13.6027 4.97644 13.9489 6.54799L14.515 10.943C14.9278 13.8198 13.263 14.9918 11.0322 14.9918Z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M10.1969 7.43457H10.1663M6.30981 7.43457H6.2793" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path></svg>';

	$block_content = preg_replace(
		'/<svg[^>]*class="[^"]*wc-block-mini-cart__icon[^"]*"[^>]*>.*?<\/svg>/s',
		$custom_icon,
		$block_content,
		1
	);

	return $block_content;
}
add_filter( 'render_block_woocommerce/mini-cart', __NAMESPACE__ . '\\replace_mini_cart_icon', 20 );
