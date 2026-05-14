<?php
/**
 * Helpers for reading metabox-backed post meta.
 *
 * @package Trove
 */

namespace Trove\Metabox;

defined( 'ABSPATH' ) || exit;

/**
 * Read a single field value for the current or given post.
 *
 * Looks up the field definition from the registry so defaults and types match save/load.
 *
 * @param int         $post_id    Post ID (0 = current post in the loop).
 * @param string      $metabox_id Registered metabox id.
 * @param string      $field_id   Field id.
 * @param mixed|null  $default    Fallback when meta is empty.
 * @return mixed
 */
function get_post_field_value( $post_id, $metabox_id, $field_id, $default = null ) {
	$post_id = (int) $post_id;
	if ( $post_id < 1 ) {
		$post_id = (int) get_the_ID();
	}
	if ( $post_id < 1 ) {
		return $default;
	}

	$metabox_id = sanitize_key( (string) $metabox_id );
	$field_id   = sanitize_key( (string) $field_id );
	$config     = Registry::get( $metabox_id );
	if ( null === $config ) {
		return $default;
	}

	foreach ( $config['fields'] as $field_raw ) {
		if ( ! is_array( $field_raw ) ) {
			continue;
		}
		$f = Fields::normalize_field( $field_raw );
		if ( null === $f ) {
			continue;
		}

		$field_def = null;
		if ( 'color_pair' === $f['type'] && ! empty( $f['pair'] ) && is_array( $f['pair'] ) ) {
			foreach ( array( 'left', 'right' ) as $side ) {
				if ( empty( $f['pair'][ $side ] ) || ! is_array( $f['pair'][ $side ] ) ) {
					continue;
				}
				$sub    = $f['pair'][ $side ];
				$sub_id = isset( $sub['id'] ) ? sanitize_key( (string) $sub['id'] ) : '';
				if ( '' === $sub_id || $sub_id !== $field_id ) {
					continue;
				}
				$sub_field = array_merge(
					array(
						'type'    => 'text',
						'id'      => $sub_id,
						'default' => null,
					),
					$sub
				);
				$field_def = Fields::normalize_field( $sub_field );
				if ( null === $field_def ) {
					continue;
				}
				break;
			}
			if ( null === $field_def ) {
				continue;
			}
		} elseif ( $f['id'] === $field_id ) {
			$field_def = $f;
		} else {
			continue;
		}

		$value = Fields::stored_value( $post_id, $metabox_id, $field_def );
		if ( null === $value || '' === $value ) {
			return $default;
		}

		return $value;
	}

	return $default;
}
