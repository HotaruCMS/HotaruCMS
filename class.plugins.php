<?php

/* **************************************************************************************************** 
 *  File: /class.plugins.php
 *  Purpose: Manages all things plugin-related.
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

//includes
require_once(includes . 'GenericPHPConfig/class.metadata.php');		// This is the generic_pmd class that reads post metadata from the top of a plugin file.

class Plugin extends generic_pmd {

	var $id = '';
	var $enabled = 0;
	var $name = '';
	var $desc = '';
	var $folder = '';
	var $prefix = '';
	var $version = 0;
	var $hooks = array();
	
	/* ******************************************************************** 
	 *  Function: get_plugins
	 *  Parameters: None
	 *  Purpose: Takes plugin info read directly from plugin files, then compares each plugin to the database. 
	 *           If present and latest version, reads info from the database. If not in database or newer version, uses info from plugin file.
	 *  Notes: This is called by the Admin Plugins template to display info in Plugin Management
	 ********************************************************************** */
	 	
	function get_plugins() {
		global $db, $lang;
		$plugins_array = $this->get_plugins_array();
		$count = 0;
		$allplugins = array();
		if($plugins_array) {
			foreach($plugins_array as $plugin_details) {
				$allplugins[$count] = array();
				$plugin_row = $db->get_row($db->prepare("SELECT * FROM " . table_plugins . " WHERE plugin_folder = %s", $plugin_details['folder']));
				if($plugin_row && version_compare($plugin_details['version'], $plugin_row->plugin_version, '<=')) {
					// if plugin in folder is older or equal to plugin in database...
					$allplugins[$count]['name'] = $plugin_row->plugin_name;
					$allplugins[$count]['description'] = $plugin_row->plugin_desc;
					$allplugins[$count]['folder'] = $plugin_row->plugin_folder;
					$allplugins[$count]['status'] = $this->get_plugin_status($plugin_row->plugin_folder);
					$allplugins[$count]['version'] = $plugin_row->plugin_version;
				} elseif($plugin_row && version_compare($plugin_details['version'], $plugin_row->plugin_version, '>')) {
					//plugin exists in database, but it's an older version than the one in the folder...
					$allplugins[$count]['name'] = $plugin_row->plugin_name . " <span style='color: red'><b>*</b></span>";
					$allplugins[$count]['description'] = $plugin_row->plugin_desc;
					$allplugins[$count]['folder'] = $plugin_row->plugin_folder;
					$allplugins[$count]['status'] = $this->get_plugin_status($plugin_row->plugin_folder);
					$allplugins[$count]['version'] = $plugin_row->plugin_version;
					
					// Long string that asks the user to upgrade...
					if($this->hook_exists($plugin_row->plugin_folder, 'plugin_upgrade')) {
						// If the plugin has an upgrade script...
						$allplugins[$count]['version'] .= "<br />";
						$allplugins[$count]['version'] .= $lang['admin_plugins_class_new_version'];
						$allplugins[$count]['version'] .= " <a href='javascript://' ";
						$allplugins[$count]['version'] .= "onclick='hide_show_replace(&quot;" . baseurl . "&quot;, &quot;changetext&quot;, &quot;widget_uninstall_result-";
						$allplugins[$count]['version'] .= $plugin_row->plugin_folder . "&quot;, &quot;" . baseurl;
						$allplugins[$count]['version'] .= "admin/admin_plugins.php&quot;, &quot;plugin_folder=";
						$allplugins[$count]['version'] .= $plugin_row->plugin_folder . "&action=upgrade&quot;);'><b>";
						$allplugins[$count]['version'] .= $lang['admin_plugins_class_upgrade_now'] . "</b></a>";
					
					} else {
						// If the plugin doesn't have an upgrade script...
						$allplugins[$count]['version'] .= "<br />";
						$allplugins[$count]['version'] .= $lang['admin_plugins_class_new_version'];
						$allplugins[$count]['version'] .= " " . $lang['admin_plugins_class_reinstall'];
					}
				} else {
					// if plugin is not in database...
					$allplugins[$count]['name'] = $plugin_details['name'];
					$allplugins[$count]['description'] = $plugin_details['description'];
					$allplugins[$count]['folder'] = $plugin_details['folder'];
					$allplugins[$count]['status'] = "inactive";
					$allplugins[$count]['version'] = $plugin_details['version'];
				}
				$count++;				
			}	
		}
		return $allplugins;
	}


	/* ******************************************************************** 
	 *  Function: get_plugins_array
	 *  Parameters: None
	 *  Purpose: Uses generic post metadata class to read and return plugin info directly from plugin files.
	 *  Notes: Returns an array of the plugin data.
	 ********************************************************************** */

	function get_plugins_array() {
		require_once('funcs.files.php');
		$plugin_list = getFilenames(plugins, "short");
		$plugins_array = array();
		foreach($plugin_list as $plugin_folder_name) {
			$plugin_metadata = $this->read(plugins . $plugin_folder_name . "/" . $plugin_folder_name . ".php");
			if($plugin_metadata) {
				array_push($plugins_array, $plugin_metadata);
			}
		}	
		return $plugins_array;
	}
	/* ******************************************************************** 
	 *  Function: get_plugin_status
	 *  Parameters: widget folder name
	 *  Purpose: Determines if a plugin is enabled or not
	 *  Notes: Needs to be in this class because its used by get_plugins()
	 ********************************************************************** */
	 
	 	
	function get_plugin_status($plugin_folder = '') {
		global $db;
		
		$plugin_row = $db->get_row($db->prepare("SELECT * FROM " . table_plugins . " WHERE plugin_folder = %s", $plugin_folder));
		
		if($plugin_row && $plugin_row->plugin_enabled == 1) {
			$status = "active";
		} else {
			$status = "inactive";
		} 
				
		return $status;
		//return $this->get_status_links($plugin_folder, $status);
	}
	
	
	/* ******************************************************************** 
	 *  Function: active_plugins
	 *  Parameters: the field to select, e.g. 'plugin_name', 'plugin_desc', or just '*'
	 *  Purpose: Returns an array of active plugins
	 *  Notes: Defaults to 'plugin_folder'. Usage: foreach($plugin->active_plugins() as $folder) { echo $folder->plugin_folder; }
	 ********************************************************************** */
	 
	function active_plugins($select = 'plugin_folder') {
		global $db;
		$select = $db->escape($select);
		$active_plugins = $db->get_results($db->prepare("SELECT " . $select . " FROM " . table_plugins . " WHERE plugin_enabled = %d", 1));
		if($active_plugins) { return $active_plugins; } else {return false; }
	}


	/* ******************************************************************** 
	 *  Function: update_plugin_statuses
	 *  Parameters: widget positions returned from EasyWidgets
	 *  Purpose: Breaks down position string from EasyWidgets, then calls necessary functions
	 *  Notes: This function is used in Plugin Management.
	 ********************************************************************** */
	 	
	function update_plugin_statuses($widget_positions) {
		foreach (explode('|',$widget_positions) as $pair) {
			list ($status,$widget) = explode ('=',$pair);
			if($widget) { 
				$comma_widgets = explode (',', $widget);	// This deals with cases such as inactive=plugin1,plugin2|active=plugin3,plugin4
				foreach($comma_widgets as $cw) {
					$pairs[$status] = $cw; 
					if($status == "active") {
						$this->activate_deactivate_plugin($pairs[$status], 1);
					} else {
						$this->activate_deactivate_plugin($pairs[$status], 0);
					}
					//echo "Confirmed status of " . $cw . " is " . $this->get_plugin_status($cw) . "<br />";
				}
		        }
		}
	}
	
	
	/* ******************************************************************** 
	 *  Function: hook_exists
	 *  Parameters: plugin folder name, hook name
	 *  Purpose: Returns true of a given hook exists for the specified plugin.
	 *  Notes: ---
	 ********************************************************************** */
	 
	function hook_exists($folder = "", $hook = "") {
		global $db;
		
		$sql = "SELECT count(*) FROM " . table_pluginhooks . " WHERE plugin_folder = %s AND plugin_hook = %s";
		if($db->get_var($db->prepare($sql, $folder, $hook))) { return true;} else { return false; }
	}


	/* ******************************************************************** 
	 *  Function: install_plugin
	 *  Parameters: plugin folder name
	 *  Purpose: Adds plugin to plugins table
	 *  Notes: ---
	 ********************************************************************** */
	 
	function install_plugin($folder = "") {
		global $db;
		
		$plugin_metadata = $this->read(plugins . $folder . "/" . $folder . ".php");
		
		$this->enabled = 1;	// Enable it at the same time we add it to the database.
		$this->name = $plugin_metadata['name'];
		$this->desc = $plugin_metadata['description'];
		$this->folder = $folder;
		$this->prefix = $plugin_metadata['prefix'];
		$this->version = $plugin_metadata['version'];
		$this->hooks = explode(',', $plugin_metadata['hooks']);
	
		$sql = "INSERT INTO " . table_plugins . " (plugin_enabled, plugin_name, plugin_prefix, plugin_folder, plugin_desc, plugin_version) VALUES (%d, %s, %s, %s, %s, %s)";
		$db->query($db->prepare($sql, $this->enabled, $this->name, $this->prefix, $this->folder, $this->desc, $this->version));
		
		foreach($this->hooks as $hook) {
			$sql = "INSERT INTO " . table_pluginhooks . " (plugin_folder, plugin_hook) VALUES (%s, %s)";
			$db->query($db->prepare($sql, $this->folder, trim($hook)));
		}
		
		$this->check_actions('install_plugin', $folder);
	}
	
	
	/* ******************************************************************** 
	 *  Function: upgrade_plugin
	 *  Parameters: plugin folder name
	 *  Purpose: Plugins hook in here with their own upgrade scripts.
	 *  Notes: This function does nothing by itself other than read the latest file's metadata.
	 ********************************************************************** */
	 
	function upgrade_plugin($folder = "") {
		global $db;
		
		$plugin_metadata = $this->read(plugins . $folder . "/" . $folder . ".php");
		
		$this->enabled = 1;	// Enable it at the same time we add it to the database.
		$this->name = $plugin_metadata['name'];
		$this->desc = $plugin_metadata['description'];
		$this->folder = $folder;
		$this->prefix = $plugin_metadata['prefix'];
		$this->version = $plugin_metadata['version'];
		$this->hooks = explode(',', $plugin_metadata['hooks']);
		
		$this->check_actions('upgrade_plugin', $folder);
	}
	
	
	/* ******************************************************************** 
	 *  Function: activate_deactivate_plugin
	 *  Parameters: plugin folder name, enabled status
	 *  Purpose: Enables or disables a plugin, installing if necessary
	 *  Notes: This function does not uninstall/delete a plugin.
	 ********************************************************************** */
	 
	function activate_deactivate_plugin($folder = "", $enabled = 0) {	// 0 = deactivate, 1 = activate
		global $db, $admin;
		
		$admin->delete_files(includes . 'ezSQL/cache');	// Clear the database cache to ensure stored plugins and hooks are up-to-date.
		
		$plugin_row = $db->get_row($db->prepare("SELECT plugin_folder, plugin_enabled FROM " . table_plugins . " WHERE plugin_folder = %s", $folder));
		if(!$plugin_row) {
			if($enabled == 1) {	// without this, the plugin would be installed and then deativated, which is dumb. Let's just not install it yet!
				$this->install_plugin($folder);
			}
		} else {
			if($plugin_row->plugin_enabled != $enabled) {		// only update if we're changing the enabled value.
				$sql = "UPDATE " . table_plugins . " SET plugin_enabled = %d WHERE plugin_folder = %s";
				$db->query($db->prepare($sql, $enabled, $folder));
			}
		}
	}
	

	/* ******************************************************************** 
	 *  Function: plugin_name
	 *  Parameters: plugin folder name
	 *  Purpose: Given a plugin's folder name, it returns the actual name.
	 *  Notes: ---
	 ********************************************************************** */

	function plugin_name($folder = "") {	
		global $db;
		$this->name = $db->get_var($db->prepare("SELECT plugin_name FROM " . table_plugins . " WHERE plugin_folder = %s", $folder));
		return $this->name;
	}
	
	
	/* ******************************************************************** 
	 *  Function: plugin_active
	 *  Parameters: plugin folder name
	 *  Purpose: Given a plugin's folder name, it returns true if currently active.
	 *  Notes: ---
	 ********************************************************************** */

	function plugin_active($folder = "") {	
		global $db;
		$enabled = $db->get_var($db->prepare("SELECT plugin_enabled FROM " . table_plugins . " WHERE plugin_folder = %s", $folder));
		if($enabled == 1) { return true; } else { return false; }
	}
	
	
	/* ******************************************************************** 
	 *  Function: num_active_plugins
	 *  Parameters: None
	 *  Purpose: Returns the number of active plugins or false if all are disabled.
	 *  Notes: ---
	 ********************************************************************** */

	function num_active_plugins() {	
		global $db;
		$enabled = $db->get_var($db->prepare("SELECT count(*) FROM " . table_plugins . " WHERE plugin_enabled = %d", 1));
		if($enabled > 0) { return $enabled; } else { return false; }
	}
		
		
	/* ******************************************************************** 
	 *  Function: uninstall_plugin
	 *  Parameters: plugin folder name
	 *  Purpose: deletes entry in table_plugins and all its entries in table_pluginhooks
	 *  Notes: ---
	 ********************************************************************** */

	function uninstall_plugin($folder = "") {	
		global $db;
			
		$db->query($db->prepare("DELETE FROM " . table_plugins . " WHERE plugin_folder = %s", $folder));
		$db->query($db->prepare("DELETE FROM " . table_pluginhooks . " WHERE plugin_folder = %s", $folder));
		$db->query($db->prepare("DELETE FROM " . table_pluginsettings . " WHERE plugin_folder = %s", $folder));
	}
		
	
	/* ******************************************************************** 
	 *  Function: check_actions
	 *  Parameters: $hook: plugin hook, 
	 *              $perform: 'true' to run the function or 'false' to just return if the function exists or not
	 * 	        $folder: plugin folder for specifying a plugin, 
	 *		$parameters: an array of optional parameters to pass to the function
	 *  Purpose: Checks if such a function exists and is part of an enabled plugin, then calls the function.
	 *  Notes: ---
	 ********************************************************************** */
	 
	function check_actions($hook = '', $perform = true, $folder = '', $parameters = array()) {
		global $db, $cage;
		if($hook == '') {
			//echo "Error: Plugin hook name not provided.";
		} else {
			$where = "";
			if(!empty($folder)) {
				$where .= "AND (" . table_plugins . ".plugin_folder = %s)";
			}
			
			$db->cache_queries = true;	// start using cache
			
			$sql = "SELECT " . table_plugins . ".plugin_enabled, " . table_plugins . ".plugin_folder, " . table_plugins . ".plugin_prefix, " . table_pluginhooks . ".plugin_hook  FROM " . table_pluginhooks . ", " . table_plugins . " WHERE (" . table_pluginhooks . ".plugin_hook = %s) AND (" . table_plugins . ".plugin_folder = " . table_pluginhooks . ".plugin_folder) " . $where;
			$plugins = $db->get_results($db->prepare($sql, $hook, $folder));
			
			$db->cache_queries = false;	// stop using cache
			
			$action_found = false;
			if($plugins) {
				foreach($plugins as $plugin) {			
					if($plugin->plugin_folder && $plugin->plugin_hook && ($plugin->plugin_enabled == 1)) {
						if(file_exists(plugins . $plugin->plugin_folder . "/" . $plugin->plugin_folder . ".php")) {
							include_once(plugins . $plugin->plugin_folder . "/" . $plugin->plugin_folder . ".php");
							if($perform == true) {
								$function_name = $plugin->plugin_prefix . "_" . $hook;
								if(function_exists($function_name)) {
									$result = $function_name($parameters);
									if($result) { $return_array[$function_name] = $result; }
								}
							}
							$action_found = true;
						} else {
							//echo "Error: Plugin file not found.";
						}
					} else {
						if($plugin->plugin_enabled != 1) {
							//echo "Error: This plugin is not active.";
						} else {
							//echo "Error: Plugin function not found.";
						}
					}
				}
			} else {
				return false;
			}	
		}
		
		if(!empty($return_array)) {
			return $return_array;		// returns an array of return values from each function, e.g. $return_array['usr_users'] = something
		} elseif($action_found == true) {
			return true;			// at least one function exists, but nothing was returned
		} else {
			return false;			// no functions were triggered. Eitherthey weren't found or they were surpressed by $perform = false.
		}
	}
			
	
	/* ******************************************************************** 
	 *  Function: plugin_settings
	 *  Parameters: Plugin folder name and setting value to retrieve
	 *  Purpose: Returns the settings for a given plugin
	 *  Notes: ---
	 ********************************************************************** */
	 
	function plugin_settings($folder = '', $setting = '') {
		global $db;
		$sql = "SELECT plugin_value FROM " . table_pluginsettings . " WHERE (plugin_folder = %s) AND (plugin_setting = %s)";
		$value = $db->get_var($db->prepare($sql, $folder, $setting));
		if($value) { return $value; } else { return false; }
	}
	
	
	/* ******************************************************************** 
	 *  Function: plugin_setting_exists
	 *  Parameters: Plugin folder name and setting name
	 *  Purpose: Determines if a setting already exists
	 *  Notes: The actual value is ignored
	 ********************************************************************** */
	 	
	function plugin_setting_exists($folder = '', $setting = '') {
		global $db;
		$sql = "SELECT plugin_setting FROM " . table_pluginsettings . " WHERE (plugin_folder = %s) AND (plugin_setting = %s)";
		$returned_setting = $db->get_var($db->prepare($sql, $folder, $setting));
		if($returned_setting) { return $returned_setting; } else { return false; }
	}	
	
	/* ******************************************************************** 
	 *  Function: plugin_settings_update
	 *  Parameters: Plugin folder name, setting to update, and new value
	 *  Purpose: Updates a plugin setting
	 *  Notes: ---
	 ********************************************************************** */
	
	function plugin_settings_update($folder = '', $setting = '', $value = '') {
		global $db;
		$exists = $this->plugin_setting_exists($folder, $setting);
		if(!$exists) {
			$sql = "INSERT INTO " . table_pluginsettings . " (plugin_folder, plugin_setting, plugin_value) VALUES (%s, %s, %s)";
			$db->query($db->prepare($sql, $folder, $setting, $value));
		} else {
			$sql = "UPDATE " . table_pluginsettings . " SET plugin_folder = %s, plugin_setting = %s, plugin_value = %s WHERE (plugin_folder = %s) AND (plugin_setting = %s)";
			$db->query($db->prepare($sql, $folder, $setting, $value, $folder, $setting));
		}
	}


	/* ******************************************************************** 
	 *  Function: plugin_settings_remove_setting
	 *  Parameters: Plugin setting name
	 *  Purpose: Deletes rows from pluginsettings that match that setting
	 *  Notes: ---
	 ********************************************************************** */
	 	
	function plugin_settings_remove_setting($setting = '') {
		global $db;
		$sql = "DELETE FROM " . table_pluginsettings . " WHERE plugin_setting = %s";
		$db->query($db->prepare($sql, $setting));
	}
	
	
	/* ******************************************************************** 
	 *  Function: plugin_settings_remove_plugin
	 *  Parameters: Plugin folder name
	 *  Purpose: Deletes rows from pluginsettings that match that plugin folder name
	 *  Notes: ---
	 ********************************************************************** */	
	 
	function plugin_settings_remove_plugin($folder = '') {
		global $db;
		$sql = "DELETE FROM " . table_pluginsettings . " WHERE plugin_folder = %s";
		$db->query($db->prepare($sql, $folder));
	}
	
}

?>