<?php
/**
 * Used in order to verify the administrator before accessing Admin
 *
 * This is only used if the Users plugin is inactive
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
 
 /**
 * Admin login
 * 
 * @return bool
 */
function admin_login()
{
    global $cage, $lang, $current_user, $hotaru;
    
    // Check username
    if (!$username_check = $cage->post->testUsername('username')) { 
        $username_check = ""; 
    } 
    
    // Check password
    if (!$password_check = $cage->post->testPassword('password')) {
        $password_check = ""; 
    }
                
    if ($username_check != "" || $password_check != "") 
    {
        $login_result = $current_user->login_check($username_check, $password_check);
        
        if ($login_result) {
                //success
                set_admin_cookie($username_check);
                $current_user->username = $username_check;
                $current_user->get_user_basic(0, $username_check);
                $current_user->logged_in = true;
                $current_user->update_user_lastlogin();
                return true;
        } else {
                // login failed
                $hotaru->message = $lang["admin_login_failed"];
                $hotaru->message_type = "red";
        }
    } 
    else 
    {
        if ($cage->post->keyExists('login_attempted')) {
            $hotaru->message = $lang["admin_login_failed"];
            $hotaru->message_type = "red";
        }
        $username_check = "";
        $password_check = "";
    }
    
    return false;
}

 /**
 * Admin login form
 */
function admin_login_form()
{
    global $cage, $lang, $hotaru;

    // Check username
    if (!$username_check = $cage->post->testUsername('username')) {
        $username_check = "";
    } 

    // Check password
    if (!$password_check = $cage->post->testPassword('password')) {
        $password_check = ""; 
    }
    
    require_once(ADMIN_THEMES . ADMIN_THEME . 'login.php');
}


/**
 * Set a 30-day cookie for the administrator
 *
 * @param string $username
 *
 * @return bool
 */
function set_admin_cookie($username)
{
    global $lang;

    if (!$username) 
    { 
        echo $lang["admin_login_error_cookie"];
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
function is_admin_cookie()
{
    global $cage, $current_user;
    
    // Check for a cookie. If present then the user goes through authentication
    if (!$hotaru_user = $cage->cookie->testUsername('hotaru_user'))
    {
        return false;
        die();
    }
    else 
    {
        // authenticate...
        if (($hotaru_user) && ($cage->cookie->keyExists('hotaru_key')))
        {
            $user_info=explode(":", base64_decode(
                                    $cage->cookie->getRaw('hotaru_key'))
            );
            
            if (($hotaru_user == $user_info[0]) 
                && (crypt($user_info[0], 22) == $user_info[1])) 
            {
                if (!$current_user->is_admin($hotaru_user)) {
                    return false;
                    die();
                } else {
                    //success...
                    return true;
                }
            }
        } 
        else 
        {
            return false;
            die();    
        }
    }
}

 /**
 * Admin logout
 *
 * @return true
 */
function admin_logout()
{
    global $current_user;
    
    $current_user->destroy_cookie_and_session();
    header("Location: " . BASEURL);
    return true;
}

?>