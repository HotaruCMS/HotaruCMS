<?php

/* ******************************************************************** 
 *  File: /admin/admin_plugins.php
 *  Purpose: EasyWidgets and Ajax send this file a string of plugin positions from Plugin Management, 
 *           showing whether they are active or inactive. This script passes that info to the Plugins 
 *           class which updates the "enabled" field in the database plugins table, or adds the plugin.
 *  Notes: ---
 ********************************************************************** */
 
// includes
require_once('admin_header.php');
require_once(libraries . 'class.plugins.php');
global $db;

$plugin_management = new Plugin();

$widget_positions = $cage->post->getRaw('position');	// Use Raw because Alnum strips out underscores!
//echo "Positions returned from EasyWidgets: " . $widget_position . "<br />";

$plugin_management->update_plugin_statuses($widget_positions);	// CYcles through all plugins, enables or disables as necessary

?>
