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

if (class_exists('TSP_Easy_Dev'))
{
	add_action( 'admin_notices', function (){
		
		$message = 'TSP Easy Dev <strong>will not be installed</strong>, <a href="plugin-install.php?tab=search&type=term&s=TSP+Easy+Dev+Pro">TSP Easy Dev Pro</a> already installed.';
	    ?>
	    <div class="error">
	        <p><?php echo $message; ?></p>
	    </div>
	    <?php
	} );
	
	deactivate_plugins('tsp-easy-dev/tsp-easy-dev.php');
	
	return;
}//endif

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
define('TSP_EASY_DEV_PATH',					plugin_dir_path( __FILE__ ) );
/**
* Every plugin that uses Easy Dev must define a UNIQUE variable that holds the plugin's URL
*
* @var string
*/
define('TSP_EASY_DEV_URL', 					plugin_dir_url( __FILE__ ) );
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

/**
 * @ignore
 */
define('TSP_EASY_DEV_CLASS_PATH',			TSP_EASY_DEV_PATH . 'classes/');
/**
 * @ignore
 */
define('TSP_EASY_DEV_LIB_PATH',				TSP_EASY_DEV_CLASS_PATH . 'lib/');

/* @group Assets */
/**
 * Assets absolute path
 *
 * @ignore
 */
define('TSP_EASY_DEV_ASSETS_PATH',			TSP_EASY_DEV_PATH . 'assets/');

// Absolute directory paths
	/**
	 * Full absolute path to the Easy Dev templates directory
	 *
	 * @var string
	 */
	define('TSP_EASY_DEV_ASSETS_TEMPLATES_PATH',TSP_EASY_DEV_ASSETS_PATH . 'templates/');
	/**
	 * Full absolute path to the Easy Dev css directory
	 *
	 * @var string
	 */
	define('TSP_EASY_DEV_ASSETS_CSS_PATH',		TSP_EASY_DEV_ASSETS_PATH . 'css/');
	/**
	 * Full absolute path to the Easy Dev javascript directory
	 *
	 * @var string
	 */
	define('TSP_EASY_DEV_ASSETS_JS_PATH',		TSP_EASY_DEV_ASSETS_PATH . 'js/');
	/**
	 * Full absolute path to the Easy Dev images directory
	 *
	 * @var string
	 */
	define('TSP_EASY_DEV_ASSETS_IMAGES_PATH',	TSP_EASY_DEV_ASSETS_PATH . 'images/');

/**
 * Assets URL
 *
 * @ignore
 */
define('TSP_EASY_DEV_ASSETS_URL',				TSP_EASY_DEV_URL . 'assets/');

	/**
	 * Full URL to the Easy Dev templates directory
	 *
	 * @var string
	 */
	define('TSP_EASY_DEV_ASSETS_TEMPLATES_URL',	TSP_EASY_DEV_ASSETS_URL . 'templates/');
	/**
	 * Full URL to the Easy Dev css directory
	 *
	 * @var string
	 */
	define('TSP_EASY_DEV_ASSETS_CSS_URL',		TSP_EASY_DEV_ASSETS_URL . 'css/');
	/**
	 * Full URL to the Easy Dev javascript directory
	 *
	 * @var string
	 */
	define('TSP_EASY_DEV_ASSETS_JS_URL',		TSP_EASY_DEV_ASSETS_URL . 'js/');
	/**
	 * Full URL to the Easy Dev images directory
	 *
	 * @var string
	 */
	define('TSP_EASY_DEV_ASSETS_IMAGES_URL',	TSP_EASY_DEV_ASSETS_URL . 'images/');
/* @end */

// Store smarty cache and compiled directories
$upload_dir	= wp_upload_dir();
/**
 * Full absolute path to the Easy Dev temp directory
 *
 * @var string
 */
define('TSP_EASY_DEV_TMP_PATH',					$upload_dir['basedir'] . '/tsp_plugins/' );

global $easy_dev_settings;

include( TSP_EASY_DEV_PATH . 'TSP_Easy_Dev.config.php');
//--------------------------------------------------------
// initialize the plugin
//--------------------------------------------------------

$easy_dev 										= new TSP_Easy_Dev( __FILE__ , TSP_EASY_DEV_REQ_VERSION );

// If the plugin does not require settings the following three variables must be set
$easy_dev->plugin_title							= $easy_dev_settings['title'];
$easy_dev->plugin_name							= $easy_dev_settings['name'];
$easy_dev->plugin_base_name						= $easy_dev_settings['base_name'];

$easy_dev->run( __FILE__ );
?>