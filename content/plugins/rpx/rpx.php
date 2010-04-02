<?php
/**
 * name: RPX
 * description: Enables registration and login with Twitter, Facebook ,Google, etc.
 * version: 0.6
 * folder: rpx
 * class: RPX
 * hooks: install_plugin, theme_index_top, header_include, pre_close_body, user_signin_login_pre_login_form, userbase_logincheck, user_signin_pre_display_register_template, user_signin_register_pre_register_form, user_signin_register_password_check, user_signin_register_post_add_user, admin_sidebar_plugin_settings, admin_plugin_settings, users_account_pre_password_user_only, userbase_delete_user, user_signin_register_error_check, user_signin_navigation_logged_out
 * requires: users 1.1, user_signin 0.1
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


class RPX
{
    /* Note: Plugin classes are recreated every time a plugin hook is triggered. So if you want 
       persistent class properties, assign them to the $h object and reassign them to their
       properties in the constructor. */
    
    protected $apiKey       = "";
    protected $application  = "";
    protected $tokenUrl     = "";
    protected $language     = "en";
    protected $account       = "basic";
    protected $display       = "embed";

    /**
     * Build an object containing $db and $cage
     */
    public function __construct($h)
    {
        $rpx_settings = $h->getSerializedSettings('rpx');
        $this->application      = strtolower($rpx_settings['application']);
        $this->apiKey           = $rpx_settings['api_key'];
        $this->language         = $rpx_settings['language'];
        $this->account          = $rpx_settings['account'];
        $this->display          = $rpx_settings['display'];
        
        // determine where to return the user to after logging in:
        if (!$h->cage->get->keyExists('return')) {
            $host = $h->cage->server->sanitizeTags('HTTP_HOST');
            $uri = $h->cage->server->sanitizeTags('REQUEST_URI');
            $return = 'http://' . $host . $uri;
            // so we don't return to the login page...
            if (strpos($return, urlencode('login')) !== false) { $return = BASEURL; }
            $return = urlencode(htmlentities($return,ENT_QUOTES,'UTF-8'));
        } else {
            $return = urlencode($h->cage->get->testUri('return')); // use existing return parameter
        }
        
        if (strpos($return, urlencode(BASEURL)) === false) { $return = urlencode(BASEURL); }
  
        $this->tokenUrl         = urlencode(BASEURL . "index.php?page=register&amp;return=" . $return);
        //$this->tokenUrl     = $h->url(array('page'=>'register')); // doesn't seem to work :(
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


    /**
     * Install RPX
     */
    public function install_plugin($h)
    {
        if (!$h->db->column_exists('users', 'user_rpx_id')) {
            // add new user_rpx field
            $sql = "ALTER TABLE " . TABLE_USERS . " ADD user_rpx_id VARCHAR(255) NULL AFTER user_date";
            $h->db->query($h->db->prepare($sql));
        }
        
        if (!$h->db->column_exists('users', 'user_rpx')) {
            // add new user_rpx field
            $sql = "ALTER TABLE " . TABLE_USERS . " ADD user_rpx TEXT NULL AFTER user_rpx_id";
            $h->db->query($h->db->prepare($sql));
        }
        
        // Plugin settings
        $rpx_settings = $h->getSerializedSettings();
        if (!isset($rpx_settings['application'])) { $rpx_settings['application'] = ""; }
        if (!isset($rpx_settings['api_key'])) { $rpx_settings['api_key'] = ""; }
        if (!isset($rpx_settings['language'])) { $rpx_settings['language'] = "en"; }
        if (!isset($rpx_settings['account'])) { $rpx_settings['account'] = "basic"; }
        if (!isset($rpx_settings['display'])) { $rpx_settings['display'] = "embed"; }
        $h->updateSetting('rpx_settings', serialize($rpx_settings));
        
        // Clean up any leftover temp data from incomplete registrations in previous versions of this plugin:
        $sql = "DELETE FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s";
        $h->db->query($h->db->prepare($sql, 'rpx_identifier'));
    }
    
    
    /**
     * The JavaScript for the RPX pop up
     */
    public function pre_close_body($h)
    {
        $rpx_script = "\n<script src='https://rpxnow.com/openid/v2/widget' type='text/javascript'></script>
            <script type='text/javascript'>
            RPXNOW.language_preference = '" . $this->language . "';
            RPXNOW.overlay = true;
            </script>\n";
        
        // if display mode is "replace" use this script on every page
        if ($this->display == 'replace') { 
            echo $rpx_script; 
            return true;
        }
        
        $this_page = $h->pageName;
        
        // if display mode is "embed" use this script only on the account page
        if ($this->display == 'embed' && $this_page == 'account') {
                echo $rpx_script; 
                return true;
        }
        
        // if display mode is "overlay" use this script only on login, register or account
        if ($this->display == 'overlay') { 
            if (($this_page == 'login') || ($this_page == 'register') || ($this_page == 'account')) {
                echo $rpx_script; 
                return true;
            }
        }
        
        return false;
    }
    
    
    /**
     * Show a login with RPX link
     */
    public function user_signin_login_pre_login_form($h)
    {
        if ($this->display == 'overlay') {
            echo "<p class='rpx_login_reg_text'>" . $h->lang['rpx_login_sign_in_1'] . " ";
            echo "<a class='rpxnow' onclick='return false;' ";
            echo "href='https://" . $this->application . ".rpxnow.com/openid/v2/signin?token_url=" . $this->tokenUrl . "'>";
            echo $h->lang['rpx_login_sign_in_2'] . "</a></p>";
        } elseif (($this->display == 'embed') || ($this->display == 'replace')) {
            echo "<iframe id='rpx_embed' src='https://" . $this->application . ".rpxnow.com/openid/embed?token_url=" . $this->tokenUrl . "' ";
            echo "scrolling='no' frameBorder='no'></iframe>";
        }
    }
    
    
    /**
     * Show a register with RPX link
     */
    public function user_signin_register_pre_register_form($h)
    {
        if ($this->display == 'overlay') {
            echo "<p class='rpx_login_reg_text'><a class='rpxnow rpx_link' onclick='return false;' ";
            echo "href='https://" . $this->application . ".rpxnow.com/openid/v2/signin?token_url=" . $this->tokenUrl . "'>";
            echo $h->lang['rpx_register_sign_up'] . "</a></p>";
        } elseif ($this->display == 'embed') {
            echo "<iframe id='rpx_embed' src='https://" . $this->application . ".rpxnow.com/openid/embed?token_url=" . $this->tokenUrl . "' ";
            echo "scrolling='no' frameBorder='no'></iframe>";
        }
    }
    
    
    /**
     * Show a login with RPX link
     */
    public function theme_index_top($h)
    {
        // get the token if available. If not, stop executing this function
        // also stop here if there's no apiKey
        $token = $h->cage->post->sanitizeAll('token');
        if (!$token || !$this->apiKey) { return false; }
        
        // get the functions file:
        require_once(PLUGINS . 'rpx/libs/RpxFunctions.php');
        $rpxFuncs = new RpxFunctions();
        
        // get the profile:
        $rpx_profile = $rpxFuncs->getProfile($token, $this->apiKey);
        
        // If adding a provider to an existing non-RPX user...
        if ($h->isPage('account') && ($this->account == 'basic'))
        {
            // add the rpx ID and profile info (serialized) into the users table
            $sql = "UPDATE " . TABLE_USERS . " SET user_rpx_id = %s, user_rpx = %s WHERE user_id = %d";
            $h->db->query($h->db->prepare($sql, $rpx_profile['identifier'], serialize($rpx_profile), $h->currentUser->id));
            return false; // gets us out of here and loads the rest of the page.
        }
        
        // If adding another provider, map it then get out of here.
        if ($h->isPage('account') && ($this->account != 'basic'))
        {
            // update the database with this user's RPX identifier IF EMPTY:
            $sql = "UPDATE " . TABLE_USERS . " SET user_rpx_id = %s WHERE user_id = %d AND user_rpx_id IS NULL";
            $h->db->query($h->db->prepare($sql, $rpx_profile['identifier'], $h->currentUser->id));
            
            // update the database with this user's RPX profile IF EMPTY:
            $sql = "UPDATE " . TABLE_USERS . " SET user_rpx = %s WHERE user_id = %d AND user_rpx IS NULL";
            $h->db->query($h->db->prepare($sql, serialize($rpx_profile), $h->currentUser->id));
            
            // map this provider with the user's existing account:
            $status = $rpxFuncs->map($h->currentUser->id, $rpx_profile['identifier'], $this->apiKey);
            if($status == 'ok') {
                return false; // gets us out of here and loads the rest of the page.
            } else { 
                die("Error: Unable to map with RPX. Please contact a site administrator"); 
                exit; 
            } 
        }
        
        if (isset($rpx_profile['primaryKey']) && ($this->account != 'basic')) // PLUS & PRO ACCOUNTS ONLY
        {
            //get username from database for this primarykey
            $sql = "SELECT user_username FROM " . TABLE_USERS . " WHERE user_id = %d";
            $username = $h->db->get_var($h->db->prepare($sql, $rpx_profile['primaryKey']));
                    
            $login_result = $h->currentUser->loginCheck($h, $username, ''); // no password necessary
            if ($login_result) {
                //success
                $h->currentUser->name = $username;
                $remember = 1; // keep them logged in for 30 days (not optional)
                require_once(PLUGINS . 'user_signin/user_signin.php');
                $user_signin = new UserSignin();
                $user_signin->loginSuccess($h, $remember);
                $return = $h->cage->get->testUri('return');
                // so that we don't return to the register page:
                if (strpos($return, urlencode('register')) !== false) { $return = BASEURL; }
                if ($return) {
                    header("Location: " . $return);
                    exit;
                } else {
                    header("Location: " . BASEURL);
                    exit;
                }
            } 
        } 
        
        if ($rpx_profile['identifier'] && ($this->account == 'basic')) // BASIC ACCOUNTS
        {
            //get username from database for this identifier
            $sql = "SELECT user_username FROM " . TABLE_USERS . " WHERE user_rpx_id = %s";
            $username = $h->db->get_var($h->db->prepare($sql, $rpx_profile['identifier']));

            if ($username) {
                $login_result = $h->currentUser->loginCheck($h, $username, ''); // no password necessary
            }
            
            if (isset($login_result) && $login_result != false) {
                //success
                $h->currentUser->name = $username;
                $remember = 1; // keep them logged in for 30 days (not optional)
                require_once(PLUGINS . 'user_signin/user_signin.php');
                $user_signin = new UserSignin();
                $user_signin->loginSuccess($h, $remember);
                $return = $h->cage->get->testUri('return');
                // so that we don't return to the register page:
                if (strpos($return, urlencode('register')) !== false) { $return = BASEURL; }
                if ($return) {
                    header("Location: " . $return);
                    exit;
                } else {
                    header("Location: " . BASEURL);
                    exit;
                }
            } 
        } 

        $rpx_profile['preferredUsername'] = str_replace (" ", "", $rpx_profile['preferredUsername']); // strip spaces from username;
        
        // Let's temporarily store the user's profile info in the databse since that would be safer than embedding it in the registration form:
        
        // first find out if it already exists:
        $sql = "SELECT miscdata_value FROM " . TABLE_MISCDATA . " WHERE miscdata_value = %s";
        $ident_exists = $h->db->get_var($h->db->prepare($sql, $rpx_profile['identifier']));
        
        // insert it if it doesn't exist, update it if it does.
        if (!$ident_exists) {
            $sql = "INSERT INTO " . TABLE_MISCDATA . " SET miscdata_key = %s, miscdata_value = %s, miscdata_default = %s";
            $h->db->query($h->db->prepare($sql, 'rpx_identifier', $rpx_profile['identifier'], serialize($rpx_profile))); 
        } else {
            $sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_key = %s, miscdata_value = %s, miscdata_default = %s WHERE  miscdata_value = %s";
            $h->db->query($h->db->prepare($sql, 'rpx_identifier', $rpx_profile['identifier'], serialize($rpx_profile), $rpx_profile['identifier'])); 
        }
        
        // Assign $prx_profile to $h to be used in the registration form, 

        $h->vars['rpx_profile'] = $rpx_profile; 
        
        // set blank if not present:
        if (!isset($h->vars['rpx_profile']['email'])) {
            $h->vars['rpx_profile']['email'] = '';
        }
        if (!isset($h->vars['rpx_profile']['preferredUsername'])) {
            $h->vars['rpx_profile']['preferredUsername'] = '';
        }
        
        /*  falls through to theme_main_index in Users plugin, where we hook in with the function 
            "user_signin_pre_display_register_template" below */
    }
     
    
    /**
     * Override the Users registration page with the RPX one:
     *
     * @return bool
     */
    public function user_signin_pre_display_register_template($h)
    {
        // don't show this register form if there's no token in the url OR the register form from RPX's register template was not submitted
        if (!$h->cage->post->keyExists('token')
            && ($h->cage->post->testAlpha('rpx') != 'true')) { return false; }
        
        /* Removed this because the user sigin plugin already does a CSRF check, therefore removing this very token!
        if ($h->cage->post->testAlpha('rpx') == 'true') {
            if (!$h->csrf()) {
                $h->showMessage($h->lang["error_csrf"], 'red');
                return true; // need to return true so the plugin hook knows it's been triggered
            }
        } */
        
        $h->messages[$h->lang["rpx_registration_nearly_complete"]] = 'green';
        $h->displayTemplate('rpx_register', 'rpx'); // display the *RPX* register form
        return true;
    }


    /**
     * Alternative to the standard username/password check
     *
     * @param array $vars - first element is a username
     * @return bool
     */
    public function userbase_logincheck($h, $vars)
    {
        // if the traditional form has been submitted, return false and 
        // use the traditional login/password authentication:
        if ($h->cage->post->testPage('page') == 'login') { return false; }
        
        $username = $vars[0];
        
        $sql = "SELECT user_id, user_rpx FROM " . TABLE_USERS . " WHERE user_username = %s";
        $results = $h->db->get_row($h->db->prepare($h->db->prepare($sql, $username)));
        
        if (!$results) { return false; }
            
        if ($results->user_id && $results->user_rpx) { 
            return true;
        } else {
            return false;
        }

    }
    
    
    /**
     * To pass the password check during registration, we'll need a dummy password
     *
     * @return array
     */
    public function user_signin_register_password_check($h)
    {
        // if the traditional form has been submitted, return false and 
        // use the traditional registration method:
        if ($h->cage->post->testPage('page') == 'register') { return false; }
        
        $password = random_string(16); // generate a random 16 char password
        $passwords = array('password'=>$password, 'password2'=>$password);
        return $passwords;
    }
    
    
    /**
     * Add the user's RPX profile info to the database...
     *
     * @param array $vars - first element is the last insert id
     */
    public function user_signin_register_post_add_user($h, $vars)
    {
        $identifier = $h->cage->post->testUri('identifier');
        if (!$identifier) { return false; }
        
        // get the rpx_profile data which we temporarily stored in the miscdata table
        $sql = "SELECT miscdata_default FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s AND miscdata_value = %s";
        $rpx_profile = $h->db->get_var($h->db->prepare($sql, 'rpx_identifier', $identifier));
        
        if (!$rpx_profile) { echo "Error: No rpx profile information in RPX userbase_add_user"; exit; }
        
        $last_insert_id = $vars[0]; // this is the user's id and RPX's primary key
        
        // add the rpx profile info (serialized) into the users table
        $sql = "UPDATE " . TABLE_USERS . " SET user_rpx = %s, user_rpx_id = %s WHERE user_id = %d";
        $h->db->query($h->db->prepare($sql, $rpx_profile, $identifier, $last_insert_id));
        
        // remove the data we stored temporarily in the misc_data:
        $sql = "DELETE FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s AND miscdata_value = %s";
        $h->db->query($h->db->prepare($sql, 'rpx_identifier', $identifier));
        
        // extract the info we need to map this user on RPX:
        $rpx_profile = unserialize($rpx_profile);
        
        // get the functions file:
        require_once(PLUGINS . 'rpx/libs/RpxFunctions.php');
        $rpxFuncs = new RpxFunctions($this->hotaru, $this->folder);
        
        // map the user:
        if ($this->account != 'basic') {
            $status = $rpxFuncs->map($last_insert_id, $rpx_profile['identifier'], $this->apiKey);
            if($status != 'ok') { die("Error: Unable to map with RPX. Please contact a site administrator"); exit; } 
        }
    }
    
    
    /**
     * Show associated providers on user's Account page
     */
    public function users_account_pre_password_user_only($h)
    {
        $output = "<div class='users_account_rpx'>\n";
        $output .= "<p id='rpx_providers_header'>" . $h->lang['rpx_account_providers'] . "</p>\n";
        $output .= "<p id='rpx_providers_desc'>" . $h->lang['rpx_account_providers_list'] . "</p>\n";
        $output .= "<ul id='rpx_user_ids'>\n";
                    
        // get the functions file:
        require_once(PLUGINS . 'rpx/libs/RpxFunctions.php');
        $rpxFuncs = new RpxFunctions($this->hotaru, $this->folder);
        
        $no_providers = false; // a simple flag for Basic accounts
        
        if ($this->account == 'basic') { 
            // see if there's an existing rpx identifier
            $sql = "SELECT user_id, user_rpx FROM " . TABLE_USERS . " WHERE user_id = %s";
            $result = $h->db->get_row($h->db->prepare($sql, $h->currentUser->id));
            if ($result->user_rpx) {
                // turn the RPX profile back into an array:
                $rpx_profile = unserialize($result->user_rpx);
                $output .= "<li>&raquo " . get_domain($rpx_profile['identifier']) . "</li>";
            } else {
                $output .= "<li>" . $h->lang['rpx_account_no_providers'] . "</li>";
                $no_providers = true;
            }
        }
                
        if ($this->account != 'basic') { 
            $data = $rpxFuncs->get_user_mappings($h->currentUser->id, $this->apiKey);
            if ($data['identifiers']) {
                foreach ($data['identifiers'] as $id) {
                    $output .= "<li>&raquo " . get_domain($id) . "</li>";
                }
            } else {
                $output .= "<li>" . $h->lang['rpx_account_no_providers'] . "</li>";
            }
        }
        
        $output .= "</ul>\n";
        
        if (($this->account != 'basic') || $no_providers) {
            $this->tokenUrl = urlencode(BASEURL . "index.php?page=account&user=" . $h->currentUser->name);
            
            $output .= "<p id='rpx_providers_add'><a class='rpxnow' onclick='return false;' ";
            $output .= "href='https://" . $this->application . ".rpxnow.com/openid/v2/signin?token_url=" . $this->tokenUrl . "'>";
            $output .= $h->lang['rpx_account_add_provider'] . "</a><br />\n";
            $output .= $h->lang['rpx_account_add_provider_instruct'] . "</p>\n";
        }
        
        $output .= "</div>";
        
        echo $output;
    }
    
    
    /**
     * Unmap a user from RPX when deleting them
     *
     * @param array $vars assoc. array containing "user_id"
     */
    public function userbase_delete_user($h, $vars)
    {
        $sql = "SELECT user_rpx FROM " . TABLE_USERS . " WHERE user_id = %d";
        $rpx_profile = $h->db->get_var($h->db->prepare($sql, $vars['user_id']));
        
        if (!$rpx_profile) { return false; }
        
        // turn the RPX profile back into an array:
        $rpx_profile = unserialize($rpx_profile);
        
        // get the functions file:
        require_once(PLUGINS . 'rpx/libs/RpxFunctions.php');
        $rpxFuncs = new RpxFunctions($this->hotaru, $this->folder);
        $rpxFuncs->unmap($vars['user_id'], $rpx_profile['identifier'], $this->apiKey);
    }


    /**
     * Check if a user already exists under a different provider
     */
    public function user_signin_register_error_check($h)
    {
        $email = $h->currentUser->email;
        
        // see if there's an existing rpx identifier
        $sql = "SELECT user_id, user_rpx FROM " . TABLE_USERS . " WHERE user_email = %s";
        $result = $h->db->get_row($h->db->prepare($sql, $email));
        
        if (!$result) { return false; }
        
        if ($result->user_rpx) // THIS USER HAS PREVIOUSLY REGISTERED WITH A DIFFERENT PROVIDER
        {
            $rpx_profile = unserialize($result->user_rpx);
            $error_msg = $h->lang['rpx_users_register_exists_error_1_rpx'] . " " . $rpx_profile['providerName'] . ".<br/>";
            $error_msg .= $h->lang['rpx_users_register_exists_error_2_rpx'];
            
            if ($this->account != 'basic') {
                $error_msg .= "<br /> <br/>" . $h->lang['rpx_users_register_exists_error_3_rpx'];
            }
            
            $h->message = $error_msg; 
            $h->messageType = 'green'; // green because it's a useful prompt rather than an error
            $h->vars['rpx_already_exists'] = true; // this will prevent us seeing other error messages and the form
            return true;
        }
        elseif ($result->user_id) // THIS USER HAS PREVIOUSLY REGISTERED WITH A LOGIN/PASSWORD COMBO
        {
            $error_msg = $h->lang['rpx_users_register_exists_error_1_password'] . "<br/><br />";
            $error_msg .= "<a href='" . $h->url(array('page'=>'login')) . "'>" . $h->lang['rpx_users_register_exists_error_2_password'] . "</a>";
            $error_msg .= "<br /> <br/>" . $h->lang['rpx_users_register_exists_error_3_password'];
            
            $h->message = $error_msg; 
            $h->messageType = 'green'; // green because it's a useful prompt rather than an error
            $h->vars['rpx_already_exists'] = true; // this will prevent us seeing other error messages and the form
            return true;
        } 
    }
    
    
    /**
     * Show sign in link if mode is "replace"
     *
     * @param return bool
     */
    public function user_signin_navigation_logged_out($h)
    {
        if ($this->display != 'replace') { return false; }
        
        echo "<li><a class='rpxnow' onclick='return false;' ";
        echo "href='https://" . $this->application . ".rpxnow.com/openid/v2/signin?token_url=" . $this->tokenUrl . "'>";
        echo $h->lang["rpx_navigation_signin"] . "</a></li>\n";
        
        return true;
    }

}
