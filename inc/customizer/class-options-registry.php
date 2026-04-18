<?php
/**
 * Maps declarative option arrays (Blocksy-style) to WP_Customize_Manager.
 *
 * @package Trove
 */

namespace Trove\Customizer;

use Trove\Customizer\Config;
use WP_Customize_Color_Control;
use WP_Customize_Image_Control;
use WP_Customize_Manager;

/**
 * Registers panels, sections, settings, and controls from config arrays.
 */
final class Options_Registry {

	/**
	 * Parent panel id, or null for root-level sections (Customizer sidebar list).
	 *
	 * @var string|null
	 */
	private $panel_id;

	/**
	 * Section id => [ 'setting' => tab theme_mod id, 'controls' => [ control_id => tab_slug ] ].
	 *
	 * @var array<string, array{setting:string, controls:array<string, string>}>
	 */
	private static $section_tabs_script_data = array();

	/**
	 * @param string|null $panel_id Parent panel id, or null to register sections at the root.
	 */
	public function __construct( $panel_id = null ) {
		$this->panel_id = $panel_id;
	}

	/**
	 * Section args for add_section: include `panel` when a parent panel applies.
	 *
	 * @param string      $title         Section title.
	 * @param string      $description   Section description HTML.
	 * @param int         $priority      Sort order.
	 * @param string|null $parent_panel  Panel id for this section, or null to use registry default.
	 * @return array<string, mixed>
	 */
	private function section_args( $title, $description, $priority, $parent_panel = null ) {
		$args = array(
			'title'       => $title,
			'description' => $description,
			'priority'    => $priority,
		);
		$panel = null !== $parent_panel ? $parent_panel : $this->panel_id;
		if ( $panel ) {
			$args['panel'] = $panel;
		}
		return $args;
	}

	/**
	 * Data for customizer-section-tabs.js (wp_localize_script).
	 *
	 * @return array{sections:array<string, array{setting:string, controls:array<string, string>}>}
	 */
	public static function get_section_tabs_script_data() {
		return array( 'sections' => self::$section_tabs_script_data );
	}

	/**
	 * @return void
	 */
	public static function reset_section_tabs_script_data() {
		self::$section_tabs_script_data = array();
	}

	/**
	 * @param string $section_id Section id.
	 * @param string $control_id Control id (matches HTML customize-control-* suffix).
	 * @param string $tab_slug   Tab key from config.
	 * @return void
	 */
	private function remember_control_tab( $section_id, $control_id, $tab_slug ) {
		if ( ! isset( self::$section_tabs_script_data[ $section_id ] ) ) {
			self::$section_tabs_script_data[ $section_id ] = array(
				'setting'  => '',
				'controls' => array(),
			);
		}
		self::$section_tabs_script_data[ $section_id ]['controls'][ $control_id ] = $tab_slug;
	}

	/**
	 * Walk the merged options tree.
	 *
	 * @param WP_Customize_Manager   $wp_customize Customizer instance.
	 * @param array<string, mixed> $config       Output of trove_get_customizer_options().
	 */
	public function register( WP_Customize_Manager $wp_customize, array $config ) {
		self::reset_section_tabs_script_data();

		foreach ( $config as $key => $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			if ( isset( $item['type'] ) && 'trove-group-title' === $item['type'] ) {
				$this->register_group_title( $wp_customize, $key, $item );
				continue;
			}

			if ( isset( $item['type'], $item['title'], $item['sections'] ) && 'trove-panel' === $item['type'] && is_array( $item['sections'] ) ) {
				$this->register_trove_panel( $wp_customize, $key, $item );
				continue;
			}

			if ( isset( $item['title'], $item['tabs'] ) && is_array( $item['tabs'] ) ) {
				$this->register_tabbed_section( $wp_customize, $key, $item );
				continue;
			}

			if ( isset( $item['title'], $item['options'] ) && is_array( $item['options'] ) ) {
				$this->register_section( $wp_customize, $key, $item );
			}
		}
	}

