<?php
/**
 * Customizer fields: Woocommerce → Single product.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

use function \Trove\Customizer\trove_customizer_default;

defined( 'ABSPATH' ) || exit;

/**
 * Show stacked-columns control only when gallery type is "stacked".
 *
 * @param \WP_Customize_Control $control Control instance.
 * @return bool
 */
function is_single_product_gallery_stacked( $control ) {
	if ( ! $control || ! isset( $control->manager ) ) {
		return false;
	}
	$setting = $control->manager->get_setting( 'trove_woo_single_product_gallery_type' );
	if ( ! $setting ) {
		return true;
	}
	$value = $setting->value();
	return 'stacked' === sanitize_key( (string) $value );
}

/**
 * Show slider-position control only when gallery type is "slider".
 *
 * @param \WP_Customize_Control $control Control instance.
 * @return bool
 */
function is_single_product_gallery_slider( $control ) {
	if ( ! $control || ! isset( $control->manager ) ) {
		return false;
	}
	$setting = $control->manager->get_setting( 'trove_woo_single_product_gallery_type' );
	if ( ! $setting ) {
		return false;
	}
	$value = $setting->value();
	return 'slider' === sanitize_key( (string) $value );
}

return array(
    'trove_woo_single_product_layout_heading' => array(
        'label' => __( 'Gallery', 'trove' ),
        'type' => 'heading',
        'section' => 'trove_section_woocommerce_single_product',
        'priority' => 10,
    ),
	'trove_woo_single_product_gallery_type' => array(
		'label'             => __( 'Product gallery type', 'trove' ),
		'type'              => 'select',
		'default'           => trove_customizer_default( 'trove_woo_single_product_gallery_type', 'stacked' ),
		'transport'         => 'refresh',
		'choices'           => array(
			'stacked' => __( 'Stacked', 'trove' ),
			'slider'  => __( 'Slider', 'trove' ),
		),
	),
	'trove_woo_single_product_gallery_stacked_columns' => array(
		'label'             => __( 'Stacked gallery columns (from 2nd image)', 'trove' ),
		'description'       => __( 'Only applies when Product gallery type is Stacked. The first image stays full width; additional images use this column count.', 'trove' ),
		'type'              => 'select',
		'default'           => trove_customizer_default( 'trove_woo_single_product_gallery_stacked_columns', '4' ),
		'transport'         => 'postMessage',
		'active_callback'   => __NAMESPACE__ . '\\is_single_product_gallery_stacked',
		'choices'           => array(
			'1' => __( '1 column', 'trove' ),
			'2' => __( '2 columns', 'trove' ),
			'3' => __( '3 columns', 'trove' ),
			'4' => __( '4 columns', 'trove' ),
			'5' => __( '5 columns', 'trove' ),
			'6' => __( '6 columns', 'trove' ),
		),
	),
	'trove_woo_single_product_gallery_slider_position' => array(
		'label'             => __( 'Image carousel position', 'trove' ),
		'description'       => __( 'Only applies when Product gallery type is Slider.', 'trove' ),
		'type'              => 'select',
		'default'           => trove_customizer_default( 'trove_woo_single_product_gallery_slider_position', 'bottom' ),
		'transport'         => 'postMessage',
		'active_callback'   => __NAMESPACE__ . '\\is_single_product_gallery_slider',
		'choices'           => array(
			'left'   => __( 'Left', 'trove' ),
			'right'  => __( 'Right', 'trove' ),
			'bottom' => __( 'Bottom', 'trove' ),
		),
	),
    'trove_woo_single_product_tab_heading' => array(
        'label' => __( 'Tabs', 'trove' ),
        'type' => 'heading',
        'section' => 'trove_section_woocommerce_single_product',
        'priority' => 10,
    ),
	'trove_woo_single_product_tabs_layout' => array(
		'label'             => __( 'Product tabs layout', 'trove' ),
		'type'              => 'select',
		'default'           => trove_customizer_default( 'trove_woo_single_product_tabs_layout', 'horizontal' ),
		'transport'         => 'refresh',
		'choices'           => array(
			'horizontal'   => __( 'Horizontal', 'trove' ),
			'vertical'     => __( 'Vertical', 'trove' ),
			'collapsible'  => __( 'Collapsible', 'trove' ),
		),
	),
	'trove_woo_single_product_extra_tabs' => array(
		'label'       => __( 'Global extra tabs', 'trove' ),
		'description' => __( 'Add custom tabs displayed after Additional Information.', 'trove' ),
		'type'        => 'repeater_key_value',
		'default'     => wp_json_encode(
			array(
				array(
					'title'   => __( 'Shipping', 'trove' ),
					'content' => __( 'Shipping cost is based on weight. Just add products to your cart and use the Shipping Calculator to see the shipping price.', 'trove' ),
				),
			)
		),
		'transport'   => 'refresh',
		'key_label'   => __( 'Tab title', 'trove' ),
		'value_label' => __( 'Tab content', 'trove' ),
		'max_items'   => 10,
	),
	'trove_woo_single_product_related_heading' => array(
        'label' => __( 'Related products', 'trove' ),
        'type' => 'heading',
        'section' => 'trove_section_woocommerce_single_product',
        'priority' => 10,
    ),
	'trove_woo_single_product_enable_related_products' => array(
		'label'             => __( 'Enable related products', 'trove' ),
		'type'              => 'switch',
		'default'           => trove_customizer_default( 'trove_woo_single_product_enable_related_products', true ),
		'transport'         => 'refresh',
	),
	'trove_woo_single_product_related_products_count' => array(
		'label'             => __( 'Number of related products', 'trove' ),
		'type'              => 'select',
		'default'           => trove_customizer_default( 'trove_woo_single_product_related_products_count', '4' ),
		'transport'         => 'refresh',
		'choices'           => array(
			'4'  => __( '4 products', 'trove' ),
			'8'  => __( '8 products', 'trove' ),
			'12' => __( '12 products', 'trove' ),
			'16' => __( '16 products', 'trove' ),
			'20' => __( '20 products', 'trove' ),
		),
	),
);
