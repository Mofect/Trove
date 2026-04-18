<?php
/**
 * Customizer fields: Social Profiles section.
 *
 * @package Trove
 */

namespace Trove\Customizer\Config;

defined( 'ABSPATH' ) || exit;

return array(
	'trove_social_facebook_url' => array(
		'label'       => __( 'Facebook URL', 'trove' ),
		'description' => __( 'Enter the URL of your Facebook profile.', 'trove' ),
		'type'        => 'url',
		'default'     => 'https://facebook.com/',
	),
	'trove_social_twitter_x_url' => array(
		'label'       => __( 'Twitter/X URL', 'trove' ),
		'description' => __( 'Enter the URL of your Twitter profile.', 'trove' ),
		'type'        => 'url',
		'default'     => 'https://x.com/',
	),
	'trove_social_instagram_url' => array(
		'label'       => __( 'Instagram URL', 'trove' ),
		'description' => __( 'Enter the URL of your Instagram profile.', 'trove' ),
		'type'        => 'url',
		'default'     => 'https://instagram.com/',
	),
	'trove_social_youtube_url' => array(
		'label'       => __( 'YouTube URL', 'trove' ),
		'description' => __( 'Enter the URL of your YouTube profile.', 'trove' ),
		'type'        => 'url',
		'default'     => '',
	),
	'trove_social_tiktok_url' => array(
		'label'       => __( 'TikTok URL', 'trove' ),
		'description' => __( 'Enter the URL of your TikTok profile.', 'trove' ),
		'type'        => 'url',
		'default'     => '',
	),
    'trove_social_vimeo_url' => array(
		'label'       => __( 'Vimeo URL', 'trove' ),
		'description' => __( 'Enter the URL of your Vimeo profile.', 'trove' ),
		'type'        => 'url',
		'default'     => '',
	),
	'trove_social_pinterest_url' => array(
		'label'       => __( 'Pinterest URL', 'trove' ),
		'description' => __( 'Enter the URL of your Pinterest profile.', 'trove' ),
		'type'        => 'url',
		'default'     => '',
	),
    'trove_social_threads_url' => array(
		'label'       => __( 'Threads URL', 'trove' ),
		'description' => __( 'Enter the URL of your Threads profile.', 'trove' ),
		'type'        => 'url',
		'default'     => '',
	),
	'trove_social_linkedin_url' => array(
		'label'       => __( 'LinkedIn URL', 'trove' ),
		'description' => __( 'Enter the URL of your LinkedIn profile.', 'trove' ),
		'type'        => 'url',
		'default'     => '',
	),
	'trove_social_github_url' => array(
		'label'       => __( 'GitHub URL', 'trove' ),
		'description' => __( 'Enter the URL of your GitHub profile.', 'trove' ),
		'type'        => 'url',
		'default'     => '',
	),
	'trove_social_reddit_url' => array(
		'label'       => __( 'Reddit URL', 'trove' ),
		'description' => __( 'Enter the URL of your Reddit profile.', 'trove' ),
		'type'        => 'url',
		'default'     => '',
	),
    'trove_social_vk_url' => array(
		'label'       => __( 'VK URL', 'trove' ),
		'description' => __( 'Enter the URL of your VK profile.', 'trove' ),
		'type'        => 'url',
		'default'     => '',
	),
    'trove_social_xing_url' => array(
		'label'       => __( 'Xing URL', 'trove' ),
		'description' => __( 'Enter the URL of your Xing profile.', 'trove' ),
		'type'        => 'url',
		'default'     => '',
	),
);