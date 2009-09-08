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
    
    var $sidebar = true;
    
    /**
     * Display admin template
     *
     * @param string $page - page name (filename without.php)
     * @param string $plugin - plugin folder name
     */
    function displayAdminTemplate($page = '', $plugin = '')
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
    function isSettingsPage($folder = '')
    {
        global $cage, $hotaru;
        
        if ($hotaru->is_page('plugin_settings') && $cage->get->testAlnumLines('plugin') == $folder) {
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
    function checkAdminAnnouncements()
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
    function getAdminSetting($setting = '')
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
    function getAllAdminSettings()
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
    function adminSettingExists($setting = '')
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
    function adminSettingUpdate($setting = '', $value = '')
    {
        global $db, $current_user;
        
        $exists = $this->adminSettingExists($setting);
        
        if (!$exists) {
            $sql = "INSERT INTO " . TABLE_SETTINGS . " (settings_name, settings_value, settings_updateby) VALUES (%s, %s, %d)";
            $db->query($db->prepare($sql, $setting, $value, $current_user->id));
        } else {
            $sql = "UPDATE " . TABLE_SETTINGS . " SET settings_name = %s, settings_value = %s, settings_updateby = %d WHERE (settings_name = %s)";
            $db->query($db->prepare($sql, $setting, $value, $current_user->id, $setting));
        }
    }


    /**
     * Delete rows from settings that match the given setting
     *
     * @param string $setting
     */    
    function adminSettingsRemove($setting = '')
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
    function clearCache($folder)
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
    function deleteFiles($dir)
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
    function settings()
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
    function listPluginTables()
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
    function optimizeTables()
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
    function emptyTable($table_name)
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
    function dropTable($table_name)
    {
        global $db, $lang, $hotaru;
        
        $db->query("DROP TABLE " . $table_name);
        
        $hotaru->message = $lang['admin_maintenance_table_deleted'];
        $hotaru->messageType = 'green';
        $hotaru->showMessage();
    }
}

?>