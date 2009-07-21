<?php

/* **************************************************************************************************** 
 *  File: /admin/admin_login.php
 *  Purpose: Used in order to verify the administrator before accessing Admin
 *  Notes: This is only used if the Users plugin is inactive.
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
 
 /* ******************************************************************** 
 *  Function: admin_login
 *  Parameters: None
 *  Purpose: Used in order to verify the administrator before accessing Admin
 *  Notes: This is only used if the Users plugin is inactive.
 *         There's no "remember" because the user is logged in via a session, not a cookie.  
 ********************************************************************** */

function admin_login() {
	global $cage, $lang, $current_user, $hotaru;
			
	if(!$username_check = $cage->post->testUsername('username')) { $username_check = ""; } 
	if(!$password_check = $cage->post->testPassword('password')) { $password_check = ""; }
				
	if($username_check != "" || $password_check != "") {
		$login_result = admin_login_check($username_check, $password_check);
		if($login_result) {
				//success
				set_admin_cookie($username_check);
				$current_user->username = $username_check;
				$current_user->get_user_basic(0, $username_check);
				$current_user->logged_in = true;
				$current_user->update_user_lastlogin();
				return true;
		} else {
				// login failed
				$hotaru->message = $lang["admin_login_failed"];
				$hotaru->message_type = "red";
		}
	} else {
		if($cage->post->keyExists('login_attempted')) {
			$hotaru->message = $lang["admin_login_failed"];
			$hotaru->message_type = "red";
		}
		$username_check = "";
		$password_check = "";
	}
	
	return false;
}
	
function admin_login_form() {
	global $cage, $lang, $hotaru;	
	
	if(!$username_check = $cage->post->testUsername('username')) { $username_check = ""; } 
	if(!$password_check = $cage->post->testPassword('password')) { $password_check = ""; }
	
	require_once(admin_themes . admin_theme . 'login.php');
}


/* ******************************************************************** 
 *  Function: set_admin_cookie
 *  Parameters: username
 *  Purpose: Sets 30 day cookies for the admin.
 *  Notes: ---
 ********************************************************************** */
 
function set_admin_cookie($username) {
	global $lang;

	if(!$username) { 
		echo $lang["admin_login_error_cookie"];
		return false;
	} else {
		$strCookie=base64_encode(join(':', array($username, crypt($username, 22))));
		$month = 2592000 + time(); // (2592000 = 60 seconds * 60 mins * 24 hours * 30 days.)
		setcookie("hotaru_user", $username, $month, "/");
		setcookie("hotaru_key", $strCookie, $month, "/");
		return true;
	}
}
        
 /* ******************************************************************** 
 *  Function: is_admin_cookie
 *  Parameters: None
 *  Purpose: Checks for the existence and validity of the admin trying to log in
 *  Notes: This is only used if the Users plugin is inactive.
 ********************************************************************** */
 
function is_admin_cookie() {
	global $cage;
	// Check for a cookie. If present then the user is logged in.
	if(!$hotaru_user = $cage->cookie->testUsername('hotaru_user')) {
		return false;
		die();
	} else {
		// authenticate...
		if(($hotaru_user) && ($cage->cookie->keyExists('hotaru_key'))) {
			$user_info=explode(":", base64_decode($cage->cookie->getRaw('hotaru_key')));
			if(($hotaru_user == $user_info[0]) && (crypt($user_info[0], 22) == $user_info[1])) {
				if(!is_admin($hotaru_user)) {
					return false;
					die();
				} else {
					//success...
					return true;
				}
			}
		} else {
			return false;
			die();	
		}
	}
}


 /* ******************************************************************** 
 *  Function: is_admin
 *  Parameters: Username
 *  Purpose: Checks if the user has an 'administrator' role
 *  Notes: ---
 ********************************************************************** */

function is_admin($username) {
	global $db;
	$role = $db->get_row($db->prepare("SELECT * FROM " . table_users . " WHERE user_username = %s AND user_role = %s", $username, 'administrator'));
	if($role) { return true; } else { return false; }
}


 /* ******************************************************************** 
 *  Function: admin_login_check
 *  Parameters: Username and password
 *  Purpose: If the username and password match then the user is logged in.
 *  Notes: ---
 ********************************************************************** */
 
function admin_login_check($username = '', $password = '') {
	global $db;
	
	$password = crypt(md5($password),md5($username));
	$result = $db->get_row($db->prepare("SELECT user_username, user_password FROM " . table_users . " WHERE user_username = %s AND user_password = %s", $username, $password));
	if($result) { return true; } else { return false; }
}


 /* ******************************************************************** 
 *  Function: admin_logout
 *  Parameters: None
 *  Purpose: Logs Admin out
 *  Notes: ---
 ********************************************************************** */
 
function admin_logout() {
	global $current_user;
	$current_user->destroy_cookie_and_session();
	header("Location: " . baseurl);
	return true;
}

?>