	/**
	 * Panel containing multiple sections (Customizer: panel → nested section list).
	 *
	 * @param WP_Customize_Manager     $wp_customize Customizer instance.
	 * @param string                   $key          Config key (panel id suffix).
	 * @param array<string, mixed> $item         Must include title, sections.
	 */
	private function register_trove_panel( WP_Customize_Manager $wp_customize, $key, array $item ) {
		$panel_id = 'trove_panel_' . sanitize_key( $key );
		$priority = 10;
		if ( isset( $item['container']['priority'] ) ) {
			$priority = (int) $item['container']['priority'];
		}

		$wp_customize->add_panel(
			$panel_id,
			array(
				'title'       => $item['title'],
				'description' => isset( $item['description'] ) ? $item['description'] : '',
				'priority'    => $priority,
			)
		);

		foreach ( $item['sections'] as $sub_key => $sub_item ) {
			if ( ! is_array( $sub_item ) ) {
				continue;
			}
			if ( isset( $sub_item['tabs'] ) && is_array( $sub_item['tabs'] ) ) {
				$this->register_tabbed_section( $wp_customize, $sub_key, $sub_item, $panel_id );
				continue;
			}
			if ( isset( $sub_item['title'], $sub_item['options'] ) && is_array( $sub_item['options'] ) ) {
				$this->register_section( $wp_customize, $sub_key, $sub_item, $panel_id );
			}
		}
	}

	/**
	 * Group title: static label row (Group_Title_Section — heading text, not an accordion button).
	 *
	 * @param WP_Customize_Manager    $wp_customize Customizer instance.
	 * @param string                  $key          Config key (section id suffix).
	 * @param array<string, mixed> $item         Group title definition.
	 */
	private function register_group_title( WP_Customize_Manager $wp_customize, $key, array $item ) {
		$section_id = 'trove_gt_' . sanitize_key( $key );
		$priority   = isset( $item['priority'] ) ? (int) $item['priority'] : 10;
		$title      = isset( $item['title'] ) ? $item['title'] : '';
		$desc       = '';
		if ( ! empty( $item['kind'] ) && 'divider' === $item['kind'] ) {
			$desc = '<span class="trove-cz-divider"></span>';
		}

		$wp_customize->add_section(
			new Group_Title_Section(
				$wp_customize,
				$section_id,
				array(
					'title'       => wp_strip_all_tags( $title ),
					'description' => $desc,
					'priority'    => $priority,
				)
			)
		);

		/*
		 * WordPress omits sections with zero controls from the sidebar. Bind a no-op control
		 * so the group heading row is listed (opening the section shows an empty pane).
		 */
		$marker_id = $section_id . '_trove_marker';
		$wp_customize->add_setting(
			$marker_id,
			array(
				'default'           => '',
				'sanitize_callback' => '__return_empty_string',
			)
		);
		$wp_customize->add_control(
			new Group_Marker_Control(
				$wp_customize,
				$marker_id,
				array(
					'section'  => $section_id,
					'settings' => $marker_id,
					'priority' => 1,
				)
			)
		);
	}

	/**
	 * Section with fields.
	 *
	 * @param WP_Customize_Manager    $wp_customize Customizer instance.
	 * @param string                  $key          Config key.
	 * @param array<string, mixed> $item         Section definition.
	 * @param string|null            $parent_panel Optional panel id (nested under trove-panel).
	 */
	private function register_section( WP_Customize_Manager $wp_customize, $key, array $item, $parent_panel = null ) {
		$section_id = 'trove_section_' . sanitize_key( $key );
		$priority   = 10;
		if ( isset( $item['container']['priority'] ) ) {
			$priority = (int) $item['container']['priority'];
		}

		$wp_customize->add_section(
			$section_id,
			$this->section_args(
				$item['title'],
				isset( $item['description'] ) ? $item['description'] : '',
				$priority,
				$parent_panel
			)
		);

		foreach ( $item['options'] as $setting_id => $field ) {
			if ( ! is_array( $field ) ) {
				continue;
			}
			$this->add_field( $wp_customize, $section_id, $setting_id, $field, null, null );
		}
	}

