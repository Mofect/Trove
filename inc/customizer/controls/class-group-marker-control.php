<?php
/**
 * Invisible control so “group title” sections (no real fields) still appear in the sidebar.
 *
 * @package Trove
 */

namespace Trove\Customizer;

use WP_Customize_Control;

/**
 * Zero-output control bound to a noop setting.
 */
class Group_Marker_Control extends WP_Customize_Control {

	/**
	 * @var string
	 */
	public $type = 'trove_group_marker';

	/**
	 * @return void
	 */
	protected function render_content() {
		// Intentionally empty — the section row uses the section title only.
	}
}
