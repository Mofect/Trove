<?php
/**
 * Front output for Page settings → Page banner (between site header and main).
 *
 * @package Trove
 */

namespace Trove\Metabox;

defined( 'ABSPATH' ) || exit;

/**
 * Whether to show the singular H1 title (Page settings → Hide page title).
 *
 * @param int|null $post_id Post ID; null uses the current post in the loop.
 * @return bool
 */
function should_show_page_title( $post_id = null ) {
	$post_id = null === $post_id ? (int) get_the_ID() : (int) $post_id;
	if ( $post_id < 1 ) {
		return true;
	}

	$config = Registry::get( 'page_settings' );
	if ( null === $config || empty( $config['post_types'] ) ) {
		return true;
	}

	$post_type = get_post_type( $post_id );
	if ( ! in_array( $post_type, $config['post_types'], true ) ) {
		return true;
	}

	$hide = get_post_field_value( $post_id, 'page_settings', 'hide_page_title', false );
	return ! wp_validate_boolean( $hide );
}

/**
 * Whether the current post type uses the page_settings metabox and banner is enabled.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function is_page_banner_enabled( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id < 1 ) {
		return false;
	}

	$config = Registry::get( 'page_settings' );
	if ( null === $config || empty( $config['post_types'] ) ) {
		return false;
	}

	$post_type = get_post_type( $post_id );
	if ( ! in_array( $post_type, $config['post_types'], true ) ) {
		return false;
	}

	return wp_validate_boolean( get_post_field_value( $post_id, 'page_settings', 'enable_page_banner', false ) )
		|| wp_validate_boolean( get_post_meta( $post_id, Fields::meta_key( 'page_settings', 'enable_page_banner' ), true ) );
}

/**
 * Banner region includes the entry title (shown in banner instead of inside .site-main).
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function page_banner_shows_entry_title( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id < 1 ) {
		return false;
	}
	if ( ! is_page_banner_enabled( $post_id ) ) {
		return false;
	}

	return should_show_page_title( $post_id );
}

/**
 * Whether Page banner → Enable breadcrumb is on.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function is_page_banner_breadcrumb_enabled( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id < 1 || ! is_page_banner_enabled( $post_id ) ) {
		return false;
	}

	if ( wp_validate_boolean( get_post_field_value( $post_id, 'page_settings', 'enable_page_banner_breadcrumb', false ) ) ) {
		return true;
	}

	// Fallback: meta may exist even if registry lookup misses the field definition.
	$bkey = Fields::meta_key( 'page_settings', 'enable_page_banner_breadcrumb' );
	return wp_validate_boolean( get_post_meta( $post_id, $bkey, true ) );
}

/**
 * Built-in breadcrumb HTML: `nav.trove-page-banner__breadcrumb` > `ol.trove-page-banner__breadcrumb-list`.
 * Mirrors common WP patterns: site name (home) → context (categories / ancestors / archive) → current title.
 *
 * @param int $post_id Post ID.
 * @return string HTML or empty string.
 */
