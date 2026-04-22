<?php
/**
 * One-off generator: php inc/customizer/data/build-setting-data.php > inc/customizer/data/setting-data.json
 * (Run from theme root.)
 *
 * @package Trove
 */

$defaults = array(
	'trove_color_page_background'           => '#ffffff',
	'trove_color_base_text'                 => '#111111',
	'trove_color_headings'                  => '#000000',
	'trove_color_link'                      => '#333333',
	'trove_color_link_hover'                => '#064FFF',
	'trove_color_selection_text'            => '#ffffff',
	'trove_color_selection_bg'              => '#38BDF8',
	'trove_color_border'                    => '#eeeeee',
	'trove_color_accent'                    => '#064FFF',
	'trove_announcement_background'         => '#064fff',
	'trove_announcement_text_color'         => 'rgba(255, 255, 255, 0.8)',
	'trove_announcement_link_color'         => 'rgba(255, 255, 255, 1)',
	'trove_announcement_link_hover_color'   => 'rgba(255, 255, 255, 0.8)',
	'trove_header_background'               => '#ffffff',
	'trove_header_text_color'               => '#111111',
	'trove_header_link_color'               => '#000000',
	'trove_header_link_hover_color'         => '#666666',
	'trove_footer_background'               => '#eeeeee',
	'trove_footer_text_color'               => '#111111',
	'trove_footer_link_color'               => '#000000',
	'trove_footer_link_hover_color'         => '#666666',
	'trove_header_cart_badge_color'         => '#ff0000',
	'trove_header_cart_count_color'         => '#ffffff',
	'trove_store_notice_background'         => '#720eec',
	'trove_store_notice_text_color'         => '#ffffff',
	'trove_store_notice_link_color'         => '#ffffff',
	'trove_store_notice_link_hover_color'   => '#e8defc',
	'trove_content_width'                   => 800,
	'trove_content_wide_width'              => 1200,
	'trove_content_vertical_spacing'        => 48,
	'trove_content_horizontal_spacing'      => 20,
	'trove_button_background'               => '#111111',
	'trove_button_background_hover'         => '#064FFF',
	'trove_button_text_color'               => '#ffffff',
	'trove_button_text_color_hover'         => '#ffffff',
	'trove_button_border_color'             => '#111111',
	'trove_button_border_color_hover'       => '#333333',
	'trove_button_border_radius'            => 6,
	'trove_button_border_width'             => 1,
	'trove_button_text_font_size'           => 16,
	'trove_button_padding_pc'               => json_encode( array( 'top' => 10, 'right' => 15, 'bottom' => 10, 'left' => 15, 'linked' => false ) ),
	'trove_button_padding_tablet'           => json_encode( array( 'top' => 10, 'right' => 15, 'bottom' => 10, 'left' => 15, 'linked' => false ) ),
	'trove_button_padding_phone'            => json_encode( array( 'top' => 10, 'right' => 15, 'bottom' => 10, 'left' => 15, 'linked' => false ) ),
	'trove_button_secondary_background'       => 'transparent',
	'trove_button_secondary_background_hover' => '#111111',
	'trove_button_secondary_text_color'       => '#111111',
	'trove_button_secondary_text_color_hover' => '#ffffff',
	'trove_button_secondary_border_color'     => '#111111',
	'trove_button_secondary_border_color_hover' => '#111111',
	'trove_button_secondary_border_radius'    => 6,
	'trove_button_secondary_border_width'     => 1,
	'trove_button_secondary_text_font_size'   => 14,
	'trove_button_secondary_padding_pc'       => json_encode( array( 'top' => 5, 'right' => 20, 'bottom' => 5, 'left' => 20, 'linked' => false ) ),
	'trove_button_secondary_padding_tablet'   => json_encode( array( 'top' => 5, 'right' => 20, 'bottom' => 5, 'left' => 20, 'linked' => false ) ),
	'trove_button_secondary_padding_phone'    => json_encode( array( 'top' => 5, 'right' => 20, 'bottom' => 5, 'left' => 20, 'linked' => false ) ),
	'trove_input_field_height'              => 42,
	'trove_input_field_text_size'           => 16,
	'trove_input_field_border_width'        => 1,
	'trove_input_field_border_radius'       => 3,
	'trove_input_field_border_color'        => '#eeeeee',
	'trove_input_field_text_color'          => '#111111',
	'trove_textarea_field_height'           => 120,
	'trove_textarea_field_text_size'        => 16,
	'trove_textarea_field_border_width'     => 1,
	'trove_textarea_field_border_radius'    => 3,
	'trove_textarea_field_border_color'     => '#eeeeee',
	'trove_textarea_field_text_color'       => '#111111',
	'trove_announcement_enabled'            => array( 'pc' => true, 'tablet' => true, 'phone' => true ),
	'trove_announcement_height'             => array( 'pc' => 48, 'tablet' => 48, 'phone' => 48 ),
	'trove_announcement_height_unit'        => 'px',
	'trove_announcement_text_size'          => array( 'pc' => 'medium', 'tablet' => 'medium', 'phone' => 'medium' ),
	'trove_announcement_autoplay'           => false,
	'trove_announcement_autoplay_interval'  => 5,
	'trove_announcement_animation'          => 'slide',
	'trove_announcement_lines_default'      => array( 'Announcement 1', 'Announcement 2' ),
	'trove_header_logo'                     => '',
	'trove_header_logo_width'               => 200,
	'trove_header_sticky'                   => 'none',
	'trove_header_bottom_border'            => false,
	'trove_header_layout'                   => 'horizontal-left-logo',
	'trove_header_menu_alignment'           => 'right',
	'trove_header_min_height'               => 0,
	'trove_blog_pagination_type'            => 'paged',
	'trove_blog_list_featured_position'     => 'top',
	'trove_blog_show_featured_image'        => true,
	'trove_blog_show_date'                  => true,
	'trove_blog_show_author'                => true,
	'trove_blog_show_continue_reading'      => true,
	'trove_blog_layout'                     => 'list',
	'trove_blog_boxed_style'                => false,
	'trove_blog_boxed_border_radius'        => 10,
	'trove_search_page_pagination_type'     => 'paged',
	'trove_search_page_list_featured_position' => 'top',
	'trove_search_page_show_featured_image' => true,
	'trove_search_page_show_date'           => true,
	'trove_search_page_show_author'         => true,
	'trove_search_page_show_continue_reading' => true,
	'trove_search_page_layout'              => 'list',
	'trove_search_page_boxed_style'         => true,
	'trove_search_page_boxed_border_radius' => 10,
	'trove_author_page_pagination_type'     => 'paged',
	'trove_author_page_list_featured_position' => 'top',
	'trove_author_page_show_featured_image' => true,
	'trove_author_page_show_date'           => true,
	'trove_author_page_show_author'         => true,
	'trove_author_page_show_continue_reading' => true,
	'trove_author_page_layout'              => 'list',
	'trove_author_page_boxed_style'         => true,
	'trove_author_page_boxed_border_radius' => 10,
	'trove_archive_page_pagination_type'    => 'paged',
	'trove_archive_page_list_featured_position' => 'top',
	'trove_archive_page_show_featured_image' => true,
	'trove_archive_page_show_date'          => true,
	'trove_archive_page_show_author'        => true,
	'trove_archive_page_show_continue_reading' => true,
	'trove_archive_page_layout'             => 'list',
	'trove_archive_page_boxed_style'        => true,
	'trove_archive_page_boxed_border_radius' => 10,
	'trove_footer_layout'                   => 'footer-1',
	'trove_footer_top_border'               => true,
	'trove_footer_menu_1_id'                => 0,
	'trove_footer_menu_2_id'                => 0,
	'trove_footer_menu_3_id'                => 0,
	'trove_footer_menu_4_id'                => 0,
	'trove_footer_menu_5_id'                => 0,
	'trove_footer_show_brand_info'          => true,
	'trove_footer_logo'                     => '',
	'trove_footer_brand_name'               => '',
	'trove_footer_brand_description'        => '',
	'trove_footer_show_social_links'        => true,
	'trove_footer_show_newsletter'          => true,
	'trove_footer_newsletter_title'         => 'Subscribe to our emails',
	'trove_footer_newsletter_description'   => 'Subscribe to our newsletter to get the latest news and updates.',
	'trove_footer_newsletter_form'          => '',
	'trove_footer_copy_text'                => '',
	'trove_social_facebook_url'             => 'https://facebook.com/',
	'trove_social_twitter_x_url'            => 'https://x.com/',
	'trove_social_instagram_url'            => 'https://instagram.com/',
	'trove_social_youtube_url'              => '',
	'trove_social_tiktok_url'               => '',
	'trove_social_vimeo_url'                => '',
	'trove_social_pinterest_url'            => '',
	'trove_social_threads_url'              => '',
	'trove_social_linkedin_url'             => '',
	'trove_social_github_url'               => '',
	'trove_social_reddit_url'               => '',
	'trove_social_vk_url'                   => '',
	'trove_social_xing_url'                 => '',
	'trove_perf_placeholder'                => '',
);

