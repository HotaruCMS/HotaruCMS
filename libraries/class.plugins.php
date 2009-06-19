<?php

/* ******************************************************************** 
 *  File: /libraries/class.plugins.php
 *  Purpose: Two Classes: Plugins and Plugin. The first deals with managing all plugins. The second deals with individual plugins.
 *  Notes: Plugins extends the generic_pmd class which is a 3rd party script called "Generic PHP Config"
 ********************************************************************** */
 
// includes
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
		global $db;
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
					$allplugins[$count]['version'] = $plugin_row->plugin_version;
					$allplugins[$count]['description'] = $plugin_row->plugin_desc;
					$allplugins[$count]['folder'] = $plugin_row->plugin_folder;
					$allplugins[$count]['status'] = $this->get_plugin_status($plugin_row->plugin_folder);
				} else {
					// if plugin is not in database OR plugin in folder is newer...
					$allplugins[$count]['name'] = $plugin_details['name'];
					$allplugins[$count]['version'] = $plugin_details['version'];
					$allplugins[$count]['description'] = $plugin_details['description'];
					$allplugins[$count]['folder'] = $plugin_details['folder'];
					$allplugins[$count]['status'] = $this->get_plugin_status($plugin_details['folder']);
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
		
		$this->id = $db->get_var($db->prepare("SELECT plugin_id FROM " . table_plugins . " WHERE plugin_folder = %s", $this->folder));
		
		foreach($this->hooks as $hook) {
			if(!$db->get_var($db->prepare("SELECT plugin_id FROM " . table_pluginmeta . " WHERE (plugin_id = %d) AND (plugin_hook = %s)", $this->id, $hook))) {
				$sql = "INSERT INTO " . table_pluginmeta . " (plugin_id, plugin_hook) VALUES (%d, %s)";
				$db->query($db->prepare($sql, $this->id, trim($hook)));
			} else {
				$sql = "UPDATE " . table_pluginmeta . " SET plugin_hook = %s WHERE plugin_id = %d";
				$db->query($db->prepare($sql, trim($hook), $this->id));
			}
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
			$sql = "SELECT " . table_plugins . ".plugin_enabled, " . table_plugins . ".plugin_id, " . table_plugins . ".plugin_folder, " . table_pluginmeta . ".plugin_id," . table_pluginmeta . ".plugin_hook  FROM " . table_pluginmeta . ", " . table_plugins . " WHERE (" . table_pluginmeta . ".plugin_hook = %s) AND (" . table_plugins . ".plugin_id = " . table_pluginmeta . ".plugin_id)";
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
			}	
		}
	}
	
	/*
	function remove_plugin($folder = "") {	
		global $db;
			
		$sql = "DELETE FROM " . table_plugins . " WHERE plugin_folder = %s";
		$db->query($db->prepare($sql, $folder));
	}
	*/
}

