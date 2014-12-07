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
namespace Libs;

class Authorization
{
        /**
	 * check cookie and log in
	 *
	 * @return bool
	 */
	public static function checkSession($h)
	{       
                // Check Session first
                $user = isset($_SESSION["hotaru_user"]) ? $_SESSION["hotaru_user"] : false;
                if ($user) {
                    // If found then user is logged in and clear for all non-secure user functions
                    // i.e. dont let them change password and notificaton settings without inputting their password again first

                    //$h->currentUser = new CurrentUser();
                    //$h->currentUser->setCurrentUser($h, $user);
                    $h->currentUser = $user;  // TODO we should send it to a mapping function with error checking
                    $h->currentUser->loggedIn = true;
                    return true; 
                }
                //$h->messages['no session'] = 'red';
                $cookie = self::getCookieHotaruKey($h);
		
                if(!$cookie) { 
                    //$h->messages['no cookie'] = 'red';
		    self::setLoggedOutUser($h);
		    return false; 
		}
                
                //deprecated old cookie
                $deprecateCookieUsername = $h->cage->cookie->testUsername('hotaru_user');
		if($deprecateCookieUsername) {
                    $oldCookieToken = self::deprecatedOldPasswordHash($deprecateCookieUsername, md5(SITEURL));
                    if ($cookie->token == $oldCookieToken) {
                        // expire old cookie
                        setcookie("hotaru_user", "", time()-3600, "/");
                        $user = $h->getUser(0, $deprecateCookieUsername);
                        if (!$user) { return false; }
                        self::setCookie($h, true);
                        $cookie = self::getCookieHotaruKey($h);
                    }
                }
                
                // load as currentUser. this will get us userid etc
                $user = $h->getUser(0, $cookie->username);
                //$h->messages['getuser: ' . $cookie->username . ', id: ' . $h->currentUser->id . ', token: ' . $cookie->token] = 'green';
                // Check if token matches db login
                $login = \Hotaru\Models2\UserLogin::getLogin($h, $h->currentUser->id, $cookie->token);
                
                if (!$login) {
                    //$h->messages['no match for cookie login'] = 'red';
                    self::setLoggedOutUser($h);
                    return false;
                }
                
                self::setAsLoggedIn($h, "cookie");
                
                // remove old login
                self::removeLoginFromDb($h, $h->currentUser->id, $cookie->token);
                //$h->messages['removed old cookie login from db'] = 'red';
                
                // set new cookie for next time we need it, update timestamp in db
                self::setCookie($h, true);
                $h->updateUserLastVisit();
                //$h->messages['added new cookie and login'] = 'red';
                
                // user_signin throws out killspammed, banned and suspended users
                $h->pluginHook('userauth_checkcookie_success');
		
                return true;
	}
        
        
        /**
         * Password check for methods like updatePassword that dont want to login but just need to confirm the password
         * 
         * @param type $h
         * @param type $username
         * @param type $password
         * @return boolean
         */
        public static function passwordCheck($h, $password = '')
	{
                $result = password_verify($password, $h->currentUser->password);
                if ($result) {
                    return true;
                }
                
                return false;
        }
        
        
	/**
	 * Log a user in if their username and password are valid
	 *
	 * @param string $username
	 * @param string $password
	 * @return bool
	 */
	public static function passwordSignIn($h, $username = '', $password = '', $rememberMe = false, $shouldLockout = false)
	{
                // a non logged in user can still be a currentUser
		$user = $h->getUser(0, $username);
                
		if (!$user) {
                    return false; 
                }
		
                // test
                //print_r($h->currentUser);
//                if ($h->currentUser->name == 'acm2001') {
//                    $h->currentUser->password = $password;
//                    $h->currentUser->updateUserBasic($h, $h->currentUser->id, 1); 
//                    //print_r($h->currentUser);
//                }
                
                // end test
                
                if (self::isUserLockedOut($h, $username)) {
                    // provide message that user is locked out
                    $h->messages['User is locked out'] = 'red';
                    return false;
                }
		
                // destroy the cookie for the following usergroups:
		$no_cookie = array('killspammed', 'banned', 'suspended');
		if (in_array($user->user_role, $no_cookie)) {
			self::destroyCookieAndSession($h);
			return false;
		}
		
		$result = '';
		
		// Allow plugin to bypass the password check with their own methods, e.g. RPX
		$plugin_result = $h->pluginHook('userbase_logincheck', '', array($username, $password));
		
		if (!$plugin_result) {
                    
                        if ($user->password_version == '1') {
                            // old deprecated passwords
                            $deprecatedPassword = self::deprecatedOldPasswordHash($password, substr($user->user_password, 0, 9));
                            $result = ($deprecatedPassword == $user->user_password) ? true : false;
                            if ($result) {
                                $h->currentUser->password = $password;
				$h->currentUser->savePassword($h, $h->currentUser->id); 
                            }
                        } else {
                            // new password version - we can test for version 2,3,4,5 etc later if required but for now anything not a 1 is new
                            $result = password_verify($password, $user->user_password);
                        }
                        
                        if ($result) {
                            $h->messages['Password correct'] = 'green';
                            //signInOrTwoFactor($user, $rememberMe);
                            
                            // TODO once we have got this far we dont need the password
                            // remove it - do not keep it in $h->currentUser ever
                            self::destroyCookieAndSession($h);
                            
                            self::setAsLoggedIn($h, "password");
                            
                            // delete old cookie if exists
                            $cookie = self::getCookieHotaruKey($h);
                            if ($cookie && isset($cookie->token)) {
                                self::removeLoginFromDb($h, $h->currentUser->id, $cookie->token);
                            }
                            
                            // save cookie
                            self::setCookie($h, $rememberMe);
                        } else {
                            $h->messages['Could not login'] = 'red';
                        }

                }  elseif ($plugin_result) {
			// a positive result was returned from the plugin(s)
			// let's hope the plugin did its own authentication and session setting because we've skipped the usual username/password check!
			$result = true;
		} 
		
                // add to lockout count
                if ($shouldLockout) {
                    // self::incrementLockoutCount($user->id);
                    // if (self::isLockedOut($user->id) {
                    //      return false;
                    // }
                }
                
		if ($result) {
                    //print "here";die();
                    self::updateUserLastLogin($h);
                    return true;
                }
                
                return false; 
	}
	
        
        private static function isUserLockedOut($h, $username = '')
        {
                if (!$username) {
                    return false;
                }
            
                $status = \Hotaru\Models2\User::isLockedOut($h, $username);
                return $status;
        }
        
        
        private function signInOrTwoFactor($user, $rememberMe = false)
        {
//            if (UserManager.GetTwoFactorEnabled(user.Id) &&
//                AuthenticationManager.TwoFactorBrowserRemembered(user.Id))
//            {
//                var identity = new ClaimsIdentity(DefaultAuthenticationTypes.TwoFactorCookie);
//                identity.AddClaim(new Claim(ClaimTypes.NameIdentifier, user.Id));
//                AuthenticationManager.SignIn(identity);
//                return SignInStatus.RequiresTwoFactorAuthentication;
//            }
//            SignIn(user, isPersistent, false);
            return true;
        }
    
        
        public function externalSignIn($loginInfo, $rememberMe = false)
        {
            $user = $h->getUserBasic($loginInfo->login);
            $h->setCurrentUser($user);
            
            if (!user) {
                return false;
            }

            if ($user->isLockedOut) {
                return false;  // or a locked out status
            }

            //return self::SignInOrTwoFactor($user, $rememberMe);
        }
        
        
        public function externalLoginCallback($returnUrl = '')
        {         
//            $loginInfo = AuthenticationManager.GetExternalLoginInfoAsync();
//            if (!$loginInfo) {
//                return RedirectToAction("Login");
//            }

            // Sign in the user with this external login provider if the user already has a login
            //$user = $h->userBasic($loginInfo->login);
            //if ($user != null) {
              //  self::SignInAsync($user, false);
                //return RedirectToLocal(returnUrl);
            //} else {
                // If the user does not have an account, then prompt the user to create an account
                //
                //$loginProvider = $loginInfo->login.LoginProvider;
                //return array("externalLoginConfirmation" => new externalLoginConfirmationViewModel { UserName = loginInfo.DefaultUserName });
            //}
        }
        
	
	/**
         * Deprecated - used only to bring old passwords up to new format
	 * Generate a hash for the password
	 *
	 * @param string $plainText - the password
	 * @param mixed $salt
	 *
	 * Note: Adapted from SocialWebCMS
	 */
	public static function deprecatedOldPasswordHash($plainText, $salt = null)
	{
		$salt_length = 9;
		if ($salt === null) {
			$salt = substr(md5(uniqid(rand(), true)), 0, $salt_length); }
		else {
			$salt = substr($salt, 0, $salt_length);
		}
		return $salt . sha1($salt . $plainText);
	}
        
	
        
