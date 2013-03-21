<?php
/**
 * @package wp_mvc_admin_bar
 */
/*
  Plugin Name: WP MVC Admin Bar
  Plugin URI: https://github.com/david-coombes/wp-mvc-admin-bar
  Description: Extends the WP_Admin_Bar class to allow developers seperate the control, modal and view so they can manipulate the front-end code
  Version: 0.1
  Author: Daithi Coombes
  Author URI: http://david-coombes.com
 */

require_once( 'class-wp-mvc-admin-bar.php' );

$admin_bar = new WP_MVC_Admin_Bar();