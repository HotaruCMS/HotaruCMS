<?php
/**
 * name: Users
 * description: Manages users within Hotaru.
 * version: 0.1
 * folder: users
 * prefix: usr
 * hooks: users, hotaru_header, install_plugin_starter_settings, navigation, theme_index_display_conditional
 *
 */
	
/* ***** ACCESS ********************************************************* 
 * This plugin is accessed in two ways:
 * 1. Directly opened via http. This happens if a file links to it <a href=""> or 
 *    sends data from a form, in which case we want to include the Hotaru environment
 *    (hotaru_header.php) and then the get_params() function to process the data;  
 * 2. Included via check_actions() in class.plugins.php. This is done to give Hotaru 
 *    access to the functions, but we don't want to actually run the script from the 
 *    top so we return false for now.
 * ******************************************************************** */

if(isset($user)) {
	if(!is_object($user)) { 
		// Accessed via 1 above;
		require_once('../../hotaru_header.php');
		//usr_get_params(); 
	} else {
		// Not the object we were expecting...
		return false; die(); 
	}
} else { 
	// Accessed via 2 above;
	return false; die(); 
}


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
	define("table_usermeta", db_prefix . 'usermeta');
	require_once(libraries . 'class.userbase.php');
	require_once(plugins . 'users/libraries/class.users.php');
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
 *  Purpose: Adds a link in the navigation bar
 *  Notes: 
 ********************************************************************** */

function usr_navigation() {	
	echo "<li><a href='" . baseurl . "index.php?page=login'>Login</a></li>";
	echo "<li><a href='" . baseurl . "index.php?page=register'>Register</a></li>";
	echo "<li><a href='" . baseurl . "index.php?page=user_settings&user='>Settings</a></li>";
}

function usr_theme_index_display_conditional() {
	global $hotaru;
	if($hotaru->is_page('login')) {
		$hotaru->display_template('/pages/login', 'users'); 
		return true;
	} else {
		return false;
	}
}
?>