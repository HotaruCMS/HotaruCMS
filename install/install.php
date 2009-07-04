<?php

/* **************************************************************************************************** 
 *  File: install/install.php
 *  Purpose: Steps through the set-up process, creating database tables and registering the Admin user.
 *  Notes: You must delete this file after installation as it poses a serious security risk if left.
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

require_once('../hotaru_settings.php');
require_once('../class.plugins.php');	// Needed for error and success messages
// Global Inspekt SuperCage
require_once(includes . 'Inspekt/Inspekt.php');	
if(!isset($cage)) { $cage = Inspekt::makeSuperCage(); }

if(file_exists(install . 'languages/install_' . strtolower(sitelanguage) . '.php')) {
	require_once(install . 'languages/install_' . strtolower(sitelanguage) . '.php');	// language file for admin
} else {
	require_once(install . 'languages/install_english.php');	// English file if specified language doesn't exist
}

$step = $cage->get->getInt('step');		// Installation steps.

if($step > 2) { 
	require_once(includes . 'ezSQL/ez_sql_core.php');
	require_once(includes . 'ezSQL/mysql/ez_sql_mysql.php');
	if(!isset($db)) { $db = new ezSQL_mysql(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST); }; 
}

switch ($step) {
	case 1:
		installation_welcome();		// "Welcome to Hotaru CMS. Click "Next" to begin installation."
		break;
	case 2:
		database_setup();		// DB name, user, password, prefix...
		break;
	case 3:
		database_creation();		// Creates the database tables
		break;
	case 4:
		register_admin();		// Username and password for Admin user...
		break;
	case 5:
		installation_complete();	// "Installation complete. Delete the "install" folder. Visit your site"
		break;
	default:
		installation_welcome();		// Anything other than step=2, 3 or 4 will return user to the welcome page.
		break;		
}

exit;


/* ******************************************************************** 
 *  Function: getFilenames
 *  Parameters: None
 *  Purpose: Returns all the filenames/paths in a specified folder.
 *  Notes: ---
 ********************************************************************** */

function html_header() {
	global $lang;
	$header = "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 3.2//EN'>\n";
	$header .= "<HTML><HEAD>\n";
	$header .= "<TITLE>" . $lang['install_title'] . "</TITLE>\n";
	$header .= "<META HTTP-EQUIV='Content-Type' CONTENT='text'>\n";
	$header .= "<link rel='stylesheet' href='" . baseurl . "3rd_party/YUI-CSS/reset-fonts-grids.css' type='text/css'>\n";
	$header .= "<link rel='stylesheet' type='text/css' href='" . baseurl . "install/install_style.css'>\n";
	$header .= "</HEAD>\n";
	$header .= "<BODY>\n";
	$header .= "<div id='doc' class='yui-t7 install'>\n";
	$header .= "<div id='hd' role='banner'><img align='left' src='" . baseurl . "admin/themes/admin_default/images/hotaru.png' style='height:60px; width:69px;'><h1>" . $lang['install_title'] . "</h1></div>\n"; 
	$header .= "<div id='bd' role='main'>\n";
	$header .= "<div class='yui-g'>\n";
	return $header;
}


/* ******************************************************************** 
 *  Function: getFilenames
 *  Parameters: None
 *  Purpose: Returns all the filenames/paths in a specified folder.
 *  Notes: ---
 ********************************************************************** */
 
function html_footer() {
	global $lang;
	$footer = "<div class='clear'></div>\n"; // clear floats
	$footer .= "<div id='ft' role='contentinfo'><p>" . $lang['install_trouble'] . "</p></div>\n"; 
	$footer .= "</div>\n"; // close "yui-g" div
	$footer .= "</div>\n"; // close "main" div
	$footer .= "</div>\n"; // close "yui-t7 install" div
	$footer .= "</BODY>\n";
	$footer .= "</HTML>\n";
	return $footer;
}


/* ******************************************************************** 
 *  Function: installation_welcome
 *  Parameters: None
 *  Purpose: Step 1 of installation - Welcome message
 *  Notes: ---
 ********************************************************************** */
 
function installation_welcome() {
	global $lang;
	echo html_header();
	echo "<h2>" . $lang['install_step1'] . "</h2>\n";
	echo "<div class='install_content'>" . $lang['install_step1_welcome'] . "</div>\n";
	echo "<div class='next'><a href='install.php?step=2'>" . $lang['install_next'] . "</a></div>\n";
	echo html_footer();
}


/* ******************************************************************** 
 *  Function: database_setup
 *  Parameters: None
 *  Purpose: Step 2 of installation - explains how to put your database info in hotaru_settings.php 
 *  Notes: ---
 ********************************************************************** */
 
