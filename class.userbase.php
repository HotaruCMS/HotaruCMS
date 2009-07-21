<?php

/* **************************************************************************************************** 
 *  File: /class.userbase.php
 *  Purpose: Basic user functions for logging in , registering, etc.
 *  Notes: Plugin such as "Users" extend this class.
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
 
class UserBase {	
 
 	var $id = 0;
	var $username = '';
	var $role = 'registered_user';
	var $password = 'password';
	var $email = '';
	var $logged_in = false;
	
	
	/* ******************************************************************** 
	 *  Function: add_user_basic
	 *  Parameters: username, role, password, email
	 *  Purpose: Inserts a new user into the database with most important data only
	 *  Notes: ---
	 ********************************************************************** */
	 
	function add_user_basic() {
		global $db;
		$sql = "INSERT INTO " . table_users . " (user_username, user_role, user_date, user_password, user_email) VALUES (%s, %s, CURRENT_TIMESTAMP, %s, %s)";
		$db->query($db->prepare($sql, $this->username, $this->role, $this->password, $this->email));
	}
	
	
	/* ******************************************************************** 
	 *  Function: update_user_basic
	 *  Parameters: username, role, password, email
	 *  Purpose: Updates user's most important details only.
	 *  Notes: ---
	 ********************************************************************** */	
	
	function update_user_basic() {
		global $db;
		if($this->id != 0) {
			$sql = "UPDATE " . table_users . " SET user_username = %s, user_role = %s, user_password = %s, user_email = %s, user_updateby = %d WHERE user_id = %d";
			$db->query($db->prepare($sql, $this->username, $this->role, $this->password, $this->email, $this->id, $this->id));
			return true;
		} else {
			return false;
		}
	}
	
	
	/* ******************************************************************** 
	 *  Function: update_user_lastlogin
	 *  Parameters: user id
	 *  Purpose: Updates the lastlogin field in the users table.
	 *  Notes: Updated when the user successfully logs in.
	 ********************************************************************** */	
	
	function update_user_lastlogin() {
		global $db;
		if($this->id != 0) {
			$sql = "UPDATE " . table_users . " SET user_lastlogin = CURRENT_TIMESTAMP WHERE user_id = %d";
			$db->query($db->prepare($sql, $this->id));
			return true;
		} else {
			return false;
		}
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
		if($user_info) {
			$this->id = $user_info->user_id;
			$this->username = $user_info->user_username;
			$this->password = $user_info->user_password;
			$this->role = $user_info->user_role;
			$this->email = $user_info->user_email;
			return $user_info;
		} else {
			return false;
		}
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
	 *  Function: login_check
	 *  Parameters: Username and password
	 *  Purpose: Returns true if a user is found with a matching username and password
	 *  Notes: ---
	 ********************************************************************** */
	 	
	function login_check($username = '', $password = '') {
		global $db;
		
		$password = crypt(md5($password),md5($username));
		$result = $db->get_row($db->prepare("SELECT user_username, user_password FROM " . table_users . " WHERE user_username = %s AND user_password = %s", $username, $password));
		if(isset($result)) {
			$this->get_user_basic(0, $username);	// Read the current user's basic details
			return true; 
		} else { 
			return false; 
		}
	}
	
	
	/* ******************************************************************** 
	 *  Function: set_cookie
	 *  Parameters: username
	 *  Purpose: Sets 30 day cookies for the user.
	 *  Notes: ---
	 ********************************************************************** */
	 
	function set_cookie($remember) {
		global $lang;
             /* Set a 30 day cookie */
            if(!$this->username) { 
            	echo $lang['main_userbase_cookie_error'];
            	return false;
            } else {
            	$strCookie=base64_encode(join(':', array($this->username, crypt($this->username, 22))));
		if($remember) { $month = 2592000 + time(); } else { $month = 0; }// (2592000 = 60 seconds * 60 mins * 24 hours * 30 days.)
		setcookie("hotaru_user", $this->username, $month, "/");
		setcookie("hotaru_key", $strCookie, $month, "/");
		return true;
            }
        }
        
        	
	/* ******************************************************************** 
	 *  Function: destory_cookie_and_session
	 *  Parameters: None
	 *  Purpose: Deletes cookies and destroys the session.
	 *  Notes: ---
	 ********************************************************************** */
	 
	function destroy_cookie_and_session() {
		/* setting a cookie with a negative time expires it */
		setcookie("hotaru_user", "", time()-3600, "/");
		setcookie("hotaru_key", "", time()-3600, "/");
		session_destroy();
		$this->logged_in = false;
        }
        
        
	/* ******************************************************************** 
	 *  Function: get_username
	 *  Parameters: None
	 *  Purpose: Gets the username for a given id
	 *  Notes: ---
	 ********************************************************************** */	
	 
	function get_username($id = 0) {
		global $db, $user;
		$sql = "SELECT user_username FROM " . table_users . " WHERE user_id = %d";
		$username = $db->get_var($db->prepare($sql, $id));
		if($username) { return $username; } else { return false; }
	}
}
 
?>