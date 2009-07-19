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
 
 /* ******************************************************************** 
 *  Function: plugins
 *  Parameters: None
 *  Purpose: Manages actions sent from the plugins template
 *  Notes: ---
 ********************************************************************** */ 
 
function plugins() {
	global $lang, $cage, $hotaru, $plugin;
	
	require_once('class.admin.php');
	$admin = New Admin();
	
	$action = $cage->get->testAlpha('action');
	$pfolder = $cage->get->testAlnumLines('plugin');
	
	switch ($action) {
		case "activate":
			$plugin->activate_deactivate_plugin($pfolder, 1);
			break;
		case "deactivate":
			$plugin->activate_deactivate_plugin($pfolder, 0);
			break;	
		case "install":
			$plugin->install_plugin($pfolder);
			break;
		case "uninstall":
			$plugin->uninstall_plugin($pfolder);
			break;	
		case "upgrade":
			$plugin->upgrade_plugin($pfolder);
			break;	
		default:
			// do nothing...
			return false;
			break;
	}
	return true;
}
?>
