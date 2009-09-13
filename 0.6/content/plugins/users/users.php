<?php
/**
 * name: Users
 * description: Manages users within Hotaru.
 * version: 0.3
 * folder: users
 * class: Users
 * hooks: hotaru_header, install_plugin, admin_sidebar_plugin_settings, admin_plugin_settings, navigation_users, theme_index_replace, theme_index_main, submit_list_filter
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
 
return false; die(); // die on direct access.

class Users extends PluginFunctions
{
    /**
     * Create a "usermeta" table when on installation, if it doesn't already exist
     */
    public function install_plugin()
    {
        global $db, $lang;
        
        // include language file
        $this->includeLanguage();
        
        // Create a new empty table called "usermeta"
        $exists = $db->table_exists('usermeta');
        if (!$exists) {
            //echo "table doesn't exist. Stopping before creation."; exit;
            $sql = "CREATE TABLE `" . DB_PREFIX . "usermeta` (
              `usermeta_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `usermeta_userid` int(20) NOT NULL DEFAULT 0,
              `usermeta_key` varchar(255) NULL,
              `usermeta_value` text NULL,
              `usermeta_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
              `usermeta_updateby` int(20) NOT NULL DEFAULT 0, 
              INDEX  (`usermeta_userid`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='User Meta';";
            $db->query($sql); 
        }
        
        $this->updateSetting('users_recaptcha_enabled', '');    
        $this->updateSetting('users_recaptcha_pubkey', '');    
        $this->updateSetting('users_recaptcha_privkey', '');
        $this->updateSetting('users_emailconf_enabled', '');
        
        // Include language file. Also included in hotaru_header, but needed here  
        // to prevent errors immediately after installation.
        $this->includeLanguage();    
        
    }
    
    
    /**
     * Define a global "TABLE_TAGSusermeta" constant for referring to the db table
     */
    public function hotaru_header() {
        global $hotaru, $lang, $cage, $userbase;
    
        if (!defined('TABLE_USERMETA')) { define("TABLE_USERMETA", DB_PREFIX . 'usermeta'); }
        
        // include language file
        $this->includeLanguage();
        
        if ($username = $cage->get->testUsername('user')) {
            $hotaru->setTitle($username);
        }
        
        // Create a new global object called "userbase" (in addition to the default "current_user").
        $userbase = new Userbase();
        $vars['userbase'] = $userbase; 
        return $vars; 
    }
    
    
    /**
     * Put a link to the settings page in the Admin sidebar under Plugin Settings
     */
    public function admin_sidebar_plugin_settings()
    {
        echo "<li><a href='" . url(array('page'=>'plugin_settings', 'plugin'=>'users'), 'admin') . "'>Users</a></li>";
    }
    
    
     /**
     * Call the function for displaying Admin settings
     *
     * @return true
     */
    public function admin_plugin_settings()
    {
        require_once(PLUGINS . 'users/users_settings.php');
        $usersSettings = new UsersSettings($this->folder);
        $usersSettings->settings();
        return true;
    }
    
    
    /**
     * Add links to the end of the navigation bar
     */
    public function navigation_users()
    {
        global $current_user, $lang, $hotaru;
        
        if ($current_user->loggedIn) {
            if ($hotaru->getTitle() == 'profile') { $status = "id='navigation_active'"; } else { $status = ""; }
            echo "<li><a  " . $status . " href='" . url(array('page'=>'profile')) . "'>" . $lang["users_profile"] . "</a></li>\n";
            
            if ($hotaru->getTitle() == 'logout') { $status = "id='navigation_active'"; } else { $status = ""; }
            echo "<li><a  " . $status . " href='" . url(array('page'=>'logout')) . "'>" . $lang["users_logout"] . "</a></li>\n";
            
            if ($current_user->getRole() == 'admin') {
                
                if ($hotaru->getTitle() == 'admin') { $status = "id='navigation_active'"; } else { $status = ""; }
                echo "<li><a  " . $status . " href='" . url(array(), 'admin') . "'>" . $lang["users_admin"] . "</a></li>\n";
            }
        } else {    
            if ($hotaru->getTitle() == 'login') { $status = "id='navigation_active'"; } else { $status = ""; }
            echo "<li><a  " . $status . " href='" . url(array('page'=>'login')) . "'>" . $lang["users_login"] . "</a></li>\n";
            
            if ($hotaru->getTitle() == 'register') { $status = "id='navigation_active'"; } else { $status = ""; }
            echo "<li><a  " . $status . " href='" . url(array('page'=>'register')) . "'>" . $lang["users_register"] . "</a></li>\n";
        }
    }
    
    
    /**
     * This function does work *before* output is sent to the page.
     *
     * @return false
     */
    public function theme_index_replace()
    {
        global $hotaru, $cage, $current_user, $userbase;
        global $send_email_confirmation;
        
        // $send_email_confirmation set to true in "is_page('register')" if email confirmation is enabled
        // it's a global so we can use it in usr_theme_index_main
        $send_email_confirmation = false; 
        
        // Pages you have to be logged in for...
        if ($current_user->loggedIn) {
             if ($hotaru->isPage('logout')) {
                $current_user->destroyCookieAndSession();
                header("Location: " . BASEURL);
            } elseif ($hotaru->isPage('profile')) {
                $this->updateGeneral();
                $this->updatePassword();    
            } 
                    
        // Pages you have to be logged out for...
        } else {
            if ($hotaru->isPage('register')) {
                $userbase->vars['users_recaptcha_enabled'] = $this->getSetting('users_recaptcha_enabled');
                $userbase->vars['users_emailconf_enabled'] = $this->getSetting('users_emailconf_enabled');
                $user_id = $this->register();
                if ($user_id) { 
                    // success!
                    if ($userbase->vars['users_emailconf_enabled']) {
                        $send_email_confirmation = true;
                        $this->sendConfirmationEmail($user_id);
                        // fall through and display "email sent" message
                    } else {
                        // redirect to login page
                        header("Location: " . BASEURL . "index.php?page=login");
                    }
                }
            } elseif ($hotaru->isPage('login')) {
                if ($this->login()) { 
                    // success, return to front page, logged IN.
                    header("Location: " . BASEURL);
                } 
            }     
        }
        return false;
    }
    
    
    /**
     * Display various forms within the body of the page.
     *
     * @return bool
     */
    public function theme_index_main()
    {
        global $hotaru, $cage, $current_user, $userbase, $lang;
        global $send_email_confirmation;
        
        // Pages you have to be logged in for...
        if ($current_user->loggedIn) {
            if ($hotaru->isPage('profile')) {
                $hotaru->displayTemplate('update', 'users');
                return true;
            } else {
                return false;
            }
            
        // Pages you have to be logged out for...
        } else {
            if ($hotaru->isPage('register')) {
                if ($send_email_confirmation) {
                    $hotaru->messages[$lang['users_register_emailconf_sent']] = 'green';
                    $hotaru->showMessages();
                    return true;
                }
                $hotaru->displayTemplate('register', 'users');
                return true;    
            } elseif ($hotaru->isPage('login')) {
                $hotaru->displayTemplate('login', 'users');
                return true;
            } elseif ($hotaru->isPage('emailconf')) {
                $this->checkEmailConfirmation();
                $hotaru->showMessages();
                return true;
            } else {
                return false;
            }    
        }
        return false;
    }
    
    
    /**
     * Filter and breadcrumbs for users
     *
     * @return bool
     */
    public function submit_list_filter() 
    {
        global $hotaru, $current_user, $cage, $filter, $lang, $page_title;
    
        if ($cage->get->keyExists('user')) 
        {
            $filter['post_author = %d'] = $current_user->getUserId($cage->get->testUsername('user')); 
            $rss = " <a href='" . url(array('page'=>'rss', 'user'=>$cage->get->testUsername('user'))) . "'>";
            $rss .= "<img src='" . BASEURL . "content/themes/" . THEME . "images/rss_10.png'></a>";
            // Undo the filter that limits results to either 'top' or 'new' (See submit.php -> sub_prepare_list())
            if(isset($filter['post_status = %s'])) { unset($filter['post_status = %s']); }
            $filter['post_status != %s'] = 'processing';
            $page_title = $lang["submit_page_breadcrumbs_user"] . " &raquo; " . $hotaru->getTitle() . $rss;
            
            return true;    
        }
        
        return false;    
    }
    
    
     /**
     * Change username or email
     *
     * @return bool
     */
    public function updateGeneral()
    {
        global $hotaru, $cage, $lang, $current_user;
        
        $error = 0;
        
        // Updating general profile info
        if ($cage->post->testAlnumLines('users_type') == 'update_general') {
            $username_check = $cage->post->testUsername('username'); // alphanumeric, dashes and underscores okay, case insensitive
            if ($username_check) {
                $current_user->userName = $username_check;
            } else {
                $hotaru->messages[$lang['users_register_username_error']] = 'red';
                $error = 1;
            }
                                
            $email_check = $cage->post->testEmail('email');    
            if ($email_check) {
                $current_user->email = $email_check;
            } else {
                $hotaru->messages[$lang['users_register_email_error']] = 'red';
                $error = 1;
            }
        }
        
        if (!isset($username_check) && !isset($email_check)) {
            $username_check = $current_user->userName;
            $email_check = $current_user->email;
            // do nothing
        } elseif ($error == 0) {
            $result = $current_user->userExists(0, $username_check, $email_check);
            if ($result != 4) { // 4 is returned when the user does not exist in the database
                //success
                $current_user->updateUserBasic();
                $current_user->setCookie(0);
                $hotaru->messages[$lang['users_update_success']] = 'green';
                return true;
            } else {
                //fail
                $hotaru->messages[$lang["users_register_unexpected_error"]] = 'red';
                return false;
            }
        } else {
            // error must = 1 so fall through and display the form again
            return false;
        }
    }
    
    
     /**
     * Enable a user to change their password
     *
     * @return bool
     */
    public function updatePassword()
    {
        global $hotaru, $cage, $lang, $current_user;
        
        $error = 0;
        
        // Updating password
        if ($cage->post->testAlnumLines('users_type') == 'update_password') {
            $password_check_old = $cage->post->testPassword('password_old');    
            
            if ($current_user->loginCheck($current_user->userName, $password_check_old)) {
                // safe, the old password matches the password for this user.
            } else {
                $hotaru->messages[$lang['users_update_password_error_old']] = 'red';
                $error = 1;
            }
        
            $password_check_new = $cage->post->testPassword('password_new');    
            if ($password_check_new) {
                $password_check_new2 = $cage->post->testPassword('password_new2');    
                if ($password_check_new2) { 
                    if ($password_check_new == $password_check_new2) {
                        // safe, the two new password fields match
                    } else {
                        $hotaru->messages[$lang['users_update_password_error_match']] = 'red';
                        $error = 1;
                    }
                } else {
                    $hotaru->messages[$lang['users_update_password_error_new']] = 'red';
                    $error = 1;
                }
            } else {
                $hotaru->messages[$lang['users_update_password_error_not_provided']] = 'red';
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
                $current_user->password = $current_user->generateHash($password_check_new);
                $current_user->updateUserBasic();
                $current_user->setCookie(0);
                $hotaru->messages[$lang['users_update_success']] = 'green';
                return true;
            } else {
                //fail
                $hotaru->messages[$lang["users_register_unexpected_error"]] = 'red';
                return false;
            }
        } else {
            // error must = 1 so fall through and display the form again
            return false;
        }
    }
    
    
     /**
     * User Login
     *
     * @return bool
     */
    public function login()
    {
        global $hotaru, $cage, $lang;
        
        $current_user = new UserBase();
        
        if (!$username_check = $cage->post->testUsername('username')) {
            $username_check = "";
        } 
        if (!$password_check = $cage->post->testPassword('password')) {
            $password_check = "";
        }
        
        if ($username_check != "" || $password_check != "") {
            $login_result = $current_user->loginCheck($username_check, $password_check);
            if ($login_result) {
                    //success
                                
                    if ($cage->post->getInt('remember') == 1){ $remember = 1; } else { $remember = 0; }
                    $current_user->userName = $username_check;
                    $current_user->getUserBasic(0, $current_user->userName);
                    
                    $userbase->vars['users_emailconf_enabled'] = $this->getSetting('users_emailconf_enabled');
                    
                    if ($userbase->vars['users_emailconf_enabled'] && ($current_user->emailValid == 0)) {
                        $this->sendConfirmationEmail($current_user->getId());
                        $hotaru->messages[$lang["users_login_failed_email_not_validated"]] = 'red';
                        $hotaru->messages[$lang["users_login_failed_email_request_sent"]] = 'green';
                        return false;
                    }
                    
                    $current_user->setCookie($remember);
                    $current_user->loggedIn = true;
                    $current_user->updateUserLastLogin();
                    return true;
            } else {
                    // login failed
                    $hotaru->messages[$lang["users_login_failed"]] = 'red';
            }
        } 
        return false;
    }
    
    
     /**
     * Register a new user
     *
     * @return false
     */
    public function register()
    {
        global $db, $hotaru, $cage, $lang, $userbase;
        
        $current_user = new UserBase();
        
        if ($userbase->vars['users_recaptcha_enabled']) {
            require_once(PLUGINS . 'users/recaptcha/recaptchalib.php');
        }
        
        $error = 0;
        if ($cage->post->getAlpha('users_type') == 'register') {
        
            $username_check = $cage->post->testUsername('username'); // alphanumeric, dashes and underscores okay, case insensitive
            if ($username_check) {
                $current_user->userName = $username_check;
            } else {
                $hotaru->messages[$lang['users_register_username_error']] = 'red';
                $error = 1;
            }
                    
            $password_check = $cage->post->testPassword('password');    
            if ($password_check) {
                $password2_check = $cage->post->testPassword('password2');
                if ($password_check == $password2_check) {
                    // safe, the two new password fields match
                    $current_user->password = $userbase->generateHash($password_check);
                } else {
                    $hotaru->messages[$lang['users_register_password_match_error']] = 'red';
                    $error = 1;
                }
                
            } else {
                $hotaru->messages[$lang['users_register_password_error']] = 'red';
                $error = 1;
            }
                        
            $email_check = $cage->post->testEmail('email');    
            if ($email_check) {
                $current_user->email = $email_check;
            } else {
                $hotaru->messages[$lang['users_register_email_error']] = 'red';
                $error = 1;
            }
        
            if ($userbase->vars['users_recaptcha_enabled']) {
                                        
                $recaptcha_pubkey = $this->getSetting('users_recaptcha_pubkey');
                $recaptcha_privkey = $this->getSetting('users_recaptcha_privkey');
                
                $rc_resp = null;
                $rc_error = null;
                
                # was there a reCAPTCHA response?
                if ($cage->post->keyExists('recaptcha_response_field')) {
                        $rc_resp = recaptcha_check_answer($recaptcha_privkey,
                                                        $cage->server->getRaw('REMOTE_ADDR'),
                                                        $cage->post->getRaw('recaptcha_challenge_field'),
                                                        $cage->post->getRaw('recaptcha_response_field'));
                                                        
                        if ($rc_resp->is_valid) {
                                // success, do nothing.
                        } else {
                                # set the error code so that we can display it
                                //$rc_error = $rc_resp->error;
                                $hotaru->messages[$lang['users_register_recaptcha_error']] = 'red';
                        $error = 1;
                        }
                } else {
                    $hotaru->messages[$lang['users_register_recaptcha_empty']] = 'red';
                        $error = 1;
                }
            }
        }    
        
        if (!isset($username_check) && !isset($password_check) && !isset($password2_check) && !isset($email_check)) {
            $username_check = "";
            $password_check = "";
            $password2_check = "";
            $email_check = "";
            // do nothing
        } elseif ($error == 0) {
            $result = $current_user->userExists(0, $username_check, $email_check);
            if ($result == 4) {
                //success
                $current_user->addUserBasic();
                $last_insert_id = $db->get_var($db->prepare("SELECT LAST_INSERT_ID()"));
                return $last_insert_id; // so we can retrieve this user's details for the email confirmation step;
            } elseif ($result == 0) {
                $hotaru->messages[$lang['users_register_id_exists']] = 'red';
    
            } elseif ($result == 1) {
                $hotaru->messages[$lang['users_register_username_exists']] = 'red';
    
            } elseif ($result == 2) {
                $hotaru->messages[$lang['users_register_email_exists']] = 'red';
            } else {
                $hotaru->messages[$lang["users_register_unexpected_error"]] = 'red';
            }
        } else {
            // error must = 1 so fall through and display the form again
        }
        return false;
    }
    
    
     /**
     * Send an email to the newly registered user
     *
     * @param int $user_id
     */
    public function sendConfirmationEmail($user_id)
    {
        global $db, $hotaru, $cage, $lang, $current_user;
        
        // Check that the site email has been changed from the default...
        /*
        if (SITE_EMAIL == "admin@hotarucms.org") {
            echo "Error: Site email not updated in Admin -> Settings";
            die(); exit;
        } 
        */
            
        $current_user->getUserBasic($user_id);
        
        // generate the email confirmation code
        $email_conf = md5(crypt(md5($current_user->email),md5($current_user->email)));
        
        // store the hash in the user table
        $sql = "UPDATE " . TABLE_USERS . " SET user_email_conf = %s WHERE user_id = %d";
        $db->query($db->prepare($sql, $email_conf, $current_user->getId()));
        
        $line_break = "\r\n\r\n";
        $next_line = "\r\n";
        
        // send email
        $subject = $lang['users_register_emailconf_subject'];
        $body = $lang['users_register_emailconf_body_hello'] . " " . $current_user->userName;
        $body .= $line_break;
        $body .= $lang['users_register_emailconf_body_welcome'];
        $body .= $line_break;
        $body .= $lang['users_register_emailconf_body_click'];
        $body .= $line_break;
        $body .= BASEURL . "index.php?page=emailconf&plugin=users&id=" . $current_user->getId() . "&conf=" . $email_conf;
        $body .= $line_break;
        $body .= $lang['users_register_emailconf_body_regards'];
        $body .= $next_line;
        $body .= $lang['users_register_emailconf_body_sign'];
        $to = $current_user->email;
        $headers = "From: " . SITE_EMAIL . "\r\nReply-To: " . SITE_EMAIL . "\r\nX-Priority: 3\r\n";
    
        mail($to, $subject, $body, $headers);    
    }
    
    
     /**
     * Check email confirmation code
     *
     * @return true;
     */
    public function checkEmailConfirmation()
    {
        global $db, $hotaru, $cage, $lang, $current_user;
        
        $user_id = $cage->get->getInt('id');
        $conf = $cage->get->getAlnum('conf');
        
        $current_user->getUserBasic($user_id);
        
        if (!$user_id || !$conf) {
            $hotaru->messages[$lang['users_register_emailconf_fail']] = 'red';
        }
        
        $sql = "SELECT user_email_conf FROM " . TABLE_USERS . " WHERE user_id = %d";
        $user_email_conf = $db->get_var($db->prepare($sql, $user_id));
        
        if ($conf === $user_email_conf) {
            $sql = "UPDATE " . TABLE_USERS . " SET user_email_valid = %d WHERE user_id = %d";
            $db->query($db->prepare($sql, 1, $current_user->getId()));
        
            $success_message = $lang['users_register_emailconf_success'] . " <b><a href='" . url(array('page'=>'login')) . "'>" . $lang['users_register_emailconf_success_login'] . "</a></b>";
            $hotaru->messages[$success_message] = 'green';
        } else {
            $hotaru->messages[$lang['users_register_emailconf_fail']] = 'red';
        }
            
        return true;
    }

}

?>