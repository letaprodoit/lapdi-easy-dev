<?php
/*
Plugin Name: 	TSP Easy Dev
Plugin URI: 	http://www.thesoftwarepeople.com/software/plugins/wordpress/easy-dev-for-wordpress.html
Description: 	Easy Dev is an API for WordPress plugin development. Easy Dev makes OOD hot again!
Author: 		The Software People
Author URI: 	http://www.thesoftwarepeople.com/
Version: 		1.0
Text Domain: 	tsped
Copyright: 		Copyright © 2013 The Software People, LLC (www.thesoftwarepeople.com). All rights reserved
License: 		APACHE v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
*/

require_once(ABSPATH . 'wp-admin/includes/plugin.php' );

/**
* Every plugin that uses Easy Dev must define a UNIQUE variable that holds the plugin's file name
*
* @var string
*/
define('TSP_EASY_DEV_FILE', 				__FILE__ );
/**
* Every plugin that uses Easy Dev must define a UNIQUE variable that holds the plugin's absolute path
*
* @var string
*/
@define('TSP_EASY_DEV_PATH',					plugin_dir_path( __FILE__ ) );
/**
* Every plugin that uses Easy Dev must define a UNIQUE variable that holds the plugin's URL
*
* @var string
*/
@define('TSP_EASY_DEV_URL', 					plugin_dir_url( __FILE__ ) );
/**
* Every plugin that uses Easy Dev must define a UNIQUE variable that holds the plugin's base file name
*
* @var string
*/
define('TSP_EASY_DEV_BASE_NAME', 			plugin_basename( __FILE__ ) );
/**
* Every plugin that uses Easy Dev must define a UNIQUE variable that holds the plugin's name/id
*
* @var string
*/
define('TSP_EASY_DEV_NAME', 				'tsp-easy-dev');
/**
* Every plugin that uses Easy Dev must define a UNIQUE variable that holds the plugin's name (not description but plugin title)
*
* @var string
*/
define('TSP_EASY_DEV_TITLE', 				'TSP Easy Dev');
/**
* Every plugin that uses Easy Dev must define a UNIQUE variable that holds the plugin's required wordpress version
*
* @var string
*/
define('TSP_EASY_DEV_REQ_VERSION', 			"3.5.1");


global $easy_dev_settings;

include( TSP_EASY_DEV_PATH . 'TSP_Easy_Dev.register.php');
include( TSP_EASY_DEV_PATH . 'TSP_Easy_Dev.config.php');
include( TSP_EASY_DEV_PATH . 'TSP_Easy_Dev.extend.php');
//--------------------------------------------------------
// initialize the plugin
//--------------------------------------------------------

$easy_dev 										= new TSP_Easy_Dev( TSP_EASY_DEV_FILE , TSP_EASY_DEV_REQ_VERSION );

// Display the parent page but not the options page for this plugin
$easy_dev->set_options_handler( new TSP_Easy_Dev_Options_Easy_Dev( $easy_dev_settings, true, false ) );

$easy_dev->set_plugin_icon( TSP_EASY_DEV_ASSETS_IMAGES_URL . 'tsp_icon_16.png' );

$easy_dev->run( TSP_EASY_DEV_FILE );
?>