<?php

/* ******************************************************************** 
 *  File: /admin/admin_index.php
 *  Purpose: Determines which page of Admin should be shown.
 *  Notes: ---
 ********************************************************************** */
 
// includes
require_once('../hotaru_header.php');
require_once('admin_functions/admin_login.php');

$hotaru->set_is_page_all_false();
$page = $cage->get->testRegex('page', '/^([a-z0-9_-])+$/i');

// Authenticate the admin if the Users plugin is INACTIVE:
if(!$plugin->plugin_active('users')) {
	if(($page != 'admin_login') && !$result = is_admin_session()) {
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
	case "plugins":
		$hotaru->is_admin_plugins = true;
		break;
	case "plugin_settings":
		$hotaru->is_admin_plugin_settings = true;
		$plugin->folder = $cage->get->testRegex('plugin', '/^([a-z0-9_-])+$/i');
		$plugin->message = $cage->get->noTags('message');
		$plugin->message_type = $cage->get->getAlpha('message_type');
		$plugin->name = $plugin->plugin_name($plugin->folder);
		break;
	case "":
		include('admin_functions/admin_news.php');	// for Admin home RSS feed
		$hotaru->is_admin_home = true;
		break;
	default:
		if(!$hotaru->is_page($page)) {
			include('admin_functions/admin_news.php');	// for Admin home RSS feed
			$hotaru->is_admin_home = true;	
		}
		break;
}

$hotaru->display_admin_template('index');	// gets the name of the current page, e.g. index.php and displays the equivalent file from the themes folder.
?>
