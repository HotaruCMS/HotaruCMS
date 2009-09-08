<?php

class PluginFunctions extends PluginManagement
{

    protected $id           = '';         // plugin id
    protected $enabled      = 0;          // activate (1), inactive (0)
    protected $name         = '';         // plugin proper name
    protected $prefix       = '';         // plugin prefix
    protected $folder       = '';         // plugin folder name
    protected $class        = '';         // plugin class name
    protected $desc         = '';         // plugin description
    protected $version      = 0;          // plugin version number
    protected $order        = 0;          // plugin order number
    protected $requires     = '';         // string of plugin->version pairs
    protected $dependencies = array();    // array of plugin->version pairs
    protected $hooks        = array();    // array of plugin hooks
        
    public function __construct($folder = '') 
    {
        $this->setFolder($folder);
        $this->setName(make_name($folder));
    }
    
    public function __toString()
    {
        return $this->class;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
    
    public function setFolder($folder)
    {
        $this->folder = $folder;
    }

    public function getFolder()
    {
        return $this->folder;
    }
        
    public function nameLength()
    {
        return strlen($this->name);
    }


    /**
     * Read and return plugin info from top of a plugin file.
     *
     * @param string $plugin_file - a file from the /plugins folder 
     * @return array|false
     */
    function readPluginMeta($plugin_file)
    {
        if($plugin_file != 'placeholder.txt') {
            $plugin_metadata = $this->read(PLUGINS . $plugin_file . "/" 
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
    function assignPluginMeta($plugin_metadata)
    {
        if(!$plugin_metadata) { return false; }
        
        $this->name     = $plugin_metadata['name'];
        $this->desc     = $plugin_metadata['description'];
        $this->prefix   = $plugin_metadata['prefix'];
        $this->version  = $plugin_metadata['version'];
        $this->folder   = $plugin_metadata['folder'];
        $this->hooks    = explode(',', $plugin_metadata['hooks']);
            
        if (isset($plugin_metadata['requires']) && $plugin_metadata['requires']) {
            $this->requires = $plugin_metadata['requires'];
            $this->requiresToDependencies();
        }
    
        if (isset($plugin_metadata['class']) && $plugin_metadata['class']) {
            $this->class = $plugin_metadata['class'];
        }
        
        return true;
    }
    

    /**
     * Determines if a plugin is enabled or not
     *
     * @param string $plugin_folder plugin folder name
     * @return string
     */
    public function getPluginStatus($plugin_folder = '')
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
     * Check if a plugin hook exists for a given plugin
     *
     * @param string $folder plugin folder name
     * @param string $hook plugin hook name
     * @return int|false
     */
    function pluginHookExists($folder = "", $hook = "")
    {
        global $db;
        
        $sql = "SELECT count(*) FROM " . TABLE_PLUGINHOOKS . " WHERE plugin_folder = %s AND plugin_hook = %s";
        if ($db->get_var($db->prepare($sql, $folder, $hook))) { return true;} else { return false; }
    }
    
    
    /**
     * Add a plugin to the plugins table
     *
     * @param int $upgrade flag to indicate upgrade script available
     */
    function install($upgrade = 0)
    {
        global $db, $lang, $hotaru, $current_user, $admin, $plugins;
        
        // Clear the database cache to ensure stored plugins and hooks 
        // are up-to-date.
        $admin->deleteFiles(CACHE . 'db_cache');
        
        // Read meta from the top of the plugin file
        $plugin_metadata = $this->read(PLUGINS . $this->folder . "/" . $this->folder . ".php");
        
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
                        $hotaru->messages[$lang["admin_plugins_install_sorry"] . " " . $this->name . " " . $lang["admin_plugins_install_requires"] . " " . $dependency . " " . $version] = 'red';
                    }
            }
            return false;
        }
                    
        $sql = "REPLACE INTO " . TABLE_PLUGINS . " (plugin_enabled, plugin_name, plugin_prefix, plugin_folder, plugin_class, plugin_desc, plugin_requires, plugin_version, plugin_updateby) VALUES (%d, %s, %s, %s, %s, %s, %s, %s, %d)";
        $db->query($db->prepare($sql, $this->enabled, $this->name, $this->prefix, $this->folder, $this->class, $this->desc, $this->requires, $this->version, $current_user->id));

        // Get the last order number - doing this after REPLACE INTO because 
        // we don't know whether the above will insert or replace.
        $sql = "SELECT plugin_order FROM " . TABLE_PLUGINS . " ORDER BY plugin_order DESC LIMIT 1";
        $highest_order = $db->get_var($db->prepare($sql));

        // Give the new plugin the order number + 1
        $sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_order = %d WHERE plugin_id = LAST_INSERT_ID()";
        $db->query($db->prepare($sql, ($highest_order + 1)));
        
        // Add any plugin hooks to the hooks table
        $this->addPluginHooks();
        
        // Force inclusion of a language file (if exists) because the 
        // plugin isn't ready to include it itself yet.
        $this->includeLanguage($this->folder);
            
        $result = $plugins->checkActions('install_plugin', $this->folder);
        
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
    function addPluginHooks()
    {
        global $db, $current_user;
        
        foreach ($this->hooks as $hook)
        {
            $exists = $this->pluginHookExists(trim($hook));

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
    function upgrade()
    {
        global $db, $lang, $hotaru, $admin, $plugins;
        
        // Read meta from the top of the plugin file
        $plugin_metadata = $this->read(PLUGINS . $this->folder . "/" . $this->folder . ".php");
        
        $this->enabled  = 1;    // Enable it when we add it to the database.
        $this->assignPluginMeta($plugin_metadata);
        
        if (in_array('upgrade_plugin', $this->hooks)) 
        {
            // Clear the database cache to ensure stored plugins and hooks 
            // are up-to-date.
            $admin->delete_files(CACHE . 'db_cache');
            
            // Force inclusion of a language file (if exists) because the 
            // plugin isn't ready to include it itself yet.
            $this->includeLanguage($folder);
            
            // Add any new hooks to the hooks table before proceeding.
            $this->addPluginHooks(); 
        } else {
            // Uninstall and then re-install because there's no upgrade function
            $this->uninstall(1);    // 1 indicates that "upgrade" is true. 
            $this->install(1);      // 1 indicates that "upgrade" is true. 
        }
                
        $result = $plugins->checkActions('upgrade_plugin', $this->folder);
                
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
    function activateDeactivate($enabled = 0)
    {    // 0 = deactivate, 1 = activate
        global $db, $hotaru, $lang, $admin, $current_user;
        
        // Clear the database cache to ensure plugins and hooks are up-to-date.
        $admin->deleteFiles(CACHE . 'db_cache');
        
        // Get the enabled status for this plugin...
        $plugin_row = $db->get_row($db->prepare("SELECT plugin_folder, plugin_enabled FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s", $this->folder));
        
        // If no result, then it's obviously not installed...
        if (!$plugin_row) 
        {
            // If the user is activating the plugin, go and install it...
            if ($enabled == 1) {    
                $this->install();
            }
        } 
        else 
        {
            // The plugin is already installed. Activate or deactivate according to $enabled (the user's action).
            if ($plugin_row->plugin_enabled != $enabled) {        // only update if we're changing the enabled value.
                $sql = "UPDATE " . TABLE_PLUGINS . " SET plugin_enabled = %d, plugin_updateby = %d WHERE plugin_folder = %s";
                $db->query($db->prepare($sql, $enabled, $current_user->id, $this->folder));
                
                if ($enabled == 1) { // Activating now...
                
                    // Get plugin version from the database...
                    $db_version = $this->getVersion();
                    
                    // Get plugin version from the file....
                    $plugin_metadata = $this->read(PLUGINS . $this->folder . "/" . $this->folder . ".php");
                    $file_version = $plugin_metadata['version'];
                    
                    // If file version is newer the the current plugin version, then upgrade...
                    if (version_compare($file_version, $db_version, '>')) {
                        $this->upgrade();
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
     * @return int - version number
     */
    function getVersion($folder = '')
    {    
        global $db;
        
        //set default to current if not specified
        if (!$folder) { $folder = $this->folder; }

        $version = $db->get_var($db->prepare("SELECT plugin_version FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s", $folder));
        if ($version) { return $version; } else { return false; }
    }
    
    
    /**
     * Delete plugin from table_plugins, pluginhooks and pluginsettings
     *
     * @param int $upgrade flag to disable message
     */
    function uninstall($upgrade = 0)
    {    
        global $db, $hotaru, $lang, $admin, $plugins;
        
        // Clear the database cache to ensure plugins and hooks are up-to-date.
        $admin->deleteFiles(CACHE . 'db_cache');

        $db->query($db->prepare("DELETE FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s", $this->folder));
        $db->query($db->prepare("DELETE FROM " . TABLE_PLUGINHOOKS . " WHERE plugin_folder = %s", $this->folder));
        $db->query($db->prepare("DELETE FROM " . TABLE_PLUGINSETTINGS . " WHERE plugin_folder = %s", $this->folder));
        
        if ($upgrade == 0) {
            $hotaru->messages[$lang["admin_plugins_uninstall_done"]] = 'green';
        }
        
        $plugins->refreshPluginOrder();
    }
    
    
    /**
     * Updates plugin order and order of their hooks, i.e. changes the order 
     * of plugins in check_actions.
     * 
     * @param string $folder plugin folder name
     * @param int $order current order
     * @param string $arrow direction to move
     */
    function pluginOrder($order = 0, $arrow = "up")
    {
        global $db, $hotaru, $lang, $plugins;
            
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
                $hotaru->messages[$this->name . " " . $lang['admin_plugins_order_first']] = 'red';
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
            $db->query($db->prepare($sql, ($order - 1), $this->folder)); 
        }
        else
        {
            // get row below
            $sql= "SELECT * FROM " . TABLE_PLUGINS . " WHERE plugin_order = %d";
            $row_below = $db->get_row($db->prepare($sql, ($order + 1)));
            
            if (!$row_below) {
                $hotaru->messages[$this->name . " " . $lang['admin_plugins_order_last']] = 'red';
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
            $db->query($db->prepare($sql, ($order + 1), $this->folder)); 
        }

        $hotaru->messages[$lang['admin_plugins_order_updated']] = 'green';

        // Resort all orders and remove any accidental gaps
        $plugins->refreshPluginOrder();
        $plugins->sortPluginHooks();

        return true;

    }
    
    
    /**
     * Get a plugin's actual name from its folder name
     *
     * @param string $folder plugin folder name
     * @return string
     */
    function pluginName($folder = "")
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
    function pluginActive($folder = "")
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
     * Get the value for a given plugin and setting
     *
     * @param string $folder name of plugin folder
     * @param string $setting name of the setting to retrieve
     * @return string|false
     *
     * Notes: If there are multiple settings with the same name,
     * this will only get the first.
     */
    function pluginSettings($folder = '', $setting = '') {
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
    function pluginSettingsArray($folder = '') {
        global $db;
        
        $sql = "SELECT plugin_setting, plugin_value FROM " . TABLE_PLUGINSETTINGS . " WHERE (plugin_folder = %s)";
        $results = $db->get_results($db->prepare($sql, $folder));
        
        if ($results) { return $results; } else { return false; }
    }
    
    
    /**
     * Get and unserialize serialized settings
     *
     * @return array - of submit settings
     */
    function getSerializedSettings($folder = '')
    {
        global $plugins;
        
        // Set default to the current plugin if not specified
        if(!$folder) { $folder = $this->folder; }
        
        // Get settings from the database if they exist...
        $settings = unserialize($this->pluginSettings($folder, $folder . '_settings'));
        return $settings;
    }
    
    
    /**
     * Determine if a plugin setting already exists
     *
     * @param string $folder name of plugin folder
     * @param string $setting name of the setting to retrieve
     * @return string|false
     */
    function pluginSettingExists($folder = '', $setting = '') {
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
    function pluginSettingsUpdate($folder = '', $setting = '', $value = '')
    {
        global $db, $current_user;
        
        $exists = $this->pluginSettingExists($folder, $setting);
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
     * Deletes rows from pluginsettings that match a given plugin
     *
     * @param string $folder name of plugin folder
     */
    function pluginSettingsRemovePlugin($folder = '')
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
    function includeLanguage($folder = '', $filename = '')
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
    function findCSSFile($folder = '', $filename = '')
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
    function findJSFile($folder = '', $filename = '')
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
     function includeCSS($plugin = '', $filename = '')
     {
        // If no filename provided, the filename is assigned the plugin name.
        if (!$filename) { $filename = $plugin; }
        
        $file_location = $this->findCSSFile($plugin, $filename);
        
        // Add this css file to the global array of css_files
        array_push($this->getIncludeCSS(), $file_location);
     }


    /**
     * Build an array of JavaScript files to combine
     *
     * @param $plugin - the folder name of the plugin
     * @param $filename - optional js file without an extension
     */
     function includeJS($plugin = '', $filename = '')
     {
        // If no filename provided, the filename is assigned the plugin name.
        if (!$filename) { $filename = $plugin; }
        
        $file_location = $this->findJSFile($plugin, $filename);
        
        // Add this css file to the global array of css_files
        array_push($this->getIncludeJS(), $file_location);
     }
}

?>