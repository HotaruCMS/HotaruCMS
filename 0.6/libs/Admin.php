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
    
    public $sidebar = true;
    

    /**
     * Admin constructor
     */
    public function __construct($entrance = '')
    {
        global $db, $current_user, $hotaru, $plugins, $admin, $cage;
        
        if ($entrance != 'admin') { return false; } // e.g. when called from Install
        
        require_once(LIBS . 'Hotaru.php');
        $hotaru = new Hotaru('admin');
        $hotaru->setPageType('admin');
        
        // Include combined css and js files
        if ($cage->get->keyExists('combine')) {
            $type = $cage->get->testAlpha('type');
            $version = $cage->get->testInt('version');
            $hotaru->combineIncludes($type, $version);
            return true;
        }
        
        $page = $cage->get->testPage('page');    // check with "get";
        if (!$page) { 
            // check with "post" - used in admin_login_form().
            $page = $cage->post->testPage('page'); 
        }
        
        // Authenticate the admin if the Users plugin is INACTIVE:
        if (!$plugins->isActive('users'))
        {
            if (($page != 'admin_login') && !$result = $this->isAdminCookie())
            {
                header('Location: ' . BASEURL . 'admin_index.php?page=admin_login');
            }
        }
        
        // Authenticate the admin if the Users plugin is ACTIVE:
        if (isset($current_user) && $plugins->isActive('users'))
        {
            // This first condition happens when the Users plugin is activated 
            // and there's no cookie for the Admin yet.
            if (($current_user->getName() == "") && $plugins->isActive('users')) 
            {
                header('Location: ' . BASEURL . 'index.php?page=login');
                die; exit;
            } 
            elseif (!$current_user->isAdmin($current_user->getName())) 
            {
                echo "You do not have permission to access this page.<br />";
                die(); exit;
            }
        }
        
        // If we get this far, we know that the user is an administrator.
        
        $admin = $this;    // $admin won't exist until this constructor is finished, 
                           // but plugins using this hook can't wait that long!
        
        $plugins->pluginHook('admin_index');
        
        switch ($page) {
            case "admin_login":
                $this->adminLogin();
                break;
            case "admin_logout":
                $this->adminLogout();
                break;
            case "settings":
                // Nothing special to do...
                break;
            case "maintenance":
                // Nothing special to do...
                break;
            case "plugins":
                $this->plugins();
                break;
            case "plugin_settings":
                $plugins->setFolder($cage->get->testAlnumLines('plugin'));
                $plugins->setName($plugins->getName($plugins->getFolder()));
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
        global $lang, $cage, $hotaru, $plugins;
        
        $action = $cage->get->testAlpha('action');
        $pfolder = $cage->get->testAlnumLines('plugin');
        $order = $cage->get->testAlnumLines('order');
        
        $this_plugin = new PluginFunctions($pfolder);
        
        switch ($action) {
            case "activate":
                $this_plugin->activateDeactivate(1);
                break;
            case "deactivate":
                $this_plugin->activateDeactivate(0);
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
     * @param string $page - page name (filename without.php)
     * @param string $plugin - plugin folder name
     */
    public function displayAdminTemplate($page = '', $plugin = '')
    {
        $page = $page . '.php';
                
        /* 
            1. Check the custom admin theme
            2. Check the default admin theme
            3. Check the plugin folder
            4. Show the 404 Not Found page
        */
        if (file_exists(ADMIN_THEMES . ADMIN_THEME . $page))
        {
            include_once(ADMIN_THEMES . ADMIN_THEME . $page);
        } 
        elseif (file_exists(ADMIN_THEMES . 'admin_default/' . $page))
        {
            include_once(ADMIN_THEMES . 'admin_default/' . $page);
        }
        elseif ($plugin != '' && file_exists(PLUGINS .  $plugin . '/templates/' . $page))
        {
                if ($plugin == 'vote') {
                    // Special case, do not restrict to include once.
                    include(PLUGINS . $plugin . '/templates/' . $page);
                } else {
                    include_once(PLUGINS . $plugin . '/templates/' . $page);
                }
                return true;
                die();
        }
        else 
        {
            include_once(ADMIN_THEMES . '404.php');
        }
    }
    
    
    /**
     * Check to see if the Admin settings page we are looking at  
     * matches the plugin passed to this function.
     *
     * @param string $folder - plugin folder
     * @return bool
     *
     *  Notes: This is used in "admin_header_include" so we only include the css, 
     *         javascript etc. for the plugin we're trying to change settings for.
     *  Usage: $hotaru->is_settings_page('login') returns true if 
     *         page=plugin_settings and plugin=THIS_PLUGIN in the url.
     */
    public function isSettingsPage($folder = '')
    {
        global $cage, $hotaru;
        
        if ($hotaru->isPage('plugin_settings') && $cage->get->testAlnumLines('plugin') == $folder) {
            return true;
        } else {    
            return false;
        }
    }
        
    /**
     * Returns an announcement for display at the top of Admin
     *
     * @return array|false - array of announcements
     */
    public function checkAdminAnnouncements()
    {
        global $lang, $plugins;
        
        // Check if the install file has been deleted:
        
        $announcements = array();
        
        // 1. Check if install file has been deleted
        $filename = INSTALL . 'install.php';
        if (file_exists($filename)) {
            array_push($announcements, $lang['admin_announcement_delete_install']);
        } 
        
        // 2. Please enter a site email address
        if (SITE_EMAIL == "admin@mysite.com") {
            array_push($announcements, $lang['admin_announcement_change_site_email']);    
        } 
        
        // 3. "Go to Plugin Management to enable some plugins"
        if (!$plugins->numActivePlugins()) {
            array_push($announcements, $lang['admin_announcement_plugins_disabled']);    
        }
        
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
        global $db;
        
        $sql = "SELECT settings_value FROM " . TABLE_SETTINGS . " WHERE (settings_name = %s)";
        $value = $db->get_var($db->prepare($sql, $setting));
        if ($value) { return $value; } else { return false; }
    }
    

    /**
     * Returns all setting-value pairs
     *
     * @return array|false
     */
    public function getAllAdminSettings()
    {
        global $db;
        
        $sql = "SELECT * FROM " . TABLE_SETTINGS;
        $results = $db->get_results($db->prepare($sql));
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
        global $db;
        
        $sql = "SELECT settings_name FROM " . TABLE_SETTINGS . " WHERE (settings_name = %s)";
        $returned_setting = $db->get_var($db->prepare($sql, $setting));
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
        global $db, $current_user;
        
        $exists = $this->adminSettingExists($setting);
        
        if (!$exists) {
            $sql = "INSERT INTO " . TABLE_SETTINGS . " (settings_name, settings_value, settings_updateby) VALUES (%s, %s, %d)";
            $db->query($db->prepare($sql, $setting, $value, $current_user->getId()));
        } else {
            $sql = "UPDATE " . TABLE_SETTINGS . " SET settings_name = %s, settings_value = %s, settings_updateby = %d WHERE (settings_name = %s)";
            $db->query($db->prepare($sql, $setting, $value, $current_user->getId(), $setting));
        }
    }


    /**
     * Delete rows from settings that match the given setting
     *
     * @param string $setting
     */    
    public function adminSettingsRemove($setting = '')
    {
        global $db;
        
        $sql = "DELETE FROM " . TABLE_SETTINGS . " WHERE admin_setting = %s";
        $db->query($db->prepare($sql, $setting));
    }
    
    
    /**
     * Calls the delete_files function, then displays a message.
     *
     * @param string $folder - path to the cache folder
     */
    public function clearCache($folder)
    {
        global $hotaru, $lang;
        
        $success = $this->deleteFiles(CACHE . $folder);
        if ($success) {
            $hotaru->message = $lang['admin_maintenance_clear_cache_success'];
            $hotaru->messageType = 'green';
        } else {
            $hotaru->message = $lang['admin_maintenance_clear_cache_failure'];
            $hotaru->messageType = 'red';    
        }
        $hotaru->showMessage();
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
        global $hotaru, $cage, $lang;
        
        $loaded_settings = $this->getAllAdminSettings();    // get all admin settings from the database
        
        $error = 0;
        
        if ($cage->post->noTags('settings_update')  == 'true') {
            foreach ($loaded_settings as $setting_name) {
                if ($cage->post->keyExists($setting_name->settings_name)) {
                    $setting_value = $cage->post->noTags($setting_name->settings_name);
                    if ($setting_value && $setting_value != $setting_name->settings_value) {
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
                $hotaru->message = $lang['admin_settings_update_success'];
                $hotaru->messageType = 'green';
                $hotaru->showMessage();        
            } else {
                $hotaru->message = $lang['admin_settings_update_failure'];
                $hotaru->messageType = 'red';
                $hotaru->showMessage();
            }
        }    
        
        $loaded_settings = $this->getAllAdminSettings();
        
        return $loaded_settings;
    }
    
    
    /**
     * List all plugin created tables
     */
    public function listPluginTables()
    {
        global $db;
        
        $core_tables = array(
            'hotaru_settings',
            'hotaru_users',
            'hotaru_plugins',
            'hotaru_pluginsettings',
            'hotaru_pluginhooks'
        );
        
        $plugin_tables = array();
            
        $db->select(DB_NAME);
        
        foreach ( $db->get_col("SHOW TABLES",0) as $table_name )
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
        global $db, $lang, $hotaru;
        
        $db->select(DB_NAME);
        
        foreach ( $db->get_col("SHOW TABLES",0) as $table_name )
        {
            $db->query("OPTIMIZE TABLE " . $table_name);
        }
        
        $hotaru->message = $lang['admin_maintenance_optimize_success'];
        $hotaru->messageType = 'green';
        $hotaru->showMessage();
    }
    
    
    /**
     * Empty plugin database table
     *
     * @param string $table_name - table to empty
     */
    public function emptyTable($table_name)
    {
        global $db, $lang, $hotaru;
        
        $db->query("TRUNCATE TABLE " . $table_name);
        
        $hotaru->message = $lang['admin_maintenance_table_emptied'];
        $hotaru->messageType = 'green';
        $hotaru->showMessage();
    }
    
    
    /**
     * Delete plugin database table
     *
     * @param string $table_name - table to drop
     */
    public function dropTable($table_name)
    {
        global $db, $lang, $hotaru;
        
        $db->query("DROP TABLE " . $table_name);
        
        $hotaru->message = $lang['admin_maintenance_table_deleted'];
        $hotaru->messageType = 'green';
        $hotaru->showMessage();
    }
    
    
     /**
     * Admin login
     * 
     * @return bool
     */
    public function adminLogin()
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
            $login_result = $current_user->loginCheck($username_check, $password_check);
            
            if ($login_result) {
                    //success
                    $this->setAdminCookie($username_check);
                    $current_user->setName($username_check);
                    $current_user->getUserBasic(0, $username_check);
                    $current_user->setLoggedIn(true);
                    $current_user->updateUserLastLogin();
                    return true;
            } else {
                    // login failed
                    $hotaru->message = $lang["admin_login_failed"];
                    $hotaru->messageType = "red";
            }
        } 
        else 
        {
            if ($cage->post->keyExists('login_attempted')) {
                $hotaru->message = $lang["admin_login_failed"];
                $hotaru->messageType = "red";
            }
            $username_check = "";
            $password_check = "";
        }
        
        return false;
    }
    
     /**
     * Admin login form
     */
    public function adminLoginForm()
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
    public function setAdminCookie($username)
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
    public function isAdminCookie()
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
                    if (!$current_user->isAdmin($hotaru_user)) {
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
        global $current_user;
        
        $current_user->destroyCookieAndSession();
        header("Location: " . BASEURL);
        return true;
    }
    
    
     /**
     * Display Hotaru forums feed on Admin front page
     *
     * @return string Returns the html output for the feed 
     */
    public function adminNews()
    {
        global $hotaru, $plugins, $lang;
            
        $max_items = 5;
        
        $feedurl = 'http://feeds2.feedburner.com/hotarucms';
        $feed = $hotaru->newSimplePie($feedurl);
        $feed->init();
            
        $output = "";
        $item_count = 0;
            
        if ($feed->data) { 
            foreach ($feed->get_items() as $item)
            {
                $output .= "<div>";
                
                // Title
                $output .= "<a href='" . $item->get_permalink() . "'>" . $item->get_title() . "</a><br />";
                
                // Posted by
                $output .= "<small>" . $lang["admin_news_posted_by"] . " ";
                
                foreach ($item->get_authors() as $author) 
                {
                    $output .= $author->get_name(); 
                }
                
                // Date
                $output .= " " . $lang["admin_news_on"] . " " . $item->get_date('j F Y');
                $output .= "</small><br />";
                
                // Content
                $output .= substr(strip_tags($item->get_content()), 0, 300);
                $output .= "... ";
                
                // Read more
                $output .= "<small><a href='" . $item->get_permalink() . "' title='" . $item->get_title() . "'>[" . $lang["admin_news_read_more"] . "]</a>";
                $output .= "</small>";
                
                $output .= "</div><br />";
                
                $item_count++;
                if ($item_count >= $max_items) { break;}
            }
        }
        
        echo $output;
    }

}

?>
