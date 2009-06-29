<?php

/* **************************************************************************************************** 
 *  File: /admin/admin_plugins.php
 *  Purpose: EasyWidgets and Ajax send this file a string of plugin positions from Plugin Management, 
 *           showing whether they are active or inactive. This script passes that info to the Plugins 
 *           class which updates the "enabled" field in the database plugins table, or adds the plugin.
 *  Notes: ---
 *  License:
 *
 *   This file is part of Hotaru CMS (http://www.hotarucms.org/).
 *
 *   Hotaru CMS is free software: you can redistribute it and/or modify it under the terms of the 
 *   GNU General Public License as published by the Free Software Foundation, either version 3 of 
 *   the License, or (at your option) any later version.
 *
 *   Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 *   even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License along with Hotaru CMS. If not, 
 *   see http://www.gnu.org/licenses/.
 *   
 *   Copyright (C) 2009 Hotaru CMS - http://www.hotarucms.org/
 *
 **************************************************************************************************** */
 
// includes
require_once('../hotaru_header.php');
global $lang;

//$plugin_management = new Plugin();

$widget_positions = $cage->post->noTags('position');
//echo "Positions returned from EasyWidgets: " . $widget_position . "<br />";
if($widget_positions) {
	$plugin->update_plugin_statuses($widget_positions);	// Cycles through all plugins, enables or disables as necessary	
}


$plugin_to_uninstall = $cage->post->testRegex('page', '/^([a-z0-9_-])+$/i');
if($plugin_to_uninstall) { 
	$plugin->uninstall_plugin($plugin_to_uninstall);
	echo "<br /><b>" . $lang['admin_plugins_uninstall_done'] . "</b> <br /><br />";
	echo $lang['admin_plugins_uninstall_deleted'] . "<br /><br />";
	echo $lang['admin_plugins_uninstall_note'] . "<br /><br />";
	echo $lang['admin_plugins_uninstall_refresh'];
}


?>
