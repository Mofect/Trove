<?php
/**
 * Customizer control: repeater rows with title + content (stored as JSON array of objects).
 *
 * @package Trove
 */

namespace Trove\Customizer;

use WP_Customize_Control;

/**
 * Repeater-style key-value rows (max items enforced in PHP + JS).
 */
class Repeater_Key_Value_Control extends WP_Customize_Control {

	/**
	 * @var string
	 */
	public $type = 'trove_repeater_key_value';

	/**
	 * Max number of rows (capped at 20).
	 *
	 * @var int
	 */
	public $max_items = 10;

	/**
	 * Label for the key/title field.
	 *
	 * @var string
	 */
	public $key_label = '';

	/**
	 * Label for the value/content field.
	 *
	 * @var string
	 */
	public $value_label = '';

	/**
	 * @param \WP_Customize_Manager $manager Customizer manager.
	 * @param string                $id      Control id.
	 * @param array<string, mixed>  $args    Control args.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		if ( isset( $args['max_items'] ) ) {
			$this->max_items = min( 20, max( 1, absint( $args['max_items'] ) ) );
		}
		if ( isset( $args['key_label'] ) ) {
			$this->key_label = $args['key_label'];
		}
		if ( isset( $args['value_label'] ) ) {
			$this->value_label = $args['value_label'];
		}
		parent::__construct( $manager, $id, $args );
	}

	/**
	 * @return void
	 */
	protected function render_content() {
		$input_id = '_customize-input-' . $this->id;
		$key_lbl  = $this->key_label ? $this->key_label : __( 'Title', 'trove' );
		$val_lbl  = $this->value_label ? $this->value_label : __( 'Content', 'trove' );
		?>
		<div class="trove-cz-repeater-kv" data-max="<?php echo esc_attr( (int) $this->max_items ); ?>">
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<input
				type="hidden"
				id="<?php echo esc_attr( $input_id ); ?>"
				value="<?php echo esc_attr( $this->value() ); ?>"
				<?php $this->link(); ?>
			/>
			<ul class="trove-cz-repeater-kv__list" aria-label="<?php esc_attr_e( 'Extra tabs', 'trove' ); ?>"></ul>
			<p>
				<button type="button" class="button button-secondary trove-cz-repeater-kv__add">
					<?php esc_html_e( 'Add new', 'trove' ); ?>
				</button>
			</p>
			<?php if ( ! empty( $this->description ) ) : ?>
				<p class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></p>
			<?php endif; ?>
		</div>
		<script type="text/template" id="tmpl-trove-repeater-kv-row">
			<li class="trove-cz-repeater-kv__row" draggable="true">
				<div class="trove-cz-repeater-kv__header">
					<button type="button" class="button-link trove-cz-repeater-kv__toggle" aria-expanded="true">
						<span class="trove-cz-repeater-kv__toggle-icon" aria-hidden="true"></span>
						<span class="trove-cz-repeater-kv__toggle-label"></span>
					</button>
					<div class="trove-cz-repeater-kv__header-actions">
						<button type="button" class="button-link trove-cz-repeater-kv__move-up" aria-label="<?php esc_attr_e( 'Move up', 'trove' ); ?>">
							<span class="trove-cz-repeater-kv__toggle-icon trove-cz-repeater-kv__toggle-icon--up" aria-hidden="true"></span>
						</button>
						<button type="button" class="button-link trove-cz-repeater-kv__move-down" aria-label="<?php esc_attr_e( 'Move down', 'trove' ); ?>">
							<span class="trove-cz-repeater-kv__toggle-icon trove-cz-repeater-kv__toggle-icon--down" aria-hidden="true"></span>
						</button>
						<button type="button" class="button-link trove-cz-repeater-kv__remove" aria-label="<?php esc_attr_e( 'Remove', 'trove' ); ?>">&times;</button>
					</div>
				</div>
				<div class="trove-cz-repeater-kv__body">
					<div class="trove-cz-repeater-kv__field">
						<label class="trove-cz-repeater-kv__field-label"><?php echo esc_html( $key_lbl ); ?></label>
						<input type="text" class="trove-cz-repeater-kv__input-key widefat" data-field="title" />
					</div>
					<div class="trove-cz-repeater-kv__field">
						<label class="trove-cz-repeater-kv__field-label"><?php echo esc_html( $val_lbl ); ?></label>
						<textarea class="trove-cz-repeater-kv__input-value widefat" rows="3" data-field="content"></textarea>
					</div>
					<p class="trove-cz-repeater-kv__row-actions">
						<button type="button" class="button button-primary trove-cz-repeater-kv__save">
							<?php esc_html_e( 'Save', 'trove' ); ?>
						</button>
					</p>
				</div>
			</li>
		</script>
		<?php
	}
}
