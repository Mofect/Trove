<?php
/**
 * Trove functions and definitions.
 *
 * @package Trove
 */

namespace Trove;

! defined( 'TROVE_THEME_VERSION' ) && define( 'TROVE_THEME_VERSION', '1.0.0' );
! defined( 'TROVE_DIR' ) && define( 'TROVE_DIR', get_template_directory() );

require_once \TROVE_DIR . '/inc/class-theme.php';

TroveTheme::init();