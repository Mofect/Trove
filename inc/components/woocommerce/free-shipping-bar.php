<?php
/**
 * Free shipping progress bar (Customizer → WooCommerce → Cart).
 *
 * @package Trove
 */

namespace Trove;

defined( 'ABSPATH' ) || exit;

/**
 * @return bool
 */
function woo_cart_enable_free_shipping_bar() {
	return wp_validate_boolean(
		get_theme_mod(
			'trove_woo_cart_enable_free_shipping_bar',
			(bool) \Trove\Customizer\customizer_default( 'trove_woo_cart_enable_free_shipping_bar', false )
		)
	);
}

/**
 * @return float
 */
function woo_cart_free_shipping_threshold() {
	$value = get_theme_mod(
		'trove_woo_cart_free_shipping_threshold',
		(float) \Trove\Customizer\customizer_default( 'trove_woo_cart_free_shipping_threshold', 100 )
	);
	return max( 0, (float) $value );
}

/**
 * @return string Hex color.
 */
function woo_cart_free_shipping_bar_color() {
	$color = get_theme_mod(
		'trove_woo_cart_free_shipping_bar_color',
		(string) \Trove\Customizer\customizer_default( 'trove_woo_cart_free_shipping_bar_color', '#22c55e' )
	);
	return sanitize_hex_color( $color ) ? sanitize_hex_color( $color ) : '#22c55e';
}

/**
 * Cart subtotal used for progress (matches displayed cart subtotal).
 *
 * @return float
 */
function get_free_shipping_progress_subtotal() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return 0.0;
	}
	return (float) WC()->cart->get_displayed_subtotal();
}

/**
 * @param float $subtotal   Current subtotal.
 * @param float $threshold  Threshold amount.
 * @return array{percent: float, remaining: float, qualified: bool}
 */
function get_free_shipping_progress( $subtotal, $threshold ) {
	$subtotal  = max( 0, (float) $subtotal );
	$threshold = max( 0, (float) $threshold );

	if ( $threshold <= 0 ) {
		return array(
			'percent'   => 100,
			'remaining' => 0,
			'qualified' => true,
		);
	}

	$qualified = $subtotal >= $threshold;
	$percent   = min( 100, ( $subtotal / $threshold ) * 100 );
	$remaining = max( 0, $threshold - $subtotal );

	return array(
		'percent'   => $percent,
		'remaining' => $remaining,
		'qualified' => $qualified,
	);
}

/**
 * @param float|null $subtotal Optional subtotal override.
 * @return string HTML or empty.
 */
function get_free_shipping_bar_html( $subtotal = null ) {
	if ( ! woo_cart_enable_free_shipping_bar() ) {
		return '';
	}

	$threshold = woo_cart_free_shipping_threshold();
	if ( $threshold <= 0 ) {
		return '';
	}

	if ( null === $subtotal ) {
		if ( ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() ) {
			return '';
		}
		$subtotal = get_free_shipping_progress_subtotal();
	}

	$progress = get_free_shipping_progress( $subtotal, $threshold );

	if ( $progress['qualified'] ) {
		$message = esc_html__( 'You qualify for free shipping!', 'trove' );
	} else {
		$message = sprintf(
			/* translators: %s: formatted price amount remaining */
			esc_html__( 'Add %s more for free shipping', 'trove' ),
			wp_strip_all_tags( wc_price( $progress['remaining'] ) )
		);
	}

	$percent = round( $progress['percent'], 2 );

	ob_start();
	?>
	<div
		class="trove-free-shipping-bar"
		data-threshold="<?php echo esc_attr( (string) $threshold ); ?>"
		data-subtotal="<?php echo esc_attr( (string) $subtotal ); ?>"
		role="status"
		aria-live="polite"
	>
		<p class="trove-free-shipping-bar__message"><?php echo esc_html( $message ); ?></p>
		<div class="trove-free-shipping-bar__track" aria-hidden="true">
			<span class="trove-free-shipping-bar__fill" style="width: <?php echo esc_attr( (string) $percent ); ?>%;"></span>
		</div>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * Prepend bar above the cart block (alignwide) and in the mini-cart drawer.
 *
 * @param string $block_content Block HTML.
 * @param array  $block         Block data.
 * @return string
 */
function prepend_free_shipping_bar_to_blocks( $block_content, $block ) {
	if ( ! woo_cart_enable_free_shipping_bar() || empty( $block['blockName'] ) ) {
		return $block_content;
	}

	// Cart page: do not prepend on woocommerce/cart — that yields a sibling bar node
	// (data-block-name="woocommerce/cart") outside .wp-block-woocommerce-cart. JS places one bar inside the cart root.
	$targets = array(
		'woocommerce/mini-cart-items-block',
	);

	if ( ! in_array( $block['blockName'], $targets, true ) ) {
		return $block_content;
	}

	if ( false !== strpos( $block_content, 'trove-free-shipping-bar' ) ) {
		return $block_content;
	}

	static $prepended = array();
	if ( isset( $prepended[ $block['blockName'] ] ) ) {
		return $block_content;
	}

	$html = get_free_shipping_bar_html();
	if ( '' === $html ) {
		return $block_content;
	}

	$prepended[ $block['blockName'] ] = true;
	return $html . $block_content;
}
add_filter( 'render_block', __NAMESPACE__ . '\\prepend_free_shipping_bar_to_blocks', 9, 2 );

/**
 * @return void
 */
function enqueue_free_shipping_bar_assets() {
	if ( ! class_exists( 'WooCommerce' ) || is_admin() ) {
		return;
	}
	if ( ! woo_cart_enable_free_shipping_bar() ) {
		return;
	}

	$deps = array();
	if ( wp_script_is( 'wp-data', 'registered' ) ) {
		$deps[] = 'wp-data';
	}
	if ( wp_script_is( 'wc-blocks-data-store', 'registered' ) ) {
		$deps[] = 'wc-blocks-data-store';
	} elseif ( wp_script_is( 'wc-blocks-middleware', 'registered' ) ) {
		$deps[] = 'wc-blocks-middleware';
	}

	wp_enqueue_script(
		'trove-free-shipping-bar',
		\TROVE_URI . '/assets/js/free-shipping-bar.js',
		$deps,
		defined( 'TROVE_THEME_VERSION' ) ? TROVE_THEME_VERSION : '1.0.0',
		true
	);

	$subtotal = 0.0;
	if ( function_exists( 'WC' ) && WC()->cart && ! WC()->cart->is_empty() ) {
		$subtotal = get_free_shipping_progress_subtotal();
	}

	wp_localize_script(
		'trove-free-shipping-bar',
		'troveFreeShippingBar',
		array(
			'threshold'        => woo_cart_free_shipping_threshold(),
			'subtotal'         => $subtotal,
			'pricesIncludeTax' => function_exists( 'wc_prices_include_tax' ) && wc_prices_include_tax(),
			'storeCartUrl'     => rest_url( 'wc/store/v1/cart' ),
			'i18n'             => array(
				'qualified' => __( 'You qualify for free shipping!', 'trove' ),
				/* translators: %s: formatted price amount remaining */
				'addMore'   => __( 'Add %s more for free shipping', 'trove' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_free_shipping_bar_assets', 20 );
