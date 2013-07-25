<?php									
/**
* Every plugin that uses Easy Plugins must define the DS variable that sets the path deliminter
*
* @var string
*/
if (!defined('DS')) {
    if (strpos(php_uname('s') , 'Win') !== false) define('DS', '\\');
    else define('DS', '/');
}//endif

$easy_plugin_settings = get_plugin_data( TSP_EASY_PLUGINS_FILE, false, false );

$easy_plugin_settings['name'] 			= TSP_EASY_PLUGINS_NAME;
$easy_plugin_settings['plugin_data']	= array(
	'category_fields'			=> array(),
	'post_fields'				=> array(),
	'widget_fields'				=> array(),
	'settings_fields'			=> array(),
);
?>