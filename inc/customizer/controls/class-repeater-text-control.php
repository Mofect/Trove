<?php
/**
 * Customizer control: multiple text lines stored as JSON (array of strings).
 *
 * @package Trove
 */

namespace Trove\Customizer;

use WP_Customize_Control;

/**
 * Repeater-style text rows (max items enforced in PHP + JS).
 */
class Repeater_Text_Control extends WP_Customize_Control {

	/**
	 * @var string
	 */
	public $type = 'trove_repeater_text';

	/**
	 * Max number of text rows (capped at 10).
	 *
	 * @var int
	 */
	public $max_items = 10;

	/**
	 * @param \WP_Customize_Manager $manager Customizer manager.
	 * @param string                $id      Control id.
	 * @param array<string, mixed>  $args    Control args.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		if ( isset( $args['max_items'] ) ) {
			$this->max_items = min( 10, max( 1, absint( $args['max_items'] ) ) );
		}
		parent::__construct( $manager, $id, $args );
	}

	/**
	 * @return void
	 */
	protected function render_content() {
		$input_id = '_customize-input-' . $this->id;
		?>
		<div class="trove-cz-repeater-text" data-max="<?php echo esc_attr( (int) $this->max_items ); ?>">
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<input
				type="hidden"
				id="<?php echo esc_attr( $input_id ); ?>"
				value="<?php echo esc_attr( $this->value() ); ?>"
				<?php $this->link(); ?>
			/>
			<ul class="trove-cz-repeater-text__list" aria-label="<?php esc_attr_e( 'Announcement', 'trove' ); ?>"></ul>
			<p>
				<button type="button" class="button button-secondary trove-cz-repeater-text__add">
					<?php esc_html_e( 'Add new', 'trove' ); ?>
				</button>
			</p>
			<?php if ( ! empty( $this->description ) ) : ?>
				<p class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}
}