	/**
	 * Section with tabbed option groups (each tab has its own options array).
	 *
	 * @param WP_Customize_Manager     $wp_customize Customizer instance.
	 * @param string                   $key          Config key.
	 * @param array<string, mixed> $item         Section definition with 'tabs'.
	 * @param string|null            $parent_panel Optional panel id (nested under trove-panel).
	 */
	private function register_tabbed_section( WP_Customize_Manager $wp_customize, $key, array $item, $parent_panel = null ) {
		$section_id = 'trove_section_' . sanitize_key( $key );
		$priority   = 10;
		if ( isset( $item['container']['priority'] ) ) {
			$priority = (int) $item['container']['priority'];
		}

		$wp_customize->add_section(
			$section_id,
			$this->section_args(
				$item['title'],
				isset( $item['description'] ) ? $item['description'] : '',
				$priority,
				$parent_panel
			)
		);

		$tab_slugs = array_keys( $item['tabs'] );
		$first_tab = ! empty( $tab_slugs ) ? $tab_slugs[0] : 'general';

		$tab_setting_id = 'trove_cz_tab_' . sanitize_key( $key );
		$wp_customize->add_setting(
			$tab_setting_id,
			array(
				'default'           => $first_tab,
				'transport'         => 'postMessage',
				'sanitize_callback' => function ( $value ) use ( $tab_slugs ) {
					$value = sanitize_key( (string) $value );
					return in_array( $value, $tab_slugs, true ) ? $value : $tab_slugs[0];
				},
			)
		);

		$tab_labels = array();
		foreach ( $item['tabs'] as $tid => $tab_def ) {
			if ( ! is_array( $tab_def ) ) {
				continue;
			}
			$tab_labels[ $tid ] = isset( $tab_def['label'] ) ? $tab_def['label'] : $tid;
		}

		self::$section_tabs_script_data[ $section_id ] = array(
			'setting'  => $tab_setting_id,
			'controls' => array(),
		);

		$wp_customize->add_control(
			new Section_Tabs_Control(
				$wp_customize,
				$section_id . '_tabs',
				array(
					'label'       => '',
					'description' => '',
					'section'     => $section_id,
					'priority'    => 1,
					'settings'    => array( 'default' => $tab_setting_id ),
					'trove_tabs'  => $tab_labels,
				)
			)
		);

		$field_priority = 10;
		foreach ( $item['tabs'] as $tab_id => $tab_def ) {
			if ( ! is_array( $tab_def ) || empty( $tab_def['options'] ) || ! is_array( $tab_def['options'] ) ) {
				continue;
			}
			foreach ( $tab_def['options'] as $setting_id => $field ) {
				if ( ! is_array( $field ) ) {
					continue;
				}
				$this->add_field( $wp_customize, $section_id, $setting_id, $field, (string) $tab_id, $field_priority );
				++$field_priority;
			}
		}
	}

