<?php
/*
Name: 			TSP Easy Dev
URI: 			http://www.thesoftwarepeople.com/software/plugins/wordpress/easy-dev-for-wordpress.html
Description: 	Easy Dev is a <strong>WordPress API library</strong> with advanced features. See <a target="_blank" href="http://lab.thesoftwarepeople.com/tracker/wiki/wordpress-ed:MainPage">API Docs</a> for information and instructions. <a target="_blank" href="https://twitter.com/#bringbackOOD">#bringbackOOD</a> 
Author: 		The Software People
Author URI: 	http://www.thesoftwarepeople.com/
Version: 		1.0.3
Copyright: 		Copyright © 2013 The Software People, LLC (www.thesoftwarepeople.com). All rights reserved
License: 		APACHE v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
*/

require_once(ABSPATH . 'wp-admin/includes/plugin.php' );

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
 * @ignore
 */
@define('TSP_EASY_DEV_CLASS_PATH',				TSP_EASY_DEV_PATH . 'classes/');
/**
 * @ignore
 */
@define('TSP_EASY_DEV_LIB_PATH',				TSP_EASY_DEV_CLASS_PATH . 'lib/');
/**
 * @ignore
 */
@define('TSP_EASY_DEV_INCLUDES_PATH',			TSP_EASY_DEV_CLASS_PATH . 'includes/');

/* @group Assets */
/**
 * Assets absolute path
 *
 * @ignore
 */
@define('TSP_EASY_DEV_ASSETS_PATH',				TSP_EASY_DEV_PATH . 'assets/');

// Absolute directory paths
	/**
	 * Full absolute path to the Easy Dev templates directory
	 *
	 * @var string
	 */
	@define('TSP_EASY_DEV_ASSETS_TEMPLATES_PATH',TSP_EASY_DEV_ASSETS_PATH . 'templates/');
	/**
	 * Full absolute path to the Easy Dev css directory
	 *
	 * @var string
	 */
	@define('TSP_EASY_DEV_ASSETS_CSS_PATH',		TSP_EASY_DEV_ASSETS_PATH . 'css/');
	/**
	 * Full absolute path to the Easy Dev javascript directory
	 *
	 * @var string
	 */
	@define('TSP_EASY_DEV_ASSETS_JS_PATH',		TSP_EASY_DEV_ASSETS_PATH . 'js/');
	/**
	 * Full absolute path to the Easy Dev images directory
	 *
	 * @var string
	 */
	@define('TSP_EASY_DEV_ASSETS_IMAGES_PATH',	TSP_EASY_DEV_ASSETS_PATH . 'images/');

/**
 * Assets URL
 *
 * @ignore
 */
@define('TSP_EASY_DEV_ASSETS_URL',				TSP_EASY_DEV_URL . 'assets/');

	/**
	 * Full URL to the Easy Dev templates directory
	 *
	 * @var string
	 */
	@define('TSP_EASY_DEV_ASSETS_TEMPLATES_URL',TSP_EASY_DEV_ASSETS_URL . 'templates/');
	/**
	 * Full URL to the Easy Dev css directory
	 *
	 * @var string
	 */
	@define('TSP_EASY_DEV_ASSETS_CSS_URL',		TSP_EASY_DEV_ASSETS_URL . 'css/');
	/**
	 * Full URL to the Easy Dev javascript directory
	 *
	 * @var string
	 */
	@define('TSP_EASY_DEV_ASSETS_JS_URL',		TSP_EASY_DEV_ASSETS_URL . 'js/');
	/**
	 * Full URL to the Easy Dev images directory
	 *
	 * @var string
	 */
	@define('TSP_EASY_DEV_ASSETS_IMAGES_URL',	TSP_EASY_DEV_ASSETS_URL . 'images/');
/* @end */

// Store smarty cache and compiled directories
$upload_dir	= wp_upload_dir();
/**
 * Full absolute path to the Easy Dev temp directory
 *
 * @var string
 */
@define('TSP_EASY_DEV_TMP_PATH',				$upload_dir['basedir'] . '/tsp_plugins/' );

//--------------------------------------------------------
// Register classes
//--------------------------------------------------------
if ( !function_exists( 'fn_easy_dev_register_classes' ) )
{
	/**
	 * Hook implementation for spl_autoload_register
	 *
	 * @ignore
	 *
	 * @since 1.0
	 *
	 * @param string $class Required - the class name to include the class file for
	 *
	 * @return none
	 */
	function fn_easy_dev_register_classes( $class )
	{
	    if (file_exists( TSP_EASY_DEV_CLASS_PATH . $class . '.class.php' ))
	    {
	    	include_once TSP_EASY_DEV_CLASS_PATH . $class . '.class.php';
	    }//end if
	    
	    if ( $class == 'Smarty' )
	    {
		    if (file_exists( TSP_EASY_DEV_LIB_PATH . $class . DS . $class. '.class.php' ))
		    {
		        include_once TSP_EASY_DEV_LIB_PATH . $class . DS . $class. '.class.php';
		    }//end if
	    }//end if
	}//end fn_easy_dev_register_classes

	spl_autoload_register( 'fn_easy_dev_register_classes' );
}//end if 
?>