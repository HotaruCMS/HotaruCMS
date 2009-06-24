<?php

/* ******************************************************************** 
 *  File: libraries/class.userbase.php
 *  Purpose: A simple base class for user basics
 *  Notes: ---
 ********************************************************************** */
 
class UserBase {	// Limited to the absolute essential user information. Plugins extend this.
 
 	var $id = 0;
	var $username = '';
	var $role = '';
	var $password = '';
	var $email = '';
	
	
	/* ******************************************************************** 
	 *  Function: add_user_basic
	 *  Parameters: username, role, password, email
	 *  Purpose: Inserts a new user into the database with most important data only
	 *  Notes: ---
	 ********************************************************************** */
	 
	function add_user_basic($username = '', $role = 'registered_user', $password = 'password', $email = '') {
		global $db;
		$sql = "INSERT INTO " . table_users . " (user_username, user_role, user_password, user_email) VALUES (%s, %s, %s, %s)";
		$db->query($db->prepare($sql, $username, $role, $password, $email));
	}
	
	
	/* ******************************************************************** 
	 *  Function: update_user_basic
	 *  Parameters: username, role, password, email
	 *  Purpose: Updates user's most important details only.
	 *  Notes: ---
	 ********************************************************************** */	
	
	function update_user_basic($username = '', $role = 'registered_user', $password = 'password', $email = '') {
		global $db;
		$sql = "UPDATE " . table_users . " SET user_username = %s, user_role = %s, user_password = %s, user_email = %s WHERE user_role = %s";
		$db->query($db->prepare($sql, $username, $role, $password, $email, $role));
	}
		
	
	/* ******************************************************************** 
	 *  Function: get_user_basic
	 *  Parameters: user id AND/OR username
	 *  Purpose: Returns the most important user details for a given user 
	 *  Notes: ---
	 ********************************************************************** */	
	 
	 
	function get_user_basic($userid = 0, $username = '') {
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
	 *  Function: user_exists
	 *  Parameters: is, username, email
	 *  Purpose: Returns 4 if a user does not exist, otherwise 0-3 for errors
	 *  Notes: ---
	 ********************************************************************** */
	 		
	function user_exists($id = 0, $username = '', $email = '') {
		global $db;
		if($id != 0) {
			if($db->get_var($db->prepare("SELECT * FROM " . table_users . " WHERE user_id = %d", $id))) {
				return 0; // id exists
			} 
		} 
		
		
		if($username != '') {
			if($db->get_var($db->prepare("SELECT * FROM " . table_users . " WHERE user_username = %s", $username))) {
				return 1; // username exists
			} 		
		} 
		
		if($email != '') {
			if($db->get_var($db->prepare("SELECT * FROM " . table_users . " WHERE user_email = %s", $email))) {
				return 2; // email exists
			} 		
		} 
		
		if(($id == 0) && ($username == '') && ($email == '')) {
				return 3; // no arguments provided
		} 
		
		return 4; // user exists
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
	
}
 
?>