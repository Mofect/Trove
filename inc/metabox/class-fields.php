<?php
/**
 * Metabox field rendering and sanitization.
 *
 * @package Trove
 */

namespace Trove\Metabox;

defined( 'ABSPATH' ) || exit;

/**
 * Field helpers for the Trove metabox framework.
 */
final class Fields {

	/**
	 * Built-in types handled by this framework.
	 */
	private static function allowed_types() {
		return array(
			'text',
			'textarea',
			'checkbox',
			'switch',
			'select',
			'radio',
			'number',
			'range',
			'url',
			'email',
			'color',
			'color_pair',
			'hidden',
			'image_attachment',
			'alignment',
		);
	}

	/**
	 * Normalize a field definition.
	 *
	 * @param array<string, mixed> $field Field args.
	 * @return array<string, mixed>|null
	 */
	public static function normalize_field( array $field ) {
		$id = isset( $field['id'] ) ? sanitize_key( (string) $field['id'] ) : '';
		if ( '' === $id ) {
			return null;
		}

		$type = isset( $field['type'] ) ? sanitize_key( (string) $field['type'] ) : 'text';

		if ( 'color_pair' === $type ) {
			$pair_raw = isset( $field['pair'] ) && is_array( $field['pair'] ) ? $field['pair'] : array();
			$left     = isset( $pair_raw['left'] ) && is_array( $pair_raw['left'] ) ? $pair_raw['left'] : array();
			$right    = isset( $pair_raw['right'] ) && is_array( $pair_raw['right'] ) ? $pair_raw['right'] : array();
			$lid      = isset( $left['id'] ) ? sanitize_key( (string) $left['id'] ) : '';
			$rid      = isset( $right['id'] ) ? sanitize_key( (string) $right['id'] ) : '';
			if ( '' === $lid || '' === $rid || $lid === $rid ) {
				return null;
			}
			$sanitize_side = static function ( array $side, string $forced_id ) {
				$side['id'] = $forced_id;
				if ( ! array_key_exists( 'default', $side ) ) {
					$side['default'] = null;
				}
				return $side;
			};
			$field['pair'] = array(
				'left'  => $sanitize_side( $left, $lid ),
				'right' => $sanitize_side( $right, $rid ),
			);
			$hints = isset( $field['swatch_hints'] ) && is_array( $field['swatch_hints'] ) ? $field['swatch_hints'] : array();
			$field['swatch_hints'] = array(
				'left'  => isset( $hints['left'] ) ? (string) $hints['left'] : '',
				'right' => isset( $hints['right'] ) ? (string) $hints['right'] : '',
			);
		} elseif ( ! in_array( $type, self::allowed_types(), true ) ) {
			$type = 'text';
		}

		if ( 'alignment' === $type ) {
			$opts = isset( $field['options'] ) && is_array( $field['options'] ) ? $field['options'] : array();
			if ( empty( $opts ) ) {
				$field['options'] = array(
					'left'   => __( 'Left', 'trove' ),
					'center' => __( 'Center', 'trove' ),
					'right'  => __( 'Right', 'trove' ),
				);
			}
		}

		$field['id']          = $id;
		$field['type']      = $type;
		$field['name']      = isset( $field['name'] ) ? (string) $field['name'] : $id;
		$field['description'] = isset( $field['description'] ) ? (string) $field['description'] : '';
		$field['default']   = array_key_exists( 'default', $field ) ? $field['default'] : null;
		$field['options']   = isset( $field['options'] ) && is_array( $field['options'] ) ? $field['options'] : array();
		$field['unless_switch']     = isset( $field['unless_switch'] ) ? sanitize_key( (string) $field['unless_switch'] ) : '';
		$field['only_when_switch'] = isset( $field['only_when_switch'] ) ? sanitize_key( (string) $field['only_when_switch'] ) : '';
		$tab_raw                   = isset( $field['tab'] ) ? sanitize_key( (string) $field['tab'] ) : '';
		$field['tab']              = in_array( $tab_raw, array( 'content', 'header', 'footer', 'page_banner' ), true ) ? $tab_raw : '';

		return $field;
	}

	/**
	 * HTML attributes for number / range inputs (min, max, step).
	 *
	 * @param array<string, mixed> $field Normalized field.
	 * @return string Safe attribute fragment (leading space before each attr).
	 */
	private static function html_min_max_step_attrs( array $field ) {
		$extra = '';
		foreach ( array( 'min', 'max', 'step' ) as $k ) {
			if ( isset( $field[ $k ] ) && is_numeric( $field[ $k ] ) ) {
				$extra .= ' ' . $k . '="' . esc_attr( (string) $field[ $k ] ) . '"';
			}
		}
		return $extra;
	}

