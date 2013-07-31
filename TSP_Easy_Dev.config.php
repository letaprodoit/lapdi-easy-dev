<?php									
/**
* Every plugin that uses Easy Dev must define the DS variable that sets the path deliminter
*
* @var string
*/
if (!defined('DS')) {
    if (strpos(php_uname('s') , 'Win') !== false) define('DS', '\\');
    else define('DS', '/');
}//endif

$easy_dev_settings = get_plugin_data( TSP_EASY_DEV_FILE, false, false );

$easy_dev_settings['name'] 				= TSP_EASY_DEV_NAME;
$easy_dev_settings['title'] 			= TSP_EASY_DEV_TITLE;
$easy_dev_settings['file']	 			= TSP_EASY_DEV_FILE;
$easy_dev_settings['base_name']	 		= TSP_EASY_DEV_BASE_NAME;
$easy_dev_settings['plugin_options']	= array(
	'category_fields'			=> array(),
	'post_fields'				=> array(),
	'widget_fields'				=> array(),
	'settings_fields'			=> array(),
	'shortcode_fields'			=> array(),
);

//--------------------------------------------------------
// Register classes
//--------------------------------------------------------
//set_include_path( TSP_EASY_DEV_CLASS_PATH );
//spl_autoload_extensions( '.class.php' ); 
//spl_autoload_register();
 
spl_autoload_register( 'register_classes' );

function register_classes( $class )
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
}//end register_classes
?>