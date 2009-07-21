<?php

/* ********** PLUGIN *********************************************************************************
 * name: Users
 * description: Manages users within Hotaru.
 * version: 0.1
 * folder: users
 * prefix: usr
 * hooks: users, hotaru_header, install_plugin, navigation_users, theme_index_replace, theme_index_main
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
	global $lang, $cage, $plugin;

	if(!defined('table_usermeta')) { define("table_usermeta", db_prefix . 'usermeta'); }
	
	// include language file
	$plugin->include_language_file('users');
}


/* ******************************************************************** 
 *  Function: usr_install_plugin
 *  Parameters: None
 *  Purpose: If it doesn't already exist, a "usermeta" table is created in the database
 *  Notes: Happens when theplugin is installed. The table is never deleted.
 ********************************************************************** */
 
function usr_install_plugin() {
	global $db, $plugin;
	
	// Create a new empty table called "usermeta"
	$exists = $db->table_exists('usermeta');
	if(!$exists) {
		//echo "table doesn't exist. Stopping before creation."; exit;
		$sql = "CREATE TABLE `" . db_prefix . "usermeta` (
		  `usermeta_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  `usermeta_userid` int(20) NOT NULL DEFAULT 0,
		  `usermeta_key` varchar(255) NULL,
		  `usermeta_value` text NULL,
		  `usermeta_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
 		  `usermeta_updateby` int(20) NOT NULL DEFAULT 0, 
		  INDEX  (`usermeta_userid`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='User Meta';";
		$db->query($sql); 
	}
}


/* ******************************************************************** 
 *  Function: usr_navigation_users
 *  Parameters: None
 *  Purpose: Adds links to the end of the navigation bar
 *  Notes: 
 ********************************************************************** */

function usr_navigation_users() {	
	global $current_user;
	
	if($current_user->logged_in) {
		echo "<li><a href='" . url(array('page'=>'profile')) . "'>Profile</a></li>\n";
		echo "<li><a href='" . url(array('page'=>'logout')) . "'>Logout</a></li>\n";
		if($current_user->role == 'administrator') {
			echo "<li><a href='" . url(array(), 'admin') . "'>Admin</a></li>\n";
		}
	} else {	
		echo "<li><a href='" . url(array('page'=>'login')) . "'>Login</a></li>\n";
		echo "<li><a href='" . url(array('page'=>'register')) . "'>Register</a></li>\n";
	}
}


/* ******************************************************************** 
 *  Function: usr_theme_index_replace
 *  Parameters: None
 *  Purpose: Echos the login form to index.php 
 *  Notes: Work to do *before* we send output to the page.
 ********************************************************************** */
 
function usr_theme_index_replace() {
	global $hotaru, $cage, $current_user;
	
	// Pages you have to be logged in for...
	if($current_user->logged_in) {
		 if($hotaru->is_page('logout')) {
			$current_user->destroy_cookie_and_session();
			header("Location: " . baseurl);
		} elseif($hotaru->is_page('profile')) {
			require_once(plugins . 'users/update.php');
			usr_update();
		} 
				
	// Pages you have to be logged out for...
	} else {
		if($hotaru->is_page('register')) {
			require_once(plugins . 'users/register.php');
			if(usr_register()) { 
				// success, return to front page, logged OUT.
				header("Location: " . baseurl . "index.php?page=login");
			}
		} elseif($hotaru->is_page('login')) {
			require_once(plugins . 'users/login.php');
			if(usr_login()) { 
				// success, return to front page, logged IN.
				header("Location: " . baseurl);
			}
		} 	
	}
	return false;
}


/* ******************************************************************** 
 *  Function: usr_theme_index_main
 *  Parameters: None
 *  Purpose: Displays various forms within the body of the page.
 *  Notes: 
 ********************************************************************** */
 
function usr_theme_index_main() {
	global $hotaru, $cage, $current_user;
	
	// Pages you have to be logged in for...
	if($current_user->logged_in) {
		if($hotaru->is_page('profile')) {
			require_once(plugins . 'users/update.php');
			usr_update_form();
			return true;
		} else {
			return false;
		}
		
	// Pages you have to be logged out for...
	} else {
		if($hotaru->is_page('register')) {
			require_once(plugins . 'users/register.php');
			usr_register_form();
			return true;	
		} elseif($hotaru->is_page('login')) {
			require_once(plugins . 'users/login.php');
			usr_login_form();
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