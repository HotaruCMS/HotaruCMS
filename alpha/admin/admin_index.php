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
require_once('admin_news.php');
require_once('admin_plugins.php');

$admin = New Admin();

if(file_exists(admin . 'languages/admin_' . strtolower(sitelanguage) . '.php')) {
	require_once(admin . 'languages/admin_' . strtolower(sitelanguage) . '.php');	// language file for admin
} else {
	require_once(admin . 'languages/admin_english.php');	// English file if specified language doesn't exist
}

$page = $cage->get->testPage('page');	// check with "get";
if(!$page) { $page = $cage->post->testPage('page'); }  // check with "post" - used in admin_login_form().

// Authenticate the admin if the Users plugin is INACTIVE:
if(!$plugin->plugin_active('users')) {
	if(($page != 'admin_login') && !$result = is_admin_cookie()) {
		header('Location: ' . baseurl . 'admin/admin_index.php?page=admin_login');
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

// If we get this far, we know that the user is an administrator.

$plugin->check_actions('admin_index');

switch ($page) {
	case "admin_login":
		admin_login();
		break;
	case "admin_logout":
		admin_logout();
		break;
	case "settings":
		// Nothing special to do...
		break;
	case "maintenance":
		// Nothing special to do...
		break;
	case "plugins":
		plugins();
		break;
	case "plugin_settings":
		$plugin->folder = $cage->get->testAlnumLines('plugin');
		$plugin->name = $plugin->plugin_name($plugin->folder);
		break;
	default:
		break;
}

$admin->display_admin_template('index');	// gets the name of the current page, e.g. index.php and displays the equivalent file from the themes folder.
?>
