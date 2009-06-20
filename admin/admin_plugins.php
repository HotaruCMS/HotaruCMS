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
global $db, $lang;

$plugin_management = new Plugin();

$widget_positions = $cage->post->getRaw('position');	// Use Raw because Alnum strips out underscores!
//echo "Positions returned from EasyWidgets: " . $widget_position . "<br />";
if($widget_positions) {
	$plugin_management->update_plugin_statuses($widget_positions);	// Cycles through all plugins, enables or disables as necessary	
}


$plugin_to_uninstall = $cage->post->getRaw('plugin_folder');	// Use Raw because Alnum strips out underscores!
if($plugin_to_uninstall) { 
	$plugin_management->uninstall_plugin($plugin_to_uninstall);
	echo "<br /><b>" . $lang['admin_plugins_uninstall_done'] . "</b> <br /><br />";
	echo $lang['admin_plugins_uninstall_deleted'] . "<br /><br />";
	echo $lang['admin_plugins_uninstall_note'] . "<br /><br />";
	echo $lang['admin_plugins_uninstall_refresh'];
}


?>
