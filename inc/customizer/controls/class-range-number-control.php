<?php
/**
 * Customizer control: range slider + number field + unit label (single theme_mod).
 *
 * @package Trove
 */

namespace Trove\Customizer;

use WP_Customize_Control;

/**
 * Renders a row: range (flex) | number + unit in a bordered box.
 */
class Range_Number_Control extends WP_Customize_Control {

	/**
	 * @var string
	 */
	public $type = 'trove_range_number';

	/**
	 * Attributes for min, max, step (range + number).
	 *
	 * @var array<string, scalar>
	 */
	public $input_attrs = array();

	/**
	 * Unit label shown after the number (e.g. px → "PX").
	 *
	 * @var string
	 */
	public $unit = 'px';

	/**
	 * @return void
	 */
	protected function render_content() {
		if ( ! isset( $this->settings['default'] ) ) {
			return;
		}

		$min  = isset( $this->input_attrs['min'] ) ? (int) $this->input_attrs['min'] : 0;
		$max  = isset( $this->input_attrs['max'] ) ? (int) $this->input_attrs['max'] : 100;
		$step = isset( $this->input_attrs['step'] ) ? (string) $this->input_attrs['step'] : '1';

		$val = $this->value();
		$val = is_numeric( $val ) ? (int) round( (float) $val ) : $min;

		$setting_id = $this->settings['default']->id;
		$range_id   = '_customize-input-' . $this->id . '-range';
		$num_id     = '_customize-input-' . $this->id;

		$unit_show = strtoupper( (string) $this->unit );
		?>
		<div class="trove-cz-range-number" data-setting-id="<?php echo esc_attr( $setting_id ); ?>">
			<?php if ( $this->label ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<div class="trove-cz-range-number__row">
				<input
					type="range"
					id="<?php echo esc_attr( $range_id ); ?>"
					class="trove-cz-range-number__range"
					min="<?php echo esc_attr( $min ); ?>"
					max="<?php echo esc_attr( $max ); ?>"
					step="<?php echo esc_attr( $step ); ?>"
					value="<?php echo esc_attr( $val ); ?>"
				/>
				<div class="trove-cz-range-number__value-wrap">
					<input
						type="number"
						id="<?php echo esc_attr( $num_id ); ?>"
						class="trove-cz-range-number__number"
						min="<?php echo esc_attr( $min ); ?>"
						max="<?php echo esc_attr( $max ); ?>"
						step="<?php echo esc_attr( $step ); ?>"
						value="<?php echo esc_attr( $val ); ?>"
						<?php $this->link( 'default' ); ?>
					/>
					<span class="trove-cz-range-number__unit"><?php echo esc_html( $unit_show ); ?></span>
				</div>
			</div>
			<?php if ( $this->description ) : ?>
				<p class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}
}
