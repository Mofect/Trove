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
	 * Loads theme PHP partials (Customizer, components, WooCommerce, performance).
	 *
	 * @return void
	 */
	private static function load_dependencies() {
		require_once \TROVE_DIR . '/inc/customizer/customizer.php';
		require_once \TROVE_DIR . '/inc/metabox/metabox.php';
		require_once \TROVE_DIR . '/inc/template-functions.php';
		require_once \TROVE_DIR . '/inc/woocommerce.php';
		require_once \TROVE_DIR . '/inc/performance.php';
		require_once \TROVE_DIR . '/inc/components/social-links.php';
		require_once \TROVE_DIR . '/inc/components/newsletter.php';
		require_once \TROVE_DIR . '/inc/components/Announcement-bar.php';
		require_once \TROVE_DIR . '/inc/components/blog.php';
		require_once \TROVE_DIR . '/inc/components/woocommerce/product-gallery.php';
		require_once \TROVE_DIR . '/inc/components/woocommerce/product-video.php';
		require_once \TROVE_DIR . '/inc/components/woocommerce/product-tabs-collapsible.php';
		require_once \TROVE_DIR . '/inc/components/woocommerce/grouped-product.php';
		require_once \TROVE_DIR . '/inc/components/woocommerce/review-media.php';
		require_once \TROVE_DIR . '/inc/components/woocommerce/orders.php';
		require_once \TROVE_DIR . '/inc/components/woocommerce/free-shipping-bar.php';
		require_once \TROVE_DIR . '/inc/components/woocommerce/cart-cross-sells.php';
		require_once \TROVE_DIR . '/inc/components/woocommerce/product-carousel.php';
	}

	/**
	 * Registers all theme hooks.
	 *
	 * @return void
	 */
	public static function init() {
		self::load_dependencies();
		add_action( 'init', array( __CLASS__, 'load_theme_translations' ), 0 );
		add_action( 'init', array( __CLASS__, 'register_theme_nav_menus' ), 5 );
		add_action( 'after_setup_theme', array( __CLASS__, 'setup' ) );
		add_action( 'widgets_init', array( __CLASS__, 'register_theme_sidebars' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_styles' ) );
		add_action( 'init', array( __CLASS__, 'register_block_styles' ) );
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
		foreach ( array_keys( \Trove\Customizer\typography_font_scope_meta() ) as $scope ) {
			$ids[] = 'trove_' . $scope . '_google_font_family';
		}
		$families = array();
		foreach ( $ids as $setting_id ) {
			$family = \Trove\Customizer\sanitize_google_font_family(
				get_theme_mod(
					$setting_id,
					(string) \Trove\Customizer\customizer_default( $setting_id, 'system' )
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
	 * Loads theme translations (must run on init or later — WordPress 6.7+).
	 *
	 * @return void
	 */
	public static function load_theme_translations() {
		load_theme_textdomain( 'trove', \TROVE_DIR . '/languages' );
	}

	/**
	 * Registers navigation menus (runs on init so translated labels load after textdomain — WP 6.7+).
	 *
	 * @return void
	 */
	public static function register_theme_nav_menus() {
		register_nav_menus(
			array(
				'primary' => esc_html__( 'Primary menu', 'trove' ),
			)
		);
	}

	/**
	 * Registers widget areas (sidebars).
	 *
	 * @return void
	 */
	public static function register_theme_sidebars() {
		register_sidebar(
			array(
				'name'          => esc_html__( 'Blog Sidebar', 'trove' ),
				'id'            => 'blog-sidebar',
				'description'   => esc_html__( 'Appears beside blog listings and post archives when your layout includes a sidebar.', 'trove' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);

		register_sidebar(
			array(
				'name'          => esc_html__( 'WooCommerce Sidebar', 'trove' ),
				'id'            => 'woocommerce-sidebar',
				'description'   => esc_html__( 'Appears on shop and product archive layouts when your layout includes a sidebar.', 'trove' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * @return void
	 */
	public static function setup() {
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'align-wide' );
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
		$editor_base = 'assets/css/base.css';
		if ( self::should_load_minified_assets() ) {
			$min_base = 'assets/css/base.min.css';
			if ( is_file( \TROVE_DIR . '/' . $min_base ) ) {
				$editor_base = $min_base;
			}
		}
		add_editor_style( $editor_base );
		if ( class_exists( 'WooCommerce' ) ) {
			$editor_shop = 'assets/css/shop.css';
			if ( self::should_load_minified_assets() ) {
				$min_shop = 'assets/css/shop.min.css';
				if ( is_file( \TROVE_DIR . '/' . $min_shop ) ) {
					$editor_shop = $min_shop;
				}
			}
			add_editor_style( $editor_shop );
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
				(bool) \Trove\Customizer\customizer_default( 'trove_perf_minify_assets', true )
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
			return \TROVE_URI . '/' . $relative_path;
		}
		$ext = pathinfo( $relative_path, PATHINFO_EXTENSION );
		$min_path = preg_replace( '/\.(' . preg_quote( $ext, '/' ) . ')$/', '.min.' . $ext, $relative_path );
		$min_file = \TROVE_DIR . '/' . $min_path;
		if ( file_exists( $min_file ) ) {
			return \TROVE_URI . '/' . $min_path;
		}
		return \TROVE_URI . '/' . $relative_path;
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

		if ( class_exists( 'WooCommerce' ) && function_exists( 'is_product' ) ) {
			$woo_shop_sidebar = \Trove\is_woo_shop_filter_sidebar_enabled();

			if ( is_product() || $woo_shop_sidebar ) {
				wp_enqueue_script(
					'sticksy',
					\TROVE_URI . '/assets/js/sticksy.min.js',
					array(),
					TROVE_THEME_VERSION,
					true
				);
			}

			if ( is_product() ) {
				$ajax_cart_deps = array( 'jquery' );
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
						'ajaxUrl'       => \WC_AJAX::get_endpoint( 'add_to_cart' ),
						'storeCartUrl'  => rest_url( 'wc/store/v1/cart' ),
						'storeApiNonce' => wp_create_nonce( 'wc_store_api' ),
					)
				);
			} elseif ( $woo_shop_sidebar ) {
				wp_enqueue_script(
					'trove-product-ajax-cart',
					self::asset_url( 'assets/js/shop.js' ),
					array( 'jquery', 'sticksy' ),
					TROVE_THEME_VERSION,
					true
				);
			} elseif ( function_exists( 'is_cart' ) && is_cart() && \Trove\woo_cart_enable_cross_sells() ) {
				wp_enqueue_script(
					'trove-cart-cross-sells',
					self::asset_url( 'assets/js/shop.js' ),
					array( 'jquery' ),
					TROVE_THEME_VERSION,
					true
				);
			}
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
}