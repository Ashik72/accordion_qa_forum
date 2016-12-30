<?php
/*
Plugin Name: Accordion Q/A Forum
Plugin URI: https://www.upwork.com/companies/~01caf98798b24dd9af
Description: Accordion Based Q/A Forum With Membership Support (s2Member)
Version: 0.0.1
Author: Ashik72
Author URI: https://www.upwork.com/companies/~01caf98798b24dd9af
License: GPLv2 or later
Text Domain: accordion_qa_forum
*/

if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));

  if (!class_exists('Kint') && file_exists(dirname( __FILE__ ) . '/kint/Kint.class.php'))
  	include_once ( dirname( __FILE__ ) . '/kint/Kint.class.php' );


  if (!function_exists('d')) {

  	function d($data) {

  		ob_start();
  		var_dump($data);
  		$output = ob_get_clean();
  		echo $output;
  	}
  }



define( 'accordion_qa_forum_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'accordion_qa_forum_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if ( ! defined( 'DS' ) ) define( 'DS', DIRECTORY_SEPARATOR );

require_once( plugin_dir_path( __FILE__ ) . 'plugin_loader.php' );
