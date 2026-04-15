<?php
/**
 * Customizer control: radio group with illustrated cards (2-column grid).
 *
 * @package Trove
 */

namespace Trove\Customizer;

use WP_Customize_Control;

/**
 * Renders image-style radio options.
 *
 * Each choice may set one of: `image` or `url` (image URL), `svg`, or `pattern` (built-in slug).
 * Precedence: image/url → svg → pattern → value key as pattern.
 */
class Image_Radio_Control extends WP_Customize_Control {

	/**
	 * @var string
	 */
	public $type = 'trove_image_radio';

	/**
	 * value => [ 'label' => string, 'image'|'url' => string, 'pattern' => string, 'svg' => string ].
	 *
	 * @var array<string, array<string, string>>
	 */
	public $trove_choices = array();

	/**
	 * @return void
	 */
	protected function render_content() {
		if ( empty( $this->trove_choices ) || ! is_array( $this->trove_choices ) ) {
			return;
		}

		$current = $this->value();
		if ( ! is_string( $current ) || '' === $current ) {
			$keys    = array_keys( $this->trove_choices );
			$current = ! empty( $keys ) ? (string) $keys[0] : '';
		}

		$name = '_customize-radio-' . $this->id;
		?>
		<div class="trove-cz-image-radio">
			<?php if ( $this->label ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<?php if ( $this->description ) : ?>
				<p class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></p>
			<?php endif; ?>

			<div class="trove-cz-image-radio__grid" role="radiogroup" aria-label="<?php echo esc_attr( $this->label ? wp_strip_all_tags( $this->label ) : __( 'Choose an option', 'trove' ) ); ?>">
				<?php
				foreach ( $this->trove_choices as $value => $choice ) :
					if ( ! is_array( $choice ) ) {
						continue;
					}
					$value      = (string) $value;
					$opt_label  = isset( $choice['label'] ) ? $choice['label'] : $value;
					$input_id   = $this->id . '-radio-' . sanitize_key( $value );
					$is_checked = ( $current === $value );
					$preview    = '';
					$image_href = '';
					if ( ! empty( $choice['image'] ) && is_string( $choice['image'] ) ) {
						$image_href = $choice['image'];
					} elseif ( ! empty( $choice['url'] ) && is_string( $choice['url'] ) ) {
						$image_href = $choice['url'];
					}
					if ( '' !== $image_href ) {
						$preview = '<img class="trove-cz-image-radio__img" src="' . esc_url( $image_href ) . '" alt="" draggable="false" />';
					} elseif ( ! empty( $choice['svg'] ) && is_string( $choice['svg'] ) ) {
						$preview = $choice['svg']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- intentional SVG from theme config.
					} elseif ( ! empty( $choice['pattern'] ) && is_string( $choice['pattern'] ) ) {
						$preview = self::pattern_svg( $choice['pattern'] );
					} else {
						$preview = self::pattern_svg( $value );
					}
					?>
					<label class="trove-cz-image-radio__option" for="<?php echo esc_attr( $input_id ); ?>">
						<input
							id="<?php echo esc_attr( $input_id ); ?>"
							class="trove-cz-image-radio__input"
							type="radio"
							name="<?php echo esc_attr( $name ); ?>"
							value="<?php echo esc_attr( $value ); ?>"
							<?php $this->link(); ?>
							<?php checked( $is_checked ); ?>
						/>
						<span class="trove-cz-image-radio__card" aria-hidden="true">
							<span class="trove-cz-image-radio__chrome">
								<?php echo $preview; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG from trusted theme helpers. ?>
							</span>
						</span>
						<span class="screen-reader-text"><?php echo esc_html( $opt_label ); ?></span>
					</label>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Built-in wireframe thumbnails (sidebar-on-right variants).
	 *
	 * @param string $pattern Pattern key.
	 * @return string SVG markup.
	 */
	public static function pattern_svg( $pattern ) {
		$pattern = sanitize_key( $pattern );
		$common  = '<rect x="2" y="3" width="68" height="42" rx="2" fill="#fff" stroke="#50575e" stroke-width="0.75"/>';
		switch ( $pattern ) {
			case 'right_medium':
				return '<svg class="trove-cz-image-radio__svg" viewBox="0 0 72 48" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">' . $common
					. '<rect x="6" y="8" width="34" height="7" rx="1" fill="#c3c4c7"/>'
					. '<rect x="6" y="19" width="44" height="2" rx="0.5" fill="#dcdcde"/><rect x="6" y="24" width="40" height="2" rx="0.5" fill="#dcdcde"/><rect x="6" y="29" width="42" height="2" rx="0.5" fill="#dcdcde"/>'
					. '<rect x="44" y="8" width="14" height="30" rx="1" fill="#8c8f94"/>'
					. '</svg>';

			case 'right_light':
				return '<svg class="trove-cz-image-radio__svg" viewBox="0 0 72 48" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">' . $common
					. '<rect x="6" y="8" width="36" height="7" rx="1" fill="#dcdcde"/>'
					. '<rect x="6" y="19" width="46" height="2" rx="0.5" fill="#e8e8e9"/><rect x="6" y="24" width="42" height="2" rx="0.5" fill="#e8e8e9"/><rect x="6" y="29" width="44" height="2" rx="0.5" fill="#e8e8e9"/>'
					. '<rect x="46" y="8" width="12" height="30" rx="1" fill="#f0f0f1"/>'
					. '</svg>';

			case 'right_wide':
				return '<svg class="trove-cz-image-radio__svg" viewBox="0 0 72 48" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">' . $common
					. '<rect x="6" y="8" width="28" height="7" rx="1" fill="#c3c4c7"/>'
					. '<rect x="6" y="19" width="36" height="2" rx="0.5" fill="#dcdcde"/><rect x="6" y="24" width="34" height="2" rx="0.5" fill="#dcdcde"/><rect x="6" y="29" width="35" height="2" rx="0.5" fill="#dcdcde"/>'
					. '<rect x="38" y="8" width="24" height="30" rx="1" fill="#8c8f94"/>'
					. '</svg>';

			case 'right_narrow':
			default:
				return '<svg class="trove-cz-image-radio__svg" viewBox="0 0 72 48" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">' . $common
					. '<rect x="6" y="8" width="38" height="7" rx="1" fill="#c3c4c7"/>'
					. '<rect x="6" y="19" width="46" height="2" rx="0.5" fill="#dcdcde"/><rect x="6" y="24" width="44" height="2" rx="0.5" fill="#dcdcde"/><rect x="6" y="29" width="45" height="2" rx="0.5" fill="#dcdcde"/>'
					. '<rect x="48" y="8" width="8" height="30" rx="1" fill="#a7aaad"/>'
					. '</svg>';
		}
	}
}
