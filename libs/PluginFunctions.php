<?php

class PluginFunctions extends Plugin
{
    /**
     * Get an array of plugins
     *
     * Takes plugin info read directly from plugin files, then compares each 
     * plugin to the database. If present and latest version, reads info from 
     * the database. If not in database or newer version, uses info from plugin 
     * file. Used by Plugin Management.
     */
    public function getPlugins()
    {
        $plugins_array = $this->getPluginsMeta();
        $count = 0;
        $allplugins = array();

        if ($plugins_array) {
            foreach ($plugins_array as $plugin_details) {
            
                $allplugins[$count] = array();
                $sql = "SELECT * FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s";
                $plugin_row = $this->db->get_row($this->db->prepare($sql, $plugin_details['folder']));
                
                if ($plugin_row) 
                {
                    // if plugin in folder is older or equal to plugin in database...
                    $allplugins[$count]['name'] = $plugin_row->plugin_name;
                    $allplugins[$count]['description'] = $plugin_row->plugin_desc;
                    $allplugins[$count]['folder'] = $plugin_row->plugin_folder;
                    $allplugins[$count]['status'] = $this->getPluginStatus($plugin_row->plugin_folder);
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
                    $allplugins[$count]['active'] .= "admin_index.php?page=plugins&amp;action=deactivate&amp;plugin=";
                    $allplugins[$count]['active'] .= $allplugins[$count]['folder'] . "'>";
                    $allplugins[$count]['active'] .= "<img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/active.png'></a>";
                } else {
                    $allplugins[$count]['active'] = "<a href='" . BASEURL;
                    $allplugins[$count]['active'] .= "admin_index.php?page=plugins&amp;action=activate&amp;plugin=";
                    $allplugins[$count]['active'] .= $allplugins[$count]['folder'] . "'>";
                    $allplugins[$count]['active'] .= "<img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/inactive.png'></a>";
                }


                // Conditions for "install"...
                if ($allplugins[$count]['install'] == 'install') { 
                    $allplugins[$count]['install'] = "<a href='" . BASEURL . "admin_index.php?page=plugins&amp;action=install&amp;plugin=". $allplugins[$count]['folder'] . "'><img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/install.png'></a>";
                } else { 
                    $allplugins[$count]['install'] = "<a href='" . BASEURL . "admin_index.php?page=plugins&amp;action=uninstall&amp;plugin=". $allplugins[$count]['folder'] . "' style='color: red; font-weight: bold'><img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/uninstall.png'></a>";
                }
                
                

                // Conditions for "requires"...
                if (isset($plugin_details['requires']) && $plugin_details['requires']) {
                    $this->requires = $plugin_details['requires'];
                    $this->requiresToDependencies();
                    
                    // Converts plugin folder names to well formatted names...
                    foreach ($this->dependencies as $this_plugin => $version)
                    {
                        $this->dependencies[$this_plugin] = $version;
                        $allplugins[$count]['requires'][$this_plugin] = $this->dependencies[$this_plugin];
                    }

                } else {
                    $allplugins[$count]['requires'] = array();
                }


                // Conditions for "order"...
                // The order is sorted numerically in the plugins.php template, so we need separate order and order_output elements.
                if ($allplugins[$count]['order'] != 0) { 
                    $order = $allplugins[$count]['order'];
                    $allplugins[$count]['order_output'] = "<a href='" . BASEURL;
                    $allplugins[$count]['order_output'] .= "admin_index.php?page=plugins&amp;";
                    $allplugins[$count]['order_output'] .= "action=orderup&amp;plugin=". $allplugins[$count]['folder'];
                    $allplugins[$count]['order_output'] .= "&amp;order=" . $order . "'>";
                    $allplugins[$count]['order_output'] .= "<img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/up.png'>";
                    $allplugins[$count]['order_output'] .= "</a> \n<a href='" . BASEURL;
                    $allplugins[$count]['order_output'] .= "admin_index.php?page=plugins&amp;";
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
     * Store basic plugin info in memory. We use the hotaru 
     * object because it's persistent during a page load
     */
    public function getPluginBasics()
    {
        $sql = "SELECT plugin_enabled, plugin_name, plugin_folder, plugin_class, plugin_version FROM " . TABLE_PLUGINS;
        $this->hotaru->pluginBasics = $this->db->get_results($this->db->prepare($sql));
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
        return $plugins_array;
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
    public function pluginHook(
        $hook = '', $perform = true, $folder = '', $parameters = array(), $exclude = array()
    )
    {
        if ($hook == '') {
            //echo "Error: Plugin hook name not provided.";
        } else {
            $where = "";
            
            if (!empty($folder)) {
                $where .= "AND (" . TABLE_PLUGINS . ".plugin_folder = %s) ";
            }

            $this->db->cache_queries = true;    // start using cache

            $sql = "SELECT " . TABLE_PLUGINS . ".plugin_enabled, " . TABLE_PLUGINS . ".plugin_folder, " . TABLE_PLUGINS . ".plugin_class, " . TABLE_PLUGINHOOKS . ".plugin_hook  FROM " . TABLE_PLUGINHOOKS . ", " . TABLE_PLUGINS . " WHERE (" . TABLE_PLUGINHOOKS . ".plugin_hook = %s) AND (" . TABLE_PLUGINS . ".plugin_folder = " . TABLE_PLUGINHOOKS . ".plugin_folder) " . $where . "ORDER BY " . TABLE_PLUGINHOOKS . ".phook_id";

            $plugins = $this->db->get_results($this->db->prepare($sql, $hook, $folder));

            $this->db->cache_queries = false;    // stop using cache

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
                                if($plugin->plugin_class)
                                {
                                    $this_plugin = new $plugin->plugin_class($plugin->plugin_folder, $this->hotaru);
                                    $result = $this_plugin->$hook($parameters);
                                    if ($result) {
                                        $return_array[$plugin->plugin_class . "_" . $hook] = $result; // name the result Class + hook name
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
     * Converts $this->requires into $this->dependencies array.
     * Result is an array containing 'plugin' -> 'version' pairs
     */
    public function requiresToDependencies()
    {
        // unset each key from previous time here
        foreach ($this->dependencies as $k => $v) {
            unset($this->dependencies[$k]);
        }
        
        foreach (explode(',', $this->requires) as $pair) 
        {
            list($k,$v) = explode (' ', trim($pair));
            $this->dependencies[$k] = $v;
        }
    }
    
    
    /**
     * Get a list of active or inactive plugins (or their descriptions, etc.)
     *
     * @param string $select the table column to return
     * @param int $enabled 0 for inactive, 1 for active
     * @return array
     */
    public function activePlugins($select = 'plugin_folder', $enabled = 1)
    {
        $sql = "SELECT $select FROM " . TABLE_PLUGINS . " WHERE plugin_enabled = %d";
        $active_plugins = $this->db->get_results($this->db->prepare($sql, $enabled));
        
        if ($active_plugins) { return $active_plugins; } else {return false; }
    }


    /**
     * Determines if a hook already exists (regardless of plugin)
     *
     * @param string $hook plugin hook name
     */
    public function HookExists($hook = '')
    {
        $sql = "SELECT plugin_hook FROM " . TABLE_PLUGINHOOKS . " WHERE (plugin_folder = %s) AND (plugin_hook = %s)";
        $returned_hook = $this->db->get_var($this->db->prepare($sql, $this->folder, $hook));
        if ($returned_hook) { return $returned_hook; } else { return false; }
    }
    
    
    /**
     * Removes gaps in plugin order where plugins have been uninstalled.
     */
    public function refreshPluginOrder()
    {    
        $sql = "SELECT * FROM " . TABLE_PLUGINS . " ORDER BY plugin_order ASC";
        $rows = $this->db->get_results($this->db->prepare($sql));
        
        if ($rows) { 
            $i = 1;
            foreach ($rows as $row) 
            {
                $sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_order = %d WHERE plugin_id = %d";
                $this->db->query($this->db->prepare($sql, $i, $row->plugin_id));
                $i++; 
            }
        }
        
        // optimize the table
        $this->db->query("OPTIMIZE TABLE " . TABLE_PLUGINS);
        
        return true;
    }
    
    
    /**
     * Orders the plugin hooks by plugin_order
     */
    public function sortPluginHooks()
    {    
        $sql = "SELECT p.plugin_folder, p.plugin_order, p.plugin_id, h.* FROM " . TABLE_PLUGINHOOKS . " h, " . TABLE_PLUGINS . " p WHERE p.plugin_folder = h.plugin_folder ORDER BY p.plugin_order ASC";
        $rows = $this->db->get_results($this->db->prepare($sql));

        // Drop and recreate the pluginhooks table, i.e. empty it.
        $this->db->query($this->db->prepare("TRUNCATE TABLE " . TABLE_PLUGINHOOKS));
            
        // Add plugin hooks back into the hooks table
        foreach ($rows  as $row)
        {
            $sql = "INSERT INTO " . TABLE_PLUGINHOOKS . " (plugin_folder, plugin_hook, plugin_updateby) VALUES (%s, %s, %d)";
            $this->db->query($this->db->prepare($sql, $row->plugin_folder, $row->plugin_hook, $this->current_user->id));
        }
        
        // optimize the table
        $this->db->query("OPTIMIZE TABLE " . TABLE_PLUGINHOOKS);
        
    }
    
    
    /**
     * Get number of active plugins
     *
     * @return int|false
     */
    public function numActivePlugins()
    {
        $enabled = $this->db->get_var($this->db->prepare("SELECT count(*) FROM " . TABLE_PLUGINS . " WHERE plugin_enabled = %d", 1));
        if ($enabled > 0) { return $enabled; } else { return false; }
    }
    
    
    /**
     * Read and return plugin info from top of a plugin file.
     *
     * @param string $plugin_file - a file from the /plugins folder 
     * @return array|false
     */
    public function readPluginMeta($plugin_file)
    {
        if ($plugin_file != 'placeholder.txt') {
            // Include the generic_pmd class that reads post metadata from the a plugin
            require_once(EXTENSIONS . 'GenericPHPConfig/class.metadata.php');
            $metaReader = new generic_pmd();
            $plugin_metadata = $metaReader->read(PLUGINS . $plugin_file . "/" 
            . $plugin_file . ".php");
            
            if ($plugin_metadata) {
                return $plugin_metadata;
            }
        }
        return false;
    }
    
    
    /**
     * Assign info from top of a plugin file to the current object.
     *
     * @param array $plugin_metadata 
     * @return array|false
     */
    public function assignPluginMeta($plugin_metadata)
    {
        if (!$plugin_metadata) { return false; }
        
        $this->name     = $plugin_metadata['name'];
        $this->desc     = $plugin_metadata['description'];
        $this->version  = $plugin_metadata['version'];
        $this->folder   = $plugin_metadata['folder'];
        $this->class    = $plugin_metadata['class'];
        $this->hooks    = explode(',', $plugin_metadata['hooks']);
        
        if (isset($plugin_metadata['requires']) && $plugin_metadata['requires']) {
            $this->requires = $plugin_metadata['requires'];
            $this->requiresToDependencies();
        }
        
        return true;
    }
    

    /**
     * Determines if a plugin is enabled or not
     *
     * @param string $folder plugin folder name
     * @return string
     */
    public function getPluginStatus($folder = '')
    {
        if (!$folder) { $folder = $this->folder; } 
        
        $sql = "SELECT * FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s";
        $plugin_row = $this->db->get_row($this->db->prepare($sql, $folder));
        
        if ($plugin_row && $plugin_row->plugin_enabled == 1) {
            $status = "active";
        } else {
            $status = "inactive";
        } 

        return $status;
    }
    
    
    /**
     * Check if a plugin hook exists for a given plugin
     *
     * @param string $folder plugin folder name
     * @param string $hook plugin hook name
     * @return int|false
     */
    public function isHook($hook = "", $folder = "")
    {
        if (!$folder) { $folder = $this->folder; }
        
        $sql = "SELECT count(*) FROM " . TABLE_PLUGINHOOKS . " WHERE plugin_folder = %s AND plugin_hook = %s";
        if ($this->db->get_var($this->db->prepare($sql, $folder, $hook))) { return true;} else { return false; }
    }
    
    
    /**
     * Add a plugin to the plugins table
     *
     * @param int $upgrade flag to indicate we need to show "Upgraded!" instead of "Installed!" message
     */
    public function install($upgrade = 0)
    {
        $admin = $this->getAdminFunctions();
        
        // Clear the database cache to ensure stored plugins and hooks 
        // are up-to-date.
        $admin->deleteFiles(CACHE . 'db_cache');
        
        // Clear the css/js cache to ensure any new ones get included
        $admin->clearCache('css_js_cache', false);
        
        // Read meta from the top of the plugin file
        $plugin_metadata = $this->readPluginMeta($this->folder);
        
        $this->enabled  = 1;    // Enable it when we add it to the database.
        $this->assignPluginMeta($plugin_metadata);

        $dependency_error = 0;
        foreach ($this->dependencies as $dependency => $version)
        {
            if (version_compare($version, $this->getVersion($dependency), '>')) {
                $dependency_error = 1;
            }
        }
        
        if ($dependency_error == 1)
        {
            foreach ($this->dependencies as $dependency => $version)
            {
                    if (($this->getPluginStatus($dependency) == 'inactive') 
                        || version_compare($version, $this->getVersion($dependency), '>')) {
                        $dependency = make_name($dependency);
                        $this->hotaru->messages[$this->lang["admin_plugins_install_sorry"] . " " . $this->name . " " . $this->lang["admin_plugins_install_requires"] . " " . $dependency . " " . $version] = 'red';
                    }
            }
            return false;
        }
                    
        $sql = "REPLACE INTO " . TABLE_PLUGINS . " (plugin_enabled, plugin_name, plugin_folder, plugin_class, plugin_desc, plugin_requires, plugin_version, plugin_updateby) VALUES (%d, %s, %s, %s, %s, %s, %s, %d)";
        $this->db->query($this->db->prepare($sql, $this->enabled, $this->name, $this->folder, $this->class, $this->desc, $this->requires, $this->version, $this->current_user->id));

        // Get the last order number - doing this after REPLACE INTO because 
        // we don't know whether the above will insert or replace.
        $sql = "SELECT plugin_order FROM " . TABLE_PLUGINS . " ORDER BY plugin_order DESC LIMIT 1";
        $highest_order = $this->db->get_var($this->db->prepare($sql));

        // Give the new plugin the order number + 1
        $sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_order = %d WHERE plugin_id = LAST_INSERT_ID()";
        $this->db->query($this->db->prepare($sql, ($highest_order + 1)));
        
        // Add any plugin hooks to the hooks table
        $this->addPluginHooks();
        
        // Force inclusion of a language file (if exists) because the 
        // plugin isn't ready to include it itself yet.
        $this->includeLanguage($this->folder);
        
        $result = $this->pluginHook('install_plugin', true, $this->folder);
        
        // For plugins to avoid showing this success message, they need to 
        // return a non-boolean value to $result.
        if (!is_array($result))
        {
            if ($upgrade == 0) {
                $this->hotaru->messages[$this->lang["admin_plugins_install_done"]] = 'green';
            } else {
                $this->hotaru->messages[$this->lang["admin_plugins_upgrade_done"]] = 'green';
            }
        }
    }

    /**
     * Adds all hooks for a given plugin
     */
    public function addPluginHooks()
    {
        foreach ($this->hooks as $hook)
        {
            $exists = $this->isHook(trim($hook));

            if (!$exists) {
                $sql = "INSERT INTO " . TABLE_PLUGINHOOKS . " (plugin_folder, plugin_hook, plugin_updateby) VALUES (%s, %s, %d)";
                $this->db->query($this->db->prepare($sql, $this->folder, trim($hook), $this->current_user->id));
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
    public function upgrade()
    {
        $admin = $this->getAdminFunctions();
        
        // Read meta from the top of the plugin file
        $plugin_metadata = $this->readPluginMeta($this->folder);
        
        $this->enabled  = 1;    // Enable it when we add it to the database.
        $this->assignPluginMeta($plugin_metadata);
        
        $this->uninstall(1);    // 1 indicates that "upgrade" is true, used to disable the "Uninstalled" message
        $this->install(1);      // 1 indicates that "upgrade" is true. 
    }
    
    
    /**
     * Enables or disables a plugin, installing if necessary
     *
     * @param int $enabled 
     * Note: This function does not uninstall/delete a plugin.
     */
    public function activateDeactivate($enabled = 0)
    {    // 0 = deactivate, 1 = activate
        $admin = $this->getAdminFunctions();

        // Clear the database cache to ensure plugins and hooks are up-to-date.
        $admin->deleteFiles(CACHE . 'db_cache');
        
        // Clear the css/js cache to ensure any new ones get included
        $admin->clearCache('css_js_cache', false);
        
        // Get the enabled status for this plugin...
        $plugin_row = $this->db->get_row($this->db->prepare("SELECT plugin_folder, plugin_enabled FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s", $this->folder));
        
        // If no result, then it's obviously not installed...
        if (!$plugin_row) 
        {
            // If the user is activating the plugin, go and install it...
            if ($enabled == 1) { $this->install(); }
        } 
        else 
        {
            $this->activateDeactivateDo($plugin_row, $enabled);
        }
    }
    

    /**
     * Enables or disables all plugins, installing if necessary
     *
     * @param int $enabled 
     * Note: This function does not uninstall/delete a plugin.
     */
    public function activateDeactivateAll($enabled = 0)
    {    // 0 = deactivate, 1 = activate
        
        // if you want to activate, find all the inactive plugins and vice-versa:
        if ($enabled == 0) { $active_plugins = $this->activePlugins('*', 1); }
        if ($enabled == 1) { $active_plugins = $this->activePlugins('*', 0); }

        if (!$active_plugins) { return false; }
                
        /*  The problem with upgrading plugins is many of them require other plugins to work, 
            therefore half the plugins can't be upgraded if the upgrade is attempted in a 
            random order. So let's minimize the problem by sorting the plugins by number of 
            requirements, i.e. plugins that have no requirements (Users, Submit, Sidebar Widgets)
            are upgraded first, then plugins with one requirement... and finally Pligg Importer,
            which has about 7 requirements. */ 
        $i = 0;
        foreach ($active_plugins as $active) {
            $this->folder = $active->plugin_folder;
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
            $plugin_row = $this->db->get_row($this->db->prepare("SELECT plugin_folder, plugin_enabled FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s", $ord['name']));
            $this->folder = $plugin_row->plugin_folder;
            $this->activateDeactivateDo($plugin_row, $enabled);
        }
    }
    
    
    /**
     * Enables or disables all plugins, installing if necessary
     *
     * @param int $enabled 
     * Note: This function does not uninstall/delete a plugin.
     */
    public function activateDeactivateDo($plugin, $enabled = 0)
    {    // 0 = deactivate, 1 = activate
        // The plugin is already installed. Activate or deactivate according to $enabled (the user's action).
        if ($plugin->plugin_enabled == $enabled) { return false; }  // only update if we're changing the enabled value.
        
        $sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_enabled = %d, plugin_updateby = %d WHERE plugin_folder = %s";
        $this->db->query($this->db->prepare($sql, $enabled, $this->current_user->id, $this->folder));
        
        if ($enabled == 1) { // Activating now...
        
            // Get plugin version from the database...
            $db_version = $this->getVersion();
            
            // Get plugin version from the file....
            $plugin_metadata = $this->readPluginMeta($this->folder);
            $file_version = $plugin_metadata['version'];
            
            // If file version is newer the the current plugin version, then upgrade...
            if (version_compare($file_version, $db_version, '>')) {
                $this->upgrade(); // runs the install function ans hows "upgraded!" message instead of "installed".
            } else {
                // else simply show an activated message...
                $this->hotaru->messages[$this->lang["admin_plugins_activated"]] = 'green'; 
            }
            
            // Force inclusion of a language file (if exists) because the 
            // plugin isn't ready to include it itself yet.
            $this->includeLanguage($this->folder);
        }
        
        if ($enabled == 0) { 
            $this->hotaru->messages[$this->lang["admin_plugins_deactivated"]] = 'green'; 
        }
    }
    
    
    
    /**
     * Get plugin version number from the database
     *
     * @return int - version number
     */
    public function getVersion($folder = '')
    {    
        //set default to current if not specified
        if (!$folder) { $folder = $this->folder; }

        $version = $this->db->get_var($this->db->prepare("SELECT plugin_version FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s", $folder));
        if ($version) { return $version; } else { return false; }
    }
    
    
    /**
     * Uninstall all plugins
     */
    public function uninstallAll()
    {            
        $admin = $this->getAdminFunctions();
        
        // Clear the database cache to ensure plugins and hooks are up-to-date.
        $admin->deleteFiles(CACHE . 'db_cache');
        
        // Clear the css/js cache to ensure this plugin's files are removed
        $admin->clearCache('css_js_cache', false);

        $admin->emptyTable(TABLE_PLUGINS, $msg = false);
        $admin->emptyTable(TABLE_PLUGINHOOKS, $msg = false);
        
        $this->hotaru->messages[$this->lang["admin_plugins_uninstall_all_done"]] = 'green';
    }
    
    
    /**
     * Delete plugin from table_plugins, pluginhooks and pluginsettings
     *
     * @param int $upgrade flag to disable message
     */
    public function uninstall($upgrade = 0)
    {    
        $admin = $this->getAdminFunctions();
        
        // Clear the database cache to ensure plugins and hooks are up-to-date.
        $admin->deleteFiles(CACHE . 'db_cache');
        
        // Clear the css/js cache to ensure this plugin's files are removed
        $admin->clearCache('css_js_cache', false);

        $this->db->query($this->db->prepare("DELETE FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s", $this->folder));
        $this->db->query($this->db->prepare("DELETE FROM " . TABLE_PLUGINHOOKS . " WHERE plugin_folder = %s", $this->folder));
        
        // Settings aren't deleted anymore, but a user can do so manually from Admin->Maintenance
        //$this->db->query($this->db->prepare("DELETE FROM " . TABLE_PLUGINSETTINGS . " WHERE plugin_folder = %s", $this->folder));
        
        if ($upgrade == 0) {
            $this->hotaru->messages[$this->lang["admin_plugins_uninstall_done"]] = 'green';
        }
        
        $this->refreshPluginOrder();
    }
    
    
    /**
     * Updates plugin order and order of their hooks, i.e. changes the order 
     * of plugins in pluginHook.
     * 
     * @param string $folder plugin folder name
     * @param int $order current order
     * @param string $arrow direction to move
     */
    public function pluginOrder($order = 0, $arrow = "up")
    {
        if ($order == 0) {
            $this->hotaru->messages[$this->lang['admin_plugins_order_zero']] = 'red';
            return false;
        }
                
        if ($arrow == "up")
        {
            // get row above
            $sql= "SELECT * FROM " . TABLE_PLUGINS . " WHERE plugin_order = %d";
            $row_above = $this->db->get_row($this->db->prepare($sql, ($order - 1)));
            
            if (!$row_above) {
                $this->hotaru->messages[$this->name . " " . $this->lang['admin_plugins_order_first']] = 'red';
                return false;
            }
            
            if ($row_above->plugin_order == $order) {
                $this->hotaru->messages[$this->lang['admin_plugins_order_above']] = 'red';
                return false;
            }
            
            // update row above 
            $sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_order = %d WHERE plugin_id = %d";
            $this->db->query($this->db->prepare($sql, ($row_above->plugin_order + 1), $row_above->plugin_id)); 
            
            // update current plugin
            $sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_order = %d WHERE plugin_folder = %s";
            $this->db->query($this->db->prepare($sql, ($order - 1), $this->folder)); 
        }
        else
        {
            // get row below
            $sql= "SELECT * FROM " . TABLE_PLUGINS . " WHERE plugin_order = %d";
            $row_below = $this->db->get_row($this->db->prepare($sql, ($order + 1)));
            
            if (!$row_below) {
                $this->hotaru->messages[$this->name . " " . $this->lang['admin_plugins_order_last']] = 'red';
                return false;
            }
            
            if ($row_below->plugin_order == $order) {
                $this->hotaru->messages[$this->lang['admin_plugins_order_below']] = 'red';
                return false;
            }
            
            // update row above 
            $sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_order = %d WHERE plugin_id = %d";
            $this->db->query($this->db->prepare($sql, ($row_below->plugin_order - 1), $row_below->plugin_id)); 
            
            // update current plugin
            $sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_order = %d WHERE plugin_folder = %s";
            $this->db->query($this->db->prepare($sql, ($order + 1), $this->folder)); 
        }

        $this->hotaru->messages[$this->lang['admin_plugins_order_updated']] = 'green';

        // Resort all orders and remove any accidental gaps
        $this->refreshPluginOrder();
        $this->sortPluginHooks();

        return true;

    }
    
    
    /**
     * Get a plugin's actual name from its folder name
     *
     * @param string $folder plugin folder name
     * @return string
     */
    public function getPluginName($folder = "")
    {    
        if (!$folder) { $folder = $this->folder; } 
        
        $this->name = $this->db->get_var($this->db->prepare("SELECT plugin_name FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s", $folder));
        return $this->name;
    }
    
    
    /**
     * Get a plugin's class from its folder name
     *
     * @param string $folder plugin folder name
     * @return string|false
     */
    public function getClassName($folder = "")
    {    
        if (!$folder) { $folder = $this->folder; } 
        
        if (!$this->hotaru->pluginBasics) { //not in memory
            $this->getPluginBasics(); // get from database
        }
        
        if (!$this->hotaru->pluginBasics) { 
            return false; // no plugin basics for this plugin found anywhere
        }
        
        // get plugin basics from memory
        foreach ($this->hotaru->pluginBasics as $item => $key) {
            if ($key->plugin_folder == $folder) {
                $this->class = $key->plugin_class;
                return $this->class;
            }
        }
        
        /* old code to get plugin class directly from the database:
        $class = $this->db->get_var($this->db->prepare("SELECT plugin_class FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s", $folder));
        */
        
        return false;
    }
    
    
    /**
     * Get version number of plugin if active
     *
     * @param string $folder plugin folder name
     * @return string|false
     */
    public function isActive($folder = "")
    {
        if (!$folder) { $folder = $this->folder; } 
        
        if (!$this->hotaru->pluginBasics) { //not in memory
            $this->getPluginBasics(); // get from database
        }

        if (!$this->hotaru->pluginBasics) { 
            return false; // no plugin basics for this plugin found anywhere
        }
        
        // get plugin basics from memory
        foreach ($this->hotaru->pluginBasics as $item => $key) {
            if (($key->plugin_folder == $folder) && ($key->plugin_enabled == 1)) {
                return $key->plugin_version;
            }
        }
        
        /* old code to get plugin version directly from the database:
        $active= $this->db->get_row($this->db->prepare("SELECT plugin_enabled, plugin_version FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s", $folder));
        */

        return false;
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
    public function includeLanguage($filename = '', $folder = '')
    {
        if (!$folder) { $folder = $this->folder; }
        
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
            
            // Add new language to our lang property
            if (isset($lang)) {
                foreach($lang as $l => $text) {
                    $this->lang[$l] = $text;
                }
            }
        }
    }
        
    /**
     * Redirect to Hotaru for including CSS (simplifies plugin development to include via Plugins)
     *
     * @param string $filename optional filename without file extension
     *
     */    
    public function includeCss($filename = '', $folder = '')
    {
        if (!$folder) { $folder = $this->folder; }
        if (!$filename) { $filename = $folder; }
        
        return $this->hotaru->includeCss($filename, $folder); // returned for testing purposes only
    }
    
    
    /**
     * Redirect to Hotaru for including CSS (simplifies plugin development to include via Plugins)
     *
     * @param string $filename optional filename without file extension
     *
     */    
    public function includeJs($filename = '', $folder = '')
    {
        if (!$folder) { $folder = $this->folder; }
        if (!$filename) { $filename = $folder; }
        
        return $this->hotaru->includeJs($filename, $folder); // returned for testing purposes only
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
    public function getSetting($setting = '', $folder = '')
    {
        if (!$folder) { $folder = $this->folder; }
        
        if ($this->hotaru->isAdmin)
        {
            // In Admin. Let's pull settings from the database to avoid problems when saving in Plugin Settings:
            $sql = "SELECT plugin_value FROM " . TABLE_PLUGINSETTINGS . " WHERE (plugin_folder = %s) AND (plugin_setting = %s)";
            $value = $this->db->get_var($this->db->prepare($sql, $folder, $setting));
        }
        else
        {
            // get all settings fro the database if we haven't already:
            if (!$this->hotaru->pluginSettings) { $this->getAllPluginSettings(); }
            
            // get the settings we need from memory
            foreach ($this->hotaru->pluginSettings as $item => $key) {
                if (($key->plugin_folder == $folder) && ($key->plugin_setting == $setting)) {
                        $value = $key->plugin_value;
                }
            }
        }

        if (isset($value)) { return $value; } else { return false; }
    }
    
    
    /**
     * Get an array of settings for a given plugin
     *
     * @param string $folder name of plugin folder
     * @return array|false
     *
     * Note: Unlike "getSetting", this will get ALL settings with the same name.
     */
    public function getSettingsArray($folder = '')
    {
        if (!$folder) { $folder = $this->folder; }
        
        $sql = "SELECT plugin_setting, plugin_value FROM " . TABLE_PLUGINSETTINGS . " WHERE (plugin_folder = %s)";
        $results = $this->db->get_results($this->db->prepare($sql, $folder));
        
        if ($results) { return $results; } else { return false; }
    }
    
    
    /**
     * Get and unserialize serialized settings
     *
     * @param string $folder plugin folder name
     * @param string $settings_name optional settings name if different from folder
     * @return array - of submit settings
     */
    public function getSerializedSettings($folder = '', $settings_name = '')
    {
        if($folder == 'vote') { echo "getting vote ettings"; }
        if (!$folder) { $folder = $this->folder; }

        // Get settings from the database if they exist...
        if (!$settings_name) {
            $settings = unserialize($this->getSetting($folder . '_settings', $folder));
        } else {
            $settings = unserialize($this->getSetting($settings_name, $folder));
        }
        return $settings;
    }
    
    
    /**
     * Get and store all plugin settings in $this->hotaru->pluginSettings
     * We use the Hotaru object because it's persistent during a page load
     *
     * @return array - all settings
     */
    public function getAllPluginSettings()
    {
        $sql = "SELECT plugin_folder, plugin_setting, plugin_value FROM " . TABLE_PLUGINSETTINGS;
        $results = $this->db->get_results($this->db->prepare($sql));
        $this->hotaru->pluginSettings = $results;
    }
    
    
    /**
     * Determine if a plugin setting already exists
     *
     * @param string $folder name of plugin folder
     * @param string $setting name of the setting to retrieve
     * @return string|false
     */
    public function isSetting($setting = '', $folder = '')
    {
        if (!$folder) { $folder = $this->folder; }
        
        $sql = "SELECT plugin_setting FROM " . TABLE_PLUGINSETTINGS . " WHERE (plugin_folder = %s) AND (plugin_setting = %s)";
        $returned_setting = $this->db->get_var($this->db->prepare($sql, $folder, $setting));
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
    public function updateSetting($setting = '', $value = '', $folder = '')
    {
        if (!$folder) { $folder = $this->folder; }
        
        $exists = $this->isSetting($setting, $folder);
        if (!$exists) 
        {
            $sql = "INSERT INTO " . TABLE_PLUGINSETTINGS . " (plugin_folder, plugin_setting, plugin_value, plugin_updateby) VALUES (%s, %s, %s, %d)";
            $this->db->query($this->db->prepare($sql, $folder, $setting, $value, $this->current_user->id));
        } else 
        {
            $sql = "UPDATE " . TABLE_PLUGINSETTINGS . " SET plugin_folder = %s, plugin_setting = %s, plugin_value = %s, plugin_updateby = %d WHERE (plugin_folder = %s) AND (plugin_setting = %s)";
            if (isset($this->current_user->id)) { $updateby = $this->current_user->id; } else { $updateby = 1; }
            $this->db->query($this->db->prepare($sql, $folder, $setting, $value, $updateby, $folder, $setting));
        }
        
        // optimize the table
        $this->db->query("OPTIMIZE TABLE " . TABLE_PLUGINSETTINGS);
    }
    

    /**
     * Deletes rows from pluginsettings that match a given setting or plugin
     *
     * @param string $setting name of the setting to remove
     * @param string $folder name of plugin folder
     */
    public function deleteSettings($setting = '', $folder = '')
    {
        if ($setting) {
            $sql = "DELETE FROM " . TABLE_PLUGINSETTINGS . " WHERE plugin_setting = %s";
            $this->db->query($this->db->prepare($sql, $setting));
        } 
        elseif ($folder) 
        {
            $sql = "DELETE FROM " . TABLE_PLUGINSETTINGS . " WHERE plugin_folder = %s";
            $this->db->query($this->db->prepare($sql, $folder));
        }
        
        // optimize the table
        $this->db->query("OPTIMIZE TABLE " . TABLE_PLUGINSETTINGS);
    }
    
    
     /**
     * Check if a value is blocked
     *
     * @param string $type - i.e. ip, url, email, user
     * @param string $value
     * @param bool $like - used for LIKE sql if true
     * @return bool
     */
    public function isBlocked($type = '', $value = '', $operator = '=')
    {
        $admin = $this->getAdminFunctions();
        return $admin->isBlocked($type, $value, $operator);
    }


     /**
     * get Admin Functions
     *
     * @return object $admin
     */
    public function getAdminFunctions()
    {
        require_once(LIBS . 'Admin.php');
        $admin = new Admin();
        return $admin;
    }
    
    
}

?>