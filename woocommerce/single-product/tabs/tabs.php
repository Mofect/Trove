<?php
/**
 * Single Product tabs
 *
 * Trove: optional inner wrapper `.trove-woo-vertical-tabs` when Customizer
 * “Product tabs layout” is vertical (see `trove_woo_single_product_tabs_layout`).
 * When layout is collapsible, tabs render without `.woocommerce-tabs` (reviews output as `#reviews.woocommerce-Reviews`).
 *
 * @see woocommerce/templates/single-product/tabs/tabs.php
 * @package WooCommerce\Templates
 * @version 9.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter tabs and allow third parties to add their own.
 *
 * Each tab is an array containing title, callback and priority.
 *
 * @see woocommerce_default_product_tabs()
 */
$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );

$trove_tabs_default = (string) \Trove\Customizer\trove_customizer_default( 'trove_woo_single_product_tabs_layout', 'horizontal' );
$trove_tabs_layout  = get_theme_mod( 'trove_woo_single_product_tabs_layout', $trove_tabs_default );
$trove_tabs_layout  = is_string( $trove_tabs_layout ) ? sanitize_key( $trove_tabs_layout ) : 'horizontal';
$trove_vertical_tabs   = ( 'vertical' === $trove_tabs_layout );
$trove_collapsible_tabs = \Trove\is_single_product_tabs_collapsible();

if ( ! empty( $product_tabs ) ) : ?>

	<?php if ( $trove_collapsible_tabs ) : ?>
		<?php
		foreach ( $product_tabs as $key => $product_tab ) {
			if ( isset( $product_tab['callback'] ) ) {
				call_user_func( $product_tab['callback'], $key, $product_tab );
			}
		}
		do_action( 'woocommerce_product_after_tabs' );
		?>
	<?php else : ?>

	<div class="woocommerce-tabs wc-tabs-wrapper<?php if ( $trove_vertical_tabs ) : ?> trove-woo-vertical-tabs<?php endif; ?>">
		<ul class="tabs wc-tabs" role="tablist">
			<?php foreach ( $product_tabs as $key => $product_tab ) : ?>
				<li role="presentation" class="<?php echo esc_attr( $key ); ?>_tab" id="tab-title-<?php echo esc_attr( $key ); ?>">
					<a href="#tab-<?php echo esc_attr( $key ); ?>" role="tab" aria-controls="tab-<?php echo esc_attr( $key ); ?>">
						<?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ) ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php if ( $trove_vertical_tabs ) : ?>
		<div class="trove-woo-vertical-tabs__content">
		<?php endif; ?>
		<?php foreach ( $product_tabs as $key => $product_tab ) : ?>
			<div class="woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr( $key ); ?> panel entry-content wc-tab" id="tab-<?php echo esc_attr( $key ); ?>" role="tabpanel" aria-labelledby="tab-title-<?php echo esc_attr( $key ); ?>">
				<?php
				if ( isset( $product_tab['callback'] ) ) {
					call_user_func( $product_tab['callback'], $key, $product_tab );
				}
				?>
			</div>
		<?php endforeach; ?>

		<?php do_action( 'woocommerce_product_after_tabs' ); ?>
		<?php if ( $trove_vertical_tabs ) : ?>
		</div>
		<?php endif; ?>
	</div>

	<?php endif; ?>

<?php endif; ?>
