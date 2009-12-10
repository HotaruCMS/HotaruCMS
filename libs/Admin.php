<?php
/**
 * Admin related functions
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

class Admin
{
    public $db;                             // database object
    public $cage;                           // Inspekt object
    public $hotaru;                         // Hotaru object
    public $lang            = array();      // stores language file content
    public $plugins;                        // PluginFunctions object
    public $current_user;                   // UserBase object
    
    protected $isAdmin      = true;         // flag to tell if we are in Admin or not
    protected $sidebars     = true;         // flag to determine whther to show sidebars or not
    
    /**
     * Constructor - make an Admin object
     *
     * @param string $entrance - usually admin or blank
     */
    public function __construct($entrance = '')
    {
        require_once(LIBS . 'Hotaru.php');
        $hotaru = new Hotaru('admin');
        $this->hotaru       = $hotaru;
        $this->db           = $hotaru->db;
        $this->cage         = $hotaru->cage;
        $this->lang         = &$hotaru->lang;    // reference to main lang array
        $this->plugins      = $hotaru->plugins;
        $this->current_user = $hotaru->current_user;
        
        // We don't need to fill the object with anything other than the plugin folder name at this time:
        if (isset($folder)) { 
            $this->folder = $folder; 
        }

        if ($entrance != 'admin') { return false; } 

        $this->adminInit($entrance);
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
     *              REGULAR METHODS
     * ********************************************************** */


    /**
     * Admin constructor
     */
    public function adminInit($entrance = '')
    {
        $page = $this->cage->get->testPage('page');    // check with "get";
        if (!$page) { 
            // check with "post" - used in admin_login_form().
            $page = $this->cage->post->testPage('page'); 
        }
        
        
        // Authenticate the admin if the Users plugin is INACTIVE:
        if (!$this->plugins->isActive('users'))
        {
            if (($page != 'admin_login') && !$this->isAdminCookie())
            {
                header('Location: ' . BASEURL . 'admin_index.php?page=admin_login');
            }
        }
        
        
        // Authenticate the admin if the Users plugin is ACTIVE:
        if (isset($this->current_user) && $this->plugins->isActive('users'))
        {
            // This first condition happens when the Users plugin is activated 
            // and there's no cookie for the Admin yet.
            if (($this->current_user->name == "") && $this->plugins->isActive('users')) 
            {
                header('Location: ' . BASEURL . 'index.php?page=login');
                die; exit;
            } 
            elseif ($this->current_user->getPermission('can_access_admin') != 'yes') 
            {
                // maybe the user has permission to access a specific plugin settings page?
                $plugin = $this->cage->get->testAlnumLines('plugin');
                if ($plugin && ($page == "plugin_settings")) {
                    $permission = "can_" . $plugin . "_settings";
                    if ($this->current_user->getPermission($permission) == 'yes') {
                        $this->sidebars = false; // hide sidebars
                        $this->displayAdminTemplate('index');
                        die(); exit;
                    }
                }
                
                // User doesn't have permission to access Admin
                $this->hotaru->messages['Access Denied'] = 'red';
                $this->displayAdminTemplate('access_denied');
                die(); exit;
            }
        }
        
        // If we get this far, we know that the user has admin access.
        
        $this->plugins->pluginHook('admin_index');
        
        switch ($page) {
            case "admin_login":
                $this->sidebars = false;
                $this->adminLogin();
                break;
            case "admin_logout":
                $this->adminLogout();
                break;
            case "admin_account":
                // Nothing special to do...
                break;
            case "settings":
                // Nothing special to do...
                break;
            case "maintenance":
                // Nothing special to do...
                break;
            case "blocked_list":
                // Nothing special to do...
                break;
            case "plugins":
                $this->plugins();
                break;
            case "plugin_settings":
                // Nothing special to do...
                break;
            default:
                break;
        }
        
        // Display the main theme's index.php template
        $this->displayAdminTemplate('index');
    }
    
    
     /**
     * Call functions based on user actions in Plugin Management
     */
    public function plugins()
    {
        $action = $this->cage->get->testAlnumLines('action');
        $pfolder = $this->cage->get->testAlnumLines('plugin');
        $order = $this->cage->get->testAlnumLines('order');
        
        $this_plugin = new PluginFunctions($this->hotaru, $pfolder);
        
        switch ($action) {
            case "activate":
                $this_plugin->activateDeactivate(1);
                break;
            case "deactivate":
                $this_plugin->activateDeactivate(0);
                break;    
            case "activate_all":
                $this_plugin->activateDeactivateAll(1);
                break;
            case "deactivate_all":
                $this_plugin->activateDeactivateAll(0);
                break;    
            case "uninstall_all":
                $this_plugin->uninstallAll();
                break;    
            case "install":
                $this_plugin->install();
                break;
            case "uninstall":
                $this_plugin->uninstall();
                break;    
            case "orderup":
                $this_plugin->pluginOrder($order, "up");
                break;    
            case "orderdown":
                $this_plugin->pluginOrder($order, "down");
                break;    
            default:
                // do nothing...
                return false;
                break;
        }
    
        return true;
    }


    /**
     * Display admin template
     *
     * @param string $page page name
     * @param array $hotaru - usually the $admin object
     * @param string $plugin optional plugin name
     * @param bool $include_once true or false
     */
    public function displayAdminTemplate($page = '', $admin = NULL, $plugin = '', $include_once = true)
    {
        // Note: This $hotaru isn't necessarily the whole object, some plugins might pass
        // $db or $lang into this parameter instead. Therefore, we need the $hotaru parameter.
        
        // if no $hotaru, provide it:
        if (!isset($admin) || !is_object($admin)) { $admin = $this; }
        
        // if no plugin folder, provide it:
        if (!$plugin) { $plugin = $this->plugins->folder; }
        
        $page = $page . '.php';
                
        /* 
            1. Check the custom admin theme
            2. Check the default admin theme
            3. Check the plugin folder
            4. Show the 404 Not Found page from the admin theme
            5. Show the 404 Not Found page from "admin_themes" folder
        */
        if (file_exists(ADMIN_THEMES . ADMIN_THEME . $page))
        {
            if (!$include_once) {
                // Special case, do not restrict to include once.
                include(ADMIN_THEMES . ADMIN_THEME . $page);
            } else {
                include_once(ADMIN_THEMES . ADMIN_THEME . $page);
            }
        } 
        elseif (file_exists(ADMIN_THEMES . 'admin_default/' . $page))
        {
            if (!$include_once) {
                // Special case, do not restrict to include once.
                include(ADMIN_THEMES . 'admin_default/' . $page);
            } else {
                include_once(ADMIN_THEMES . 'admin_default/' . $page);
            }
        }
        elseif ($plugin != '' && file_exists(PLUGINS .  $plugin . '/templates/' . $page))
        {
                if (!$include_once) {
                    // Special case, do not restrict to include once.
                    include(PLUGINS . $plugin . '/templates/' . $page);
                } else {
                    include_once(PLUGINS . $plugin . '/templates/' . $page);
                }
                return true;
                die();
        }
        elseif (file_exists(ADMIN_THEMES . ADMIN_THEME . '404error.php')) 
        {
            include_once(ADMIN_THEMES . ADMIN_THEME . '404error.php');
        }
        else
        {
            include_once(ADMIN_THEMES . '404error.php');
        }
    }
    
        
    /**
     * Returns an announcement for display at the top of Admin
     *
     * @return array|false - array of announcements
     */
    public function checkAdminAnnouncements()
    {
        // Check if the install file has been deleted:
        
        $announcements = array();
        
        // Check if install file has been deleted
        $filename = INSTALL . 'install.php';
        if (file_exists($filename)) {
            array_push($announcements, $this->lang['admin_announcement_delete_install']);
        } 
        
        // Site is currently undergoing maintenance
        if (SITE_OPEN == "false") {
            array_push($announcements, $this->lang['admin_announcement_site_closed']);
        }
        
        // Please enter a site email address
        if (SITE_EMAIL == "admin@mysite.com") {
            array_push($announcements, $this->lang['admin_announcement_change_site_email']);    
        } 
        
        // "Go to Plugin Management to enable some plugins"
        if (!$this->plugins->numActivePlugins()) {
            array_push($announcements, $this->lang['admin_announcement_plugins_disabled']);    
        }
        
        // Plugins can add announcements with this:
        $this->hotaru->vars['admin_announcements'] = $announcements;
        $this->plugins->pluginHook('admin_announcements');
        $announcements = $this->hotaru->vars['admin_announcements'];
        
        if (!is_array($announcements)) {
            return false;
        } else {
            return $announcements;
        }
    }


    /**
     * Returns the value for a given setting
     *
     * @param string $setting
     * @return mixed|false
     */
    public function getAdminSetting($setting = '')
    {
        $sql = "SELECT settings_value FROM " . TABLE_SETTINGS . " WHERE (settings_name = %s)";
        $value = $this->db->get_var($this->db->prepare($sql, $setting));
        if ($value) { return $value; } else { return false; }
    }
    

    /**
     * Returns all setting-value pairs
     *
     * @return array|false
     */
    public function getAllAdminSettings()
    {
        $sql = "SELECT * FROM " . TABLE_SETTINGS;
        $results = $this->db->get_results($this->db->prepare($sql));
        if ($results) { return $results; } else { return false; }
    }
    
    
    /**
     * Determine if a setting already exists
     *
     * Note: The actual value is ignored
     *
     * @param string $setting
     * @return mixed|false
     */
    public function adminSettingExists($setting = '')
    {
        $sql = "SELECT settings_name FROM " . TABLE_SETTINGS . " WHERE (settings_name = %s)";
        $returned_setting = $this->db->get_var($this->db->prepare($sql, $setting));
        if ($returned_setting) { return $returned_setting; } else { return false; }
    }    
    
    /**
     * Update an admin setting
     *
     * @param string $setting
     * @param string $value
     */
    public function adminSettingUpdate($setting = '', $value = '')
    {
        $exists = $this->adminSettingExists($setting);
        
        if (!$exists) {
            $sql = "INSERT INTO " . TABLE_SETTINGS . " (settings_name, settings_value, settings_updateby) VALUES (%s, %s, %d)";
            $this->db->query($this->db->prepare($sql, $setting, $value, $this->current_user->id));
        } else {
            $sql = "UPDATE " . TABLE_SETTINGS . " SET settings_name = %s, settings_value = %s, settings_updateby = %d WHERE (settings_name = %s)";
            $this->db->query($this->db->prepare($sql, $setting, $value, $this->current_user->id, $setting));
        }
    }


    /**
     * Delete rows from settings that match the given setting
     *
     * @param string $setting
     */    
    public function adminSettingsRemove($setting = '')
    {
        $sql = "DELETE FROM " . TABLE_SETTINGS . " WHERE admin_setting = %s";
        $this->db->query($this->db->prepare($sql, $setting));
    }
    
    
    /**
     * Open or close the site for maintenance
     *
     * @param string $switch - 'open' or 'close'
     */
    public function openCloseSite($switch = 'open')
    {
        if ($switch == 'open') { 
            // open
            $sql = "UPDATE " . TABLE_SETTINGS . " SET settings_value = %s WHERE settings_name = %s";
            $this->db->query($this->db->prepare($sql, 'true', 'SITE_OPEN'));
            $this->hotaru->message = $this->lang['admin_maintenance_site_opened'];
            $this->hotaru->messageType = 'green';
        } else {
            //close
            $sql = "UPDATE " . TABLE_SETTINGS . " SET settings_value = %s WHERE settings_name = %s";
            $this->db->query($this->db->prepare($sql, 'false', 'SITE_OPEN'));
            $this->hotaru->message = $this->lang['admin_maintenance_site_closed'];
            $this->hotaru->messageType = 'green';
        }
        
        $this->hotaru->showMessage();
    }
    
    
    /**
     * Calls the delete_files function, then displays a message.
     *
     * @param string $folder - path to the cache folder
     */
    public function clearCache($folder, $msg = true)
    {
        $success = $this->deleteFiles(CACHE . $folder);
        if (!$msg) { return true; }
        if ($success) {
            $this->hotaru->message = $this->lang['admin_maintenance_clear_cache_success'];
            $this->hotaru->messageType = 'green';
        } else {
            $this->hotaru->message = $this->lang['admin_maintenance_clear_cache_failure'];
            $this->hotaru->messageType = 'red';    
        }
        $this->hotaru->showMessage();
    }


    /**
     * Delete all files in the specified directory except placeholder.txt
     *
     * @param string $dir - path to the cache folder
     * @return bool
     */    
    public function deleteFiles($dir)
    {
        $handle=opendir($dir);
    
        while (($file = readdir($handle))!==false) {
            if ($file != 'placeholder.txt') {
                if (@unlink($dir.'/'.$file)) {
                    $success = true;
                } else {
                    $success = false;
                }
            }
        }
        
        closedir($handle);
        
        return $success;
    }
    
    
    /**
     * Process the settings form
    */    
    public function settings()
    {
        $loaded_settings = $this->getAllAdminSettings();    // get all admin settings from the database
        
        $error = 0;
        
        if ($this->cage->post->noTags('settings_update')  == 'true') {
        
            // if either the login or forgot password form is submitted, check the CSRF key
            $csrf = new csrf($this->db);
            $csrf->action = $this->hotaru->getPagename();
            $safe =  $csrf->checkcsrf($this->cage->post->testAlnum('token'));
            if (!$safe) { $error = 1; }
        
            foreach ($loaded_settings as $setting_name) {
                if ($this->cage->post->keyExists($setting_name->settings_name)) {
                    $setting_value = $this->cage->post->noTags($setting_name->settings_name);
                    if ($safe && $setting_value && $setting_value != $setting_name->settings_value) {
                        $this->adminSettingUpdate($setting_name->settings_name, $setting_value);
    
                    } else {
                        if (!$setting_value) {
                            // empty value 
                            $error = 1; 
                        } else { 
                            // No change to the value
                            $error = 0; 
                        }
                    }
                } else {
                    // error, setting empty.
                    $error = 1;
                }
            }
            
            if ($error == 0) {
                $this->hotaru->message = $this->lang['admin_settings_update_success'];
                $this->hotaru->messageType = 'green';
                $this->hotaru->showMessage();        
            } else {
                $this->hotaru->message = $this->lang['admin_settings_update_failure'];
                $this->hotaru->messageType = 'red';
                $this->hotaru->showMessage();
            }
        }    
        
        $loaded_settings = $this->getAllAdminSettings();
        
        // the above CSRF check clears the existing token if valid, so we need to generate a new one
        // for the form that is still on the page:
        if (!$this->hotaru->token) {
            $csrf = new csrf($this->db);  
            $csrf->action = $this->hotaru->getPagename();
            $csrf->life = 10; 
            $this->hotaru->token = $csrf->csrfkey();
        }
        
        return $loaded_settings;
    }
    
    
    /**
     * List all plugins with settings
     *
     * @return array|false
     */
    public function listPluginSettings()
    {
        $plugin_settings = array();
        $sql = "SELECT DISTINCT plugin_folder FROM " . DB_PREFIX . "pluginsettings";
        $results = $this->db->get_results($this->db->prepare($sql));
    
        if (!$results) { return false; } 
        
        foreach ($results as $item) {
            array_push($plugin_settings, $item->plugin_folder);
        }
        return $plugin_settings; 
    }
    
    
    /**
     * List all plugin created tables
     */
    public function listPluginTables()
    {
        // These should match the tables created in the install script.
        $core_tables = array(
            'hotaru_settings',
            'hotaru_miscdata',
            'hotaru_users',
            'hotaru_plugins',
            'hotaru_pluginsettings',
            'hotaru_pluginhooks',
            'hotaru_blocked',
            'hotaru_tokens'
        );
        
        $plugin_tables = array();
            
        $this->db->select(DB_NAME);
        
        if (!$this->db->get_col("SHOW TABLES",0)) { return $plugin_tables; }
        
        foreach ( $this->db->get_col("SHOW TABLES",0) as $table_name )
        {
            if (!in_array($table_name, $core_tables)) {
                array_push($plugin_tables, $table_name);
            }
        }
        
        return $plugin_tables;
    }
    
    
    /**
     * Optimize all database tables
     */
    public function optimizeTables()
    {
        $this->db->select(DB_NAME);
        
        foreach ( $this->db->get_col("SHOW TABLES",0) as $table_name )
        {
            $this->db->query("OPTIMIZE TABLE " . $table_name);
        }
        
        $this->hotaru->message = $this->lang['admin_maintenance_optimize_success'];
        $this->hotaru->messageType = 'green';
        $this->hotaru->showMessage();
    }
    
    
    /**
     * Empty plugin database table
     *
     * @param string $table_name - table to empty
     */
    public function emptyTable($table_name, $msg = true)
    {
        $this->db->query("TRUNCATE TABLE " . $table_name);
        
        if ($msg) {
            $this->hotaru->message = $this->lang['admin_maintenance_table_emptied'];
            $this->hotaru->messageType = 'green';
            $this->hotaru->showMessage();
        }
    }
    
    
    /**
     * Delete plugin database table
     *
     * @param string $table_name - table to drop
     */
    public function dropTable($table_name, $msg = true)
    {
        $this->db->query("DROP TABLE " . $table_name);
        
        if ($msg) {
            $this->hotaru->message = $this->lang['admin_maintenance_table_deleted'];
            $this->hotaru->messageType = 'green';
            $this->hotaru->showMessage();
        }
    }
    
    
    /**
     * Remove plugin settings
     *
     * @param string $plugin_name - settings to remove
     */
    public function removeSettings($plugin_name, $msg = true)
    {
        $sql = "DELETE FROM " . DB_PREFIX . "pluginsettings WHERE plugin_folder = %s";
        $this->db->get_results($this->db->prepare($sql, $plugin_name));
    
        if ($msg) {
            $this->hotaru->message = $this->lang['admin_maintenance_settings_removed'];
            $this->hotaru->messageType = 'green';
            $this->hotaru->showMessage();
        }
    }
    
    
     /**
     * Admin login
     * 
     * @return bool
     */
    public function adminLogin()
    {
        // Check username
        if (!$username_check = $this->cage->post->testUsername('username')) { 
            $username_check = ''; 
        } 
        
        // Check password
        if (!$password_check = $this->cage->post->testPassword('password')) {
            $password_check = ''; 
        }
        
        if ($this->cage->post->keyExists('login_attempted') || $this->cage->post->keyExists('forgotten_password')) {
            // if either the login or forgot password form is submitted, check the CSRF key
            $csrf = new csrf($this->db);
            $csrf->action = $this->hotaru->getPagename();
            $safe =  $csrf->checkcsrf($this->cage->post->testAlnum('token'));
            if (!$safe) {
                $this->hotaru->messages[$this->lang['error_csrf']] = 'red';
                return false;
            }
        }

        if ($username_check != '' || $password_check != '') 
        {
            $login_result = $this->current_user->loginCheck($username_check, $password_check);

            if ($login_result) {
                    //success
                    $this->setAdminCookie($username_check);
                    $this->current_user->name = $username_check;
                    $this->current_user->getUserBasic(0, $username_check);
                    $this->current_user->loggedIn = true;
                    $this->current_user->updateUserLastLogin();
                    $this->sidebars = true;
                    return true;
            } else {
                    // login failed
                    $this->hotaru->message = $this->lang["admin_login_failed"];
                    $this->hotaru->messageType = "red";
            }
        } 
        else 
        {
            if ($this->cage->post->keyExists('login_attempted')) {
                $this->hotaru->message = $this->lang["admin_login_failed"];
                $this->hotaru->messageType = "red";
            }
            $username_check = '';
            $password_check = '';
            
            // forgotten password request
            if ($this->cage->post->keyExists('forgotten_password')) {
                $this->adminPassword();
            }
            
            // confirming forgotten password email
            $passconf = $this->cage->get->getAlnum('passconf');
            $userid = $this->cage->get->testInt('userid');
            
            if ($passconf && $userid) {
                if ($this->current_user->newRandomPassword($userid, $passconf)) {
                    $this->hotaru->message = $this->lang['admin_email_password_conf_success'];
                    $this->hotaru->messageType = "green";
                } else {
                    $this->hotaru->message = $this->lang['admin_email_password_conf_fail'];
                    $this->hotaru->messageType = "red";
                }
            }
        }
        
        // the above CSRF check clears the existing token if valid, so we need to generate a new one
        // for the form that is still on the page:
        if (!$this->hotaru->token) {
            $csrf = new csrf($this->db);  
            $csrf->action = $this->hotaru->getPagename();
            $csrf->life = 10; 
            $this->hotaru->token = $csrf->csrfkey();
        }
        
        return false;
    }
    

     /**
     * Admin password forgotten
     * 
     * @return bool
     */
    public function adminPassword()
    {
        // Check email
        if (!$email_check = $this->cage->post->testEmail('email')) { 
            $email_check = ''; 
            // login failed
            $this->hotaru->message = $this->lang["admin_login_email_invalid"];
            $this->hotaru->messageType = "red";
            return false;
        } 
                    
        $valid_email = $this->current_user->validEmail($email_check, 'admin');
        $userid = $this->current_user->getUserIdFromEmail($valid_email);
        
        if ($valid_email && $userid) {
                //success
                $this->current_user->sendPasswordConf($userid, $valid_email);
                $this->hotaru->message = $this->lang['admin_email_password_conf_sent'];
                $this->hotaru->messageType = "green";
                return true;
        } else {
                // login failed
                $this->hotaru->message = $this->lang["admin_login_email_invalid"];
                $this->hotaru->messageType = "red";
                return false;
        }
    }
    
    
     /**
     * Admin login form
     */
    public function adminLoginForm($admin)
    {
        // Check username
        if (!$username_check = $this->cage->post->testUsername('username')) {
            $username_check = "";
        } 
    
        // Check password
        if (!$password_check = $this->cage->post->testPassword('password')) {
            $password_check = ""; 
        }
        
        // Check email (for forgotten password form)
        if (!$email_check = $this->cage->post->testEmail('email')) {
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
    public function isAdminCookie()
    {   
        // Check for a cookie. If present then the user goes through authentication
        if (!$hotaru_user = $this->cage->cookie->testUsername('hotaru_user'))
        {
            return false;
            die();
        }
        else 
        {
            // authenticate...
            if (($hotaru_user) && ($this->cage->cookie->keyExists('hotaru_key')))
            {
                $user_info=explode(":", base64_decode(
                                        $this->cage->cookie->getRaw('hotaru_key'))
                );
                
                if (($hotaru_user == $user_info[0]) 
                    && (crypt($user_info[0], 22) == $user_info[1])) 
                {
                    if (!$this->current_user->isAdmin($hotaru_user)) {
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
    public function adminLogout()
    {
        $this->current_user->destroyCookieAndSession();
        header("Location: " . BASEURL);
        return true;
    }
    
    
     /**
     * Display Hotaru forums feed on Admin front page
     *
     * @return string Returns the html output for the feed 
     */
    public function adminNews($max_items = 10, $items_with_content = 3, $max_chars = 300)
    {
        $feedurl = 'http://feeds2.feedburner.com/hotarucms';
        $feed = $this->hotaru->newSimplePie($feedurl);
        $feed->init();
            
        $output = "";
        $item_count = 0;
            
        if ($feed->data) { 
            foreach ($feed->get_items() as $item)
            {
                $output .= "<div>";
                
                // Title
                $output .= "<a href='" . $item->get_permalink() . "'>" . $item->get_title() . "</a><br />";
                
                if ($item_count < $items_with_content)
                {
                    // Posted by
                    $output .= "<small>" . $this->lang["admin_news_posted_by"] . " ";
                    
                    foreach ($item->get_authors() as $author) 
                    {
                        $output .= $author->get_name(); 
                    }
                    
                    // Date
                    $output .= " " . $this->lang["admin_news_on"] . " " . $item->get_date('j F Y');
                    $output .= "</small><br />";
                    
                    // Content
                    $output .= substr(strip_tags($item->get_content()), 0, $max_chars);
                    $output .= "... ";
                    
                    // Read more
                    $output .= "<small><a href='" . $item->get_permalink() . "' title='" . $item->get_title() . "'>[" . $this->lang["admin_news_read_more"] . "]</a>";
                    $output .= "</small>";
                }
                
                $output .= "</div>";
                if ($item_count < $items_with_content) { $output .="<br />"; }
                if ($item_count == ($items_with_content - 1)) { $output .= "<h3>" . $this->lang["admin_news_more_threads"] . "</h3>"; }
                
                $item_count++;
                if ($item_count >= $max_items) { break;}
            }
        }
        
        echo $output;
    }


    /* *************************************************************
     *              BLOCKED LIST
     * ********************************************************** */


     /**
     * Prepare a list of blocked items for the Admin "Blocked List" page
     */
    public function buildBlockedList()
    {
        $safe = ''; // CSRF flag
        
        if ($this->cage->post->keyExists('type')) {
            $csrf = new csrf($this->db);
            $csrf->action = $this->hotaru->getPagename();
            $safe =  $csrf->checkcsrf($this->cage->post->testAlnum('token'));
            if (!$safe) {
                $this->hotaru->message = $this->lang['error_csrf'];
                $this->hotaru->messageType = 'red';
            }
        }
        
        // the above CSRF check clears the existing token if valid, so we need to generate a new one
        // for the form that is still on the page:
        if (!$this->hotaru->token) {
            $csrf = new csrf($this->db);  
            $csrf->action = $this->hotaru->getPagename();
            $csrf->life = 10; 
            $this->hotaru->token = $csrf->csrfkey();
        }
        
        // if new item to block
        if ($safe && $this->cage->post->getAlpha('type') == 'new') {
            $type = $this->cage->post->testAlnumLines('blocked_type');
            $value = $this->cage->post->getMixedString2('value');
            
            if (!$value) {
                $this->hotaru->message = $this->lang['admin_blocked_list_empty'];
                $this->hotaru->messageType = 'red';
            } else {
                $this->addToBlockedList($type, $value);
            }
        }
        
        // if edit item
        if ($safe && $this->cage->post->getAlpha('type') == 'edit') {
            $id = $this->cage->post->testInt('id');
            $type = $this->cage->post->testAlnumLines('blocked_type');
            $value = $this->cage->post->getMixedString2('value');
            $this->updateBlockedList($id, $type, $value);
            $this->hotaru->message = $this->lang['admin_blocked_list_updated'];
            $this->hotaru->messageType = 'green';
        }
        
        // if remove item
        if ($safe && $this->cage->get->getAlpha('action') == 'remove') {
            $id = $this->cage->get->testInt('id');
            $this->removeFromBlockedList($id);
            $this->hotaru->message = $this->lang["admin_blocked_list_removed"];
            $this->hotaru->messageType = 'green';
        }
        
        // GET CURRENTLY BLOCKED ITEMS...
        
        $where_clause = '';
        
        // if search
        if ($safe && $this->cage->post->getAlpha('type') == 'search') {
            $search_term = $this->cage->post->getMixedString2('search_value');
            $where_clause = " WHERE blocked_value LIKE '%" . trim($this->db->escape($search_term)) . "%'";
        }
        
        // if filter
        if ($safe && $this->cage->post->getAlpha('type') == 'filter') {
            $filter = $this->cage->post->testAlnumLines('blocked_type');
            if ($filter == 'all') { $where_clause = ''; } else { $where_clause = " WHERE blocked_type = %s"; }
        }
        
        // SQL
        $sql = "SELECT * FROM " . TABLE_BLOCKED . $where_clause;

        if (isset($search_term)) { 
            $blocked_items = $this->db->get_results($sql);
        } elseif (isset($filter)) { 
            $blocked_items = $this->db->get_results($this->db->prepare($sql, $filter));
        } else {
            $blocked_items = $this->db->get_results($this->db->prepare($sql));
        }
        
        if (!$blocked_items) { return array(); }
        
        $pg = $this->cage->get->getInt('pg');
        $items = 20;
        $output = "";
        
        require_once(EXTENSIONS . 'Paginated/Paginated.php');
        require_once(EXTENSIONS . 'Paginated/DoubleBarLayout.php');
        $pagedResults = new Paginated($blocked_items, $items, $pg);
        
        $alt = 0;
        while($block = $pagedResults->fetchPagedRow()) {    //when $story is false loop terminates    
            $alt++;
            $output .= "<tr class='table_row_" . $alt % 2 . "'>\n";
            $output .= "<td>" . $block->blocked_type . "</td>\n";
            $output .= "<td>" . $block->blocked_value . "</td>\n";
            $output .= "<td>" . "<a class='table_drop_down' href='#'>\n";
            $output .= "<img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/edit.png'>" . "</a></td>\n";
            $output .= "<td>" . "<a href='" . BASEURL . "admin_index.php?page=blocked_list&amp;action=remove&amp;id=" . $block->blocked_id . "'>\n";
            $output .= "<img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/delete.png'>" . "</a></td>\n";
            $output .= "</tr>\n";
            $output .= "<tr class='table_tr_details' style='display:none;'>\n";
            $output .= "<td colspan=3 class='table_description'>\n";
            $output .= "<form name='blocked_list_edit_form' action='" . BASEURL . "admin_index.php' method='post'>\n";
            $output .= "<table><tr><td><select name='blocked_type'>\n";
            
            switch($block->blocked_type) { 
                case 'url':
                    $text = $this->lang["admin_theme_blocked_url"];
                    break;
                case 'email':
                    $text = $this->lang["admin_theme_blocked_email"];
                    break;
                default:
                    $text = $this->lang["admin_theme_blocked_ip"];
                    break;
            }
            
            $output .= "<option value='" . $block->blocked_type . "'>" . $text . "</option>\n";
            $output .= "<option value='ip'>" . $this->lang["admin_theme_blocked_ip"] . "</option>\n";
            $output .= "<option value='url'>" . $this->lang["admin_theme_blocked_url"] . "</option>\n";
            $output .= "<option value='email'>" . $this->lang["admin_theme_blocked_email"] . "</option>\n";
            $output .= "<option value='user'>" . $this->lang["admin_theme_blocked_username"] . "</option>\n";
            $output .= "</select></td>\n";
            $output .= "<td><input type='text' size=30 name='value' value='" . $block->blocked_value . "' /></td>\n";
            $output .= "<td><input class='submit' type='submit' value='" . $this->lang['admin_blocked_list_update'] . "' /></td>\n";
            $output .= "</tr></table>\n";
            $output .= "<input type='hidden' name='id' value='" . $block->blocked_id . "' />\n";
            $output .= "<input type='hidden' name='page' value='blocked_list' />\n";
            $output .= "<input type='hidden' name='type' value='edit' />\n";
            $output .= "<input type='hidden' name='token' value='" . $this->hotaru->token . "' />";
            $output .= "</form>\n";
            $output .= "</td>";
            $output .= "<td class='table_description_close'><a class='table_hide_details' href='#'>" . $this->lang["admin_theme_plugins_close"] . "</a></td>";
            $output .= "</tr>";
        }

        $blocked_array = array('blocked_items' => $output, 'pagedResults' => $pagedResults);
        
        return $blocked_array;
    }
    
    
     /**
     * Add to or update items 
     *
     * @return array|false
     */
    public function addToBlockedList($type = '', $value = 0, $msg = true)
    {
        $sql = "SELECT blocked_id FROM " . TABLE_BLOCKED . " WHERE blocked_type = %s AND blocked_value = %s"; 
        $id = $this->db->get_var($this->db->prepare($sql, $type, $value));
        
        if ($id) { // already exists
            if ($msg) { 
                $this->hotaru->message = $this->lang['admin_blocked_list_exists']; 
                $this->hotaru->messageType = 'red';
            }
            return false;
        } 
        
        $sql = "INSERT INTO " . TABLE_BLOCKED . " (blocked_type, blocked_value, blocked_updateby) VALUES (%s, %s, %d)"; 
        $this->db->query($this->db->prepare($sql, $type, $value, $this->current_user->id));
        if ($msg) { 
            $this->hotaru->message = $this->lang['admin_blocked_list_added']; 
            $this->hotaru->messageType = 'green';
        }
        
        return true;
    }
    
    
     /**
     * Add to or update items 
     *
     * @return array|false
     */
    public function updateBlockedList($id = 0, $type = '', $value = 0)
    {
        $sql = "UPDATE " . TABLE_BLOCKED . " SET blocked_type = %s, blocked_value = %s, blocked_updateby = %d WHERE blocked_id = %d"; 
        $this->db->query($this->db->prepare($sql, $type, $value, $this->current_user->id, $id));
    }
    
    
     /**
     * Remove from blocked list
     */
    public function removeFromBlockedList($id = 0)
    {
        $sql = "DELETE FROM " . TABLE_BLOCKED . " WHERE blocked_id = %d"; 
        $this->db->get_var($this->db->prepare($sql, $id));
    }
    
    
     /**
     * Check if a value is blocked
     *
     * Note: Other methods for the Blocked List can be found in the Admin class
     *
     * @param string $type - i.e. ip, url, email, user
     * @param string $value
     * @param bool $like - used for LIKE sql if true
     * @return bool
     */
    public function isBlocked($type = '', $value = '', $operator = '=')
    {
        $exists = 0;
        
        // if both type and value provided...
        if ($type && $value) {
            $sql = "SELECT blocked_value FROM " . TABLE_BLOCKED . " WHERE blocked_type = %s AND blocked_value " . $operator . " %s"; 
            $exists = $this->db->get_var($this->db->prepare($sql, $type, $value));
        } 
        // if only value provided...
        elseif ($value) 
        {
            $sql = "SELECT blocked_value FROM " . TABLE_BLOCKED . " WHERE blocked_value " . $operator . " %s"; 
            $exists = $this->db->get_var($this->db->prepare($sql, $value));
        }
        
        if ($exists) { return true; } else { return false; }
    }
}

?>
