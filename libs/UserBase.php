<?php
/**
 * Base User functions for basic info, settings and permissions
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

class UserBase extends Prefab
{
	protected $id           = 0;
	protected $name         = '';
	protected $role         = 'member';
        protected $isAdmin      = false;
        protected $adminAccess  = false;
	protected $password     = 'unknown';
        protected $passwordVersion = 2;     // current default
	protected $email        = '';
	protected $emailValid   = 0;
            protected $loggedIn     = false;
	protected $perms        = array();  // permissions
	protected $settings     = array();  // settings
	protected $profile      = array();  // profile
	protected $ip           = 0;
	protected $lastActivity = 0;
            protected $loginType    = "";
	
	/**
	 * Access modifier to set protected properties
	 */
	public function __set($var, $val)
	{
		$this->$var = $val;  
	}
	
	
	/**
	 * Access modifier to get protected properties
	 */
	public function &__get($var)
	{
		return $this->$var;
	}
	
	
	/**
	 * Set permission 
	 *
	 * @param string $perm_name
	 * @param mixed $setting
	 */
	public function setPermission($perm_name, $setting)
	{
		// don't need options for each user:
		if ($perm_name == 'options') { return false; }

		$this->perms[$perm_name] = $setting;
	}
	
	
	/**
	 * Set ALL permissions 
	 *
	 * @param string $setting
	 */
	public function setAllPermissions($perms)
	{
		foreach ($perms as $key => $value) {
			$this->setPermission($key, $value);
		}
	}
	
	
	/**
	 * Get permission 
	 *
	 * @param string $perm_name
	 * @return mixed 
	 */
	public function getPermission($perm_name)
	{
		if (!isset($this->perms[$perm_name])) { 
			return false; 
		} else {
			return $this->perms[$perm_name];
		}
	}
	
	
	/**
	 * Get ALL permissions (serialized)
	 *
	 * @return string 
	 */
	public function getAllPermissions()
	{
		return $this->perms;
	}
	
	
	/**
	 * Get basic user details
	 *
	 * @param int $userid 
	 * @param string $username
	 * @param bool $no_cache - set true to disable caching of SQl results
	 * @return array|false
	 *
	 * Note: Needs either userid or username, not both
	 */    
	public function getUserBasic($h, $userId = 0, $username = '', $no_cache = false)
	{
                if ($userId != 0){ 
                    if (isset($h->users[$userId])) {                        
                        $user = $h->users[$userId];
                    } else {
                        //$user = \HotaruModels\User::getBasicFromUserId($userId);
                        $user = \HotaruModels2\User::getBasicFromUserId($h, $userId);
                        $h->users[$userId] = $user;
                    }
                } elseif ($username != '') {
                    if (isset($h->users[$username]) && !empty($h->users[$username])) {
                        $user = $h->users[$username];
                    } else {
                        //$user = \HotaruModels\User::getBasicFromUsername($username);
                        $user = \HotaruModels2\User::getBasicFromUsername($h, $username);
                        $h->users[$username] = $user;
                    }
                } else {
                    return false;
                }
		 
                if (!$user) { return false; }
                
		return $user;
	}
        
        
        /**
         * Sets the $h->currentUser info
         * 
         * @param type $h
         * @param type $user
         * @return boolean
         */
        public function setCurrentUser($h, $user)
        {
                if (!$user) { return false; }
		
		$this->id = $user->user_id;
		$this->name = $user->user_username;
		$this->password = $user->user_password;
                $this->passwordVersion = $user->password_version;
		$this->role = $user->user_role;
                $this->isAdmin = $this->role == 'admin' ? true : false;
                $this->adminAccess = $this->getPermission('can_access_admin') == 'yes' ? true : false;
		$this->email = $user->user_email;
		$this->emailValid = $user->user_email_valid;
		$this->ip = $user->user_ip;
        }
        
        
        /**
         * Sets the $h->displayUser info
         * 
         * @param type $h
         * @param type $user
         * @return boolean
         */
        public function set($h, $userid = 0, $username = '', $no_cache = false)
        {
                $user = $this->getUser($h, $userid, $username, $no_cache);
                
                if (!$user) { return false; }
                
		//print_r($user);
		$this->id = $user->user_id;
		$this->name = $user->user_username;
		$this->password = $user->user_password;
                $this->passwordVersion = $user->password_version;
		$this->role = $user->user_role;
                $this->isAdmin = $this->role == 'admin' ? true : false;
                $this->adminAccess = $this->getPermission('can_access_admin') == 'yes' ? true : false;
		$this->email = $user->user_email;
		$this->emailValid = $user->user_email_valid;
		$this->ip = $user->user_ip;
        }
	
	
	/**
	 * Get full user details (i.e. permissions and settings, too)
	 *
	 * @param int $userid 
	 * @param string $username
	 * @param bool $no_cache - set true to disable caching of SQl results
	 * @return array|false
	 *
	 * Note: Needs either userid or username, not both
	 */    
	public function getUser($h, $userid = 0, $username = '', $no_cache = false)
	{
		$user_info = $this->getUserBasic($h, $userid, $username, $no_cache);
		if (!$user_info) { return false; }

		// If a new plugin is installed, we need a way of adding any new default permissions
		// that plugin provides. So, we get all defaults, then overwrite with existing perms.
		
		// get default permissions for the site
		$default_perms = $this->getDefaultPermissions($h, $this->role);
		
		// get existing permissions for the user		
		$existing_perms = unserialize($user_info->user_permissions);
		
		// merge permissions
		if (!$default_perms) { $default_perms = array(); }
		if (!$existing_perms) { $existing_perms = array(); }
		$updated_perms = array_merge($default_perms, $existing_perms);
		
		$this->setAllPermissions($updated_perms);
		$user_info->user_permissions = serialize($updated_perms);   // update $user_info
		
		// get user settings:
		$this->settings = $this->getProfileSettingsData($h, 'user_settings', $this->id);
                
                $this->setCurrentUser($h, $user_info);
                
		return $user_info;
	}
	
	
	/**
	 * Add a new user
	 */
	public function addUserBasic($h)
	{
		// get default permissions
		$permissions = $this->getDefaultPermissions($h, $this->role);
		
		// get user ip
		$userip = $h->cage->server->testIp('REMOTE_ADDR');
		
                $passwordHash = password_hash($this->password, PASSWORD_DEFAULT);
                
		// add user to the database
		$sql = "INSERT INTO " . TABLE_USERS . " (user_username, user_role, user_date, user_password, password_version, user_email, user_permissions, user_ip) VALUES (%s, %s, CURRENT_TIMESTAMP, %s, %d, %s, %s, %s)";
		$h->db->query($h->db->prepare($sql, $this->name, $this->role, $passwordHash, 2, $this->email, serialize($permissions), $userip));
	}
	
        
	/**
	 * Update a user
	 */
	public function updateUserBasic($h)
	{
		if ($this->id != 0) {
			$sql = "UPDATE " . TABLE_USERS . " SET user_username = %s, user_role = %s, user_email = %s, user_email_valid = %d, user_permissions = %s, user_ip = %s, user_updateby = %d WHERE user_id = %d";
			$h->db->query($h->db->prepare($sql, $this->name, $this->role, $this->email, 1, serialize($this->getAllPermissions()), $this->ip, $this->id, $this->id));
			return true;
		} else {
			return false;
		}
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
				$h->setCookie($h, false);           // delete the cookie
				$h->getUser($h, $h->currentUser->id, '', true);    // re-read the database record to get updated info
				$h->setCookie($h, true);            // create a new, updated cookie
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
				$h->setCookie($h, false);           // delete the cookie
				$h->getUser($h, $h->currentUser->id, '', true);    // re-read the database record to get updated info
				$h->setCookie($h, true);            // create a new, updated cookie
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
			
			if ($h->passwordCheck($password_check_old)) {
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
				$this->password = $password_check_new;
				$this->savePassword($h, $this->id); // update the database record for this user
				$h->setCookie($h, false);           // delete the cookie
				$this->getUser($h, $this->id, '', true);    // re-read the database record to get updated info
				$h->setCookie($h, true);            // create a new, updated cookie
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
	 * save a users password
	 */
        public function savePassword($h, $passwordVersion = 2)
        {
                if ($passwordVersion == 1) {
                    // only used when testing for old accounts
                    $auth = new \Libs\Authorization();
                    $passwordHash = $auth->deprecatedOldPasswordHash($this->password);
                    $this->passwordVersion = 1;
                } else {
                    $passwordHash = password_hash($this->password, PASSWORD_DEFAULT);
                    $this->passwordVersion = 2;
                }
                
                $sql = "UPDATE " . TABLE_USERS . " SET user_password = %s, password_version = %d, user_email_valid = %d, user_updateby = %d WHERE user_id = %d";
                $h->db->query($h->db->prepare($sql, $passwordHash, $this->passwordVersion, 1, $this->id, $this->id));
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
		$sql = "UPDATE " . TABLE_USERS . " SET user_password = %s, password_version = %d WHERE user_id = %d";
		$h->db->query($h->db->prepare($sql, password_hash($temp_pass, PASSWORD_DEFAULT), 2, $userid));
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
        
	
	/**
	 * Physically delete this user
	 * Note: You should delete all their posts, comments, etc. first
	 *
	 * @param array $user_id (optional)
	 */
	public function deleteUser($h, $user_id = 0) 
	{
		if (!$user_id) { $user_id = $this->id; }
		
		$h->pluginHook('userbase_delete_user', '', array('user_id'=>$user_id));
		
		$sql = "DELETE FROM " . TABLE_USERS . " WHERE user_id = %d";
		$h->db->query($h->db->prepare($sql, $user_id));
		
		$sql = "DELETE FROM " . TABLE_USERMETA . " WHERE usermeta_userid = %d";
		$h->db->query($h->db->prepare($sql, $user_id));
	}


	/**
	 * Default permissions
	 *
	 * @param string $role or 'all'
	 * @param string $field 'site' for site defaults and 'base' for base defaults
	 * @param book $options_only returns just the options if true
	 * @return array $perms
	 */
	public function getDefaultPermissions($h, $role = '', $defaults = 'site', $options_only = false) 
	{
		$perms = array(); // to be filled with default permissions for this user
		
		if ($defaults == 'site') { 
                    //$field = 'miscdata_value';
                    //$db_perms = \HotaruModels\Miscdata::getCurrentValue('permissions');
                    $db_perms = \HotaruModels2\Miscdata::getCurrentValue($h, 'permissions');
		} else {
                    //$field = 'miscdata_default';
                    //$db_perms = \HotaruModels\Miscdata::getDefaultValue('permissions');
                    $db_perms = \HotaruModels2\Miscdata::getDefaultValue($h, 'permissions');
		}
		
		// get default permissions from the database:
                
//		$query = "SELECT " . $field . " FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s LIMIT 1";
//		$sql = $h->db->prepare($query, 'permissions');
//		
//		// Create temp cache array
//		if (!isset($h->vars['tempPermissionsCache'])) { $h->vars['tempPermissionsCache'] = array(); }
//		
//		// If this query has already been read once this page load, we should have it in memory...
//		if (array_key_exists($sql, $h->vars['tempPermissionsCache'])) {
//			// Fetch from memory
//			$db_perms = $h->vars['tempPermissionsCache'][$sql];
//		} else {
//			// Fetch from database
//			$db_perms = $h->db->get_var($sql);
//			$h->vars['tempPermissionsCache'][$sql] = $db_perms;
//		}
		
		$permissions = unserialize($db_perms);
		
		if (!$permissions) { return array(); } // must return an empty array for array_merge, not false.
		
		if ($options_only) {
			return $permissions['options']; // the editPermissions function in the Users plugin needs these 
		}
		
		if ($role == 'all') { return $permissions; } // plugins need all permissions and options when installed
		
		unset($permissions['options']); // don't need the options anymore
		
		foreach ($permissions as $perm => $roles) { 
			if (isset($roles[$role])) {
				$perms[$perm] = $roles[$role];  // perm for this role
			} else {
				$perms[$perm] = $roles['default']; // default perm because nothing specified for this role
			}
		}
		
		return $perms;
	}
	
	
	/**
	 * Update Default permissions
	 *
	 * @param array $new_perms from a plugin's install function
	 * @param string $defaults - either "site", "base" or "both"
	 * @param bool $remove - false if adding perms, true if deleting them
	 */
	public function updateDefaultPermissions($h, $new_perms = array(), $defaults = 'both', $remove = false) 
	{
		if (!$new_perms) { return false; }
		
		// get and merge permissions
		if ($defaults == 'site')
		{
			if ($remove) {
				$site_perms = $new_perms;
			} else {
				$site_perms = $this->getDefaultPermissions($h,'all', 'site'); //get site defaults
				$site_perms = array_merge_recursive($site_perms, $new_perms); // merge
			}
			$sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_value = %s WHERE miscdata_key = %s";
			$h->db->query($h->db->prepare($sql, serialize($site_perms), 'permissions'));
		} 
		elseif ($defaults == 'base')
		{
			if ($remove) {
				$base_perms = $new_perms;
			} else {
				$base_perms = $this->getDefaultPermissions($h,'all', 'base'); // get base defaults
				$base_perms = array_merge_recursive($base_perms, $new_perms); // merge
			}
			$sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_default = %s WHERE miscdata_key = %s";
			$h->db->query($h->db->prepare($sql, serialize($base_perms), 'permissions'));
		}
		else 
		{
			if ($remove) {
				$site_perms = $new_perms;
				$base_perms = $new_perms;
			} else {
				$site_perms = $this->getDefaultPermissions($h,'all', 'site'); //get site defaults
				$site_perms = array_merge_recursive($site_perms, $new_perms); // merge
				$base_perms = $this->getDefaultPermissions($h,'all', 'base'); // get base defaults
				$base_perms = array_merge_recursive($base_perms, $new_perms); // merge
			}
			$sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_value = %s, miscdata_default = %s WHERE miscdata_key = %s";
			$h->db->query($h->db->prepare($sql, serialize($site_perms), serialize($base_perms), 'permissions'));
		}
	}
	
	
	/**
	 * update permissions in the database
	 *
	 * @param int $userid
	 */
	public function updatePermissions($h)
	{
		$sql = "UPDATE " . TABLE_USERS . " SET user_permissions = %s WHERE user_id = %d";
		$h->db->query($h->db->prepare($sql, serialize($this->getAllPermissions()), $this->id));
		
		// for undermods and above, set their emailValid to true when updating permissions or changing role
		$safe_array = array('undermod', 'member', 'moderator', 'supermod', 'admin');
		if (!$this->emailValid && in_array($this->role, $safe_array)) {
			$sql = "UPDATE " . TABLE_USERS . " SET user_email_valid = %d WHERE user_id = %d";
			$h->db->query($h->db->prepare($sql, 1, $this->id));
		}
	}
		
		
	/**
	* Get a user's profile or settings data
	*
	* @return array|false
	*/
	public function getProfileSettingsData($h, $type = 'user_profile', $userid = 0, $check_exists_only = false)
	{
                //print_r($this);
                if (!$userid) { $userid = $this->id; }
                //print "id: " . $this->id;
                //$result = \HotaruModels\Usermeta::getProfileSetting($userid, $type);
                $result = \HotaruModels2\Usermeta::getProfileSetting($h, $userid, $type);

		// if we're only testing to see if the settings exist, return here:
		if($check_exists_only && $result) { return true; }
		if($check_exists_only && !$result) { return false; }
		
		if ($result) { 
                    $result = unserialize($result);
                    if ($type == 'user_settings') {
                            $defaults = $this->getDefaultSettings($h);
                            if ($defaults) {
                                    $result = array_merge($defaults, $result);
                            }
                    }
                    return $result; 
		} elseif ($type == 'user_settings') {
                    return $this->getDefaultSettings($h);
                }
                
                return false;
	}
	
	
	/**
	 * Save a user's profile or settings data
	 *
	 * @return array|false
	 */
	public function saveProfileSettingsData($h, $data = array(), $type = 'user_profile', $userid = 0)
	{
		if (!$data) { return false; }
		if (!$userid) { $userid = $this->id; }
		
		$result = $this->getProfileSettingsData($h, $type, $userid, true);
		
		if (!$result) {
			$sql = "INSERT INTO " . TABLE_USERMETA . " (usermeta_userid, usermeta_key, usermeta_value, usermeta_updateby) VALUES(%d, %s, %s, %d)";
			$h->db->get_row($h->db->prepare($sql, $userid, $type, serialize($data), $h->currentUser->id));
		} else {
			$sql = "UPDATE " . TABLE_USERMETA . " SET usermeta_value = %s, usermeta_updateby = %d WHERE usermeta_userid = %d AND usermeta_key = %s";
			$h->db->get_row($h->db->prepare($sql, serialize($data), $h->currentUser->id, $userid, $type));
		}
		
		return true;
	}
	
	
	/**
	 * Get the default user settings
	 *
	 * @param string $type either 'site' or 'base' (base for the originals)
	 * @return array
	 */
	public function getDefaultSettings($h, $type = 'site')
	{
                // since we already have all of the miscdata in $h->miscdata we should be able to get defaultsettings straight from there without going to db
                
                //$result = \HotaruModels\Miscdata::getUserSettings($type);
                $result = \HotaruModels2\Miscdata::getUserSettings($h, $type);

		if ($result) {
			return unserialize($result);
		} else {
			return false;
		}
	}
	
	
	/**
	 * Update the default user settings
	 *
	 * @param array $settings 
	 * @param string $type either 'site' or 'base' (base for the originals)
	 * @return array
	 */
	public function updateDefaultSettings($h, $settings, $type = 'site')
	{
		if (!$settings) { return false; } else { $settings = serialize($settings); }
		
		if ($type == 'site') {
			// update the site defaults
			$sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_value = %s, miscdata_updateby = %d WHERE miscdata_key = %s";
			$h->db->query($h->db->prepare($sql, $settings, $h->currentUser->id, 'user_settings'));
		} elseif ($type == 'base') {
			// update the base defaults
			$sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_default = %s, miscdata_updateby = %d WHERE miscdata_key = %s";
			$h->db->query($h->db->prepare($sql, $settings, $h->currentUser->id, 'user_settings'));
		}
	}


	/**
	 * Get User Roles - returns an array of role names
	 *
	 * @param string $type 'all', 'default', or 'custom'
	 * @return array|false
	 */
	public function getRoles($h, $type = 'all') 
	{
		switch ($type)
		{
			case 'default':
				return $this->getDefaultRoles();
				break;
			case 'custom':
				return $this->getCustomRoles($h);
				break;
			default:
				return $this->getUniqueRoles($h);
				break;
		}
	}


	/**
	 * Get Unique User Roles
	 *
	 * @return array|false
	 */
	public function getUniqueRoles($h) 
	{
		$unique_roles = $this->getDefaultRoles();
		
		// Add any custom roles:
		$custom_roles = $this->getCustomRoles($h);
		if ($custom_roles) {
                    foreach ($custom_roles as $role) {
                        if (!in_array($role, $unique_roles)) { 
                            array_push($unique_roles, $role);
                        }
                    }
		}
		
		if ($unique_roles) { return $unique_roles; } else { return false; }
	}


	/**
	 * Get Default User Roles
	 *
	 * @return array|false
	 */
	public function getDefaultRoles() 
	{
		return array('admin', 'supermod', 'moderator', 'member', 'undermod', 'pending', 'suspended', 'banned', 'killspammed'); 
	}


	/**
	 * Get Custom User Roles
	 *
	 * @return array|false
	 */
	public function getCustomRoles($h) 
	{
                //$result = \HotaruModels\Miscdata::getCurrentValue('custom_roles');
                $result = \HotaruModels2\Miscdata::getCurrentValue($h, 'custom_roles');
            
//		$sql = "SELECT miscdata_value FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s LIMIT 1";
//		$result = $h->db->get_var($h->db->prepare($sql, 'custom_roles'));
		if (!$result) {
                    return false;
                } 
		
		$custom_roles = unserialize($result); // result should be an array

		return $custom_roles; 
	}


	/**
	 * Add Custom User Role
	 *
	 * @param string $new_role name of new custom role
	 * @return bool
	 */
	public function addCustomRole($h, $new_role = '', $base_role = 'default') 
	{
		if (!$new_role) { return false; }

		$new_role = mb_strtolower($new_role, 'UTF-8');

		// test if this role name is reserved:
		$default_roles = $this->getDefaultRoles();
		if (in_array($new_role, $default_roles)) { return false; }

		// test if this role name is already a custom role:
		$custom_roles = $this->getCustomRoles($h);
		if ($custom_roles && (in_array($new_role, $custom_roles))) { return false; }

		// add new role to custom roles
		if (!$custom_roles) { $custom_roles = array(); }
		array_push($custom_roles, $new_role);
		
		// check custom_roles row exists in the database:
                
                //$result = \HotaruModels\Miscdata::getCurrentValue('custom_roles');
                $result = \HotaruModels2\Miscdata::getCurrentValue($h, 'custom_roles');

		// update or insert accordingly 
		if ($result)
		{
			$sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_value = %s, miscdata_updateby = %d WHERE miscdata_key = %s";
			$h->db->query($h->db->prepare($sql, serialize($custom_roles), $h->currentUser->id, 'custom_roles'));
		} 
		else 
		{
			$sql = "INSERT INTO " . TABLE_MISCDATA . " (miscdata_key, miscdata_value, miscdata_updateby) VALUES(%s, %s, %d)";
			$h->db->query($h->db->prepare($sql, 'custom_roles', serialize($custom_roles), $h->currentUser->id));
		}

		// Next, update Hotaru's base permissions
		$base_perms = $h->getDefaultPermissions('all', 'base');
		$base_perms = $this->copyRolePerms($h, $base_perms, $new_role, $base_role);
		$h->updateDefaultPermissions($base_perms, 'base');

		// Next, update Hotaru's site permissions
		$site_perms = $h->getDefaultPermissions('all', 'site');
		$site_perms = $this->copyRolePerms($h, $site_perms, $new_role, $base_role);
		$h->updateDefaultPermissions($site_perms, 'site');

		return true;
	}


	/**
	 * Remove Custom User Role
	 *
	 * @param string $remove_role name of custom role to remove
	 * @param string $move_to name of role to move existing users to
	 * @return bool
	 */
	public function removeCustomRole($h, $remove_role = '', $move_to = '') 
	{
		if (!$remove_role) { return false; }

		$remove_role = mb_strtolower($remove_role, 'UTF-8');

		// return false if this is a default role:
		$default_roles = $this->getDefaultRoles();
		if (in_array($remove_role, $default_roles)) { return false; }

		// return false if this is not a custom role:
		$custom_roles = $this->getCustomRoles($h);
		if (!$custom_roles || (!in_array($remove_role, $custom_roles))) { return false; }

		// update all users with the old role
		if ($move_to) { $this->bulkRoleChange($h, $remove_role, $move_to); }

		// remove role from custom roles
		$custom_roles = array_remove($custom_roles, $remove_role); // custom Hotaru function

		// update custom_roles record
		$sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_value = %s, miscdata_updateby = %d WHERE miscdata_key = %s";
		$h->db->query($h->db->prepare($sql, serialize($custom_roles), $h->currentUser->id, 'custom_roles'));

		// remove the role from Hotaru's base permissions
		$base_perms = $h->getDefaultPermissions('all', 'base');
		$base_perms = $this->removeRolePerms($h, $base_perms, $remove_role);
		$h->updateDefaultPermissions($base_perms, 'base', true);

		// remove the role from  Hotaru's site permissions
		$site_perms = $h->getDefaultPermissions('all', 'site');
		$site_perms = $this->removeRolePerms($h, $site_perms, $remove_role);
		$h->updateDefaultPermissions($site_perms, 'site', true);

		return true;
	}


	/**
	 * Bulk User Role Change
	 *
	 * @param string $from name of role to move from
	 * @param string $to name of role to move to
	 * @return bool
	 */
	public function bulkRoleChange($h, $from = '', $to = '') 
	{
		if (!$from || !$to) { return false; }

		// check $from and $to exist
		$unique_roles = $this->getUniqueRoles($h);
		if (!in_array($from, $unique_roles)) { return false; }
		if (!in_array($to, $unique_roles)) { return false; }

		$sql = "SELECT user_id FROM " . TABLE_USERS . " WHERE user_role = %s";
		$items = $h->db->get_results($h->db->prepare($sql, $from));
		if ($items) 
		{
			// Change role and permissions for each user being moved
			foreach ($items as $item) 
			{
				$user = new UserAuth();
				$user->getUser($h, $item->user_id);
				$user->role = $to;
				$new_perms = $user->getDefaultPermissions($h, $user->role);
				$user->setAllPermissions($new_perms);
				$user->updateUserBasic($h);
			}
		}
		
		return true;
	}


	/**
	 * Copy Usergroup Default Permissions
	 *
	 * @param array $perms
	 * @param string $new_role name of custom role to copy to
	 * @param string $base_role name of base role to copy from
	 * @return array
	 */
	public function copyRolePerms($h, $perms = array(), $new_role = '', $base_role = 'default') 
	{
		if (!$perms || !$new_role) { return array(); }

		$new_perms = array();

		foreach ($perms as $perm => $roles)
		{
			foreach ($roles as $role => $value)
			{
				if ($role == $base_role)
				{
					$new_perms[$perm][$new_role] = $value;
				}
			} 
		}

		return $new_perms;
	}


	/**
	 * Remove Usergroup Default Permissions
	 *
	 * @param array $perms
	 * @param string $new_role name of custom role to copy to
	 * @param string $base_role name of base role to copy from
	 * @return array
	 */
	public function removeRolePerms($h, $perms = array(), $delete_role = '') 
	{
		if (!$perms || !$delete_role) { return array(); }

		foreach ($perms as $perm => $roles)
		{
			foreach ($roles as $role => $value)
			{
				if ($role == $delete_role)
				{
					unset($perms[$perm][$delete_role]);
				}
			} 
		}

		return $perms;
	}
        
        
        public function getCount($h, $role = '')
        {
            //$num = \HotaruModels\User::getCount($role);
            $num = \HotaruModels2\User::getCount($h, $role);
            
            if (!$num) {
                $num = "0";
            } 
            
            return $num;
        }
        
        public function newUserCount($h)
        {                    
            $time = strtotime("-1 year", time());
            $begin = date('Y-m-d', $time);  
            $sql = "SELECT EXTRACT(YEAR_MONTH FROM user_date), count(user_id) FROM " . TABLE_USERS . " WHERE user_date >= %s GROUP BY EXTRACT(YEAR_MONTH FROM user_date) ";
            //$sql = "SELECT user_date FROM " . TABLE_USERS . " WHERE user_date >= %s";
            $query = $h->db->prepare($sql, $begin);
                        
            $users = $h->db->get_results($query, ARRAY_N);
            return $users;
        }
        
        public function newUserCountPie($h)
        {                    
            $sql = "SELECT user_role, count(user_id) FROM " . TABLE_USERS . " GROUP BY user_role ";
            $query = $h->db->prepare($sql);

            $users = $h->db->get_results($query, ARRAY_N);
            return $users;
        }
}
