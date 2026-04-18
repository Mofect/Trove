<?php
/**
 * Customizer fields: Header → styles (WooCommerce mini-cart badge).
 *
 * Merged into header/styles.php when WooCommerce is active.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_header_cart_badge_color' => array(
		'label'             => __( 'Cart badge', 'trove' ),
		'description'       => __( 'Background color of the cart item count badge next to the mini cart icon. Leave empty for WooCommerce defaults.', 'trove' ),
		'type'              => 'color',
		'default'           => '',
		'transport'         => 'postMessage',
		'swatch_hint'       => __( 'Cart badge', 'trove' ),
	),
	'trove_header_cart_count_color' => array(
		'label'             => __( 'Cart count', 'trove' ),
		'description'       => __( 'Text color for the number inside the cart badge. Leave empty for WooCommerce defaults.', 'trove' ),
		'type'              => 'color',
		'default'           => '',
		'transport'         => 'postMessage',
		'swatch_hint'       => __( 'Cart count', 'trove' ),
	),
);
