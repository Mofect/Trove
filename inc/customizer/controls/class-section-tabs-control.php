<?php
/**
 * Section-level tab bar (General / Design / …) — switches which controls are visible below.
 *
 * @package Trove
 */

namespace Trove\Customizer;

use WP_Customize_Control;

/**
 * Renders tab buttons bound to a theme_mod that stores the active tab slug.
 */
class Section_Tabs_Control extends WP_Customize_Control {

	/**
	 * @var string
	 */
	public $type = 'trove_section_tabs';

	/**
	 * Tab slug => label.
	 *
	 * @var array<string, string>
	 */
	public $trove_tabs = array();

	/**
	 * @return void
	 */
	protected function render_content() {
		if ( empty( $this->trove_tabs ) || ! is_array( $this->trove_tabs ) ) {
			return;
		}

		$current = $this->value();
		if ( ! is_string( $current ) || ! array_key_exists( $current, $this->trove_tabs ) ) {
			$keys    = array_keys( $this->trove_tabs );
			$current = $keys[0];
		}

		$setting_id_attr = ( $this->setting instanceof \WP_Customize_Setting ) ? $this->setting->id : '';
		?>
		<div
			class="trove-cz-section-tabs"
			role="tablist"
			aria-label="<?php esc_attr_e( 'Section options', 'trove' ); ?>"
			data-setting-id="<?php echo esc_attr( $setting_id_attr ); ?>"
		>
			<?php
			$i = 0;
			foreach ( $this->trove_tabs as $slug => $label ) :
				$active = ( $slug === $current );
				?>
			<button
				type="button"
				class="trove-cz-section-tab<?php echo $active ? ' is-active' : ''; ?>"
				role="tab"
				data-trove-section-tab="<?php echo esc_attr( $slug ); ?>"
				aria-selected="<?php echo $active ? 'true' : 'false'; ?>"
				id="<?php echo esc_attr( $this->id . '-tab-' . $slug ); ?>"
			>
				<?php echo esc_html( $label ); ?>
			</button>
				<?php
				++$i;
			endforeach;
			?>
		</div>
		<input
			type="hidden"
			<?php $this->link(); ?>
			value="<?php echo esc_attr( $current ); ?>"
		/>
		<?php
	}
}
