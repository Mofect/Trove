<?php
/**
 * Registers metaboxes, renders boxes, saves post meta.
 *
 * @package Trove
 */

namespace Trove\Metabox;

defined( 'ABSPATH' ) || exit;

/**
 * Hooks Trove metaboxes into WordPress admin.
 */
final class Manager {

	/**
	 * @return void
	 */
	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'register_boxes' ), 10, 1 );
		add_action( 'save_post', array( __CLASS__, 'save_post' ), 10, 2 );
	}

	/**
	 * @param string $post_type Current screen post type.
	 * @return void
	 */
	public static function register_boxes( $post_type ) {
		foreach ( Registry::all() as $id => $box ) {
			if ( ! in_array( $post_type, $box['post_types'], true ) ) {
				continue;
			}

			add_meta_box(
				'trove_mb_' . $id,
				$box['title'],
				array( __CLASS__, 'render_box' ),
				$post_type,
				$box['context'],
				$box['priority'],
				array(
					'trove_metabox_id' => $id,
				)
			);
		}
	}

	/**
	 * Metabox callback.
	 *
	 * @param \WP_Post $post    Post object.
	 * @param array    $box_args Meta box registration args (includes `args` key from WP).
	 * @return void
	 */
	public static function render_box( $post, $box_args ) {
		if ( ! isset( $box_args['args']['trove_metabox_id'] ) ) {
			return;
		}

		$id = sanitize_key( (string) $box_args['args']['trove_metabox_id'] );
		$config = Registry::get( $id );
		if ( null === $config ) {
			return;
		}

		wp_nonce_field( 'trove_mb_save_' . $id, '_trove_mb_nonce_' . $id );

		echo '<div class="trove-metabox-fields trove-mb-cz-layout">';

		$items = array();
		foreach ( $config['fields'] as $field_raw ) {
			$field = Fields::normalize_field( $field_raw );
			if ( null === $field ) {
				continue;
			}
			$items[] = $field;
		}

		$use_tabs = false;
		foreach ( $items as $field ) {
			if ( '' !== $field['tab'] ) {
				$use_tabs = true;
				break;
			}
		}

		// Always tabbed so grouped controls never render in a flat list if `tab` is stripped.
		if ( in_array( $id, array( 'page_settings', 'product_settings' ), true ) ) {
			$use_tabs = true;
		}

		if ( $use_tabs ) {
			self::render_metabox_tabbed_fields( $id, $post, $items );
		} else {
			foreach ( $items as $field ) {
				$value = ( 'color_pair' === $field['type'] )
					? null
					: Fields::stored_value( (int) $post->ID, $id, $field );
				Fields::render( $id, $field, $value, (int) $post->ID );
			}
		}

		echo '</div>';
	}

	/**
	 * Resolves which tab panel a field belongs in (used when grouping for output).
	 *
	 * For `page_settings`, header-related controls are always grouped under `header`
	 * so they never fall back to `content` if `tab` is missing or filtered.
	 *
	 * @param string               $metabox_id Metabox id.
	 * @param array<string, mixed> $field      Normalized field definition.
	 * @return string One of content|header|footer|page_banner.
	 */
	private static function field_tab_for_grouping( $metabox_id, array $field ) {
		$metabox_id = sanitize_key( (string) $metabox_id );
		$allowed    = array( 'content', 'header', 'footer', 'page_banner' );

		if ( PRODUCT_SETTINGS_METABOX_ID === $metabox_id ) {
			$tab = isset( $field['tab'] ) ? sanitize_key( (string) $field['tab'] ) : '';
			if ( in_array( $tab, array( 'gallery', 'content' ), true ) ) {
				return $tab;
			}
			return 'gallery';
		}

		if ( 'page_settings' === $metabox_id ) {
			$fid = isset( $field['id'] ) ? sanitize_key( (string) $field['id'] ) : '';
			if ( in_array(
				$fid,
				array(
					'transparent_header',
					'header_background_color',
					'header_text_color',
					'pair_header_links',
					'header_border_color',
				),
				true
			) ) {
				return 'header';
			}
			if ( in_array(
				$fid,
				array(
					'hide_page_title',
					'enable_page_banner',
					'page_banner_height',
					'page_title_alignment',
					'page_title_size',
					'page_title_color',
					'page_banner_background_image',
					'page_banner_background_image_mobile',
					'page_banner_background_color',
					'page_banner_overlay_color',
					'enable_page_banner_breadcrumb',
				),
				true
			) ) {
				return 'page_banner';
			}
		}

		$tab = isset( $field['tab'] ) ? sanitize_key( (string) $field['tab'] ) : '';
		if ( in_array( $tab, $allowed, true ) ) {
			return $tab;
		}

		return 'content';
	}

	/**
	 * Tab labels for a tabbed metabox.
	 *
	 * @param string $metabox_id Metabox id.
	 * @return array<string, string>
	 */
	private static function metabox_tabs_for( $metabox_id ) {
		$metabox_id = sanitize_key( (string) $metabox_id );

		if ( PRODUCT_SETTINGS_METABOX_ID === $metabox_id ) {
			/**
			 * Product settings metabox tab labels.
			 *
			 * @param array<string, string> $tabs Tab id => label.
			 */
			return apply_filters(
				'trove_product_settings_metabox_tabs',
				array(
					'gallery' => __( 'Gallery', 'trove' ),
					'content' => __( 'Content', 'trove' ),
				)
			);
		}

		/**
		 * Page settings metabox tab labels.
		 *
		 * @param array<string, string> $tabs Tab id => label.
		 */
		return apply_filters(
			'trove_page_settings_metabox_tabs',
			array(
				'page_banner' => __( 'Page banner', 'trove' ),
				'header'      => __( 'Header', 'trove' ),
				'content'     => __( 'Content', 'trove' ),
			)
		);
	}

	/**
	 * Renders fields grouped under metabox tabs.
	 *
	 * @param string    $metabox_id Metabox id.
	 * @param \WP_Post  $post       Post object.
	 * @param array<int, array<string, mixed>> $items Normalized field definitions (same order as registered).
	 * @return void
	 */
	private static function render_metabox_tabbed_fields( $metabox_id, $post, array $items ) {
		$metabox_id = sanitize_key( (string) $metabox_id );
		$post_id    = (int) $post->ID;

		$tabs    = self::metabox_tabs_for( $metabox_id );
		$grouped = array_fill_keys( array_keys( $tabs ), array() );

		foreach ( $items as $field ) {
			$tab = self::field_tab_for_grouping( $metabox_id, $field );
			$grouped[ $tab ][] = $field;
		}

		echo '<div class="trove-mb-tabs" data-trove-mb-tabs>';
		echo '<div class="trove-mb-tabs__nav" role="tablist">';

		$i = 0;
		foreach ( $tabs as $tid => $label ) {
			$btn_id    = 'trove-mb-tab-' . $metabox_id . '-' . $tid;
			$panel_id  = 'trove-mb-tabpanel-' . $metabox_id . '-' . $tid;
			$is_active = ( 0 === $i );
			printf(
				'<button type="button" class="trove-mb-tabs__btn%1$s" role="tab" id="%2$s" aria-controls="%3$s" aria-selected="%4$s" data-trove-mb-tab="%5$s">%6$s</button>',
				$is_active ? ' is-active' : '',
				esc_attr( $btn_id ),
				esc_attr( $panel_id ),
				$is_active ? 'true' : 'false',
				esc_attr( $tid ),
				esc_html( $label )
			);
			++$i;
		}

		echo '</div><div class="trove-mb-tabs__panels">';

		$i = 0;
		foreach ( $tabs as $tid => $label ) {
			$btn_id    = 'trove-mb-tab-' . $metabox_id . '-' . $tid;
			$panel_id  = 'trove-mb-tabpanel-' . $metabox_id . '-' . $tid;
			$is_active = ( 0 === $i );
			printf(
				'<div class="trove-mb-tab-panel%1$s" role="tabpanel" id="%2$s" aria-labelledby="%3$s" data-trove-mb-tab-panel="%4$s" tabindex="0"%5$s>',
				$is_active ? ' is-active' : '',
				esc_attr( $panel_id ),
				esc_attr( $btn_id ),
				esc_attr( $tid ),
				$is_active ? '' : ' style="display:none !important"'
			);

			foreach ( $grouped[ $tid ] as $field ) {
				$value = ( 'color_pair' === $field['type'] )
					? null
					: Fields::stored_value( $post_id, $metabox_id, $field );
				Fields::render( $metabox_id, $field, $value, $post_id );
			}

			echo '</div>';
		}

		echo '</div></div>';
	}

	/**
	 * Persist metabox fields.
	 *
	 * @param int     $post_id Post ID.
	 * @param \WP_Post $post   Post object.
	 * @return void
	 */
	public static function save_post( $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( ! is_object( $post ) || ! isset( $post->post_type ) ) {
			return;
		}

		foreach ( Registry::all() as $id => $box ) {
			if ( ! in_array( $post->post_type, $box['post_types'], true ) ) {
				continue;
			}

			$nonce_action = 'trove_mb_save_' . $id;
			$nonce_name   = '_trove_mb_nonce_' . $id;

			if ( ! isset( $_POST[ $nonce_name ] ) ) {
				continue;
			}

			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $nonce_name ] ) ), $nonce_action ) ) {
				continue;
			}

			$cap = isset( $box['capability'] ) ? (string) $box['capability'] : 'edit_post';
			if ( ! current_user_can( $cap, $post_id ) ) {
				continue;
			}

			foreach ( $box['fields'] as $field_raw ) {
				$field = Fields::normalize_field( $field_raw );
				if ( null === $field ) {
					continue;
				}

				if ( 'heading' === $field['type'] ) {
					continue;
				}

				if ( 'color_pair' === $field['type'] && ! empty( $field['pair'] ) && is_array( $field['pair'] ) ) {
					foreach ( array( 'left', 'right' ) as $side ) {
						if ( empty( $field['pair'][ $side ] ) || ! is_array( $field['pair'][ $side ] ) ) {
							continue;
						}
						$sub     = $field['pair'][ $side ];
						$sub_raw = array_merge(
							array(
								'type'    => 'text',
								'default' => null,
							),
							$sub
						);
						$sub_field = Fields::normalize_field( $sub_raw );
						if ( null === $sub_field ) {
							continue;
						}
						$input = Fields::input_name( $id, $sub_field['id'] );
						$key   = Fields::meta_key( $id, $sub_field['id'] );
						if ( ! isset( $_POST[ $input ] ) ) {
							continue;
						}
						$raw   = wp_unslash( $_POST[ $input ] );
						$value = Fields::sanitize( $sub_field, $raw );
						if ( '' === $value || null === $value ) {
							delete_post_meta( $post_id, $key );
						} else {
							update_post_meta( $post_id, $key, $value );
						}
					}
					continue;
				}

				$input = Fields::input_name( $id, $field['id'] );
				$key   = Fields::meta_key( $id, $field['id'] );

				if ( 'checkbox' === $field['type'] || 'switch' === $field['type'] ) {
					$raw   = isset( $_POST[ $input ] ) ? wp_unslash( $_POST[ $input ] ) : '0';
					$value = Fields::sanitize( $field, $raw );
					if ( $value ) {
						update_post_meta( $post_id, $key, '1' );
					} else {
						delete_post_meta( $post_id, $key );
					}
					continue;
				}

				if ( ! isset( $_POST[ $input ] ) ) {
					continue;
				}

				$raw   = wp_unslash( $_POST[ $input ] );
				$value = Fields::sanitize( $field, $raw );

				if ( '' === $value || null === $value ) {
					delete_post_meta( $post_id, $key );
				} else {
					update_post_meta( $post_id, $key, $value );
				}
			}

			if ( 'page_settings' === $id ) {
				$tkey = Fields::meta_key( $id, 'transparent_header' );
				if ( wp_validate_boolean( get_post_meta( $post_id, $tkey, true ) ) ) {
					delete_post_meta( $post_id, Fields::meta_key( $id, 'header_background_color' ) );
				}

				$banner_children = array(
					'page_banner_height',
					'page_title_alignment',
					'page_title_size',
					'page_title_color',
					'page_banner_background_image',
					'page_banner_background_image_mobile',
					'page_banner_background_color',
					'page_banner_overlay_color',
					'enable_page_banner_breadcrumb',
				);

				if ( ! wp_validate_boolean( get_post_meta( $post_id, Fields::meta_key( $id, 'enable_page_banner' ), true ) ) ) {
					foreach ( $banner_children as $bk ) {
						delete_post_meta( $post_id, Fields::meta_key( $id, $bk ) );
					}
				}

				if ( wp_validate_boolean( get_post_meta( $post_id, Fields::meta_key( $id, 'hide_page_title' ), true ) ) ) {
					delete_post_meta( $post_id, Fields::meta_key( $id, 'page_title_alignment' ) );
					delete_post_meta( $post_id, Fields::meta_key( $id, 'page_title_size' ) );
					delete_post_meta( $post_id, Fields::meta_key( $id, 'page_title_color' ) );
				}
			}
		}
	}
}
