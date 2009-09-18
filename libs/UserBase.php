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
     * Set user id
     *
     * @param int
     */    
    public function setId($id)
    {
        $this->id = $id;
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
     * Set user name
     *
     * @param string
     */    
    public function setName($username)
    {
        $this->userName = $username;
    }
    
    
    /**
     * Get user name
     *
     * @return string
     */    
    public function getName()
    {
        return $this->userName;
    }
    
    
    /**
     * Set user role
     *
     * @param string
     */    
    public function setRole($role)
    {
        $this->role = $role;
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
     * Set user password
     *
     * @param string
     */    
    public function setPassword($pass)
    {
        $this->password = $pass;
    }
    
    
    /**
     * Get user password
     *
     * @return string
     */    
    public function getPassword()
    {
        return $this->password;
    }
    
    
    /**
     * Set user email
     *
     * @param string
     */    
    public function setEmail($email)
    {
        $this->email = $email;
    }
    
    
    /**
     * Get user email
     *
     * @return string
     */    
    public function getEmail()
    {
        return $this->email;
    }
    
    
    /**
     * Set user emailValid
     *
     * @param int
     */    
    public function setEmailValid($ev)
    {
        $this->emailValid = $ev;
    }
    
    
    /**
     * Get user emailValid
     *
     * @return int
     */    
    public function getEmailValid()
    {
        return $this->emailValid;
    }
    
    
    /**
     * Set logged in
     *
     * @param bool
     */    
    public function setLoggedIn($bool)
    {
        $this->loggedIn = $bool;
    }
    
    
    /**
     * Get logged in
     *
     * @return bool
     */    
    public function getLoggedIn()
    {
        return $this->loggedIn;
    }
    
    
    /**
     * Add a new user
     */
    public function addUserBasic()
    {
        global $db;
        
        $sql = "INSERT INTO " . TABLE_USERS . " (user_username, user_role, user_date, user_password, user_email) VALUES (%s, %s, CURRENT_TIMESTAMP, %s, %s)";
        $db->query($db->prepare($sql, $this->getName(), $this->getRole(), $this->getPassword(), $this->getEmail()));
    }


    /**
     * Update a user
     */
    public function updateUserBasic()
    {
        global $db;
        
        if ($this->getId() != 0) {
            $sql = "UPDATE " . TABLE_USERS . " SET user_username = %s, user_role = %s, user_password = %s, user_email = %s, user_updateby = %d WHERE user_id = %d";
            $db->query($db->prepare($sql, $this->getName(), $this->getRole(), $this->getPassword(), $this->getEmail(), $this->getId(), $this->getId()));
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
        
        if ($this->getId() != 0) {
            $sql = "UPDATE " . TABLE_USERS . " SET user_lastlogin = CURRENT_TIMESTAMP WHERE user_id = %d";
            $db->query($db->prepare($sql, $this->getId()));
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
            $this->setId($user_info->user_id);
            $this->setName($user_info->user_username);
            $this->setPassword($user_info->user_password);
            $this->setRole($user_info->user_role);
            $this->setEmail($user_info->user_email);
            $this->setEmailValid($user_info->user_email_valid);
        
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
    public function adminExists()
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
    public function isAdmin($username)
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

        if (!$this->getName())
        { 
            echo $lang['main_userbase_cookie_error'];
            return false;
        } else {
            $strCookie=base64_encode(
                join(':', array($this->getName(), crypt($this->getName(), 22)))
            );
            
            if ($remember) { 
                // 2592000 = 60 seconds * 60 mins * 24 hours * 30 days
                $month = 2592000 + time(); 
            } else { 
                $month = 0; 
            }
            
            setcookie("hotaru_user", $this->getName(), $month, "/");
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
        
        $this->setLoggedIn(false);
    }
        
        
    /**
     * Get the username for a given user id
     *
     * @param int $id user id
     * @return string|false
     */
    public function getUserNameFromId($id = 0)
    {
        global $db;
        
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
    public function getUserIdFromName($username = '')
    {
        global $db;
        
        $sql = "SELECT user_id FROM " . TABLE_USERS . " WHERE user_username = %s";
        
        $userid = $db->get_var($db->prepare($sql, $username));
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
        global $db;
        
        $sql = "SELECT user_id FROM " . TABLE_USERS . " WHERE user_email = %s";
        
        $userid = $db->get_var($db->prepare($sql, $email));
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
        global $db;
        
        $sql = "SELECT user_email FROM " . TABLE_USERS . " WHERE user_id = %d";
        
        $email = $db->get_var($db->prepare($sql, $userid));
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
        global $db;
        
        $sql = "SELECT user_email FROM " . TABLE_USERS . " WHERE user_password_conf = %s";
        
        $email = $db->get_var($db->prepare($sql, $passconf));
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
        global $db, $user;
        
        if (!$email) {  return false; }
        
        if ($role) { 
            $sql = "SELECT user_email FROM " . TABLE_USERS . " WHERE user_email = %s AND user_role = %s";
            $valid_email = $db->get_var($db->prepare($sql, $email, $role));
        } else {
            $sql = "SELECT user_email FROM " . TABLE_USERS . " WHERE user_email = %s";
            $valid_email = $db->get_var($db->prepare($sql, $email));
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
        global $db, $hotaru, $cage, $lang, $plugins;
        
        // We need to explicity include the Admin language file 
        // for plugins using this function (e.g. Users).
        $hotaru->includeLanguagePack('admin');
        
        // generate the email confirmation code
        $pass_conf = md5(crypt(md5($email),md5($email)));
        
        // store the hash in the user table
        $sql = "UPDATE " . TABLE_USERS . " SET user_password_conf = %s WHERE user_id = %d";
        $db->query($db->prepare($sql, $pass_conf, $userid));
        
        $line_break = "\r\n\r\n";
        $next_line = "\r\n";
        
        if ($plugins->isActive('users')) { 
            $url = BASEURL . 'index.php?page=login&plugin=users&userid=' . $userid . '&passconf=' . $pass_conf; 
        } else { 
            $url = BASEURL . 'admin_index.php?page=admin_login&userid=' . $userid . '&passconf=' . $pass_conf; 
        }
        
        // send email
        $subject = $lang['admin_email_password_conf_subject'];
        $body = $lang['admin_email_password_conf_body_hello'] . " " . $this->getUserNameFromId($userid);
        $body .= $line_break;
        $body .= $lang['admin_email_password_conf_body_welcome'];
        $body .= $lang['admin_email_password_conf_body_click'];
        $body .= $line_break;
        $body .= $url;
        $body .= $line_break;
        $body .= $lang['admin_email_password_conf_body_no_request'];
        $body .= $line_break;
        $body .= $lang['admin_email_password_conf_body_regards'];
        $body .= $next_line;
        $body .= $lang['admin_email_password_conf_body_sign'];
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
        global $db, $hotaru, $cage, $lang, $plugins;
        
        $email = $this->getEmailFromId($userid);
        
        // check the email and confirmation code are a pair
        $pass_conf_check = md5(crypt(md5($email),md5($email)));
        if ($pass_conf_check != $passconf) {
            return false;
        }
        
        // We need to explicity include the Admin language file 
        // for plugins using this function (e.g. Users).
        $hotaru->includeLanguagePack('admin');
        
        // update the password to something random
        $temp_pass = random_string(8);
        $sql = "UPDATE " . TABLE_USERS . " SET user_password = %s WHERE user_id = %d";
        $db->query($db->prepare($sql, $this->generateHash($temp_pass), $userid));
        $line_break = "\r\n\r\n";
        $next_line = "\r\n";
        
        if ($plugins->isActive('users')) { 
            $url = BASEURL . 'index.php?page=login&plugin=users'; 
        } else { 
            $url = BASEURL . 'admin_index.php?page=admin_login'; 
        }
        
        // send email
        $subject = $lang['admin_email_new_password_subject'];
        $body = $lang['admin_email_password_conf_body_hello'] . " " . $this->getUserNameFromId($userid);
        $body .= $line_break;
        $body .= $lang['admin_email_password_conf_body_requested'];
        $body .= $line_break;
        $body .= $temp_pass;
        $body .= $line_break;
        $body .= $lang['admin_email_password_conf_body_remember'];
        $body .= $line_break;
        $body .= $lang['admin_email_password_conf_body_pass_change'];
        $body .= $line_break;
        $body .= $url; 
        $body .= $line_break;
        $body .= $lang['admin_email_password_conf_body_regards'];
        $body .= $next_line;
        $body .= $lang['admin_email_password_conf_body_sign'];
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
    public function updateAccount($userid)
    {
        global $hotaru, $cage, $lang, $current_user;
        // current_user is the person looking at the page
        // "this" will represent the person whose account is being modified
                
        // Get the details of the account to show.
        // If no account is specified, assume it's your own.
        if (!$userid) { $userid = $current_user->getId(); }
        $this->getUserBasic($userid);

        $error = 0;
        
        // We need to explicity include the Admin language file 
        // for plugins using this function (e.g. Users).
        $hotaru->includeLanguagePack('admin');
        
        // fill checks
        $checks['username_check'] = '';
        $checks['email_check'] = '';
        $checks['password_check_old'] = '';
        $checks['password_check_new'] = '';
        $checks['password_check_new2'] = '';
        
        // Updating account info (username and email address)
        if ($cage->post->testAlnumLines('update_type') == 'update_general') {
            $username_check = $cage->post->testUsername('username'); // alphanumeric, dashes and underscores okay, case insensitive
            if ($username_check) {
                $this->setName($username_check); // updates the db record
            } else {
                $hotaru->messages[$lang['admin_account_update_username_error']] = 'red';
                $error = 1;
            }
                                
            $email_check = $cage->post->testEmail('email');    
            if ($email_check) {
                $this->email = $email_check;
            } else {
                $hotaru->messages[$lang['admin_account_update_email_error']] = 'red';
                $error = 1;
            }
        }
        
        if (!isset($username_check) && !isset($email_check)) {
            $username_check = $this->getName();
            $email_check = $this->getEmail();
            // do nothing
        } elseif ($error == 0) {
            $result = $this->userExists(0, $username_check, $email_check);
            if ($result != 4) { // 4 is returned when the user does not exist in the database
                //success
                $this->updateUserBasic();
                // only update the cookie if it's your own account:
                if ($current_user->getId() != $this->getId()) { $this->setCookie(0); }
                $hotaru->messages[$lang['admin_account_update_success']] = 'green';
            } else {
                //fail
                $hotaru->messages[$lang["admin_account_update_unexpected_error"]] = 'red';
            }
        } else {
            // error must = 1 so fall through and display the form again
        }
        
        //update checks
        $this->updatePassword();
        $checks['username_check'] = $username_check;
        $checks['email_check'] = $email_check;
        return $checks;
    }
    
    
     /**
     * Enable a user to change their password
     *
     * @return bool
     */
    public function updatePassword()
    {
        global $hotaru, $cage, $lang, $current_user; 
        // current_user is the person looking at the page
        
        // we don't want to edit the password if this isn't our own account.
        if ($current_user->getId() != $this->getId()) { return false; }
        
        $error = 0;
        
        // Updating password
        if ($cage->post->testAlnumLines('update_type') == 'update_password') {
            $password_check_old = $cage->post->testPassword('password_old');    
            
            if ($current_user->loginCheck($current_user->getName(), $password_check_old)) {
                // safe, the old password matches the password for this user.
            } else {
                $hotaru->messages[$lang['admin_account_update_password_error_old']] = 'red';
                $error = 1;
            }
        
            $password_check_new = $cage->post->testPassword('password_new');    
            if ($password_check_new) {
                $password_check_new2 = $cage->post->testPassword('password_new2');    
                if ($password_check_new2) { 
                    if ($password_check_new == $password_check_new2) {
                        // safe, the two new password fields match
                    } else {
                        $hotaru->messages[$lang['admin_account_update_password_error_match']] = 'red';
                        $error = 1;
                    }
                } else {
                    $hotaru->messages[$lang['admin_account_updatee_password_error_new']] = 'red';
                    $error = 1;
                }
            } else {
                $hotaru->messages[$lang['admin_account_update_password_error_not_provided']] = 'red';
                $error = 1;
            }
                        
        }

                
        if (!isset($password_check_old) && !isset($password_check_new) && !isset($password_check_new2)) {
            $password_check_old = "";
            $password_check_new = "";
            $password_check_new2 = "";
            // do nothing
        } elseif ($error == 0) {
            $result = $current_user->userExists(0, $current_user->userName, $current_user->email);
            if ($result != 4) { // 4 is returned when the user does not exist in the database
                //success
                //$current_user->setPassword($current_user->generateHash($password_check_new));
                //$current_user->updateUserBasic();
                //$current_user->setCookie(0);
                $hotaru->messages[$lang['admin_account_update_password_success']] = 'green';
            } else {
                //fail
                $hotaru->messages[$lang["admin_account_update_unexpected_error"]] = 'red';
            }
        } else {
            // error must = 1 so fall through and display the form again
        }

    }
}
 
?>