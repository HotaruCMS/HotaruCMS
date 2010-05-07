<?php
/**
 * Plugin Management Functions
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
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2010, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
class PluginManagement
{
	/**
	 * Get an array of plugins
	 *
	 * Reads plugin info directly from top of plugin files, then compares each 
	 * plugin to the database. If present and latest version, reads info from 
	 * the database. If not in database or newer version, uses info from plugin 
	 * file. Used by Plugin Management.
	 *
	 * @return array $allplugins
	 */
	public function getPlugins($h)
	{
		$plugins_array = $this->getPluginsMeta();
		$count = 0;
		$allplugins = array();
		
		if ($plugins_array) {
			foreach ($plugins_array as $plugin_details) {
			
				$allplugins[$count] = array();
				$sql = "SELECT * FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s";
				$plugin_row = $h->db->get_row($h->db->prepare($sql, $plugin_details['folder']));
				
				if ($plugin_row) 
				{
					// if plugin is in the database...
					$allplugins[$count]['name'] = $plugin_row->plugin_name;
					$allplugins[$count]['description'] = $plugin_row->plugin_desc;
					$allplugins[$count]['folder'] = $plugin_row->plugin_folder;
					$allplugins[$count]['author'] = $plugin_row->plugin_author;
					$allplugins[$count]['authorurl'] = urldecode($plugin_row->plugin_authorurl);
					
					if ($plugin_row->plugin_enabled) {
						$allplugins[$count]['status'] = 'active';
					} else {
						$allplugins[$count]['status'] = 'inactive';
					}
					
					$allplugins[$count]['version'] = $plugin_row->plugin_version;
					$allplugins[$count]['install'] = "installed";
					$allplugins[$count]['location'] = "database";
					$allplugins[$count]['settings'] = $h->hasSettings($allplugins[$count]['folder']); // true or false
					$allplugins[$count]['order'] = $plugin_row->plugin_order;
				} 
				else 
				{
					// if plugin is not in database...
					$allplugins[$count]['name'] = $plugin_details['name'];
					$allplugins[$count]['description'] = $plugin_details['description'];
					$allplugins[$count]['folder'] = $plugin_details['folder'];
					
					if (isset($plugin_details['author'])) {
						$allplugins[$count]['author'] = $plugin_details['author'];
					}
					
					if (isset($plugin_details['authorurl'])) {
						$allplugins[$count]['authorurl'] = urldecode($plugin_details['authorurl']);
					}
					
					$allplugins[$count]['status'] = "inactive";
					$allplugins[$count]['version'] = $plugin_details['version'];
					$allplugins[$count]['install'] = "install";
					$allplugins[$count]['location'] = "folder";
					$allplugins[$count]['order'] = 0;
				}
				
				// Conditions for "active"...
				if (($allplugins[$count]['status'] == 'active') && ($allplugins[$count]['install'] == 'install')) {
					$allplugins[$count]['active'] = "<img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/active_16.png'></a>";
				} elseif (($allplugins[$count]['status'] == 'inactive') && ($allplugins[$count]['install'] == 'install')) {
					$allplugins[$count]['active'] = "<img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/inactive_16.png'></a>";
				} elseif ($allplugins[$count]['status'] == 'active') {
					$allplugins[$count]['active'] = "<a href='" . BASEURL;
					$allplugins[$count]['active'] .= "admin_index.php?page=plugin_management&amp;action=deactivate&amp;plugin=";
					$allplugins[$count]['active'] .= $allplugins[$count]['folder'] . "'>";
					$allplugins[$count]['active'] .= "<img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/active_16.png'></a>";
				} else {
					$allplugins[$count]['active'] = "<a href='" . BASEURL;
					$allplugins[$count]['active'] .= "admin_index.php?page=plugin_management&amp;action=activate&amp;plugin=";
					$allplugins[$count]['active'] .= $allplugins[$count]['folder'] . "'>";
					$allplugins[$count]['active'] .= "<img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/inactive_16.png'></a>";
				}
				
				
				// Conditions for "install"...
				if ($allplugins[$count]['install'] == 'install') { 
					$allplugins[$count]['install'] = "<a href='" . BASEURL . "admin_index.php?page=plugin_management&amp;action=install&amp;plugin=". $allplugins[$count]['folder'] . "'><img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/install_16.png'></a>";
				} else { 
					$allplugins[$count]['install'] = "<a href='" . BASEURL . "admin_index.php?page=plugin_management&amp;action=uninstall&amp;plugin=". $allplugins[$count]['folder'] . "'><img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/uninstall_16.png'></a>";
				}
				
				
				
				// Conditions for "requires"...
				if (isset($plugin_details['requires']) && $plugin_details['requires']) {
					$h->plugin->requires = $plugin_details['requires'];
					$this->requiresToDependencies($h);
					
					// Converts plugin folder names to well formatted names...
					foreach ($h->plugin->dependencies as $this_plugin => $version)
					{
						$h->plugin->dependencies[$this_plugin] = $version;
						$allplugins[$count]['requires'][$this_plugin] = $h->plugin->dependencies[$this_plugin];
					}
				
				} else {
					$allplugins[$count]['requires'] = array();
				}
				
				
				// Conditions for "order"...
				// The order is sorted numerically in the plugin_management.php template, so we need separate order and order_output elements.
				if ($allplugins[$count]['order'] != 0) { 
					$order = $allplugins[$count]['order'];
					$allplugins[$count]['order_output'] = "<a href='" . BASEURL;
					$allplugins[$count]['order_output'] .= "admin_index.php?page=plugin_management&amp;";
					$allplugins[$count]['order_output'] .= "action=orderup&amp;plugin=". $allplugins[$count]['folder'];
					$allplugins[$count]['order_output'] .= "&amp;order=" . $order . "'>";
					$allplugins[$count]['order_output'] .= "<img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/up_12.png'>";
					$allplugins[$count]['order_output'] .= "</a> \n&nbsp;<a href='" . BASEURL;
					$allplugins[$count]['order_output'] .= "admin_index.php?page=plugin_management&amp;";
					$allplugins[$count]['order_output'] .= "action=orderdown&amp;plugin=". $allplugins[$count]['folder'];
					$allplugins[$count]['order_output'] .= "&amp;order=" . $order . "'>";
					$allplugins[$count]['order_output'] .= "<img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/down_12.png'>";
					$allplugins[$count]['order_output'] .= "</a>\n";
				} else {
					$allplugins[$count]['order_output'] = "";
				}
			
				$count++;
			}
		}
		return $allplugins;
	}
	
	
	/**
	 * Used by array_filter to keep only installed plugins
	 */
	public function getInstalledPlugins($var)
	{
		if ($var['location'] == 'database') { return $var; }
	}
	
	
	/**
	 * Used by array_filter in template to keep only installed plugins
	 */
	public function getUninstalledPlugins($var)
	{
		if ($var['location'] == 'folder') { return $var; }
	}
	
	
	/**
	 * Read and return plugin info directly from plugin files.
	 */
	public function getPluginsMeta()
	{
		$plugin_list = getFilenames(PLUGINS, "short");
		$plugins_array = array();
		foreach ($plugin_list as $plugin_folder_name)
		{
			if($plugin_metadata = $this->readPluginMeta($plugin_folder_name)) {
				array_push($plugins_array, $plugin_metadata);
			}
		}
		return $plugins_array; // return plugins in alphabetical order
	}
	
	
	/**
	 * Read and return plugin info from top of a plugin file.
	 *
	 * @param string $plugin_file - a file from the /plugins folder 
	 * @return array|false
	 */
	public function readPluginMeta($plugin_file)
	{
		if ($plugin_file == 'placeholder.txt') { return false; }
		
		// Include the generic_pmd class that reads post metadata from the a plugin
		require_once(EXTENSIONS . 'GenericPHPConfig/class.metadata.php');
		$metaReader = new generic_pmd();
		$plugin_metadata = $metaReader->read(PLUGINS . $plugin_file . "/" . $plugin_file . ".php");
		
		if ($plugin_metadata) { return $plugin_metadata; } else { return false; }
	}
	
	
	/**
	 * Converts $h->plugin->requires into $h->plugin->dependencies array.
	 * Result is an array containing 'plugin' -> 'version' pairs
	 */
	public function requiresToDependencies($h)
	{
		// unset each key from previous time here
		foreach ($h->plugin->dependencies as $k => $v) {
			unset($h->plugin->dependencies[$k]);
		}
		
		foreach (explode(',', $h->plugin->requires) as $pair) 
		{		    
		    $pair_array = explode(' ', trim(strtolower($pair)));
		    $pair_array ? $k = $pair_array[0] : $k=$h->lang["admin_plugins_install_unknown_plugin"];
		    count($pair_array) > 1 ? $v = $pair_array[1] : $v=0;		    
		    $h->plugin->dependencies[$k] = $v;
		}
	}
	
	
	/**
	 * Add a plugin to the plugins table
	 *
	 * @param int $upgrade flag to indicate we need to show "Upgraded!" instead of "Installed!" message
	 */
	public function install($h, $upgrade = 0)
	{
		// Clear the database cache to ensure stored plugins and hooks 
		// are up-to-date.
		$h->deleteFiles(CACHE . 'db_cache');
		
		// Clear the css/js cache to ensure any new ones get included
		$h->deleteFiles(CACHE . 'css_js_cache');
		
		// Read meta from the top of the plugin file
		$plugin_metadata = $this->readPluginMeta($h->plugin->folder);
		
		$h->plugin->enabled  = 1;    // Enable it when we add it to the database.
		$this->assignPluginMeta($h, $plugin_metadata);
		
		$dependency_error = 0;
		foreach ($h->plugin->dependencies as $dependency => $version)
		{
			if (version_compare($version, $h->getPluginVersion($dependency), '>')) {
				$dependency_error = 1;
			}
		}
		
		if ($dependency_error == 1)
		{
			foreach ($h->plugin->dependencies as $dependency => $version)
			{
				if (($h->isActive($dependency) == 'inactive') 
					|| version_compare($version, $h->getPluginVersion($dependency), '>')) {
					$dependency = make_name($dependency);
					$h->messages[$h->lang["admin_plugins_install_sorry"] . " " . $h->plugin->name . " " . $h->lang["admin_plugins_install_requires"] . " " . $dependency . " " . $version] = 'red';
				}
			}
			return false;
		}
		
		// set a new plugin order if NOT upgrading
		if ($upgrade == 0) {
		
			$sql = "REPLACE INTO " . TABLE_PLUGINS . " (plugin_enabled, plugin_name, plugin_folder, plugin_class, plugin_extends, plugin_type, plugin_desc, plugin_requires, plugin_version, plugin_author, plugin_authorurl, plugin_updateby) VALUES (%d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d)";
			$h->db->query($h->db->prepare($sql, $h->plugin->enabled, $h->plugin->name, $h->plugin->folder, $h->plugin->class, $h->plugin->extends, $h->plugin->type, $h->plugin->desc, $h->plugin->requires, $h->plugin->version, $h->plugin->author, urlencode($h->plugin->authorurl), $h->currentUser->id));
			
			// Get the last order number - doing this after REPLACE INTO because 
			// we don't know whether the above will insert or replace.
			$sql = "SELECT plugin_order FROM " . TABLE_PLUGINS . " ORDER BY plugin_order DESC LIMIT 1";
			$highest_order = $h->db->get_var($h->db->prepare($sql));
			
			// Give the new plugin the order number + 1
			$sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_order = %d WHERE plugin_id = LAST_INSERT_ID()";
			$h->db->query($h->db->prepare($sql, ($highest_order + 1)));
		} else {
			// upgrading:
			$sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_enabled = %d, plugin_name = %s, plugin_folder = %s, plugin_class = %s, plugin_extends = %s, plugin_type = %s, plugin_desc = %s, plugin_requires = %s, plugin_version = %s, plugin_author = %s, plugin_authorurl = %s, plugin_updateby = %d WHERE plugin_folder = %s";
			$h->db->query($h->db->prepare($sql, $h->plugin->enabled, $h->plugin->name, $h->plugin->folder, $h->plugin->class, $h->plugin->extends, $h->plugin->type, $h->plugin->desc, $h->plugin->requires, $h->plugin->version, $h->plugin->author, urlencode($h->plugin->authorurl), $h->currentUser->id, $h->plugin->folder));
		}
		
		// Add any plugin hooks to the hooks table
		$this->addPluginHooks($h);
		
		// Force inclusion of a language file (if exists) because the 
		// plugin isn't ready to include it itself yet.
		$h->includeLanguage();
		
		$result = $h->pluginHook('install_plugin', $h->plugin->folder);
		
		// Re-sort all orders and remove any accidental gaps
		$this->refreshPluginOrder($h);
		$this->sortPluginHooks($h);
		
		// For plugins to avoid showing this success message, they need to 
		// return a non-boolean value to $result.
		if (!is_array($result))
		{
			if ($upgrade == 0) {
				$h->messages[$h->lang["admin_plugins_install_done"]] = 'green';
			} else {
				$h->messages[$h->lang["admin_plugins_upgrade_done"]] = 'green';
			}
		}
	}
	
	
	/**
	 * Assign info from top of a plugin file to the current object.
	 *
	 * @param array $plugin_metadata 
	 * @return array|false
	 */
	public function assignPluginMeta($h, $plugin_metadata)
	{
		if (!$plugin_metadata) { return false; }
		
		$h->plugin->name         = $plugin_metadata['name'];
		$h->plugin->desc         = $plugin_metadata['description'];
		$h->plugin->version      = $plugin_metadata['version'];
		$h->plugin->folder       = $plugin_metadata['folder'];
		$h->plugin->class        = $plugin_metadata['class'];
		
		if (!isset($plugin_metadata['hooks'])) { 
			$h->plugin->hooks = array();
		} else {
			$h->plugin->hooks = explode(',', $plugin_metadata['hooks']);
		}
		
		if (isset($plugin_metadata['extends'])) {   $h->plugin->extends      = $plugin_metadata['extends']; }
		if (isset($plugin_metadata['type'])) {      $h->plugin->type         = $plugin_metadata['type'];    }
		if (isset($plugin_metadata['author'])) {    $h->plugin->author       = $plugin_metadata['author'];  }
		if (isset($plugin_metadata['authorurl'])) { $h->plugin->authorurl    = $plugin_metadata['authorurl']; }
		
		if (isset($plugin_metadata['requires']) && $plugin_metadata['requires']) {
			$h->plugin->requires = $plugin_metadata['requires'];
			$this->requiresToDependencies($h);
		}
		
		return true;
	}
	
	
	/**
	 * Adds all hooks for a given plugin
	 */
	public function addPluginHooks($h)
	{
		foreach ($h->plugin->hooks as $hook)
		{
			$exists = $this->isHook($h, trim($hook));
		
			if (!$exists) {
				$sql = "INSERT INTO " . TABLE_PLUGINHOOKS . " (plugin_folder, plugin_hook, plugin_updateby) VALUES (%s, %s, %d)";
				$h->db->query($h->db->prepare($sql, $h->plugin->folder, trim($hook), $h->currentUser->id));
			}
		}
	}
	
	
	/**
	 * Check if a plugin hook exists for a given plugin
	 *
	 * @param string $folder plugin folder name
	 * @param string $hook plugin hook name
	 * @return int|false
	 */
	public function isHook($h, $hook = "", $folder = "")
	{
		if (!$folder) { $folder = $h->plugin->folder; }
		
		$sql = "SELECT count(*) FROM " . TABLE_PLUGINHOOKS . " WHERE plugin_folder = %s AND plugin_hook = %s";
		if ($h->db->get_var($h->db->prepare($sql, $folder, $hook))) { return true;} else { return false; }
	}
	
	
	/**
	 * Uninstall all plugins
	 */
	public function uninstallAll($h)
	{
		// Clear the database cache to ensure plugins and hooks are up-to-date.
		$h->deleteFiles(CACHE . 'db_cache');
		
		// Clear the css/js cache to ensure any new ones get included
		$h->deleteFiles(CACHE . 'css_js_cache');
		
		$h->db->query("TRUNCATE TABLE " . TABLE_PLUGINS);
		$h->db->query("TRUNCATE TABLE " . TABLE_PLUGINHOOKS);
		
		$h->messages[$h->lang["admin_plugins_uninstall_all_done"]] = 'green';
	}
	
	
	/**
	 * Delete plugin from table_plugins, pluginhooks and pluginsettings
	 *
	 * @param int $upgrade flag to disable message
	 */
	public function uninstall($h, $upgrade = 0)
	{
		// Clear the database cache to ensure plugins and hooks are up-to-date.
		$h->deleteFiles(CACHE . 'db_cache');
		
		// Clear the css/js cache to ensure this plugin's files are removed
		$h->deleteFiles(CACHE . 'css_js_cache');
		
		if ($upgrade == 0) { // don't delete plugin when we're upgrading
			$h->db->query($h->db->prepare("DELETE FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s", $h->plugin->folder));
		}
		
		$h->db->query($h->db->prepare("DELETE FROM " . TABLE_PLUGINHOOKS . " WHERE plugin_folder = %s", $h->plugin->folder));
		
		// Settings aren't deleted anymore, but a user can do so manually from Admin->Maintenance
		//$h->db->query($h->db->prepare("DELETE FROM " . TABLE_PLUGINSETTINGS . " WHERE plugin_folder = %s", $h->plugin->folder));
		
		$h->pluginHook('uninstall_plugin', $h->plugin->folder);
		
		if ($upgrade == 0) {
			$h->messages[$h->lang["admin_plugins_uninstall_done"]] = 'green';
		}
		
		// Re-sort all orders and remove any accidental gaps
		$this->refreshPluginOrder($h);
		$this->sortPluginHooks($h);
	}
	
	
	/**
	 * Removes gaps in plugin order where plugins have been uninstalled.
	 */
	public function refreshPluginOrder($h)
	{
		$sql = "SELECT * FROM " . TABLE_PLUGINS . " ORDER BY plugin_order ASC";
		$rows = $h->db->get_results($h->db->prepare($sql));
		
		if ($rows) { 
			$i = 1;
			foreach ($rows as $row) 
			{
				$sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_order = %d WHERE plugin_id = %d";
				$h->db->query($h->db->prepare($sql, $i, $row->plugin_id));
				$i++; 
			}
		}
		
		// optimize the table
		$h->db->query("OPTIMIZE TABLE " . TABLE_PLUGINS);
		
		return true;
	}
	
	
	/**
	 * Orders the plugin hooks by plugin_order
	 */
	public function sortPluginHooks($h)
	{
		$sql = "SELECT p.plugin_folder, p.plugin_order, p.plugin_id, h.* FROM " . TABLE_PLUGINHOOKS . " h, " . TABLE_PLUGINS . " p WHERE p.plugin_folder = h.plugin_folder ORDER BY p.plugin_order ASC";
		$rows = $h->db->get_results($h->db->prepare($sql));
		
		// Drop and recreate the pluginhooks table, i.e. empty it.
		$h->db->query($h->db->prepare("TRUNCATE TABLE " . TABLE_PLUGINHOOKS));
		
		// Add plugin hooks back into the hooks table
		if ($rows) {
			foreach ($rows  as $row)
			{
				$sql = "INSERT INTO " . TABLE_PLUGINHOOKS . " (plugin_folder, plugin_hook, plugin_updateby) VALUES (%s, %s, %d)";
				$h->db->query($h->db->prepare($sql, $row->plugin_folder, $row->plugin_hook, $h->currentUser->id));
			}
		}
		
		// optimize the table
		$h->db->query("OPTIMIZE TABLE " . TABLE_PLUGINHOOKS);
		
	}
	
	
	/**
	 * Upgrade plugin
	 *
	 * @param string $folder plugin folder name
	 *
	 * Note: This function does nothing by itself other than read the latest 
	 * file's metadata.
	 */
	public function upgrade($h)
	{
		// Read meta from the top of the plugin file
		$plugin_metadata = $this->readPluginMeta($h->plugin->folder);
		
		$h->plugin->enabled  = 1;    // Enable it when we add it to the database.
		$this->assignPluginMeta($h, $plugin_metadata);
		
		$this->uninstall($h, 1);    // 1 indicates that "upgrade" is true, used to disable the "Uninstalled" message
		$this->install($h, 1);      // 1 indicates that "upgrade" is true.
	}
	
	
	/**
	 * Enables or disables a plugin, installing if necessary
	 *
	 * @param int $enabled 
	 * Note: This function does not uninstall/delete a plugin.
	 */
	public function activateDeactivate($h, $enabled = 0)
	{	// 0 = deactivate, 1 = activate
		
		// Clear the database cache to ensure plugins and hooks are up-to-date.
		$h->deleteFiles(CACHE . 'db_cache');
		
		// Clear the css/js cache to ensure any new ones get included
		$h->deleteFiles(CACHE . 'css_js_cache');
		
		// Get the enabled status for this plugin...
		$plugin_row = $h->db->get_row($h->db->prepare("SELECT plugin_folder, plugin_enabled FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s", $h->plugin->folder));
		
		// If no result, then it's obviously not installed...
		if (!$plugin_row) 
		{
			// If the user is activating the plugin, go and install it...
			if ($enabled == 1) { $this->install($h); }
		} 
		else 
		{
			$this->activateDeactivateDo($h, $plugin_row, $enabled);
		}
	}
	
	
	/**
	 * Enables or disables all plugins, installing if necessary
	 *
	 * @param int $enabled 
	 * Note: This function does not uninstall/delete a plugin.
	 */
	public function activateDeactivateAll($h, $enabled = 0)
	{	// 0 = deactivate, 1 = activate
		
		// if you want to activate, find all the inactive plugins and vice-versa:
		if ($enabled == 0) { $active_plugins = $this->activePlugins($h->db, '*', 1); }
		if ($enabled == 1) { $active_plugins = $this->activePlugins($h->db, '*', 0); }
		
		if (!$active_plugins) { return false; }
		        
		/*  The problem with upgrading plugins is many of them require other plugins to work, 
			therefore half the plugins can't be upgraded if the upgrade is attempted in a 
			random order. So let's minimize the problem by sorting the plugins by number of 
			requirements, i.e. plugins that have no requirements (Users, Submit, Sidebar Widgets)
			are upgraded first, then plugins with one requirement... and finally Pligg Importer,
			which has about 7 requirements. */ 
		$i = 0;
		foreach ($active_plugins as $active) {
			$h->plugin->folder = $active->plugin_folder;
			$ordered[$i]['name'] = $active->plugin_folder;
			if (!$active->plugin_requires) { 
				$ordered[$i]['req_count'] = 0; 
			} else {
				$requires = explode(', ', $active->plugin_requires);
				$ordered[$i]['req_count'] = count($requires);
			}
			$i++;
		}
		
		$ordered = sksort($ordered, 'req_count', 'int', true);
		foreach ($ordered as $ord) {
			$plugin_row = $h->db->get_row($h->db->prepare("SELECT plugin_folder, plugin_enabled FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s", $ord['name']));
			$h->plugin->folder = $plugin_row->plugin_folder;
			$this->activateDeactivateDo($h, $plugin_row, $enabled);
		}
	}
	
	
	/**
	 * Enables or disables all plugins, installing if necessary
	 *
	 * @param int $enabled 
	 * Note: This function does not uninstall/delete a plugin.
	 */
	public function activateDeactivateDo($h, $plugin, $enabled = 0)
	{	// 0 = deactivate, 1 = activate
		// The plugin is already installed. Activate or deactivate according to $enabled (the user's action).
		if ($plugin->plugin_enabled == $enabled) { return false; }  // only update if we're changing the enabled value.
		
		$sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_enabled = %d, plugin_updateby = %d WHERE plugin_folder = %s";
		$h->db->query($h->db->prepare($sql, $enabled, $h->currentUser->id, $h->plugin->folder));
		
		if ($enabled == 1) { // Activating now...
		
			// Get plugin version from the database...
			$db_version = $h->getPluginVersion($h->plugin->folder);
			
			// Get plugin version from the file....
			$plugin_metadata = $this->readPluginMeta($h->plugin->folder);
			$file_version = $plugin_metadata['version'];
			
			// If file version is newer the the current plugin version, then upgrade...
			if (version_compare($file_version, $db_version, '>')) {
				$this->upgrade($h); // runs the install function and shows "upgraded!" message instead of "installed".
			} else {
				// else simply show an activated message...
				$h->messages[$h->lang["admin_plugins_activated"]] = 'green'; 
			}
			
			// Force inclusion of a language file (if exists) because the 
			// plugin isn't ready to include it itself yet.
			$h->includeLanguage();
		}
		
		if ($enabled == 0) { 
			$h->messages[$h->lang["admin_plugins_deactivated"]] = 'green'; 
		}
		
		$h->pluginHook('activate_deactivate', '', array('enabled' => $enabled));
	}
	
	
	/**
	 * Get a list of active or inactive plugins (or their descriptions, etc.)
	 *
	 * @param string $select the table column to return
	 * @param int $enabled 0 for inactive, 1 for active
	 * @return array
	 */
	public function activePlugins($db, $select = 'plugin_folder', $enabled = 1)
	{
		$sql = "SELECT $select FROM " . TABLE_PLUGINS . " WHERE plugin_enabled = %d";
		$active_plugins = $db->get_results($db->prepare($sql, $enabled));
		
		if ($active_plugins) { return $active_plugins; } else {return false; }
	}
	
	
	/**
	 * Updates plugin order and order of their hooks, i.e. changes the order 
	 * of plugins in pluginHook.
	 * 
	 * @param string $folder plugin folder name
	 * @param int $order current order
	 * @param string $arrow direction to move
	 */
	public function pluginOrder($h, $order = 0, $arrow = "up")
	{
		if ($order == 0) {
			$h->messages[$h->lang['admin_plugins_order_zero']] = 'red';
			return false;
		}
		
		$h->getPluginName();
		
		if ($arrow == "up")
		{
			// get row above
			$sql= "SELECT * FROM " . TABLE_PLUGINS . " WHERE plugin_order = %d";
			$row_above = $h->db->get_row($h->db->prepare($sql, ($order - 1)));
			
			if (!$row_above) {
				$h->messages[$h->plugin->name . " " . $h->lang['admin_plugins_order_first']] = 'red';
				return false;
			}
			
			if ($row_above->plugin_order == $order) {
				$h->messages[$h->lang['admin_plugins_order_above']] = 'red';
				return false;
			}
			
			// update row above 
			$sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_order = %d WHERE plugin_id = %d";
			$h->db->query($h->db->prepare($sql, ($row_above->plugin_order + 1), $row_above->plugin_id)); 
			
			// update current plugin
			$sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_order = %d WHERE plugin_folder = %s";
			$h->db->query($h->db->prepare($sql, ($order - 1), $h->plugin->folder)); 
		}
		else
		{
			// get row below
			$sql= "SELECT * FROM " . TABLE_PLUGINS . " WHERE plugin_order = %d";
			$row_below = $h->db->get_row($h->db->prepare($sql, ($order + 1)));
			
			if (!$row_below) {
				$h->messages[$h->plugin->name . " " . $h->lang['admin_plugins_order_last']] = 'red';
				return false;
			}
			
			if ($row_below->plugin_order == $order) {
				$h->messages[$h->lang['admin_plugins_order_below']] = 'red';
				return false;
			}
			
			// update row above 
			$sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_order = %d WHERE plugin_id = %d";
			$h->db->query($h->db->prepare($sql, ($row_below->plugin_order - 1), $row_below->plugin_id)); 
			
			// update current plugin
			$sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_order = %d WHERE plugin_folder = %s";
			$h->db->query($h->db->prepare($sql, ($order + 1), $h->plugin->folder)); 
		}
		
		$h->messages[$h->lang['admin_plugins_order_updated']] = 'green';
		
		// Re-sort all orders and remove any accidental gaps
		$this->refreshPluginOrder($h);
		$this->sortPluginHooks($h);
		
		return true;
	
	}
}
?>
