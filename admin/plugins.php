<?php

// includes
require_once('../hotaru_settings.php');
require_once(libraries . 'class.plugins.php');
global $db;

$plugin_management = new Plugin();

$widget_positions = $cage->post->getRaw('position');	// Use Raw because Alnum strips out underscores!
//echo "Positions returned from EasyWidgets: " . $widget_position . "<br />";

$plugin_management->update_plugin_statuses($widget_positions);	// CYcles through all plugins, enables or disables as necessary

?>
