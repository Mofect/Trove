<?php
/**
 * Horizontal rule for visual separation between option groups (no saved value).
 *
 * @package Trove
 */

namespace Trove\Customizer;

use WP_Customize_Control;

/**
 * Renders a divider line inside a section.
 */
final class Divider_Control extends WP_Customize_Control {

	/**
	 * @var string
	 */
	public $type = 'trove_divider';

	/**
	 * @param \WP_Customize_Manager $manager Manager.
	 * @param string                 $id      Control id (matches dummy setting id).
	 * @param array<string, mixed>   $args    Arguments.
	 */
	public function __construct( $manager, $id, array $args = array() ) {
		$args['settings'] = array( 'default' => $id );
		parent::__construct( $manager, $id, $args );
	}

	/**
	 * @return void
	 */
	protected function render_content() {
		echo '<span class="trove-cz-divider" role="presentation" aria-hidden="true"></span>';
	}
}