	/**
	 * Two color settings in one control row (label + two swatches).
	 *
	 * @param WP_Customize_Manager     $wp_customize Customizer instance.
	 * @param string                   $section_id   Section id.
	 * @param string                   $control_id   Control id (not a theme_mod key).
	 * @param array<string, mixed>     $field        Must include `pair` with `left`/`right` (setting + default).
	 * @param string|null              $tab_slug     Tab key when inside a tabbed section.
	 * @param int                      $control_priority Control order.
	 */
	private function add_color_pair_field( WP_Customize_Manager $wp_customize, $section_id, $control_id, array $field, $tab_slug, $control_priority ) {
		$pair = isset( $field['pair'] ) && is_array( $field['pair'] ) ? $field['pair'] : array();
		$left = isset( $pair['left'] ) && is_array( $pair['left'] ) ? $pair['left'] : array();
		$right = isset( $pair['right'] ) && is_array( $pair['right'] ) ? $pair['right'] : array();
		if ( empty( $left['setting'] ) || empty( $right['setting'] ) ) {
			return;
		}

		$transport = isset( $field['transport'] ) ? $field['transport'] : 'refresh';

		foreach ( array( $left, $right ) as $sw ) {
			$sid = $sw['setting'];
			$def = isset( $sw['default'] ) ? $sw['default'] : '';
			$wp_customize->add_setting(
				$sid,
				array(
					'default'           => $def,
					'transport'         => $transport,
					'sanitize_callback' => array( $this, 'sanitize_color_value' ),
				)
			);
		}

		$wp_customize->add_control(
			new Dual_Color_Control(
				$wp_customize,
				$control_id,
				array(
					'label'        => isset( $field['label'] ) ? $field['label'] : '',
					'description'  => isset( $field['description'] ) ? $field['description'] : '',
					'section'      => $section_id,
					'priority'     => $control_priority,
					'swatch_left'  => array(
						'setting' => $left['setting'],
						'default' => isset( $left['default'] ) ? (string) $left['default'] : '',
					),
					'swatch_right' => array(
						'setting' => $right['setting'],
						'default' => isset( $right['default'] ) ? (string) $right['default'] : '',
					),
				)
			)
		);
	}