function build_page_banner_default_breadcrumb_html( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id < 1 ) {
		return '';
	}

	$post = get_post( $post_id );
	if ( ! $post instanceof \WP_Post ) {
		return '';
	}

	$items = array();

	$home_label = get_bloginfo( 'name', 'display' );
	if ( '' === trim( (string) $home_label ) ) {
		$home_label = __( 'Home', 'trove' );
	}
	$items[] = array(
		'href'  => home_url( '/' ),
		'label' => $home_label,
	);

	if ( 'page' === $post->post_type ) {
		$ancestor_ids = get_post_ancestors( $post_id, 'page' );
		if ( ! empty( $ancestor_ids ) ) {
			$ancestor_ids = array_reverse( array_map( 'absint', $ancestor_ids ) );
			foreach ( $ancestor_ids as $aid ) {
				if ( $aid < 1 ) {
					continue;
				}
				$ancestor = get_post( $aid );
				if ( ! $ancestor instanceof \WP_Post ) {
					continue;
				}
				if ( ! is_post_status_viewable( $ancestor->post_status ) && ! current_user_can( 'read_post', $aid ) ) {
					continue;
				}
				$items[] = array(
					'href'  => get_permalink( $aid ),
					'label' => get_the_title( $ancestor ),
				);
			}
		}
	} elseif ( 'post' === $post->post_type ) {
		$cats = get_the_category( $post_id );
		if ( ! empty( $cats ) ) {
			$primary = $cats[0];
			$chain   = array();
			$term    = $primary;
			$guard   = 0;
			while ( $term && ! is_wp_error( $term ) && $guard < 20 ) {
				array_unshift( $chain, $term );
				if ( empty( $term->parent ) ) {
					break;
				}
				$term = get_term( (int) $term->parent, 'category' );
				++$guard;
			}
			foreach ( $chain as $term ) {
				if ( ! $term instanceof \WP_Term ) {
					continue;
				}
				$link = get_term_link( $term );
				if ( is_wp_error( $link ) ) {
					continue;
				}
				$items[] = array(
					'href'  => $link,
					'label' => $term->name,
				);
			}
		}
	} else {
		$pto = get_post_type_object( $post->post_type );
		if ( $pto && ! empty( $pto->has_archive ) ) {
			$archive = get_post_type_archive_link( $post->post_type );
			if ( is_string( $archive ) && '' !== $archive ) {
				$archive_label = ( isset( $pto->labels->name ) && is_string( $pto->labels->name ) && '' !== $pto->labels->name )
					? $pto->labels->name
					: $post->post_type;
				$items[] = array(
					'href'  => $archive,
					'label' => $archive_label,
				);
			}
		}
		if ( is_post_type_hierarchical( $post->post_type ) ) {
			$ancestor_ids = get_post_ancestors( $post_id, $post->post_type );
			if ( ! empty( $ancestor_ids ) ) {
				$ancestor_ids = array_reverse( array_map( 'absint', $ancestor_ids ) );
				foreach ( $ancestor_ids as $aid ) {
					if ( $aid < 1 ) {
						continue;
					}
					$ancestor = get_post( $aid );
					if ( ! $ancestor instanceof \WP_Post ) {
						continue;
					}
					if ( ! is_post_status_viewable( $ancestor->post_status ) && ! current_user_can( 'read_post', $aid ) ) {
						continue;
					}
					$items[] = array(
						'href'  => get_permalink( $aid ),
						'label' => get_the_title( $ancestor ),
					);
				}
			}
		}
	}

	$title = get_the_title( $post );
	if ( '' === trim( (string) $title ) ) {
		$title = sprintf( __( 'Post #%d', 'trove' ), $post_id );
	}

	$items[] = array(
		'href'    => '',
		'label'   => $title,
		'current' => true,
	);

	$lis = array();
	foreach ( $items as $it ) {
		$label = isset( $it['label'] ) ? (string) $it['label'] : '';
		if ( '' === trim( $label ) ) {
			continue;
		}
		$li = '<li>';
		if ( ! empty( $it['href'] ) && empty( $it['current'] ) ) {
			$li .= '<a href="' . esc_url( $it['href'] ) . '">' . esc_html( $label ) . '</a>';
		} else {
			$li .= '<span';
			if ( ! empty( $it['current'] ) ) {
				$li .= ' aria-current="page"';
			}
			$li .= '>' . esc_html( $label ) . '</span>';
		}
		$li   .= '</li>';
		$lis[] = $li;
	}

	return '<nav class="trove-page-banner__breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'trove' ) . '"><ol class="trove-page-banner__breadcrumb-list">' . implode( '', $lis ) . '</ol></nav>';
}

/**
 * Breadcrumb markup for inside the page banner (positioned top-left in CSS).
 *
 * Always builds the theme trail first: `<nav class="trove-page-banner__breadcrumb">` with an
 * ordered list (`Home` link, parent pages, current page as `aria-current="page"`).
 * Filter `trove_page_banner_breadcrumb_html` receives that HTML and may replace or extend it.
 *
 * @param int $post_id Post ID.
 * @return string HTML or empty string.
 */
function build_page_banner_breadcrumb_html( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id < 1 || ! is_page_banner_breadcrumb_enabled( $post_id ) ) {
		return '';
	}

	$default = build_page_banner_default_breadcrumb_html( $post_id );
	if ( '' === trim( $default ) ) {
		return '';
	}

	/**
	 * Filter the page banner breadcrumb HTML (built-in: `nav.trove-page-banner__breadcrumb` > `ol` > `li`).
	 *
	 * @param string $html    Default markup generated by the theme.
	 * @param int    $post_id Post ID.
	 */
	$html = apply_filters( 'trove_page_banner_breadcrumb_html', $default, $post_id );

	if ( ! is_string( $html ) ) {
		return $default;
	}

	$html = trim( $html );
	if ( '' === $html ) {
		return $default;
	}

	if ( $html === $default ) {
		return $default;
	}

	return wp_kses_post( $html );
}

/**
 * @param int $post_id Post ID.
 * @return string Non-empty CSS or empty string.
 */