	/**
	 * SVG icon for alignment segmented control (matches Customizer Alignment_Control).
	 *
	 * @param string $value left|center|right
	 * @return string SVG markup (safe fixed strings).
	 */
	private static function alignment_option_icon_svg( $value ) {
		$v = sanitize_key( (string) $value );
		if ( 'left' === $v ) {
			return '<svg class="trove-cz-align__icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="4" y="6" width="12" height="2" rx="1" fill="currentColor"/><rect x="4" y="11" width="9" height="2" rx="1" fill="currentColor"/><rect x="4" y="16" width="13" height="2" rx="1" fill="currentColor"/></svg>';
		}
		if ( 'center' === $v ) {
			return '<svg class="trove-cz-align__icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="6" y="6" width="12" height="2" rx="1" fill="currentColor"/><rect x="8" y="11" width="8" height="2" rx="1" fill="currentColor"/><rect x="5" y="16" width="14" height="2" rx="1" fill="currentColor"/></svg>';
		}
		return '<svg class="trove-cz-align__icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="8" y="6" width="12" height="2" rx="1" fill="currentColor"/><rect x="11" y="11" width="9" height="2" rx="1" fill="currentColor"/><rect x="7" y="16" width="13" height="2" rx="1" fill="currentColor"/></svg>';
	}

	/**
	 * HTML name attribute for POST data.
	 *
	 * @param string $metabox_id Metabox id.
	 * @param string $field_id   Field id.
	 * @return string
	 */
	public static function input_name( $metabox_id, $field_id ) {
		return 'trove_mb_' . sanitize_key( $metabox_id ) . '_' . sanitize_key( $field_id );
	}

	/**
	 * Post meta key used to persist the value.
	 *
	 * @param string $metabox_id Metabox id.
	 * @param string $field_id   Field id.
	 * @return string
	 */
	public static function meta_key( $metabox_id, $field_id ) {
		return '_trove_mb_' . sanitize_key( $metabox_id ) . '_' . sanitize_key( $field_id );
	}

