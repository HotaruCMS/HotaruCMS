<?php
/**
 * The Base Plugin class. It contains member data and default methods for many
 * of the plugin hooks. That means, plugins wishing to use the default behavior
 * just have to include the hook at the top of the plugin file, but need not
 * write a function for it. Hotaru will use these defaults instead. Of course, 
 * by writing a function for the hook, you can override these defaults.
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

class Plugin
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
    private $settings;                    // instance of PluginSettings object

     /* ******************************************************************** 
     * 
     * ****************************************************************** */
     

    /**
     * Constructor - make a PluginAcess object
     */
    public function __construct($folder = '')
    {
        // We don't need to fill the object with anything other than the plugin folder name at this time:
        if ($folder) { 
            $this->setFolder($folder); 
        }
    }
    
    
    /* *************************************************************
     *                      ACCESSOR METHODS
     * ********************************************************** */
     
     
    /**
     * Set plugin name
     *
     * @param string $name
     */    
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get plugin name
     *
     * @return string
     */    
    public function getName()
    {
        return $this->name;
    }
    

    /**
     * Set plugin folder
     *
     * @param string $folder
     */    
    public function setFolder($folder)
    {
        $this->folder = $folder;
    }


    /**
     * Get plugin folder
     *
     * @return string
     */    
    public function getFolder()
    {
        return $this->folder;
    }
    
    
    /**
     * Set plugin class
     *
     * @param string $class
     */    
    public function setClass($class)
    {
        $this->class = $class;
    }


    /**
     * Get plugin class
     *
     * @return string
     */    
    public function getClass()
    {
        return $this->class;
    }
        
        
    /**
     * Get plugin name length
     *
     * @return int
     */    
    public function nameLength()
    {
        return strlen($this->name);
    }
    
    

    /* *************************************************************
     *              DEFAULT PLUGIN HOOK ACTIONS
     * ********************************************************** */
     
    /**
     * Include language file if available
     */
    public function hotaru_header()
    {
        global $plugins;

        $plugins->includeLanguage('', $this->getFolder());
    }
     
     
    /**
     * Include All CSS and JavaScript files for this plugin
     */
    public function header_include()
    {
        global $hotaru, $admin, $cage;
        
        // include a files that match the name of the plugin folder:
        $hotaru->includeJs('', $this->getFolder()); // filename, folder name
        $hotaru->includeCss('', $this->getFolder());
    }
    
    
    /**
     * Include All CSS and JavaScript files for this plugin in Admin
     */
    public function admin_header_include()
    {
        global $plugins, $admin, $cage;

        $this->header_include();
    }
    
    /**
     * Include code as a template before the closing </body> tag
     */
    public function pre_close_body()
    {
        global $hotaru;
        
        $hotaru->displayTemplate($this->getFolder() . '_footer', $this->getFolder());
    }
    

    /**
     * Display Admin sidebar link
     */
    public function admin_sidebar_plugin_settings()
    {
        echo "<li><a href='" . url(array('page'=>'plugin_settings', 'plugin'=>$this->getFolder()), 'admin') . "'>" . make_name($this->getFolder()) . "</a></li>";
    }
    
    
    /**
     * Display Admin settings page
     *
     * @return true
     */
    public function admin_plugin_settings()
    {
        // This requires there to be a file in the plugin folder called pluginname_settings.php
        // The file must contain a class titled PluginNameSettings
        // The class must have a method called "settings".
        if (file_exists(PLUGINS . $this->getFolder() . '/' . $this->getFolder() . '_settings.php')) {
            include_once(PLUGINS . $this->getFolder() . '/' . $this->getFolder() . '_settings.php');
        }
        
        $settings_class = make_name($this->getFolder(), '') . 'Settings'; // e.g. CategoriesSettings
        $settings_object = new $settings_class();
        $settings_object->settings($this->getFolder());   // call the settings function
        return true;
    }

}

?>