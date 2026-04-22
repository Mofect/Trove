<?php
/**
 * Customizer control: responsive device tabs + TRBL padding (px) + link sides.
 *
 * @package Trove
 */

namespace Trove\Customizer;

use WP_Customize_Control;

/**
 * Three JSON theme_mods (pc / tablet / phone) with top, right, bottom, left, linked.
 */
final class Responsive_Spacing_Control extends WP_Customize_Control {

	/**
	 * @var string
	 */
	public $type = 'trove_responsive_spacing';

	/**
	 * Labels for device tabs (pc, tablet, phone).
	 *
	 * @var array<string, string>
	 */
	public $trove_device_labels = array();

	/**
	 * Number input bounds.
	 *
	 * @var array<string, int|float>
	 */
	public $trove_input_attrs = array();

	/**
	 * @param \WP_Customize_Manager $manager Manager.
	 * @param string                  $id      Control id.
	 * @param array<string, mixed>    $args    Arguments.
	 */
	public function __construct( $manager, $id, array $args = array() ) {
		$labels = isset( $args['trove_device_labels'] ) && is_array( $args['trove_device_labels'] ) ? $args['trove_device_labels'] : array();
		$attrs  = isset( $args['trove_input_attrs'] ) && is_array( $args['trove_input_attrs'] ) ? $args['trove_input_attrs'] : array();
		unset( $args['trove_device_labels'], $args['trove_input_attrs'] );
		parent::__construct( $manager, $id, $args );
		$this->trove_device_labels = $labels;
		$this->trove_input_attrs   = $attrs;
	}

	/**
	 * @return void
	 */
	public function enqueue() {
		parent::enqueue();
	}