	/**
	 * Output one field.
	 *
	 * @param string               $metabox_id Metabox id.
	 * @param array<string, mixed> $field      Normalized field.
	 * @param mixed                $value      Stored value (unused for `color_pair`).
	 * @param int                  $post_id    Post ID (required for `color_pair` sub-values).
	 * @return void
	 */
	public static function render( $metabox_id, array $field, $value, $post_id = 0 ) {
		$post_id = (int) $post_id;
		$name     = self::input_name( $metabox_id, $field['id'] );
		$id_attr  = 'trove-mb-' . sanitize_key( $metabox_id ) . '-' . sanitize_key( $field['id'] );

		$unless_switch     = isset( $field['unless_switch'] ) ? sanitize_key( (string) $field['unless_switch'] ) : '';
		$only_when_switch  = isset( $field['only_when_switch'] ) ? sanitize_key( (string) $field['only_when_switch'] ) : '';
		$div_attrs         = '';
		$initial_hide      = false;
		if ( '' !== $unless_switch && $unless_switch !== $field['id'] ) {
			$peer_id_attr = 'trove-mb-' . sanitize_key( $metabox_id ) . '-' . $unless_switch;
			$div_attrs   .= ' data-trove-mb-hide-when-checked="' . esc_attr( $peer_id_attr ) . '"';
			if ( $post_id > 0 && wp_validate_boolean( get_post_meta( $post_id, self::meta_key( $metabox_id, $unless_switch ), true ) ) ) {
				$initial_hide = true;
			}
		}
		if ( '' !== $only_when_switch && $only_when_switch !== $field['id'] ) {
			$peer_id_attr2 = 'trove-mb-' . sanitize_key( $metabox_id ) . '-' . $only_when_switch;
			$div_attrs    .= ' data-trove-mb-hide-when-unchecked="' . esc_attr( $peer_id_attr2 ) . '"';
			if ( $post_id > 0 && ! wp_validate_boolean( get_post_meta( $post_id, self::meta_key( $metabox_id, $only_when_switch ), true ) ) ) {
				$initial_hide = true;
			}
		}
		if ( $initial_hide ) {
			$div_attrs .= ' style="display:none"';
		}

		echo '<div class="trove-metabox-field trove-metabox-field--' . esc_attr( $field['type'] ) . '"' . $div_attrs . '>';

		if ( 'checkbox' !== $field['type'] && 'switch' !== $field['type'] && 'range' !== $field['type'] && 'hidden' !== $field['type'] && 'color' !== $field['type'] && 'color_pair' !== $field['type'] && 'image_attachment' !== $field['type'] && 'alignment' !== $field['type'] ) {
			echo '<p class="trove-metabox-field__label"><label for="' . esc_attr( $id_attr ) . '">';
			echo esc_html( $field['name'] );
			echo '</label></p>';
		}

		switch ( $field['type'] ) {
			case 'textarea':
				printf(
					'<textarea class="widefat" id="%1$s" name="%2$s" rows="4">%3$s</textarea>',
					esc_attr( $id_attr ),
					esc_attr( $name ),
					esc_textarea( is_string( $value ) ? $value : '' )
				);
				break;

			case 'checkbox':
				$checked = wp_validate_boolean( $value );
				printf(
					'<p><label for="%1$s"><input type="hidden" name="%2$s" value="0" /><input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s /> %4$s</label></p>',
					esc_attr( $id_attr ),
					esc_attr( $name ),
					checked( $checked, true, false ),
					esc_html( $field['name'] )
				);
				break;

			case 'switch':
				$checked = wp_validate_boolean( $value );
				echo '<p class="trove-metabox-switch">';
				echo '<span class="trove-metabox-switch__text">';
				printf(
					'<label class="trove-metabox-switch__label" for="%1$s">%2$s</label>',
					esc_attr( $id_attr ),
					esc_html( $field['name'] )
				);
				echo '</span>';
				echo '<span class="trove-metabox-switch__control">';
				printf(
					'<input type="hidden" name="%1$s" value="0" />',
					esc_attr( $name )
				);
				printf(
					'<input type="checkbox" class="trove-metabox-switch__input" id="%1$s" name="%2$s" value="1" %3$s />',
					esc_attr( $id_attr ),
					esc_attr( $name ),
					checked( $checked, true, false )
				);
				printf(
					'<label class="trove-metabox-switch__track" for="%1$s"><span class="trove-metabox-switch__thumb" aria-hidden="true"></span></label>',
					esc_attr( $id_attr )
				);
				echo '</span>';
				echo '</p>';
				break;

			case 'select':
				printf( '<select class="widefat" id="%1$s" name="%2$s">', esc_attr( $id_attr ), esc_attr( $name ) );
				foreach ( $field['options'] as $opt_val => $opt_label ) {
					printf(
						'<option value="%1$s" %2$s>%3$s</option>',
						esc_attr( (string) $opt_val ),
						selected( (string) $value, (string) $opt_val, false ),
						esc_html( (string) $opt_label )
					);
				}
				echo '</select>';
				break;

			case 'alignment':
				$choices = isset( $field['options'] ) && is_array( $field['options'] ) ? $field['options'] : array();
				if ( empty( $choices ) ) {
					$choices = array(
						'left'   => __( 'Left', 'trove' ),
						'center' => __( 'Center', 'trove' ),
						'right'  => __( 'Right', 'trove' ),
					);
				}
				$current = is_string( $value ) ? $value : '';
				if ( '' === $current || ! isset( $choices[ $current ] ) ) {
					$keys    = array_keys( $choices );
					$current = (string) $keys[0];
				}
				echo '<p class="trove-metabox-field__label">' . esc_html( $field['name'] ) . '</p>';
				echo '<div class="trove-cz-align">';
				echo '<div class="trove-cz-align__group" role="radiogroup" aria-label="' . esc_attr( wp_strip_all_tags( $field['name'] ) ) . '">';
				foreach ( $choices as $opt_val => $opt_label ) {
					$opt_val     = (string) $opt_val;
					$is_checked  = ( $current === $opt_val );
					$radio_id    = $id_attr . '-align-' . sanitize_key( $opt_val );
					$icon        = self::alignment_option_icon_svg( $opt_val );
					echo '<label class="trove-cz-align__option" for="' . esc_attr( $radio_id ) . '">';
					printf(
						'<input type="radio" class="trove-cz-align__input" id="%1$s" name="%2$s" value="%3$s" %4$s />',
						esc_attr( $radio_id ),
						esc_attr( $name ),
						esc_attr( $opt_val ),
						checked( $is_checked, true, false )
					);
					echo '<span class="trove-cz-align__pill" aria-hidden="true">' . $icon . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed SVG markup.
					echo '<span class="screen-reader-text">' . esc_html( (string) $opt_label ) . '</span>';
					echo '</label>';
				}
				echo '</div></div>';
				break;

			case 'radio':
				if ( ! empty( $field['options'] ) ) {
					echo '<fieldset><legend class="screen-reader-text">' . esc_html( $field['name'] ) . '</legend>';
					$i = 0;
					foreach ( $field['options'] as $opt_val => $opt_label ) {
						$rid = $id_attr . '-' . $i++;
						printf(
							'<p><label for="%1$s"><input type="radio" id="%1$s" name="%2$s" value="%3$s" %4$s /> %5$s</label></p>',
							esc_attr( $rid ),
							esc_attr( $name ),
							esc_attr( (string) $opt_val ),
							checked( (string) $value, (string) $opt_val, false ),
							esc_html( (string) $opt_label )
						);
					}
					echo '</fieldset>';
				}
				break;

			case 'number':
				$num_val = is_numeric( $value ) ? (string) $value : '';
				$extra   = self::html_min_max_step_attrs( $field );
				if ( isset( $field['placeholder'] ) && '' !== (string) $field['placeholder'] ) {
					$extra .= ' placeholder="' . esc_attr( (string) $field['placeholder'] ) . '"';
				}
				printf(
					'<input class="widefat" type="number" id="%1$s" name="%2$s" value="%3$s"%4$s />',
					esc_attr( $id_attr ),
					esc_attr( $name ),
					esc_attr( $num_val ),
					$extra
				);
				break;

			case 'range':
				if ( is_numeric( $value ) ) {
					$range_val = (string) (int) $value;
				} elseif ( isset( $field['placeholder'] ) && is_numeric( $field['placeholder'] ) ) {
					$range_val = (string) (int) $field['placeholder'];
				} elseif ( isset( $field['min'] ) && is_numeric( $field['min'] ) ) {
					$range_val = (string) (int) $field['min'];
				} else {
					$range_val = '0';
				}
				$extra = self::html_min_max_step_attrs( $field );
				$show_number = ! empty( $field['show_number'] );

				echo '<div class="trove-metabox-field__range-row">';
				echo '<span class="trove-metabox-field__range-label">';
				printf(
					'<label class="trove-metabox-field__range-label-text" for="%1$s">%2$s</label>',
					esc_attr( $id_attr ),
					esc_html( $field['name'] )
				);
				echo '</span>';

				$id_range = $id_attr . '-range-ui';
				if ( $show_number ) {
					echo '<div class="trove-metabox-field__range-slider-row">';
					printf(
						'<input class="trove-metabox-range" type="range" id="%1$s" value="%2$s"%3$s />',
						esc_attr( $id_range ),
						esc_attr( $range_val ),
						$extra
					);
					$unit = isset( $field['number_unit'] ) ? (string) $field['number_unit'] : 'px';
					echo '<span class="trove-metabox-field__range-number-wrap">';
					printf(
						'<input class="trove-metabox-field__range-number" type="number" id="%1$s" name="%2$s" value="%3$s"%4$s />',
						esc_attr( $id_attr ),
						esc_attr( $name ),
						esc_attr( $range_val ),
						$extra
					);
					if ( '' !== $unit ) {
						printf(
							'<span class="trove-metabox-field__range-number-suffix">%s</span>',
							esc_html( $unit )
						);
					}
					echo '</span>';
					echo '</div>';
				} else {
					printf(
						'<input class="trove-metabox-range" type="range" id="%1$s" name="%2$s" value="%3$s"%4$s />',
						esc_attr( $id_attr ),
						esc_attr( $name ),
						esc_attr( $range_val ),
						$extra
					);
				}
				echo '</div>';
				break;

			case 'color':
				$val      = is_string( $value ) ? $value : '';
				$def_attr = isset( $field['default_color'] ) ? (string) $field['default_color'] : '';
				echo '<div class="trove-mb-cz-field-row">';
				echo '<p class="trove-metabox-field__label"><label for="' . esc_attr( $id_attr ) . '">';
				echo esc_html( $field['name'] );
				echo '</label></p>';
				echo '<div class="trove-mb-cz-field-row__controls">';
				echo '<div class="trove-mb-cz-color-single__row">';
				$swatch_tip = isset( $field['swatch_hint'] ) ? (string) $field['swatch_hint'] : (string) $field['name'];
				echo '<span class="trove-dual-color__pill" data-trove-swatch-tooltip="' . esc_attr( $swatch_tip ) . '">';
				printf(
					'<input type="text" class="trove-cz-swatch-picker regular-text" id="%1$s" name="%2$s" value="%3$s" data-default-color="%4$s" autocomplete="off" />',
					esc_attr( $id_attr ),
					esc_attr( $name ),
					esc_attr( $val ),
					esc_attr( $def_attr )
				);
				echo '</span>';
				printf(
					'<button type="button" class="button-link trove-mb-cz-color-clear trove-mb-cz-color-clear--single" title="%1$s" aria-label="%1$s"><span class="trove-mb-cz-color-clear__x" aria-hidden="true">&times;</span></button>',
					esc_attr__( 'Remove color', 'trove' )
				);
				echo '</div></div></div>';
				break;

			case 'color_pair':
				$pair  = isset( $field['pair'] ) && is_array( $field['pair'] ) ? $field['pair'] : array();
				$hints = isset( $field['swatch_hints'] ) && is_array( $field['swatch_hints'] ) ? $field['swatch_hints'] : array();
				echo '<div class="trove-mb-cz-field-row">';
				echo '<p class="trove-metabox-field__label">' . esc_html( $field['name'] ) . '</p>';
				echo '<div class="trove-mb-cz-field-row__controls">';
				echo '<div class="trove-mb-cz-color-pair-wrap">';
				echo '<div class="trove-dual-color__swatches">';
				foreach ( array( 'left', 'right' ) as $prop ) {
					if ( empty( $pair[ $prop ] ) || ! is_array( $pair[ $prop ] ) ) {
						continue;
					}
					$sw  = $pair[ $prop ];
					$pid = isset( $sw['id'] ) ? sanitize_key( (string) $sw['id'] ) : '';
					if ( '' === $pid ) {
						continue;
					}
					$iname   = self::input_name( $metabox_id, $pid );
					$iid     = 'trove-mb-' . sanitize_key( $metabox_id ) . '-' . sanitize_key( $pid );
					$sub_raw = array_merge(
						array(
							'type'    => 'text',
							'id'      => $pid,
							'default' => null,
						),
						$sw
					);
					$nf = self::normalize_field( $sub_raw );
					$st = $nf ? self::stored_value( $post_id, $metabox_id, $nf ) : null;
					$val = is_string( $st ) ? $st : '';
					$def_attr = isset( $sw['default_color'] ) ? (string) $sw['default_color'] : '';
					$hint = isset( $hints[ $prop ] ) ? (string) $hints[ $prop ] : '';
					if ( '' === trim( $hint ) && isset( $sw['name'] ) && is_string( $sw['name'] ) ) {
						$hint = (string) $sw['name'];
					}
					echo '<span class="trove-dual-color__pill" data-trove-swatch-tooltip="' . esc_attr( $hint ) . '">';
					printf(
						'<input type="text" class="trove-cz-swatch-picker regular-text" id="%1$s" name="%2$s" value="%3$s" data-default-color="%4$s" autocomplete="off" />',
						esc_attr( $iid ),
						esc_attr( $iname ),
						esc_attr( $val ),
						esc_attr( $def_attr )
					);
					echo '</span>';
				}
				echo '</div>';
				printf(
					'<button type="button" class="button-link trove-mb-cz-color-clear trove-mb-cz-color-clear--pair" title="%1$s" aria-label="%1$s"><span class="trove-mb-cz-color-clear__x" aria-hidden="true">&times;</span></button>',
					esc_attr__( 'Remove both colors', 'trove' )
				);
				echo '</div></div></div>';
				break;

			case 'hidden':
				printf(
					'<input type="hidden" id="%1$s" name="%2$s" value="%3$s" />',
					esc_attr( $id_attr ),
					esc_attr( $name ),
					esc_attr( is_scalar( $value ) ? (string) $value : '' )
				);
				break;

			case 'image_attachment':
				echo '<p class="trove-metabox-field__label"><label for="' . esc_attr( $id_attr ) . '">';
				echo esc_html( $field['name'] );
				echo '</label></p>';
				$aid   = is_numeric( $value ) ? absint( $value ) : 0;
				$thumb = $aid && wp_attachment_is_image( $aid ) ? wp_get_attachment_image_url( $aid, 'thumbnail' ) : '';
				echo '<div class="trove-metabox-field__media" data-trove-mb-media-field>';
				printf(
					'<input type="hidden" class="trove-metabox-field__media-input" id="%1$s" name="%2$s" value="%3$d" />',
					esc_attr( $id_attr ),
					esc_attr( $name ),
					$aid
				);
				echo '<div class="trove-metabox-field__media-preview">';
				if ( $thumb ) {
					printf(
						'<img src="%s" width="60" height="60" alt="" loading="lazy" decoding="async" />',
						esc_url( $thumb )
					);
				}
				echo '</div><p class="trove-metabox-field__media-actions">';
				printf(
					'<button type="button" class="button trove-mb-media-select">%s</button> ',
					esc_html__( 'Select image', 'trove' )
				);
				printf(
					'<button type="button" class="button-link trove-mb-media-remove">%s</button>',
					esc_html__( 'Remove', 'trove' )
				);
				echo '</p></div>';
				break;

			case 'email':
				printf(
					'<input class="widefat" type="email" id="%1$s" name="%2$s" value="%3$s" autocomplete="off" />',
					esc_attr( $id_attr ),
					esc_attr( $name ),
					esc_attr( is_string( $value ) ? $value : '' )
				);
				break;

			case 'url':
				printf(
					'<input class="widefat" type="url" id="%1$s" name="%2$s" value="%3$s" />',
					esc_attr( $id_attr ),
					esc_attr( $name ),
					esc_attr( is_string( $value ) ? $value : '' )
				);
				break;

			case 'text':
			default:
				printf(
					'<input class="widefat" type="text" id="%1$s" name="%2$s" value="%3$s" />',
					esc_attr( $id_attr ),
					esc_attr( $name ),
					esc_attr( is_string( $value ) ? $value : '' )
				);
				break;
		}

		if ( '' !== $field['description'] ) {
			echo '<p class="description">' . wp_kses_post( $field['description'] ) . '</p>';
		}

		echo '</div>';
	}

