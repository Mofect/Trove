<?php
/**
 * Customizer section: static group label (no accordion button).
 *
 * @package Trove
 */

namespace Trove\Customizer;

use WP_Customize_Section;

/**
 * Renders as plain heading text in the sidebar, not a &lt;button&gt; row.
 */
class Group_Title_Section extends WP_Customize_Section {

	/**
	 * @var string
	 */
	public $type = 'trove_group_title';

	/**
	 * @return void
	 */
	protected function render_template() {
		?>
		<li id="accordion-section-{{ data.id }}" class="accordion-section control-section control-section-{{ data.type }} cannot-expand trove-cz-group-title-section">
			<h3 class="accordion-section-title trove-cz-group-title__text">{{ data.title }}</h3>
			<# if ( data.description ) { #>
				<div class="trove-cz-group-title__extra">{{{ data.description }}}</div>
			<# } #>
		</li>
		<?php
	}
}
