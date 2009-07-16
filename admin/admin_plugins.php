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
if(file_exists(admin . 'languages/admin_' . strtolower(sitelanguage) . '.php')) {
	require_once(admin . 'languages/admin_' . strtolower(sitelanguage) . '.php');	// language file for admin
} else {
	require_once(admin . 'languages/admin_english.php');	// English file if specified language doesn't exist
}
global $lang;

require_once('class.admin.php');
$admin = New Admin();

$widget_positions = $cage->post->noTags('position');
//echo "Positions returned from EasyWidgets: " . $widget_positions . "<br />";
if($widget_positions) {
	$plugin->update_plugin_statuses($widget_positions);	// Cycles through all plugins, enables or disables as necessary	
}

$the_plugin = $cage->post->testAlnumLines('plugin_folder');
$action = $cage->post->testAlpha('action');

if($the_plugin && ($action == 'upgrade')) {  
	$plugin->upgrade_plugin($the_plugin);
	//echo "<br /><b>" . $lang['admin_plugins_upgrade_done'] . "</b> <br /><br />";
	//echo $lang['admin_plugins_upgrade_refresh'];
}

if($the_plugin && ($action == 'uninstall')) { 
	$plugin->uninstall_plugin($the_plugin);
	//echo "<br /><b>" . $lang['admin_plugins_uninstall_done'] . "</b> <br /><br />";
	//echo $lang['admin_plugins_uninstall_refresh'];
}

?>
