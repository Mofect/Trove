<?php
/**
 * Theme bootstrap and runtime hooks.
 *
 * @package Trove
 */

namespace Trove;

defined( 'ABSPATH' ) || exit;

/**
 * Theme lifecycle and integrations.
 */
class TroveTheme {

	/**
	 * Registers all theme hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'after_setup_theme', array( __CLASS__, 'setup' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_styles' ) );
		add_action( 'init', array( __CLASS__, 'register_block_styles' ) );
		add_filter( 'render_block_woocommerce/customer-account', array( __CLASS__, 'replace_account_icon' ), 20 );
		add_filter( 'render_block_woocommerce/mini-cart', array( __CLASS__, 'replace_mini_cart_icon' ), 20 );
	}

	/**
	 * All selected Google font families across body/headings/blockquote.
	 *
	 * @return string[]
	 */
	public static function get_selected_google_font_families() {
		$ids = array(
			'trove_google_font_family',
			'trove_menu_google_font_family',
		);
		foreach ( array_keys( \Trove\Customizer\trove_typography_font_scope_meta() ) as $scope ) {
			$ids[] = 'trove_' . $scope . '_google_font_family';
		}
		$families = array();
		foreach ( $ids as $setting_id ) {
			$family = \Trove\Customizer\sanitize_google_font_family( get_theme_mod( $setting_id, 'system' ) );
			if ( 'system' === $family ) {
				continue;
			}
			$families[ $family ] = true;
		}
		return array_keys( $families );
	}

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * @return void
	 */
	public static function setup() {
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
		foreach ( self::get_selected_google_font_families() as $family ) {
			$gf = \Trove\Customizer\get_google_font_stylesheet_url( $family );
			if ( $gf ) {
				add_editor_style( $gf );
			}
		}
		add_editor_style( 'assets/css/base.css' );
		if ( class_exists( 'WooCommerce' ) ) {
			add_editor_style( 'assets/css/woocommerce.css' );
		}
	}

	/**
	 * Enqueues theme styles/scripts.
	 *
	 * @return void
	 */
	public static function enqueue_styles() {
		$deps = array();
		foreach ( self::get_selected_google_font_families() as $i => $family ) {
			$gf = \Trove\Customizer\get_google_font_stylesheet_url( $family );
			if ( ! $gf ) {
				continue;
			}
			$handle = 'trove-google-font-' . $i;
			wp_enqueue_style( $handle, $gf, array(), TROVE_THEME_VERSION );
			$deps[] = $handle;
		}
		wp_enqueue_style(
			'trove-style',
			get_template_directory_uri() . '/assets/css/base.css',
			$deps,
			TROVE_THEME_VERSION
		);
		if ( class_exists( 'WooCommerce' ) ) {
			wp_enqueue_style(
				'trove-woocommerce',
				get_template_directory_uri() . '/assets/css/woocommerce.css',
				array( 'trove-style' ),
				TROVE_THEME_VERSION
			);
		}

		wp_enqueue_script(
			'trove-header-sticky',
			get_template_directory_uri() . '/assets/js/header-sticky.js',
			array(),
			TROVE_THEME_VERSION,
			true
		);
	}

	/**
	 * Registers custom block style variations (for block editor content).
	 *
	 * @return void
	 */
	public static function register_block_styles() {
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

	/**
	 * Replaces the default WooCommerce customer account icon with a custom SVG.
	 *
	 * @param string $block_content The block content.
	 * @return string
	 */
	public static function replace_account_icon( $block_content ) {
		$custom_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 20 20" fill="none"><path d="M13.654 5.972a3.683 3.683 0 1 1-7.366 0 3.683 3.683 0 0 1 7.366 0Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"></path><path d="M10 12.352c2.18-.006 4.098.797 5.208 2.504.502.772.114 1.775-.743 2.109-1.37.532-2.88.75-4.465.746-1.584.004-3.095-.214-4.465-.746-.858-.334-1.245-1.337-.743-2.11C5.9 13.149 7.817 12.347 10 12.353Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"></path></svg>';

		$block_content = preg_replace(
			'/<svg[^>]*>.*?<\/svg>/s',
			$custom_icon,
			$block_content,
			1
		);

		return $block_content;
	}

	/**
	 * Replaces the default WooCommerce mini-cart icon with a custom SVG at 20x20.
	 *
	 * @param string $block_content The block content.
	 * @return string
	 */
	public static function replace_mini_cart_icon( $block_content ) {
		$custom_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16" fill="none"><path d="M11.1322 4.15145C11.1322 2.41555 9.72495 1.00833 7.98905 1.00833C7.15314 1.00479 6.35025 1.33437 5.75792 1.9242C5.16559 2.51404 4.83261 3.31553 4.83262 4.15145M11.0322 14.9918H4.9591C2.72828 14.9918 1.01688 14.186 1.503 10.943L2.06903 6.54799C2.36869 4.92982 3.40086 4.31052 4.3065 4.31052H11.7115C12.6304 4.31052 13.6027 4.97644 13.9489 6.54799L14.515 10.943C14.9278 13.8198 13.263 14.9918 11.0322 14.9918Z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M10.1969 7.43457H10.1663M6.30981 7.43457H6.2793" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path></svg>';

		$block_content = preg_replace(
			'/<svg[^>]*class="[^"]*wc-block-mini-cart__icon[^"]*"[^>]*>.*?<\/svg>/s',
			$custom_icon,
			$block_content,
			1
		);

		return $block_content;
	}
}
