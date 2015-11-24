<?php
/*
Plugin Name: MJJ Comment Ratings
Plugin URI:
Description: Adds in ratings fields to comments. This will *only* add in the ratings and not sort them.
Version: 1.0
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
} // end if

require_once( plugin_dir_path( __FILE__ ) . 'class-mjj-comment-ratings.php' );
require_once( plugin_dir_path( __FILE__ ) . 'class-mjj-comment-ratings-admin.php' );

MJJ_Comment_Ratings::get_instance();
MJJ_Comment_Ratings_Admin::get_instance();
