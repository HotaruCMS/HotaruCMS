<?php

/* ******************************************************************** 
 *  File: /plugins/users/register.php
 *  Purpose: Includes necessary functions for logging in and out.
 *  Notes: 
 ********************************************************************** */
 
 /* ******************************************************************** 
 *  Function: usr_register
 *  Parameters: None, but gets register and password for verification from $cage
 *  Purpose: Displays a register form, retrieves submitted values and calls the User class to verify them.
 *  Notes: 
 ********************************************************************** */
 
function usr_register() {
	global $plugin, $cage, $lang;
	
	$user = new User();
	
	echo "<div id='main'>";
		echo "<h2><a href=" . baseurl . "index.php'>Home</a> &raquo; Register</h2>\n";
		
		if(!empty($plugin->message)) { echo "<div class='message " . $plugin->message_type . "'>" . $plugin->message . "</div>\n"; } 
		
		echo "<div class='main_inner'>";
		echo $lang["users_register_instructions"] . "\n";
		
		$error = 0;
		if($cage->post->getAlpha('users_type') == 'register') {
			$username_check = $cage->post->testRegex('username', '/^([a-z0-9_-]{4,32})+$/i');	// alphanumeric, dashes and underscores okay, case insensitive
			if($username_check) {
				$user->username = $username_check;
			} else {
				$plugin->message = $lang['users_register_username_error'];
				$plugin->message_type = 'red';
				echo "<div class='message " . $plugin->message_type . "'>" . $plugin->message . "</div>\n"; 
				$error = 1;
			}
					
			$password_check = $cage->post->testRegex('password', '/^([a-z0-9@*#_-]{8,60})+$/i');	
			if($password_check) {
				$user->password = crypt(md5($password_check),md5($user->username));
			} else {
				$plugin->message = $lang['users_register_password_error'];
				$plugin->message_type = 'red';
				echo "<div class='message " . $plugin->message_type . "'>" . $plugin->message . "</div>\n"; 
				$error = 1;
			}
						
			$email_check = $cage->post->testEmail('email');	
			if($email_check) {
				$user->email = $email_check;
			} else {
				$plugin->message = $lang['users_register_email_error'];
				$plugin->message_type = 'red';
				echo "<div class='message " . $plugin->message_type . "'>" . $plugin->message . "</div>\n"; 
				$error = 1;
			}
		}
				
		if(!isset($username_check) && !isset($password_check) && !isset($email_check)) {
			$username_check = "";
			$password_check = "";
			$email_check = "";
			// do nothing
		} elseif($error == 0) {
			$result = $user->user_exists(0, $username_check, $email_check);
			echo "result: " . $result . "<br />"; 
			if($result == 4) {
				$user->add_user_basic($user->username, 'registered_user', $user->password, $user->email);
				//success
				header("Location:" . baseurl . "index.php");	// TEMPORARY 
			} elseif($result == 0) {
				echo $lang['users_register_id_exists'];

			} elseif($result == 1) {
				echo $lang['users_register_username_exists'];

			} elseif($result == 2) {
				echo $lang['users_register_email_exists'];
			} else {
				echo $lang["users_register_unexpected_error"];
			}
		} else {
			// error must = 1 so fall through and display the form again
		}
		
			echo "<form name='register_form' action='" . baseurl . "index.php?page=register' method='post'>\n";	
			echo "<table>";
			echo "<tr><td>Username:&nbsp; </td><td><input type='text' size=30 name='username' value='" . $username_check . "' /></td></tr>\n";
			echo "<tr><td>Email:&nbsp; </td><td><input type='text' size=30 name='email' value='" . $email_check . "' /></td></tr>\n";
			echo "<tr><td>Password:&nbsp; </td><td><input type='password' size=30 name='password' value='" . $password_check . "' /></td></tr>\n";
			echo "<input type='hidden' name='users_type' value='register' />\n";
			echo "<tr><td>&nbsp;</td><td style='text-align:right;'><input type='submit' value='" . $lang['users_register_form_submit'] . "' /></td></tr>\n";			
			echo "</table>\n";
			echo "</form>\n";
		echo "</div>\n";
	echo "</div>\n";
}

?>