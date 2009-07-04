<?php

/* **************************************************************************************************** 
 *  File: /admin/admin_index.php
 *  Purpose: Determines which page of Admin should be shown.
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
require_once('admin_login.php');
require_once('class.admin.php');

$admin = New Admin();

if(file_exists(admin . 'languages/admin_' . strtolower(sitelanguage) . '.php')) {
	require_once(admin . 'languages/admin_' . strtolower(sitelanguage) . '.php');	// language file for admin
} else {
	require_once(admin . 'languages/admin_english.php');	// English file if specified language doesn't exist
}

$page = $cage->get->testRegex('page', '/^([a-z0-9_-])+$/i');

// Authenticate the admin if the Users plugin is INACTIVE:
if(!$plugin->plugin_active('users')) {
	if(($page != 'admin_login') && !$result = is_admin_cookie()) {
		echo "You do not have permission to access this page.<br />";
		die(); exit;
	}
}

// Authenticate the admin if the Users plugin is ACTIVE:
if(isset($current_user) && $plugin->plugin_active('users')) {

	// This first condition happens when the Users plugin is activated and there's no cookie for the Admin yet.
	if(($current_user->username == "") && $plugin->plugin_active('users')) {
		header('Location: ' . baseurl . 'index.php?page=login');
		die; exit;
	} elseif (!is_admin($current_user->username)) {
		echo "You do not have permission to access this page.<br />";
		die(); exit;
	}
}

// If we get this far, we know that the Users plugin is active and the user is an administrator.

switch ($page) {
	case "admin_logout":
		admin_logout();
	case "settings":
		// Nothing special to do...
		break;
	case "maintenance":
		// Nothing special to do...
		break;
	case "plugins":
		// Nothing special to do...
		break;
	case "plugin_settings":
		$plugin->folder = $cage->get->testRegex('plugin', '/^([a-z0-9_-])+$/i');
		$plugin->name = $plugin->plugin_name($plugin->folder);
		break;
	case "":
		include('admin_news.php');	// for Admin home RSS feed
		break;
	default:
		if(!$hotaru->is_page($page)) {
			include('admin_news.php');	// for Admin home RSS feed
		}
		break;
}

$admin->display_admin_template('index');	// gets the name of the current page, e.g. index.php and displays the equivalent file from the themes folder.
?>
