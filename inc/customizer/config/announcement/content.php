<?php
/**
 * Customizer fields: Announcement Bar → content.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_announcement_lines' => array(
		'label'       => __( 'Announcements', 'trove' ),
		'description' => __( 'Add up to 10 announcemnts. Empty announcements are not shown on the site.', 'trove' ),
		'type'        => 'repeater_text',
		'default'     => \Trove\get_default_announcement_lines_json(),
		'max_items'   => 10,
		'transport'   => 'postMessage',
		'priority'    => 10,
	),
);