	/**
	 * @return void
	 */
	protected function render_content() {
		$devices = array( 'pc', 'tablet', 'phone' );
		$labels  = $this->trove_device_labels;
		$map     = array();
		foreach ( $devices as $d ) {
			if ( isset( $this->settings[ $d ] ) && $this->settings[ $d ] instanceof \WP_Customize_Setting ) {
				$map[ $d ] = $this->settings[ $d ]->id;
			}
		}
		if ( count( $map ) !== 3 ) {
			return;
		}

		$attrs = $this->trove_input_attrs;
		$min   = isset( $attrs['min'] ) ? (int) $attrs['min'] : 0;
		$max   = isset( $attrs['max'] ) ? (int) $attrs['max'] : 60;
		$step  = isset( $attrs['step'] ) ? (int) $attrs['step'] : 1;

		?>
		<div
			class="trove-responsive-spacing-control"
			data-trove-spacing="1"
			data-settings="<?php echo esc_attr( wp_json_encode( $map ) ); ?>"
			data-min="<?php echo esc_attr( (string) $min ); ?>"
			data-max="<?php echo esc_attr( (string) $max ); ?>"
			data-step="<?php echo esc_attr( (string) $step ); ?>"
		>
			<div class="trove-r-heading">
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<div class="trove-r-device-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Responsive breakpoint', 'trove' ); ?>">
					<?php
					foreach ( $devices as $i => $device ) :
						$tab_label = isset( $labels[ $device ] ) ? $labels[ $device ] : $device;
						$active    = ( 0 === $i );
						?>
					<button
						type="button"
						class="trove-r-tab<?php echo $active ? ' is-active' : ''; ?>"
						role="tab"
						data-device="<?php echo esc_attr( $device ); ?>"
						aria-selected="<?php echo $active ? 'true' : 'false'; ?>"
						title="<?php echo esc_attr( $tab_label ); ?>"
					>
						<span class="screen-reader-text"><?php echo esc_html( $tab_label ); ?></span>
						<?php echo self::icon_svg( $device ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
						<?php
					endforeach;
					?>
				</div>
			</div>
			<?php if ( $this->description ) : ?>
				<p class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></p>
			<?php endif; ?>
			<div class="trove-spacing-box__grid">
				<div class="trove-spacing-box__cell">
					<label class="trove-spacing-box__label"><?php esc_html_e( 'TOP', 'trove' ); ?></label>
					<input type="number" class="trove-sb-input trove-sb-top" inputmode="numeric" <?php echo $min ? ' min="' . esc_attr( (string) $min ) . '"' : ''; ?> <?php echo $max ? ' max="' . esc_attr( (string) $max ) . '"' : ''; ?> step="<?php echo esc_attr( (string) $step ); ?>" />
				</div>
				<div class="trove-spacing-box__cell">
					<label class="trove-spacing-box__label"><?php esc_html_e( 'RIGHT', 'trove' ); ?></label>
					<input type="number" class="trove-sb-input trove-sb-right" inputmode="numeric" <?php echo $min ? ' min="' . esc_attr( (string) $min ) . '"' : ''; ?> <?php echo $max ? ' max="' . esc_attr( (string) $max ) . '"' : ''; ?> step="<?php echo esc_attr( (string) $step ); ?>" />
				</div>
				<div class="trove-spacing-box__cell">
					<label class="trove-spacing-box__label"><?php esc_html_e( 'BOTTOM', 'trove' ); ?></label>
					<input type="number" class="trove-sb-input trove-sb-bottom" inputmode="numeric" <?php echo $min ? ' min="' . esc_attr( (string) $min ) . '"' : ''; ?> <?php echo $max ? ' max="' . esc_attr( (string) $max ) . '"' : ''; ?> step="<?php echo esc_attr( (string) $step ); ?>" />
				</div>
				<div class="trove-spacing-box__cell">
					<label class="trove-spacing-box__label"><?php esc_html_e( 'LEFT', 'trove' ); ?></label>
					<input type="number" class="trove-sb-input trove-sb-left" inputmode="numeric" <?php echo $min ? ' min="' . esc_attr( (string) $min ) . '"' : ''; ?> <?php echo $max ? ' max="' . esc_attr( (string) $max ) . '"' : ''; ?> step="<?php echo esc_attr( (string) $step ); ?>" />
				</div>
				<div class="trove-spacing-box__cell trove-spacing-box__cell--action">
					<span class="trove-spacing-box__label trove-spacing-box__label--spacer" aria-hidden="true">&nbsp;</span>
					<button type="button" class="button trove-sb-link-toggle" aria-pressed="false" title="<?php esc_attr_e( 'Link values', 'trove' ); ?>">
						<span class="dashicons dashicons-editor-unlink" aria-hidden="true"></span>
						<span class="screen-reader-text"><?php esc_html_e( 'Link padding values', 'trove' ); ?></span>
					</button>
				</div>
				<div class="trove-spacing-box__cell trove-spacing-box__cell--unit">
					<span class="trove-spacing-box__label trove-spacing-box__label--spacer" aria-hidden="true">&nbsp;</span>
					<span class="trove-sb-unit" aria-hidden="true"><?php esc_html_e( 'PX', 'trove' ); ?></span>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * @param string $device pc|tablet|phone.
	 * @return string
	 */
	private static function icon_svg( $device ) {
		switch ( $device ) {
			case 'tablet':
				return '<svg class="trove-r-icon trove-r-icon--tablet" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><rect x="5" y="3" width="14" height="18" rx="1.5" stroke="currentColor" stroke-width="1.5"/><path d="M10 19.5h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>';
			case 'phone':
				return '<svg class="trove-r-icon trove-r-icon--phone" width="18" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><rect x="7" y="2" width="10" height="20" rx="2" stroke="currentColor" stroke-width="1.5"/><circle cx="12" cy="18" r="1" fill="currentColor"/></svg>';
			default:
				return '<svg class="trove-r-icon trove-r-icon--pc" width="24" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><rect x="3" y="4" width="18" height="12" rx="1" stroke="currentColor" stroke-width="1.5"/><path d="M8 20h8M12 16v4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>';
		}
	}
}
