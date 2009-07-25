<?php

/* ********** PLUGIN *********************************************************************************
 * name: Users
 * description: Manages users within Hotaru.
 * version: 0.1
 * folder: users
 * prefix: usr
 * hooks: users, hotaru_header, install_plugin, navigation_users, theme_index_replace, theme_index_main
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
	global $lang, $cage, $plugin;

	if(!defined('table_usermeta')) { define("table_usermeta", db_prefix . 'usermeta'); }
	
	// include language file
	$plugin->include_language_file('users');
}


/* ******************************************************************** 
 *  Function: usr_install_plugin
 *  Parameters: None
 *  Purpose: If it doesn't already exist, a "usermeta" table is created in the database
 *  Notes: Happens when theplugin is installed. The table is never deleted.
 ********************************************************************** */
 
function usr_install_plugin() {
	global $db, $plugin;
	
	// Create a new empty table called "usermeta"
	$exists = $db->table_exists('usermeta');
	if(!$exists) {
		//echo "table doesn't exist. Stopping before creation."; exit;
		$sql = "CREATE TABLE `" . db_prefix . "usermeta` (
		  `usermeta_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  `usermeta_userid` int(20) NOT NULL DEFAULT 0,
		  `usermeta_key` varchar(255) NULL,
		  `usermeta_value` text NULL,
		  `usermeta_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
 		  `usermeta_updateby` int(20) NOT NULL DEFAULT 0, 
		  INDEX  (`usermeta_userid`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='User Meta';";
		$db->query($sql); 
	}
}


/* ******************************************************************** 
 *  Function: usr_navigation_users
 *  Parameters: None
 *  Purpose: Adds links to the end of the navigation bar
 *  Notes: 
 ********************************************************************** */

function usr_navigation_users() {	
	global $current_user, $lang;
	
	if($current_user->logged_in) {
		echo "<li><a href='" . url(array('page'=>'profile')) . "'>" . $lang["users_profile"] . "</a></li>\n";
		echo "<li><a href='" . url(array('page'=>'logout')) . "'>" . $lang["users_logout"] . "</a></li>\n";
		if($current_user->role == 'administrator') {
			echo "<li><a href='" . url(array(), 'admin') . "'>" . $lang["users_admin"] . "</a></li>\n";
		}
	} else {	
		echo "<li><a href='" . url(array('page'=>'login')) . "'>" . $lang["users_login"] . "</a></li>\n";
		echo "<li><a href='" . url(array('page'=>'register')) . "'>" . $lang["users_register"] . "</a></li>\n";
	}
}


/* ******************************************************************** 
 *  Function: usr_theme_index_replace
 *  Parameters: None
 *  Purpose: Echos the login form to index.php 
 *  Notes: Work to do *before* we send output to the page.
 ********************************************************************** */
 
function usr_theme_index_replace() {
	global $hotaru, $cage, $current_user;
	
	// Pages you have to be logged in for...
	if($current_user->logged_in) {
		 if($hotaru->is_page('logout')) {
			$current_user->destroy_cookie_and_session();
			header("Location: " . baseurl);
		} elseif($hotaru->is_page('profile')) {
			usr_update_general();
			usr_update_password();	
		} 
				
	// Pages you have to be logged out for...
	} else {
		if($hotaru->is_page('register')) {
			if(usr_register()) { 
				// success, return to front page, logged OUT.
				header("Location: " . baseurl . "index.php?page=login");
			}
		} elseif($hotaru->is_page('login')) {
			if(usr_login()) { 
				// success, return to front page, logged IN.
				header("Location: " . baseurl);
			}
		} 	
	}
	return false;
}


/* ******************************************************************** 
 *  Function: usr_theme_index_main
 *  Parameters: None
 *  Purpose: Displays various forms within the body of the page.
 *  Notes: 
 ********************************************************************** */
 
function usr_theme_index_main() {
	global $hotaru, $cage, $current_user;
	
	// Pages you have to be logged in for...
	if($current_user->logged_in) {
		if($hotaru->is_page('profile')) {
			$hotaru->display_template('update', 'users');
			return true;
		} else {
			return false;
		}
		
	// Pages you have to be logged out for...
	} else {
		if($hotaru->is_page('register')) {
			$hotaru->display_template('register', 'users');
			return true;	
		} elseif($hotaru->is_page('login')) {
			$hotaru->display_template('login', 'users');
			return true;
		} else {
			return false;
		}	
	}
	return false;
}


 /* ******************************************************************** 
 *  Function: usr_update_general
 *  Parameters: None
 *  Purpose: Enables a user to change their username or email.
 *  Notes: ---
 ********************************************************************** */
 
function usr_update_general() {
	global $hotaru, $cage, $lang, $current_user;
	
	$error = 0;
	
	// Updating general profile info
	if($cage->post->testAlnumLines('users_type') == 'update_general') {
		$username_check = $cage->post->testUsername('username'); // alphanumeric, dashes and underscores okay, case insensitive
		if($username_check) {
			$current_user->username = $username_check;
		} else {
			$hotaru->messages[$lang['users_register_username_error']] = 'red';
			$error = 1;
		}
							
		$email_check = $cage->post->testEmail('email');	
		if($email_check) {
			$current_user->email = $email_check;
		} else {
			$hotaru->messages[$lang['users_register_email_error']] = 'red';
			$error = 1;
		}
	}
	
	if(!isset($username_check) && !isset($email_check)) {
		$username_check = $current_user->username;
		$email_check = $current_user->email;
		// do nothing
	} elseif($error == 0) {
		$result = $current_user->user_exists(0, $username_check, $email_check);
		if($result != 4) { // 4 is returned when the user does not exist in the database
			//success
			$current_user->update_user_basic();
			$current_user->set_cookie(0);
			$hotaru->messages[$lang['users_update_success']] = 'green';
			return true;
		} else {
			//fail
			$hotaru->messages[$lang["users_register_unexpected_error"]] = 'red';
			return false;
		}
	} else {
		// error must = 1 so fall through and display the form again
		return false;
	}
}


 /* ******************************************************************** 
 *  Function: usr_update_password
 *  Parameters: None
 *  Purpose: Enables a user to change their password.
 *  Notes: ---
 ********************************************************************** */
 
function usr_update_password() {
	global $hotaru, $cage, $lang, $current_user;
	
	$error = 0;
	
	// Updating password
	if($cage->post->testAlnumLines('users_type') == 'update_password') {
		$password_check_old = $cage->post->testPassword('password_old');	
		if($password_check_old && (crypt(md5($password_check_old),md5($current_user->username) == $current_user->password))) {
			// safe, the old password matches the password for this user.
		} else {
			$hotaru->messages[$lang['users_update_password_error_old']] = 'red';
			$error = 1;
		}
	
		$password_check_new = $cage->post->testPassword('password_new');	
		if($password_check_new) {
			$password_check_new2 = $cage->post->testPassword('password_new2');	
			if($password_check_new2) { 
				if($password_check_new == $password_check_new2) {
					// safe, the two new password fields match
				} else {
					$hotaru->messages[$lang['users_update_password_error_match']] = 'red';
					$error = 1;
				}
			} else {
				$hotaru->messages[$lang['users_update_password_error_new']] = 'red';
				$error = 1;
			}
		} else {
			$hotaru->messages[$lang['users_update_password_error_not_provided']] = 'red';
			$error = 1;
		}
					
	}
			
	if(!isset($password_check_old) && !isset($password_check_new) && !isset($password_check_new2)) {
		$password_check_old = "";
		$password_check_new = "";
		$password_check_new2 = "";
		// do nothing
	} elseif($error == 0) {
		$result = $current_user->user_exists(0, $current_user->username, $current_user->email);
		if($result != 4) { // 4 is returned when the user does not exist in the database
			//success
			$current_user->password = crypt(md5($password_check_new),md5($current_user->username));
			$current_user->update_user_basic();
			$current_user->set_cookie(0);
			$hotaru->messages[$lang['users_update_success']] = 'green';
			return true;
		} else {
			//fail
			$hotaru->messages[$lang["users_register_unexpected_error"]] = 'red';
			return false;
		}
	} else {
		// error must = 1 so fall through and display the form again
		return false;
	}
}


 /* ******************************************************************** 
 *  Function: usr_login
 *  Parameters: None, but gets login and password for verification from $cage
 *  Purpose: Verifies whether a user can log in or not.
 *  Notes: 
 ********************************************************************** */
 
function usr_login() {
	global $hotaru, $cage, $lang;
	
	$current_user = new UserBase();
	
	if(!$username_check = $cage->post->testUsername('username')) {
		$username_check = "";
	} 
	if(!$password_check = $cage->post->testPassword('password')) {
		$password_check = "";
	}
	
	if($username_check != "" || $password_check != "") {
		$login_result = $current_user->login_check($username_check, $password_check);
		if($login_result) {
				//success
				if($cage->post->getInt('remember') == 1){ $remember = 1; } else { $remember = 0; }
				$current_user->username = $username_check;
				$current_user->get_user_basic(0, $current_user->username);
				$current_user->set_cookie($remember);
				$current_user->logged_in = true;
				$current_user->update_user_lastlogin();
				return true;
		} else {
				// login failed
				$hotaru->message = $lang["users_login_failed"];
				$hotaru->message_type = 'red';
		}
	} 
	return false;
}


 /* ******************************************************************** 
 *  Function: usr_register
 *  Parameters: None, but gets register and password for verification from $cage
 *  Purpose: Registering a new user.
 *  Notes: 
 ********************************************************************** */
 
function usr_register() {
	global $hotaru, $cage, $lang;
	
	$current_user = new UserBase();
	
	$error = 0;
	if($cage->post->getAlpha('users_type') == 'register') {
		$username_check = $cage->post->testUsername('username'); // alphanumeric, dashes and underscores okay, case insensitive
		if($username_check) {
			$current_user->username = $username_check;
		} else {
			$hotaru->messages[$lang['users_register_username_error']] = 'red';
			$error = 1;
		}
				
		$password_check = $cage->post->testPassword('password');	
		if($password_check) {
			$password2_check = $cage->post->testPassword('password2');
			if($password_check == $password2_check) {
				// safe, the two new password fields match
				$current_user->password = crypt(md5($password_check),md5($current_user->username));
			} else {
				$hotaru->messages[$lang['users_register_password_match_error']] = 'red';
				$error = 1;
			}
			
		} else {
			$hotaru->messages[$lang['users_register_password_error']] = 'red';
			$error = 1;
		}
					
		$email_check = $cage->post->testEmail('email');	
		if($email_check) {
			$current_user->email = $email_check;
		} else {
			$hotaru->messages[$lang['users_register_email_error']] = 'red';
			$error = 1;
		}
	}
			
	if(!isset($username_check) && !isset($password_check) && !isset($password2_check) && !isset($email_check)) {
		$username_check = "";
		$password_check = "";
		$password2_check = "";
		$email_check = "";
		// do nothing
	} elseif($error == 0) {
		$result = $current_user->user_exists(0, $username_check, $email_check);
		if($result == 4) {
			$current_user->add_user_basic();
			//success
			return true;
		} elseif($result == 0) {
			$hotaru->messages[$lang['users_register_id_exists']] = 'red';

		} elseif($result == 1) {
			$hotaru->messages[$lang['users_register_username_exists']] = 'red';

		} elseif($result == 2) {
			$hotaru->messages[$lang['users_register_email_exists']] = 'red';
		} else {
			$hotaru->messages[$lang["users_register_unexpected_error"]] = 'red';
		}
	} else {
		// error must = 1 so fall through and display the form again
	}
	return false;
}

?>