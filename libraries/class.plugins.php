<?php

/* ******************************************************************** 
 *  File: /libraries/class.plugins.php
 *  Purpose: Two Classes: Plugins and Plugin. The first deals with managing all plugins. The second deals with individual plugins.
 *  Notes: Plugins extends the generic_pmd class which is a 3rd party script called "Generic PHP Config"
 ********************************************************************** */
 
// includes
if(file_exists('hotaru_header.php')) {
	require_once('hotaru_header.php');	// assumes we are in the root directory
} else {
	require_once('../hotaru_header.php');	// assumes we are one level deep, e.g. in the admin directory
}
require_once(libraries . 'class.metadata.php');	// This is the generic_pmd class that reads post metadata from the top of a plugin file.

class Plugins extends generic_pmd {
	
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
					$allplugins[$count]['version'] = $plugin_row->plugin_version . "<br />" . $lang['admin_plugins_class_new_version'];
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
}

/* ****************************************************************************************************************** 
   ****************************************************************************************************************** 
   ****************************************************************************************************************** 
   ****************************************************************************************************************** */

class Plugin extends Plugins {
	var $id = '';
	var $enabled = 0;
	var $name = '';
	var $desc = '';
	var $folder = '';
	var $version = 0;
	var $message = '';
	var $hooks = array();


	/* ******************************************************************** 
	 *  Function: install_plugin
	 *  Parameters: plugin folder name
	 *  Purpose: Adds plugin to plugins table, or updates it
	 *  Notes: ---
	 ********************************************************************** */
	 
	function install_plugin($folder = "") {
		global $db;
		
		$plugin_metadata = $this->read(plugins . $folder . "/" . $folder . ".php");
		
		$this->enabled = 1;	// Enable it at the same time we add it to the database.
		$this->name = $plugin_metadata['name'];
		$this->desc = $plugin_metadata['description'];
		$this->folder = $folder;
		$this->version = $plugin_metadata['version'];
		$this->hooks = explode(',', $plugin_metadata['hooks']);
	
		$sql = "INSERT INTO " . table_plugins . " (plugin_enabled, plugin_name, plugin_desc, plugin_folder, plugin_version) VALUES (%d, %s, %s, %s, %s)";
		$db->query($db->prepare($sql, $this->enabled, $this->name, $this->desc, $this->folder, $this->version));
		
		foreach($this->hooks as $hook) {
			$sql = "INSERT INTO " . table_pluginhooks . " (plugin_folder, plugin_hook) VALUES (%s, %s)";
			$db->query($db->prepare($sql, $this->folder, trim($hook)));
		}
	}
	
	
	/* ******************************************************************** 
	 *  Function: activate_deactivate_plugin
	 *  Parameters: plugin folder name, enabled status
	 *  Purpose: Enables or disables a plugin, installing if necessary
	 *  Notes: This function does not uninstall/delete a plugin.
	 ********************************************************************** */
	 
	function activate_deactivate_plugin($folder = "", $enabled = 0) {	// 0 = deactivate, 1 = activate
		global $db;
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
	 *  Purpose: Give a plugin's folder name, it returns tha actual name.
	 *  Notes: ---
	 ********************************************************************** */

	function plugin_name($folder = "") {	
		global $db;
		$this->name = $db->get_var($db->prepare("SELECT plugin_name FROM " . table_plugins . " WHERE plugin_folder = %s", $folder));
		return $this->name;
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
	 *  Parameters: plugin main function name (matches the plugin folder name), array of optional parameters
	 *  Purpose: Checks if such a function exists and is part of an enabled plugin, then calls the function.
	 *  Notes: ---
	 ********************************************************************** */
	 
	function check_actions($hook = '', $parameters = array()) {
		global $db;
		if($hook == '') {
			echo "Error: Plugin hook name not provided.";
		} else {
			$sql = "SELECT " . table_plugins . ".plugin_enabled, " . table_plugins . ".plugin_folder, " . table_pluginhooks . ".plugin_hook  FROM " . table_pluginhooks . ", " . table_plugins . " WHERE (" . table_pluginhooks . ".plugin_hook = %s) AND (" . table_plugins . ".plugin_folder = " . table_pluginhooks . ".plugin_folder)";
			$plugins = $db->get_results($db->prepare($sql, $hook));
			if($plugins) {
				foreach($plugins as $plugin) {			
					if($plugin->plugin_folder && $plugin->plugin_hook && ($plugin->plugin_enabled == 1)) {
						if(file_exists(plugins . $plugin->plugin_folder . "/" . $plugin->plugin_folder . ".php")) {
							include_once(plugins . $plugin->plugin_folder . "/" . $plugin->plugin_folder . ".php");
							$hook($parameters);
						} else {
							echo "Error: Plugin file not found.";
						}
					} else {
						if($plugin->plugin_enabled != 1) {
							echo "Error: This plugin is not active.";
						} else {
							echo "Error: Plugin function not found.";
						}
					}
				}
			} else {
				return false;
			}	
		}
	}
		
	
	/* ******************************************************************** 
	 *  Function: plugin_settings
	 *  Parameters: Plugin folder name and setting to retrieve
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
	 *  Function: plugin_settings_update
	 *  Parameters: Plugin folder name, setting to update, and new value
	 *  Purpose: Updates a plugin setting
	 *  Notes: ---
	 ********************************************************************** */
	
	function plugin_settings_update ($folder = '', $setting = '', $value = '') {
		global $db;
		$exists = $this->plugin_settings($folder, $setting);
		if(!$exists) {
			$sql = "INSERT INTO " . table_pluginsettings . " (plugin_folder, plugin_setting, plugin_value) VALUES (%s, %s, %s)";
			$db->query($db->prepare($sql, $folder, $setting, $value));
		} else {
			$sql = "UPDATE " . table_pluginsettings . " SET plugin_folder = %s, plugin_setting = %s, plugin_value = %s WHERE (plugin_folder = %s) AND (plugin_setting = %s)";
			$db->query($db->prepare($sql, $folder, $setting, $value, $folder, $setting));
		}
	}
	
	
	/* ******************************************************************** 
	 *  Function: plugin_settings
	 *  Parameters: Plugin folder name and form method (post or get)
	 *  Purpose: Opens the form, forces sanitation through Inspekt in functions/funcs.forms.php
	 *  Notes: Passes the plugin name alomg as a hidden value so we know how to get back to the plugin
	 ********************************************************************** */
	 
	function plugin_form_open($folder = '', $method = 'get') {
		echo "<form name='" . $folder . "_form' action='" . baseurl . "functions/funcs.forms.php' method='" . $method . "'>\n";
		echo "<input type='hidden' name='plugin' value='" . $folder . "'>";
	}
	
	
	/* ******************************************************************** 
	 *  Function: plugin_form_close
	 *  Parameters: None
	 *  Purpose: Closes the form
	 *  Notes: ---
	 ********************************************************************** */
	 
	function plugin_form_close() {
		echo "</form>\n";
	}
}

