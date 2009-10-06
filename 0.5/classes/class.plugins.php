<?php
/**
 * Manages all things plugin-related.
 *
 * Plugin extends the generic_pmd class in class.metadata.php which is a 3rd
 * party script called "Generic PHP Config"
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
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

// Include the generic_pmd class that reads post metadata from the a plugin
require_once(INCLUDES . 'GenericPHPConfig/class.metadata.php');

class Plugin extends generic_pmd {

    var $id             = '';
    var $enabled        = 0;
    var $name           = '';
    var $prefix         = '';
    var $folder         = '';
    var $desc           = '';
    var $version        = 0;
    var $order          = 0;
    var $requires       = '';       // string of plugin->version pairs
    var $dependencies   = array();  // array of plugin->version pairs
    var $hooks          = array();
    var $include_css    = array();  // a list of css files to include
    var $include_js     = array();  // a list of js files to include
    var $include_type   = '';       // 'css' or 'js'


    /**
     * Get an array of plugins
     *
     * Takes plugin info read directly from plugin files, then compares each 
     * plugin to the database. If present and latest version, reads info from 
     * the database. If not in database or newer version, uses info from plugin 
     * file. Used by Plugin Management.
     */
    function get_plugins()
    {
        global $db, $lang;
        $plugins_array = $this->get_plugins_array();
        $count = 0;
        $allplugins = array();

        if ($plugins_array) {
            foreach ($plugins_array as $plugin_details) {
            
                $allplugins[$count] = array();
                $sql = "SELECT * FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s";
                $plugin_row = $db->get_row($db->prepare($sql, $plugin_details['folder']));
                
                if ($plugin_row) 
                {
                    // if plugin in folder is older or equal to plugin in database...
                    $allplugins[$count]['name'] = $plugin_row->plugin_name;
                    $allplugins[$count]['description'] = $plugin_row->plugin_desc;
                    $allplugins[$count]['folder'] = $plugin_row->plugin_folder;
                    $allplugins[$count]['status'] = $this->get_plugin_status($plugin_row->plugin_folder);
                    $allplugins[$count]['version'] = $plugin_row->plugin_version;
                    $allplugins[$count]['install'] = "installed";
                    $allplugins[$count]['location'] = "database";
                    $allplugins[$count]['order'] = $plugin_row->plugin_order;
                } 
                else 
                {
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
                if (($allplugins[$count]['status'] == 'active') && ($allplugins[$count]['install'] == 'install')) {
                    $allplugins[$count]['active'] = "<img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/active.png'></a>";
                } elseif (($allplugins[$count]['status'] == 'inactive') && ($allplugins[$count]['install'] == 'install')) {
                    $allplugins[$count]['active'] = "<img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/inactive.png'></a>";
                } elseif ($allplugins[$count]['status'] == 'active') {
                    $allplugins[$count]['active'] = "<a href='" . BASEURL;
                    $allplugins[$count]['active'] .= "admin/admin_index.php?page=plugins&amp;action=deactivate&amp;plugin=";
                    $allplugins[$count]['active'] .= $allplugins[$count]['folder'] . "'>";
                    $allplugins[$count]['active'] .= "<img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/active.png'></a>";
                } else {
                    $allplugins[$count]['active'] = "<a href='" . BASEURL;
                    $allplugins[$count]['active'] .= "admin/admin_index.php?page=plugins&amp;action=activate&amp;plugin=";
                    $allplugins[$count]['active'] .= $allplugins[$count]['folder'] . "'>";
                    $allplugins[$count]['active'] .= "<img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/inactive.png'></a>";
                }


                // Conditions for "install"...
                if ($allplugins[$count]['install'] == 'install') { 
                    $allplugins[$count]['install'] = "<a href='" . BASEURL . "admin/admin_index.php?page=plugins&amp;action=install&amp;plugin=". $allplugins[$count]['folder'] . "'><img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/install.png'></a>";
                } else { 
                    $allplugins[$count]['install'] = "<a href='" . BASEURL . "admin/admin_index.php?page=plugins&amp;action=uninstall&amp;plugin=". $allplugins[$count]['folder'] . "' style='color: red; font-weight: bold'><img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/uninstall.png'></a>";
                }
                
                

                // Conditions for "requires"...
                if (isset($plugin_details['requires']) && $plugin_details['requires']) {
                    $this->requires = $plugin_details['requires'];
                    $this->requires_to_dependencies();

                    // Converts plugin folder names to well formatted names...
                    foreach ($this->dependencies as $this_plugin => $version)
                    {
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
                if ($allplugins[$count]['order'] != 0) { 
                    $order = $allplugins[$count]['order'];
                    $allplugins[$count]['order_output'] = "<a href='" . BASEURL;
                    $allplugins[$count]['order_output'] .= "admin/admin_index.php?page=plugins&amp;";
                    $allplugins[$count]['order_output'] .= "action=orderup&amp;plugin=". $allplugins[$count]['folder'];
                    $allplugins[$count]['order_output'] .= "&amp;order=" . $order . "'>";
                    $allplugins[$count]['order_output'] .= "<img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/up.png'>";
                    $allplugins[$count]['order_output'] .= "</a> \n<a href='" . BASEURL;
                    $allplugins[$count]['order_output'] .= "admin/admin_index.php?page=plugins&amp;";
                    $allplugins[$count]['order_output'] .= "action=orderdown&amp;plugin=". $allplugins[$count]['folder'];
                    $allplugins[$count]['order_output'] .= "&amp;order=" . $order . "'>";
                    $allplugins[$count]['order_output'] .= "<img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/down.png'>";
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
     * Read and return plugin info directly from plugin files.
     */
    function get_plugins_array()
    {
        $plugin_list = getFilenames(PLUGINS, "short");
        $plugins_array = array();
        foreach ($plugin_list as $plugin_folder_name)
        {
            if($plugin_folder_name != 'placeholder.txt') {
                $plugin_metadata = $this->read(PLUGINS . $plugin_folder_name . "/" 
                . $plugin_folder_name . ".php");
                
                if ($plugin_metadata) {
                    array_push($plugins_array, $plugin_metadata);
                }
            }
        }    
        return $plugins_array;
    }


    /**
     * Converts $this->requires into $this->dependencies array.
     * Result is an array containing 'plugin' -> 'version' pairs
     */
    function requires_to_dependencies()
    {
        unset($this->dependencies);
        foreach (explode(',', $this->requires) as $pair) 
        {
            list($k,$v) = explode (' ', trim($pair));
            $this->dependencies[$k] = $v;
        }
    }


    /**
     * Changes 'plugin_name' into 'Plugin Name'
     *
     * @param string $plugin plugin folder name
     * @return string
     */
    function folder_to_name($plugin)
    {
        $dep_array  = array();
        $dep_array  = explode('_', trim($plugin));
        $dep_array  = array_map('ucfirst', $dep_array);
        $plugin     = implode(' ', $dep_array);

        return $plugin;
    }


    /**
     * Determines if a plugin is enabled or not
     *
     * @param string $plugin_folder plugin folder name
     * @return string
     */
    function get_plugin_status($plugin_folder = '')
    {
        global $db;
        
        $sql = "SELECT * FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s";
        $plugin_row = $db->get_row($db->prepare($sql, $plugin_folder));
        
        if ($plugin_row && $plugin_row->plugin_enabled == 1) {
            $status = "active";
        } else {
            $status = "inactive";
        } 

        return $status;
    }


    /**
     * Get a list of active plugins (or their descriptions, etc.)
     *
     * @param string $select the table column to return
     * @return array
     */
    function active_plugins($select = 'plugin_folder')
    {
        global $db;
        $sql = "SELECT " . $select . " FROM " . TABLE_PLUGINS . " WHERE plugin_enabled = %d";
        $active_plugins = $db->get_results($db->prepare($sql, $select, 1));
        if ($active_plugins) { return $active_plugins; } else {return false; }
    }


    /**
     * Check if a plugin hook exists for a given plugin
     *
     * @param string $folder plugin folder name
     * @param string $hook plugin hook name
     * @return int|false
     */
    function hook_exists($folder = "", $hook = "")
    {
        global $db;
        
        $sql = "SELECT count(*) FROM " . TABLE_PLUGINHOOKS . " WHERE plugin_folder = %s AND plugin_hook = %s";
        if ($db->get_var($db->prepare($sql, $folder, $hook))) { return true;} else { return false; }
    }




    /**
     * Determines if a hook already exists
     *
     * @param string $hook plugin hook name
     */
    function plugin_hook_exists($hook = "")
    {
        global $db;
        
        $sql = "SELECT plugin_hook FROM " . TABLE_PLUGINHOOKS . " WHERE (plugin_folder = %s) AND (plugin_hook = %s)";
        $returned_hook = $db->get_var($db->prepare($sql, $this->folder, $hook));
        if ($returned_hook) { return $returned_hook; } else { return false; }
    }


    /**
     * Add a plugin to the plugins table
     *
     * @param string $folder plugin folder name
     * @param int $upgrade flag to indicate upgrade script available
     */
    function install_plugin($folder = "", $upgrade = 0)
    {
        global $db, $lang, $hotaru, $current_user, $admin;
        
        // Clear the database cache to ensure stored plugins and hooks 
        // are up-to-date.
        $admin->delete_files(CACHE . 'db_cache');
        
        $plugin_metadata = $this->read(PLUGINS . $folder . "/" . $folder . ".php");
        
        $this->enabled  = 1;    // Enable it when we add it to the database.
        $this->name     = $plugin_metadata['name'];
        $this->desc     = $plugin_metadata['description'];
        $this->folder   = $folder;
        $this->prefix   = $plugin_metadata['prefix'];
        $this->version  = $plugin_metadata['version'];
        $this->hooks    = explode(',', $plugin_metadata['hooks']);
        
        if (isset($plugin_metadata['requires']) && $plugin_metadata['requires']) {
            $this->requires = $plugin_metadata['requires'];
            $this->requires_to_dependencies();
        }
        
        $dependency_error = 0;
        foreach ($this->dependencies as $dependency => $version)
        {
            if (version_compare($version, $this->plugin_version($dependency), '>')) {
                $dependency_error = 1;
            }
        }
        
        if ($dependency_error == 1)
        {
            foreach ($this->dependencies as $dependency => $version)
            {
                    if (($this->get_plugin_status($dependency) == 'inactive') 
                        || version_compare($version, $this->plugin_version($dependency), '>')) {
                        $dependency = $this->folder_to_name($dependency);
                        $hotaru->messages[$lang["admin_plugins_install_sorry"] . " " . $this->name . " " . $lang["admin_plugins_install_requires"] . " " . $dependency . " " . $version] = 'red';
                    }
            }
            return false;
        }
                    
        $sql = "REPLACE INTO " . TABLE_PLUGINS . " (plugin_enabled, plugin_name, plugin_prefix, plugin_folder, plugin_desc, plugin_requires, plugin_version, plugin_updateby) VALUES (%d, %s, %s, %s, %s, %s, %s, %d)";
        $db->query($db->prepare($sql, $this->enabled, $this->name, $this->prefix, $this->folder, $this->desc, $this->requires, $this->version, $current_user->id));

        // Get the last order number - doing this after REPLACE INTO because 
        // we don't know whether the above will insert or replace.
        $sql = "SELECT plugin_order FROM " . TABLE_PLUGINS . " ORDER BY plugin_order DESC LIMIT 1";
        $highest_order = $db->get_var($db->prepare($sql));

        // Give the new plugin the order number + 1
        $sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_order = %d WHERE plugin_id = LAST_INSERT_ID()";
        $db->query($db->prepare($sql, ($highest_order + 1)));
        
        // Add any plugin hooks to the hooks table
        $this->add_plugin_hooks();
        
        // Force inclusion of a language file (if exists) because the 
        // plugin isn't ready to include it itself yet.
        $this->include_language($folder);
            
        $result = $this->check_actions('install_plugin', $folder);
        
        // For plugins to avoid showing this success message, they need to 
        // return a non-boolean value to $result.
        if (!is_array($result))
        {
            if ($upgrade == 0) {
                $hotaru->messages[$lang["admin_plugins_install_done"]] = 'green';
            } else {
                $hotaru->messages[$lang["admin_plugins_upgrade_done"]] = 'green';
            }
        }
    }

    /**
     * Adds all hooks for a given plugin
     */
    function add_plugin_hooks()
    {
        global $db, $current_user;
        
        foreach ($this->hooks as $hook)
        {
            $exists = $this->plugin_hook_exists(trim($hook));

            if (!$exists) {
                $sql = "INSERT INTO " . TABLE_PLUGINHOOKS . " (plugin_folder, plugin_hook, plugin_updateby) VALUES (%s, %s, %d)";
                $db->query($db->prepare($sql, $this->folder, trim($hook), $current_user->id));
            }
        }
    }
    
    
    /**
     * Upgrade plugin
     *
     * @param string $folder plugin folder name
     *
     * Note: This function does nothing by itself other than read the latest 
     * file's metadata.
     */
    function upgrade_plugin($folder = "")
    {
        global $db, $lang, $hotaru, $admin;
        
        $plugin_metadata = $this->read(PLUGINS . $folder . "/" . $folder . ".php");
        
        $this->enabled  = 1;    // Enable it when we add it to the database.
        $this->name     = $plugin_metadata['name'];
        $this->desc     = $plugin_metadata['description'];
        $this->folder   = $folder;
        $this->prefix   = $plugin_metadata['prefix'];
        $this->version  = $plugin_metadata['version'];
        $this->hooks    = explode(',', $plugin_metadata['hooks']);
        
        if (in_array('upgrade_plugin', $this->hooks)) 
        {
            // Clear the database cache to ensure stored plugins and hooks 
            // are up-to-date.
            $admin->delete_files(CACHE . 'db_cache');
            
            // Force inclusion of a language file (if exists) because the 
            // plugin isn't ready to include it itself yet.
            $this->include_language($folder);
            
            // Add any new hooks to the hooks table before proceeding.
            $this->add_plugin_hooks(); 
        } else {
            // Uninstall and then re-install because there's no upgrade function
            $this->uninstall_plugin($folder, 1);
            $this->install_plugin($folder, 1);
        }
                
        $result = $this->check_actions('upgrade_plugin', $folder);
                
        // For plugins to avoid showing this success message, they need to
        // return a non-boolean value to $result.
        if (!is_array($result)) {
            $hotaru->messages[$lang["admin_plugins_upgrade_done"]] = 'green';
        }
    }
    
    
    /**
     * Enables or disables a plugin, installing if necessary
     *
     * @param string $folder plugin folder name
     * @param int $enabled 
     * Note: This function does not uninstall/delete a plugin.
     */
    function activate_deactivate_plugin($folder = "", $enabled = 0)
    {    // 0 = deactivate, 1 = activate
        global $db, $hotaru, $lang, $admin, $current_user;
        
        // Clear the database cache to ensure plugins and hooks are up-to-date.
        $admin->delete_files(CACHE . 'db_cache');
        
        // Get the enabled status for this plugin...
        $plugin_row = $db->get_row($db->prepare("SELECT plugin_folder, plugin_enabled FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s", $folder));
        
        // If no result, then it's obviously not installed...
        if (!$plugin_row) 
        {
            // If the user is activating the plugin, go and install it...
            if ($enabled == 1) {    
                $this->install_plugin($folder);
            }
        } 
        else 
        {
            // The plugin is already installed. Activate or deactivate according to $enabled (the user's action).
            if ($plugin_row->plugin_enabled != $enabled) {        // only update if we're changing the enabled value.
                $sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_enabled = %d, plugin_updateby = %d WHERE plugin_folder = %s";
                $db->query($db->prepare($sql, $enabled, $current_user->id, $folder));
                
                if ($enabled == 1) { // Activating now...
                
                    // Get plugin version from the database...
                    $db_version = $this->plugin_version($folder);
                    
                    // Get plugin version from the file....
                    $plugin_metadata = $this->read(PLUGINS . $folder . "/" . $folder . ".php");
                    $file_version = $plugin_metadata['version'];
                    
                    // If file version is newer the the current plugin version, then upgrade...
                    if (version_compare($file_version, $db_version, '>')) {
                        $this->upgrade_plugin($folder);
                    } else {
                        // else simply show an activated message...
                        $hotaru->messages[$lang["admin_plugins_activated"]] = 'green'; 
                    }
                }
                
                if ($enabled == 0) { 
                    $hotaru->messages[$lang["admin_plugins_deactivated"]] = 'green'; 
                }
            }
        }
    }
    

    /**
     * Get plugin version number from the database
     *
     * @param string $folder plugin folder name
     * @return int - version number
     */
    function plugin_version($folder = "")
    {    
        global $db;

        $version = $db->get_var($db->prepare("SELECT plugin_version FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s", $folder));
        if ($version) { return $version; } else { return false; }
    }
    
    
    /**
     * Delete plugin from table_plugins, pluginhooks and pluginsettings
     *
     * @param string $folder plugin folder name
     * @param int $upgrade flag to disable message
     */
    function uninstall_plugin($folder = "", $upgrade = 0)
    {    
        global $db, $hotaru, $lang, $admin;
        
        // Clear the database cache to ensure plugins and hooks are up-to-date.
        $admin->delete_files(CACHE . 'db_cache');

        $db->query($db->prepare("DELETE FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s", $folder));
        $db->query($db->prepare("DELETE FROM " . TABLE_PLUGINHOOKS . " WHERE plugin_folder = %s", $folder));
        $db->query($db->prepare("DELETE FROM " . TABLE_PLUGINSETTINGS . " WHERE plugin_folder = %s", $folder));
        
        if ($upgrade == 0) {
            $hotaru->messages[$lang["admin_plugins_uninstall_done"]] = 'green';
        }
        
        $this->refresh_plugin_order();
    }
    
    
    /**
     * Updates plugin order and order of their hooks, i.e. changes the order 
     * of plugins in check_actions.
     * 
     * @param string $folder plugin folder name
     * @param int $order current order
     * @param string $arrow direction to move
     */
    function plugin_order($folder = "", $order = 0, $arrow = "up")
    {
        global $db, $hotaru, $lang;
            
        if ($order == 0) {
            $hotaru->messages[$lang['admin_plugins_order_zero']] = 'red';
            return false;
        }
                
        if ($arrow == "up")
        {
            // get row above
            $sql= "SELECT * FROM " . TABLE_PLUGINS . " WHERE plugin_order = %d";
            $row_above = $db->get_row($db->prepare($sql, ($order - 1)));
            
            if (!$row_above) {
                $hotaru->messages[$this->folder_to_name($folder) . " " . $lang['admin_plugins_order_first']] = 'red';
                return false;
            }
            
            if ($row_above->plugin_order == $order) {
                $hotaru->messages[$lang['admin_plugins_order_above']] = 'red';
                return false;
            }
            
            // update row above 
            $sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_order = %d WHERE plugin_id = %d";
            $db->query($db->prepare($sql, ($row_above->plugin_order + 1), $row_above->plugin_id)); 
            
            // update current plugin
            $sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_order = %d WHERE plugin_folder = %s";
            $db->query($db->prepare($sql, ($order - 1), $folder)); 
        }
        else
        {
            // get row below
            $sql= "SELECT * FROM " . TABLE_PLUGINS . " WHERE plugin_order = %d";
            $row_below = $db->get_row($db->prepare($sql, ($order + 1)));
            
            if (!$row_below) {
                $hotaru->messages[$this->folder_to_name($folder) . " " . $lang['admin_plugins_order_last']] = 'red';
                return false;
            }
            
            if ($row_below->plugin_order == $order) {
                $hotaru->messages[$lang['admin_plugins_order_below']] = 'red';
                return false;
            }
            
            // update row above 
            $sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_order = %d WHERE plugin_id = %d";
            $db->query($db->prepare($sql, ($row_below->plugin_order - 1), $row_below->plugin_id)); 
            
            // update current plugin
            $sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_order = %d WHERE plugin_folder = %s";
            $db->query($db->prepare($sql, ($order + 1), $folder)); 
        }

        $hotaru->messages[$lang['admin_plugins_order_updated']] = 'green';

        // Resort all orders and remove any accidental gaps
        $this->refresh_plugin_order();

        $this->sort_plugin_hooks();

        return true;

    }
    
    
    /**
     * Removes gaps in plugin order where plugins have been uninstalled.
     */
    function refresh_plugin_order()
    {    
        global $db;
        
        $sql = "SELECT * FROM " . TABLE_PLUGINS . " ORDER BY plugin_order ASC";
        $rows = $db->get_results($db->prepare($sql));
        
        if ($rows) { 
            $i = 1;
            foreach ($rows as $row) 
            {
                $sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_order = %d WHERE plugin_id = %d";
                $db->query($db->prepare($sql, $i, $row->plugin_id));
                $i++; 
            }
        }
        return true;
    }
    
    
    /**
     * Orders the plugin hooks by plugin_order
     */
    function sort_plugin_hooks()
    {    
        global $db, $current_user;
        
        $sql = "SELECT p.plugin_folder, p.plugin_order, p.plugin_id, h.* FROM " . TABLE_PLUGINHOOKS . " h, " . TABLE_PLUGINS . " p WHERE p.plugin_folder = h.plugin_folder ORDER BY p.plugin_order ASC";
        $rows = $db->get_results($db->prepare($sql));

        // Drop and recreate the pluginhooks table, i.e. empty it.
        $db->query($db->prepare("TRUNCATE TABLE " . TABLE_PLUGINHOOKS));
            
        // Add plugin hooks back into the hooks table
        foreach ($rows  as $row)
        {
            $sql = "INSERT INTO " . TABLE_PLUGINHOOKS . " (plugin_folder, plugin_hook, plugin_updateby) VALUES (%s, %s, %d)";
            $db->query($db->prepare($sql, $row->plugin_folder, $row->plugin_hook, $current_user->id));
        }
        
    }
    

    /**
     * Get a plugin's actual name from its folder name
     *
     * @param string $folder plugin folder name
     * @return string
     */
    function plugin_name($folder = "")
    {    
        global $db;
        $this->name = $db->get_var($db->prepare("SELECT plugin_name FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s", $folder));
        return $this->name;
    }
    
    
    /**
     * Get version number of plugin if active
     *
     * @param string $folder plugin folder name
     * @return string|false
     */
    function plugin_active($folder = "")
    {
        global $db;
        
        $active= $db->get_row($db->prepare("SELECT plugin_enabled, plugin_version FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s", $folder));
        
        if ($active) {
            if ($active->plugin_enabled == 1) { 
                return $active->plugin_version; 
            } 
        } 
        return false;
    }
    
    
    /**
     * Get number of active plugins
     *
     * @return int|false
     */
    function num_active_plugins()
    {
        global $db;
        $enabled = $db->get_var($db->prepare("SELECT count(*) FROM " . TABLE_PLUGINS . " WHERE plugin_enabled = %d", 1));
        if ($enabled > 0) { return $enabled; } else { return false; }
    }
                
    
    /**
     * Look for and run actions at a given plugin hook
     *
     * @param string $hook name of the plugin hook
     * @param bool $perform false to check existence, true to actually run
     * @param string $folder name of plugin folder
     * @param array $parameters mixed values passed from plugin hook
     * @return array | bool
     */
    function check_actions(
        $hook = '', $perform = true, $folder = '', $parameters = array(), $exclude = array()
    )
    {
        global $db, $cage, $current_user;

        if ($hook == '') {
            //echo "Error: Plugin hook name not provided.";
        } else {
            $where = "";

            if (!empty($folder)) {
                $where .= "AND (" . TABLE_PLUGINS . ".plugin_folder = %s)";
            }

            $db->cache_queries = true;    // start using cache

            $sql = "SELECT " . TABLE_PLUGINS . ".plugin_enabled, " . TABLE_PLUGINS . ".plugin_folder, " . TABLE_PLUGINS . ".plugin_prefix, " . TABLE_PLUGINHOOKS . ".plugin_hook  FROM " . TABLE_PLUGINHOOKS . ", " . TABLE_PLUGINS . " WHERE (" . TABLE_PLUGINHOOKS . ".plugin_hook = %s) AND (" . TABLE_PLUGINS . ".plugin_folder = " . TABLE_PLUGINHOOKS . ".plugin_folder) " . $where . "ORDER BY " . TABLE_PLUGINHOOKS . ".phook_id";

            $plugins = $db->get_results($db->prepare($sql, $hook, $folder));

            $db->cache_queries = false;    // stop using cache

            $action_found = false;
            if ($plugins)
            {
                foreach ($plugins as $plugin)
                {
                    if (    $plugin->plugin_folder 
                        &&  $plugin->plugin_hook 
                        &&  ($plugin->plugin_enabled == 1)
                        && !in_array($plugin->plugin_folder, $exclude)
                    ) {
                        if (file_exists(PLUGINS . $plugin->plugin_folder . "/" . $plugin->plugin_folder . ".php"))
                        {
                            include_once(PLUGINS . $plugin->plugin_folder . "/" . $plugin->plugin_folder . ".php");
                            
                            if ($perform == true)
                            {
                                $function_name = $plugin->plugin_prefix . "_" . $hook;
                                
                                if (function_exists($function_name))
                                {
                                    $result = $function_name($parameters);
                                    if ($result) {
                                        $return_array[$function_name] = $result;
                                    }
                                }
                            }
                            $action_found = true;
                        } else {
                            //echo "Error: Plugin file not found.";
                        }
                    } else {
                        if ($plugin->plugin_enabled != 1) {
                            //echo "Error: This plugin is not active.";
                        } else {
                            //echo "Error: Plugin function not found.";
                        }
                    }
                }
            } 
            else 
            {
                return false;
            }
        }
        
        if (!empty($return_array))
        {
            // return an array of return values from each function, 
            // e.g. $return_array['usr_users'] = something
            return $return_array;
        } 
        elseif ($action_found == true) 
        {
            // at least one function exists, but nothing was returned
            return true;

        }
        else 
        {
            // no functions were triggered. Either they weren't found or 
            // they were surpressed by $perform = false.
            return false;
        }
    }


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
    function plugin_settings($folder = '', $setting = '') {
        global $db;
        
        $sql = "SELECT plugin_value FROM " . TABLE_PLUGINSETTINGS . " WHERE (plugin_folder = %s) AND (plugin_setting = %s)";
        $value = $db->get_var($db->prepare($sql, $folder, $setting));
        if ($value) { return $value; } else { return false; }
    }
    
    
    /**
     * Get an array of settings for a given plugin
     *
     * @param string $folder name of plugin folder
     * @return array|false
     *
     * Note: Unlike "plugin_settings", this will get ALL settings with the same name.
     */
    function plugin_settings_array($folder = '') {
        global $db;
        
        $sql = "SELECT plugin_setting, plugin_value FROM " . TABLE_PLUGINSETTINGS . " WHERE (plugin_folder = %s)";
        $results = $db->get_results($db->prepare($sql, $folder));
        
        if ($results) { return $results; } else { return false; }
    }
    
    
    /**
     * Determine if a plugin setting already exists
     *
     * @param string $folder name of plugin folder
     * @param string $setting name of the setting to retrieve
     * @return string|false
     */
    function plugin_setting_exists($folder = '', $setting = '') {
        global $db;
        
        $sql = "SELECT plugin_setting FROM " . TABLE_PLUGINSETTINGS . " WHERE (plugin_folder = %s) AND (plugin_setting = %s)";
        $returned_setting = $db->get_var($db->prepare($sql, $folder, $setting));
        if ($returned_setting) { 
            return $returned_setting; 
        } else { 
            return false; 
        }
    }
    
    /**
     * Update a plugin setting
     *
     * @param string $folder name of plugin folder
     * @param string $setting name of the setting
     * @param string $setting stting value
     */
    function plugin_settings_update($folder = '', $setting = '', $value = '')
    {
        global $db, $current_user;
        
        $exists = $this->plugin_setting_exists($folder, $setting);
        if (!$exists) 
        {
            $sql = "INSERT INTO " . TABLE_PLUGINSETTINGS . " (plugin_folder, plugin_setting, plugin_value, plugin_updateby) VALUES (%s, %s, %s, %d)";
            $db->query($db->prepare($sql, $folder, $setting, $value, $current_user->id));
        } else 
        {
            $sql = "UPDATE " . TABLE_PLUGINSETTINGS . " SET plugin_folder = %s, plugin_setting = %s, plugin_value = %s, plugin_updateby = %d WHERE (plugin_folder = %s) AND (plugin_setting = %s)";
            $db->query($db->prepare($sql, $folder, $setting, $value, $current_user->id, $folder, $setting));
        }
    }


    /**
     * Delete rows from pluginsettings that match a given setting
     *
     * @param string $setting name of the setting to remove
     */
    function plugin_settings_remove_setting($setting = '')
    {
        global $db;
        
        $sql = "DELETE FROM " . TABLE_PLUGINSETTINGS . " WHERE plugin_setting = %s";
        $db->query($db->prepare($sql, $setting));
    }
    
    
    /**
     * Deletes rows from pluginsettings that match a given plugin
     *
     * @param string $folder name of plugin folder
     */
    function plugin_settings_remove_plugin($folder = '')
    {
        global $db;
        $sql = "DELETE FROM " . TABLE_PLUGINSETTINGS . " WHERE plugin_folder = %s";
        $db->query($db->prepare($sql, $folder));
    }


    /**
     * Include a language file in a plugin
     *
     * @param string $folder name of plugin folder
     * @param string $filename optional filename without file extension
     *
     * Note: the language file should be in a plugin folder named 'languages'.
     * '_language.php' is appended automatically to the folder of file name.
     */    
    function include_language($folder = '', $filename = '')
    {
        global $lang;
        
        if ($folder) {
        
            // If not filename given, make the plugin name the file name
            if (!$filename) { $filename = $folder; }
            
            // First, look in the user's language_pack folder for a language file...
            if (file_exists(LANGUAGES . LANGUAGE_PACK . $filename . '_language.php')) {
                include_once(LANGUAGES . LANGUAGE_PACK . $filename . '_language.php');
                
            // If not there, look in the default language_pack folder for a language file...
            } elseif (file_exists(LANGUAGES . 'language_default/' . $filename . '_language.php')) {
                include_once(LANGUAGES . 'language_default/' . $filename . '_language.php');

            // If still not found, look in the plugin folder for a language file... 
            } elseif (file_exists(PLUGINS . $folder . '/languages/' . $filename . '_language.php')) {
                include_once(PLUGINS . $folder . '/languages/' . $filename . '_language.php');
            
            // If STILL not found, include the user's main language file...
            } elseif (file_exists(LANGUAGES . LANGUAGE_PACK . 'main_language.php')) {
                include_once(LANGUAGES . LANGUAGE_PACK . 'main_language.php');

            // Finally, give up and include the main default language file...
            } else {
                include_once(LANGUAGES . 'language_default/main_language.php');
            }
            
            return true;
            
        }
        
        return false; 
    }


    /**
     * Find CSS file
     *
     * @param string $folder name of plugin folder
     * @param string $filename optional filename without file extension
     *
     * Note: the css file should be in a folder named 'css' and a file of 
     * the format plugin_name.css, e.g. rss_show.css
     */    
    function find_css_file($folder = '', $filename = '')
    {
        global $lang;
        
        if ($folder) {

            // If filename not given, make the plugin name the file name
            if (!$filename) { $filename = $folder; }
            
            // First look in the theme folder for a css file...     
            if (file_exists(THEMES . THEME . 'css/' . $filename . '.css')) {    
                $file_location = THEMES . THEME . 'css/' . $filename . '.css';
            
            // If not found, look in the default theme folder for a css file...     
            } elseif (file_exists(THEMES . 'default/css/' . $filename . '.css')) {    
                $file_location = THEMES . 'default/css/' . $filename . '.css';
            
            // If still not found, look in the plugin folder for a css file... 
            } elseif (file_exists(PLUGINS . $folder . '/css/' . $filename . '.css')) {
                $file_location = PLUGINS . $folder . '/css/' . $filename . '.css';
            }
             
            if (isset($file_location)) {
                return $file_location;
            }
            
        }
        
        return false; 
    }


    /**
     * Find JavaScript file
     *
     * @param string $folder name of plugin folder
     * @param string $filename optional filename without file extension
     *
     * Note: the js file should be in a folder named 'javascript' and a file of the format plugin_name.js, e.g. category_manager.js
     */    
    function find_js_file($folder = '', $filename = '')
    {
        global $lang;
        
        if ($folder) {

            // If filename not given, make the plugin name the file name
            if (!$filename) { $filename = $folder; }
            
            // First look in the theme folder for a js file...     
            if (file_exists(THEMES . THEME . 'javascript/' . $filename . '.js')) {    
                $file_location = THEMES . THEME . 'javascript/' . $filename . '.js';
                
            // If not found, look in the default theme folder for a js file...     
            } elseif (file_exists(THEMES . 'default/javascript/' . $filename . '.js')) {    
                $file_location = THEMES . 'default/javascript/' . $filename . '.js';
                
            // If still not found, look in the plugin folder for a js file... 
            } elseif (file_exists(PLUGINS . $folder . '/javascript/' . $filename . '.js')) {
                $file_location = PLUGINS . $folder . '/javascript/' . $filename . '.js';
            }
             
            if (isset($file_location)) {
                return $file_location;
            }
            
        }
        
        return false; 
    }


    /**
     * Build an array of css files to combine
     *
     * @param $plugin - the folder name of the plugin
     * @param $filename - optional css file without an extension
     */
     function include_css($plugin = '', $filename = '')
     {
        // If no filename provided, the filename is assigned the plugin name.
        if (!$filename) { $filename = $plugin; }
        
        $file_location = $this->find_css_file($plugin, $filename);
        
        // Add this css file to the global array of css_files
        array_push($this->include_css, $file_location);
     }


    /**
     * Build an array of JavaScript files to combine
     *
     * @param $plugin - the folder name of the plugin
     * @param $filename - optional js file without an extension
     */
     function include_js($plugin = '', $filename = '')
     {
        // If no filename provided, the filename is assigned the plugin name.
        if (!$filename) { $filename = $plugin; }
        
        $file_location = $this->find_js_file($plugin, $filename);
        
        // Add this css file to the global array of css_files
        array_push($this->include_js, $file_location);
     }

}

?>