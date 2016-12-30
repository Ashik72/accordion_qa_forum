<?php

if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));

//require_once( plugin_dir_path( __FILE__ ) . '/session/wp-session-manager.php' );

// require_once( 'titan-framework-checker.php' );
// require_once( 'titan-framework-options.php' );

require_once( plugin_dir_path( __FILE__ ) . '/inc/class.the_forum.php' );

$theForum = new TheForum();
