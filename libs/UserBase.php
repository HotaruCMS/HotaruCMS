<?php
/**
 * Basic user functions for logging in , registering, etc.
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
class UserBase {
 
    protected $id           = 0;
    protected $name         = '';
    protected $role         = 'member';
    protected $password     = 'password';
    protected $email        = '';
    protected $emailValid   = 0;
    protected $loggedIn     = false;
    protected $perms        = array();    // permissions
    protected $ip           = 0;
    
    public $vars            = array();  // multi-purpose, used by plugins
    public $db;                         // database object
    public $cage;                       // Inspekt object
    public $hotaru;                     // Hotaru object
    public $lang            = array();  // stores language file content
    public $plugins;                    // PluginFunctions object
    
    /**
     * Build a userbase object containing $db and $cage
     */
    public function __construct($hotaru)
    {
        $this->hotaru           = $hotaru;
        $this->db               = $hotaru->db;
        $this->cage             = $hotaru->cage;
        $this->lang             = &$hotaru->lang;   // reference to main lang array
        $this->plugins          = $hotaru->plugins;
    }
    

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
    public function __get($var)
    {
        return $this->$var;
    }


    /* *************************************************************
     *              UNIQUE ACCESS MODIFIERS
     * ********************************************************** */


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
    
    
    /* *************************************************************
     *              REGULAR METHODS
     * ********************************************************** */
    
    
    /**
     * Add a new user
     */
    public function addUserBasic()
    {
        // get default permissions
        $permissions = $this->getDefaultPermissions($this->role);
        unset($permissions['options']);  // don't need this for individual users
        $permissions = serialize($permissions);

        // get user ip
        $userip = $this->cage->server->testIp('REMOTE_ADDR');
        
        // add user to the database
        $sql = "INSERT INTO " . TABLE_USERS . " (user_username, user_role, user_date, user_password, user_email, user_permissions, user_ip) VALUES (%s, %s, CURRENT_TIMESTAMP, %s, %s, %s, %s)";
        $this->db->query($this->db->prepare($sql, $this->name, $this->role, $this->password, $this->email, $permissions, $userip));
    }


    /**
     * Update a user
     */
    public function updateUserBasic($userid = 0)
    {
        //determine if the current user is the same as this object's user
        if($userid != $this->id) {
            $updatedby = $userid;
        } else {
            $updatedby = $this->id;
        }
        
        if ($this->id != 0) {
            $sql = "UPDATE " . TABLE_USERS . " SET user_username = %s, user_role = %s, user_password = %s, user_email = %s, user_permissions = %s, user_ip = %s, user_updateby = %d WHERE user_id = %d";
            $this->db->query($this->db->prepare($sql, $this->name, $this->role, $this->password, $this->email, serialize($this->getAllPermissions()), $this->ip, $updatedby, $this->id));
            return true;
        } else {
            return false;
        }
    }
    
    
    /**
     * Update last login
     *
     * @return bool
     */
    public function updateUserLastLogin()
    {
        if ($this->id != 0) {
            $sql = "UPDATE " . TABLE_USERS . " SET user_lastlogin = CURRENT_TIMESTAMP WHERE user_id = %d";
            $this->db->query($this->db->prepare($sql, $this->id));
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Give logged out user default permissions
     */    
    public function setLoggedOutUser()
    {
            $default_perms = $this->getDefaultPermissions();
            unset($default_perms['options']);  // don't need this for individual users
            $this->setAllPermissions($default_perms);
    }
    
    
    /**
     * Get basic user details
     *
     * @param int $userid 
     * @param string $username
     * @return array|false
     *
     * Note: Needs either userid or username, not both
     */    
    public function getUserBasic($userid = 0, $username = '')
    {
        // Prepare SQL
        if ($userid != 0){              // use userid
            $where = "user_id = %d";
            $param = $userid;
        } elseif ($username != '') {    // use username
            $where = "user_username = %s";
            $param = $username;
        } else {
            return false;
        }
        
        // Build SQL
        $query = "SELECT * FROM " . TABLE_USERS . " WHERE " . $where;
        $sql = $this->db->prepare($query, $param);
        
        if (!isset($this->hotaru->vars['tempUserCache'])) { $this->hotaru->vars['tempUserCache'] = array(); }

        // If this query has already been read once this page load, we should have it in memory...
        if (array_key_exists($sql, $this->hotaru->vars['tempUserCache'])) {
            // Fetch from memory
            $user_info = $this->hotaru->vars['tempUserCache'][$sql];
        } else {
            // Fetch from database
            $user_info = $this->db->get_row($sql);
            $this->hotaru->vars['tempUserCache'][$sql] = $user_info;
        }

        if ($user_info) {
            $this->id = $user_info->user_id;
            $this->name = $user_info->user_username;
            $this->password = $user_info->user_password;
            $this->role = $user_info->user_role;
            $this->email = $user_info->user_email;
            $this->emailValid = $user_info->user_email_valid;
            $this->ip = $user_info->user_ip;
            
            // If a new plugin is installed, we need a way of adding any new default permissions
            // that plugin provides. So, we get all defaults, then overwrite with existing perms.
            
            // get default permissions for the site
            $default_perms = $this->getDefaultPermissions($this->role);
            
            // get existing permissions for the user
            $existing_perms = unserialize($user_info->user_permissions);
            
            // merge permissions
            $updated_perms = array_merge($default_perms, $existing_perms);
            
            $this->setAllPermissions($updated_perms);
            $user_info->user_permissions = serialize($updated_perms);   // update user_info
            
            return $user_info;
        } else {
            return false;
        }
    }


    /**
     * Get UserMeta for a specified user
     *
     * @param int $userid 
     * @return array|false
     *
     * Notes: Returns 4 if a user exists, otherwise 0-3 for errors
     */
    public function getUserMeta($userid = 0)
    {
        if ($userid != 0) { $userid = $this->current_user->id; }
        
        $sql = "SELECT * FROM " . TABLE_USERMETA . " WHERE usermeta_userid = %d";
        $result = $this->db->get_var($this->db->prepare($sql, $userid));
        
        if ($result) { return $result; } else { return false; }
    }
    
    
    /**
     * Check if a user exists
     *
     * @param int $userid 
     * @param string $username
     * @return int
     *
     * Notes: Returns 4 if a user exists, otherwise 0-3 for errors
     */
    public function userExists($id = 0, $username = '', $email = '')
    {
        // Error 0 - id exists
        if ($id != 0) {
            if ($this->db->get_var($this->db->prepare("SELECT * FROM " . TABLE_USERS . " WHERE user_id = %d", $id))) {
                return 0; // id exists
            } 
        } 
        
        // Error 1 - username exists
        if ($username != '') {
            if ($this->db->get_var($this->db->prepare("SELECT * FROM " . TABLE_USERS . " WHERE user_username = %s", $username))) {
                return 1; // username exists
            }         
        } 
        
        // Error 2 - email exists
        if ($email != '') {
            if ($this->db->get_var($this->db->prepare("SELECT * FROM " . TABLE_USERS . " WHERE user_email = %s", $email))) {
                return 2; // email exists
            }         
        } 
        
        // Error 3 - no arguments provided
        if (($id == 0) && ($username == '') && ($email == '')) {
                return 3; // no arguments provided
        } 
        
        // Success
        return 4; // user exists
    }


    /**
     * Check if an admin exists
     *
     * @return string|false
     */
    public function adminExists()
    {
        $sql = "SELECT user_username FROM " . TABLE_USERS . " WHERE user_role = %s";
        
        if ($admin_name = $this->db->get_var($this->db->prepare($sql, 'admin'))) {
            return $admin_name; // admin exists
        } else {
            return false;
        }
    }
    
    
     /**
     * Checks if the user has an 'admin' role
     *
     * @return bool
     */
    public function isAdmin($username)
    {
        $sql = "SELECT * FROM " . TABLE_USERS . " WHERE user_username = %s AND user_role = %s";
        
        $role = $this->db->get_row($this->db->prepare($sql, $username, 'admin'));
        
        if ($role) { return true; } else { return false; }
    }


    /**
     * Log a user in if their username and password are valid
     *
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function loginCheck($username = '', $password = '')
    {
        // Read the current user's basic details
        $userX = $this->getUserBasic(0, $username);
        
        // destroy the cookie for the following usergroups:
        $no_cookie = array('killspammed', 'banned', 'suspended');
        if (in_array($userX->user_role, $no_cookie)) {
            $this->destroyCookieAndSession();
            return false;
        }
        
        $salt_length = 9;
        $result = '';
        
        // Allow plugin to bypass the password check with their own methods, e.g. RPX
        $plugin_result = $this->plugins->pluginHook('userbase_logincheck', true, '', array($username, $password));
        
        if (!isset($plugin_result))
        {
            // nothing was returned from the plugins, not even "false", so confirm the username and password match:
            $password = $this->generateHash($password, substr($userX->user_password, 0, $salt_length));
            $sql = "SELECT user_username, user_password FROM " . TABLE_USERS . " WHERE user_username = %s AND user_password = %s";
            $result = $this->db->get_row($this->db->prepare($sql, $username, $password));
        } 
        elseif ($plugin_result)
        {
            // a non-false result was returned from the plugin(s)
            // let's hope the plugin did its own authentication because we've skipped the usual username/passowrd check!
            $result = true;
        } 
        else 
        {
            // a false result was returned from the plugin(s), so the user won't be able to login.
            $result = false;
        }
        
        if (isset($result)) { return true; } else { return false; }
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
     * Set a 30-day cookie
     *
     * @param string $remember checkbox with value "checked" or empty
     * @return bool
     */
    public function setCookie($remember)
    {
        if (!$this->name)
        { 
            echo $this->lang['main_userbase_cookie_error'];
            return false;
        } else {
            $strCookie=base64_encode(
                join(':', array($this->name, crypt($this->name, 22)))
            );
            
            if ($remember) { 
                // 2592000 = 60 seconds * 60 mins * 24 hours * 30 days
                $month = 2592000 + time(); 
            } else { 
                $month = 0; 
            }
            
            setcookie("hotaru_user", $this->name, $month, "/");
            setcookie("hotaru_key", $strCookie, $month, "/");
            return true;
        }
    }
        
            
    /**
     * Delete cookie and destroy session
     */
    public function destroyCookieAndSession()
    {
        // setting a cookie with a negative time expires it
        setcookie("hotaru_user", "", time()-3600, "/");
        setcookie("hotaru_key", "", time()-3600, "/");
        
        // session_destroy(); There is no session in Hotaru yet
        
        $this->loggedIn = false;
    }
        
        
    /**
     * Get the username for a given user id
     *
     * @param int $id user id
     * @return string|false
     */
    public function getUserNameFromId($id = 0)
    {
        $sql = "SELECT user_username FROM " . TABLE_USERS . " WHERE user_id = %d";
        
        $username = $this->db->get_var($this->db->prepare($sql, $id));
        if ($username) { return $username; } else { return false; }
    }
    
    
    /**
     * Get the user id for a given username
     *
     * @param string $username
     * @return int|false
     */
    public function getUserIdFromName($username = '')
    {
        $sql = "SELECT user_id FROM " . TABLE_USERS . " WHERE user_username = %s";
        
        $userid = $this->db->get_var($this->db->prepare($sql, $username));
        if ($userid) { return $userid; } else { return false; }
    }
    
    
    /**
     * Get the user id from email
     *
     * @param string $email
     * @return string|false
     */
    public function getUserIdFromEmail($email = '')
    {
        $sql = "SELECT user_id FROM " . TABLE_USERS . " WHERE user_email = %s";
        
        $userid = $this->db->get_var($this->db->prepare($sql, $email));
        if ($userid) { return $userid; } else { return false; }
    }
    
    
    /**
     * Get the email from password conf
     *
     * @param int $userid
     * @return string|false
     */
    public function getEmailFromId($userid = 0)
    {
        $sql = "SELECT user_email FROM " . TABLE_USERS . " WHERE user_id = %d";
        
        $email = $this->db->get_var($this->db->prepare($sql, $userid));
        if ($email) { return $email; } else { return false; }
    }
    
    
    /**
     * Get the email from password conf
     *
     * @param string $passconf
     * @return string|false
     */
    public function getEmailFromPassConf($passconf = '')
    {
        $sql = "SELECT user_email FROM " . TABLE_USERS . " WHERE user_password_conf = %s";
        
        $email = $this->db->get_var($this->db->prepare($sql, $passconf));
        if ($email) { return $email; } else { return false; }
    }
    
    /**
     * Get the username for a given user id
     *
     * @param int $id user id
     * @return string|false
     */
    public function validEmail($email = '', $role = '')
    {
        if (!$email) {  return false; }
        
        if ($role) { 
            $sql = "SELECT user_email FROM " . TABLE_USERS . " WHERE user_email = %s AND user_role = %s";
            $valid_email = $this->db->get_var($this->db->prepare($sql, $email, $role));
        } else {
            $sql = "SELECT user_email FROM " . TABLE_USERS . " WHERE user_email = %s";
            $valid_email = $this->db->get_var($this->db->prepare($sql, $email));
        }

        if ($valid_email) { return $valid_email; } else { return false; }
    }
    
    
     /**
     * Send a confirmation code to a user who has forgotten his/her password
     *
     * @param string $email - already validated above
     */
    public function sendPasswordConf($userid, $email)
    {
        // We need to explicity include the Admin language file 
        // for plugins using this function (e.g. Users).
        $this->hotaru->includeLanguagePack('admin');
        
        // generate the email confirmation code
        $pass_conf = md5(crypt(md5($email),md5($email)));
        
        // store the hash in the user table
        $sql = "UPDATE " . TABLE_USERS . " SET user_password_conf = %s WHERE user_id = %d";
        $this->db->query($this->db->prepare($sql, $pass_conf, $userid));
        
        $line_break = "\r\n\r\n";
        $next_line = "\r\n";

        if ($this->plugins->isActive('users')) { 
            $url = BASEURL . 'index.php?page=login&plugin=users&userid=' . $userid . '&passconf=' . $pass_conf; 
        } else { 
            $url = BASEURL . 'admin_index.php?page=admin_login&userid=' . $userid . '&passconf=' . $pass_conf; 
        }
        
        // send email
        $subject = $this->lang['admin_email_password_conf_subject'];
        $body = $this->lang['admin_email_password_conf_body_hello'] . " " . $this->getUserNameFromId($userid);
        $body .= $line_break;
        $body .= $this->lang['admin_email_password_conf_body_welcome'];
        $body .= $this->lang['admin_email_password_conf_body_click'];
        $body .= $line_break;
        $body .= $url;
        $body .= $line_break;
        $body .= $this->lang['admin_email_password_conf_body_no_request'];
        $body .= $line_break;
        $body .= $this->lang['admin_email_password_conf_body_regards'];
        $body .= $next_line;
        $body .= $this->lang['admin_email_password_conf_body_sign'];
        $to = $email;
        $headers = "From: " . SITE_EMAIL . "\r\nReply-To: " . SITE_EMAIL . "\r\nX-Priority: 3\r\n";
    
        mail($to, $subject, $body, $headers);    
        
        return true;
    }
    
    
     /**
     * Reset the user's password to soemthing random and email it.
     *
     * @param string $passconf - confirmation code clicked in email
     */
    public function newRandomPassword($userid, $passconf)
    {
        $email = $this->getEmailFromId($userid);
        
        // check the email and confirmation code are a pair
        $pass_conf_check = md5(crypt(md5($email),md5($email)));
        if ($pass_conf_check != $passconf) {
            return false;
        }
        
        // We need to explicity include the Admin language file 
        // for plugins using this function (e.g. Users).
        $this->hotaru->includeLanguagePack('admin');
        
        // update the password to something random
        $temp_pass = random_string(8);
        $sql = "UPDATE " . TABLE_USERS . " SET user_password = %s WHERE user_id = %d";
        $this->db->query($this->db->prepare($sql, $this->generateHash($temp_pass), $userid));
        $line_break = "\r\n\r\n";
        $next_line = "\r\n";
        
        if ($this->plugins->isActive('users')) { 
            $url = BASEURL . 'index.php?page=login&plugin=users'; 
        } else { 
            $url = BASEURL . 'admin_index.php?page=admin_login'; 
        }
        
        // send email
        $subject = $this->lang['admin_email_new_password_subject'];
        $body = $this->lang['admin_email_password_conf_body_hello'] . " " . $this->getUserNameFromId($userid);
        $body .= $line_break;
        $body .= $this->lang['admin_email_password_conf_body_requested'];
        $body .= $line_break;
        $body .= $temp_pass;
        $body .= $line_break;
        $body .= $this->lang['admin_email_password_conf_body_remember'];
        $body .= $line_break;
        $body .= $this->lang['admin_email_password_conf_body_pass_change'];
        $body .= $line_break;
        $body .= $url; 
        $body .= $line_break;
        $body .= $this->lang['admin_email_password_conf_body_regards'];
        $body .= $next_line;
        $body .= $this->lang['admin_email_password_conf_body_sign'];
        $to = $email;
        $headers = "From: " . SITE_EMAIL . "\r\nReply-To: " . SITE_EMAIL . "\r\nX-Priority: 3\r\n";
    
        mail($to, $subject, $body, $headers);    
        
        return true;
    }
    
    
     /**
     * Change username or email
     *
     * @param int $userid
     * @return bool
     */
    public function updateAccount($userid = 0)
    {
        // $this is the person looking at the page, i.e. the viewer
        // $viewee is the person whose account is being modified
        // if looking at your own account then $this = $viewee.
        
        $viewee = new UserBase($this->hotaru);
        
        // Get the details of the account to show.
        // If no account is specified, assume it's your own.
        
        if (!$userid) {
            $userid = $this->id; 
        }
        
        $viewee->getUserBasic($userid);

        $error = 0;
        
        // We need to explicity include the Admin language file 
        // for plugins using this function (e.g. Users).
        $this->hotaru->includeLanguagePack('admin');
        
        // fill checks
        $checks['username_check'] = '';
        $checks['email_check'] = '';
        $checks['role_check'] = '';
        $checks['password_check_old'] = '';
        $checks['password_check_new'] = '';
        $checks['password_check_new2'] = '';
        
        // Updating account info (username and email address)
        if ($this->cage->post->testAlnumLines('update_type') == 'update_general') {
            $username_check = $this->cage->post->testUsername('username'); // alphanumeric, dashes and underscores okay, case insensitive
            if ($username_check) {
                $viewee->name = $username_check; // updates the db record
            } else {
                $this->hotaru->messages[$this->lang['admin_account_update_username_error']] = 'red';
                $error = 1;
            }
                                
            $email_check = $this->cage->post->testEmail('email');    
            if ($email_check) {
                $viewee->email = $email_check;
            } else {
                $this->hotaru->messages[$this->lang['admin_account_update_email_error']] = 'red';
                $error = 1;
            }
            
            $role_check = $this->cage->post->testAlnumLines('user_role'); // from Users plugin account page
            // compare with current role and update if different
            if ($role_check && ($role_check != $viewee->role)) {
                $viewee->role = $role_check;
                $new_perms = $viewee->getDefaultPermissions($role_check);
                $viewee->setAllPermissions($new_perms);
                $viewee->updatePermissions();
                if ($role_check == 'killspammed' || $role_check == 'deleted') {
                    $viewee->deleteComments(); // includes child comments from *other* users
                    $viewee->deletePosts(); // includes tags and votes for self-submitted posts
                    
                    $this->plugins->pluginHook('userbase_killspam', true, '', array('target_user' => $viewee->id));
                    
                    if ($role_check == 'deleted') { 
                        $viewee->deleteUser(); 
                        $checks['username_check'] = 'deleted';
                        $this->hotaru->message = $this->hotaru->lang["users_account_deleted"];
                        $this->hotaru->messageType = 'red';
                        return $checks; // This will then show a red "deleted" notice
                    }
                }
            }
        }
        
        if (!isset($username_check) && !isset($email_check)) {
            $username_check = $viewee->name;
            $email_check = $viewee->email;
            $role_check = $viewee->role;
            // do nothing
        } elseif ($error == 0) {
            $result = $viewee->userExists(0, $username_check, $email_check);
            if ($result != 4) { // 4 is returned when the user does not exist in the database
                //success
                $viewee->updateUserBasic($userid);
                // only update the cookie if it's your own account:
                if ($userid == $this->id) { 
                    $this->setCookie(true); 
                }
                $this->hotaru->messages[$this->lang['admin_account_update_success']] = 'green';
            } else {
                //fail
                $this->hotaru->messages[$this->lang["admin_account_update_unexpected_error"]] = 'red';
            }
        } else {
            // error must = 1 so fall through and display the form again
        }
        
        //update checks
        $this->updatePassword($userid);
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
    public function updatePassword($userid)
    {
        // current_user is the person looking at the page
        
        // we don't want to edit the password if this isn't our own account.
        if ($userid != $this->id) { return false; }
        
        $error = 0;
        
        // Updating password
        if ($this->cage->post->testAlnumLines('update_type') == 'update_password') {
            $password_check_old = $this->cage->post->testPassword('password_old');    
            
            if ($this->loginCheck($this->name, $password_check_old)) {
                // safe, the old password matches the password for this user.
            } else {
                $this->hotaru->messages[$this->lang['admin_account_update_password_error_old']] = 'red';
                $error = 1;
            }
        
            $password_check_new = $this->cage->post->testPassword('password_new');    
            if ($password_check_new) {
                $password_check_new2 = $this->cage->post->testPassword('password_new2');    
                if ($password_check_new2) { 
                    if ($password_check_new == $password_check_new2) {
                        // safe, the two new password fields match
                    } else {
                        $this->hotaru->messages[$this->lang['admin_account_update_password_error_match']] = 'red';
                        $error = 1;
                    }
                } else {
                    $this->hotaru->messages[$this->lang['admin_account_updatee_password_error_new']] = 'red';
                    $error = 1;
                }
            } else {
                $this->hotaru->messages[$this->lang['admin_account_update_password_error_not_provided']] = 'red';
                $error = 1;
            }
                        
        }

                
        if (!isset($password_check_old) && !isset($password_check_new) && !isset($password_check_new2)) {
            $password_check_old = "";
            $password_check_new = "";
            $password_check_new2 = "";
            // do nothing
        } elseif ($error == 0) {
            $result = $this->userExists(0, $this->name, $this->email);
            if ($result != 4) { // 4 is returned when the user does not exist in the database
                //success
                $this->password = $this->generateHash($password_check_new);
                $this->updateUserBasic();
                $this->setCookie(0);
                $this->hotaru->messages[$this->lang['admin_account_update_password_success']] = 'green';
            } else {
                //fail
                $this->hotaru->messages[$this->lang["admin_account_update_unexpected_error"]] = 'red';
            }
        } else {
            // error must = 1 so fall through and display the form again
        }

    }
    
    
    /**
     * Default permissions
     *
     * @param string $role or 'all'
     * @param string $field 'site' for site defaults and 'base' for base defaults
     * @param book $options_only returns just the options if true
     * @return array $perms
     */
    public function getDefaultPermissions($role = '', $defaults = 'site', $options_only = false) 
    {
        $perms = array(); // to be filled with default permissions for this user
        
        if ($defaults == 'site') { 
            $field = 'miscdata_value';  // get site permissions
        } else {
            $field = 'miscdata_default'; // get base permissions (i.e. the originals)
        }
        
        // get default permissions from the database:
        $query = "SELECT " . $field . " FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s";
        $sql = $this->db->prepare($query, 'permissions');
        
        // Create temp cache array
        if (!isset($this->hotaru->vars['tempPermissionsCache'])) { $this->hotaru->vars['tempPermissionsCache'] = array(); }
        
        // If this query has already been read once this page load, we should have it in memory...
        if (array_key_exists($sql, $this->hotaru->vars['tempPermissionsCache'])) {
            // Fetch from memory
            $db_perms = $this->hotaru->vars['tempPermissionsCache'][$sql];
        } else {
            // Fetch from database
            $db_perms = $this->db->get_var($sql);
            $this->hotaru->vars['tempPermissionsCache'][$sql] = $db_perms;
        }

        $permissions = unserialize($db_perms);
        
        if (!$permissions) { return false; }
        
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
     */
    public function updateDefaultPermissions($new_perms = array(), $defaults = 'both') 
    {
        if (!$new_perms) { return false; }
        
        // get and merge permissions
        if ($defaults == 'site')
        {
            $site_perms = $this->getDefaultPermissions('all', 'site'); //get site defaults
            $site_perms = array_merge_recursive($site_perms, $new_perms); // merge
            $sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_value = %s WHERE miscdata_key = %s";
            $this->db->query($this->db->prepare($sql, serialize($site_perms), 'permissions'));
        } 
        elseif ($defaults == 'base')
        {
            $base_perms = $this->getDefaultPermissions('all', 'base'); // get base defaults
            $base_perms = array_merge_recursive($site_perms, $new_perms); // merge
            $sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_default = %s WHERE miscdata_key = %s";
            $this->db->query($this->db->prepare($sql, serialize($base_perms), 'permissions'));
        }
        else 
        {
            $site_perms = $this->getDefaultPermissions('all', 'site'); //get site defaults
            $site_perms = array_merge_recursive($site_perms, $new_perms); // merge
            $base_perms = $this->getDefaultPermissions('all', 'base'); // get base defaults
            $base_perms = array_merge_recursive($site_perms, $new_perms); // merge
            $sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_value = %s, miscdata_default = %s WHERE miscdata_key = %s";
            $this->db->query($this->db->prepare($sql, serialize($site_perms), serialize($base_perms), 'permissions'));
        }
    }

    
    /**
     * update permissions in the database
     *
     * @param int $userid
     */
    public function updatePermissions()
    {
        $sql = "UPDATE " . TABLE_USERS . " SET user_permissions = %s WHERE user_id = %d";
        $this->db->get_var($this->db->prepare($sql, serialize($this->getAllPermissions()), $this->id));
    }
    
    
    /**
     * Get Unique Roles
     *
     * @return array|false
     */
    public function getUniqueRoles() 
    {
        /* This function pulls all the different user roles from the database, 
        or adds some defaults if not present.*/

        $unique_roles = array();

        // Some essentials:
        array_push($unique_roles, 'admin');
        array_push($unique_roles, 'supermod');
        array_push($unique_roles, 'moderator');
        array_push($unique_roles, 'member');
        array_push($unique_roles, 'pending');
        array_push($unique_roles, 'undermod');
        array_push($unique_roles, 'suspended');
        array_push($unique_roles, 'banned');
        array_push($unique_roles, 'killspammed');
        
        // Add any other roles already in use:
        $sql = "SELECT DISTINCT user_role FROM " . TABLE_USERS;
        $roles = $this->db->get_results($this->db->prepare($sql));
        if ($roles) {
            foreach ($roles as $role) {
                if (!in_array($role->user_role, $unique_roles)) { 
                    array_push($unique_roles, $role->user_role);
                }
            }
        }
        
        if ($unique_roles) { return $unique_roles; } else { return false; }
    }
    
    
    /**
     * Physically delete this user
     */
    public function deleteUser() 
    {
        $this->plugins->pluginHook('userbase_delete_user', true, '', array('user_id'=>$this->id));
        
        $sql = "DELETE FROM " . TABLE_USERS . " WHERE user_id = %d";
        $this->db->query($this->db->prepare($sql, $this->id));
        
        $sql = "DELETE FROM " . TABLE_USERMETA . " WHERE usermeta_userid = %d";
        $this->db->query($this->db->prepare($sql, $this->id));
    }
    

    /**
     * Physically delete posts by this user
     *
     * @return bool
     */
    public function deletePosts() 
    {
        $exists = $this->db->table_exists('posts');
        if (!$exists) { return false; }
        
        $sql = "SELECT post_id FROM " . DB_PREFIX . "posts WHERE post_author = %d";
        $results = $this->db->get_results($this->db->prepare($sql, $this->id));
        
        if (!file_exists(PLUGINS . 'submit/libs/Post.php')) { return false; }

        include_once(PLUGINS . 'submit/libs/Post.php');
        
        if ($results) {
            foreach ($results as $r) {
                $p = new Post($this->hotaru);
                $p->id = $r->post_id;
                $this->hotaru->post->id = $p->id;   // used by other plugins in "post_delete_post" function/hook
                $p->deletePost();
            }
        }
        
        return true;
    }
    
    
    /**
     * Physically delete comments by this user (and responses)
     *
     * @return bool
     */
    public function deleteComments() 
    {
        $exists = $this->db->table_exists('comments');
        if (!$exists) { return false; }
        
        $sql = "SELECT comment_id FROM " . DB_PREFIX . "comments WHERE comment_user_id = %d";
        $results = $this->db->get_results($this->db->prepare($sql, $this->id));
        
        if (!file_exists(PLUGINS . 'comments/libs/Comment.php')) { return false; }

        include_once(PLUGINS . 'comments/libs/Comment.php');

        if ($results) {
            foreach ($results as $r) {
                $c = new Comment($this->hotaru);
                $c->id = $r->comment_id;
                $this->hotaru->comment->id = $c->id;   // used by other plugins in "comment_delete_comment" function/hook
                $c->deleteComment();    // delete parent comment
                $c->deleteCommentTree($c->id);  // delete all children of that comment regardless of user
            }
        }
        
        return true;
    }
    
}
 
?>