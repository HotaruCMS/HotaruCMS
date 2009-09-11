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
    protected $userName     = '';
    protected $role         = 'member';
    protected $password     = 'password';
    protected $email        = '';
    protected $emailValid   = 0;
    protected $loggedIn     = false;
    
    public $vars = array();


    /**
     * Set additonal member variables
     *
     * @param string $name
     * @param mixed $value
     */
    private function __set($name, $value)
    {
        $this->vars[$name] = $value;
    }


    /**
     * Get additonal member variables
     *
     * @param string $name
     * @return mixed
     */
    private function __get($name)
    {
        if (array_key_exists($name, $this->vars)) {
            return $this->vars[$name];
        }
    }
    
    
    /**
     * Get user id
     *
     * @return int
     */    
    public function getId()
    {
        return $this->id;
    }
    
    
    /**
     * Get user role
     *
     * @return string
     */    
    public function getRole()
    {
        return $this->role;
    }
    
    
    /**
     * Add a new user
     */
    public function addUserBasic()
    {
        global $db;
        
        $sql = "INSERT INTO " . TABLE_USERS . " (user_username, user_role, user_date, user_password, user_email) VALUES (%s, %s, CURRENT_TIMESTAMP, %s, %s)";
        $db->query($db->prepare($sql, $this->userName, $this->role, $this->password, $this->email));
    }


    /**
     * Update a user
     */
    public function updateUserBasic()
    {
        global $db;
        
        if ($this->id != 0) {
            $sql = "UPDATE " . TABLE_USERS . " SET user_username = %s, user_role = %s, user_password = %s, user_email = %s, user_updateby = %d WHERE user_id = %d";
            $db->query($db->prepare($sql, $this->userName, $this->role, $this->password, $this->email, $this->id, $this->id));
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
        global $db;
        
        if ($this->id != 0) {
            $sql = "UPDATE " . TABLE_USERS . " SET user_lastlogin = CURRENT_TIMESTAMP WHERE user_id = %d";
            $db->query($db->prepare($sql, $this->id));
            return true;
        } else {
            return false;
        }
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
        global $db;
        
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
        $sql = "SELECT * FROM " . TABLE_USERS . " WHERE " . $where;
        
        // Fetch from database
        $user_info = $db->get_row($db->prepare($sql, $param));
        
        if ($user_info) {
            $this->id = $user_info->user_id;
            $this->userName = $user_info->user_username;
            $this->password = $user_info->user_password;
            $this->role = $user_info->user_role;
            $this->email = $user_info->user_email;
            $this->emailValid = $user_info->user_email_valid;
            return $user_info;
        } else {
            return false;
        }
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
        global $db;
        
        // Error 0 - id exists
        if ($id != 0) {
            if ($db->get_var($db->prepare("SELECT * FROM " . TABLE_USERS . " WHERE user_id = %d", $id))) {
                return 0; // id exists
            } 
        } 
        
        // Error 1 - username exists
        if ($username != '') {
            if ($db->get_var($db->prepare("SELECT * FROM " . TABLE_USERS . " WHERE user_username = %s", $username))) {
                return 1; // username exists
            }         
        } 
        
        // Error 2 - email exists
        if ($email != '') {
            if ($db->get_var($db->prepare("SELECT * FROM " . TABLE_USERS . " WHERE user_email = %s", $email))) {
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
function adminExists()
{
    global $db;
    
    $sql = "SELECT user_username FROM " . TABLE_USERS . " WHERE user_role = %s";
    
    if ($admin_name = $db->get_var($db->prepare($sql, 'admin'))) {
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
function isAdmin($username)
{
    global $db;
    
    $sql = "SELECT * FROM " . TABLE_USERS . " WHERE user_username = %s AND user_role = %s";
    
    $role = $db->get_row($db->prepare($sql, $username, 'admin'));
    
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
        global $db;

        // Read the current user's basic details
        $userX = $this->getUserBasic(0, $username);
            
        $salt_length = 9;
        $password = $this->generateHash($password, substr($userX->user_password, 0, $salt_length));
        
        $sql = "SELECT user_username, user_password FROM " . TABLE_USERS . " WHERE user_username = %s AND user_password = %s";
        
        $result = $db->get_row($db->prepare($sql, $username, $password));
        
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
        global $lang;

        if (!$this->userName)
        { 
            echo $lang['main_userbase_cookie_error'];
            return false;
        } else {
            $strCookie=base64_encode(
                join(':', array($this->userName, crypt($this->userName, 22)))
            );
            
            if ($remember) { 
                // 2592000 = 60 seconds * 60 mins * 24 hours * 30 days
                $month = 2592000 + time(); 
            } else { 
                $month = 0; 
            }
            
            setcookie("hotaru_user", $this->userName, $month, "/");
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
    public function getUserName($id = 0)
    {
        global $db, $user;
        
        $sql = "SELECT user_username FROM " . TABLE_USERS . " WHERE user_id = %d";
        
        $username = $db->get_var($db->prepare($sql, $id));
        if ($username) { return $username; } else { return false; }
    }
    
    
    /**
     * Get the user id for a given username
     *
     * @param string $username
     * @return int|false
     */
    public function getUserId($username = '')
    {
        global $db, $user;
        
        $sql = "SELECT user_id FROM " . TABLE_USERS . " WHERE user_username = %s";
        
        $user_id = $db->get_var($db->prepare($sql, $username));
        if ($user_id) { return $user_id; } else { return false; }
    }
}
 
?>