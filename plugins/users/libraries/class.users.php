<?php

/* ******************************************************************** 
 *  File: plugins/users/libraries/class.users.php
 *  Purpose: Everything pertaining to users
 *  Notes: ---
 ********************************************************************** */

//includes
//require_once(libraries . 'class.userbase.php');

class Users extends UserBase {	// functions dealing with multiple users
 

}

class User extends Users {	// functions dealing with individual users
 
	function login_check($username = '', $password = '') {
		global $db;
		
		$password = crypt(md5($password),md5($username));
		$result = $db->get_row($db->prepare("SELECT user_username, user_password FROM " . table_users . " WHERE user_username = %s AND user_password = %s", $username, $password));
		if(isset($result)) {return true; } else { return false; }
	}
}
 
?>