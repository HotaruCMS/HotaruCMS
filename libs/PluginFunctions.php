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
namespace Libs;

class PluginFunctions extends Prefab
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
		$metaReader = new \generic_pmd();
		$plugin_metadata = $metaReader->read(PLUGINS . $folder . '/' . $folder . '.php');
                if (!$plugin_metadata) { 
                    $h->showMessage('File "' . $folder . '/' . $folder . '.php"' . ' could not be found', 'red');
                    return false;
                }
		
		return $plugin_metadata;
	}
        
        
	/**
	 * Look for and run actions at a given plugin hook
         * This function runs many times on each page due to the number of hooks
         * Improving this function helps page speed.
         * 
	 *
	 * @param string $hook name of the plugin hook
	 * @param string $folder name of plugin folder
	 * @param array $parameters mixed values passed from plugin hook
	 * @return array | bool
	 */
	public function pluginHook($h, $hook = '', $folder = '', $parameters = array(), $exclude = array())
	{
                //print "******** here for : " . $hook . ' --- in folder: ' .$folder . '  *********<Br/>';
		if (!$hook) { return false; }
		
                // if list of plugins and hooks not in memory then go get them. Should only run once on page
		if (!isset($h->allPluginDetails['pluginData']) || ($hook == 'install_plugin')) { 
			$this->getAllPluginDetails($h); // get from database (false to disable cache)
		}
		
                // no plugin data at all
		if (!isset($h->allPluginDetails['hooks'])) { return false; }
		
                // no plugins for this hook
                if (!isset($h->allPluginDetails['hookdata'][$hook])) { return false; }
                
                // check if we already have the plugindetails in memory for this page
                $inMemory = isset($h->allPluginDetails['plugins'][$hook]) ? true : false;
		
		$valid_plugins = array();
                // transform the index list of plugins that use this hook into a plain array
                // $h->allPluginDetails['hookdata'][$hook] is the list of plugins that use this hook
		foreach (array_keys($h->allPluginDetails['hookdata'][$hook]) as $folderName) {
			$valid_plugins[] = $folderName; 
                        if (!$inMemory) {
                            if (isset($h->allPluginDetails['pluginData'][$folderName])) {
                                // testing for isset here to avoid error when we uninstall a plugin from admin dashboard and hotaru thinks the hook still exists
                                $h->allPluginDetails['plugins'][$hook][] = $h->allPluginDetails['pluginData'][$folderName];
                            }
                        }
                }
                //print_r($h->allPluginDetails['plugins'][$hook]);
                //print "=========<br/>";
                // TODO review whether array_keys is performing better or not. Feels like foreach loops are faster
                
                // only used when we are targeting a specific folder - not often
		if ($folder) {
                        //print "targeting one plugin ============== " . $folder . '<br/>';
			if (!in_array($folder, $valid_plugins)) {
                            // targeted plugin doesn't use this hook
                            return false;
                        }
                        $valid_plugins = array($folder); // replace list of valid plugins with just the one we're targeting.
		}
                //print_r($valid_plugins);
                // TODO we should only get hooks that are relevent to this page
                //print "count for " . $hook . ": " . count($valid_plugins) . "<br/>";
               
                // get plugin details from memory for this hook and check against valid plugins filtered above
		$plugins = array();
		foreach ($h->allPluginDetails['plugins'][$hook] as $item => $key) {
			if (!isset($key->plugin_folder)) { continue; }
			if (in_array($key->plugin_folder, $valid_plugins)) {
				array_push($plugins, $key);
			}
		}
		
		if (!$plugins) { return false; } // no matching plugins in allPluginDetails
		
		// chain of plugin folder names so we can revert to the parent one when plugins hook into other plugins.
		if (!isset($h->vars['plugin_chain'])) { $h->vars['plugin_chain'] = array(); }
		
                //print "start the hook -> " . $hook . " loop @@@@@@@<br/>";
		foreach ($plugins as $plugin)
		{
                        //print "hook -> " . $hook . " with " . $plugin->plugin_folder . "   extends -> " . $plugin->plugin_extends .  "<br/>";
                        
                        // if the plugin isn't active, skip this iteration
                        // Should not be a problem beacuse query only got enabled ones but leave in just in case
			if (!$plugin->plugin_enabled) { continue; } 
                        
                        // TODO test this is working properly
                        if ($exclude && in_array($plugin->plugin_folder, $exclude)) { continue; } 
			
                        // Skip if file not found. Should we give an error message as well?
                        $pluginFile = PLUGINS . $plugin->plugin_folder . "/" . $plugin->plugin_folder . ".php";
                        
                        // build list of plugins with file exists checked, so we dont have to keep hitting file_exists every time for same plugins
//                        if ($h->isTest) { timer_start('pluginList'); }
//                        if(!isset($h->vars['plugin_path_list'][$folder])) {
//                            if (!file_exists($pluginFile)) { continue; }
//                            $h->vars['plugin_path_list'][$pluginFile] = 1;
//                        }
//                        if ($h->isTest) { print timer_stop(7, 'pluginList') . '<br/>'; }
                        // timetests averaging 0.0000091, 0.0000100, 0.0000069 - Sep 23, 2014
                        
                        if ($h->isTest) { timer_start('pluginFile'); }
                        if (!file_exists($pluginFile))  { continue; }
                        if ($h->isTest) { print timer_stop(7, 'pluginFile') . '<br/>'; }
                        // timetests averaging 0.0000029, 0.0000041, 0.0000038 - Sep 23, 2014
			
                        // TODO come back and review this
                        // Child plugins
                        /*  loop through all the plugins that use this hook. Include any necessary parent classes
                            and skip to the next iteration if this class has children. */
                        foreach ($plugins as $key => $value) {
                                //print $value->plugin_folder . '*****<Br/>';
                                // If this plugin class is a child, include the parent class
                                if ($value->plugin_class == $plugin->plugin_extends) {
                                        include_once($pluginFile);
                                        $h->includeLanguage($value->plugin_folder); // include the language for the parent plugin
                                }

                                // If this plugin class has children, skip it because we will use the children instead
                                if ($value->plugin_extends == $plugin->plugin_class) { 
                                        continue 2; // skip to next iteration of outer foreach loop
                                }
                        }
			
                        // include this plugin's file (even child classes need the parent class)
                        include_once($pluginFile);

                        // find the class
                        //print "look for class: " . $plugin->plugin_class . '<br/>';
                        if (class_exists($plugin->plugin_class)) {
                            //print "class exists:<br/>"; 
                            
                            // call the method that matches this hook
                            $result = $this->callMethodFromPlugin($h, $plugin, $hook, $parameters);
//print "result: "; print_r($result) . '<br/>';
                            if ($result) {
                                    // allow a plugin to halt execution of remaining functions for this hook
                                    if (is_string($result) && ($result == "skip")) { return false; }

                                    // otherwise add to return array...
                                    $return_array[$plugin->plugin_class . "_" . $hook] = $result; // name the result Class + hook name
                            }
                        } else {                                    
                            $h->messages['Plugin class could not be found'] = "alert-danger";
                            return false;
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
	
        
        private function callMethodFromPlugin($h, $plugin, $method, $parameters)
        {
                $tempPluginObject = new $plugin->plugin_class($h);      // create a temporary object of the plugin class
                $h->plugin->folder = $plugin->plugin_folder;            // assign plugin folder to $h
                $h->vars['plugin_chain'][] = $h->plugin->folder;        // add this plugin to the chain...  

                if (method_exists($tempPluginObject, $method)) {
                        // give Hotaru the right plugin folder name    (data not available when installing so skip in that case)
                        if ($method != 'install_plugin') {
                            $rClass = new \ReflectionClass($plugin->plugin_class);
                            $rMethod = $rClass->getMethod($method);
                            //echo '-------- method exists: ' . $rMethod->class . '<br/>';                // the method's class
                            //echo get_class($tempPluginObject) . '<br/>';              // the object's class

                            // set the folder by looking up the class in the pluginslist
                            $h->getPluginFolderFromClass($rMethod->class); 
                        }
                        $h->readPlugin();                              // fill Hotaru's plugin properties
                        $h->includeLanguage();                         // if a language file exists, include it
                        
                        $result = $tempPluginObject->$method($h, $parameters);
                } else {
                        //print "method didnt exist. read plugin from readPlugin<br/>";
                        $h->readPlugin();                              // fill Hotaru's plugin properties
                        $h->includeLanguage();                         // if a language file exists, include it										
                        
                        // fall back on default function in Hotaru.php
                        if (method_exists($h, $method)) {
                            $result = $h->$method($parameters);              
                        } else {	
                            if ($h->currentUser->getPermission('can_access_admin') == 'yes') { $h->showMessage('Could not find ' . $method  .  ' function for ' . $plugin->plugin_folder, 'red'); }
                            $result ='';					   
                        }
                }
                
                return $result;
        }
	
	/**
	 * Get number of active plugins
	 *
	 * @return int|false
	 */
	public function numActivePlugins($h)
	{
                //$enabled = \Hotaru\Models\Plugin::countEnabled();
                $enabled = \Hotaru\Models2\Plugin::countEnabled($h);
                
                //TODO
                // we could cache this off and increment it on plugin activtated etc
                
                if ($enabled > 0) { return $enabled; } else { return false; }
	}
	
	
	/**
	 * Get a plugin's folder from its class name
	 *
	 * @param string $class plugin class name
	 * @return string|false
	 */
	public function getPluginFolderFromClass($h, $class = "")
	{
		if (!isset($h->allPluginDetails['pluginFolderIndexOnClass'])) { //not in memory
			$this->getAllPluginDetails($h); // get from database
		}
		
		if (!$h->allPluginDetails['pluginFolderIndexOnClass']) { 
			return false; // no plugin deatils for this plugin found anywhere
		}
                
                if (isset($h->allPluginDetails['pluginFolderIndexOnClass'][$class])) {
                    return $h->allPluginDetails['pluginFolderIndexOnClass'][$class];
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
                //print "here for folder : " . $folder . ' on propery: ' . $property . ' or field: ' . $field;
		if (!$folder) { $folder = $h->plugin->folder; } 
		
		if (!isset($h->allPluginDetails['pluginData'])) { //not in memory
			$this->getAllPluginDetails($h); // get from database
		}
		
		if (!$h->allPluginDetails['pluginData']) { 
			return false; // no plugin basics for this plugin found anywhere
		}
		
		if ($field) {
                        $property = 'plugin_' . $field;
		}
		
		if (isset($h->allPluginDetails['pluginData'][$folder])) {
                    return $h->allPluginDetails['pluginData'][$folder]->$property;
                }
                
                return false;
	}
	
	
	/**
	 * Get a single plugin's details for Hotaru
	 *
	 * @param string $folder - plugin folder name, else $h->plugin->folder is used
	 */
	public function readPlugin($h, $folder = '', $admin = false)
	{
		if (!$folder) { $folder = $h->plugin->folder; } 
		
		if (!isset($h->allPluginDetails['pluginData'])) { //not in memory
			$this->getAllPluginDetails($h); // get from database
		}
		
		if (!$h->allPluginDetails['pluginData']) { 
			return false; // no plugin basics for this plugin found anywhere
		}
		
		// get plugin basics from memory
                if (isset($h->allPluginDetails['pluginData'][$folder])) {
                    $key = $h->allPluginDetails['pluginData'][$folder];

                        $h->plugin->id             = $key->plugin_id;        // plugin id
                        $h->plugin->enabled        = $key->plugin_enabled;   // activate (1), inactive (0)
                        $h->plugin->name           = $key->plugin_name;      // plugin proper name
                        $h->plugin->folder         = $key->plugin_folder;    // plugin folder name
                        $h->plugin->class          = $key->plugin_class;     // plugin class name
                        $h->plugin->extends        = $key->plugin_extends;   // plugin class parent
                        $h->plugin->type           = $admin ? $key->plugin_type : null;      // plugin class type e.g. "avatar"
                        $h->plugin->desc           = $admin ? $key->plugin_desc : null;      // plugin description
                        $h->plugin->requires       = $admin ? $key->plugin_requires : null;  // plugins required for use
                        $h->plugin->version        = $admin ? $key->plugin_version : null;   // plugin version number
                        $h->plugin->order          = $key->plugin_order;     // plugin order number
                        $h->plugin->author         = $admin ? $key->plugin_author : null;    // plugin author
                        $h->plugin->authorurl      = $admin ? $key->plugin_authorurl : null; // plugin author's website
                        $h->plugin->latestversion  = $admin ? $key->plugin_latestversion : null; // latest available version

                        return $key;  // done what we need to do so return $key;
                }
		
		return false;
	}
	
        
        /**
         * Get list of all plugins (names only)
         * @param type $h
         */
//        public static function getAllActivePluginNames($h)
//	{
//                //$pluginNames = \Hotaru\Models\Plugin::getAllActiveNames();
//                $pluginNames = \Hotaru\Models2\Plugin::getAllActiveNames($h);
//                return $pluginNames;
//        }
        
	
	/**
	 * Store all plugin details for ALL PLUGINS info in memory. This is a single query
	 * per page load unless cached. Every thing else then draws what it needs from memory.
	 */
	public static function getAllPluginDetails($h)
	{   
                // to much overhead to call it like this and leave as object. 
                // but if we change to array and make list below as that then we have to convert all other uses to array as well
                // $pluginsRawData = \Hotaru\Models\Plugin::getAllDetails();
                // NB both active and nonactive need to be read in
                //  $sql = "SELECT * FROM " . TABLE_PLUGINS . " ORDER BY plugin_order ASC";
                //  $pluginsRawData = $h->db->get_results($sql); 

                //print "getAllPluginDetails *** <br/>*****<br/>*** <br/>*****<br/>";
                $pluginsRawData = \Hotaru\Models2\Plugin::getAllDetails($h);
                    
                $h->allPluginDetails['pluginData'] = array();
                if ($pluginsRawData) {
                    foreach ($pluginsRawData as $plugin) {
                        $h->allPluginDetails['pluginData'][$plugin->plugin_folder] = $plugin;
                        $h->allPluginDetails['pluginFolderIndexOnClass'][$plugin->plugin_class] = $plugin->plugin_folder;
                    }
                }

                
                // hooks
                //$h->allPluginDetails['hooks'] = \Hotaru\Models\Pluginhook::getAllEnabled();
                $h->allPluginDetails['hooks'] = \Hotaru\Models2\Pluginhook::getAllEnabled($h);
                
                //print_r($h->allPluginDetails['hooks']);
                // turn this into an index based array as it runs faster than later calling an array_in func
                // we are going to be using isset funcs later with this
                // http://nickology.com/2012/07/03/php-faster-array-lookup-than-using-in_array/
                
                if ($h->allPluginDetails['hooks']) {
                    foreach ($h->allPluginDetails['hooks'] as $hooks) {
                        $h->allPluginDetails['hookdata'][$hooks->plugin_hook][$hooks->plugin_folder] = 1;
                    }
                }
	}
	
	
	/**
	 * Determines if a plugin type or specific plugin is enabled or not
	 *
	 * @param object $h
	 * @param string $folder plugin type or folder name
	 * @return string
	 */
//	public function isActive($h, $type = '')
//	{       
//               //using getpluginproperty instead from $h->isActive
//	}
	
	
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
                
                if (isset($h->allPluginDetails['hookdata']['admin_plugin_settings'][$folder])) { return true; }
                
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
            if (!is_array($pluginResult)) { return false; }
                        
            if ($folder == '') {
                // no plugin name selected so lets strip off all plugin names and return values                
                reset ($pluginResult); // reset back to first point in array                            
                $result = array_values($pluginResult); // remove the plugin names first                                         
            } else {
                if (isset($pluginResult[$folder])) {
                    $result = $pluginResult[$folder];
                }
            }
            
            if (isset($result)) { return $result; } 
            
            return false;
        }
}
