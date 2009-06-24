<?php
/**
 * name: Users
 * description: Manages users within Hotaru.
 * version: 0.1
 * folder: users
 * prefix: usr
 * hooks: users, hotaru_header, install_plugin_starter_settings, navigation, theme_index_display
 *
 */
	
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
	global $lang;
	define("table_usermeta", db_prefix . 'usermeta');
	require_once(libraries . 'class.userbase.php');
	require_once(plugins . 'users/libraries/class.users.php');
	
	// include users language file
	if(file_exists(plugins . 'users/languages/users_' . strtolower(sitelanguage) . '.php')) {
		require_once(plugins . 'users/languages/users_' . strtolower(sitelanguage) . '.php');	// language file for admin
	} else {
		require_once(plugins . 'users/languages/users_english.php');	// English file if specified language doesn't exist
	}
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
	echo "<li><a href='" . baseurl . "index.php?page=login'>Login</a></li>";
	echo "<li><a href='" . baseurl . "index.php?page=register'>Register</a></li>";
	echo "<li><a href='" . baseurl . "index.php?page=user_settings&user='>Settings</a></li>";
}


/* ******************************************************************** 
 *  Function: usr_theme_index_display
 *  Parameters: None
 *  Purpose: Echos the login form to index.php 
 *  Notes: Previously directed to a login.php template file included in this plugin, but decided a function was better. (Nick)
 ********************************************************************** */
 
function usr_theme_index_display() {
	global $hotaru, $cage;
	if($hotaru->is_page('login')) {
		require_once(plugins . 'users/login.php');
		//$hotaru->display_template('/pages/login', 'users');  
		usr_login();
		return true;
	} elseif($hotaru->is_page('register')) {
		require_once(plugins . 'users/register.php');
		usr_register();
		return true;		
	} else {
		return false;
	}
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