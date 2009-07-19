<?php
 
/* **************************************************************************************************** 
 *  File: /plugins/users/update.php
 *  Purpose: For updating your username, email or password.
 *  Notes: This file is part of the Users plugin. The main file is /plugins/users/users.php
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
 *  Function: usr_update
 *  Parameters: None
 *  Purpose: Enables a user to change their username, email or password.
 *  Notes: ---
 ********************************************************************** */
 
function usr_update() {
	global $hotaru, $cage, $lang, $current_user;
	
	echo "<div id='main'>";
		echo "<p class='breadcrumbs'><a href='" . baseurl . "'>Home</a> &raquo; User Settings</p>\n";
		
		echo "<div class='main_inner'>";
		echo $lang["users_update_instructions"] . "\n";
		
		$error = 0;
		if($cage->post->getAlpha('users_type') == 'update') {
			$username_check = $cage->post->testUsername('username'); // alphanumeric, dashes and underscores okay, case insensitive
			if($username_check) {
				$current_user->username = $username_check;
			} else {
				$hotaru->message = $lang['users_register_username_error'];
				$hotaru->message_type = 'red';
				$hotaru->show_message();
				$error = 1;
			}
					
			$password_check = $cage->post->testPassword('password');	
			if($password_check) {
				$current_user->password = crypt(md5($password_check),md5($current_user->username));
			} else {
				$hotaru->message = $lang['users_register_password_error'];
				$hotaru->message_type = 'red';
				$hotaru->show_message();
				$error = 1;
			}
						
			$email_check = $cage->post->testEmail('email');	
			if($email_check) {
				$current_user->email = $email_check;
			} else {
				$hotaru->message = $lang['users_register_email_error'];
				$hotaru->message_type = 'red';
				$hotaru->show_message();
				$error = 1;
			}
		}
				
		if(!isset($username_check) && !isset($password_check) && !isset($email_check)) {
			$username_check = $current_user->username;
			$password_check = $current_user->password;
			$email_check = $current_user->email;
			// do nothing
		} elseif($error == 0) {
			$result = $current_user->user_exists(0, $username_check, $email_check);
			if($result != 4) { // 4 is returned when the user does not exist in the database
				//success
				$current_user->update_user_basic();
				$current_user->set_cookie(0);
				$hotaru->message = $lang['users_update_success'];
				$hotaru->message_type = 'green';
				$hotaru->show_message();
			} else {
				//fail
				$hotaru->message = $lang["users_register_unexpected_error"];
				$hotaru->message_type = 'red';
				$hotaru->show_message();
			}
		} else {
			// error must = 1 so fall through and display the form again
		}
		
			echo "<form name='update_form' action='" . baseurl . "index.php?page=profile' method='post'>\n";	
			echo "<table>";
			echo "<tr><td>Username:&nbsp; </td><td><input type='text' size=30 name='username' value='" . $username_check . "' /></td></tr>\n";
			echo "<tr><td>Email:&nbsp; </td><td><input type='text' size=30 name='email' value='" . $email_check . "' /></td></tr>\n";
			echo "<tr><td>Password:&nbsp; </td><td><input type='password' size=30 name='password' value='" . $password_check . "' /></td></tr>\n";
			echo "<input type='hidden' name='users_type' value='update' />\n";
			echo "<tr><td>&nbsp;</td><td style='text-align:right;'><input type='submit' value='" . $lang['users_update_form_submit'] . "' /></td></tr>\n";			
			echo "</table>\n";
			echo "</form>\n";
		echo "</div>\n";
	echo "</div>\n";
}

?>