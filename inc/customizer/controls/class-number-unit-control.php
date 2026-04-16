<?php
/**
 * Customizer control: number + unit select on one row (two theme_mods).
 *
 * @package Trove
 */

namespace Trove\Customizer;

use WP_Customize_Control;

/**
 * Renders a flex row: number input + unit dropdown (no separate unit label).
 */
class Number_Unit_Control extends WP_Customize_Control {

	/**
	 * @var string
	 */
	public $type = 'trove_number_unit';

	/**
	 * Attributes for the number input (min, max, step).
	 *
	 * @var array<string, scalar>
	 */
	public $number_input_attrs = array();

	/**
	 * Unit slug => label for &lt;select&gt;.
	 *
	 * @var array<string, string>
	 */
	public $unit_choices = array();

	/**
	 * @return void
	 */
	protected function render_content() {
		if ( ! isset( $this->settings['default'], $this->settings['unit'] ) ) {
			return;
		}

		$num_id  = '_customize-input-' . $this->id . '-number';
		$unit_id = '_customize-input-' . $this->id . '-unit';

		$choices = is_array( $this->unit_choices ) ? $this->unit_choices : array();
		$attrs   = is_array( $this->number_input_attrs ) ? $this->number_input_attrs : array();

		$input_attrs = '';
		foreach ( $attrs as $ak => $av ) {
			$input_attrs .= ' ' . esc_attr( (string) $ak ) . '="' . esc_attr( (string) $av ) . '"';
		}
		?>
		<?php if ( $this->label ) : ?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php endif; ?>
		<?php if ( $this->description ) : ?>
			<p class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></p>
		<?php endif; ?>
		<div class="trove-cz-number-unit">
			<input
				id="<?php echo esc_attr( $num_id ); ?>"
				class="trove-cz-number-unit__input"
				type="number"
				<?php echo $input_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from safe attrs ?>
				<?php $this->link( 'default' ); ?>
			/>
			<select
				id="<?php echo esc_attr( $unit_id ); ?>"
				class="trove-cz-number-unit__unit"
				<?php $this->link( 'unit' ); ?>
				aria-label="<?php esc_attr_e( 'Unit', 'trove' ); ?>"
			>
				<?php foreach ( $choices as $val => $label ) : ?>
					<option value="<?php echo esc_attr( $val ); ?>" <?php selected( (string) $this->settings['unit']->value(), (string) $val ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php
	}
}