function database_setup() {
	global $lang;
	echo html_header();
	echo "<h2>" . $lang['install_step2'] . "</h2>\n";
	echo "<div class='install_content'>" . $lang['install_step2_instructions'] . ":</div>\n";
	echo "<ol class='install_content'>\n";
	echo "<li>" . $lang['install_step2_instructions1'] . "</li>\n";
	echo "<li>" . $lang['install_step2_instructions2'] . "</li>\n";
	echo "<li>" . $lang['install_step2_instructions3'] . "</li>\n";
	echo "<li>" . $lang['install_step2_instructions4'] . "</li>\n";
	echo "</ol>\n";
	echo "<div class='install_content'><span style='color: red;'>" . $lang['install_step2_warning'] . "</span>: " . $lang['install_step2_warning_note'] . "</div>\n";
	echo "<div class='back'><a href='install.php?step=1'>" . $lang['install_back'] . "</a></div>\n";
	echo "<div class='next'><a href='install.php?step=3'>" . $lang['install_next'] . "</a></div>\n";
	echo html_footer();
}


/* ******************************************************************** 
 *  Function: database_creation
 *  Parameters: None
 *  Purpose: Step 3 of installation - Creates database tables
 *  Notes: ---
 ********************************************************************** */
 
function database_creation() {
	global $db, $lang;
	echo html_header();
	echo "<h2>" . $lang['install_step3'] . "</h2>\n";
	
	$skip = 0;
	$tables = array('settings', 'users', 'plugins', 'pluginhooks', 'pluginsettings');

	foreach($tables as $table_name) {
		create_table($table_name);
	} 
	
	echo "<div class='install_content'>" . $lang['install_step3_success'] . "</div>\n";
	echo "<div class='back'><a href='install.php?step=2'>" . $lang['install_back'] . "</a></div>\n";
	echo "<div class='next'><a href='install.php?step=4'>" . $lang['install_next'] . "</a></div>\n";
	echo html_footer();	
}


/* ******************************************************************** 
 *  Function: register_admin
 *  Parameters: None
 *  Purpose: Step 4 of installation - registers the site Admin.
 *  Notes: ---
 ********************************************************************** */
 
function register_admin() {
	global $lang, $cage, $db;
	
	// Remove any cookies set in a previous installation:
	setcookie("hotaru_user", "", time()-3600, "/");
	setcookie("hotaru_key", "", time()-3600, "/");
	// --------------------------------------------------
	
	$plugin = new Plugin();

	echo html_header();
	echo "<h2>" . $lang['install_step4'] . "</h2>\n";
	echo "<div class='install_content'>" . $lang['install_step4_instructions'] . ":<br />\n";
	
	$error = 0;
	if($cage->post->getInt('step') == 4) {
		$name_check = $cage->post->testRegex('username', '/^([a-z0-9_-]{4,32})+$/i');	// alphanumeric, dashes and underscores okay, case insensitive
		if($name_check) {
			$user_name = $name_check;
		} else {
			$plugin->message = $lang['install_step4_username_error'];
			$plugin->message_type = 'red';
			$plugin->show_message();
			$error = 1;
		}
	
		$password_check = $cage->post->testRegex('password', '/^([a-z0-9@*#_-]{8,60})+$/i');	
		if($password_check) {
			$user_password = crypt(md5($password_check),md5($user_name));
		} else {
			$plugin->message = $lang['install_step4_password_error'];
			$plugin->message_type = 'red';
			$plugin->show_message();
			$error = 1;
		}
		
		$email_check = $cage->post->testEmail('email');	
		if($email_check) {
			$user_email = $email_check;
		} else {
			$plugin->message = $lang['install_step4_email_error'];
			$plugin->message_type = 'red';
			$plugin->show_message();
			$error = 1;
		}
	}
	
	if(($cage->post->getInt('step') == 4) && $error == 0) {
		$plugin->message = $lang['install_step4_update_success'];
		$plugin->message_type = 'green';
		$plugin->show_message();
	}
	
	if($error == 0) {
		if(!$admin_name = admin_exists()) {
			// Insert default settings
			$sql = "INSERT INTO " . table_users . " (user_username, user_role, user_password, user_email) VALUES (%s, %s, %s, %s)";
			$db->query($db->prepare($sql, 'admin', 'administrator', 'password', 'admin@mysite.com'));
			$user_name = 'admin';
			$user_email = 'admin@mysite.com';
			$user_password = 'password';		
		} else {
			$user_info = get_admin_details(0, $admin_name);
			// On returning to this page via back or next, the fields are empty at this point, so...
			if(!isset($user_name)) { $user_name = ""; }
			if(!isset($user_email)){ $user_email = ""; } 
			if(!isset($user_password)) { $user_password = ""; }
			if(($user_name != "") && ($user_email != "") && ($user_password != "")) {
				// There's been a change so update...
				$sql = "UPDATE " . table_users . " SET user_username = %s, user_role = %s, user_password = %s, user_email = %s WHERE user_role = %s";
				$db->query($db->prepare($sql, $user_name, 'administrator', $user_password, $user_email, 'administrator'));
			} else {
				$user_id = $user_info->user_id;
				$user_name = $user_info->user_username;
				$user_email = $user_info->user_email;
				$user_password = $user_info->user_password;
			}
		}
	}

	echo "<form name='install_admin_reg_form' action='" . baseurl . "install/install.php?step=4' method='post'>\n";
	echo "<table>";
	echo "<tr><td>Username:&nbsp; </td><td><input type='text' size=30 name='username' value='" . $user_name . "' /></td></tr>\n";
	echo "<tr><td>Email:&nbsp; </td><td><input type='text' size=30 name='email' value='" . $user_email . "' /></td></tr>\n";
	if(!$cage->post->getInt('step') == 4) { $password_check = ""; } // if loaded from database show blank, otherwise shows the password just submitted.
	echo "<tr><td>Password:&nbsp; </td><td><input type='password' size=30 name='password' value='" . $password_check . "' /></td></tr>\n";
	echo "<input type='hidden' name='step' value='4' />\n";
	echo "<tr><td>&nbsp;</td><td style='text-align:right;'><input type='submit' value='" . $lang['install_step4_form_update'] . "' /></td></tr>\n";
	echo "</table>";
	echo "</form>\n";
	echo $lang["install_step4_make_note"] . "</div>\n";
	echo "<div class='back'><a href='install.php?step=3'>" . $lang['install_back'] . "</a></div>\n";
	echo "<div class='next'><a href='install.php?step=5'>" . $lang['install_next'] . "</a></div>\n";
	echo html_footer();
}
	
	
/* ******************************************************************** 
 *  Function: installation_complete 
 *  Parameters: None
 *  Purpose: Step 5 of installation - shows completion.
 *  Notes: ---
 ********************************************************************** */
 
