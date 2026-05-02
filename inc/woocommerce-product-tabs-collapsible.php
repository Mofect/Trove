<?php
/**
 * Product tabs “Collapsible” layout: Reviews stay in the standard tab bar; Description +
 * Additional information render as an accordion under the add-to-cart form.
 *
 * @package Trove
 */

namespace Trove;

defined( 'ABSPATH' ) || exit;

/**
 * @return string `horizontal`|`vertical`|`collapsible`
 */
function get_single_product_tabs_layout() {
	$def = (string) \Trove\Customizer\trove_customizer_default( 'trove_woo_single_product_tabs_layout', 'horizontal' );
	$v   = sanitize_key( (string) get_theme_mod( 'trove_woo_single_product_tabs_layout', $def ) );
	if ( 'collapsible' === $v ) {
		return 'collapsible';
	}
	if ( 'vertical' === $v ) {
		return 'vertical';
	}
	return 'horizontal';
}

/**
 * @return bool
 */
function is_single_product_tabs_collapsible() {
	return 'collapsible' === get_single_product_tabs_layout();
}

/**
 * Removes Description / Additional information from the main tab strip; content is output in {@see output_collapsible_product_accordion()}.
 *
 * @param array<string, array<string, mixed>> $tabs Product tabs.
 * @return array<string, array<string, mixed>>
 */
function filter_product_tabs_for_collapsible_layout( $tabs ) {
	if ( ! is_single_product_tabs_collapsible() || ! is_array( $tabs ) ) {
		return $tabs;
	}

	$keys                                           = array( 'description', 'additional_information' );
	$GLOBALS['trove_woo_collapsible_accordion_tabs'] = array();

	foreach ( $keys as $key ) {
		if ( isset( $tabs[ $key ] ) ) {
			$GLOBALS['trove_woo_collapsible_accordion_tabs'][ $key ] = $tabs[ $key ];
			unset( $tabs[ $key ] );
		}
	}

	return $tabs;
}

/**
 * While printing the under–add-to-cart accordion, hide duplicate &lt;h2&gt; from Woo tab templates
 * (summary already shows the label).
 *
 * @param string|false $heading Tab heading text.
 * @return string|false
 */
function filter_suppress_wc_tab_h2_in_collapsible_accordion( $heading ) {
	if ( ! empty( $GLOBALS['trove_woo_rendering_collapsible_accordion'] ) ) {
		return false;
	}
	return $heading;
}

/**
 * @return void
 */
function output_collapsible_product_accordion() {
	if ( ! is_single_product_tabs_collapsible() ) {
		return;
	}

	/*
	 * Tabs template runs later (`woocommerce_after_single_product_summary`). Resolve tabs here so
	 * stash is populated before accordion markup prints under the add-to-cart form.
	 */
	apply_filters( 'woocommerce_product_tabs', array() );

	if ( empty( $GLOBALS['trove_woo_collapsible_accordion_tabs'] ) || ! is_array( $GLOBALS['trove_woo_collapsible_accordion_tabs'] ) ) {
		return;
	}

	$panels = $GLOBALS['trove_woo_collapsible_accordion_tabs'];

	echo '<div class="trove-woo-product-accordion" aria-label="' . esc_attr__( 'Product information', 'trove' ) . '">';

	$GLOBALS['trove_woo_rendering_collapsible_accordion'] = true;
	try {
		foreach ( $panels as $key => $product_tab ) {
			if ( ! isset( $product_tab['callback'] ) || ! is_callable( $product_tab['callback'] ) ) {
				continue;
			}

			$title = isset( $product_tab['title'] ) ? (string) $product_tab['title'] : '';
			$title = wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $title, $key ) );

			echo '<details class="trove-woo-accordion__item woocommerce-Tabs-panel--' . esc_attr( $key ) . '" id="trove-woo-accordion-' . esc_attr( $key ) . '">';
			echo '<summary class="trove-woo-accordion__summary">';
			echo '<span class="trove-woo-accordion__title">' . $title . '</span>';
			echo '</summary>';
			echo '<div class="trove-woo-accordion__panel entry-content">';
			call_user_func( $product_tab['callback'], $key, $product_tab );
			echo '</div>';
			echo '</details>';
		}
	} finally {
		unset( $GLOBALS['trove_woo_rendering_collapsible_accordion'] );
	}

	echo '</div>';
}

/* After WooCommerce `woocommerce_sort_product_tabs` (priority 99). */
add_filter( 'woocommerce_product_tabs', __NAMESPACE__ . '\\filter_product_tabs_for_collapsible_layout', 100 );
add_filter( 'woocommerce_product_description_heading', __NAMESPACE__ . '\\filter_suppress_wc_tab_h2_in_collapsible_accordion', 10, 1 );
add_filter( 'woocommerce_product_additional_information_heading', __NAMESPACE__ . '\\filter_suppress_wc_tab_h2_in_collapsible_accordion', 10, 1 );
add_action( 'woocommerce_after_add_to_cart_form', __NAMESPACE__ . '\\output_collapsible_product_accordion', 20 );
