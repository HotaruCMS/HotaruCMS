<?php

/* ******************************************************************** 
 *  File: /plugins/users/login.php
 *  Purpose: Includes necessary functions for logging in and out.
 *  Notes: 
 ********************************************************************** */
 
 /* ******************************************************************** 
 *  Function: usr_login
 *  Parameters: None, but gets login and password for verification from $cage
 *  Purpose: Displays a login form, retrieves submitted values and calls the User class to verify them.
 *  Notes: 
 ********************************************************************** */
 
function usr_login() {
	global $plugin, $cage, $lang;
	
	$current_user = new User();
	
	echo "<div id='main'>";
		echo "<h2><a href=" . baseurl . "index.php'>Home</a> &raquo; Login</h2>\n";
		
		echo "<div class='main_inner'>";
		echo $lang["users_login_instructions"] . "\n";

			if(!$username_check = $cage->post->testRegex('username', '/^([a-z0-9_-]{4,32})+$/i')) {
				$username_check = "";
			} 
			if(!$password_check = $cage->post->testRegex('password', '/^([a-z0-9@*#_-]{8,60})+$/i')) {
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
						header("Location:" . baseurl);	// Return to front page 
				} else {
						// login failed
						$plugin->message = $lang["users_login_failed"];
						$plugin->message_type = 'red';
						echo "<div class='message " . $plugin->message_type . "'>" . $plugin->message . "</div>\n"; 
						if($cage->post->getInt('remember') == 1){ $remember_check = "checked"; } else { $remember_check = ""; }
				}
			} else {
				$username_check = "";
				$password_check = "";
				$remember_check = "";
			}

			echo "<form name='login_form' action='" . baseurl . "index.php?page=login' method='post'>\n";
			echo "<table>\n";
				echo "<tr><td>Username:&nbsp; </td><td><input type='text' size=30 name='username' value='" . $username_check . "' /></td></tr>\n";
				echo "<tr><td>Password:&nbsp; </td><td><input type='password' size=30 name='password' value='" . $password_check . "' /></td></tr>\n";
				echo "<tr><td>Remember: </td><td><input type='checkbox' name='remember' value='1'" . $remember_check . " /></td></tr>\n";
				echo "<tr><td>&nbsp; </td><td style='text-align:right;'><input type='submit' value='" . $lang['users_login_form_submit'] . "' /></td></tr>\n";
							
			echo "</table>\n";
			echo "</form>\n";
		echo "</div>\n";
	echo "</div>\n";
}

?>