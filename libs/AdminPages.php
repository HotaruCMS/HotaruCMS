<?php
/**
 * Functions for Admin pages, e.g. settings, maintenance, blocked list...
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
class AdminPages
{
     /**
     * Admin Pages
     */
    public function pages($hotaru, $page = 'admin_login')
    {
        $hotaru->vars['admin_sidebar_layout'] = 'vertical';
        
        $hotaru->pluginHook('admin_pages');
        
        switch ($page) {
            case "admin_login":
                $hotaru->sidebars = false;
                $hotaru->adminLoginLogout('login');
                break;
            case "admin_logout":
                $hotaru->adminLoginLogout('logout');
                break;
            case "admin_account":
                $hotaru->vars['admin_account'] = $this->adminAccount($hotaru);
                break;
            case "settings":
                $hotaru->vars['admin_settings'] = $this->settings($hotaru);
                break;
            case "maintenance":
                $this->maintenanceAction($hotaru);
                $hotaru->vars['admin_plugin_settings'] = $this->listPluginSettings($hotaru);
                $hotaru->vars['admin_plugin_tables'] = $this->listPluginTables($hotaru);
                break;
            case "blocked_list":
                $hotaru->vars['admin_blocked_list'] = $this->blocked($hotaru);
                break;
            case "plugin_management":
                $hotaru->sidebars = false;
                $hotaru->vars['admin_sidebar_layout'] = 'horizontal';
                $this->adminPlugins($hotaru);
                break;
            case "plugin_settings":
                // Nothing special to do...
                break;
            default:
                // we need this because it's not specified in the url:
                $hotaru->pageName = 'admin_home';
                break;
        }
        
        // Display the main theme's index.php template
        $hotaru->displayTemplate('index');
    }
    
    
 /* *************************************************************
 *
 *  ACCOUNT PAGE
 *
 * *********************************************************** */
 
    
    /**
     * Call the updateAccount method in UserAuth
     */    
    public function adminAccount($hotaru)
    {
        return $hotaru->currentUser->updateAccount($hotaru);
    }
    
    
 /* *************************************************************
 *
 *  SETTINGS PAGE
 *
 * *********************************************************** */
 
    
    /**
     * Process the settings form
     */    
    public function settings($hotaru)
    {
        $loaded_settings = $this->getAllAdminSettings($hotaru->db);    // get all admin settings from the database
        
        $error = 0;
        
        if ($hotaru->cage->post->noTags('settings_update')  == 'true') {
        
            // if either the login or forgot password form is submitted, check the CSRF key
            if (!$hotaru->csrf()) { $error = 1; }
        
            foreach ($loaded_settings as $setting_name) {
                if ($hotaru->cage->post->keyExists($setting_name->settings_name)) {
                    $setting_value = $hotaru->cage->post->noTags($setting_name->settings_name);
                    if (!$error && $setting_value && $setting_value != $setting_name->settings_value) {
                        $this->adminSettingUpdate($hotaru, $setting_name->settings_name, $setting_value);
    
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
                $hotaru->message = $hotaru->lang['admin_settings_update_success'];
                $hotaru->messageType = 'green';
            } else {
                $hotaru->message = $hotaru->lang['admin_settings_update_failure'];
                $hotaru->messageType = 'red';
            }
        }    
        
        $loaded_settings = $this->getAllAdminSettings($hotaru->db);
        
        return $loaded_settings;
    }
    
    
    /**
     * Returns all setting-value pairs
     *
     * @return array|false
     */
    public function getAllAdminSettings($db)
    {
        $sql = "SELECT * FROM " . TABLE_SETTINGS;
        $results = $db->get_results($db->prepare($sql));
        if ($results) { return $results; } else { return false; }
    }
    
    
    /**
     * Update an admin setting
     *
     * @param string $setting
     * @param string $value
     */
    public function adminSettingUpdate($hotaru, $setting = '', $value = '')
    {
        $exists = $this->adminSettingExists($hotaru->db, $setting);
        
        if (!$exists) {
            $sql = "INSERT INTO " . TABLE_SETTINGS . " (settings_name, settings_value, settings_updateby) VALUES (%s, %s, %d)";
            $hotaru->db->query($hotaru->db->prepare($sql, $setting, $value, $hotaru->currentUser->id));
        } else {
            $sql = "UPDATE " . TABLE_SETTINGS . " SET settings_name = %s, settings_value = %s, settings_updateby = %d WHERE (settings_name = %s)";
            $hotaru->db->query($hotaru->db->prepare($sql, $setting, $value, $hotaru->currentUser->id, $setting));
        }
    }
    
    
    /**
     * Determine if a setting already exists
     *
     * Note: The actual value is ignored
     *
     * @param string $setting
     * @return mixed|false
     */
    public function adminSettingExists($db, $setting = '')
    {
        $sql = "SELECT settings_name FROM " . TABLE_SETTINGS . " WHERE (settings_name = %s)";
        $returned_setting = $db->get_var($db->prepare($sql, $setting));
        if ($returned_setting) { return $returned_setting; } else { return false; }
    }
    
    
 /* *************************************************************
 *
 *  MAINTENANCE PAGE
 *
 * *********************************************************** */
 
 
    /**
     * Check action called in Maintenance template
     */
    public function maintenanceAction($hotaru)
    {
        if (!$action = $hotaru->cage->get->testAlnumLines('action')) { return false; }
        
        if ($action == 'open') { $hotaru->openCloseSite('open'); }
        if ($action == 'close') { $hotaru->openCloseSite('close'); }
        if ($action == 'clear_db_cache') { $hotaru->clearCache('db_cache'); }
        if ($action == 'clear_css_js_cache') { $hotaru->clearCache('css_js_cache'); }
        if ($action == 'clear_rss_cache') { $hotaru->clearCache('rss_cache'); }
        if ($action == 'clear_html_cache') { $hotaru->clearCache('html_cache'); }
        if ($action == 'optimize') { $hotaru->optimizeTables(); }
        if ($action == 'empty') { $hotaru->emptyTable($hotaru->cage->get->testAlnumLines('table')); }
        if ($action == 'drop') { $hotaru->dropTable($hotaru->cage->get->testAlnumLines('table')); }
        if ($action == 'remove_settings') { $hotaru->removeSettings($hotaru->cage->get->testAlnumLines('settings')); }
    }
    
    
    /**
     * List all plugins with settings
     *
     * @return array|false
     */
    public function listPluginSettings($hotaru)
    {
        $plugin_settings = array();
        $sql = "SELECT DISTINCT plugin_folder FROM " . DB_PREFIX . "pluginsettings";
        $results = $hotaru->db->get_results($hotaru->db->prepare($sql));
    
        if (!$results) { return false; } 
        
        foreach ($results as $item) {
            array_push($plugin_settings, $item->plugin_folder);
        }
        
        return $plugin_settings;
    }
    
    
    /**
     * List all plugin created tables
     */
    public function listPluginTables($hotaru)
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
            
        $hotaru->db->select(DB_NAME);
        
        if (!$hotaru->db->get_col("SHOW TABLES",0)) { return $plugin_tables; }
        
        foreach ( $hotaru->db->get_col("SHOW TABLES",0) as $table_name )
        {
            if (!in_array($table_name, $core_tables)) {
                array_push($plugin_tables, $table_name);
            }
        }
        
        return $plugin_tables;
    }
    
    
 /* *************************************************************
 *
 *  BLOCKED PAGE
 *
 * *********************************************************** */
 
 
    /**
     * Determine and respond to actions from the Blocked list
     */
    public function blocked($hotaru)
    {
        require_once(LIBS . 'Blocked.php');
        $blocked = new Blocked();
        $blocked_items = $blocked->buildBlockedList($hotaru);

        return $blocked_items;
    }
    
    
 /* *************************************************************
 *
 *  PLUGIN MANAGEMENT PAGE
 *
 * *********************************************************** */
 
 
     /**
     * Call functions based on user actions in Plugin Management
     */
    public function adminPlugins($hotaru)
    {
        $pfolder = $hotaru->cage->get->testAlnumLines('plugin');
        $hotaru->pluginFolder = $pfolder;   // assign this plugin to Hotaru
        
        $action = $hotaru->cage->get->testAlnumLines('action');
        $order = $hotaru->cage->get->testAlnumLines('order');
        
        require_once(LIBS . 'PluginManagement.php');
        $plugman = new PluginManagement();
        
        switch ($action) {
            case "activate":
                $plugman->activateDeactivate($hotaru, 1);
                break;
            case "deactivate":
                $plugman->activateDeactivate($hotaru, 0);
                break;    
            case "activate_all":
                $plugman->activateDeactivateAll($hotaru, 1);
                break;
            case "deactivate_all":
                $plugman->activateDeactivateAll($hotaru, 0);
                break;    
            case "uninstall_all":
                $plugman->uninstallAll($hotaru);
                break;    
            case "install":
                $plugman->install($hotaru);
                break;
            case "uninstall":
                $plugman->uninstall($hotaru);
                break;    
            case "orderup":
                $plugman->pluginOrder($hotaru, $order, "up");
                break;    
            case "orderdown":
                $plugman->pluginOrder($hotaru, $order, "down");
                break;    
            default:
                // nothing to do here...
                break;
        }
        
        // get and sort all the plugins ready for display:
        $allplugins = $plugman->getPlugins($hotaru);  // get plugins
        
        $installed_plugins = array_filter($allplugins, array($plugman, 'getInstalledPlugins'));
        $hotaru->vars['installed_plugins'] = sksort($installed_plugins, "order", "int", true);
        
        $uninstalled_plugins = array_filter($allplugins, array($plugman, 'getUninstalledPlugins'));
        $hotaru->vars['uninstalled_plugins'] = sksort($uninstalled_plugins, 'name', 'char', true);
    
        return true;
    }
}
?>