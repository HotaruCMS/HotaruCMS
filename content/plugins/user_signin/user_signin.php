<?php
/**
 * name: User Signin
 * description: Provides user registration and login
 * version: 0.1
 * folder: user_signin
 * type: signin
 * class: UserSignin
 * hooks: install_plugin, theme_index_top, admin_header_include_raw, navigation_users, theme_index_main, admin_sidebar_plugin_settings, admin_plugin_settings
 * author: Nick Ramsay
 * authorurl: http://hotarucms.org/member.php?1-Nick
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

class UserSignin
{
    /**
     * Install plugin
     */
    public function install_plugin($h)
    {
        // Permissions
        $site_perms = $h->getDefaultPermissions('all');
        if (!isset($site_perms['can_login'])) { 
            $perms['options']['can_login'] = array('yes', 'no'); 
            $perms['can_login']['admin'] = 'yes';
            $perms['can_login']['supermod'] = 'yes';
            $perms['can_login']['moderator'] = 'yes';
            $perms['can_login']['member'] = 'yes';
            $perms['can_login']['undermod'] = 'yes';
            $perms['can_login']['default'] = 'no';
            $h->updateDefaultPermissions($perms);
        }

        // Plugin settings
        $user_signin_settings = $h->getSerializedSettings();
        if (!isset($user_signin_settings['recaptcha_enabled'])) { $user_signin_settings['recaptcha_enabled'] = ""; }
        if (!isset($user_signin_settings['recaptcha_pubkey'])) { $user_signin_settings['recaptcha_pubkey'] = ""; }
        if (!isset($user_signin_settings['recaptcha_privkey'])) { $user_signin_settings['recaptcha_privkey'] = ""; }
        if (!isset($user_signin_settings['emailconf_enabled'])) { $user_signin_settings['emailconf_enabled'] = ""; }
        if (!isset($user_signin_settings['registration_status'])) { $user_signin_settings['registration_status'] = "member"; }
        if (!isset($user_signin_settings['email_notify'])) { $user_signin_settings['email_notify'] = ""; }
        if (!isset($user_signin_settings['email_notify_mods'])) { $user_signin_settings['email_notify_mods'] = array(); }
        
        $h->updateSetting('user_signin_settings', serialize($user_signin_settings));
    }
    
    
    /**
     * Determine what page we're looking at
     */
    public function theme_index_top($h)
    {
        switch ($h->pageName)
        {
            case 'logout':
                $h->currentUser->destroyCookieAndSession();
                header("Location: " . BASEURL);
                exit;
                break;
            case 'login':
                $h->pageTitle = $h->lang["user_signin_login"];
                $h->pageType = 'login';
                if ($this->login($h)) { 
                    // success, return to front page, logged IN.
                    $return = $h->cage->post->testUri('return');
                    if ($return) {
                        header("Location: " . $return);
                    } else {
                        header("Location: " . BASEURL);
                    }
                } 
                break;
            case 'register':
                $h->pageTitle = $h->lang["user_signin_register"];
                $h->pageType = 'register';
                $user_signin_settings = $h->getSerializedSettings('user_signin');
                $h->vars['useRecaptcha'] = $user_signin_settings['recaptcha_enabled'];
                $h->vars['useEmailConf'] = $user_signin_settings['emailconf_enabled'];
                $h->vars['regStatus'] = $user_signin_settings['registration_status'];
                $h->vars['useEmailNotify'] = $user_signin_settings['email_notify'];

                $userid = $this->register($h);
                if ($userid) { 
                    // success!
                    if ($h->vars['useEmailConf']) {
                        $h->vars['send_email_confirmation'] = true;
                        $this->sendConfirmationEmail($h, $userid);
                        // fall through and display "email sent" message
                    } else {
                        // redirect to login page
                        header("Location: " . BASEURL . "index.php?page=login");
                    }
                }
                break;
            case 'emailconf':
                $h->pageTitle = $h->lang['user_signin_register_emailconf'];
                $h->pageType = 'register';
                break;
        }
    }
    
    
    /**
     * Include jQuery for hiding and showing email options in plugin settings
     */
    public function admin_header_include_raw($h)
    {
        if ($h->isSettingsPage('user_signin')) {
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
     * Add links to the end of the navigation bar
     */
    public function navigation_users($h)
    {
        if ($h->currentUser->loggedIn) {
            
            if ($h->pageName == 'logout') { $status = "id='navigation_active'"; } else { $status = ""; }
            echo "<li><a  " . $status . " href='" . $h->url(array('page'=>'logout')) . "'>" . $h->lang["user_signin_logout"] . "</a></li>\n";
            
            if ($h->currentUser->getPermission('can_access_admin') == 'yes') {
                
                if ($h->pageName == 'admin') { $status = "id='navigation_active'"; } else { $status = ""; }
                echo "<li><a  " . $status . " href='" . $h->url(array(), 'admin') . "'>" . $h->lang["user_signin_admin"] . "</a></li>\n";
            }
        } else {    
            
            // Allow other plugins to override the Login / Register links
            $result = $h->pluginHook('user_signin_navigation_logged_out');
            if (!$result)
            { 
                // determine where to return the user to after logging in:
                if (!$h->cage->get->keyExists('return')) {
                    $host = $h->cage->server->sanitizeTags('HTTP_HOST');
                    $uri = $h->cage->server->sanitizeTags('REQUEST_URI');
                    $return = 'http://' . $host . $uri;
                    $return = urlencode(htmlentities($return,ENT_QUOTES,'UTF-8'));
                } else {
                    $return = urlencode($h->cage->get->testUri('return')); // use existing return parameter
                }
                
                if (strpos($return, urlencode(BASEURL)) === false) { $return = urlencode(BASEURL); }
                
                // No plugin results, show the regular Login / Register links:
                if ($h->pageName == 'login') { $status = "id='navigation_active'"; } else { $status = ""; }
                
                if (!$h->isPage('login')) {
                    echo "<li><a  " . $status . " href='" . BASEURL . "index.php?page=login&amp;return=" . $return . "'>" . $h->lang["user_signin_login"] . "</a></li>\n";
                } else {
                    echo "<li><a  " . $status . " href='" . $h->url(array('page'=>'login')) . "'>" . $h->lang["user_signin_login"] . "</a></li>\n";
                }
                
                if ($h->pageName == 'register') { $status = "id='navigation_active'"; } else { $status = ""; }
                echo "<li><a  " . $status . " href='" . $h->url(array('page'=>'register')) . "'>" . $h->lang["user_signin_register"] . "</a></li>\n";
            }
        }
    }
    

    /**
     * Display the right page
     */
    public function theme_index_main($h)
    {
        if (($h->pageType != 'login') && ($h->pageType != 'register')) { return false; }
        
        switch($h->pageName) {
            case 'login':
                $h->displayTemplate('user_signin_login');
                return true;
                break;
            case 'register':
                if (isset($h->vars['send_email_confirmation'])) {
                    $h->messages[$h->lang['user_signin_register_emailconf_sent']] = 'green';
                    $h->showMessages();
                    return true;
                }
                $result = $h->pluginHook('user_signin_pre_display_register_template');
                if (!$result) {
                    // show this form if not overridden by a plugin
                    $h->displayTemplate('user_signin_register', 'user_signin');
                    return true;
                }
                return true;
                break;
            case 'emailconf':
                $user_signin_settings = $h->getSerializedSettings();
                $h->vars['useEmailNotify'] = $user_signin_settings['email_notify'];
                $h->vars['regStatus'] = $user_signin_settings['registration_status'];
                $this->checkEmailConfirmation($h);
                $h->showMessages();
                return true;
                break;
        }
        
        if ($denied) {
            $h->messages[$h->lang["user_signin_access_denied"]] = 'red';
            $h->showMessages();
        }
    }


     /**
     * User Login
     *
     * @return bool
     */
    public function login($h)
    {
        if (!$username_check = $h->cage->post->testUsername('username')) { $username_check = ""; } 
        if (!$password_check = $h->cage->post->testPassword('password')) { $password_check = ""; }
        if ($h->cage->post->getInt('remember') == 1) { $remember = 1; } else { $remember = 0; }
        
        if (($h->cage->post->testPage('page') == 'login') || $h->cage->post->keyExists('forgotten_password')) {
            // if either the login or forgot password form is submitted, check the CSRF key
            if (!$h->csrf()) {
                $h->messages[$h->lang['error_csrf']] = 'red';
                return false;
            }
        }
        
        if ($username_check != "" || $password_check != "") {
            $login_result = $h->currentUser->loginCheck($h, $username_check, $password_check);
            if ($login_result) {
                    //success
                    $h->currentUser->name = $username_check;
                    $result = $this->loginSuccess($h, $remember);
                    return $result;
            } else {
                    // login failed
                    $h->messages[$h->lang["user_signin_login_failed"]] = 'red';
            }
            
        } else {

            if ($h->cage->post->testPage('page') == 'login') {
                // login failed
                $h->messages[$h->lang["user_signin_login_failed"]] = 'red';
            }
            $username_check = '';
            $password_check = '';
            
            // forgotten password request
            if ($h->cage->post->keyExists('forgotten_password')) {
                $this->password($h);
                unset($h->messages[$h->lang["user_signin_login_failed"]]);
            }
            
            // confirming forgotten password email
            $passconf = $h->cage->get->getAlnum('passconf');
            $userid = $h->cage->get->testInt('userid');
            
            if ($passconf && $userid) {
                if ($h->currentUser->newRandomPassword($h, $userid, $passconf)) {
                    $h->messages[$h->lang['user_signin_email_password_conf_success']] = 'green';
                } else {
                    $h->messages[$h->lang['user_signin_email_password_conf_fail']] = 'red';
                }
            }
        }

        return false;
    }
    

     /**
     * Login Success
     *
     * @return bool
     */
    public function loginSuccess($h, $remember = 0)
    {
        $h->currentUser->getUserBasic(0, $h->currentUser->name);
        
        $user_signin_settings = $h->getSerializedSettings('user_signin');
        $h->vars['useEmailConf'] = $user_signin_settings['emailconf_enabled'];
        
        if ($h->vars['useEmailConf'] && ($h->currentUser->emailValid == 0)) {
            $this->sendConfirmationEmail($h, $h->currentUser->id);
            $h->messages[$h->lang["user_signin_login_failed_email_not_validated"]] = 'red';
            $h->messages[$h->lang["user_signin_login_failed_email_request_sent"]] = 'green';
            return false;
        }
        
        if ($h->currentUser->getPermission('can_login') == 'no') {
            if ($h->currentUser->role == 'pending') {
                $h->messages[$h->lang["user_signin_login_failed_not_approved"]] = 'red';
            } else {
                $h->messages[$h->lang["user_signin_login_failed_no_permission"]] = 'red';
            }
            return false;
        }
        
        $h->currentUser->setCookie($h, $remember);
        $h->currentUser->loggedIn = true;
        $h->currentUser->updateUserLastLogin($h);
        $h->currentUser->updateUserLastVisit($h);
        
        return true;
    }
    
    
     /**
     * Password forgotten
     * 
     * @return bool
     */
    public function password($h)
    {
        // Check email
        if (!$email_check = $h->cage->post->testEmail('email')) { 
            $email_check = ''; 
            // login failed
            $h->messages[$h->lang["user_signin_email_invalid"]] = 'red';
            return false;
        } 
        
        $valid_email = $h->emailExists($email_check);
        $userid = $h->getUserIdFromEmail($valid_email);
        
        if ($valid_email && $userid) {
                //success
                $h->currentUser->sendPasswordConf($h, $userid, $valid_email);
                $h->messages[$h->lang['user_signin_email_password_conf_sent']] = 'green';
                return true;
        } else {
                // login failed
                $h->messages[$h->lang["user_signin_email_invalid"]] = 'red';
                return false;
        }
    }
    
    
     /**
     * Register a new user
     *
     * @return false
     */
    public function register($h)
    {
        if ($h->vars['useRecaptcha']) {
            require_once(PLUGINS . 'user_signin/recaptcha/recaptchalib.php');
        }
        
        $error = 0;
        if ($h->cage->post->getAlpha('users_type') == 'register') {
        
            // check CSRF key
            if (!$h->csrf()) {
                $h->messages[$h->lang['error_csrf']] = 'red';
                $error = 1;
            }
        
            $username_check = $h->cage->post->testUsername('username'); // alphanumeric, dashes and underscores okay, case insensitive
            if ($username_check) {
                $h->currentUser->name = $username_check;
            } else {
                $h->messages[$h->lang['user_signin_register_username_error']] = 'red';
                $error = 1;
            }
                    
            $password_check = $h->cage->post->testPassword('password');
            $password2_check = $h->cage->post->testPassword('password2');
            
            // plugins like RPX can override the password values:
            $result = $h->pluginHook('user_signin_register_password_check');
            if ($result) { 
                reset($result); // make sure the array is ordered
                $passwords = $result[key($result)]; // get the value from the first array position - should be an array
                if (is_array($passwords)) {
                    $password_check = $passwords['password'];
                    $password2_check = $passwords['password2'];
                }
            }
            
            if ($password_check) {
                if ($password_check == $password2_check) {
                    // safe, the two new password fields match
                    $h->currentUser->password = $h->currentUser->generateHash($password_check);
                } else {
                    $h->messages[$h->lang['user_signin_register_password_match_error']] = 'red';
                    $error = 1;
                }
                
            } else {
                $h->messages[$h->lang['user_signin_register_password_error']] = 'red';
                $error = 1;
            }
                        
            $email_check = $h->cage->post->testEmail('email');    
            if ($email_check) {
                $h->currentUser->email = $email_check;
            } else {
                $h->messages[$h->lang['user_signin_register_email_error']] = 'red';
                $error = 1;
            }
        
            if ($h->vars['useRecaptcha']) {
                                        
                $user_signin_settings = $h->getSerializedSettings();
                $recaptcha_pubkey = $user_signin_settings['recaptcha_pubkey'];
                $recaptcha_privkey = $user_signin_settings['recaptcha_privkey'];
                
                $rc_resp = null;
                $rc_error = null;
                
                // was there a reCAPTCHA response?
                if ($h->cage->post->keyExists('recaptcha_response_field')) {
                        $rc_resp = recaptcha_check_answer($recaptcha_privkey,
                                                        $h->cage->server->getRaw('REMOTE_ADDR'),
                                                        $h->cage->post->getRaw('recaptcha_challenge_field'),
                                                        $h->cage->post->getRaw('recaptcha_response_field'));
                                                        
                        if ($rc_resp->is_valid) {
                                // success, do nothing.
                        } else {
                                # set the error code so that we can display it
                                $rc_error = $rc_resp->error;
                                $h->messages[$h->lang['user_signin_register_recaptcha_error']] = 'red';
                        $error = 1;
                        }
                } else {
                    $h->messages[$h->lang['user_signin_register_recaptcha_empty']] = 'red';
                        $error = 1;
                }
            }
            
            // let plugins run their own registration checks:
            $h->vars['reg_error'] = $error;
            $h->pluginHook('user_signin_register_error_check');
            $error = $h->vars['reg_error'];
        }    
        
        if (!isset($username_check) && !isset($password_check) && !isset($password2_check) && !isset($email_check)) {
            $username_check = "";
            $password_check = "";
            $password2_check = "";
            $email_check = "";
            // do nothing
        } elseif ($error == 0) {
            $blocked = $this->checkBlocked($h, $username_check, $email_check); // true if blocked, false if safe
            $exists = $h->userExists(0, $username_check, $email_check);
            if (!$blocked && ($exists == 'no')) {
                
                // SUCCESS!!!
                $h->currentUser->role = $h->vars['regStatus'];
                $h->pluginHook('user_signin_register_pre_add_user');
                if ($h->vars['useEmailConf']) { $h->currentUser->role = 'pending'; }
                $h->currentUser->addUserBasic($h);
                $last_insert_id = $h->db->get_var($h->db->prepare("SELECT LAST_INSERT_ID()"));
                
                $h->pluginHook('user_signin_register_post_add_user', '', array($last_insert_id));
                
                // notify chosen mods of new user by email IF email confirmation is DISABLED:
                // If email confirmation is ENABLED, the email gets sent in checkEmailConfirmation().
                if (($h->vars['useEmailNotify']) && (!$h->vars['useEmailConf']) && (file_exists(PLUGINS . 'users/libs/UserFunctions.php')))
                {
                    require_once(PLUGINS . 'users/libs/UserFunctions.php');
                    $uf = new UserFunctions();
                    $uf->notifyMods($h, 'user', $h->currentUser->role, $last_insert_id);
                }
        
                return $last_insert_id; // so we can retrieve this user's details for the email confirmation step;
            } elseif ($exists == 'id') {
                $h->messages[$h->lang['user_signin_register_id_exists']] = 'red';
    
            } elseif ($exists == 'name') {
                $h->messages[$h->lang['user_signin_register_username_exists']] = 'red';
    
            } elseif ($exists == 'email') {
                $h->messages[$h->lang['user_signin_register_email_exists']] = 'red';
            } elseif ($blocked) {
                $h->messages[$h->lang['user_signin_register_user_blocked']] = 'red';
            } else {
                // allow plugin to override the default "unexpected error" message:
                $result = $h->pluginHook('user_signin_register_error_message');
                if (!$result) {
                    $h->messages[$h->lang["user_signin_register_unexpected_error"]] = 'red';
                }
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
    public function checkBlocked($h, $username, $email)
    {
        // Is user IP address blocked?
        $ip = $h->cage->server->testIp('REMOTE_ADDR');
        if ($h->isBlocked('ip', $ip)) {
            return true;
        }
        
        // Is email domain blocked?
        $email_bits = explode('@', $email);
        $email_domain = $email_bits[1];
        if ($h->isBlocked('email', $email_domain)) {
            return true;
        }
        
        // Is email blocked?
        if ($h->isBlocked('email', $email)) {
            return true;
        }
        
        // Is username blocked?
        if ($h->isBlocked('user', $username)) {
            return true;
        }
        
        $h->pluginHook('user_signin_register_check_blocked');  // Stop Spam is one plugin that uses this
        if (isset($h->vars['block']) && $h->vars['block'] == true) { return true; }

        return false;   // not blocked
    }
    
    
     /**
     * Send an email to the newly registered user
     *
     * @param int $user_id
     */
    public function sendConfirmationEmail($h, $user_id)
    {
        $user = new UserAuth();
        $user->getUserBasic($h, $user_id);
        
        // generate the email confirmation code
        $email_conf = md5(crypt(md5($user->email),md5($user->email)));
        
        // store the hash in the user table
        $sql = "UPDATE " . TABLE_USERS . " SET user_email_conf = %s WHERE user_id = %d";
        $h->db->query($h->db->prepare($sql, $email_conf, $user->id));
        
        $line_break = "\r\n\r\n";
        $next_line = "\r\n";
        
        // send email
        $subject = $h->lang['user_signin_register_emailconf_subject'];
        $body = $h->lang['user_signin_register_emailconf_body_hello'] . " " . $user->name;
        $body .= $line_break;
        $body .= $h->lang['user_signin_register_emailconf_body_welcome'];
        $body .= $line_break;
        $body .= $h->lang['user_signin_register_emailconf_body_click'];
        $body .= $line_break;
        $body .= BASEURL . "index.php?page=emailconf&plugin=users&id=" . $user->id . "&conf=" . $email_conf;
        $body .= $line_break;
        $body .= $h->lang['user_signin_register_emailconf_body_regards'];
        $body .= $next_line;
        $body .= $h->lang['user_signin_register_emailconf_body_sign'];
        $to = $user->email;
        $headers = "From: " . SITE_EMAIL . "\r\nReply-To: " . SITE_EMAIL . "\r\nX-Priority: 3\r\n";
        
        /*
        echo "To: " . $to . "<br />";
        echo "Subject: " . $subject . "<br />";
        echo "Body: " . $body . "<br />";
        echo "Headers: " . $headers . "<br />";
        */

        mail($to, $subject, $body, $headers);    
    }
    
    
     /**
     * Check email confirmation code
     *
     * @return true;
     */
    public function checkEmailConfirmation($h)
    {
        $user_id = $h->cage->get->getInt('id');
        $conf = $h->cage->get->getAlnum('conf');
        
        $user = new UserAuth();
        $user->getUserBasic($h, $user_id);
        
        if (!$user_id || !$conf) {
            $h->messages[$h->lang['user_signin_register_emailconf_fail']] = 'red';
        }
        
        $sql = "SELECT user_email_conf FROM " . TABLE_USERS . " WHERE user_id = %d";
        $user_email_conf = $h->db->get_var($h->db->prepare($sql, $user_id));
        
        if ($conf === $user_email_conf) 
        {
            // update role:
            $user->role = $h->vars['regStatus'];
            
            $h->pluginHook('user_signin_email_conf_post_role');

            // update user with new permissions:
            $new_perms = $user->getDefaultPermissions($h, $user->role);
            unset($new_perms['options']);  // don't need this for individual users
            $user->setAllPermissions($new_perms);
            $user->updatePermissions($h);
            $user->updateUserBasic($h);
        
            // set email valid to 1:
            $sql = "UPDATE " . TABLE_USERS . " SET user_email_valid = %d WHERE user_id = %d";
            $h->db->query($h->db->prepare($sql, 1, $user->id));
            
            // notify chosen mods of new user by email:
            if (($h->vars['useEmailNotify'] == 'checked') && (file_exists(PLUGINS . 'users/libs/UserFunctions.php'))) {
                require_once(PLUGINS . 'users/libs/UserFunctions.php');
                $uf = new UserFunctions();
                $uf->notifyMods($h, 'user', $user->role, $user->id);
            }
        
            $success_message = $h->lang['user_signin_register_emailconf_success'] . " <br /><b><a href='" . $h->url(array('page'=>'login')) . "'>" . $h->lang['user_signin_register_emailconf_success_login'] . "</a></b>";
            $h->messages[$success_message] = 'green';
        } else {
            $h->messages[$h->lang['user_signin_register_emailconf_fail']] = 'red';
        }
            
        return true;
    }
}

?>
