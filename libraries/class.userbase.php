<?php

/* ******************************************************************** 
 *  File: libraries/class.userbase.php
 *  Purpose: A simple base class for user basics
 *  Notes: ---
 ********************************************************************** */
 
// includes
require_once('../hotaru_header.php');

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
	
}
 
?>