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
	
	var $sidebar = true;
	
	/* ******************************************************************** 
	 *  Function: display_admin_template
	 *  Parameters: page name (filename without.php)
	 *  Purpose: First looks in the user's chosen admin theme directory, if not there, gets the file from the default admin theme.
	 *  Notes: ---
	 ********************************************************************** */
	 
	function display_admin_template($page = '', $plugin = '')  {

		$page = $page . '.php';
		
		/* First check if there's a specified plugin for the file and load 
		   the template from the plugin folder if it's there. */
		if($plugin != '') {
			if(file_exists(plugins .  $plugin . '/templates/' . $page)) {
				include_once(plugins . $plugin . '/templates/' . $page);
				return;
			}
		}
		
		// Check the custom theme then the default theme...		
		if(file_exists(admin_themes . admin_theme . $page)) {
			include_once(admin_themes . admin_theme . $page);
		} elseif(file_exists(admin_themes . 'admin_default/' . $page)) {
			include_once(admin_themes . 'admin_default/' . $page);
		} else {
			include_once(admin_themes . '404.php');
		}
	}
	
		
	/* ******************************************************************** 
	 *  Function: check_admin_announcements
	 *  Parameters: --- 
	 *  Purpose: Returns an announcement for display at the top of Admin.
	 *  Notes: ---
	 ********************************************************************** */
	 
	function check_admin_announcements() {
		global $lang, $plugin;
		// Check if the install file has been deleted:
		
		$announcements = array();
		
		// 1. Check if install file has been deleted
		$filename = install . 'install.php';
		if(file_exists($filename)) {
			array_push($announcements, $lang['admin_announcement_delete_install']);
		} 
		
		// 2. "Go to Plugin Management to enable some plugins"
		if(!$plugin->num_active_plugins()) {
			array_push($announcements, $lang['admin_announcement_plugins_disabled']);	
		}
		
		/*
		// 3. Please install the Users plugin
		if (!$plugin->plugin_active('users')) {
			array_push($announcements, $lang['admin_announcement_users_disabled']);	
		} 
		*/
		
		if(!is_array($announcements)) {
			return false;
		} else {
			return $announcements;
		}
	}


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
		global $db, $current_user;
		$exists = $this->admin_setting_exists($setting);
		if(!$exists) {
			$sql = "INSERT INTO " . table_settings . " (settings_name, settings_value, settings_updateby) VALUES (%s, %s, %d)";
			$db->query($db->prepare($sql, $setting, $value, $current_user->id));
		} else {
			$sql = "UPDATE " . table_settings . " SET settings_name = %s, settings_value = %s, settings_updateby = %d WHERE (settings_name = %s)";
			$db->query($db->prepare($sql, $setting, $value, $current_user->id, $setting));
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
		global $hotaru, $lang;
		
		$success = $this->delete_files(includes . $folder . '/cache');
		if($success) {
			$hotaru->message = $lang['admin_maintenance_clear_cache_success'];
			$hotaru->message_type = 'green';
		} else {
			$hotaru->message = $lang['admin_maintenance_clear_cache_failure'];
			$hotaru->message_type = 'red';	
		}
		$hotaru->show_message();
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
		global $hotaru, $cage, $lang;
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
				$hotaru->message = $lang['admin_settings_update_success'];
				$hotaru->message_type = 'green';
				$hotaru->show_message();		
			} else {
				$hotaru->message = $lang['admin_settings_update_failure'];
				$hotaru->message_type = 'red';
				$hotaru->show_message();
			}
		}	
		
		$loaded_settings = $this->get_all_admin_settings();
		return $loaded_settings;	
	}
}

?>