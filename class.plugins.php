<?php

/* **************************************************************************************************** 
 *  File: /class.plugins.php
 *  Purpose: Manages all things plugin-related.
 *  Notes: Plugin extends the generic_pmd class in class.metadata.php which is a 3rd party script called "Generic PHP Config"
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
	var $prefix = '';
	var $folder = '';
	var $desc = '';
	var $version = 0;
	var $order = 0;
	var $requires = '';		// string
	var $dependencies = array();	// same as $requires but an array of plugin->version pairs.
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
					$allplugins[$count]['install'] = "installed";
					$allplugins[$count]['location'] = "database";
					$allplugins[$count]['order'] = $plugin_row->plugin_order;
				} elseif($plugin_row && version_compare($plugin_details['version'], $plugin_row->plugin_version, '>')) {
					//plugin exists in database, but it's an older version than the one in the folder...
					$allplugins[$count]['name'] = $plugin_row->plugin_name;
					$allplugins[$count]['description'] = $plugin_row->plugin_desc;
					$allplugins[$count]['folder'] = $plugin_row->plugin_folder;
					$allplugins[$count]['status'] = $this->get_plugin_status($plugin_row->plugin_folder);
					$allplugins[$count]['version'] = $plugin_row->plugin_version;
					$allplugins[$count]['install'] = "upgrade";
					$allplugins[$count]['location'] = "database";
					$allplugins[$count]['order'] = $plugin_row->plugin_order;
				} else {
					// if plugin is not in database...
					$allplugins[$count]['name'] = $plugin_details['name'];
					$allplugins[$count]['description'] = $plugin_details['description'];
					$allplugins[$count]['folder'] = $plugin_details['folder'];
					$allplugins[$count]['status'] = "inactive";
					$allplugins[$count]['version'] = $plugin_details['version'];
					$allplugins[$count]['install'] = "install";
					$allplugins[$count]['location'] = "folder";
					$allplugins[$count]['order'] = 0;
				}
				
				// Conditions for "active"...
				if($allplugins[$count]['status'] == 'active') {
					$allplugins[$count]['active'] = "<img src='" . baseurl . "content/admin_themes/" . admin_theme . "images/active.png'>";
				} else {
					$allplugins[$count]['active'] = "<img src='" . baseurl . "content/admin_themes/" . admin_theme . "images/inactive.png'>";
				}
				
				// Conditions for "status"...
				if($allplugins[$count]['status'] == 'active') { 
					$allplugins[$count]['status'] = "<a href='" . baseurl;
					$allplugins[$count]['status'] .= "admin/admin_index.php?page=plugins&amp;action=deactivate&amp;plugin=";
					$allplugins[$count]['status'] .= $allplugins[$count]['folder'] . "'>" . $lang['admin_plugins_off'] . "</a>";
				} elseif($allplugins[$count]['status'] != 'install') { 
					$allplugins[$count]['status'] = "<a href='" . baseurl;
					$allplugins[$count]['status'] .= "admin/admin_index.php?page=plugins&amp;action=activate&amp;plugin=";
					$allplugins[$count]['status'] .= $allplugins[$count]['folder'] . "'>" . $lang['admin_plugins_on'] . "</a>";
				} else {
					$allplugins[$count]['status'] = '';
				}
				
				// Conditions for "install"...
				if($allplugins[$count]['install'] == 'install') { 
					$allplugins[$count]['install'] = "<a href='" . baseurl . "admin/admin_index.php?page=plugins&amp;action=install&amp;plugin=". $allplugins[$count]['folder'] . "'>" . $lang['admin_plugins_install'] . "</a>";
				} elseif($allplugins[$count]['install'] == 'installed') { 
					$allplugins[$count]['install'] = "<a href='" . baseurl . "admin/admin_index.php?page=plugins&amp;action=uninstall&amp;plugin=". $allplugins[$count]['folder'] . "' style='color: red; font-weight: bold'>" . $lang['admin_plugins_uninstall'] . "</a>";
				} elseif($allplugins[$count]['install'] == 'upgrade') { 
					$allplugins[$count]['install'] = "<a href='" . baseurl . "admin/admin_index.php?page=plugins&amp;action=upgrade&amp;plugin=". $allplugins[$count]['folder'] . "' style='color: #ff9900; font-weight: bold'>" . $lang['admin_plugins_upgrade'] . "</a>";
				} else {
					$allplugins[$count]['install'] = $lang['admin_plugins_installed'];
				}
				
				// Conditions for "requires"...
				if(isset($plugin_details['requires']) && $plugin_details['requires']) {
					$this->requires = $plugin_details['requires'];
					$this->requires_to_dependencies();
					
					// Converts plugin folder names to well formatted names...
					foreach($this->dependencies as $this_plugin => $version) {
						$formatted_plugin = $this->folder_to_name($this_plugin);
						unset($this->dependencies[$this_plugin]);
						$this->dependencies[$formatted_plugin] = $version;
						$allplugins[$count]['requires'][$formatted_plugin] = $this->dependencies[$formatted_plugin];
					}
					
				} else {
					$allplugins[$count]['requires'] = array();
				}
				
				
				// Conditions for "order"...
				// The order is sorted numerically in the plugins.php template, so we need separate order and order_output elements.
				if($allplugins[$count]['order'] != 0) { 
					$order = $allplugins[$count]['order'];
					$allplugins[$count]['order_output'] = "<a href='" . baseurl;
					$allplugins[$count]['order_output'] .= "admin/admin_index.php?page=plugins&amp;";
					$allplugins[$count]['order_output'] .= "action=orderup&amp;plugin=". $allplugins[$count]['folder'];
					$allplugins[$count]['order_output'] .= "&amp;order=" . $order . "'>";
					$allplugins[$count]['order_output'] .= "<img src='" . baseurl . "content/admin_themes/" . admin_theme . "images/up.png'>";
					$allplugins[$count]['order_output'] .= "</a> \n<a href='" . baseurl;
					$allplugins[$count]['order_output'] .= "admin/admin_index.php?page=plugins&amp;";
					$allplugins[$count]['order_output'] .= "action=orderdown&amp;plugin=". $allplugins[$count]['folder'];
					$allplugins[$count]['order_output'] .= "&amp;order=" . $order . "'>";
					$allplugins[$count]['order_output'] .= "<img src='" . baseurl . "content/admin_themes/" . admin_theme . "images/down.png'>";
					$allplugins[$count]['order_output'] .= "</a>\n";
				} else {
					$allplugins[$count]['order_output'] = "";
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
	 *  Function: requires_to_dependencies
	 *  Parameters: None
	 *  Purpose: Converts $this->requires into $this->dependencies array.
	 *  Notes: Result is array containing 'category_manager' -> '0.1' pairs
	 ********************************************************************** */

	function requires_to_dependencies() {
		unset($this->dependencies);
		foreach(explode(',', $this->requires) as $pair) {
			list($k,$v) = explode (' ', trim($pair));
       			$this->dependencies[$k] = $v;
		}
	}

	
	/* ******************************************************************** 
	 *  Function: folder_to_name
	 *  Parameters: A plugin folder name, e.g. 'category_manager'
	 *  Purpose: Changes 'category_manager' into 'Category Manager'
	 *  Notes: ---
	 ********************************************************************** */

	function folder_to_name($plugin) {
		$dep_array = array();
		
		$dep_array = explode('_', trim($plugin));
		$dep_array = array_map('ucfirst', $dep_array);
		$plugin = implode(' ', $dep_array);
		
		return $plugin;
	}


	/* ******************************************************************** 
	 *  Function: get_plugin_status
	 *  Parameters: widget folder name
	 *  Purpose: Determines if a plugin is enabled or not
	 *  Notes: ---
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
	 *  Parameters: plugin folder name, optional upgrade flag
	 *  Purpose: Adds plugin to plugins table
	 *  Notes: The upgrade argument is used by upgrade_plugin(). If a a new 
	 * 	   version of a plugin doesn't have an upgrade script, it's 
	 *         uninstalled then sent here for a reinstall instead.
	 *         The flag is used to disable show_message.
	 ********************************************************************** */
	 
	function install_plugin($folder = "", $upgrade = 0) {
		global $db, $lang, $hotaru, $current_user, $admin;
		
		$admin->delete_files(includes . 'ezSQL/cache');	// Clear the database cache to ensure stored plugins and hooks are up-to-date.
		
		$plugin_metadata = $this->read(plugins . $folder . "/" . $folder . ".php");
		
		$this->enabled = 1;	// Enable it at the same time we add it to the database.
		$this->name = $plugin_metadata['name'];
		$this->desc = $plugin_metadata['description'];
		$this->folder = $folder;
		$this->prefix = $plugin_metadata['prefix'];
		$this->version = $plugin_metadata['version'];
		$this->hooks = explode(',', $plugin_metadata['hooks']);
		
		if(isset($plugin_metadata['requires']) && $plugin_metadata['requires']) {
			$this->requires = $plugin_metadata['requires'];
			$this->requires_to_dependencies();
		}
		
		$dependency_error = 0;
		foreach($this->dependencies as $dependency => $version) {
			if(version_compare($version, $this->plugin_active($dependency), '>')) {
				$dependency_error = 1;
			}
		}
		
		if($dependency_error == 1) {
			foreach($this->dependencies as $dependency => $version) {
					if($this->get_plugin_status($dependency) == 'inactive') {
						$dependency = $this->folder_to_name($dependency);				
						$hotaru->messages[$lang["admin_plugins_install_sorry"] . " " . $this->name . " " . $lang["admin_plugins_install_requires"] . " " . $dependency . " " . $version] = 'red';
					}
			}
			return false;	
		}
					
		$sql = "REPLACE INTO " . table_plugins . " (plugin_enabled, plugin_name, plugin_prefix, plugin_folder, plugin_desc, plugin_requires, plugin_version, plugin_updateby) VALUES (%d, %s, %s, %s, %s, %s, %s, %d)";
		$db->query($db->prepare($sql, $this->enabled, $this->name, $this->prefix, $this->folder, $this->desc, $this->requires, $this->version, $current_user->id));
		
		// Get the last order number - doing this after REPLACE INTO because we don't know whether the above will insert or replace.
		$sql = "SELECT plugin_order FROM " . table_plugins . " ORDER BY plugin_order DESC LIMIT 1";
		$highest_order = $db->get_var($db->prepare($sql));
		
		// Give the new plugin the order number + 1
		$sql = "UPDATE " . table_plugins . " SET plugin_order = %d WHERE plugin_id = LAST_INSERT_ID()";
		$db->query($db->prepare($sql, ($highest_order + 1)));
		
		// Add any plugin hooks to the hooks table
		$this->add_plugin_hooks();
			
		$result = $this->check_actions('install_plugin', $folder);
		
		// For plugins to avoid showing this success message, they need to return a non-boolean value to $result.
		if(!is_array($result)) {
			if($upgrade == 0) {
				$hotaru->messages[$lang["admin_plugins_install_done"]] = 'green';
			} else {
				$hotaru->messages[$lang["admin_plugins_upgrade_done"]] = 'green';
			}
		}
	}
	
	
	/* ******************************************************************** 
	 *  Function: plugin_hook_exists
	 *  Parameters: Hook name
	 *  Purpose: Determines if a hook already exists
	 *  Notes: ---
	 ********************************************************************** */
	 	
	function plugin_hook_exists($hook = "") {
		global $db;
		
		$sql = "SELECT plugin_hook FROM " . table_pluginhooks . " WHERE (plugin_folder = %s) AND (plugin_hook = %s)";
		$returned_hook = $db->get_var($db->prepare($sql, $this->folder, $hook));
		if($returned_hook) { return $returned_hook; } else { return false; }
	}
	
	
	/* ******************************************************************** 
	 *  Function: add_plugin_hooks
	 *  Parameters: None
	 *  Purpose: Adds all hooks for a given plugin
	 *  Notes: ---
	 ********************************************************************** */
	 	
	function add_plugin_hooks() {
		global $db, $current_user;
		
		foreach($this->hooks as $hook) {
			$exists = $this->plugin_hook_exists(trim($hook));
			if(!$exists) {
				$sql = "INSERT INTO " . table_pluginhooks . " (plugin_folder, plugin_hook, plugin_updateby) VALUES (%s, %s, %d)";
				$db->query($db->prepare($sql, $this->folder, trim($hook), $current_user->id));
			}
		}
	}
	
	
	/* ******************************************************************** 
	 *  Function: upgrade_plugin
	 *  Parameters: plugin folder name
	 *  Purpose: Plugins hook in here with their own upgrade scripts.
	 *  Notes: This function does nothing by itself other than read the latest file's metadata.
	 ********************************************************************** */
	 
	function upgrade_plugin($folder = "") {
		global $db, $lang, $hotaru, $admin;
		
		$plugin_metadata = $this->read(plugins . $folder . "/" . $folder . ".php");
		
		$this->enabled = 1;	// Enable it at the same time we add it to the database.
		$this->name = $plugin_metadata['name'];
		$this->desc = $plugin_metadata['description'];
		$this->folder = $folder;
		$this->prefix = $plugin_metadata['prefix'];
		$this->version = $plugin_metadata['version'];
		$this->hooks = explode(',', $plugin_metadata['hooks']);
		
		if(in_array('upgrade_plugin', $this->hooks)) {
			$admin->delete_files(includes . 'ezSQL/cache');	// Clear the database cache to ensure stored plugins and hooks are up-to-date.
			$this->add_plugin_hooks(); // Add any new plugin hooks to the hooks table before proceeding with upgrade
		} else {
			// Uninstall and then re-install because there's no upgrade function
			$this->uninstall_plugin($folder, 1);
			$this->install_plugin($folder, 1);
		}
				
		$result = $this->check_actions('upgrade_plugin', $folder);
				
		// For plugins to avoid showing this success message, they need to return a non-boolean value to $result.
		if(!is_array($result)) {
			$hotaru->messages[$lang["admin_plugins_upgrade_done"]] = 'green';
		}
	}
	
	
	/* ******************************************************************** 
	 *  Function: activate_deactivate_plugin
	 *  Parameters: plugin folder name, enabled status
	 *  Purpose: Enables or disables a plugin, installing if necessary
	 *  Notes: This function does not uninstall/delete a plugin.
	 ********************************************************************** */
	 
	function activate_deactivate_plugin($folder = "", $enabled = 0) {	// 0 = deactivate, 1 = activate
		global $db, $hotaru, $lang, $admin, $current_user;
		
		$admin->delete_files(includes . 'ezSQL/cache');	// Clear the database cache to ensure stored plugins and hooks are up-to-date.
		
		$plugin_row = $db->get_row($db->prepare("SELECT plugin_folder, plugin_enabled FROM " . table_plugins . " WHERE plugin_folder = %s", $folder));
		if(!$plugin_row) {
			if($enabled == 1) {	// without this, the plugin would be installed and then deactivated, which is dumb. Let's just not install it yet!
				$this->install_plugin($folder);
			}
		} else {
			if($plugin_row->plugin_enabled != $enabled) {		// only update if we're changing the enabled value.
				$sql = "UPDATE " . table_plugins . " SET plugin_enabled = %d, plugin_updateby = %d WHERE plugin_folder = %s";
				$db->query($db->prepare($sql, $enabled, $current_user->id, $folder));
				
				if($enabled == 1) { $hotaru->messages[$lang["admin_plugins_activated"]] = 'green'; }
				if($enabled == 0) { $hotaru->messages[$lang["admin_plugins_deactivated"]] = 'green'; }
			}
		}
	}
	
	
	/* ******************************************************************** 
	 *  Function: uninstall_plugin
	 *  Parameters: plugin folder name, optional upgrade flag
	 *  Purpose: deletes entry in table_plugins and all its entries in table_pluginhooks
	 *  Notes: If upgrade_plugin() calls this function, the flag is used to disable the message.
	 ********************************************************************** */

	function uninstall_plugin($folder = "", $upgrade = 0) {	
		global $db, $hotaru, $lang, $admin;
		
		$admin->delete_files(includes . 'ezSQL/cache');	// Clear the database cache to ensure stored plugins and hooks are up-to-date.
			
		$db->query($db->prepare("DELETE FROM " . table_plugins . " WHERE plugin_folder = %s", $folder));
		$db->query($db->prepare("DELETE FROM " . table_pluginhooks . " WHERE plugin_folder = %s", $folder));
		$db->query($db->prepare("DELETE FROM " . table_pluginsettings . " WHERE plugin_folder = %s", $folder));
		
		if($upgrade == 0) {
			$hotaru->messages[$lang["admin_plugins_uninstall_done"]] = 'green';
		}
		
		$this->refresh_plugin_order();
	}
	
	
	/* ******************************************************************** 
	 *  Function: plugin_order
	 *  Parameters: plugin folder name, current order#, direction (up or down)
	 *  Purpose: Updates plugin order and order of their hooks, i.e. changes the order of plugins in check_actions.
	 *  Notes: ---
	 ********************************************************************** */

	function plugin_order($folder = "", $order = 0, $arrow = "up") {	
		global $db, $hotaru, $lang;
			
		if($order == 0) {
			$hotaru->messages[$lang['admin_plugins_order_zero']] = 'red';
			return false;
		}
				
		if($arrow == "up") {
			// get row above
			$sql= "SELECT * FROM " . table_plugins . " WHERE plugin_order = %d";
			$row_above = $db->get_row($db->prepare($sql, ($order - 1)));
			
			if(!$row_above) {
				$hotaru->messages[$this->folder_to_name($folder) . " " . $lang['admin_plugins_order_first']] = 'red';
				return false;
			}
			
			if($row_above->plugin_order == $order) {
				$hotaru->messages[$lang['admin_plugins_order_above']] = 'red';
				return false;
			}
			
			// update row above 
			$sql = "UPDATE " . table_plugins . " SET plugin_order = %d WHERE plugin_id = %d";
			$db->query($db->prepare($sql, ($row_above->plugin_order + 1), $row_above->plugin_id)); 
			
			// update current plugin
			$sql = "UPDATE " . table_plugins . " SET plugin_order = %d WHERE plugin_folder = %s";
			$db->query($db->prepare($sql, ($order - 1), $folder)); 
		} else {
			// get row below
			$sql= "SELECT * FROM " . table_plugins . " WHERE plugin_order = %d";
			$row_below = $db->get_row($db->prepare($sql, ($order + 1)));
			
			if(!$row_below) {
				$hotaru->messages[$this->folder_to_name($folder) . " " . $lang['admin_plugins_order_last']] = 'red';
				return false;
			}
			
			if($row_below->plugin_order == $order) {
				$hotaru->messages[$lang['admin_plugins_order_below']] = 'red';
				return false;
			}
			
			// update row above 
			$sql = "UPDATE " . table_plugins . " SET plugin_order = %d WHERE plugin_id = %d";
			$db->query($db->prepare($sql, ($row_below->plugin_order - 1), $row_below->plugin_id)); 
			
			// update current plugin
			$sql = "UPDATE " . table_plugins . " SET plugin_order = %d WHERE plugin_folder = %s";
			$db->query($db->prepare($sql, ($order + 1), $folder)); 
		}
		
		$hotaru->messages[$lang['admin_plugins_order_updated']] = 'green';
		
		$this->refresh_plugin_order();	// Resort all orders and remove any accidental gaps
		
		$this->sort_plugin_hooks();
		
		return true;

	}
	
	
	/* ******************************************************************** 
	 *  Function: refresh_plugin_order
	 *  Parameters: None
	 *  Purpose: Removes gaps in plugin order where plugins have been uninstalled.
	 *  Notes: ---
	 ********************************************************************** */

	function refresh_plugin_order() {	
		global $db;
		
		$sql = "SELECT * FROM " . table_plugins . " ORDER BY plugin_order ASC";
		$rows = $db->get_results($db->prepare($sql));
		
		if($rows) { 
			$i = 1;
			foreach($rows as $row) {
				$sql = "UPDATE " . table_plugins . " SET plugin_order = %d WHERE plugin_id = %d";
				$db->query($db->prepare($sql, $i, $row->plugin_id));
				$i++; 
			}
		} else {
			return true;
		}
	}
	
	
	/* ******************************************************************** 
	 *  Function: sort_plugin_hooks
	 *  Parameters: None
	 *  Purpose: Orders the plugin hooks by plugin_order
	 *  Notes: ---
	 ********************************************************************** */

	function sort_plugin_hooks() {	
		global $db, $current_user;
		
		$sql = "SELECT p.plugin_folder, p.plugin_order, p.plugin_id, h.* FROM " . table_pluginhooks . " h, " . table_plugins . " p WHERE p.plugin_folder = h.plugin_folder ORDER BY p.plugin_order ASC";
		$rows = $db->get_results($db->prepare($sql));

		// Drop and recreate the pluginhooks table, i.e. empty it.
		$db->query($db->prepare("TRUNCATE TABLE " . table_pluginhooks));
			
		// Add plugin hooks back into the hooks table
		foreach($rows  as $row) {
			$sql = "INSERT INTO " . table_pluginhooks . " (plugin_folder, plugin_hook, plugin_updateby) VALUES (%s, %s, %d)";
			$db->query($db->prepare($sql, $row->plugin_folder, $row->plugin_hook, $current_user->id));
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
	 *  Purpose: Given a plugin's folder name, it returns the version if currently active.
	 *  Notes: ---
	 ********************************************************************** */

	function plugin_active($folder = "") {	
		global $db;
		$active= $db->get_row($db->prepare("SELECT plugin_enabled, plugin_version FROM " . table_plugins . " WHERE plugin_folder = %s", $folder));
		if($active) {
			if($active->plugin_enabled == 1) { return $active->plugin_version; } else { return false; }
		} else {
			return false;
		}
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
	 *  Function: check_actions
	 *  Parameters: $hook: plugin hook, 
	 *              $perform: 'true' to run the function or 'false' to just return if the function exists or not
	 * 	        $folder: plugin folder for specifying a plugin, 
	 *		$parameters: an array of optional parameters to pass to the function
	 *  Purpose: Checks if such a function exists and is part of an enabled plugin, then calls the function.
	 *  Notes: ---
	 ********************************************************************** */
	 
	function check_actions($hook = '', $perform = true, $folder = '', $parameters = array()) {
		global $db, $cage, $current_user;
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
			return false;			// no functions were triggered. Either they weren't found or they were surpressed by $perform = false.
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
		global $db, $current_user;
		$exists = $this->plugin_setting_exists($folder, $setting);
		if(!$exists) {
			$sql = "INSERT INTO " . table_pluginsettings . " (plugin_folder, plugin_setting, plugin_value, plugin_updateby) VALUES (%s, %s, %s, %d)";
			$db->query($db->prepare($sql, $folder, $setting, $value, $current_user->id));
		} else {
			$sql = "UPDATE " . table_pluginsettings . " SET plugin_folder = %s, plugin_setting = %s, plugin_value = %s, plugin_updateby = %d WHERE (plugin_folder = %s) AND (plugin_setting = %s)";
			$db->query($db->prepare($sql, $folder, $setting, $value, $current_user->id, $folder, $setting));
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
	
	
	/* ******************************************************************** 
	 *  Function: include_language_file
	 *  Parameters: plugin folder name, optional filename (no extension)
	 *  Purpose: Includes a plugin's language file
	 *  Notes: the language file must be in a folder named languages. '_language.php' is appended automatically.
	 ********************************************************************** */	
	 
	function include_language_file($folder = '', $filename = '') {
		global $lang;
		if($folder) {
		
			if(!$filename) { $filename = $folder; }
			
			// First look in the plugin folder for a language file... 
			if(file_exists(plugins . $folder . '/languages/' . $filename . '_language.php')) {
				require_once(plugins . $folder . '/languages/' . $filename . '_language.php');
			
			// If not there, look in the user's language_pack folder for a language file...
			} elseif(file_exists(languages . language_pack . 'plugins/' . $filename . '_language.php')) {
				require_once(languages . language_pack . 'plugins/' . $filename . '_language.php');
				
			// Finally, look in the default language_pack folder for a language file...
			} else {
				require_once(languages . 'language_default/plugins/' . $filename . '_language.php');
			}
		}
	}
	
	
	/* ******************************************************************** 
	 *  Function: include_css_file
	 *  Parameters: plugin folder name, optional filename (no extension)
	 *  Purpose: Includes a plugin's CSS file
	 *  Notes: the css file must be in a folder named css and a file of the format plugin_name.css, e.g. rss_show.css
	 ********************************************************************** */	
	 
	function include_css_file($folder = '', $filename = '') {
		global $lang;
		if($folder) {
		
			if(!$filename) { $filename = $folder; }
			
			// First look in the plugin folder for a css file... 
			if(file_exists(plugins . $folder . '/css/' . $filename . '.css')) {
				echo "<link rel='stylesheet' href='" . baseurl . "content/plugins/" . $folder . "/css/" . $filename. ".css' type='text/css'>\n";
				
			// If not found, look in the theme folder for a css file... 	
			} elseif(file_exists(themes . theme . 'css/' . $filename . '.css')) {	
				echo "<link rel='stylesheet' href='" . baseurl . "content/themes/" . theme . "css/" . $filename . ".css' type='text/css'>\n";
			}
		}
	}
	
	
	/* ******************************************************************** 
	 *  Function: include_js_file
	 *  Parameters: plugin folder name, optional filename (no extension)
	 *  Purpose: Includes a plugin's javascript file
	 *  Notes: the js file must be in a folder named javascript and a file of the format plugin_name.js, e.g. category_manager.js
	 ********************************************************************** */	
	 
	function include_js_file($folder = '', $filename = '') {
		global $lang;
		
		if($folder) {
			if(!$filename) { $filename = $folder; }
			
			// First, look in the plugin folder for a js file... 
			if(file_exists(plugins . $folder . '/javascript/' . $filename . '.js')) {
				echo "<script language='JavaScript' src='" . baseurl . "content/plugins/" . $folder . "/javascript/" . $filename . ".js'></script>\n";
			
			// If not found, look in the theme folder for a js file... 	
			} elseif(file_exists(themes . theme . 'javascript/' . $filename . '.js')) {	
				echo "<script language='JavaScript' src='" . baseurl . "content/themes/" . theme . "javascript/" . $filename . ".js'></script>\n";
			}
		}
	}
	
}

?>