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

	global $cage, $lang;
	
	echo "<div id='main'>";
		echo "<h2><a href=" . baseurl . ">Home</a> &raquo; Login</h2>\n";
		
		if(!empty($message)) {  } 
		
		echo "<div class='main_inner'>";
		echo $lang["admin_login_instructions"] . "\n";

			if(!$username_check = $cage->post->testUsername('username')) {
				$username_check = "";
			} 
			if(!$password_check = $cage->post->testPassword('password')) {
				$password_check = "";
			}
			
			if($username_check != "" || $password_check != "") {
				$login_result = admin_login_check($username_check, $password_check);
				if($login_result) {
						//success
						set_admin_cookie($username_check);
						header("Location:" . baseurl . 'admin/admin_index.php');	// Return to front page 
				} else {
						// login failed
						$message = $lang["admin_login_failed"];
						echo "<div class='message red'>" . $message . "</div>\n";
				}
			} else {
				$username_check = "";
				$password_check = "";
			}
			
			echo "<form name='login_form' action='" . baseurl . "admin/admin_index.php?page=admin_login' method='post'>\n";	
			echo "<table>\n";
				echo "<tr><td>Username:&nbsp; </td><td><input type='text' size=30 name='username' value='" . $username_check . "' /></td></tr>\n";
				echo "<tr><td>Password:&nbsp; </td><td><input type='password' size=30 name='password' value='" . $password_check . "' /></td></tr>\n";
				echo "<tr><td>&nbsp; </td><td style='text-align:right;'><input type='submit' value='" . $lang['admin_login_form_submit'] . "' /></td></tr>\n";
							
			echo "</table>\n";
			echo "</form>\n";
		echo "</div>\n";
	echo "</div>\n";
}


/* ******************************************************************** 
 *  Function: set_admin_cookie
 *  Parameters: username
 *  Purpose: Sets 30 day cookies for the admin.
 *  Notes: ---
 ********************************************************************** */
 
function set_admin_cookie($username) {
     /* Set a 30 day cookie */
    if(!$username) { 
    	echo "Error setting cookie. Username not provided.";
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
		header('Location: ' . baseurl . 'admin/admin_index.php?page=admin_login');
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