<?php
/**
 * Customizer control: Google Font (Select2, full catalog).
 *
 * @package Trove
 */

namespace Trove\Customizer;

use WP_Customize_Control;

/**
 * Renders a select enhanced by Select2; options are injected via JS (full list).
 */
class Google_Font_Control extends WP_Customize_Control {

	/**
	 * @var string
	 */
	public $type = 'trove_google_font';

	/**
	 * @return void
	 */
	protected function render_content() {
		$input_id = '_customize-input-' . $this->id;
		$current  = $this->value();
		if ( ! is_string( $current ) || '' === trim( $current ) ) {
			$current = 'Instrument Sans';
		}
		?>
		<div class="trove-cz-google-font trove-cz-google-font--select2">
			<?php if ( $this->label ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<?php if ( $this->description ) : ?>
				<p class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></p>
			<?php endif; ?>
			<select
				id="<?php echo esc_attr( $input_id ); ?>"
				class="trove-cz-google-font__select js-trove-google-font-select"
				<?php $this->link(); ?>
			>
				<option value="<?php echo esc_attr( $current ); ?>">
					<?php
					echo esc_html(
						'system' === $current
							? __( 'System UI', 'trove' )
							: $current
					);
					?>
				</option>
			</select>
		</div>
		<?php
	}
}