	/**
	 * @param WP_Customize_Manager    $wp_customize Customizer instance.
	 * @param string                  $section_id   Section id.
	 * @param string                  $setting_id   Theme mod key (base id when responsive).
	 * @param array<string, mixed> $field        Field definition.
	 * @param string|null             $tab_slug     Tab key when inside a tabbed section.
	 * @param int|null                $priority     Control priority (null = use field default or 10).
	 */
	private function add_field( WP_Customize_Manager $wp_customize, $section_id, $setting_id, array $field, $tab_slug = null, $priority = null ) {
		if ( ! empty( $field['responsive'] ) ) {
			$this->add_responsive_field( $wp_customize, $section_id, $setting_id, $field, $tab_slug, $priority );
			return;
		}

		$type    = isset( $field['type'] ) ? $field['type'] : 'text';
		$default = isset( $field['default'] ) ? $field['default'] : '';

		$control_priority = $priority !== null ? (int) $priority : ( isset( $field['priority'] ) ? (int) $field['priority'] : 10 );

		if ( 'color_pair' === $type ) {
			$this->add_color_pair_field( $wp_customize, $section_id, $setting_id, $field, $tab_slug, $control_priority );
			if ( null !== $tab_slug && '' !== $tab_slug ) {
				$this->remember_control_tab( $section_id, $setting_id, $tab_slug );
			}
			return;
		}

		if ( 'heading' === $type ) {
			$wp_customize->add_setting(
				$setting_id,
				array(
					'default'           => '',
					'transport'         => isset( $field['transport'] ) ? $field['transport'] : 'refresh',
					'sanitize_callback' => '__return_empty_string',
				)
			);
			$heading_args = array(
				'label'    => isset( $field['label'] ) ? $field['label'] : '',
				'section'  => $section_id,
				'priority' => $control_priority,
			);
			if ( ! empty( $field['color_schemes'] ) && is_array( $field['color_schemes'] ) ) {
				$heading_args['trove_color_schemes'] = $field['color_schemes'];
			}
			$wp_customize->add_control(
				new Heading_Control(
					$wp_customize,
					$setting_id,
					$heading_args
				)
			);
			if ( null !== $tab_slug && '' !== $tab_slug ) {
				$this->remember_control_tab( $section_id, $setting_id, $tab_slug );
			}
			return;
		}

		if ( 'image_radio' === $type ) {
			$choices_map = isset( $field['choices'] ) && is_array( $field['choices'] ) ? $field['choices'] : array();
			if ( empty( $choices_map ) ) {
				return;
			}
			$choice_keys = array_keys( $choices_map );
			$def         = isset( $field['default'] ) ? (string) $field['default'] : $choice_keys[0];
			$def         = in_array( $def, $choice_keys, true ) ? $def : $choice_keys[0];

			$img_setting_args = array(
				'default'           => $def,
				'transport'         => isset( $field['transport'] ) ? $field['transport'] : 'refresh',
				'sanitize_callback' => function ( $value ) use ( $choice_keys ) {
					$value = sanitize_key( (string) $value );
					return in_array( $value, $choice_keys, true ) ? $value : $choice_keys[0];
				},
			);

			$wp_customize->add_setting( $setting_id, $img_setting_args );

			$img_control_args = array(
				'label'         => isset( $field['label'] ) ? $field['label'] : '',
				'description'   => isset( $field['description'] ) ? $field['description'] : '',
				'section'       => $section_id,
				'priority'      => $control_priority,
				'trove_choices' => $choices_map,
			);

			$wp_customize->add_control(
				new Image_Radio_Control(
					$wp_customize,
					$setting_id,
					$img_control_args
				)
			);
			if ( null !== $tab_slug && '' !== $tab_slug ) {
				$this->remember_control_tab( $section_id, $setting_id, $tab_slug );
			}
			return;
		}

		if ( 'alignment' === $type ) {
			$choices_map = isset( $field['choices'] ) && is_array( $field['choices'] ) ? $field['choices'] : array();
			if ( empty( $choices_map ) ) {
				$choices_map = array(
					'left'   => __( 'Left', 'trove' ),
					'center' => __( 'Center', 'trove' ),
					'right'  => __( 'Right', 'trove' ),
				);
			}
			$choice_keys = array_keys( $choices_map );
			$def         = isset( $field['default'] ) ? (string) $field['default'] : (string) $choice_keys[0];
			$def         = in_array( $def, $choice_keys, true ) ? $def : (string) $choice_keys[0];

			$align_setting_args = array(
				'default'           => $def,
				'transport'         => isset( $field['transport'] ) ? $field['transport'] : 'refresh',
				'sanitize_callback' => function ( $value ) use ( $choice_keys, $def ) {
					$value = sanitize_key( (string) $value );
					return in_array( $value, $choice_keys, true ) ? $value : $def;
				},
			);
			$wp_customize->add_setting( $setting_id, $align_setting_args );

			$align_control_args = array(
				'label'       => isset( $field['label'] ) ? $field['label'] : '',
				'description' => isset( $field['description'] ) ? $field['description'] : '',
				'section'     => $section_id,
				'priority'    => $control_priority,
				'choices'     => $choices_map,
			);
			$wp_customize->add_control(
				new Alignment_Control(
					$wp_customize,
					$setting_id,
					$align_control_args
				)
			);
			if ( null !== $tab_slug && '' !== $tab_slug ) {
				$this->remember_control_tab( $section_id, $setting_id, $tab_slug );
			}
			return;
		}

		if ( 'google_font' === $type ) {
			$def = isset( $field['default'] ) ? (string) $field['default'] : 'Instrument Sans';
			$def = \Trove\Customizer\sanitize_google_font_family( $def );

			$wp_customize->add_setting(
				$setting_id,
				array(
					'default'           => $def,
					'transport'         => isset( $field['transport'] ) ? $field['transport'] : 'refresh',
					'sanitize_callback' => 'Trove\\Customizer\\sanitize_google_font_family',
				)
			);

			$wp_customize->add_control(
				new Google_Font_Control(
					$wp_customize,
					$setting_id,
					array(
						'label'       => isset( $field['label'] ) ? $field['label'] : '',
						'description' => isset( $field['description'] ) ? $field['description'] : '',
						'section'     => $section_id,
						'priority'    => $control_priority,
					)
				)
			);
			if ( null !== $tab_slug && '' !== $tab_slug ) {
				$this->remember_control_tab( $section_id, $setting_id, $tab_slug );
			}
			return;
		}

		if ( 'number_unit' === $type ) {
			$unit_key = isset( $field['unit_setting'] ) ? (string) $field['unit_setting'] : '';
			if ( '' === $unit_key ) {
				return;
			}

			$num_default = isset( $field['number_default'] ) ? $field['number_default'] : 16;
			if ( isset( $field['default'] ) && is_array( $field['default'] ) && isset( $field['default']['number'] ) ) {
				$num_default = $field['default']['number'];
			}

			$unit_default = isset( $field['unit_default'] ) ? $field['unit_default'] : 'px';
			if ( isset( $field['default'] ) && is_array( $field['default'] ) && isset( $field['default']['unit'] ) ) {
				$unit_default = $field['default']['unit'];
			}

			$transport = isset( $field['transport'] ) ? $field['transport'] : 'refresh';

			$num_sanitize = isset( $field['sanitize_callback'] ) ? $field['sanitize_callback'] : array( $this, 'sanitize_by_type' );
			$unit_sanitize = isset( $field['unit_sanitize_callback'] ) ? $field['unit_sanitize_callback'] : array( $this, 'sanitize_by_type' );

			$wp_customize->add_setting(
				$setting_id,
				array(
					'default'           => $num_default,
					'transport'         => $transport,
					'sanitize_callback' => $num_sanitize,
				)
			);

			$wp_customize->add_setting(
				$unit_key,
				array(
					'default'           => $unit_default,
					'transport'         => $transport,
					'sanitize_callback' => $unit_sanitize,
				)
			);

			$unit_choices = isset( $field['unit_choices'] ) && is_array( $field['unit_choices'] ) ? $field['unit_choices'] : array();

			$wp_customize->add_control(
				new Number_Unit_Control(
					$wp_customize,
					$setting_id,
					array(
						'label'              => isset( $field['label'] ) ? $field['label'] : '',
						'description'        => isset( $field['description'] ) ? $field['description'] : '',
						'section'            => $section_id,
						'priority'           => $control_priority,
						'settings'           => array(
							'default' => $setting_id,
							'unit'    => $unit_key,
						),
						'number_input_attrs' => isset( $field['input_attrs'] ) && is_array( $field['input_attrs'] ) ? $field['input_attrs'] : array(),
						'unit_choices'       => $unit_choices,
					)
				)
			);
			if ( null !== $tab_slug && '' !== $tab_slug ) {
				$this->remember_control_tab( $section_id, $setting_id, $tab_slug );
			}
			return;
		}

		$setting_args = array(
			'default'   => $default,
			'transport' => isset( $field['transport'] ) ? $field['transport'] : 'refresh',
		);
		if ( 'color' === $type ) {
			$setting_args['sanitize_callback'] = array( $this, 'sanitize_color_value' );
		} elseif ( isset( $field['sanitize_callback'] ) ) {
			$setting_args['sanitize_callback'] = $field['sanitize_callback'];
		} else {
			$setting_args['sanitize_callback'] = array( $this, 'sanitize_by_type' );
		}

		$wp_customize->add_setting( $setting_id, $setting_args );

		$control_args = array(
			'label'       => isset( $field['label'] ) ? $field['label'] : '',
			'description' => isset( $field['description'] ) ? $field['description'] : '',
			'section'     => $section_id,
			'priority'    => $control_priority,
		);

		if ( 'color' === $type ) {
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					$setting_id,
					$control_args
				)
			);
			if ( null !== $tab_slug && '' !== $tab_slug ) {
				$this->remember_control_tab( $section_id, $setting_id, $tab_slug );
			}
			return;
		}

		if ( 'image' === $type ) {
			$wp_customize->add_control(
				new WP_Customize_Image_Control(
					$wp_customize,
					$setting_id,
					$control_args
				)
			);
			if ( null !== $tab_slug && '' !== $tab_slug ) {
				$this->remember_control_tab( $section_id, $setting_id, $tab_slug );
			}
			return;
		}

		if ( 'switch' === $type ) {
			$wp_customize->add_control(
				new Switch_Control(
					$wp_customize,
					$setting_id,
					$control_args
				)
			);
			if ( null !== $tab_slug && '' !== $tab_slug ) {
				$this->remember_control_tab( $section_id, $setting_id, $tab_slug );
			}
			return;
		}

		$control_args['type'] = $type;

		if ( ( 'number' === $type || 'range' === $type ) && isset( $field['input_attrs'] ) ) {
			$control_args['input_attrs'] = $field['input_attrs'];
		}

		if ( 'select' === $type && isset( $field['choices'] ) ) {
			$control_args['choices'] = $field['choices'];
		}

		$wp_customize->add_control( $setting_id, $control_args );

		if ( null !== $tab_slug && '' !== $tab_slug ) {
			$this->remember_control_tab( $section_id, $setting_id, $tab_slug );
		}
	}

	/**
	 * Registers three settings ({id}_pc, _tablet, _phone) and one tabbed control.
	 *
	 * Field keys:
	 * - `default` — scalar (same default for all devices) or array with keys pc, tablet, phone.
	 * - `responsive_labels` — optional map of device => short label (translated).
	 *
	 * @param WP_Customize_Manager    $wp_customize Customizer instance.
	 * @param string                  $section_id   Section id.
	 * @param string                  $setting_id   Base theme mod key (suffixes added per device).
	 * @param array<string, mixed> $field        Field definition.
	 * @param string|null             $tab_slug     Tab key when inside a tabbed section.
	 * @param int|null                $priority     Control priority.
	 */
	private function add_responsive_field( WP_Customize_Manager $wp_customize, $section_id, $setting_id, array $field, $tab_slug = null, $priority = null ) {
		$type = isset( $field['type'] ) ? $field['type'] : 'text';

		$devices = array(
			'pc'     => __( 'PC', 'trove' ),
			'tablet' => __( 'Tablet', 'trove' ),
			'phone'  => __( 'Phone', 'trove' ),
		);
		if ( ! empty( $field['responsive_labels'] ) && is_array( $field['responsive_labels'] ) ) {
			foreach ( $devices as $key => $label ) {
				if ( isset( $field['responsive_labels'][ $key ] ) && is_string( $field['responsive_labels'][ $key ] ) ) {
					$devices[ $key ] = $field['responsive_labels'][ $key ];
				}
			}
		}

		$raw_default = isset( $field['default'] ) ? $field['default'] : '';
		$defaults    = array();
		if ( is_array( $raw_default ) ) {
			foreach ( array_keys( $devices ) as $key ) {
				$defaults[ $key ] = isset( $raw_default[ $key ] ) ? $raw_default[ $key ] : ( isset( $raw_default['pc'] ) ? $raw_default['pc'] : '' );
			}
		} else {
			foreach ( array_keys( $devices ) as $key ) {
				$defaults[ $key ] = $raw_default;
			}
		}

		$base_label       = isset( $field['label'] ) ? $field['label'] : '';
		$base_description = isset( $field['description'] ) ? $field['description'] : '';
		$transport     = isset( $field['transport'] ) ? $field['transport'] : 'refresh';
		$priority_base = $priority !== null ? (int) $priority : ( isset( $field['priority'] ) ? (int) $field['priority'] : 10 );

		$custom_sanitize = isset( $field['sanitize_callback'] ) && is_callable( $field['sanitize_callback'] )
			? $field['sanitize_callback']
			: null;

		foreach ( $devices as $suffix => $_label ) {
			$sub_id = $setting_id . '_' . $suffix;
			$args   = array(
				'default'   => $defaults[ $suffix ],
				'transport' => $transport,
			);
			if ( $custom_sanitize ) {
				$args['sanitize_callback'] = $custom_sanitize;
			} else {
				$args['sanitize_callback'] = function ( $value ) use ( $type ) {
					return $this->sanitize_by_responsive_type( $value, $type );
				};
			}
			$wp_customize->add_setting( $sub_id, $args );
		}

		$control_args = array(
			'label'               => $base_label,
			'description'         => $base_description,
			'section'             => $section_id,
			'priority'            => $priority_base,
			'settings'            => array(
				'pc'     => $setting_id . '_pc',
				'tablet' => $setting_id . '_tablet',
				'phone'  => $setting_id . '_phone',
			),
			'field_type'          => $type,
			'trove_device_labels' => $devices,
		);

		if ( 'number' === $type && isset( $field['input_attrs'] ) ) {
			$control_args['trove_input_attrs'] = $field['input_attrs'];
		}
		if ( 'select' === $type && isset( $field['choices'] ) ) {
			$control_args['trove_choices'] = $field['choices'];
		}

		$wp_customize->add_control(
			new Responsive_Tabs_Control(
				$wp_customize,
				$setting_id . '_responsive',
				$control_args
			)
		);

		if ( null !== $tab_slug && '' !== $tab_slug ) {
			$this->remember_control_tab( $section_id, $setting_id . '_responsive', $tab_slug );
		}
	}

	/**
	 * Sanitizer for responsive settings when no custom callback is set (control id is not per-field type).
	 *
	 * @param mixed  $value Value.
	 * @param string $field_type Field type from config.
	 * @return mixed
	 */
	private function sanitize_by_responsive_type( $value, $field_type ) {
		switch ( $field_type ) {
			case 'checkbox':
			case 'switch':
				return (bool) $value;
			case 'number':
				return is_numeric( $value ) ? 0 + $value : 0;
			case 'color':
				return $this->sanitize_color_value( $value );
			default:
				return sanitize_text_field( (string) $value );
		}
	}

	/**
	 * Accepts hex and rgb/rgba colors for Customizer color controls.
	 *
	 * @param mixed $value Raw value.
	 * @return string
	 */
	public function sanitize_color_value( $value ) {
		$raw = is_string( $value ) ? trim( $value ) : trim( (string) $value );
		if ( '' === $raw ) {
			return '';
		}

		$hex = sanitize_hex_color( $raw );
		if ( $hex ) {
			return $hex;
		}

		if ( preg_match( '/^rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})(?:\s*,\s*(0|0?\.\d+|1(?:\.0+)?)\s*)?\)$/i', $raw, $m ) ) {
			$r = min( 255, max( 0, (int) $m[1] ) );
			$g = min( 255, max( 0, (int) $m[2] ) );
			$b = min( 255, max( 0, (int) $m[3] ) );
			if ( isset( $m[4] ) && '' !== $m[4] ) {
				$a = min( 1, max( 0, (float) $m[4] ) );
				$alpha = rtrim( rtrim( sprintf( '%.3F', $a ), '0' ), '.' );
				if ( '' === $alpha ) {
					$alpha = '0';
				}
				return sprintf( 'rgba(%d,%d,%d,%s)', $r, $g, $b, $alpha );
			}
			return sprintf( 'rgb(%d,%d,%d)', $r, $g, $b );
		}

		return '';
	}

	/**
	 * Fallback sanitizer when none provided.
	 *
	 * @param mixed                $value Value.
	 * @param WP_Customize_Setting $setting Setting instance.
	 * @return mixed
	 */
	public function sanitize_by_type( $value, $setting ) {
		$control = $setting->manager->get_control( $setting->id );
		$type    = ( $control && isset( $control->type ) ) ? $control->type : 'text';
		switch ( $type ) {
			case 'checkbox':
			case 'trove_switch':
				return (bool) $value;
			case 'trove_image_radio':
				return sanitize_key( (string) $value );
			case 'number':
				return is_numeric( $value ) ? 0 + $value : 0;
			case 'range':
				return is_numeric( $value ) ? absint( round( (float) $value ) ) : 0;
			default:
				return sanitize_text_field( (string) $value );
		}
	}
}
