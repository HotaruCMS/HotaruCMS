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
namespace Libs;

class PluginSettings extends Prefab
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
               //print 'folder: ' . $folder; print ' ** setting: ' . $setting . '<br/>';
                //print '$h->plugin->folder: ' . $h->plugin->folder . '<br/>';
		
            if (!$folder) { 
                    // we cant do an isset here as the propery is protected and wont return. trying this instead
                    if (!property_exists($h, 'plugin')) { 
//                        // TODO check what is causing this to be empty
                        //print "no folder". '<br/>';
                        return false;                         
                    } else {
                        //print "folder found". '<br/>';
                        $folder = $h->plugin->folder;
                    }
                }                
		
		if ($h->adminPage)
		{
			// In Admin. Let's pull settings from the database to avoid problems when saving in Plugin Settings:
			$sql = "SELECT plugin_value FROM " . TABLE_PLUGINSETTINGS . " WHERE (plugin_folder = %s) AND (plugin_setting = %s) LIMIT 1";
			$value = $h->db->get_var($h->db->prepare($sql, $folder, $setting));
		}
		else
		{
			if (!$h->pluginSettings) { return false; }
                        
                        $value = isset($h->pluginSettings[$folder][$setting]) ? $h->pluginSettings[$folder][$setting] : null;
		}
		//print "value of setting " . $value . '<Br/>';
		if (isset($value)) { return $value; } else { return false; }
	}
	
	
        // TODO test more - e.g install of some plugins use this
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
		
                if (isset($h->pluginSettings[$folder])) {
                    return $h->pluginSettings[$folder];
                }
                
                return false;
		//$sql = "SELECT plugin_setting, plugin_value FROM " . TABLE_PLUGINSETTINGS . " WHERE (plugin_folder = %s)";
		//$results = $h->db->get_results($h->db->prepare($sql, $folder));
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
                
                if (!$settings_name) {
                    $settings_name = $folder . '_settings';
                }
    
                if (!isset($h->pluginSettings[$folder])) {
                    //print "set settings for: " . $folder . '<br/>';
                    $h->pluginSettings[$folder] = unserialize($this->getSetting($h, $settings_name, $folder));
                }
		
		return $h->pluginSettings[$folder];
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
		
                //print "isSetting for folder: " . $folder . '   setting: ' .$setting . '<Br/>';
                        
                // dont do check in memory. we are going to update based on this. better to go to db
                if (!isset($h->pluginSettings[$folder])) {
                    //print "set settings for: " . $folder . '<br/>';
                    $h->pluginSettings[$folder] = unserialize($this->getSetting($h, $setting, $folder));
                }
		
		return $h->pluginSettings[$folder];
                
//		$sql = "SELECT plugin_setting FROM " . TABLE_PLUGINSETTINGS . " WHERE (plugin_folder = %s) AND (plugin_setting = %s) LIMIT 1";
//		$returned_setting = $h->db->get_var($h->db->prepare($sql, $folder, $setting));
//		if ($returned_setting) { 
//			return $returned_setting; 
//		}

		return false; 		
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
                //print "@@@@ UPDATESETTING for folder: " . $folder . ',  setting: ' .$setting . ',  value:' . $value . '<br/><br/>';
		if (!$folder) { $folder = $h->plugin->folder; }
		
		$exists = $this->isSetting($h, $setting, $folder);
		if (!$exists) {
			$sql = "INSERT INTO " . TABLE_PLUGINSETTINGS . " (plugin_folder, plugin_setting, plugin_value, plugin_updateby) VALUES (%s, %s, %s, %d)";
//print $sql; print '<br/>';			
$h->db->query($h->db->prepare($sql, $folder, $setting, $value, $h->currentUser->id));
		} else {
			$sql = "UPDATE " . TABLE_PLUGINSETTINGS . " SET plugin_folder = %s, plugin_setting = %s, plugin_value = %s, plugin_updateby = %d WHERE (plugin_folder = %s) AND (plugin_setting = %s)";
			if (isset($h->currentUser->id)) { $updateby = $h->currentUser->id; } else { $updateby = 1; }
//print $sql; print '<br/>';			
$h->db->query($h->db->prepare($sql, $folder, $setting, $value, $updateby, $folder, $setting));
		}
		
		// optimize the table
		$h->db->query("OPTIMIZE TABLE " . TABLE_PLUGINSETTINGS);
                
                // update the in memory settings for this plugin otherwise they wont be displayed back on form
//                if (is_serialized($value)) {
//                    $h->pluginSettings[$folder] = unserialize($value);
//                } else {
//                    $h->pluginSettings[$folder][$setting] = $value;
//                }
//                
                // After updating a setting we should always recall the init methods for populting $h functions   
                $h->getAllPluginSettings();
	}
        
        
        public static function getSettingsDropdownList($h, $title = "Plugins")
        {
            if (!isset($h->plugins['activeFolders']) || !$h->plugins['activeFolders']) { return false; }
            
            $output = '<a href="#" id="myTabDrop1" class="dropdown-toggle" data-toggle="dropdown">' . $title . '<span class="caret"></span></a>';
            $output .= '<ul class="dropdown-menu" role="menu" aria-labelledby="myTabDrop1">';
                        
            foreach($h->plugins['activeFolders'] as $plugin => $val) {
                $pluginData = $h->allPluginDetails['pluginData'][$plugin];
                
                $name = ucfirst(preg_replace('/_/', ' ', $plugin));   
                $output .= '<li>';
                $output .= '<a href="/admin_index.php?page=plugin_settings&plugin=' . $plugin . '" tabindex="-1" role="tab" >';
                $output .= $pluginData->plugin_latestversion > $pluginData->plugin_version ? '<span class="btn btn-warning btn-xs">' . $name . '</span>' : $name;
                //$output .= $pluginData->plugin_resourceId != 0 ? '<span class="pull-right"><i class="fa fa-comments"></i></span>' : '';
                $output .= '</a>';
                $output .= '</li>';
            }
            
            $output .= '</ul>';
            
            return $output;
        }
}
