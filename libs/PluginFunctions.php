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
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
class PluginFunctions
{
        /**
	 * Read and return plugin info from top of a plugin file.
	 *
	 * @param string $plugin_file - a file from the /plugins folder 
	 * @return array|false
	 */
	public function readPluginMeta($h, $folder = '')
	{
		if (!$folder) { $folder = rtrim(PLUGINS, '/'); }
		
		// Include the generic_pmd class that reads post metadata from the a plugin
		require_once(EXTENSIONS . 'GenericPHPConfig/class.metadata.php');
		$metaReader = new generic_pmd();
		$plugin_metadata = $metaReader->read(PLUGINS . $folder . '/' . $folder . '.php');
                if (!$plugin_metadata) $h->showMessage('File "' . $folder . '/' . $folder . '.php"' . ' could not be found', 'red');
		
		if ($plugin_metadata) { return $plugin_metadata; } else { return false; }
	}
        
        
	/**
	 * Look for and run actions at a given plugin hook
	 *
	 * @param string $hook name of the plugin hook
	 * @param string $folder name of plugin folder
	 * @param array $parameters mixed values passed from plugin hook
	 * @return array | bool
	 */
	public function pluginHook($h, $hook = '', $folder = '', $parameters = array(), $exclude = array())
	{
		if (!$hook) { return false; }
		
		if (!$h->allPluginDetails || ($hook == 'install_plugin')) { //not in memory
			$this->getAllPluginDetails($h); // get from database (false to disable cache)
		}
		
		if (!isset($h->allPluginDetails['hooks'])) { return false; }
		
		// get the plugins that use this hook
		$valid_plugins = array();
		foreach ($h->allPluginDetails['hooks'] as $hooks) {
			if ($hooks->plugin_hook == $hook) { array_push($valid_plugins, $hooks->plugin_folder); }
		}
		
		if (!$valid_plugins) { return false; } // no plugins use this hook

		if ($folder) {
			if (!in_array($folder, $valid_plugins)) { return false; } // targeted plugin doesn't use this hook			
                        $valid_plugins = array($folder); // replace list of valid plugins with just the one we're targeting.
		}

		// get plugin details from memory
		$plugins = array();
		foreach ($h->allPluginDetails as $item => $key) {
			if (!isset($key->plugin_folder)) { continue; }
			if (in_array($key->plugin_folder, $valid_plugins)) {
				array_push($plugins, $key);
			}
		}
		
		if (!$plugins) { return false; } // no matching plugins in allPluginDetails
		
		// chain of plugin folder names so we can revert to the parent one when plugins hook into other plugins.
		if (!isset($h->vars['plugin_chain'])) { $h->vars['plugin_chain'] = array(); }
		
		foreach ($plugins as $plugin)
		{
			if (!$plugin->plugin_enabled) { continue; } // if the plugin isn't active, skip this iteration
			
			if ($plugin->plugin_folder && ($plugin->plugin_enabled == 1)
				&& !in_array($plugin->plugin_folder, $exclude)) 
			{
			
				if (!file_exists(PLUGINS . $plugin->plugin_folder . "/" . $plugin->plugin_folder . ".php"))  { continue; }
			
				/*  loop through all the plugins that use this hook. Include any necessary parent classes
					and skip to the next iteration if this class has children. */
				foreach ($plugins as $key => $value) {
					// If this plugin class is a child, include the parent class
					if ($value->plugin_enabled && $value->plugin_class == $plugin->plugin_extends) {
						include_once(PLUGINS . $value->plugin_folder . "/" . $value->plugin_folder . ".php");
						$h->includeLanguage($value->plugin_folder); // include the language for the parent plugin
					}
					
					// If this plugin class has children, skip it because we will use the children instead
					if ($value->plugin_enabled && $value->plugin_extends == $plugin->plugin_class) { 
						continue 2; // skip to next iteration of outer foreach loop
					}
				}
			
				// include this plugin's file (even child classes need the parent class)
				include_once(PLUGINS . $plugin->plugin_folder . "/" . $plugin->plugin_folder . ".php");
				
                                if (class_exists($plugin->plugin_class)) {
                                    $tempPluginObject = new $plugin->plugin_class($h);        // create a temporary object of the plugin class
                                    $h->plugin->folder = $plugin->plugin_folder;     // assign plugin folder to $h

                                    // add this plugin to the chain...
                                    array_push($h->vars['plugin_chain'], $h->plugin->folder);                                
                                
                                
                                    // call the method that matches this hook
                                    if (method_exists($tempPluginObject, $hook)) {
                                            $rClass = new ReflectionClass($plugin->plugin_class);
                                            $rMethod = $rClass->getMethod($hook);
                                            // echo $rMethod->class;                            // the method's class
                                            // echo get_class($tempPluginObject);               // the object's class
                                            // give Hotaru the right plugin folder name (unless installing because data not yet avaialble)
                                            if ($hook != 'install_plugin') { $h->getPluginFolderFromClass($rMethod->class); }
                                            $h->readPlugin();                              // fill Hotaru's plugin properties
                                            $h->includeLanguage();                         // if a language file exists, include it
                                            $result = $tempPluginObject->$hook($h, $parameters);
                                    } else {
                                            $h->readPlugin();                              // fill Hotaru's plugin properties
                                            $h->includeLanguage();                         // if a language file exists, include it										
                                            if (method_exists($h, $hook)) {
                                                $result = $h->$hook($parameters);              // fall back on default function in Hotaru.php
                                            } else {	
                                                if ($h->currentUser->getPermission('can_access_admin') == 'yes') { $h->showMessage('Could not find ' . $hook  .  ' function for ' . $plugin->plugin_folder, 'red'); }
                                                $result ='';					   
                                            }
                                    }
				
                                    if ($result) {
                                            // allow a plugin to halt execution of remaining functions for this hook
                                            if (is_string($result) && ($result == "skip")) { return false; }

                                            // otherwise add to return array...
                                            $return_array[$plugin->plugin_class . "_" . $hook] = $result; // name the result Class + hook name
                                    }
                                } else {                                    
                                    $h->messages['Plugin class could not be found'] = "alert-error";
                                    return false;
                                }
			}
		
			// finished with this hook so remove this plugin from the chain and revert $h->plugin->folder to the previous one:
			array_pop($h->vars['plugin_chain']);
			$h->plugin->folder = end($h->vars['plugin_chain']);
		}
		
		if (isset($return_array))
		{
			// return an array of return values from each function, 
			// e.g. $return_array['ClassName_method_name'] = something
			return $return_array;
		}
		return false;
	}
	
	
	/**
	 * Get number of active plugins
	 *
	 * @return int|false
	 */
	public function numActivePlugins($h)
	{
                if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50300 || !ACTIVERECORD) {
                    $enabled = $h->db->get_var($h->db->prepare("SELECT count(plugin_id) FROM " . TABLE_PLUGINS . " WHERE plugin_enabled = %d", 1));   
                } else {
                    $enabled = models___Plugins::count_by_plugin_enabled(1);
                }
                
