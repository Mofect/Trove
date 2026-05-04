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
		add_filter( 'body_class', array( __CLASS__, 'filter_body_class_header_logo' ) );
		add_filter( 'body_class', array( __CLASS__, 'filter_body_class_blog_layout' ) );
		add_filter( 'body_class', array( __CLASS__, 'filter_body_class_blog_boxed' ) );
		add_filter( 'body_class', array( __CLASS__, 'filter_body_class_blog_pagination' ) );
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
			$family = \Trove\Customizer\sanitize_google_font_family(
				get_theme_mod(
					$setting_id,
					(string) \Trove\Customizer\trove_customizer_default( $setting_id, 'system' )
				)
			);
			if ( 'system' === $family ) {
				continue;
			}
			$families[ $family ] = true;
		}
		return array_keys( $families );
	}

	/**
	 * Adds `has-custom-logo` when the Header → Layout logo is set (without Site Identity logo).
	 *
	 * @param string[] $classes Body classes.
	 * @return string[]
	 */
	public static function filter_body_class_header_logo( $classes ) {
		if ( ! is_array( $classes ) ) {
			$classes = array();
		}
		if (
			'' !== \Trove\get_header_logo_url() &&
			! in_array( 'has-custom-logo', $classes, true )
		) {
			$classes[] = 'has-custom-logo';
		}
		return $classes;
	}

	/**
	 * Adds `trove-blog-layout--{list|grid|masonry}` on blog index and post archives.
	 *
	 * @param string[] $classes Body classes.
	 * @return string[]
	 */
	public static function filter_body_class_blog_layout( $classes ) {
		if ( ! is_array( $classes ) ) {
			$classes = array();
		}
		if ( ! \Trove\is_blog_listing_view() ) {
			return $classes;
		}
		$classes[] = 'trove-blog-layout--' . \Trove\get_blog_layout();
		return $classes;
	}

	/**
	 * Adds `trove-blog-boxed` when Boxed style is enabled (blog index/archives only).
	 *
	 * @param string[] $classes Body classes.
	 * @return string[]
	 */
	public static function filter_body_class_blog_boxed( $classes ) {
		if ( ! is_array( $classes ) ) {
			$classes = array();
		}
		if ( ! \Trove\is_blog_listing_view() || ! \Trove\is_blog_boxed_style() ) {
			return $classes;
		}
		$classes[] = 'trove-blog-boxed';
		return $classes;
	}

	/**
	 * Adds `trove-blog-pagination--paged` or `trove-blog-pagination--infinite` on blog listings.
	 *
	 * @param string[] $classes Body classes.
	 * @return string[]
	 */
	public static function filter_body_class_blog_pagination( $classes ) {
		if ( ! is_array( $classes ) ) {
			$classes = array();
		}
		if ( ! \Trove\is_blog_listing_view() ) {
			return $classes;
		}
		$classes[] = 'infinite_scroll' === \Trove\get_blog_pagination_type()
			? 'trove-blog-pagination--infinite'
			: 'trove-blog-pagination--paged';
		return $classes;
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
			add_editor_style( 'assets/css/shop.css' );
		}
	}

	/**
	 * Whether minified assets should be loaded.
	 *
	 * @return bool
	 */
	public static function should_load_minified_assets() {
		return wp_validate_boolean(
			get_theme_mod(
				'trove_perf_minify_assets',
				(bool) \Trove\Customizer\trove_customizer_default( 'trove_perf_minify_assets', true )
			)
		);
	}

	/**
	 * Returns the asset URL, using .min version when minification is enabled.
	 *
	 * @param string $relative_path Relative path under theme root (e.g. 'assets/css/base.css').
	 * @return string
	 */
	public static function asset_url( $relative_path ) {
		$relative_path = ltrim( $relative_path, '/' );
		if ( ! self::should_load_minified_assets() ) {
			return get_template_directory_uri() . '/' . $relative_path;
		}
		$ext = pathinfo( $relative_path, PATHINFO_EXTENSION );
		$min_path = preg_replace( '/\.(' . preg_quote( $ext, '/' ) . ')$/', '.min.' . $ext, $relative_path );
		$min_file = get_template_directory() . '/' . $min_path;
		if ( file_exists( $min_file ) ) {
			return get_template_directory_uri() . '/' . $min_path;
		}
		return get_template_directory_uri() . '/' . $relative_path;
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
			self::asset_url( 'assets/css/base.css' ),
			$deps,
			TROVE_THEME_VERSION
		);
		if ( class_exists( 'WooCommerce' ) ) {
			wp_enqueue_style(
				'trove-woocommerce',
				self::asset_url( 'assets/css/shop.css' ),
				array( 'trove-style' ),
				TROVE_THEME_VERSION
			);
		}

		wp_enqueue_script(
			'trove-header-sticky',
			self::asset_url( 'assets/js/header-sticky.js' ),
			array(),
			TROVE_THEME_VERSION,
			true
		);

		wp_enqueue_script(
			'trove-header-search-toggle',
			self::asset_url( 'assets/js/header-search-toggle.js' ),
			array(),
			TROVE_THEME_VERSION,
			true
		);

		wp_enqueue_script(
			'trove-header-menu-toggle',
			self::asset_url( 'assets/js/header-menu-toggle.js' ),
			array(),
			TROVE_THEME_VERSION,
			true
		);

		wp_enqueue_script(
			'trove-footer-menu-toggle',
			self::asset_url( 'assets/js/footer-menu-toggle.js' ),
			array(),
			TROVE_THEME_VERSION,
			true
		);

		if ( class_exists( 'WooCommerce' ) && function_exists( 'is_product' ) && is_product() ) {
			$ajax_cart_deps = array( 'jquery' );
			wp_enqueue_script(
				'sticksy',
				get_template_directory_uri() . '/assets/js/sticksy.min.js',
				array(),
				TROVE_THEME_VERSION,
				true
			);
			$ajax_cart_deps[] = 'sticksy';
			// Let Woo add localized params; ensures fragment + legacy events behave like core.
			if ( wp_script_is( 'wc-add-to-cart', 'registered' ) ) {
				wp_enqueue_script( 'wc-add-to-cart' );
				$ajax_cart_deps[] = 'wc-add-to-cart';
			}
			if ( wp_script_is( 'wc-cart-fragments', 'registered' ) ) {
				wp_enqueue_script( 'wc-cart-fragments' );
				$ajax_cart_deps[] = 'wc-cart-fragments';
			}
			if ( wp_script_is( 'wp-data', 'registered' ) ) {
				wp_enqueue_script( 'wp-data' );
				$ajax_cart_deps[] = 'wp-data';
			}
			// After Woo (loads wp-data + block cart store when mini-cart is present).
			if ( wp_script_is( 'wc-block-mini-cart', 'registered' ) ) {
				wp_enqueue_script( 'wc-block-mini-cart' );
				$ajax_cart_deps[] = 'wc-block-mini-cart';
			}

			wp_enqueue_script(
				'trove-product-ajax-cart',
				self::asset_url( 'assets/js/shop.js' ),
				array_values( array_unique( $ajax_cart_deps ) ),
				TROVE_THEME_VERSION,
				true
			);
			wp_enqueue_script(
				'trove-product-review-dialog',
				self::asset_url( 'assets/js/product-review-dialog.js' ),
				array(),
				TROVE_THEME_VERSION,
				true
			);
			wp_localize_script(
				'trove-product-ajax-cart',
				'troveProductAjaxCart',
				array(
					'ajaxUrl'         => \WC_AJAX::get_endpoint( 'add_to_cart' ),
					'storeCartUrl'    => rest_url( 'wc/store/v1/cart' ),
					'storeApiNonce'   => wp_create_nonce( 'wc_store_api' ),
				)
			);
		}

		global $wp_query;
		$blog_infinite = \Trove\is_blog_listing_view()
			&& 'infinite_scroll' === \Trove\get_blog_pagination_type()
			&& ! is_customize_preview()
			&& isset( $wp_query )
			&& (int) $wp_query->max_num_pages > 1
			&& \Trove\get_blog_list_paged() < (int) $wp_query->max_num_pages;

		if ( $blog_infinite ) {
			wp_enqueue_script(
				'trove-blog-infinite',
				self::asset_url( 'assets/js/blog-infinite-scroll.js' ),
				array(),
				TROVE_THEME_VERSION,
				true
			);
			wp_localize_script(
				'trove-blog-infinite',
				'troveInfiniteScroll',
				array(
					'loading' => __( 'Loading…', 'trove' ),
					'error'   => __( 'Could not load more posts.', 'trove' ),
				)
			);
		}
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
		$custom_icon = '<svg width="23" height="23" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 21C5 17.134 8.13401 14 12 14C15.866 14 19 17.134 19 21M16 7C16 9.20914 14.2091 11 12 11C9.79086 11 8 9.20914 8 7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

		$block_content = preg_replace(
			'/<svg[^>]*>.*?<\/svg>/s',
			$custom_icon,
			$block_content,
			1
		);

		return $block_content;
	}

	/**
	 * Replaces the default WooCommerce mini-cart icon with a custom SVG.
	 *
	 * @param string $block_content The block content.
	 * @return string
	 */
	public static function replace_mini_cart_icon( $block_content ) {
		$custom_icon = '<svg class="wc-block-mini-cart__icon" width="25" height="25" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 11.01V11M16 11.01V11M8 8V7C8 5.93913 8.42143 4.92172 9.17157 4.17157C9.92172 3.42143 10.9391 3 12 3C13.0609 3 14.0783 3.42143 14.8284 4.17157C15.5786 4.92172 16 5.93913 16 7V8M8 8H6.84027C5.80009 8 4.93356 8.79732 4.84718 9.83391L4.18051 17.8339C4.08334 18.9999 5.00352 20 6.1736 20H17.8264C18.9965 20 19.9167 18.9999 19.8195 17.8339L19.1528 9.83391C19.0664 8.79732 18.1999 8 17.1597 8H16M8 8H16" transform="translate(0 1)" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

		$block_content = preg_replace(
			'/<svg[^>]*class="[^"]*wc-block-mini-cart__icon[^"]*"[^>]*>.*?<\/svg>/s',
			$custom_icon,
			$block_content,
			1
		);

		return $block_content;
	}
}
