<?php									
// Get the plugin path
if (!defined('WP_CONTENT_DIR')) define('WP_CONTENT_DIR', ABSPATH . 'wp-content');

if (!defined('DS')) {
    if (strpos(php_uname('s') , 'Win') !== false) define('DS', '\\');
    else define('DS', '/');
}//endif

$plugin_globals = get_plugin_data( TSP_EASY_PLUGINS_FILE, false, false );

$plugin_globals['name'] 			= TSP_EASY_PLUGINS_NAME;
$plugin_globals['widget_fields']	= array();
?>