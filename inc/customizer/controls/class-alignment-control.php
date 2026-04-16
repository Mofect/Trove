<?php
/**
 * Customizer control: pill-style alignment segmented tabs.
 *
 * @package Trove
 */

namespace Trove\Customizer;

use WP_Customize_Control;

class Alignment_Control extends WP_Customize_Control {

	/**
	 * @var string
	 */
	public $type = 'trove_alignment';

	/**
	 * @return void
	 */
	protected function render_content() {
		$choices = is_array( $this->choices ) ? $this->choices : array();
		if ( empty( $choices ) ) {
			$choices = array(
				'left'   => __( 'Left', 'trove' ),
				'center' => __( 'Center', 'trove' ),
				'right'  => __( 'Right', 'trove' ),
			);
		}
		$current = (string) $this->value();
		if ( '' === $current || ! isset( $choices[ $current ] ) ) {
			$keys    = array_keys( $choices );
			$current = (string) $keys[0];
		}
		$name = '_customize-radio-' . $this->id;
		?>
		<div class="trove-cz-align">
			<?php if ( $this->label ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<?php if ( $this->description ) : ?>
				<p class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></p>
			<?php endif; ?>
			<div class="trove-cz-align__group" role="radiogroup" aria-label="<?php echo esc_attr( $this->label ? wp_strip_all_tags( $this->label ) : __( 'Alignment', 'trove' ) ); ?>">
				<?php foreach ( $choices as $value => $label ) : ?>
					<?php
					$value      = (string) $value;
					$is_checked = ( $current === $value );
					$input_id   = $this->id . '-align-' . sanitize_key( $value );
					?>
					<label class="trove-cz-align__option" for="<?php echo esc_attr( $input_id ); ?>">
						<input
							id="<?php echo esc_attr( $input_id ); ?>"
							class="trove-cz-align__input"
							type="radio"
							name="<?php echo esc_attr( $name ); ?>"
							value="<?php echo esc_attr( $value ); ?>"
							<?php $this->link(); ?>
							<?php checked( $is_checked ); ?>
						/>
						<span class="trove-cz-align__pill" aria-hidden="true">
							<?php echo self::icon_svg( $value ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</span>
						<span class="screen-reader-text"><?php echo esc_html( (string) $label ); ?></span>
					</label>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * @param string $value left|center|right
	 * @return string
	 */
	private static function icon_svg( $value ) {
		$v = sanitize_key( (string) $value );
		if ( 'left' === $v ) {
			return '<svg class="trove-cz-align__icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="4" y="6" width="12" height="2" rx="1" fill="currentColor"/><rect x="4" y="11" width="9" height="2" rx="1" fill="currentColor"/><rect x="4" y="16" width="13" height="2" rx="1" fill="currentColor"/></svg>';
		}
		if ( 'center' === $v ) {
			return '<svg class="trove-cz-align__icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="6" y="6" width="12" height="2" rx="1" fill="currentColor"/><rect x="8" y="11" width="8" height="2" rx="1" fill="currentColor"/><rect x="5" y="16" width="14" height="2" rx="1" fill="currentColor"/></svg>';
		}
		return '<svg class="trove-cz-align__icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="8" y="6" width="12" height="2" rx="1" fill="currentColor"/><rect x="11" y="11" width="9" height="2" rx="1" fill="currentColor"/><rect x="7" y="16" width="13" height="2" rx="1" fill="currentColor"/></svg>';
	}
}

