<?php
/*
Plugin Name: 	TSP Easy Plugins
Plugin URI: 	http://www.thesoftwarepeople.com/software/plugins/wordpress/easy-plugins-for-wordpress.html
Description: 	Easy Plugins is an API for WordPress plugin creation. Easy Plugins makes OOD hot again!
Author: 		The Software People
Author URI: 	http://www.thesoftwarepeople.com/
Version: 		1.0
Text Domain: 	tspep
Copyright: 		Copyright © 2013 The Software People, LLC (www.thesoftwarepeople.com). All rights reserved
License: 		APACHE v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
*/

require_once(ABSPATH . 'wp-admin/includes/plugin.php' );

if (class_exists('TSP_Easy_Plugins'))
{
	add_action( 'admin_notices', function (){
		
		$message = 'TSP Easy Plugins <strong>will not be installed</strong>, <a href="plugin-install.php?tab=search&type=term&s=TSP+Easy+Plugins+Pro">TSP Easy Plugins Pro</a> already installed.';
	    ?>
	    <div class="error">
	        <p><?php echo $message; ?></p>
	    </div>
	    <?php
	} );
	
	deactivate_plugins('tsp-easy-plugins/tsp-easy-plugins.php');
	
	return;
}//endif

/**
* Every plugin that uses Easy Plugins must define a UNIQUE variable that holds the plugin's file name
*
* @var string
*/
define('TSP_EASY_PLUGINS_FILE', 					__FILE__ );
/**
* Every plugin that uses Easy Plugins must define a UNIQUE variable that holds the plugin's absolute path
*
* @var string
*/
define('TSP_EASY_PLUGINS_PATH',						plugin_dir_path( __FILE__ ) );
/**
* Every plugin that uses Easy Plugins must define a UNIQUE variable that holds the plugin's URL
*
* @var string
*/
define('TSP_EASY_PLUGINS_URL', 						plugin_dir_url( __FILE__ ) );
/**
* Every plugin that uses Easy Plugins must define a UNIQUE variable that holds the plugin's name/id
*
* @var string
*/
define('TSP_EASY_PLUGINS_NAME', 					'tsp-easy-plugins');
/**
* Every plugin that uses Easy Plugins must define a UNIQUE variable that holds the plugin's name (not description but plugin title)
*
* @var string
*/
define('TSP_EASY_PLUGINS_TITLE', 					'TSP Easy Plugins');

/**
 * @ignore
 */
define('TSP_EASY_PLUGINS_CLASS_PATH',				TSP_EASY_PLUGINS_PATH . 'classes/');
/**
 * @ignore
 */
define('TSP_EASY_PLUGINS_LIB_PATH',					TSP_EASY_PLUGINS_PATH . 'lib/');

/* @group Assets */
/**
 * Assets absolute path
 *
 * @ignore
 */
define('TSP_EASY_PLUGINS_ASSETS_PATH',				TSP_EASY_PLUGINS_PATH . 'assets/');

// Absolute directory paths
	/**
	 * Full absolute path to the Easy Plugins templates directory
	 *
	 * @var string
	 */
	define('TSP_EASY_PLUGINS_ASSETS_TEMPLATES_PATH',TSP_EASY_PLUGINS_ASSETS_PATH . 'templates/');
	/**
	 * Full absolute path to the Easy Plugins css directory
	 *
	 * @var string
	 */
	define('TSP_EASY_PLUGINS_ASSETS_CSS_PATH',		TSP_EASY_PLUGINS_ASSETS_PATH . 'css/');
	/**
	 * Full absolute path to the Easy Plugins javascript directory
	 *
	 * @var string
	 */
	define('TSP_EASY_PLUGINS_ASSETS_JS_PATH',		TSP_EASY_PLUGINS_ASSETS_PATH . 'js/');
	/**
	 * Full absolute path to the Easy Plugins images directory
	 *
	 * @var string
	 */
	define('TSP_EASY_PLUGINS_ASSETS_IMAGES_PATH',	TSP_EASY_PLUGINS_ASSETS_PATH . 'images/');

/**
 * Assets URL
 *
 * @ignore
 */
define('TSP_EASY_PLUGINS_ASSETS_URL',				TSP_EASY_PLUGINS_URL . 'assets/');

	/**
	 * Full URL to the Easy Plugins templates directory
	 *
	 * @var string
	 */
	define('TSP_EASY_PLUGINS_ASSETS_TEMPLATES_URL',	TSP_EASY_PLUGINS_ASSETS_URL . 'templates/');
	/**
	 * Full URL to the Easy Plugins css directory
	 *
	 * @var string
	 */
	define('TSP_EASY_PLUGINS_ASSETS_CSS_URL',		TSP_EASY_PLUGINS_ASSETS_URL . 'css/');
	/**
	 * Full URL to the Easy Plugins javascript directory
	 *
	 * @var string
	 */
	define('TSP_EASY_PLUGINS_ASSETS_JS_URL',		TSP_EASY_PLUGINS_ASSETS_URL . 'js/');
	/**
	 * Full URL to the Easy Plugins images directory
	 *
	 * @var string
	 */
	define('TSP_EASY_PLUGINS_ASSETS_IMAGES_URL',	TSP_EASY_PLUGINS_ASSETS_URL . 'images/');
/* @end */

require_once( TSP_EASY_PLUGINS_PATH . 'tsp-easy-plugins.config.php');
require_once( TSP_EASY_PLUGINS_CLASS_PATH  . 'class.easy-plugins.php');

// Store smarty cache and compiled directories
$upload_dir	= wp_upload_dir();
/**
 * Full absolute path to the Easy Plugins temp directory
 *
 * @var string
 */
define('TSP_EASY_PLUGINS_TMP_PATH',					$upload_dir['basedir'] . DS . 'tsp_plugins' . DS );

//--------------------------------------------------------
// initialize the plugin
//--------------------------------------------------------
global $easy_plugin_settings;

$easy_plugins 										= new TSP_Easy_Plugins( $easy_plugin_settings );

$easy_plugins->required_wordpress_version 			= "3.5.1";

$easy_plugins->run( __FILE__ );
?>