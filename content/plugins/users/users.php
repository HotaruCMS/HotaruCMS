<?php
/**
 * name: Users
 * description: Manages users within Hotaru.
 * version: 0.8
 * folder: users
 * class: Users
 * hooks: hotaru_header, header_include, admin_header_include_raw, install_plugin, admin_sidebar_plugin_settings, admin_plugin_settings, navigation_first, navigation_users, theme_index_replace, theme_index_main, post_list_filter, submit_post_breadcrumbs, userbase_default_permissions, submit_pre_list
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
        // Create a new empty table called "usermeta"
        $exists = $this->db->table_exists('usermeta');
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
            $this->db->query($sql); 
        }
        
        // Create a new empty table called "useractivity"
        $exists = $this->db->table_exists('useractivity');
        if (!$exists) {
            //echo "table doesn't exist. Stopping before creation."; exit;
            $sql = "CREATE TABLE `" . DB_PREFIX . "useractivity` (
              `useract_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `useract_archived` enum('Y','N') NOT NULL DEFAULT 'N',
              `useract_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
              `useract_userid` int(20) NOT NULL DEFAULT 0,
              `useract_status` varchar(32) NOT NULL DEFAULT 'show',
              `useract_key` varchar(255) NULL,
              `useract_value` text NULL,
              `useract_key2` varchar(255) NULL,
              `useract_value2` text NULL,
              `useract_date` timestamp NOT NULL,
              `useract_updateby` int(20) NOT NULL DEFAULT 0, 
              INDEX  (`useract_userid`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='User Activity';";
            $this->db->query($sql); 
        }
        
        $users_settings = $this->getSerializedSettings();
        if (!isset($users_settings['users_recaptcha_enabled'])) { $users_settings['users_recaptcha_enabled'] = ""; }
        if (!isset($users_settings['users_recaptcha_pubkey'])) { $users_settings['users_recaptcha_pubkey'] = ""; }
        if (!isset($users_settings['users_recaptcha_privkey'])) { $users_settings['users_recaptcha_privkey'] = ""; }
        if (!isset($users_settings['users_emailconf_enabled'])) { $users_settings['users_emailconf_enabled'] = ""; }
        if (!isset($users_settings['users_registration_status'])) { $users_settings['users_registration_status'] = "member"; }
        if (!isset($users_settings['users_email_notify'])) { $users_settings['users_email_notify'] = ""; }
        if (!isset($users_settings['users_email_notify_mods'])) { $users_settings['users_email_notify_mods'] = array(); }
        
        $this->updateSetting('users_settings', serialize($users_settings));
        
        // Include language file. Also included in hotaru_header, but needed here  
        // to prevent errors immediately after installation.
        $this->includeLanguage();    
    }
    
    
    /**
     * Define a constant "TABLE_USERMETA" constant for referring to the db table
     */
    public function hotaru_header() {
        if (!defined('TABLE_USERMETA')) { define("TABLE_USERMETA", DB_PREFIX . 'usermeta'); }
        if (!defined('TABLE_USERACTIVITY')) { define("TABLE_USERACTIVITY", DB_PREFIX . 'useractivity'); }
        
        // include language file
        $this->includeLanguage();
        
        // Under these conditions, we're looking at a user page - i.e. user posts filtered to popular, latest, etc.
        if ($username = $this->cage->get->testUsername('user')) {
            $this->hotaru->title = $username;
            $this->hotaru->pageType = 'user';
        }
        
        // Under these conditions, we're looking at the user's main page - the profile.
        if ($this->hotaru->isPage('main') && $this->cage->get->keyExists('user') && !$this->cage->get->keyExists('sort')) {
            $this->hotaru->pageType = 'profile';
        }
    }

    
    /**
     * Add the account link at the front of the navigation bar
     */
    public function navigation_first()
    {
        if (!$this->current_user->loggedIn) { return false; }
            
        if ($this->hotaru->title == 'account') { $status = "id='navigation_active'"; } else { $status = ""; }
        echo "<li>";
        echo "<a class='users_navigation_name' " . $status . " href='" . $this->hotaru->url(array('page'=>'account')) . "' ";
        echo "title='" . $this->lang["users_account"] . "'>";
        $this->pluginHook('users_pre_navigation_first'); // gravatar
        echo $this->current_user->name . "</a></li>\n";
    }
    
    
    /**
     * Add links to the end of the navigation bar
     */
    public function navigation_users()
    {
        if ($this->current_user->loggedIn) {
            
            if ($this->hotaru->title == 'logout') { $status = "id='navigation_active'"; } else { $status = ""; }
            echo "<li><a  " . $status . " href='" . $this->hotaru->url(array('page'=>'logout')) . "'>" . $this->lang["users_logout"] . "</a></li>\n";
            
            if ($this->current_user->getPermission('can_access_admin') == 'yes') {
                
                if ($this->hotaru->title == 'admin') { $status = "id='navigation_active'"; } else { $status = ""; }
                echo "<li><a  " . $status . " href='" . $this->hotaru->url(array(), 'admin') . "'>" . $this->lang["users_admin"] . "</a></li>\n";
            }
        } else {    
            if ($this->hotaru->title == 'login') { $status = "id='navigation_active'"; } else { $status = ""; }
            echo "<li><a  " . $status . " href='" . $this->hotaru->url(array('page'=>'login')) . "'>" . $this->lang["users_login"] . "</a></li>\n";
            
            if ($this->hotaru->title == 'register') { $status = "id='navigation_active'"; } else { $status = ""; }
            echo "<li><a  " . $status . " href='" . $this->hotaru->url(array('page'=>'register')) . "'>" . $this->lang["users_register"] . "</a></li>\n";
        }
    }
    
    
    /**
     * This function does work *before* output is sent to the page.
     *
     * @return false
     */
    public function theme_index_replace()
    {
        // send_email_confirmation set to true in "is_page('register')" if email confirmation is enabled
        $this->hotaru->vars['send_email_confirmation'] = false; 
        
        // Pages you have to be logged in for...
        if ($this->current_user->loggedIn) {
            if ($this->hotaru->isPage('logout')) {
                $this->current_user->destroyCookieAndSession();
                header("Location: " . BASEURL);
            } 
            elseif ($this->hotaru->isPage('account')) 
            {
                if ($user = $this->cage->get->testUsername('user')) {
                    $this->hotaru->vars['userid'] = $this->current_user->getUserIdFromName($user);
                } else {
                    $this->hotaru->vars['userid'] = $this->cage->post->testInt('userid');
                }
                
                // if userid is blank, assume current user's id.
                if (!$this->hotaru->vars['userid']) { $this->hotaru->vars['userid'] = $this->current_user->id; }

                $this->hotaru->vars['checks'] = $this->current_user->updateAccount($this->hotaru->vars['userid']);
            } 
                    
        // Pages you have to be logged out for...
        } else {
            if ($this->hotaru->isPage('register')) {
            
                $users_settings = $this->getSerializedSettings();
                $this->current_user->vars['useRecaptcha'] = $users_settings['users_recaptcha_enabled'];
                $this->current_user->vars['useEmailConf'] = $users_settings['users_emailconf_enabled'];
                $this->current_user->vars['regStatus'] = $users_settings['users_registration_status'];
                $this->current_user->vars['useEmailNotify'] = $users_settings['users_email_notify'];

                $userid = $this->register();
                if ($userid) { 
                    // success!
                    if ($this->current_user->vars['useEmailConf']) {
                        $this->hotaru->vars['send_email_confirmation'] = true;
                        $this->sendConfirmationEmail($userid);
                        // fall through and display "email sent" message
                    } else {
                        // redirect to login page
                        header("Location: " . BASEURL . "index.php?page=login");
                    }
                }
            } elseif ($this->hotaru->isPage('login')) {
                if ($this->login()) { 
                    // success, return to front page, logged IN.
                    header("Location: " . BASEURL);
                } 
            }     
        }
        return false;
    }
    
    
    /**
     * Display the main user profile page.
     *
     * @return bool
     */
    public function submit_pre_list()
    {
        if ($this->hotaru->pageType == 'profile') {
            $this->hotaru->user = new UserBase($this->hotaru);
            $this->hotaru->user->getUserBasic(0, $this->cage->get->testUsername('user'));
            $this->hotaru->displayTemplate('profile', 'users');
        }
    }
    
    
    /**
     * Display various forms within the body of the page.
     *
     * @return bool
     */
    public function theme_index_main()
    {
        // Pages you have to be logged in for...
        if ($this->current_user->loggedIn) {
            if ($this->hotaru->isPage('account')) {
                // Note: the "account" template calls the functions it needs 
                // from the UserBase class.
                extract($this->hotaru->vars['checks']);
                if (($role_check == 'admin') && ($this->current_user->role != 'admin')) {
                    $this->hotaru->messages[$this->lang["users_account_admin_admin"]] = 'red';
                    $this->hotaru->showMessages();
                } else {
                    $this->hotaru->displayTemplate('account', 'users');
                }
                return true;
            } elseif ($this->hotaru->isPage('permissions')) {
                if ($this->current_user->getPermission('can_access_admin') == 'yes') { 
                    $this->editPermissions();
                } else {
                    $this->hotaru->messages[$this->lang["access_denied"]] = 'red';
                    $this->hotaru->showMessages();
                }
                return true;
            } else {
                return false;
            }
            
        // Pages you have to be logged out for...
        } else {
            if ($this->hotaru->isPage('register')) {
                if ($this->hotaru->vars['send_email_confirmation']) {
                    $this->hotaru->messages[$this->lang['users_register_emailconf_sent']] = 'green';
                    $this->hotaru->showMessages();
                    return true;
                }
                $this->hotaru->displayTemplate('register', 'users');
                return true;    
            } elseif ($this->hotaru->isPage('login')) {
                $this->hotaru->displayTemplate('login', 'users');
                return true;
            } elseif ($this->hotaru->isPage('emailconf')) {
                $users_settings = $this->getSerializedSettings();
                $this->current_user->vars['useEmailNotify'] = $users_settings['users_email_notify'];
                $this->current_user->vars['regStatus'] = $users_settings['users_registration_status'];
                $this->checkEmailConfirmation();
                $this->hotaru->showMessages();
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
    public function post_list_filter() 
    {
        if ($this->cage->get->keyExists('user')) 
        {
            $username = $this->cage->get->testUsername('user');
            $this->hotaru->vars['filter']['post_author = %d'] = $this->current_user->getUserIdFromName($username); 
            $rss = " <a href='" . $this->hotaru->url(array('page'=>'rss', 'user'=>$username)) . "'>";
            $rss .= "<img src='" . BASEURL . "content/themes/" . THEME . "images/rss_10.png'></a>";
            
            $this->hotaru->vars['page_title'] = $this->lang["post_breadcrumbs_user"] . " &raquo; ";
            $this->hotaru->vars['page_title'] .= "<a href='" . $this->hotaru->url(array('user'=>$username)) . "'>";
            $this->hotaru->vars['page_title'] .= $username . "</a>";
            $this->hotaru->vars['page_title'] .= $rss;
            
            //$this->hotaru->pageType = 'user'; - this was changing "profile" to "user" so for now it's commented out.
            
            return true;    
        }
        
        return false;    
    }
    
    
     /**
     * User Login
     *
     * @return bool
     */
    public function login()
    {
        $current_user = new UserBase($this->hotaru);
        
        if (!$username_check = $this->cage->post->testUsername('username')) {
            $username_check = "";
        } 
        if (!$password_check = $this->cage->post->testPassword('password')) {
            $password_check = "";
        }
        
        if ($username_check != "" || $password_check != "") {
            $login_result = $this->current_user->loginCheck($username_check, $password_check);
            if ($login_result) {
                    //success
                                
                    if ($this->cage->post->getInt('remember') == 1){ $remember = 1; } else { $remember = 0; }
                    $this->current_user->name = $username_check;
                    $this->current_user->getUserBasic(0, $this->current_user->name);
                    
                    $users_settings = $this->getSerializedSettings();
                    $this->current_user->vars['useEmailConf'] = $users_settings['users_emailconf_enabled'];
                    
                    if ($this->current_user->vars['useEmailConf'] && ($this->current_user->emailValid == 0)) {
                        $this->sendConfirmationEmail($this->current_user->id);
                        $this->hotaru->messages[$this->lang["users_login_failed_email_not_validated"]] = 'red';
                        $this->hotaru->messages[$this->lang["users_login_failed_email_request_sent"]] = 'green';
                        return false;
                    }
                    
                    
                    if ($this->current_user->getPermission('can_login') == 'no') {
                        if ($this->current_user->role == 'pending') {
                            $this->hotaru->messages[$this->lang["users_login_failed_not_approved"]] = 'red';
                        } else {
                            $this->hotaru->messages[$this->lang["users_login_failed_no_permission"]] = 'red';
                        }
                        return false;
                    }
                    
                    
                    $this->current_user->setCookie($remember);
                    $this->current_user->loggedIn = true;
                    $this->current_user->updateUserLastLogin();
                    
              
                    return true;
            } else {
                    // login failed
                    $this->hotaru->messages[$this->lang["users_login_failed"]] = 'red';
            }
        } else {
        
            // forgotten password request
            if ($this->cage->post->keyExists('forgotten_password')) {
                $this->password();
            }
            
            // confirming forgotten password email
            $passconf = $this->cage->get->getAlnum('passconf');
            $userid = $this->cage->get->testInt('userid');
            
            if ($passconf && $userid) {
                if ($this->current_user->newRandomPassword($userid, $passconf)) {
                    $this->hotaru->messages[$this->lang['users_email_password_conf_success']] = 'green';
                } else {
                    $this->hotaru->messages[$this->lang['users_email_password_conf_fail']] = 'red';
                }
            }
        }
        return false;
    }
    
    
     /**
     * Password forgotten
     * 
     * @return bool
     */
    public function password()
    {
        // Check email
        if (!$email_check = $this->cage->post->testEmail('email')) { 
            $email_check = ''; 
            // login failed
            $this->hotaru->messages[$this->lang["users_email_invalid"]] = 'red';
            return false;
        } 
                    
        $valid_email = $this->current_user->validEmail($email_check);
        $userid = $this->current_user->getUserIdFromEmail($valid_email);
        
        if ($valid_email && $userid) {
                //success
                $this->current_user->sendPasswordConf($userid, $valid_email);
                $this->hotaru->messages[$this->lang['users_email_password_conf_sent']] = 'green';
                return true;
        } else {
                // login failed
                $this->hotaru->messages[$this->lang["users_email_invalid"]] = 'red';
                return false;
        }
    }
    
    
     /**
     * Register a new user
     *
     * @return false
     */
    public function register()
    {
        $current_user = new UserBase($this->hotaru);
        
        if ($this->current_user->vars['useRecaptcha']) {
            require_once(PLUGINS . 'users/recaptcha/recaptchalib.php');
        }
        
        $error = 0;
        if ($this->cage->post->getAlpha('users_type') == 'register') {
        
            $username_check = $this->cage->post->testUsername('username'); // alphanumeric, dashes and underscores okay, case insensitive
            if ($username_check) {
                $this->current_user->name = $username_check;
            } else {
                $this->hotaru->messages[$this->lang['users_register_username_error']] = 'red';
                $error = 1;
            }
                    
            $password_check = $this->cage->post->testPassword('password');    
            if ($password_check) {
                $password2_check = $this->cage->post->testPassword('password2');
                if ($password_check == $password2_check) {
                    // safe, the two new password fields match
                    $this->current_user->password = $this->current_user->generateHash($password_check);
                } else {
                    $this->hotaru->messages[$this->lang['users_register_password_match_error']] = 'red';
                    $error = 1;
                }
                
            } else {
                $this->hotaru->messages[$this->lang['users_register_password_error']] = 'red';
                $error = 1;
            }
                        
            $email_check = $this->cage->post->testEmail('email');    
            if ($email_check) {
                $this->current_user->email = $email_check;
            } else {
                $this->hotaru->messages[$this->lang['users_register_email_error']] = 'red';
                $error = 1;
            }
        
            if ($this->current_user->vars['useRecaptcha']) {
                                        
                $users_settings = $this->getSerializedSettings();
                $recaptcha_pubkey = $users_settings['users_recaptcha_pubkey'];
                $recaptcha_privkey = $users_settings['users_recaptcha_privkey'];
                
                $rc_resp = null;
                $rc_error = null;
                
                // was there a reCAPTCHA response?
                if ($this->cage->post->keyExists('recaptcha_response_field')) {
                        $rc_resp = recaptcha_check_answer($recaptcha_privkey,
                                                        $this->cage->server->getRaw('REMOTE_ADDR'),
                                                        $this->cage->post->getRaw('recaptcha_challenge_field'),
                                                        $this->cage->post->getRaw('recaptcha_response_field'));
                                                        
                        if ($rc_resp->is_valid) {
                                // success, do nothing.
                        } else {
                                # set the error code so that we can display it
                                $rc_error = $rc_resp->error;
                                $this->hotaru->messages[$this->lang['users_register_recaptcha_error']] = 'red';
                        $error = 1;
                        }
                } else {
                    $this->hotaru->messages[$this->lang['users_register_recaptcha_empty']] = 'red';
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
            $blocked = $this->checkBlocked($username_check, $email_check); // true if blocked, false if safe
            $result = $this->current_user->userExists(0, $username_check, $email_check);
            if (!$blocked && $result == 4) {
                
                // SUCCESS!!!
                $this->current_user->role = $this->current_user->vars['regStatus'];
                if ($this->current_user->vars['useEmailConf']) { $this->current_user->role = 'pending'; }
                $this->current_user->addUserBasic();
                $last_insert_id = $this->db->get_var($this->db->prepare("SELECT LAST_INSERT_ID()"));
                
                // notify chosen mods of new user by email IF email confirmation is DISABLED:
                // If email confirmation is ENABLED, the email gets sent in checkEmailConfirmation().
                if (($this->current_user->vars['useEmailNotify']) && (!$this->current_user->vars['useEmailConf']))
                {
                    require_once(PLUGINS . 'users/libs/UserFunctions.php');
                    $uf = new UserFunctions($this->hotaru);
                    $uf->notifyMods('user', $this->current_user->role);
                }
        
                return $last_insert_id; // so we can retrieve this user's details for the email confirmation step;
            } elseif ($result == 0) {
                $this->hotaru->messages[$this->lang['users_register_id_exists']] = 'red';
    
            } elseif ($result == 1) {
                $this->hotaru->messages[$this->lang['users_register_username_exists']] = 'red';
    
            } elseif ($result == 2) {
                $this->hotaru->messages[$this->lang['users_register_email_exists']] = 'red';
            } elseif ($blocked) {
                $this->hotaru->messages[$this->lang['users_register_user_blocked']] = 'red';
            } else {
                $this->hotaru->messages[$this->lang["users_register_unexpected_error"]] = 'red';
            }
        } else {
            // error must = 1 so fall through and display the form again
        }
        return false;
    }
    
    
    /**
     * Check if user is on the blocked list
     *
     * @param string $username
     * @param string $email
     * @return bool - true if blocked
     */
    public function checkBlocked($username, $email)
    {
        // Is user IP address blocked?
        $ip = $this->cage->server->testIp('REMOTE_ADDR');
        if ($this->isBlocked('ip', $ip)) {
            return true;
        }
        
        // Is email domain blocked?
        $email_bits = split('@', $email);
        $email_domain = $email_bits[1];
        if ($this->isBlocked('email', $email_domain)) {
            return true;
        }
        
        // Is email blocked?
        if ($this->isBlocked('email', $email)) {
            return true;
        }
        
        // Is username blocked?
        if ($this->isBlocked('user', $username)) {
            return true;
        }
                        
        return false;   // not blocked
    }
    
    
     /**
     * Send an email to the newly registered user
     *
     * @param int $user_id
     */
    public function sendConfirmationEmail($user_id)
    {
        $this->current_user->getUserBasic($user_id);
        
        // generate the email confirmation code
        $email_conf = md5(crypt(md5($this->current_user->email),md5($this->current_user->email)));
        
        // store the hash in the user table
        $sql = "UPDATE " . TABLE_USERS . " SET user_email_conf = %s WHERE user_id = %d";
        $this->db->query($this->db->prepare($sql, $email_conf, $this->current_user->id));
        
        $line_break = "\r\n\r\n";
        $next_line = "\r\n";
        
        // send email
        $subject = $this->lang['users_register_emailconf_subject'];
        $body = $this->lang['users_register_emailconf_body_hello'] . " " . $this->current_user->name;
        $body .= $line_break;
        $body .= $this->lang['users_register_emailconf_body_welcome'];
        $body .= $line_break;
        $body .= $this->lang['users_register_emailconf_body_click'];
        $body .= $line_break;
        $body .= BASEURL . "index.php?page=emailconf&plugin=users&id=" . $this->current_user->id . "&conf=" . $email_conf;
        $body .= $line_break;
        $body .= $this->lang['users_register_emailconf_body_regards'];
        $body .= $next_line;
        $body .= $this->lang['users_register_emailconf_body_sign'];
        $to = $this->current_user->email;
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
        $user_id = $this->cage->get->getInt('id');
        $conf = $this->cage->get->getAlnum('conf');
        
        $this->current_user->getUserBasic($user_id);
        
        if (!$user_id || !$conf) {
            $this->hotaru->messages[$this->lang['users_register_emailconf_fail']] = 'red';
        }
        
        $sql = "SELECT user_email_conf FROM " . TABLE_USERS . " WHERE user_id = %d";
        $user_email_conf = $this->db->get_var($this->db->prepare($sql, $user_id));
        
        if ($conf === $user_email_conf) 
        {
            // update role:
            $this->current_user->role = $this->current_user->vars['regStatus'];

            // update user with new permissions:
            $new_perms = $this->current_user->getDefaultPermissions($this->current_user->role);
            unset($new_perms['options']);  // don't need this for individual users
            $this->current_user->setAllPermissions($new_perms);
            $this->current_user->updatePermissions();
            $this->current_user->updateUserBasic();
        
            // set email valid to 1:
            $sql = "UPDATE " . TABLE_USERS . " SET user_email_valid = %d WHERE user_id = %d";
            $this->db->query($this->db->prepare($sql, 1, $this->current_user->id));
            
            // notify chosen mods of new user by email:
            if ($this->current_user->vars['useEmailNotify'] == 'checked') {
                require_once(PLUGINS . 'users/libs/UserFunctions.php');
                $uf = new UserFunctions($this->hotaru);
                $uf->notifyMods('user', $this->current_user->role);
            }
                
        
            $success_message = $this->lang['users_register_emailconf_success'] . " <b><a href='" . $this->hotaru->url(array('page'=>'login')) . "'>" . $this->lang['users_register_emailconf_success_login'] . "</a></b>";
            $this->hotaru->messages[$success_message] = 'green';
        } else {
            $this->hotaru->messages[$this->lang['users_register_emailconf_fail']] = 'red';
        }
            
        return true;
    }
    
    /** 
     * Enable admins to edit a user
     */
    public function submit_post_breadcrumbs()
    {
        // not ideal, but the easiest way to get the target username is from the page title:
        $username = $this->hotaru->title;
        $page_type = $this->hotaru->pageType;
        
        if (($page_type == 'user' || $page_type == 'profile' ) && $this->current_user->getPermission('can_access_admin') == 'yes') {
            echo "<div class='post_breadcrumbs_links_bar'>";
            echo $this->lang["users_account_edit"] . " " . $username . ": ";
            echo " <a href='" . $this->hotaru->url(array('page' => 'account', 'user' => $username)) . "'>";
            echo $this->lang["users_account_account"] . "</a> | ";
            echo " <a href='" . $this->hotaru->url(array('page' => 'permissions', 'user' => $username)) . "'>";
            echo $this->lang["users_account_permissions"] . "</a>";
            echo "</div>";
        }
    }
    
    
    /** 
     * Enable admins to edit a user
     */
    public function editPermissions()
    {
        $user = new UserBase($this->hotaru);

        // Read this user...
        if ($this->cage->get->keyExists('user')) {
            $user->getUserbasic(0, $this->cage->get->testUsername('user'));   // username when viewing perms page
        } elseif ($this->cage->post->keyExists('userid')) {
            $user->getUserbasic($this->cage->post->testInt('userid'));        // userid when submitting perms form
        } else {
            return false;
        }
        
        // prevent non-admin user viewing permissions of admin user
        if (($user->role) == 'admin' && ($this->current_user->role != 'admin')) {
            $this->hotaru->messages[$this->lang["users_account_admin_admin"]] = 'red';
            $this->hotaru->showMessages();
            return true;
        }
        
        $perm_options = $user->getDefaultPermissions();
        $perms = $user->getAllPermissions();
        
        // If the form has been submitted...
        if ($this->cage->post->keyExists('permissions')) {
           foreach ($perm_options['options'] as $key => $options) {
                if ($value = $this->cage->post->testAlnumLines($key)) {
                    $user->setPermission($key, $value);
                }
            }

            $user->updatePermissions();   // physically store changes in the database
            
            // get the newly updated latest permissions:
            $perm_options = $user->getDefaultPermissions();
            $perms = $user->getAllPermissions();
            $this->hotaru->messages[$this->lang['users_account_permissions_updated']] = 'green';
        }
               
        // Breadcrumbs:
        echo "<div id='breadcrumbs'><a href='" . BASEURL . "'>" . $this->lang["users_home"] . "</a> "; 
        echo "&raquo; <a href='" . $this->hotaru->url(array('user' => $user->name)) . "'>" . $user->name . "</a> "; 
        echo "&raquo; " . $this->lang["users_account_permissions"] . "</div>";
            
        $this->hotaru->vars['username'] = $user->name;
        $this->hotaru->displayTemplate('user_tabs', 'users');
        
        echo '<h2>' . $this->lang["users_account_user_permissions"] . ': ' . $user->name . '</h2>';
        
        $this->hotaru->showMessages();
            
        echo "<form name='permissions_form' action='" . BASEURL . "index.php' method='post'>\n";
        echo "<table class='permissions'>\n";
        foreach ($perm_options['options'] as $key => $options) {
            echo "<tr><td>" . make_name($key) . ": </td>\n";
            foreach($options as $value) {
                if (isset($perms[$key]) && ($perms[$key] == $value)) { $checked = 'checked'; } else { $checked = ''; } 
                if ($key == 'can_access_admin' && $user->role == 'admin') { $disabled = 'disabled'; } else { $disabled = ''; }
                echo "<td><input type='radio' name='" . $key . "' value='" . $value . "' " . $checked . " " . $disabled . "> " . $value . " &nbsp;</td>\n";
            }
            echo "</tr>";
        }
        
        echo "</table>\n";
        echo "<input type='hidden' name='page' value='permissions' />\n";
        echo "<input type='hidden' name='permissions' value='updated' />\n";
        echo "<input type='hidden' name='userid' value='" . $user->id . "' />\n";
        echo "<div style='text-align: right'><input class='submit' type='submit' value='" . $this->lang['users_account_form_submit'] . "' /></div>\n";
        echo "</form>\n";
    }
    
    
    /**
     * Include jQuery for hiding and showing email options in plugin settings
     */
    public function admin_header_include_raw()
    {
        $admin = new Admin();
        
        if ($admin->isSettingsPage('users')) {
            echo "<script type='text/javascript'>\n";
            echo "$(document).ready(function(){\n";
                echo "$('#email_notify').click(function () {\n";
                echo "$('#email_notify_options').slideToggle();\n";
                echo "});\n";
            echo "});\n";
            echo "</script>\n";
        }
    }
    
    
    /**
     * Default permissions 
     *
     * @param array $params - conatins "role"
     */
    public function userbase_default_permissions($params)
    {
        $perms = $this->hotaru->vars['perms'];

        $role = $params['role'];
        
        // Permission Options
        $perms['options']['can_login'] = array('yes', 'no');

        // Permissions for $role
        switch ($role) {
            case 'admin':
            case 'supermod':
            case 'moderator':
            case 'member':
            case 'undermod':
                $perms['can_login'] = 'yes';
                break;
            default:
                $perms['can_login'] = 'no';
        }
        
        $this->hotaru->vars['perms'] = $perms;
    }
}

?>