function build_page_banner_css( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id < 1 || ! is_page_banner_enabled( $post_id ) ) {
		return '';
	}

	$height_raw = get_post_field_value( $post_id, 'page_settings', 'page_banner_height', null );
	$height_px  = ( null !== $height_raw && '' !== (string) $height_raw && is_numeric( $height_raw ) )
		? max( 0, min( 600, (int) $height_raw ) )
		: 200;

	$desktop_id = get_post_field_value( $post_id, 'page_settings', 'page_banner_background_image', null );
	$mobile_id  = get_post_field_value( $post_id, 'page_settings', 'page_banner_background_image_mobile', null );
	$color_raw  = get_post_field_value( $post_id, 'page_settings', 'page_banner_background_color', null );

	$desktop_id = is_numeric( $desktop_id ) ? absint( $desktop_id ) : 0;
	$mobile_id  = is_numeric( $mobile_id ) ? absint( $mobile_id ) : 0;

	$desk_url = $desktop_id && wp_attachment_is_image( $desktop_id )
		? wp_get_attachment_image_url( $desktop_id, 'full' )
		: '';
	$mob_url  = $mobile_id && wp_attachment_is_image( $mobile_id )
		? wp_get_attachment_image_url( $mobile_id, 'full' )
		: '';

	$color = is_string( $color_raw ) && '' !== trim( $color_raw )
		? \Trove\Customizer\trove_sanitize_css_color( $color_raw )
		: '';

	$bid = 'trove-page-banner-' . $post_id;

	$rules   = array();
	$rules[] = '#' . $bid . '{min-height:' . $height_px . 'px;display:flex;align-items:center;box-sizing:border-box;background-size:cover;background-position:center center;background-repeat:no-repeat}';

	if ( '' !== $desk_url ) {
		$rules[] = '#' . $bid . '{background-image:url(' . esc_url_raw( $desk_url ) . ')}';
	}
	if ( '' !== $color ) {
		$rules[] = '#' . $bid . '{background-color:' . esc_attr( $color ) . '}';
	}
	if ( '' !== $mob_url ) {
		$rules[] = '@media(max-width:781px){#' . $bid . '{background-image:url(' . esc_url_raw( $mob_url ) . ')}}';
	}

	return implode( '', $rules );
}

/**
 * @return void
 */
function enqueue_front_page_banner_css() {
	if ( ! is_singular() ) {
		return;
	}
	if ( ! wp_style_is( 'trove-style', 'enqueued' ) ) {
		return;
	}

	$post_id = (int) get_queried_object_id();
	$css      = build_page_banner_css( $post_id );
	if ( '' === $css ) {
		return;
	}

	wp_add_inline_style( 'trove-style', $css );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_front_page_banner_css', 26 );

/**
 * Renders the page banner after the theme header (before .site-main).
 *
 * @return void
 */
function render_page_banner_after_header() {
	if ( ! is_singular() ) {
		return;
	}

	$post_id = (int) get_queried_object_id();
	if ( $post_id < 1 || ! is_page_banner_enabled( $post_id ) ) {
		return;
	}

	$align = get_post_field_value( $post_id, 'page_settings', 'page_title_alignment', 'left' );
	$align = is_string( $align ) ? sanitize_key( $align ) : 'left';
	if ( ! in_array( $align, array( 'left', 'center', 'right' ), true ) ) {
		$align = 'left';
	}

	$size = get_post_field_value( $post_id, 'page_settings', 'page_title_size', 'medium' );
	$size = is_string( $size ) ? sanitize_key( $size ) : 'medium';
	if ( ! in_array( $size, array( 'small', 'medium', 'large' ), true ) ) {
		$size = 'medium';
	}

	$bid = 'trove-page-banner-' . $post_id;

	$overlay_raw = get_post_field_value( $post_id, 'page_settings', 'page_banner_overlay_color', null );
	$overlay     = is_string( $overlay_raw ) && '' !== trim( $overlay_raw )
		? \Trove\Customizer\trove_sanitize_css_color( $overlay_raw )
		: '';
	if ( 'transparent' === strtolower( (string) $overlay ) ) {
		$overlay = '';
	}

	$title_color_raw = get_post_field_value( $post_id, 'page_settings', 'page_title_color', null );
	$title_color     = is_string( $title_color_raw ) && '' !== trim( $title_color_raw )
		? \Trove\Customizer\trove_sanitize_css_color( $title_color_raw )
		: '';
	if ( 'transparent' === strtolower( (string) $title_color ) ) {
		$title_color = '';
	}

	$show_title = page_banner_shows_entry_title( $post_id );
	$title      = $show_title ? get_the_title( $post_id ) : '';

	$breadcrumb_html = build_page_banner_breadcrumb_html( $post_id );

	$banner_style = '';
	if ( '' !== $title_color ) {
		$banner_style = ' style="--trove-page-banner-title-color:' . esc_attr( $title_color ) . '"';
	}

	$banner_classes = array(
		'trove-page-banner',
		'trove-page-banner--align-' . $align,
	);
	if ( '' !== $breadcrumb_html ) {
		$banner_classes[] = 'trove-page-banner--has-breadcrumb';
	}

	echo '<div id="' . esc_attr( $bid ) . '" class="' . esc_attr( implode( ' ', $banner_classes ) ) . '"' . $banner_style . ' role="region" aria-label="' . esc_attr__( 'Page banner', 'trove' ) . '">';
	if ( '' !== $overlay ) {
		echo '<div class="trove-page-banner__overlay" style="background-color:' . esc_attr( $overlay ) . '" aria-hidden="true"></div>';
	}
	if ( '' !== $breadcrumb_html ) {
		echo $breadcrumb_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup from build_page_banner_breadcrumb_html() (escaped in builder or via wp_kses_post on filter output).
	}
	echo '<div class="trove-page-banner__inner">';
	if ( $show_title && '' !== $title ) {
		echo '<h1 class="trove-page-banner__title entry-title trove-page-banner__title--size-' . esc_attr( $size ) . '">' . esc_html( $title ) . '</h1>';
	}
	echo '</div></div>';
}
add_action( 'trove_after_header', __NAMESPACE__ . '\\render_page_banner_after_header', 10 );
