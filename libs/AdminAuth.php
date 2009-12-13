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
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
class AdminAuth
{
    /**
     * Initialize Admin
     */
    public function adminInit($hotaru)
    {
        $page = $hotaru->cage->get->testPage('page');    // check with "get";
        if (!$page) { 
            // check with "post" - used in admin_login_form().
            $page = $hotaru->cage->post->testPage('page'); 
        }
        
        
        // Authenticate the admin if the Users plugin is INACTIVE:
        if (!$hotaru->isActive('users'))
        {
            if (($page != 'admin_login') && !$this->isAdminCookie($hotaru))
            {
                header('Location: ' . BASEURL . 'admin_index.php?page=admin_login');
            }
        }
        

        // Authenticate the admin if the Users plugin is ACTIVE:
        if (isset($hotaru->currentUser) && $hotaru->isActive('users'))
        {
            // This first condition happens when the Users plugin is activated 
            // and there's no cookie for the Admin yet.
            if (($hotaru->currentUser->name == "") && $hotaru->isActive('users')) 
            {
                header('Location: ' . BASEURL . 'index.php?page=login');
                die; exit;
            } 
            elseif ($hotaru->currentUser->getPermission('can_access_admin') != 'yes') 
            {
                // maybe the user has permission to access a specific plugin settings page?
                $plugin = $hotaru->cage->get->testAlnumLines('plugin');
                if ($plugin && ($page == "plugin_settings")) {
                    $permission = "can_" . $plugin . "_settings";
                    if ($hotaru->currentUser->getPermission($permission) == 'yes') {
                        $hotaru->sidebars = false; // hide sidebars
                        $hotaru->displayTemplate('index');
                        die(); exit;
                    }
                }
                
                // User doesn't have permission to access Admin
                $hotaru->messages['Access Denied'] = 'red';
                $hotaru->displayTemplate('access_denied');
                die(); exit;
            }
        }
        
        // If we get this far, we know that the user has admin access.
        
        return $page;
    }
    
    
     /**
     * Admin login
     * 
     * @return bool
     */
    public function adminLogin($hotaru)
    {
        // Check username
        if (!$username_check = $hotaru->cage->post->testUsername('username')) { 
            $username_check = ''; 
        } 
        
        // Check password
        if (!$password_check = $hotaru->cage->post->testPassword('password')) {
            $password_check = ''; 
        }
        
        if ($hotaru->cage->post->keyExists('login_attempted') || $hotaru->cage->post->keyExists('forgotten_password')) {
            // if either the login or forgot password form is submitted, check the CSRF key
            
            if (!$hotaru->csrf()) {
                $hotaru->message = $hotaru->lang["error_csrf"];
                $hotaru->messageType = "red";
                return false;
            }
        }

        if ($username_check != '' || $password_check != '') 
        {
            $login_result = $hotaru->currentUser->loginCheck($hotaru, $username_check, $password_check);

            if ($login_result) {
                    //success
                    $this->setAdminCookie($username_check);
                    $hotaru->currentUser->name = $username_check;
                    $hotaru->currentUser->getUserBasic($hotaru, 0, $username_check);
                    $hotaru->currentUser->loggedIn = true;
                    $hotaru->currentUser->updateUserLastLogin($hotaru);
                    $hotaru->sidebars = true;
                    $hotaru->pageName = 'admin_home'; // a wee hack
                    return true;
            } else {
                    // login failed
                    $hotaru->message = $hotaru->lang["admin_login_failed"];
                    $hotaru->messageType = "red";
            }
        } 
        else 
        {
            if ($hotaru->cage->post->keyExists('login_attempted')) {
                $hotaru->message = $hotaru->lang["admin_login_failed"];
                $hotaru->messageType = "red";
            }
            $username_check = '';
            $password_check = '';
            
            // forgotten password request
            if ($hotaru->cage->post->keyExists('forgotten_password')) {
                $this->adminPassword($hotaru);
            }
            
            // confirming forgotten password email
            $passconf = $hotaru->cage->get->getAlnum('passconf');
            $userid = $hotaru->cage->get->testInt('userid');
            
            if ($passconf && $userid) {
                if ($hotaru->currentUser->newRandomPassword($userid, $passconf)) {
                    $hotaru->message = $hotaru->lang['admin_email_password_conf_success'];
                    $hotaru->messageType = "green";
                } else {
                    $hotaru->message = $hotaru->lang['admin_email_password_conf_fail'];
                    $hotaru->messageType = "red";
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
    public function adminPassword($hotaru)
    {
        // Check email
        if (!$email_check = $hotaru->cage->post->testEmail('email')) { 
            $email_check = ''; 
            // login failed
            $hotaru->message = $hotaru->lang["admin_login_email_invalid"];
            $hotaru->messageType = "red";
            return false;
        } 
                    
        $valid_email = $hotaru->currentUser->validEmail($email_check, 'admin');
        $userid = $hotaru->currentUser->getUserIdFromEmail($valid_email);
        
        if ($valid_email && $userid) {
                //success
                $hotaru->currentUser->sendPasswordConf($userid, $valid_email);
                $hotaru->message = $hotaru->lang['admin_email_password_conf_sent'];
                $hotaru->messageType = "green";
                return true;
        } else {
                // login failed
                $hotaru->message = $hotaru->lang["admin_login_email_invalid"];
                $hotaru->messageType = "red";
                return false;
        }
    }
    
    
     /**
     * Admin login form
     */
    public function adminLoginForm($hotaru)
    {
        // Check username
        if (!$username_check = $hotaru->cage->post->testUsername('username')) {
            $username_check = '';
        } 
    
        // Check password
        if (!$password_check = $hotaru->cage->post->testPassword('password')) {
            $password_check = ''; 
        }
        
        // Check email (for forgotten password form)
        if (!$email_check = $hotaru->cage->post->testEmail('email')) {
            $email_check = ''; 
        }
        
        require_once(ADMIN_THEMES . ADMIN_THEME . 'admin_login.php');
    }
    
    
    /**
     * Set a 30-day cookie for the administrator
     *
     * @param string $username
     *
     * @return bool
     */
    public function setAdminCookie($username)
    {
        if (!$username) 
        { 
            echo $this->lang["admin_login_error_cookie"];
            return false;
        } 
        else 
        {
            $strCookie=base64_encode(
                        join(':', array($username, crypt($username, 22)))
            );
            
            // (2592000 = 60 seconds * 60 mins * 24 hours * 30 days.)
            $month = 2592000 + time();
            
            setcookie("hotaru_user", $username, $month, "/");
            setcookie("hotaru_key", $strCookie, $month, "/");
            
            return true;
        }
    }
            
     /**
     *  Checks if a cookie exists and if it belongs to an Admin user
     *
     * @return bool
     *
     * Note: This is only used if the Users plugin is inactive.
     */
    public function isAdminCookie($hotaru)
    {   
        // Check for a cookie. If present then the user goes through authentication
        if (!$hotaru->cage->cookie->testUsername('hotaru_user')) { return false; }
        if (!$hotaru->cage->cookie->keyExists('hotaru_key')) { return false; }
        
        $hotaru_user = $hotaru->cage->cookie->testUsername('hotaru_user');
        
        // authenticate...
        $user_info=explode(":", base64_decode($hotaru->cage->cookie->getRaw('hotaru_key')));
        
        if (($hotaru_user != $user_info[0]) || (crypt($user_info[0], 22) != $user_info[1])) { return false; }
        
        if (!$hotaru->isAdmin($hotaru_user)) { return false; }

        //success...
        return true;
    }
    
     /**
     * Admin logout
     *
     * @return true
     */
    public function adminLogout($hotaru)
    {
        $hotaru->currentUser->destroyCookieAndSession();
        header("Location: " . BASEURL);
        return true;
    }
}
?>