function installation_complete() {
	global $lang;
	echo html_header();	
	echo "<h2>" . $lang['install_step5'] . "</h2>\n";
	echo "<div class='install_content'>" . $lang['install_step5_installation_complete'] . "</div>\n";
	echo "<div class='install_content'>" . $lang['install_step5_installation_delete'] . "</div>\n";
	echo "<div class='install_content'>" . $lang['install_step5_installation_go_play'] . "</div>\n";
	echo "<div class='back'><a href='install.php?step=4'>" . $lang['install_back'] . "</a></div>\n";
	echo "<div class='next'><a href='" . baseurl . "'>" . $lang['install_home'] . " " . sitename . "</a></div>\n";
	echo html_footer();	
}


/* ******************************************************************** 
 *  Function: admin_exists
 *  Parameters: None
 *  Purpose: Returns true if a user with administrator role is in the database 
 *  Notes: Used during Hotaru installation, but otherwise pretty pointless
 ********************************************************************** */
 		
function admin_exists() {
	global $db;
	$sql = "SELECT user_username FROM " . table_users . " WHERE user_role = %s";
	if($admin_name = $db->get_var($db->prepare($sql, 'administrator'))) {
		return $admin_name; // admin exists
	} else {
		return false;
	}
}


/* ******************************************************************** 
 *  Function: get_admin_details
 *  Parameters: user id AND/OR username
 *  Purpose: Returns the most important user details for a given user 
 *  Notes: ---
 ********************************************************************** */	
 
 
function get_admin_details($userid = 0, $username = '') {
	global $db;
	if($userid != 0) {	// use userid
		$where = "user_id = %d";
		$param = $userid;
	} elseif($username != '') {	// use username
		$where = "user_username = %s";
		$param = $username;
	} else {
		return false;
	}
	
	$sql = "SELECT user_id, user_username, user_role, user_password, user_email FROM " . table_users . " WHERE " . $where;
	$user_info = $db->get_row($db->prepare($sql, $param));
	return $user_info;
}
	
	
/* ******************************************************************** 
 *  Function: create_table
 *  Parameters: Name of the table to be created
 *  Purpose: Creates a database table
 *  Notes: Deletes the table if it already exists, then makes it again
 ********************************************************************** */
 
