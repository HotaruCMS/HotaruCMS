<?php
/**
 * Functions for plugin settings
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
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
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
	public function getSetting($h, $setting = '', $folder = '')
	{
		if (!$folder) { $folder = $h->plugin->folder; }
		
		if ($h->adminPage)
		{
			// In Admin. Let's pull settings from the database to avoid problems when saving in Plugin Settings:
			$sql = "SELECT plugin_value FROM " . TABLE_PLUGINSETTINGS . " WHERE (plugin_folder = %s) AND (plugin_setting = %s) LIMIT 1";
			$value = $h->db->get_var($h->db->prepare($sql, $folder, $setting));
		}
		else
		{
			// get all settings from the database if we haven't already:
			if (!$h->pluginSettings) { $h->getAllPluginSettings(); }
			
			// return false if no plugin settings found in the database
			if (!$h->pluginSettings) { return false; }
			
			// get the settings we need from memory
			foreach ($h->pluginSettings as $item => $key) {
				if (($key->plugin_folder == $folder) && ($key->plugin_setting == $setting)) {
					$value = $key->plugin_value;
				}
			}
		}
		
		if (isset($value)) { return $value; } else { return false; }
	}
	
	
	/**
	 * Get an array of settings for a given plugin
	 *
	 * @param string $folder name of plugin folder
	 * @return array|false
	 *
	 * Note: Unlike "getSetting", this will get ALL settings with the same name.
	 */
	public function getSettingsArray($h, $folder = '')
	{
		if (!$folder) { $folder = $h->plugin->folder; }
		
		$sql = "SELECT plugin_setting, plugin_value FROM " . TABLE_PLUGINSETTINGS . " WHERE (plugin_folder = %s)";
		$results = $h->db->get_results($h->db->prepare($sql, $folder));
		
		if ($results) { return $results; } else { return false; }
	}
	
	
	/**
	 * Get and unserialize serialized settings
	 *
	 * @param string $folder plugin folder name
	 * @param string $settings_name optional settings name if different from folder
	 * @return array - of submit settings
	 */
	public function getSerializedSettings($h, $folder = '', $settings_name = '')
	{
		if (!$folder) { $folder = $h->plugin->folder; }
		
		// Get settings from the database if they exist...
		if (!$settings_name) {
			$settings = unserialize($h->getSetting($folder . '_settings', $folder));
		} else {
			$settings = unserialize($h->getSetting($settings_name, $folder));
		}
		return $settings;
	}
	
	
	/**
	 * Get and store all plugin settings in $h->pluginSettings
	 * We use the Hotaru object because it's persistent during a page load
	 *
	 * @return array - all settings
	 */
	public function getAllPluginSettings($h)
	{
		$sql = "SELECT plugin_folder, plugin_setting, plugin_value FROM " . TABLE_PLUGINSETTINGS;
		$results = $h->db->get_results($h->db->prepare($sql));
		if ($results) { return $results; } else { return false; }
	}
	
	
	/**
	 * Determine if a plugin setting already exists
	 *
	 * @param string $folder name of plugin folder
	 * @param string $setting name of the setting to retrieve
	 * @return string|false
	 */
	public function isSetting($h, $setting = '', $folder = '')
	{
		if (!$folder) { $folder = $h->plugin->folder; }
		
		$sql = "SELECT plugin_setting FROM " . TABLE_PLUGINSETTINGS . " WHERE (plugin_folder = %s) AND (plugin_setting = %s) LIMIT 1";
		$returned_setting = $h->db->get_var($h->db->prepare($sql, $folder, $setting));
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
	 * @param string $setting setting value
	 */
	public function updateSetting($h, $setting = '', $value = '', $folder = '')
	{
		if (!$folder) { $folder = $h->plugin->folder; }
		
		$exists = $h->isSetting($setting, $folder);
		if (!$exists) 
		{
			$sql = "INSERT INTO " . TABLE_PLUGINSETTINGS . " (plugin_folder, plugin_setting, plugin_value, plugin_updateby) VALUES (%s, %s, %s, %d)";
			$h->db->query($h->db->prepare($sql, $folder, $setting, $value, $h->currentUser->id));
		} else 
		{
			$sql = "UPDATE " . TABLE_PLUGINSETTINGS . " SET plugin_folder = %s, plugin_setting = %s, plugin_value = %s, plugin_updateby = %d WHERE (plugin_folder = %s) AND (plugin_setting = %s)";
			if (isset($h->currentUser->id)) { $updateby = $h->currentUser->id; } else { $updateby = 1; }
			$h->db->query($h->db->prepare($sql, $folder, $setting, $value, $updateby, $folder, $setting));
		}
		
		// optimize the table
		$h->db->query("OPTIMIZE TABLE " . TABLE_PLUGINSETTINGS);
	}
}
?>
