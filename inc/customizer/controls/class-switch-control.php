<?php
/**
 * Customizer control: boolean setting as a pill switch (border + sliding knob).
 *
 * @package Trove
 */

namespace Trove\Customizer;

use WP_Customize_Control;

/**
 * Renders a switch UI bound to a checkbox theme_mod.
 */
class Switch_Control extends WP_Customize_Control {

	/**
	 * @var string
	 */
	public $type = 'trove_switch';

	/**
	 * @return void
	 */
	protected function render_content() {
		$input_id = '_customize-input-' . $this->id;
		?>
		<div class="trove-cz-switch">
			<label class="trove-cz-switch__row">
				<span class="trove-cz-switch__text"><?php echo esc_html( $this->label ); ?></span>
				<span class="trove-cz-switch__track-wrap">
					<input
						id="<?php echo esc_attr( $input_id ); ?>"
						type="checkbox"
						class="trove-cz-switch__input"
						<?php $this->link(); ?>
						<?php checked( $this->value() ); ?>
					/>
					<span class="trove-cz-switch__track" aria-hidden="true">
						<span class="trove-cz-switch__knob"></span>
					</span>
				</span>
			</label>
			<?php if ( $this->description ) : ?>
				<p class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}
}
