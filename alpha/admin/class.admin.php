<?php


/* **************************************************************************************************** 
 *  File: admin/class.admin.php
 *  Purpose: Admin related functions
 *  Notes: Plugins extend the generic_pmd class in class.metadata.php which is a 3rd party script called "Generic PHP Config"
 *  License:
 *
 *   This file is part of Hotaru CMS (http://www.hotarucms.org/).
 *
 *   Hotaru CMS is free software: you can redistribute it and/or modify it under the terms of the 
 *   GNU General Public License as published by the Free Software Foundation, either version 3 of 
 *   the License, or (at your option) any later version.
 *
 *   Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 *   even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License along with Hotaru CMS. If not, 
 *   see http://www.gnu.org/licenses/.
 *   
 *   Copyright (C) 2009 Hotaru CMS - http://www.hotarucms.org/
 *
 **************************************************************************************************** */

class Admin {
	
	/* ******************************************************************** 
	 *  Function: get_admin_setting
	 *  Parameters: Setting name
	 *  Purpose: Returns the value for a given setting
	 *  Notes: ---
	 ********************************************************************** */
	 
	function get_admin_setting($setting = '') {
		global $db;
		$sql = "SELECT settings_value FROM " . table_settings . " WHERE (settings_name = %s)";
		$value = $db->get_var($db->prepare($sql, $setting));
		if($value) { return $value; } else { return false; }
	}
	

	/* ******************************************************************** 
	 *  Function: get_all_admin_settings
	 *  Parameters: None
	 *  Purpose: Returns all setting-value pairs
	 *  Notes: ---
	 ********************************************************************** */
	 
	function get_all_admin_settings() {
		global $db;
		$sql = "SELECT * FROM " . table_settings;
		$results = $db->get_results($db->prepare($sql));
		if($results) { return $results; } else { return false; }
	}
	
	
	/* ******************************************************************** 
	 *  Function: admin_setting_exists
	 *  Parameters: Setting name
	 *  Purpose: Determines if a setting already exists
	 *  Notes: The actual value is ignored
	 ********************************************************************** */
	 	
	function admin_setting_exists($setting = '') {
		global $db;
		$sql = "SELECT settings_name FROM " . table_settings . " WHERE (settings_name = %s)";
		$returned_setting = $db->get_var($db->prepare($sql, $setting));
		if($returned_setting) { return $returned_setting; } else { return false; }
	}	
	
	/* ******************************************************************** 
	 *  Function: admin_setting_update
	 *  Parameters: Setting to update, and new value
	 *  Purpose: Updates an admin setting
	 *  Notes: ---
	 ********************************************************************** */
	
	function admin_setting_update($setting = '', $value = '') {
		global $db;
		$exists = $this->admin_setting_exists($setting);
		if(!$exists) {
			$sql = "INSERT INTO " . table_settings . " (settings_name, settings_value) VALUES (%s, %s)";
			$db->query($db->prepare($sql, $setting, $value));
		} else {
			$sql = "UPDATE " . table_settings . " SET settings_name = %s, settings_value = %s WHERE (settings_name = %s)";
			$db->query($db->prepare($sql, $setting, $value, $setting));
		}
	}


	/* ******************************************************************** 
	 *  Function: admin_setting_remove
	 *  Parameters: Setting name
	 *  Purpose: Deletes rows from settings that match that setting
	 *  Notes: ---
	 ********************************************************************** */
	 	
	function admin_settings_remove($setting = '') {
		global $db;
		$sql = "DELETE FROM " . table_settings . " WHERE admin_setting = %s";
		$db->query($db->prepare($sql, $setting));
	}
	
	
	/* ******************************************************************** 
	 *  Function: clear_cache
	 *  Parameters: cache folder name in the 3rd party directory
	 *  Purpose: Calls the delete_files function, then displays a message.
	 *  Notes: ---
	 ********************************************************************** */

	function clear_cache($folder) {
		global $plugin, $lang;
		
		$success = $this->delete_files(includes . $folder . '/cache');
		if($success) {
			$plugin->message = $lang['admin_maintenance_clear_cache_success'];
			$plugin->message_type = 'green';
		} else {
			$plugin->message = $lang['admin_maintenance_clear_cache_failure'];
			$plugin->message_type = 'red';	
		}
		$plugin->show_message();
	}


	/* ******************************************************************** 
	 *  Function: delete_files
	 *  Parameters: Path to directory 
	 *  Purpose: Deletes all files in the specified directory except placeholder.txt
	 *  Notes: ---
	 ********************************************************************** */
	 	
	function delete_files($dir) {
		$handle=opendir($dir);
	
		while (($file = readdir($handle))!==false) {
			if($file != 'placeholder.txt') {
				if(@unlink($dir.'/'.$file)) {
					$success = true;
				} else {
					$success = false;
				}
			}
		}
		
		closedir($handle);
		
		return $success;
	}
	
	
	/* ******************************************************************** 
	 *  Function: settings
	 *  Parameters: None
	 *  Purpose: Processes the settings form.
	 *  Notes: ---
	 ********************************************************************** */
	 	
	function settings() {
		global $plugin, $cage, $lang;
		$loaded_settings = $this->get_all_admin_settings();	// get all admin settings from the database
		
		$error = 0;
		
		if($cage->post->noTags('settings_update')  == 'true') {
			foreach($loaded_settings as $setting_name) {
				if($cage->post->keyExists($setting_name->settings_name)) {
					$setting_value = $cage->post->noTags($setting_name->settings_name);
					if($setting_value && $setting_value != $setting_name->settings_value) {
						$this->admin_setting_update($setting_name->settings_name, $setting_value);
	
					} else {
						if(!$setting_value) {
							// empty value 
							$error = 1; 
						} else { 
							// No change to the value
							$error = 0; 
						}
					}
				} else {
					// error, setting doesn't exist.
					$error = 1;
				}
			}
			
			if($error == 0) {
				$plugin->message = $lang['admin_settings_update_success'];
				$plugin->message_type = 'green';
				$plugin->show_message();		
			} else {
				$plugin->message = $lang['admin_settings_update_failure'];
				$plugin->message_type = 'red';
				$plugin->show_message();
			}
		}	
		
		$loaded_settings = $this->get_all_admin_settings();
		return $loaded_settings;	
	}
}

?>
	