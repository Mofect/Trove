<?php
/**
 * Metabox definitions registry (CMB2-style registration API).
 *
 * @package Trove
 */

namespace Trove\Metabox;

defined( 'ABSPATH' ) || exit;

/**
 * Holds registered metabox configurations.
 */
final class Registry {

	/**
	 * @var array<string, array<string, mixed>>
	 */
	private static $boxes = array();

	/**
	 * Register a metabox configuration.
	 *
	 * @param string               $id   Unique id (sanitize_key), e.g. `page_settings`.
	 * @param array<string, mixed> $args {
	 *     @type string             $title       Metabox title.
	 *     @type string[]           $post_types  Post types that show this box.
	 *     @type string             $context     normal|side|advanced. Default normal.
	 *     @type string             $priority    high|core|default|low. Default high.
	 *     @type string             $capability  Capability required to edit. Default edit_post.
	 *     @type array<int, array>  $fields      Field definitions (optional per-field `tab`: content|header|footer). Page defaults live in `inc/metabox/config/page-settings.php`.
	 * }
	 * @return void
	 */
	public static function register( $id, array $args ) {
		$id = sanitize_key( (string) $id );
		if ( '' === $id ) {
			return;
		}

		$post_types = isset( $args['post_types'] ) && is_array( $args['post_types'] )
			? array_values( array_filter( array_map( 'sanitize_key', $args['post_types'] ) ) )
			: array();

		$defaults = array(
			'title'       => '',
			'post_types'  => array(),
			'context'     => 'normal',
			'priority'    => 'high',
			'capability'  => 'edit_post',
			'fields'      => array(),
		);

		$merged                = array_merge( $defaults, $args );
		$merged['post_types']  = $post_types;
		$merged['context']     = in_array( (string) $merged['context'], array( 'normal', 'side', 'advanced' ), true )
			? (string) $merged['context']
			: 'normal';
		$merged['priority']    = sanitize_key( (string) $merged['priority'] );
		if ( '' === $merged['priority'] ) {
			$merged['priority'] = 'high';
		}
		$merged['fields']      = is_array( $merged['fields'] ) ? $merged['fields'] : array();

		self::$boxes[ $id ] = $merged;
	}

	/**
	 * @return array<string, array<string, mixed>>
	 */
	public static function all() {
		return self::$boxes;
	}

	/**
	 * @param string $id Metabox id.
	 * @return array<string, mixed>|null
	 */
	public static function get( $id ) {
		$id = sanitize_key( (string) $id );
		return isset( self::$boxes[ $id ] ) ? self::$boxes[ $id ] : null;
	}
}