        private static function setAsLoggedIn($h, $loginType = '')
        {
            $h->currentUser->loggedIn = true;
            $h->currentUser->loginType = $loginType;

            // remove old and create new session object for user
            //unset($_SESSION["hotaru_user"]);
            @session_start();
            $_SESSION["hotaru_user"] = $h->currentUser;
            //print_r($_SESSION);
            //$h->messages['new session set'] = 'green';
        }
	
	/**
	 * Give logged out user default permissions
	 */    
	public static function setLoggedOutUser($h)
	{
		$default_perms = $h->currentUser->getDefaultPermissions($h);
		unset($default_perms['options']);  // don't need this for individual users
		$h->currentUser->setAllPermissions($default_perms);
	}
	
	
	/**
	 * Update last login timeand update user ip
	 *
	 * @return bool
	 */
	private static function updateUserLastLogin($h)
	{
		if ($h->currentUser->id == 0) {
                    return false;
                }
		
                // TODO only save IP if settings tell us to, for performance reasons
                //if ($settings->save_ip_address) {
                    $ip = $h->cage->server->testIp('REMOTE_ADDR');
                //}
                $sql = "UPDATE " . TABLE_USERS . " SET user_lastlogin = CURRENT_TIMESTAMP, user_lastvisit = CURRENT_TIMESTAMP, user_ip = %s WHERE user_id = %d";
                $h->db->query($h->db->prepare($sql, $ip, $h->currentUser->id));
                
                return true;
	}
	
	
	/**
	 * Update last visit (new session started)
	 *
	 * @return bool
	 */
	public static function updateUserLastVisit($h, $user_id = 0)
	{
                if ($user_id == 0) {
                    $user_id = $h->currentUser->id == 0 ? 0 : $h->currentUser->id; 
                }
            
		if ($user_id == 0) { 
                    return false;
                }
                
                $sql = "UPDATE " . TABLE_USERS . " SET user_lastvisit = CURRENT_TIMESTAMP WHERE user_id = %d";
                $h->db->query($h->db->prepare($sql, $user_id));
                
                return true;
	}
	
	
	/**
	 * Set a 30-day cookie
	 *
	 * @param string $remember checkbox with value "checked" or empty
	 * @return bool
	 */
	public static function setCookie($h, $rememberMe)
	{
                if (!$rememberMe) { 
                    return false;
                }
            
		if (!$h->currentUser->name) { 
                    $h->messages['main_userbase_cookie_error'] = 'green';
                    return false;
		} else {
                    // just need random token here. no real reason to pass name in. just easy to use password_hash to create it
                    $cookieToken = password_hash($h->currentUser->name, PASSWORD_DEFAULT);
                    \Hotaru\Models2\UserLogin::addLogin($h, $h->currentUser->id, $cookieToken);
                    
                    $strCookie=base64_encode(
                            join(':', array(
                                $h->currentUser->name,
                                $cookieToken
                            ))
                    );
                    
                    // 2592000 = 60 seconds * 60 mins * 24 hours * 30 days
                    $month = 2592000 + time();
                    
                    if (strpos(SITEURL, "localhost") !== false) {
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

                            setcookie("hotaru_key", $strCookie, $month, "/");
                    }
                    return true;
		}
	}
	
