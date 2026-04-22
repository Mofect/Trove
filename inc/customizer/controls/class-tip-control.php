<?php
/**
 * Static tip text row (no saved value).
 *
 * @package Trove
 */

namespace Trove\Customizer;

use WP_Customize_Control;
use WP_Customize_Manager;

/**
 * Renders description-only helper text inside a section.
 */
final class Tip_Control extends WP_Customize_Control {

	/**
	 * @var string
	 */
	public $type = 'trove_tip';

	/**
	 * @param WP_Customize_Manager   $manager Manager.
	 * @param string                 $id      Control id.
	 * @param array<string, mixed>   $args    Arguments.
	 */
	public function __construct( WP_Customize_Manager $manager, $id, array $args = array() ) {
		$args['settings'] = array( 'default' => $id );
		parent::__construct( $manager, $id, $args );
	}

	/**
	 * @return void
	 */
	protected function render_content() {
		if ( empty( $this->description ) ) {
			return;
		}
		echo '<p class="description customize-control-description trove-cz-tip">' . wp_kses_post( $this->description ) . '</p>';
	}
}