	/**
	 * Sanitize incoming POST value for a field.
	 *
	 * @param array<string, mixed> $field Field definition.
	 * @param mixed                $raw   Raw POST value.
	 * @return mixed
	 */
	public static function sanitize( array $field, $raw ) {
		if ( isset( $field['sanitize_callback'] ) && is_callable( $field['sanitize_callback'] ) ) {
			return call_user_func( $field['sanitize_callback'], $raw, $field );
		}

		switch ( $field['type'] ) {
			case 'checkbox':
			case 'switch':
				return wp_validate_boolean( $raw );

			case 'textarea':
				return isset( $raw ) ? sanitize_textarea_field( wp_unslash( (string) $raw ) ) : '';

			case 'number':
			case 'range':
				return '' === $raw || null === $raw ? '' : sanitize_text_field( wp_unslash( (string) $raw ) );

			case 'email':
				return sanitize_email( wp_unslash( (string) $raw ) );

			case 'url':
				return esc_url_raw( wp_unslash( (string) $raw ) );

			case 'color':
				return \Trove\Customizer\trove_sanitize_css_color( isset( $raw ) ? wp_unslash( $raw ) : '' );

			case 'image_attachment':
				$id = isset( $raw ) ? absint( wp_unslash( $raw ) ) : 0;
				if ( $id < 1 ) {
					return '';
				}
				return wp_attachment_is_image( $id ) ? (string) $id : '';

			case 'select':
			case 'radio':
			case 'alignment':
				$choices = ( isset( $field['options'] ) && is_array( $field['options'] ) )
					? array_keys( $field['options'] )
					: array( 'left', 'center', 'right' );
				$val     = sanitize_text_field( wp_unslash( (string) $raw ) );
				return in_array( $val, array_map( 'strval', $choices ), true ) ? $val : (string) ( $choices[0] ?? 'left' );

			case 'hidden':
			case 'text':
			default:
				return sanitize_text_field( wp_unslash( (string) $raw ) );
		}
	}

	/**
	 * Load stored value with default fallback.
	 *
	 * @param int                  $post_id    Post ID.
	 * @param string               $metabox_id Metabox id.
	 * @param array<string, mixed> $field      Normalized field.
	 * @return mixed
	 */
	public static function stored_value( $post_id, $metabox_id, array $field ) {
		$key = self::meta_key( $metabox_id, $field['id'] );
		$raw = get_post_meta( $post_id, $key, true );

		if ( '' === $raw || null === $raw ) {
			return $field['default'];
		}

		if ( 'checkbox' === $field['type'] || 'switch' === $field['type'] ) {
			return wp_validate_boolean( $raw );
		}

		return $raw;
	}
}