$typography_scopes = array(
	'body'       => array( 'family' => 'Instrument Sans', 'size' => 16, 'size_unit' => 'px', 'weight' => 400, 'line' => 1.5, 'line_unit' => 'unitless', 'letter' => 0, 'letter_unit' => 'em' ),
	'menu'       => array( 'family' => 'Instrument Sans', 'size' => 16, 'size_unit' => 'px', 'weight' => 500, 'line' => 1.5, 'line_unit' => 'unitless', 'letter' => -0.02, 'letter_unit' => 'em' ),
	'h1'         => array( 'family' => 'Instrument Sans', 'size' => 4.0, 'size_unit' => 'rem', 'weight' => 600, 'line' => 1.1, 'line_unit' => 'unitless', 'letter' => -0.15, 'letter_unit' => 'rem' ),
	'h2'         => array( 'family' => 'Instrument Sans', 'size' => 3.0, 'size_unit' => 'rem', 'weight' => 600, 'line' => 1.2, 'line_unit' => 'unitless', 'letter' => -0.07, 'letter_unit' => 'rem' ),
	'h3'         => array( 'family' => 'Instrument Sans', 'size' => 2.4, 'size_unit' => 'rem', 'weight' => 600, 'line' => 1.2, 'line_unit' => 'unitless', 'letter' => -0.02, 'letter_unit' => 'rem' ),
	'h4'         => array( 'family' => 'Instrument Sans', 'size' => 2.2, 'size_unit' => 'rem', 'weight' => 600, 'line' => 1.2, 'line_unit' => 'unitless', 'letter' => -0.01, 'letter_unit' => 'rem' ),
	'h5'         => array( 'family' => 'Instrument Sans', 'size' => 2.0, 'size_unit' => 'rem', 'weight' => 600, 'line' => 1.2, 'line_unit' => 'unitless', 'letter' => -0.01, 'letter_unit' => 'rem' ),
	'h6'         => array( 'family' => 'Instrument Sans', 'size' => 1.8, 'size_unit' => 'rem', 'weight' => 600, 'line' => 1.2, 'line_unit' => 'unitless', 'letter' => -0.01, 'letter_unit' => 'rem' ),
	'blockquote' => array( 'family' => 'Playfair Display', 'size' => 1.125, 'size_unit' => 'rem', 'weight' => 500, 'line' => 1.6, 'line_unit' => 'unitless', 'letter' => 0, 'letter_unit' => 'em' ),
	'accent'     => array( 'family' => 'Playfair Display', 'size' => 1.125, 'size_unit' => 'rem', 'weight' => 500, 'line' => 1.6, 'line_unit' => 'unitless', 'letter' => 0, 'letter_unit' => 'em' ),
);

