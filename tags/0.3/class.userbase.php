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
 
    var $id             = 0;
    var $username       = '';
    var $role           = 'registered_user';
    var $password       = 'password';
    var $email          = '';
    var $email_valid    = 0;
    var $logged_in      = false;
    
    var $userbase_vars = array();


    /**
     * Set additonal member variables
     *
     * @param string $name
     * @param mixed $value
     */
    function __set($name, $value)
    {
        $this->userbase_vars[$name] = $value;
    }


    /**
     * Get additonal member variables
     *
     * @param string $name
     * @return mixed
     */
    function __get($name)
    {
        if (array_key_exists($name, $this->userbase_vars)) {
            return $this->userbase_vars[$name];
        }
    }
    
    
    /**
     * Add a new user
     */
    function add_user_basic()
    {
        global $db;
        
        $sql = "INSERT INTO " . table_users . " (user_username, user_role, user_date, user_password, user_email) VALUES (%s, %s, CURRENT_TIMESTAMP, %s, %s)";
        $db->query($db->prepare($sql, $this->username, $this->role, $this->password, $this->email));
    }


    /**
     * Update a user
     */
    function update_user_basic()
    {
        global $db;
        
        if ($this->id != 0) {
            $sql = "UPDATE " . table_users . " SET user_username = %s, user_role = %s, user_password = %s, user_email = %s, user_updateby = %d WHERE user_id = %d";
            $db->query($db->prepare($sql, $this->username, $this->role, $this->password, $this->email, $this->id, $this->id));
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
    function update_user_lastlogin()
    {
        global $db;
        
        if ($this->id != 0) {
            $sql = "UPDATE " . table_users . " SET user_lastlogin = CURRENT_TIMESTAMP WHERE user_id = %d";
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
    function get_user_basic($userid = 0, $username = '')
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
        $sql = "SELECT * FROM " . table_users . " WHERE " . $where;
        
        // Fetch from database
        $user_info = $db->get_row($db->prepare($sql, $param));
        if ($user_info) {
            $this->id = $user_info->user_id;
            $this->username = $user_info->user_username;
            $this->password = $user_info->user_password;
            $this->role = $user_info->user_role;
            $this->email = $user_info->user_email;
            $this->email_valid = $user_info->user_email_valid;
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
             
    function user_exists($id = 0, $username = '', $email = '')
    {
        global $db;
        
        // Error 0 - id exists
        if ($id != 0) {
            if ($db->get_var($db->prepare("SELECT * FROM " . table_users . " WHERE user_id = %d", $id))) {
                return 0; // id exists
            } 
        } 
        
        // Error 1 - username exists
        if ($username != '') {
            if ($db->get_var($db->prepare("SELECT * FROM " . table_users . " WHERE user_username = %s", $username))) {
                return 1; // username exists
            }         
        } 
        
        // Error 2 - email exists
        if ($email != '') {
            if ($db->get_var($db->prepare("SELECT * FROM " . table_users . " WHERE user_email = %s", $email))) {
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
     * Log a user in if their username and password are valid
     *
     * @param string $username
     * @param string $password
     * @return bool
     */
    function login_check($username = '', $password = '')
    {
        global $db;
        
        $password = crypt(md5($password),md5($username));
        
        $sql = "SELECT user_username, user_password FROM " . table_users . " WHERE user_username = %s AND user_password = %s";
        
        $result = $db->get_row($db->prepare($sql, $username, $password));
        
        if (isset($result)) {
            // Read the current user's basic details
            $this->get_user_basic(0, $username);
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
    function set_cookie($remember)
    {
        global $lang;

        if (!$this->username)
        { 
            echo $lang['main_userbase_cookie_error'];
            return false;
        } else {
            $strCookie=base64_encode(
                join(':', array($this->username, crypt($this->username, 22)))
            );
            
            if ($remember) { 
                // 2592000 = 60 seconds * 60 mins * 24 hours * 30 days
                $month = 2592000 + time(); 
            } else { 
                $month = 0; 
            }
            
            setcookie("hotaru_user", $this->username, $month, "/");
            setcookie("hotaru_key", $strCookie, $month, "/");
            return true;
        }
    }
        
            
    /**
     * Delete cookie and destroy session
     */
    function destroy_cookie_and_session()
    {
        // setting a cookie with a negative time expires it
        setcookie("hotaru_user", "", time()-3600, "/");
        setcookie("hotaru_key", "", time()-3600, "/");
        
        // session_destroy(); There is no session in Hotaru yet
        
        $this->logged_in = false;
    }
        
        
    /**
     * Get the username for a given user id
     *
     * @param int $id user id
     * @return string|false
     */
    function get_username($id = 0)
    {
        global $db, $user;
        
        $sql = "SELECT user_username FROM " . table_users . " WHERE user_id = %d";
        
        $username = $db->get_var($db->prepare($sql, $id));
        if ($username) { return $username; } else { return false; }
    }
    
    
    /**
     * Get the user id for a given username
     *
     * @param string $username
     * @return int|false
     */
    function get_user_id($username = '')
    {
        global $db, $user;
        
        $sql = "SELECT user_id FROM " . table_users . " WHERE user_username = %s";
        
        $user_id = $db->get_var($db->prepare($sql, $username));
        if ($user_id) { return $user_id; } else { return false; }
    }
}
 
?>