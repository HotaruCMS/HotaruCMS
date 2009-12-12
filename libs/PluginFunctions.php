<?php
/**
 * Plugin Functions
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
class PluginFunctions
{
    /**
     * Look for and run actions at a given plugin hook
     *
     * @param string $hook name of the plugin hook
     * @param string $folder name of plugin folder
     * @param array $parameters mixed values passed from plugin hook
     * @return array | bool
     */
    public function pluginHook($hotaru, $hook = '', $folder = '', $parameters = array(), $exclude = array())
    {
        if (!$hook) { return false; }
        
        $where = '';
        
        if ($folder) {
            $where .= "AND (" . TABLE_PLUGINS . ".plugin_folder = %s) ";
        }

        $hotaru->db->cache_queries = true;    // start using cache

        $sql = "SELECT " . TABLE_PLUGINS . ".plugin_enabled, " . TABLE_PLUGINS . ".plugin_folder, " . TABLE_PLUGINS . ".plugin_class, " . TABLE_PLUGINHOOKS . ".plugin_hook  FROM " . TABLE_PLUGINHOOKS . ", " . TABLE_PLUGINS . " WHERE (" . TABLE_PLUGINHOOKS . ".plugin_hook = %s) AND (" . TABLE_PLUGINS . ".plugin_folder = " . TABLE_PLUGINHOOKS . ".plugin_folder) " . $where . "ORDER BY " . TABLE_PLUGINHOOKS . ".phook_id";

        $plugins = $hotaru->db->get_results($hotaru->db->prepare($sql, $hook, $folder));

        $hotaru->db->cache_queries = false;    // stop using cache

        if (!$plugins) { return false; }

        foreach ($plugins as $plugin)
        {
            if ($plugin->plugin_folder &&  $plugin->plugin_hook &&  ($plugin->plugin_enabled == 1)
                && !in_array($plugin->plugin_folder, $exclude)) 
            {

                if (!file_exists(PLUGINS . $plugin->plugin_folder . "/" . $plugin->plugin_folder . ".php"))  { continue; }

                include_once(PLUGINS . $plugin->plugin_folder . "/" . $plugin->plugin_folder . ".php");
                
                // create a temporary object of the plugin class
                $this_plugin = new $plugin->plugin_class($this->hotaru, $plugin->plugin_folder);
                
                $hotaru->folder = $plugin->plugin_folder; // so we know the current plugin
                
                // call the method that matches this hook
                $result = $this_plugin->$hook($parameters);
                if ($result) {
                    $return_array[$plugin->plugin_class . "_" . $hook] = $result; // name the result Class + hook name
                }
            }
        }

        if (!empty($return_array))
        {
            // return an array of return values from each function, 
            // e.g. $return_array['usr_users'] = something
            return $return_array;
        } 

        return false;
    }
    
    
    /**
     * Get number of active plugins
     *
     * @return int|false
     */
    public function numActivePlugins($db)
    {
        $enabled = $db->get_var($db->prepare("SELECT count(*) FROM " . TABLE_PLUGINS . " WHERE plugin_enabled = %d", 1));
        if ($enabled > 0) { return $enabled; } else { return false; }
    }
    
    
    /**
     * Get version number of plugin if active
     *
     * @param string $folder plugin folder name
     * @return string|false
     */
    public function isActive($hotaru, $folder = "")
    {
        if (!$folder) { $folder = $hotaru->folder; } 
        
        if (!$hotaru->pluginBasics) { //not in memory
            $hotaru->getPluginBasics(); // get from database
        }

        if (!$hotaru->pluginBasics) { 
            return false; // no plugin basics for this plugin found anywhere
        }
        
        // get plugin basics from memory
        foreach ($hotaru->pluginBasics as $item => $key) {
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
     * Store basic plugin info in memory. We use the hotaru 
     * object because it's persistent during a page load
     */
    public function getPluginBasics($db)
    {
        $sql = "SELECT plugin_enabled, plugin_name, plugin_folder, plugin_class, plugin_version FROM " . TABLE_PLUGINS;
        return $db->get_results($db->prepare($sql));
    }
    
    
    /**
     * Determines if a plugin is enabled or not
     *
     * @param object $hotaru
     * @param string $folder plugin folder name
     * @return string
     */
    public function getPluginStatus($hotaru, $folder = '')
    {
        if (!$folder) { $folder = $hotaru->folder; } 
        
        $sql = "SELECT * FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s";
        $plugin_row = $hotaru->db->get_row($hotaru->db->prepare($sql, $folder));
        
        if ($plugin_row && $plugin_row->plugin_enabled == 1) {
            $status = "active";
        } else {
            $status = "inactive";
        } 

        return $status;
    }
}
?>