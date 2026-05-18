<?php
/**
 * TroveWP demo import definitions (One Click Demo Import).
 *
 * @package Trove
 */

namespace Trove\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Absolute path to demo import files directory.
 *
 * @param string $demo Demo folder slug.
 * @return string
 */
function get_trove_demo_dir( $demo = 'default' ) {
	return trailingslashit( \TROVE_DIR ) . 'inc/demos/' . sanitize_file_name( $demo ) . '/';
}

/**
 * Predefined demo imports for TroveWP.
 *
 * Add XML, widgets.json, and customizer.dat under inc/demos/{demo-slug}/.
 * Filter with `trove_demo_import_files` or use remote URLs via `import_file_url`, etc.
 *
 * @return array<int, array<string, mixed>>
 */
function get_trove_demo_import_files() {
	$demo_dir = get_trove_demo_dir( 'default' );

	$demos = array(
		array(
			'import_file_name'             => __( 'Trove Store', 'trove' ),
			'categories'                   => array( __( 'WooCommerce', 'trove' ), __( 'Elementor', 'trove' ) ),
			'local_import_file'            => $demo_dir . 'content.xml',
			'local_import_widget_file'     => $demo_dir . 'widgets.json',
			'local_import_customizer_file' => $demo_dir . 'customizer.dat',
			'import_preview_image_url'     => \TROVE_URI . '/inc/demos/default/screenshot.jpg',
			'preview_url'                  => 'https://trovewp.com/',
			'import_notice'                => __( 'Install WooCommerce and Elementor before importing. Demo media can take several minutes to download.', 'trove' ),
		),
	);

	/**
	 * Filters predefined demo imports for TroveWP.
	 *
	 * @param array<int, array<string, mixed>> $demos OCDI-compatible demo import arrays.
	 */
	return apply_filters( 'trove_demo_import_files', $demos );
}

/**
 * Only return demos whose content file exists (local or remote).
 *
 * @param array<int, array<string, mixed>> $demos Demo imports.
 * @return array<int, array<string, mixed>>
 */
function filter_trove_demo_import_files_with_content( $demos ) {
	$available = array();

	foreach ( $demos as $demo ) {
		if ( ! empty( $demo['local_import_file'] ) && file_exists( $demo['local_import_file'] ) ) {
			$available[] = $demo;
			continue;
		}

		if ( ! empty( $demo['import_file_url'] ) ) {
			$available[] = $demo;
		}
	}

	return $available;
}
