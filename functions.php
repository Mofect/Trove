<?php
/**
 * Trove functions and definitions.
 *
 * @package Trove
 */

namespace Trove;

define( 'TROVE_THEME_VERSION', '1.0.0' );

require_once get_template_directory() . '/inc/customizer/customizer.php';
require_once get_template_directory() . '/inc/class-theme.php';

TroveTheme::init();