                if ($enabled > 0) { return $enabled; } else { return false; }
	}
	
	
	/**
	 * Get a plugin's folder from its class name
	 *
	 * This is called from the pluginHook function. It looks like overkill, but all the details
	 * get stored in memory and are used by other functions via readPost() below.
	 *
	 * @param string $class plugin class name
	 * @return string|false
	 */
	public function getPluginFolderFromClass($h, $class = "")
	{
		if (!$h->allPluginDetails) { //not in memory
			$this->getAllPluginDetails($h); // get from database
		}
		
		if (!$h->allPluginDetails) { 
			return false; // no plugin deatils for this plugin found anywhere
		}
		
		// get plugin details from memory
		foreach ($h->allPluginDetails as $item => $key) {
			if ($key->plugin_class == $class) {
				return $key->plugin_folder;
			}
		}
		
		return false;
	}
	
	
	/**
	 * Get a single property from a specified plugin
	 *
	 * @param string $property - plugin property, e.g. "plugin_version"
	 * @param string $folder - plugin folder name, else $h->plugin->folder is used
	 * @param string $field - an alternative field to use instead of $folder (no "plugin_" prefix)
	 */
	public function getPluginProperty($h, $property = '', $folder = '', $field = '')
	{
		if (!$folder) { $folder = $h->plugin->folder; } 
		
		if (!$h->allPluginDetails) { //not in memory
			$this->getAllPluginDetails($h); // get from database
		}
		
		if (!$h->allPluginDetails) { 
			return false; // no plugin basics for this plugin found anywhere
		}
		
		if (!$field)
		{
			// get plugin basics from memory
			foreach ($h->allPluginDetails as $item => $key) {
				if (!isset($key->plugin_folder)) { continue; }
				if ($key->plugin_folder == $folder) {
					return $key->$property;        // plugin property, e.g. "plugin_version"
				}
			}
		}
		else
		{
			$field = 'plugin_' . $field;
			
			// get plugin basics from memory
			foreach ($h->allPluginDetails as $item => $key) {
				if (!isset($key->$field)) { continue; }
				if ($key->$field == $folder) {
					return $key->$property;        // plugin property, e.g. "plugin_version"
				}
			}
		}
		
		return false;
	}
	
	
	/**
	 * Get a single plugin's details for Hotaru
	 *
	 * @param string $folder - plugin folder name, else $h->plugin->folder is used
	 */
	public function readPlugin($h, $folder = '')
	{
		if (!$folder) { $folder = $h->plugin->folder; } 
		
		if (!$h->allPluginDetails) { //not in memory
			$this->getAllPluginDetails($h); // get from database
		}
		
		if (!$h->allPluginDetails) { 
			return false; // no plugin basics for this plugin found anywhere
		}
		
		// get plugin basics from memory
		foreach ($h->allPluginDetails as $item => $key) {
			if (isset($key->plugin_folder) && ($key->plugin_folder == $folder)) {
				$h->plugin->id             = $key->plugin_id;        // plugin id
				$h->plugin->enabled        = $key->plugin_enabled;   // activate (1), inactive (0)
				$h->plugin->name           = $key->plugin_name;      // plugin proper name
				$h->plugin->folder         = $key->plugin_folder;    // plugin folder name
				$h->plugin->class          = $key->plugin_class;     // plugin class name
				$h->plugin->extends        = $key->plugin_extends;   // plugin class parent
				$h->plugin->type           = $key->plugin_type;      // plugin class type e.g. "avatar"
				$h->plugin->desc           = $key->plugin_desc;      // plugin description
				$h->plugin->requires       = $key->plugin_requires;  // plugins required for use
				$h->plugin->version        = $key->plugin_version;   // plugin version number
				$h->plugin->order          = $key->plugin_order;     // plugin order number
				$h->plugin->author         = $key->plugin_author;    // plugin author
				$h->plugin->authorurl      = $key->plugin_authorurl; // plugin author's website
				$h->plugin->latestversion  = $key->plugin_latestversion; // latest available version
				
				return $key;  // done what we need to do so return $key (it may be handy);
			}
		}
		
		return false;
	}
	
        
        /**
         * Get list of all plugins (names only)
         * @param type $h
         */
        public static function getAllActivePluginNames($h)
	{
                if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50300 || !ACTIVERECORD) {
                    $sql = "SELECT plugin_name, plugin_folder FROM " . TABLE_PLUGINS . " WHERE plugin_enabled = 1 ORDER BY plugin_name ASC";
                    $h->smartCache('on', 'plugins', $h->db->cache_timeout, $sql); // start using cache
                    $pluginNames = $h->db->get_results($sql);

                    $h->smartCache('off');   // stop using cache                     
                } else {
                    $pluginNames = models___Plugins::all(array(
                        'select' => 'plugin_name, plugin_folder',
                        'conditions' => array('plugin_enabled = ?', 1),
                        'order' => 'plugin_name asc'
                    )); 
                }
                
                return $pluginNames;
        }
        
	
	/**
	 * Store all plugin details for ALL PLUGINS info in memory. This is a single query
	 * per page load unless cached. Every thing else then draws what it needs from memory.
	 */
	public static function getAllPluginDetails($h)
	{
                if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50300 || !ACTIVERECORD) {           
                    $sql = "SELECT * FROM " . TABLE_PLUGINS . " ORDER BY plugin_order ASC";
                    $h->smartCache('on', 'plugins', $h->db->cache_timeout, $sql); // start using cache
                    $h->allPluginDetails = $h->db->get_results($sql);

                    $sql = "SELECT plugin_folder, plugin_hook FROM " . TABLE_PLUGINHOOKS;
                    $h->smartCache('on', 'pluginhooks', $h->db->cache_timeout, $sql); // start using cache
                    $h->allPluginDetails['hooks'] = $h->db->get_results($sql);

                    $h->smartCache('off');   // stop using cache                 
                } else {
                    $h->allPluginDetails = models___Plugins::find('all',array('order'=>'plugin_order asc'));
                    $h->allPluginDetails['hooks'] = models___Pluginhooks::find('all', array('select'=> 'plugin_folder, plugin_hook'));
                }
	}
	
	
	/**
	 * Determines if a plugin type or specific plugin is enabled or not
	 *
	 * @param object $h
	 * @param string $folder plugin type or folder name
	 * @return string
	 */
	public function isActive($h, $type = '')
	{
                if (defined('PHP_VERSION_ID') && PHP_VERSION_ID < 50300 || !ACTIVERECORD) {                       
                    // first see if there's an active plugin with this *type*:
                    if ($type) {                                                 
                        $sql = "SELECT count(plugin_enabled) FROM " . TABLE_PLUGINS . " WHERE plugin_type = %s";
                        $status = $h->db->get_var($h->db->prepare($sql, $type));  

                        if (!$status) {                                                            
                            $sql = "SELECT count(plugin_enabled) FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s";
                            $status = $h->db->get_var($h->db->prepare($sql, $type));
                        }
                    } else {
			// if not $type provided, see if the *current* plugin is enabled... (which it obviously is! doh!)			                       
                        $sql = "SELECT count(plugin_enabled) FROM " . TABLE_PLUGINS . " WHERE plugin_folder = %s";
			$status = $h->db->get_var($h->db->prepare($sql, $h->plugin->folder));
                    }
                } else {
                    // first see if there's an active plugin with this *type*:
                    if ($type) { 
                        $status = models___Plugins::count_by_plugin_type($type);
                            
                        if (!$status) 
                            $status = models___Plugins::count_by_plugin_folder($type);                            
                    } else {
                        $status = models___Plugins::count_by_plugin_folder($h->plugin->folder);
                    }                    
                } 
                
		if ($status) { return true; } else { return false; }
	}
	
	
	/**
	 * Determines if a plugin has a settings page or not
	 *
	 * @param object $h
	 * @param string $folder plugin folder name (optional)
	 * @return bool
	 */
	public function hasSettings($h, $folder = '')
	{
		if (!$folder) { $folder = $h->plugin->folder; }
		
		if (!isset($h->vars['all_plugin_hooks'])) {
                    if (defined('PHP_VERSION_ID') && PHP_VERSION_ID < 50300 || !ACTIVERECORD) {  
                        $sql = "SELECT plugin_folder, plugin_hook FROM " . TABLE_PLUGINHOOKS . " WHERE plugin_hook = %s";
			$h->vars['all_plugin_hooks'] = $h->db->get_results($h->db->prepare($sql, 'admin_plugin_settings'));		
                    } else {
                        $h->vars['all_plugin_hooks'] = models___Pluginhooks::find('first', array(
                            'select' => 'plugin_folder, plugin_hook',
                            'conditions' => array('plugin_hook', 'admin_plugin_settings')                       
                        )); 
                    }
                } 

		if ($h->vars['all_plugin_hooks']) {
		    foreach ($h->vars['all_plugin_hooks'] as $item => $key) {
			if (($key->plugin_folder == $folder) && $key->plugin_hook == 'admin_plugin_settings') {
					return true;
			}
		    }
		}		
		return false;
	}
        
        
        /**
         * Get the values that have been passed to the pluginhook and return them in a simple array
         * 
         * @param type $h
         * @param type $pluginResult
         * @param type $folder
         * @return boolean
         */
        public static function getValues($h, $pluginResult = '', $folder = '')
        {          
            if (!is_array($pluginResult)) return false;
                        
            if ($folder == '') {
                // no plugin name selected so lets strip off all plugin names and return values                
                reset ($pluginResult); // reset back to first point in array                            
                $result = array_values($pluginResult); // remove the plugin names first                                         
            } else {
                if (isset($pluginResult[$folder])) {
                    $result = $pluginResult[$folder];
                }
            }
            
            if (isset($result)) return $result; else return false;
        }
}
?>
