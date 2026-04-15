<?php
/**
 * Customizer control: one label row with device tabs (icons) + single field bound to active breakpoint.
 *
 * @package Trove
 */

namespace Trove\Customizer;

use WP_Customize_Control;

/**
 * Renders responsive options as tabs (PC / tablet / phone) with one visible control.
 */
class Responsive_Tabs_Control extends WP_Customize_Control {

	/**
	 * @var string
	 */
	public $type = 'trove_responsive_tabs';

	/**
	 * Field widget type: text, number, checkbox, select, color.
	 *
	 * @var string
	 */
	public $field_type = 'text';

	/**
	 * Labels for device tabs (pc, tablet, phone).
	 *
	 * @var array<string, string>
	 */
	public $trove_device_labels = array();

	/**
	 * Number input HTML5 attributes.
	 *
	 * @var array<string, string|int|float>
	 */
	public $trove_input_attrs = array();

	/**
	 * Select options value => label.
	 *
	 * @var array<string, string>
	 */
	public $trove_choices = array();

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

		$field = isset( $this->field_type ) ? $this->field_type : 'text';
		?>
		<div
			class="trove-responsive-tabs-control"
			data-field-type="<?php echo esc_attr( $field ); ?>"
			data-settings="<?php echo esc_attr( wp_json_encode( $map ) ); ?>"
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
			<div class="trove-r-field-wrap">
				<?php $this->render_field( $field ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * @param string $field Field type.
	 * @return void
	 */
	private function render_field( $field ) {
		switch ( $field ) {
			case 'number':
				$this->render_number();
				break;
			case 'checkbox':
				$this->render_checkbox();
				break;
			case 'select':
				$this->render_select();
				break;
			case 'color':
				$this->render_color();
				break;
			default:
				$this->render_text();
				break;
		}
	}

	/**
	 * @return void
	 */
	private function render_text() {
		?>
		<input
			type="text"
			class="trove-r-active-input widefat"
			value=""
			autocomplete="off"
		/>
		<?php
	}

	/**
	 * @return void
	 */
	private function render_number() {
		$attrs = $this->trove_input_attrs;
		$min   = isset( $attrs['min'] ) ? (string) $attrs['min'] : '';
		$max   = isset( $attrs['max'] ) ? (string) $attrs['max'] : '';
		$step  = isset( $attrs['step'] ) ? (string) $attrs['step'] : 'any';
		?>
		<input
			type="number"
			class="trove-r-active-input widefat"
			value=""
			<?php echo $min ? ' min="' . esc_attr( $min ) . '"' : ''; ?>
			<?php echo $max ? ' max="' . esc_attr( $max ) . '"' : ''; ?>
			step="<?php echo esc_attr( $step ); ?>"
		/>
		<?php
	}

	/**
	 * @return void
	 */
	private function render_checkbox() {
		?>
		<label class="trove-r-checkbox-label">
			<input
				type="checkbox"
				class="trove-r-active-input"
				aria-label="<?php esc_attr_e( 'Toggle for the selected device', 'trove' ); ?>"
			/>
		</label>
		<?php
	}

	/**
	 * @return void
	 */
	private function render_select() {
		?>
		<select class="trove-r-active-input widefat">
			<?php
			foreach ( $this->trove_choices as $val => $opt_label ) {
				printf(
					'<option value="%s">%s</option>',
					esc_attr( $val ),
					esc_html( $opt_label )
				);
			}
			?>
		</select>
		<?php
	}

	/**
	 * @return void
	 */
	private function render_color() {
		?>
		<input
			type="text"
			class="trove-r-active-input trove-r-color-input widefat"
			value=""
			data-default-color=""
		/>
		<?php
	}

	/**
	 * Inline SVG icons (monitor / tablet / phone).
	 *
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
