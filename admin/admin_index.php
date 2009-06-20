<?php

/* ******************************************************************** 
 *  File: /admin/admin_index.php
 *  Purpose: Determines which page of Admin should be shown.
 *  Notes: ---
 ********************************************************************** */
 
// includes
require_once('../hotaru_header.php');
global $hotaru, $plugin;
 
$hotaru->set_is_page_all_false();

$page = $cage->get->getRaw('page');
switch ($page) {
	case "plugins":
		$hotaru->is_admin_plugins = true;
		break;
	case "plugin_settings":
		$hotaru->is_admin_plugin_settings = true;
		$plugin_folder = $cage->get->getRaw('plugin');
		$plugin->folder = $plugin_folder;
		$plugin->name = $plugin->plugin_name($plugin_folder);
		break;
	default:
		$hotaru->is_admin_home = true;
		break;
}

$hotaru->display_admin_template('index');	// gets the name of the current page, e.g. index.php and displays the equivalent file from the themes folder.
?>
