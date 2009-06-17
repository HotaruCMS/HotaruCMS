<?php

/* Two Classes: Plugins and Plugin. The fist deals with managing all plugins. The second deals with individual plugins. */

// includes
require_once('../hotaru_header.php');	
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

class Plugin extends Plugins {
	var $id = '';
	var $enabled = 0;
	var $name = '';
	var $desc = '';
	var $folder = '';
	var $version = 0;


	/* ******************************************************************** 
	 *  Function: install_plugin
	 *  Parameters: plugin folder name
	 *  Purpose: Adds plugin to plugins table, or updates it
	 *  Notes: ---
	 ********************************************************************** */
	 
	function install_plugin($folder = "") {
		global $db;
		
		$plugin_metadata = $this->read(plugins . $folder . "/" . $folder . ".php");
		
		$this->enabled = 0;	// just because it's installed doesn't mean it should be enabled.
		$this->name = $plugin_metadata['name'];
		$this->desc = $plugin_metadata['description'];
		$this->folder = $folder;
		$this->version = $plugin_metadata['version'];
		
		if(!$db->get_var($db->prepare("SELECT plugin_folder FROM " . table_plugins . " WHERE plugin_folder = %s", $this->folder))) {
			$sql = "INSERT INTO " . table_plugins . " (plugin_enabled, plugin_name, plugin_desc, plugin_folder, plugin_version) VALUES (%d, %s, %s, %s, %s)";
		} else {
			$sql = "UPDATE " . table_plugins . " SET plugin_enabled = %d, plugin_name = %s, plugin_desc = %s, plugin_folder = %s, plugin_version = %s WHERE plugin_folder = %s";
		}
		$db->query($db->prepare($sql, $this->enabled, $this->name, $this->desc, $this->folder, $this->version, $this->folder));
	}
	
	
	/* ******************************************************************** 
	 *  Function: activate_deactivate_plugin
	 *  Parameters: plugin folder name, enabled status
	 *  Purpose: Enables or disables a plugin, installing if necessary
	 *  Notes: This function does not uninstall/delete a plugin.
	 ********************************************************************** */
	 
	function activate_deactivate_plugin($folder = "", $enabled = 0) {	// 0 = deactivate, 1 = activate
		global $db;
		if(!$db->get_var($db->prepare("SELECT plugin_folder FROM " . table_plugins . " WHERE plugin_folder = %s", $folder))) {
			$this->install_plugin($folder);
		} else {
			$sql = "UPDATE " . table_plugins . " SET plugin_enabled = %d WHERE plugin_folder = %s";
			$db->query($db->prepare($sql, $enabled, $folder));
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

