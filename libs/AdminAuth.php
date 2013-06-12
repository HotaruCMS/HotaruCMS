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
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
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
                if (($h->pageName == 'admin_logout')) {
                    $this->adminLogout($h);
                }
                    
		// Authenticate the admin if the User Signin plugin is INACTIVE:
		if (!$h->isActive('signin'))
		{
			if (($h->pageName != 'admin_login') && !$this->isAdminCookie($h))
			{
				header('Location: ' . SITEURL . 'admin_index.php?page=admin_login');
				die; exit;
			}
		}
		
		// Authenticate the admin if a Signin plugin is ACTIVE and site is OPEN:
		if (is_object($h->currentUser) && $h->isActive('signin') && (SITE_OPEN == 'true'))
		{
			// This first condition happens when the Users plugin is activated 
			// and there's no cookie for the Admin yet.
			if (($h->currentUser->name == "") && $h->isActive('signin')) 
			{
				header('Location: ' . SITEURL . 'index.php?page=login');
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
						$h->template('index');
						die(); exit;
					}
				}
				
				// User doesn't have permission to access Admin
				$h->messages[$h->lang('main_access_denied')] = 'red';
				$h->template('admin_denied');
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
			// check the CSRF key coming from either the navigation bar, login or forgot password forms     
			if (!$h->csrf('check', 'navigation') && !$h->csrfToken) {
				$h->message = $h->lang("error_csrf");
				$h->messageType = "red";
				return false;
			}
		}
		
		if ($username_check != '' || $password_check != '') 
		{
			$login_result = $h->currentUser->loginCheck($h, $username_check, $password_check);
			
			if ($login_result) {
				//success                            
				$h->currentUser->name = $username_check;
				$h->currentUser->getUser($h, 0, $username_check);
				$this->setAdminCookie($h, $username_check);
				$h->currentUser->loggedIn = true;
				$h->currentUser->updateUserLastLogin($h);
                                
                                // check role is admin
                                if ($h->currentUser->role == 'admin') 
                                { 	$h->sidebars = true;
                                        $h->pageName = 'admin_home'; // a wee hack
                                } else {
                                        $h->sidebars = false;
                                        $h->pageName = 'admin_login';
                                        $h->message = $h->lang("admin_not_adminuser");
                                        $h->messageType = "red";
                                }
                				
				return true;
			} else {
				// login failed
				$h->message = $h->lang("admin_login_failed");
				$h->messageType = "red";
			}
		} 
		else 
		{
			if ($h->cage->post->keyExists('login_attempted')) {
				$h->message = $h->lang("admin_login_failed");
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
					$h->message = $h->lang('admin_email_password_conf_success');
					$h->messageType = "green";
				} else {
					$h->message = $h->lang('admin_email_password_conf_fail');
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
			$h->message = $h->lang("admin_login_email_invalid");
			$h->messageType = "red";
			return false;
		} 

		$valid_email = $h->emailExists($email_check, 'admin');
		$userid = $h->getUserIdFromEmail($valid_email);
		
		if ($valid_email && $userid) {
			//success
			$h->currentUser->sendPasswordConf($h, $userid, $valid_email);
			$h->message = $h->lang('admin_email_password_conf_sent');
			$h->messageType = "green";
			return true;
		} else {
			// login failed
			$h->message = $h->lang("admin_login_email_invalid");
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
	 * @return bool
	 */
	public function setAdminCookie($h)
	{                                
		if (!$h->currentUser->name) 
		{ 
			echo $this->lang["admin_login_error_cookie"];
			return false;
		} 
		else 
		{
			$strCookie=base64_encode(
				join(':', array($h->currentUser->name, 
				$h->currentUser->generateHash($h->currentUser->name, md5(SITEURL)),
				md5($h->currentUser->password)))
			);
			
			// (2592000 = 60 seconds * 60 mins * 24 hours * 30 days.)
			$month = 2592000 + time();
			
			if (strpos(SITEURL, "localhost") !== false) {
			     setcookie("hotaru_user", $h->currentUser->name, $month, "/");
			     setcookie("hotaru_key", $strCookie, $month, "/");
			} else {			                
			     setcookie("hotaru_user", $h->currentUser->name, $month, "/");
			     setcookie("hotaru_key", $strCookie, $month, "/");
			}  
			
			return true;
		}
	}

	 /**
	 *  Checks if a cookie exists and if it belongs to an Admin user
	 *
	 * @return bool
	 *
	 * NOTE!!! This is ONLY used if the User Signin plugin is inactive.
	 */
	public function isAdminCookie($h)
	{
		if (!$h->currentUser->checkCookie($h)) { return false; }
		
		if (!$h->isAdmin($h->currentUser->name)) { return false; }

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
		header("Location: " . SITEURL);
		return true;
	}
}
?>
