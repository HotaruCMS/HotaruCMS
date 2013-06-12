<?php
/**
 * Theme Settings
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
class ThemeSettings
{
	/**
	 * Read and return plugin info from top of a plugin file.
	 *
	 * @param string $plugin_file - a file from the /plugins folder 
	 * @return array|false
	 */
	public function readThemeMeta($h, $theme = 'default')
	{
		if (!$theme) { $theme = rtrim(THEME, '/'); }
		
		// Include the generic_pmd class that reads post metadata from the a plugin
		require_once(EXTENSIONS . 'GenericPHPConfig/class.metadata.php');
		$metaReader = new generic_pmd();
		$plugin_metadata = $metaReader->read(THEMES . $theme . '/index.php');
		
		if ($plugin_metadata) { return $plugin_metadata; } else { return false; }
	}
	
	
	/**
	 * Get and unserialize serialized settings
	 *
	 * @param string $theme theme folder name
	 * @param string $return 'value' or 'default'
	 * @return array - of theme settings
	 */
	public function getThemeSettings($h, $theme = '', $return = 'value')
	{
		if (!$theme) { $theme = rtrim(THEME, '/'); }
		
		// Get settings from the database if they exist...
		$sql = "SELECT miscdata_value, miscdata_default FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s";
		$query = $h->db->prepare($sql, $theme . '_settings');
                
                $h->smartCache('on', 'theme_settings', 60, $query); // start using cache
                $settings = $h->db->get_row($query);
                $h->smartCache('off'); // stop using cache
                
		if (!$settings) { return false; } 
		
		if ($return == 'value') {
			$settings = unserialize($settings->miscdata_value);
		} else { 
			$settings = unserialize($settings->miscdata_default);
		}
		
		if ($settings) { return $settings; } else { return false; } 
	}
	
	
	/**
	 * Update theme settings
	 *
	 * @param array $settings array of settings
	 * @param string $theme theme folder name
	 * @param string $column 'value', 'default' or 'both'
	
	 */
	public function updateThemeSettings($h, $settings = array(), $theme = '', $column = 'value')
	{
		if (!$theme) { $theme = rtrim(THEME, '/'); }
		
		$settings = serialize($settings);
		if (isset($h->currentUser->id)) { $updateby = $h->currentUser->id; } else { $updateby = 1; }
		
		$exists = $h->getThemeSettings($theme);
		if (!$exists) 
		{
			$sql = "INSERT INTO " . TABLE_MISCDATA . " (miscdata_key, miscdata_value, miscdata_default, miscdata_updateby) VALUES (%s, %s, %s, %d)";
			$h->db->query($h->db->prepare($sql, $theme . '_settings', $settings, $settings, $updateby));
		} 
		else 
		{
			switch ($column) {
				case 'default':
					$sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_default = %s, miscdata_updateby = %d WHERE miscdata_key = %s";
					$h->db->query($h->db->prepare($sql, $settings, $updateby, $theme . '_settings'));
					break;
				case 'both':
					$sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_value = %s, miscdata_default = %s, miscdata_updateby = %d WHERE miscdata_key = %s";
					$h->db->query($h->db->prepare($sql, $settings, $settings, $updateby, $theme . '_settings'));
				default:
					$sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_value = %s, miscdata_updateby = %d WHERE miscdata_key = %s";
					$h->db->query($h->db->prepare($sql, $settings, $updateby, $theme . '_settings'));
			}
		}
		
		// optimize the table
		$h->db->query("OPTIMIZE TABLE " . TABLE_MISCDATA);
	}
}
?>
