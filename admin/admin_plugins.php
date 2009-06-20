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
if($widget_positions) {
	$plugin_management->update_plugin_statuses($widget_positions);	// Cycles through all plugins, enables or disables as necessary	
}


$plugin_to_uninstall = $cage->post->getRaw('plugin_folder');	// Use Raw because Alnum strips out underscores!
if($plugin_to_uninstall) { 
	$plugin_management->uninstall_plugin($plugin_to_uninstall);
	echo "<br /><b>Done!</b> <br /><br />This plugin has been deleted from the <i>plugins</i> and <i>pluginmeta</i> database tables (if it was there in the first place).<br /><br /><i>Note: </i>Any other database entries or tables created by the plugin have not been deleted.<br /><br />Please <a href='javascript:location.reload(true);' target='_self'>refresh this page</a> to update these lists.";
}


?>