$family_key = array(
	'body'       => 'trove_google_font_family',
	'menu'       => 'trove_menu_google_font_family',
	'h1'         => 'trove_h1_google_font_family',
	'h2'         => 'trove_h2_google_font_family',
	'h3'         => 'trove_h3_google_font_family',
	'h4'         => 'trove_h4_google_font_family',
	'h5'         => 'trove_h5_google_font_family',
	'h6'         => 'trove_h6_google_font_family',
	'blockquote' => 'trove_blockquote_google_font_family',
	'accent'     => 'trove_accent_google_font_family',
);

foreach ( $typography_scopes as $scope => $t ) {
	$defaults[ $family_key[ $scope ] ] = $t['family'];
	$defaults[ 'trove_' . $scope . '_font_size' ]               = $t['size'];
	$defaults[ 'trove_' . $scope . '_font_size_unit' ]          = $t['size_unit'];
	$defaults[ 'trove_' . $scope . '_font_weight' ]             = $t['weight'];
	$defaults[ 'trove_' . $scope . '_font_line_height' ]        = $t['line'];
	$defaults[ 'trove_' . $scope . '_font_line_height_unit' ]   = $t['line_unit'];
	$defaults[ 'trove_' . $scope . '_font_letter_spacing' ]      = $t['letter'];
	$defaults[ 'trove_' . $scope . '_font_letter_spacing_unit' ] = $t['letter_unit'];
}

