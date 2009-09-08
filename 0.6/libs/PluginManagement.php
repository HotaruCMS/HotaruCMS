<?php

class PluginManagement extends Plugin
{

    private $includeCSS    = array();  // a list of css files to include
    private $includeJS     = array();  // a list of js files to include
    private $includeType   = '';       // 'css' or 'js'
    
    /**
     * getIncludeCSS
     */
    public function getIncludeCSS()
    {
        return $this->includeCSS;
    }
    

    /**
     * getIncludeJS
     */
    public function getIncludeJS()
    {
        return $this->includeJS;
    }
    
    
    /**
     * getIncludeType
     */
    public function getIncludeType()
    {
        return $this->includeType;
    }
    
    
    /**
     * Get an array of plugins
     *
     * Takes plugin info read directly from plugin files, then compares each 
     * plugin to the database. If present and latest version, reads info from 
     * the database. If not in database or newer version, uses info from plugin 
     * file. Used by Plugin Management.
     */
    function getPlugins()
    {
        global $db, $lang;
        $plugins_array = $this->getPluginsMeta();
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
                    $this->requiresToDependencies();

                    // Converts plugin folder names to well formatted names...
                    foreach ($this->dependencies as $this_plugin => $version)
                    {
                        unset($this->dependencies[$this_plugin]);
                        $this->dependencies[$this->name] = $version;
                        $allplugins[$count]['requires'][$this->name] = $this->dependencies[$this->name];
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
    function getPluginsMeta()
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
    function checkActions(
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

            $sql = "SELECT " . TABLE_PLUGINS . ".plugin_enabled, " . TABLE_PLUGINS . ".plugin_folder, " . TABLE_PLUGINS . ".plugin_class, " . TABLE_PLUGINS . ".plugin_prefix, " . TABLE_PLUGINHOOKS . ".plugin_hook  FROM " . TABLE_PLUGINHOOKS . ", " . TABLE_PLUGINS . " WHERE (" . TABLE_PLUGINHOOKS . ".plugin_hook = %s) AND (" . TABLE_PLUGINS . ".plugin_folder = " . TABLE_PLUGINHOOKS . ".plugin_folder) " . $where . "ORDER BY " . TABLE_PLUGINHOOKS . ".phook_id";

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
                                elseif($plugin->plugin_class)
                                {
                                    $this_plugin = new $plugin->plugin_class($plugin->plugin_folder);
                                    $result = $this_plugin->$hook($parameters);
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
     * Converts $this->requires into $this->dependencies array.
     * Result is an array containing 'plugin' -> 'version' pairs
     */
    function requiresToDependencies()
    {
        unset($this->dependencies);
        foreach (explode(',', $this->requires) as $pair) 
        {
            list($k,$v) = explode (' ', trim($pair));
            $this->dependencies[$k] = $v;
        }
    }
    
    
    /**
     * Get a list of active plugins (or their descriptions, etc.)
     *
     * @param string $select the table column to return
     * @return array
     */
    function activePlugins($select = 'plugin_folder')
    {
        global $db;
        $sql = "SELECT " . $select . " FROM " . TABLE_PLUGINS . " WHERE plugin_enabled = %d";
        $active_plugins = $db->get_results($db->prepare($sql, $select, 1));
        if ($active_plugins) { return $active_plugins; } else {return false; }
    }


    /**
     * Determines if a hook already exists (regardless of plugin)
     *
     * @param string $hook plugin hook name
     */
    function HookExists($hook = "")
    {
        global $db;
        
        $sql = "SELECT plugin_hook FROM " . TABLE_PLUGINHOOKS . " WHERE (plugin_folder = %s) AND (plugin_hook = %s)";
        $returned_hook = $db->get_var($db->prepare($sql, $this->folder, $hook));
        if ($returned_hook) { return $returned_hook; } else { return false; }
    }
    
    
    /**
     * Removes gaps in plugin order where plugins have been uninstalled.
     */
    function refreshPluginOrder()
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
    function sortPluginHooks()
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
     * Get number of active plugins
     *
     * @return int|false
     */
    function numActivePlugins()
    {
        global $db;
        $enabled = $db->get_var($db->prepare("SELECT count(*) FROM " . TABLE_PLUGINS . " WHERE plugin_enabled = %d", 1));
        if ($enabled > 0) { return $enabled; } else { return false; }
    }
    
    
    /**
     * Delete rows from pluginsettings that match a given setting
     *
     * @param string $setting name of the setting to remove
     */
    function pluginSettingsRemoveSetting($setting = '')
    {
        global $db;
        
        $sql = "DELETE FROM " . TABLE_PLUGINSETTINGS . " WHERE plugin_setting = %s";
        $db->query($db->prepare($sql, $setting));
    }
}

?>