<?php

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
		echo "<h2><a href=" . baseurl . "index.php'>Home</a> &raquo; Login</h2>\n";
		
		if(!empty($message)) {  } 
		
		echo "<div class='main_inner'>";
		echo $lang["admin_login_reason"] . "<br /><br />\n";
		echo $lang["admin_login_instructions"] . "\n";

			if(!$username_check = $cage->post->testRegex('username', '/^([a-z0-9_-]{4,32})+$/i')) {
				$username_check = "";
			} 
			if(!$password_check = $cage->post->testRegex('password', '/^([a-z0-9@*#_-]{8,60})+$/i')) {
				$password_check = "";
			}
			
			if($username_check != "" || $password_check != "") {
				$login_result = admin_login_check($username_check, $password_check);
				if($login_result) {
						//success
						$_SESSION['username'] = $username_check;
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
 *  Function: is_admin_session
 *  Parameters: None
 *  Purpose: Checks for the existence and validity of the admin trying to log in
 *  Notes: This is only used if the Users plugin is inactive.
 ********************************************************************** */
 
function is_admin_session() {
	// check if session exists...
	if(!isset($_SESSION['username'])) { 
		header('Location: ' . baseurl . 'admin/admin_index.php?page=admin_login');
		die();
	} else {
		// check if it's a safe username...
		if(!preg_match('/^([a-z0-9_-])+$/i', $_SESSION['username'])) {
			return false;
			die();	
		} else {
			// check if the user is an admin
			$possible_admin_user = $_SESSION['username'];
			if(!is_admin($possible_admin_user)) {
				return false;
				die();
			}
		}
	}
	
	return true;
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

?>