// Responsive typography defaults: heading sizes/line-height/letter-spacing.
$defaults['trove_h1_font_size_tablet'] = 3.5;
$defaults['trove_h1_font_size_phone'] = 2.5;
$defaults['trove_h1_font_line_height_tablet'] = 1.1;
$defaults['trove_h1_font_line_height_phone'] = 1.1;
$defaults['trove_h1_font_letter_spacing_tablet'] = -0.12;
$defaults['trove_h1_font_letter_spacing_phone'] = -0.05;

$defaults['trove_h2_font_size_tablet'] = 2.5;
$defaults['trove_h2_font_size_phone'] = 2.2;
$defaults['trove_h2_font_line_height_tablet'] = 1.2;
$defaults['trove_h2_font_line_height_phone'] = 1.2;
$defaults['trove_h2_font_letter_spacing_tablet'] = -0.05;
$defaults['trove_h2_font_letter_spacing_phone'] = -0.02;

$defaults['trove_h3_font_size_tablet'] = 2.2;
$defaults['trove_h3_font_size_phone'] = 2.0;
$defaults['trove_h3_font_line_height_tablet'] = 1.2;
$defaults['trove_h3_font_line_height_phone'] = 1.2;
$defaults['trove_h3_font_letter_spacing_tablet'] = -0.02;
$defaults['trove_h3_font_letter_spacing_phone'] = -0.01;

$defaults['trove_h4_font_size_tablet'] = 2.0;
$defaults['trove_h4_font_size_phone'] = 1.8;
$defaults['trove_h4_font_line_height_tablet'] = 1.2;
$defaults['trove_h4_font_line_height_phone'] = 1.2;
$defaults['trove_h4_font_letter_spacing_tablet'] = -0.01;
$defaults['trove_h4_font_letter_spacing_phone'] = -0.01;

$defaults['trove_h5_font_size_tablet'] = 1.8;
$defaults['trove_h5_font_size_phone'] = 1.6;
$defaults['trove_h5_font_line_height_tablet'] = 1.2;
$defaults['trove_h5_font_line_height_phone'] = 1.2;
$defaults['trove_h5_font_letter_spacing_tablet'] = -0.01;
$defaults['trove_h5_font_letter_spacing_phone'] = -0.01;

