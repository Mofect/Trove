<?php
/**
 * Two wpColorPicker swatches in one row (e.g. link + hover).
 *
 * @package Trove
 */

namespace Trove\Customizer;

use WP_Customize_Control;
use WP_Customize_Manager;

/**
 * @phpstan-type SwatchDef array{setting:string, default?:string}
 */
final class Dual_Color_Control extends WP_Customize_Control {

	/**
	 * @var string
	 */
	public $type = 'trove_dual_color';

	/**
	 * @var array{setting:string, default?:string}
	 */
	public $swatch_left = array();

	/**
	 * @var array{setting:string, default?:string}
	 */
	public $swatch_right = array();

	/**
	 * @param WP_Customize_Manager $manager Manager.
	 * @param string                 $id      Control id (not a theme_mod).
	 * @param array<string, mixed>   $args    Arguments.
	 */
	public function __construct( WP_Customize_Manager $manager, $id, array $args = array() ) {
		$this->swatch_left  = isset( $args['swatch_left'] ) && is_array( $args['swatch_left'] ) ? $args['swatch_left'] : array();
		$this->swatch_right = isset( $args['swatch_right'] ) && is_array( $args['swatch_right'] ) ? $args['swatch_right'] : array();
		if ( ! empty( $this->swatch_left['setting'] ) ) {
			$args['settings'] = array(
				'default' => $this->swatch_left['setting'],
			);
		}
		unset( $args['swatch_left'], $args['swatch_right'] );
		parent::__construct( $manager, $id, $args );
	}

	/**
	 * @return void
	 */
	public function enqueue() {
		parent::enqueue();
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );
	}

	/**
	 * @return void
	 */
	public function render_content() {
		if ( empty( $this->swatch_left['setting'] ) || empty( $this->swatch_right['setting'] ) ) {
			return;
		}
		?>
		<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<div class="trove-dual-color__swatches">
			<?php
			foreach ( array( 'swatch_left', 'swatch_right' ) as $prop ) {
				$sw = $this->$prop;
				$sid = $sw['setting'];
				$s   = $this->manager->get_setting( $sid );
				$val = $s ? (string) $s->value() : '';
				if ( '' === $val ) {
					$val = isset( $sw['default'] ) ? (string) $sw['default'] : '';
				}
				$def = isset( $sw['default'] ) ? (string) $sw['default'] : '';
				?>
				<span class="trove-dual-color__pill">
					<input
						type="text"
						class="trove-cz-swatch-picker"
						id="<?php echo esc_attr( $this->id . '_' . $prop ); ?>"
						data-trove-setting="<?php echo esc_attr( $sid ); ?>"
						value="<?php echo esc_attr( $val ); ?>"
						data-default-color="<?php echo esc_attr( $def ); ?>"
					/>
				</span>
				<?php
			}
			?>
		</div>
		<?php
		if ( ! empty( $this->description ) ) {
			echo '<span class="description customize-control-description">' . wp_kses_post( $this->description ) . '</span>';
		}
	}
}
