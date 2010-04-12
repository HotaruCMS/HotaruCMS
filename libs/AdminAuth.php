<?php
/**
 * Admin functions - Initialize and authentication
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
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
class AdminAuth
{
	/**
	 * Initialize Admin
	 */
	public function adminInit($h)
	{
		// Authenticate the admin if the Users plugin is INACTIVE:
		if (!$h->isActive('signin'))
		{
			if (($h->pageName != 'admin_login') && !$this->isAdminCookie($h))
			{
				header('Location: ' . BASEURL . 'admin_index.php?page=admin_login');
			}
		}
		
		// Authenticate the admin if a Signin plugin is ACTIVE and site is OPEN:
		if (is_object($h->currentUser) && $h->isActive('signin') && (SITE_OPEN == 'true'))
		{
			// This first condition happens when the Users plugin is activated 
			// and there's no cookie for the Admin yet.
			if (($h->currentUser->name == "") && $h->isActive('signin')) 
			{
				header('Location: ' . BASEURL . 'index.php?page=login');
				die; exit;
			} 
			elseif ($h->currentUser->getPermission('can_access_admin') != 'yes') 
			{
				// maybe the user has permission to access a specific plugin settings page?
				$plugin = $h->cage->get->testAlnumLines('plugin');
				if ($plugin && ($h->pageName == "plugin_settings")) {
					$permission = "can_" . $plugin . "_settings";
					if ($h->currentUser->getPermission($permission) == 'yes') {
						$h->sidebars = false; // hide sidebars
						$h->displayTemplate('index');
						die(); exit;
					}
				}
				
				// User doesn't have permission to access Admin
				$h->messages[$h->lang['main_access_denied']] = 'red';
				$h->displayTemplate('admin_denied');
				die(); exit;
			}
		}
		
		// If we get this far, we know that the user has admin access.
		
		return $h->pageName;
	}
	
	
	 /**
	 * Admin login
	 * 
	 * @return bool
	 */
	public function adminLogin($h)
	{
		// Check username
		if (!$username_check = $h->cage->post->testUsername('username')) { 
			$username_check = ''; 
		} 
		
		// Check password
		if (!$password_check = $h->cage->post->testPassword('password')) {
			$password_check = ''; 
		}
		
		if ($h->cage->post->keyExists('login_attempted') || $h->cage->post->keyExists('forgotten_password')) {
			// if either the login or forgot password form is submitted, check the CSRF key
			
			if (!$h->csrf()) {
				$h->message = $h->lang["error_csrf"];
				$h->messageType = "red";
				return false;
			}
		}
		
		if ($username_check != '' || $password_check != '') 
		{
			$login_result = $h->currentUser->loginCheck($h, $username_check, $password_check);
			
			if ($login_result) {
				//success
				$this->setAdminCookie($username_check);
				$h->currentUser->name = $username_check;
				$h->currentUser->getUserBasic($h, 0, $username_check);
				$h->currentUser->loggedIn = true;
				$h->currentUser->updateUserLastLogin($h);
				$h->sidebars = true;
				$h->pageName = 'admin_home'; // a wee hack
				return true;
			} else {
				// login failed
				$h->message = $h->lang["admin_login_failed"];
				$h->messageType = "red";
			}
		} 
		else 
		{
			if ($h->cage->post->keyExists('login_attempted')) {
				$h->message = $h->lang["admin_login_failed"];
				$h->messageType = "red";
			}
			$username_check = '';
			$password_check = '';
			
			// forgotten password request
			if ($h->cage->post->keyExists('forgotten_password')) {
				$this->adminPassword($h);
			}
			
			// confirming forgotten password email
			$passconf = $h->cage->get->getAlnum('passconf');
			$userid = $h->cage->get->testInt('userid');
			
			if ($passconf && $userid) {
				if ($h->currentUser->newRandomPassword($h, $userid, $passconf)) {
					$h->message = $h->lang['admin_email_password_conf_success'];
					$h->messageType = "green";
				} else {
					$h->message = $h->lang['admin_email_password_conf_fail'];
					$h->messageType = "red";
				}
			}
		}
		
		return false;
	}
	
	
	 /**
	 * Admin password forgotten
	 * 
	 * @return bool
	 */
	public function adminPassword($h)
	{
		// Check email
		if (!$email_check = $h->cage->post->testEmail('email')) { 
			$email_check = ''; 
			// login failed
			$h->message = $h->lang["admin_login_email_invalid"];
			$h->messageType = "red";
			return false;
		} 

		$valid_email = $h->emailExists($email_check, 'admin');
		$userid = $h->getUserIdFromEmail($valid_email);
		
		if ($valid_email && $userid) {
			//success
			$h->currentUser->sendPasswordConf($h, $userid, $valid_email);
			$h->message = $h->lang['admin_email_password_conf_sent'];
			$h->messageType = "green";
			return true;
		} else {
			// login failed
			$h->message = $h->lang["admin_login_email_invalid"];
			$h->messageType = "red";
			return false;
		}
	}
	
	
	 /**
	 * Admin login form
	 */
	public function adminLoginForm($h)
	{
		// Check username
		if (!$username_check = $h->cage->post->testUsername('username')) {
			$username_check = '';
		} 
		
		// Check password
		if (!$password_check = $h->cage->post->testPassword('password')) {
			$password_check = ''; 
		}
		
		// Check email (for forgotten password form)
		if (!$email_check = $h->cage->post->testEmail('email')) {
			$email_check = ''; 
		}
		
		require_once(ADMIN_THEMES . ADMIN_THEME . 'admin_login.php');
	}
	
	
	/**
	 * Set a 30-day cookie for the administrator
	 *
	 * @param string $username
	 *
	 * @return bool
	 */
	public function setAdminCookie($username)
	{
		if (!$username) 
		{ 
			echo $this->lang["admin_login_error_cookie"];
			return false;
		} 
		else 
		{
			$strCookie=base64_encode(
				join(':', array($username, crypt($username, 22)))
			);
			
			// (2592000 = 60 seconds * 60 mins * 24 hours * 30 days.)
			$month = 2592000 + time();
			
			if (strpos(BASEURL, "localhost") !== false) {
			     setcookie("hotaru_user", $username, $month, "/");
			     setcookie("hotaru_key", $strCookie, $month, "/");
			} else {
			     $parsed = parse_url(BASEURL); 
			                
			     // now we need a dot in front of that so cookies work across subdomains:
			     setcookie("hotaru_user", $username, $month, "/", "." . $parsed['host']);
			     setcookie("hotaru_key", $strCookie, $month, "/", "." . $parsed['host']);
			}  
			
			return true;
		}
	}

	 /**
	 *  Checks if a cookie exists and if it belongs to an Admin user
	 *
	 * @return bool
	 *
	 * Note: This is only used if the Users plugin is inactive.
	 */
	public function isAdminCookie($h)
	{   
		// Check for a cookie. If present then the user goes through authentication
		if (!$h->cage->cookie->testUsername('hotaru_user')) { return false; }
		if (!$h->cage->cookie->keyExists('hotaru_key')) { return false; }
		
		$h_user = $h->cage->cookie->testUsername('hotaru_user');
		
		// authenticate...
		$user_info=explode(":", base64_decode($h->cage->cookie->getRaw('hotaru_key')));
		
		if (($h_user != $user_info[0]) || (crypt($user_info[0], 22) != $user_info[1])) { return false; }
		
		if (!$h->isAdmin($h_user)) { return false; }
		
		if (!session_id()) {
			$h->currentUser->updateUserLastVisit($h);
		}
		
		//success...
		return true;
	}
	
	 /**
	 * Admin logout
	 *
	 * @return true
	 */
	public function adminLogout($h)
	{
		$h->currentUser->destroyCookieAndSession();
		header("Location: " . BASEURL);
		return true;
	}
}
?>
