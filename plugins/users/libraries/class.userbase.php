<?php

/* ******************************************************************** 
 *  File: libraries/class.userbase.php
 *  Purpose: A simple base class for user basics
 *  Notes: ---
 ********************************************************************** */
 
class UserBase {	// Limited to the absolute essential user information. Plugins extend this.
 
 	var $id = 0;
	var $username = '';
	var $role = 'registered_user';
	var $password = '';
	var $email = '';
	var $logged_in = false;
	
	
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
		$this->id = $user_info->user_id;
		$this->username = $user_info->user_username;
		$this->role = $user_info->user_role;
		$this->email = $user_info->user_email;
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
             /* Set a 30 day cookie */
            if(!$this->username) { 
            	echo "Error setting cookie. Username not provided.";
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
	 *  Function: session_defaults
	 *  Parameters: None
	 *  Purpose: Sets default session data.
	 *  Notes: ---
	 ********************************************************************** */
	/* 
        function session_defaults() {
        	$_SESSION['loggedin'] = false;
		$_SESSION['userid'] = 0;
		$_SESSION['username'] = '';
		$_SESSION['remember'] = false; 
        }
        */	
        	
	/* ******************************************************************** 
	 *  Function: set_session
	 *  Parameters: None
	 *  Purpose: Sets ession data for this user.
	 *  Notes: ---
	 ********************************************************************** */  
	/*       
	function set_session() {
		$_SESSION['loggedin'] = true;
		$_SESSION['userid'] = $this->id;
		$_SESSION['username'] = $this->username;
	} 
	
	*/
}
 
?>