function create_table($table_name) {
	global $db, $lang;	

	$sql = 'DROP TABLE IF EXISTS `' . db_prefix . $table_name . '`;';
	$db->query($sql);

	if($table_name == "settings") {
		$sql = "CREATE TABLE `" . db_prefix . $table_name . "` (
		  `settings_id` int(20) NOT NULL auto_increment,
		  `settings_name` varchar(64) NOT NULL default '',
		  `settings_value` text NOT NULL default '',
		  `settings_default` text NOT NULL default '',
		  `settings_note` text NOT NULL default '',
		  PRIMARY KEY  (`settings_id`),
		  UNIQUE KEY `key` (`settings_name`)
		) TYPE = MyISAM;";
		echo $lang['install_step3_creating_table'] . ": '" . $table_name . "'...<br />\n";
		$db->query($sql);
		
		// Insert default settings...
		
		$sql = "INSERT INTO " . db_prefix . $table_name . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
		$db->query($db->prepare($sql, 'site_name', 'Hotaru CMS', 'Hotaru CMS', ''));
		
		$sql = "INSERT INTO " . db_prefix . $table_name . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
		$db->query($db->prepare($sql, 'friendly_urls', 'false', 'false', ''));
		
		$sql = "INSERT INTO " . db_prefix . $table_name . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
		$db->query($db->prepare($sql, 'theme', 'default/', 'default/', 'You need the "\/"'));
		
		$sql = "INSERT INTO " . db_prefix . $table_name . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
		$db->query($db->prepare($sql, 'admin_theme', 'admin_default/', 'admin_default/', 'You need the "\/"'));
		
		$sql = "INSERT INTO " . db_prefix . $table_name . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
		$db->query($db->prepare($sql, 'debug', 'false', 'false', ''));
	}
	
	if($table_name == "users") {	
		$sql = "CREATE TABLE `" . db_prefix . $table_name . "` (
		  `user_id` int(20) NOT NULL auto_increment,
		  `user_username` varchar(32) NOT NULL default '',
		  `user_role` varchar(32) NOT NULL default 'registered_user',
		  `user_date` timestamp NOT NULL,
		  `user_password` varchar(64) NOT NULL default '',
		  `user_email` varchar(128) NOT NULL default '',
		  `user_lastlogin` timestamp NOT NULL,
		  PRIMARY KEY  (`user_id`),
		  UNIQUE KEY `key` (`user_username`),
		  KEY `user_email` (`user_email`)
		) TYPE = MyISAM;";
		echo $lang['install_step3_creating_table'] . ": '" . $table_name . "'...<br />\n";
		$db->query($sql); 
	}
	
	if($table_name == "plugins") {
		$sql = "CREATE TABLE `" . db_prefix . $table_name . "` (
		  `plugin_id` int(11) NOT NULL auto_increment,
		  `plugin_enabled` tinyint(1) NOT NULL default '0',
		  `plugin_name` varchar(64) NOT NULL default '',
		  `plugin_prefix` varchar(16) NOT NULL default '',
		  `plugin_folder` varchar(64) NOT NULL default '',
		  `plugin_desc` varchar(255) NOT NULL default '',
		  `plugin_version` varchar(32) NOT NULL default '0.0',
		  PRIMARY KEY  (`plugin_id`),
		  UNIQUE KEY `key` (`plugin_folder`)
		) TYPE = MyISAM;";
		echo $lang['install_step3_creating_table'] . ": '" . $table_name . "'...<br />\n";
		$db->query($sql);
	}
	
	if($table_name == "pluginhooks") {
		$sql = "CREATE TABLE `" . db_prefix . $table_name . "` (
		  `phook_id` int(20) NOT NULL auto_increment,
		  `plugin_folder` varchar(64) NOT NULL default '',
		  `plugin_hook` varchar(128) NOT NULL default '',
		  PRIMARY KEY  (`phook_id`),
		  INDEX  (`plugin_folder`)
		) TYPE = MyISAM;";
		echo $lang['install_step3_creating_table'] . ": '" . $table_name . "'...<br />\n";
		$db->query($sql);
	}
	
	if($table_name == "pluginsettings") {
		$sql = "CREATE TABLE `" . db_prefix . $table_name . "` (
		  `psetting_id` int(20) NOT NULL auto_increment,
		  `plugin_folder` varchar(64) NOT NULL default '',
		  `plugin_setting` varchar(64) NULL,
		  `plugin_value` text NULL,
		  PRIMARY KEY  (`psetting_id`),
		  INDEX  (`plugin_folder`)
		) TYPE = MyISAM;";
		echo $lang['install_step3_creating_table'] . ": '" . $table_name . "'...<br />\n";
		$db->query($sql);
	}
}
?>