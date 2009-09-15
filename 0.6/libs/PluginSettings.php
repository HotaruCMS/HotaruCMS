<?php
/**
 * The PluginSettings class contains some useful methods for simplifying
 * the creation and usage of settings pages for individual plugins
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

class PluginSettings
{

    /**
     * Get the value for a given plugin and setting
     *
     * @param string $folder name of plugin folder
     * @param string $setting name of the setting to retrieve
     * @return string|false
     *
     * Notes: If there are multiple settings with the same name,
     * this will only get the first.
     */
    public function getSetting($setting = '', $folder = '')
    {
        global $db;
        
        $sql = "SELECT plugin_value FROM " . TABLE_PLUGINSETTINGS . " WHERE (plugin_folder = %s) AND (plugin_setting = %s)";
        $value = $db->get_var($db->prepare($sql, $folder, $setting));

        if ($value) { return $value; } else { return false; }
    }
    
    
    /**
     * Get an array of settings for a given plugin
     *
     * @param string $folder name of plugin folder
     * @return array|false
     *
     * Note: Unlike "getSetting", this will get ALL settings with the same name.
     */
    public function getSettingsArray($folder = '')
    {
        global $db;
        
        $sql = "SELECT plugin_setting, plugin_value FROM " . TABLE_PLUGINSETTINGS . " WHERE (plugin_folder = %s)";
        $results = $db->get_results($db->prepare($sql, $folder));
        
        if ($results) { return $results; } else { return false; }
    }
    
    
    /**
     * Get and unserialize serialized settings
     *
     * @return array - of submit settings
     */
    public function getSerializedSettings($folder = '')
    {
        // Get settings from the database if they exist...
        $settings = unserialize($this->getSetting($folder . '_settings', $folder));
        return $settings;
    }
    
    
    /**
     * Determine if a plugin setting already exists
     *
     * @param string $folder name of plugin folder
     * @param string $setting name of the setting to retrieve
     * @return string|false
     */
    public function isSetting($setting = '', $folder = '')
    {
        global $db;
        
        $sql = "SELECT plugin_setting FROM " . TABLE_PLUGINSETTINGS . " WHERE (plugin_folder = %s) AND (plugin_setting = %s)";
        $returned_setting = $db->get_var($db->prepare($sql, $folder, $setting));
        if ($returned_setting) { 
            return $returned_setting; 
        } else { 
            return false; 
        }
    }
    
    /**
     * Update a plugin setting
     *
     * @param string $folder name of plugin folder
     * @param string $setting name of the setting
     * @param string $setting stting value
     */
    public function updateSetting($setting = '', $value = '', $folder = '')
    {
        global $db, $current_user;
        
        $exists = $this->isSetting($setting, $folder);
        if (!$exists) 
        {
            $sql = "INSERT INTO " . TABLE_PLUGINSETTINGS . " (plugin_folder, plugin_setting, plugin_value, plugin_updateby) VALUES (%s, %s, %s, %d)";
            $db->query($db->prepare($sql, $folder, $setting, $value, $current_user->getId()));
        } else 
        {
            $sql = "UPDATE " . TABLE_PLUGINSETTINGS . " SET plugin_folder = %s, plugin_setting = %s, plugin_value = %s, plugin_updateby = %d WHERE (plugin_folder = %s) AND (plugin_setting = %s)";
            $db->query($db->prepare($sql, $folder, $setting, $value, $current_user->getId(), $folder, $setting));
        }
    }
    

    /**
     * Deletes rows from pluginsettings that match a given setting or plugin
     *
     * @param string $setting name of the setting to remove
     * @param string $folder name of plugin folder
     */
    public function deleteSettings($setting = '', $folder = '')
    {
        global $db;
        
        if ($setting) {
            $sql = "DELETE FROM " . TABLE_PLUGINSETTINGS . " WHERE plugin_setting = %s";
            $db->query($db->prepare($sql, $setting));
        }
        
        if ($folder) {
            $sql = "DELETE FROM " . TABLE_PLUGINSETTINGS . " WHERE plugin_folder = %s";
            $db->query($db->prepare($sql, $folder));
        }
    }
    
}

?>