        private static function getCookieHotaruKey($h)
        {
            if(!$h->cage->cookie->keyExists('hotaru_key')) { 
                return false;
            }
            
            $user_info = explode(":", base64_decode($h->cage->cookie->getRaw('hotaru_key')));
		
            $cookie = new \stdClass();
            $cookie->username = isset($user_info[0]) ? $user_info[0] : '';
            $cookie->token = isset($user_info[1]) ? $user_info[1] : '';
            
            return $cookie;
        }
        
        
        private static function removeLoginFromDb($h, $userId, $cookieToken)
        {
                \Hotaru\Models2\UserLogin::removeLogin($h, $userId, $cookieToken);
        }
	
	/**
	 * Delete cookie and destroy session
	 */
	public static function destroyCookieAndSession($h)
	{
                // remove cookieToken from db
                $user = isset($_SESSION["hotaru_user"]) ? $_SESSION["hotaru_user"] : false;
                $cookie = self::getCookieHotaruKey($h);
                //$h->messages['got cookie and user ready to destroy cookie'] = 'green';
                if ($cookie) {
                    if (!$user) {
                        $user = $h->getUser(0, $cookie->username);
                    }
                    if ($user) {
                        // make sure we have $user because cookie could have been old, fake or deleted from db
                        self::removeLoginFromDb($h, $user->user_id, $cookie->token);
                    }
                }
            
		// setting a cookie with a negative time expires it
		if (strpos(SITEURL, "localhost") !== false) {
			setcookie("hotaru_user", "", time()-3600, "/");
			setcookie("hotaru_key", "", time()-3600, "/");
		} else {		
			setcookie("hotaru_user", "", time()-3600, "/");
			setcookie("hotaru_key", "", time()-3600, "/");
		}
		
                // sessions are used in CSRF and for currentUser
                if (session_id()) {
                    session_destroy(); 
                }

		$h->currentUser->loggedIn = false;
	}
}