$defaults['trove_h6_font_size_tablet'] = 1.6;
$defaults['trove_h6_font_size_phone'] = 1.4;
$defaults['trove_h6_font_line_height_tablet'] = 1.2;
$defaults['trove_h6_font_line_height_phone'] = 1.2;
$defaults['trove_h6_font_letter_spacing_tablet'] = -0.01;
$defaults['trove_h6_font_letter_spacing_phone'] = -0.01;

$light_colors = array(
	'trove_color_page_background'           => '#ffffff',
	'trove_color_base_text'                 => '#111111',
	'trove_color_headings'                  => '#000000',
	'trove_color_link'                      => '#333333',
	'trove_color_link_hover'                => '#000000',
	'trove_color_selection_text'            => '#ffffff',
	'trove_color_selection_bg'              => '#38BDF8',
	'trove_color_border'                    => '#f3f3f3',
	'trove_color_accent'                    => '#38BDF8',
	'trove_announcement_background'         => '#064fff',
	'trove_announcement_text_color'         => 'rgba(255, 255, 255, 0.8)',
	'trove_announcement_link_color'         => 'rgba(255, 255, 255, 1)',
	'trove_announcement_link_hover_color'   => 'rgba(255, 255, 255, 0.8)',
	'trove_header_background'               => '#ffffff',
	'trove_header_text_color'               => '#111111',
	'trove_header_link_color'               => '#000000',
	'trove_header_link_hover_color'         => '#666666',
	'trove_footer_background'               => '#fafafa',
	'trove_footer_text_color'               => '#111111',
	'trove_footer_link_color'               => '#000000',
	'trove_footer_link_hover_color'         => '#666666',
	'trove_header_cart_badge_color'         => '#ff0000',
	'trove_header_cart_count_color'         => '#ffffff',
	'trove_store_notice_background'         => '#720eec',
	'trove_store_notice_text_color'         => '#ffffff',
	'trove_store_notice_link_color'        => '#ffffff',
	'trove_store_notice_link_hover_color'  => '#e8defc',
);

$dark_colors = array(
	'trove_color_page_background'           => '#111111',
	'trove_color_base_text'                 => '#efefef',
	'trove_color_headings'                  => '#ffffff',
	'trove_color_link'                      => '#ffffff',
	'trove_color_link_hover'                => '#efefef',
	'trove_color_selection_text'            => '#ffffff',
	'trove_color_selection_bg'              => '#4BB674',
	'trove_color_border'                    => '#222222',
	'trove_color_accent'                    => '#4BB674',
	'trove_announcement_background'         => '#467C5B',
	'trove_announcement_text_color'         => 'rgba(255, 255, 255, 0.8)',
	'trove_announcement_link_color'         => 'rgba(255, 255, 255, 1)',
	'trove_announcement_link_hover_color'   => 'rgba(255, 255, 255, 0.8)',
	'trove_header_background'               => '#151515',
	'trove_header_text_color'               => 'rgba(255, 255, 255, 0.8)',
	'trove_header_link_color'               => 'rgba(255, 255, 255, 1)',
	'trove_header_link_hover_color'         => 'rgba(255, 255, 255, 0.8)',
	'trove_footer_background'               => '#151515',
	'trove_footer_text_color'               => 'rgba(255, 255, 255, 0.8)',
	'trove_footer_link_color'               => 'rgba(255, 255, 255, 1)',
	'trove_footer_link_hover_color'         => 'rgba(255, 255, 255, 0.8)',
	'trove_header_cart_badge_color'         => '#ff0000',
	'trove_header_cart_count_color'         => '#ffffff',
	'trove_store_notice_background'         => '#720eec',
	'trove_store_notice_text_color'         => '#ffffff',
	'trove_store_notice_link_color'        => '#ffffff',
	'trove_store_notice_link_hover_color'  => '#e8defc',
);

$out = array(
	'defaults'               => $defaults,
	'color_scheme_presets'   => array(
		array( 'colors' => $light_colors ),
		array( 'colors' => $dark_colors ),
	),
);

echo json_encode( $out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n";
