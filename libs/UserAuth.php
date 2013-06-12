<?php
/**
 * Functions for authnticating, logging in and registering users
 *
 * PHP version 5
 *
 * LICENSE: Hotaru CMS is free software: you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License as 
 * published by the Free Software Foundation, either version 3 of 
 * the License, or (at your option) any later version. 
 *
 * Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE. 
 *
 * You should have received a copy of the GNU General Public License along 
 * with Hotaru CMS. If not, see http://www.gnu.org/licenses/.
 * 
 * @category  Content Management System
 * @package   HotaruCMS
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
class UserAuth extends UserBase
{
	/**
	 * check cookie and log in
	 *
	 * @return bool
	 */
	public function checkCookie($h)
	{
		// Check for a cookie. If present then the user is logged in.
		$h_user = $h->cage->cookie->testUsername('hotaru_user');
		
		if((!$h_user) || (!$h->cage->cookie->keyExists('hotaru_key'))) { 
		    $this->setLoggedOutUser($h);
		    return false; 
		}
		
		$user_info=explode(":", base64_decode($h->cage->cookie->getRaw('hotaru_key')));
		
		if (($h_user != $user_info[0]) || ($h->currentUser->generateHash($h_user, md5(SITEURL)) != $user_info[1])) {
		    $this->setLoggedOutUser($h);
		    return false; 
		}
		
		$this->name = $h_user;
		if ($h_user)
		{
			$valid = false;
			
			// Read the user from the database
			$user_exists = $this->getUser($h, 0, $this->name);
                        if (!$user_exists) return false;
                        
			// validate the user's password
			if ($user_info[2] != md5($user_exists->user_password)) {
				$user_exists = false;
			} else {
				$valid = true;
			}
			
			// Log the user in if valid
			if ($valid) {
				$this->loggedIn = true;
				if (!session_id()) { $this->updateUserLastVisit($h); } // update user_lastvisit field when a new session is created
				$h->pluginHook('userauth_checkcookie_success'); // user_signin throws out killspammed, banned and suspended users
				
				// SUCCESS!!!
				return true;
			} else {
				$h->currentUser->destroyCookieAndSession(); // removes cookie and session for physically deleted users
			}
		}
		
		// otherwise, give them "logged out" permissions
		$this->setLoggedOutUser($h);
		return false; 
	}
	
	
	/**
	 * Log a user in if their username and password are valid
	 *
	 * @param string $username
	 * @param string $password
	 * @return bool
	 */
	public function loginCheck($h, $username = '', $password = '')
	{
		// Read the current user's basic details
		$userX = $this->getUser($h, 0, $username);
		if (!$userX) { return false; }
		
		// destroy the cookie for the following usergroups:
		$no_cookie = array('killspammed', 'banned', 'suspended');
		if (in_array($userX->user_role, $no_cookie)) {
			$this->destroyCookieAndSession();
			return false;
		}
		
		$salt_length = 9;
		$result = '';
		
		// Allow plugin to bypass the password check with their own methods, e.g. RPX
		$plugin_result = $h->pluginHook('userbase_logincheck', '', array($username, $password));
		
		if (!$plugin_result)
		{
			// nothing or (false) was returned from the plugins, so confirm the username and password match:
			$password = $this->generateHash($password, substr($userX->user_password, 0, $salt_length));
			$sql = "SELECT user_username, user_password FROM " . TABLE_USERS . " WHERE user_username = %s AND user_password = %s";
			$result = $h->db->get_row($h->db->prepare($sql, $username, $password));
		} 
		elseif ($plugin_result)
		{
			// a positive result was returned from the plugin(s)
			// let's hope the plugin did its own authentication because we've skipped the usual username/password check!
			$result = true;
		} 
		
		if ($result) { return true; } else { return false; }
	}
	
	
	/**
	 * Generate a hash for the password
	 *
	 * @param string $plainText - the password
	 * @param mixed $salt
	 *
	 * Note: Adapted from SocialWebCMS
	 */
	public function generateHash($plainText, $salt = null)
	{
		$salt_length = 9;
		if ($salt === null) {
			$salt = substr(md5(uniqid(rand(), true)), 0, $salt_length); }
		else {
			$salt = substr($salt, 0, $salt_length);
		}
		return $salt . sha1($salt . $plainText);
	}
	
	
	/**
	 * Give logged out user default permissions
	 */    
	public function setLoggedOutUser($h)
	{
		$default_perms = $this->getDefaultPermissions($h);
		unset($default_perms['options']);  // don't need this for individual users
		$this->setAllPermissions($default_perms);
	}
	
	
	/**
	 * Update last login timeand update user ip
	 *
	 * @return bool
	 */
	public function updateUserLastLogin($h)
	{
		if ($this->id != 0)
		{
			$ip = $h->cage->server->testIp('REMOTE_ADDR');
			$sql = "UPDATE " . TABLE_USERS . " SET user_lastlogin = CURRENT_TIMESTAMP, user_ip = %s WHERE user_id = %d";
			$h->db->query($h->db->prepare($sql, $ip, $this->id));
			return true;
		} else {
			return false;
		}
	}
	
	
	/**
	 * Update last visit (new session started)
	 *
	 * @return bool
	 */
	public function updateUserLastVisit($h, $user_id = 0)
	{
		if ($this->id != 0) {
			if (!$user_id) { $user_id = $this->id; }
			$sql = "UPDATE " . TABLE_USERS . " SET user_lastvisit = CURRENT_TIMESTAMP WHERE user_id = %d";
			$h->db->query($h->db->prepare($sql, $user_id));
			return true;
		} else {
			return false;
		}
	}
	
	
	/**
	 * Set a 30-day cookie
	 *
	 * @param string $remember checkbox with value "checked" or empty
	 * @return bool
	 */
	public function setCookie($h, $remember)
	{
		if (!$this->name)
		{ 
			echo $h->lang('main_userbase_cookie_error');
			return false;
		} else {
			$strCookie=base64_encode(
				join(':', array($this->name, 
				$h->currentUser->generateHash($this->name, md5(SITEURL)),
				md5($this->password)))
			);
			
			if ($remember) { 
				// 2592000 = 60 seconds * 60 mins * 24 hours * 30 days
				$month = 2592000 + time(); 
			} else { 
				$month = 0; 
			}
			
			if (strpos(SITEURL, "localhost") !== false) {
				setcookie("hotaru_user", $this->name, $month, "/");
				setcookie("hotaru_key", $strCookie, $month, "/");
			} else {				
                                /*
                                 * http://no2.php.net/setcookie
                                 * bool setcookie ( string $name [, string $value [, int $expire = 0 [, string $path [, string $domain [, bool $secure = false [, bool $httponly = false ]]]]]] )
                                 * 
                                 * The domain that the cookie is available to.
                                 * Setting the domain to 'www.example.com' will make the cookie available in the www subdomain and higher subdomains.
                                 * Cookies available to a lower domain, such as 'example.com' will be available to higher subdomains, such as 'www.example.com'.
                                 * Older browsers still implementing the deprecated » RFC 2109 may require a leading . to match all subdomains.
                                 * Since we dont want the cookie set on one subdomain to pass to another, we call setcookie without the domain paramater :'get a cookie with "subdomain.example.net" (and not ".subdomain.example.net")'
                                 */                    

				setcookie("hotaru_user", $this->name, $month, "/");
				setcookie("hotaru_key", $strCookie, $month, "/");
			}
			return true;
		}
	}
	
	
	/**
	 * Delete cookie and destroy session
	 */
	public function destroyCookieAndSession()
	{
		// setting a cookie with a negative time expires it
		
		if (strpos(SITEURL, "localhost") !== false) {
			setcookie("hotaru_user", "", time()-3600, "/");
			setcookie("hotaru_key", "", time()-3600, "/");
		} else {		
			setcookie("hotaru_user", "", time()-3600, "/");
			setcookie("hotaru_key", "", time()-3600, "/");
		}
		
                if (session_id()) {
                    session_destroy(); // sessions are used in CSRF
                }
		
		$this->loggedIn = false;
	}
	
	
	 /**
	 * Change username or email
	 *
	 * @param int $userid
	 * @return bool
	 */
	public function updateAccount($h, $userid = 0)
	{
		// $viewee is the person whose account is being modified
		
		$viewee = new UserBase($h);
		
		// Get the details of the account to show.
		// If no account is specified, assume it's your own.
		
		if (!$userid) {
		    $userid = $this->id; 
		}
		
		$viewee->getUser($h, $userid);
		
		$error = 0;
		
		// fill checks
		$checks['userid_check'] = '';
		$checks['username_check'] = '';
		$checks['email_check'] = '';
		$checks['role_check'] = '';
		$checks['password_check_old'] = '';
		$checks['password_check_new'] = '';
		$checks['password_check_new2'] = '';
		
		// Updating account info (username and email address)
		if ($h->cage->post->testAlnumLines('update_type') == 'update_general') {
		
			// check CSRF key
			if (!$h->csrf()) {
				$h->messages[$h->lang('error_csrf')] = 'red';
				$error = 1;
			}
			
			$username_check = $h->cage->post->testUsername('username'); // alphanumeric, dashes and underscores okay, case insensitive
			if (!$username_check) {
				$h->messages[$h->lang('main_user_account_update_username_error')] = 'red';
				$error = 1;
			} elseif($h->nameExists($username_check, '', $viewee->id) || $h->isBlocked('user', $username_check)) {
				$h->messages[$h->lang('main_user_account_update_username_exists')] = 'red';
				$error = 1;
			} else {
				//success
				$viewee->name = $username_check;
			}
			
			$email_check = $h->cage->post->testEmail('email');
			if (!$email_check) {
				$h->messages[$h->lang('main_user_account_update_email_error')] = 'red';
				$error = 1;
			} elseif($h->emailExists($email_check, '', $viewee->id) || $h->isBlocked('email', $email_check)) {
				$h->messages[$h->lang('main_user_account_update_email_exists')] = 'red';
				$error = 1;
			} else {
				//success
				$viewee->email = $email_check;
			}
			
			$role_check = $h->cage->post->testUsername('user_role'); // from Users plugin account page
			// compare with current role and update if different
			if (!$error && $role_check && ($role_check != $viewee->role)) {
				$viewee->role = $role_check;
				$new_perms = $viewee->getDefaultPermissions($h, $role_check);
				$viewee->setAllPermissions($new_perms);
				$viewee->updatePermissions($h);
				if ($role_check == 'killspammed' || $role_check == 'deleted') {
					$h->deleteComments($viewee->id); // includes child comments from *other* users
					$h->deletePosts($viewee->id); // includes tags and votes for self-submitted posts
					
					$h->pluginHook('userbase_killspam', '', array('target_user' => $viewee->id));
					
					if ($role_check == 'deleted') { 
						$h->deleteUser($viewee->id); 
						$checks['username_check'] = 'deleted';
						$h->message = $h->lang("users_account_deleted");
						$h->messageType = 'red';
						return $checks; // This will then show a red "deleted" notice
					}
				}
			}
			
			// If we've just edited our own account, let's refresh the cookie so it uses our latest username:
			if ($h->currentUser->id == $h->cage->post->testInt('userid')) {
				$h->currentUser->setCookie($h, false);           // delete the cookie
				$h->currentUser->getUser($h, $h->currentUser->id, '', true);    // re-read the database record to get updated info
				$h->currentUser->setCookie($h, true);            // create a new, updated cookie
			}
		}
		
		if (!isset($username_check) && !isset($email_check)) {
			$username_check = $viewee->name;
			$email_check = $viewee->email;
			$role_check = $viewee->role;
			// do nothing
		} elseif ($error == 0) {
			$exists = $h->userExists(0, $username_check, $email_check);
			if (($exists != 'no') && ($exists != 'error')) { // user exists
				//success
				$viewee->updateUserBasic($h, $userid);
				// only update the cookie if it's your own account:
				if ($userid == $this->id) { 
				$h->currentUser->setCookie($h, false);           // delete the cookie
				$h->currentUser->getUser($h, $h->currentUser->id, '', true);    // re-read the database record to get updated info
				$h->currentUser->setCookie($h, true);            // create a new, updated cookie
				}
				$h->messages[$h->lang('main_user_account_update_success')] = 'green';
			} else {
				//fail
				$h->messages[$h->lang("main_user_account_update_unexpected_error")] = 'red';
			}
		} else {
			// error must = 1 so fall through and display the form again
		}
		
		//update checks
		$this->updatePassword($h, $userid);
		$userid_check = $viewee->id; 
		$checks['userid_check'] = $userid_check;
		$checks['username_check'] = $username_check;
		$checks['email_check'] = $email_check;
		$checks['role_check'] = $role_check;
		
		return $checks;
	}
	
	
	 /**
	 * Enable a user to change their password
	 *
	 * @return bool
	 */
	public function updatePassword($h, $userid)
	{
		// we don't want to edit the password if this isn't our own account.
		if ($userid != $this->id) { return false; }
		
		$error = 0;
		
		// Updating password
		if ($h->cage->post->testAlnumLines('update_type') == 'update_password') {
		
			// check CSRF key
			if (!$h->csrf()) {
				$h->messages[$h->lang('error_csrf')] = 'red';
				$error = 1;
			}
			
			
			$password_check_old = $h->cage->post->noTags('password_old');
			
			if ($this->loginCheck($h, $this->name, $password_check_old)) {
				// safe, the old password matches the password for this user.
			} else {
				$h->messages[$h->lang('main_user_account_update_password_error_old')] = 'red';
				$error = 1;
			}
			
			$password_check_new = $h->cage->post->testPassword('password_new');    
			if ($password_check_new) {
				$password_check_new2 = $h->cage->post->testPassword('password_new2');    
				if ($password_check_new2) { 
					if ($password_check_new == $password_check_new2) {
						// safe, the two new password fields match
					} else {
						$h->messages[$h->lang('main_user_account_update_password_error_match')] = 'red';
						$error = 1;
					}
				} else {
					$h->messages[$h->lang('main_user_account_update_password_error_new')] = 'red';
					$error = 1;
				}
			} else {
				$h->messages[$h->lang('main_user_account_update_password_error_not_provided')] = 'red';
				$error = 1;
			}
		
		}
		
		if (!isset($password_check_old) && !isset($password_check_new) && !isset($password_check_new2)) {
			$password_check_old = "";
			$password_check_new = "";
			$password_check_new2 = "";
			// do nothing
		} elseif ($error == 0) {
			$exists = $h->userExists(0, $this->name, $this->email);
			if (($exists != 'no') && ($exists != 'error')) { // user exists
				//success
				$this->password = $this->generateHash($password_check_new);
				$this->updateUserBasic($h, $this->id); // update the database record for this user
				$this->setCookie($h, false);           // delete the cookie
				$this->getUser($h, $this->id, '', true);    // re-read the database record to get updated info
				$this->setCookie($h, true);            // create a new, updated cookie
				$h->messages[$h->lang('main_user_account_update_password_success')] = 'green';
			} else {
				//fail
				$h->messages[$h->lang("main_user_account_update_unexpected_error")] = 'red';
			}
		} else {
			// error must = 1 so fall through and display the form again
		}
	}
	
	
	 /**
	 * Send a confirmation code to a user who has forgotten his/her password
	 *
	 * @param string $email - already validated above
	 */
	public function sendPasswordConf($h, $userid, $email)
	{
		// generate the email confirmation code
		$pass_conf = md5(crypt(md5($email),md5($email)));
		
		// store the hash in the user table
		$sql = "UPDATE " . TABLE_USERS . " SET user_password_conf = %s WHERE user_id = %d";
		$h->db->query($h->db->prepare($sql, $pass_conf, $userid));
		
		$line_break = "\r\n\r\n";
		$next_line = "\r\n";
		
		if ($h->isActive('signin')) { 
			$url = SITEURL . 'index.php?page=login&plugin=user_signin&userid=' . $userid . '&passconf=' . $pass_conf; 
		} else { 
			$url = SITEURL . 'admin_index.php?page=admin_login&userid=' . $userid . '&passconf=' . $pass_conf; 
		}
		
		// send email
		$subject = $h->lang('main_user_email_password_conf_subject');
		$body = $h->lang('main_user_email_password_conf_body_hello') . " " . $h->getUserNameFromId($userid);
		$body .= $line_break;
		$body .= $h->lang('main_user_email_password_conf_body_welcome');
		$body .= $h->lang('main_user_email_password_conf_body_click');
		$body .= $line_break;
		$body .= $url;
		$body .= $line_break;
		$body .= $h->lang('main_user_email_password_conf_body_no_request');
		$body .= $line_break;
		$body .= $h->lang('main_user_email_password_conf_body_regards');
		$body .= $next_line;
		$body .= $h->lang('main_user_email_password_conf_body_sign');
		$to = $email;
		
		$h->email($to, $subject, $body);    
		
		return true;
	}
	
	
	 /**
	 * Reset the user's password to soemthing random and email it.
	 *
	 * @param string $passconf - confirmation code clicked in email
	 */
	public function newRandomPassword($h, $userid, $passconf)
	{
		$email = $h->getEmailFromId($userid);
		
		// check the email and confirmation code are a pair
		$pass_conf_check = md5(crypt(md5($email),md5($email)));
		if ($pass_conf_check != $passconf) {
			return false;
		}
		
		// update the password to something random
		$temp_pass = random_string(10);
		$sql = "UPDATE " . TABLE_USERS . " SET user_password = %s WHERE user_id = %d";
		$h->db->query($h->db->prepare($sql, $this->generateHash($temp_pass), $userid));
		$line_break = "\r\n\r\n";
		$next_line = "\r\n";
		
		if ($h->isActive('signin')) { 
			$url = SITEURL . 'index.php?page=login&plugin=user_signin'; 
		} else { 
			$url = SITEURL . 'admin_index.php?page=admin_login'; 
		}
		
		$username = $h->getUserNameFromId($userid);
		
		// send email
		$subject = $h->lang('main_user_email_new_password_subject');
		$body = $h->lang('main_user_email_password_conf_body_hello') . " " . $username;
		$body .= $line_break;
		$body .= $h->lang('main_user_email_password_conf_body_requested');
		$body .= $line_break;
		$body .= $username;
		$body .= $next_line;
		$body .= $temp_pass;
		$body .= $line_break;
		$body .= $h->lang('main_user_email_password_conf_body_remember');
		$body .= $line_break;
		$body .= $h->lang('main_user_email_password_conf_body_pass_change');
		$body .= $line_break;
		$body .= $url; 
		$body .= $line_break;
		$body .= $h->lang('main_user_email_password_conf_body_regards');
		$body .= $next_line;
		$body .= $h->lang('main_user_email_password_conf_body_sign');
		$to = $email;
		
		$h->email($to, $subject, $body);    
		
		return true;
	}
}
