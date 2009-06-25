<?php

/* ********** PLUGIN *********************************************************************************
 * name: Users
 * description: Manages users within Hotaru.
 * version: 0.1
 * folder: users
 * prefix: usr
 * hooks: users, hotaru_header, install_plugin_starter_settings, navigation, theme_index_main
 *
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
 
return false; die(); // die on direct access.


/* ******************************************************************** 
 *  Function: usr_users
 *  Parameters: None
 *  Purpose: 
 *  Notes: 
 ********************************************************************** */

function usr_users(&$parameters) {

}


/* ******************************************************************** 
 *  Function: usr_hotaru_header
 *  Parameters: None
 *  Purpose: Defines a global "table_usermeta" constant for referring to the db table
 *  Notes: ---
 ********************************************************************** */
 
function usr_hotaru_header() {
	global $lang, $cage;
	define("table_usermeta", db_prefix . 'usermeta');
	require_once(plugins . 'users/libraries/class.userbase.php');
	require_once(plugins . 'users/libraries/class.users.php');
	
	// include users language file
	if(file_exists(plugins . 'users/languages/users_' . strtolower(sitelanguage) . '.php')) {
		require_once(plugins . 'users/languages/users_' . strtolower(sitelanguage) . '.php');	// language file for admin
	} else {
		require_once(plugins . 'users/languages/users_english.php');	// English file if specified language doesn't exist
	}
	
	$current_user = new User();
	// Check for a cookie. If present then the user is logged in.
	$hotaru_user = $cage->cookie->testRegex('hotaru_user', '/^([a-z0-9_-]{4,32})+$/i');
	if(($hotaru_user) && ($cage->cookie->keyExists('hotaru_key'))) {
		$user_info=explode(":", base64_decode($cage->cookie->getRaw('hotaru_key')));
		if(($hotaru_user == $user_info[0]) && (crypt($user_info[0], 22) == $user_info[1])) {
			$current_user->username = $hotaru_user;
			$current_user->get_user_basic(0, $current_user->username);
			$current_user->logged_in = true;
		}
	}
		
	/* IMPORTANT NOTE: declaring $current_user above doesn't make it available to other functions, even with "global".
	 * So, we need to return it back to hotaru_header.php and declare it there. That's done below by passing it back through 
	 * check_actions() as an array. Look at hotaru_header.php to see how we extract the $current_user object for global use. */
	 
	$vars['current_user'] = $current_user;
	return $vars;
}


/* ******************************************************************** 
 *  Function: usr_install_plugin_starter_settings
 *  Parameters: None
 *  Purpose: If it doesn't already exist, a "usermeta" table is created in the database
 *  Notes: Happens when theplugin is installed. The table is never deleted.
 ********************************************************************** */
 
function usr_install_plugin_starter_settings() {
	global $db, $plugin;
	
	// Create a new empty table called "usermeta"
	$exists = $db->table_exists('usermeta');
	if(!$exists) {
		//echo "table doesn't exist. Stopping before creation."; exit;
		$sql = "CREATE TABLE `" . db_prefix . "usermeta` (
		  `usermeta_id` int(20) NOT NULL auto_increment,
		  `user_id` int(20) NOT NULL default 0,
		  `user_key` varchar(255) NULL,
		  `user_value` text NULL,
		  PRIMARY KEY  (`usermeta_id`),
		  INDEX  (`user_id`)
		) TYPE = MyISAM;";
		$db->query($sql); 
	}
}


/* ******************************************************************** 
 *  Function: usr_navigation
 *  Parameters: None
 *  Purpose: Adds links to the navigation bar
 *  Notes: 
 ********************************************************************** */

function usr_navigation() {	
	global $current_user;
	
	if($current_user->logged_in) {
		echo "<li><a href='" . baseurl . "index.php?page=user_settings&user='>Settings</a></li>\n";
		echo "<li><a href='" . baseurl . "index.php?page=logout'>Logout</a></li>\n";
		if($current_user->role == 'administrator') {
			echo "<li><a href='" . baseurl . "admin/admin_index.php'>Admin</a></li>\n";	
		}
	} else {	
		echo "<li><a href='" . baseurl . "index.php?page=login'>Login</a></li>\n";
		echo "<li><a href='" . baseurl . "index.php?page=register'>Register</a></li>\n";
	}
}


/* ******************************************************************** 
 *  Function: usr_theme_index_display
 *  Parameters: None
 *  Purpose: Echos the login form to index.php 
 *  Notes: Previously directed to a login.php template file included in this plugin, but decided a function was better. (Nick)
 ********************************************************************** */
 
function usr_theme_index_main() {
	global $hotaru, $cage, $current_user;
	
	// Pages you have to be logged in for...
	if($current_user->logged_in) {
		 if($hotaru->is_page('logout')) {
			$current_user->destroy_cookie_and_session();
			header("Location: " . baseurl);
			return true;
		} elseif($hotaru->is_page('user_settings')) {
			require_once(plugins . 'users/update.php');
			usr_update();
			return true;
		} else {
			return false;
		}
		
	// Pages you have to be logged out for...
	} else {
		if($hotaru->is_page('register')) {
			require_once(plugins . 'users/register.php');
			usr_register();
			return true;	
		} elseif($hotaru->is_page('login')) {
			require_once(plugins . 'users/login.php');
			//$hotaru->display_template('/pages/login', 'users');  
			usr_login();
			return true;
		} else {
			return false;
		}	
	}
	return false;
}


/* ******************************************************************** 
 *  Function: usr_get_params
 *  Parameters: None
 *  Purpose: Gets parameters sent from a form of directly via http
 *  Notes: ---
 ********************************************************************** */
 
function usr_get_params() {
	global $cage;

}

?>