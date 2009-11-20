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
    protected $folder       = '';         // plugin folder name
    protected $class        = '';         // plugin class name
    protected $desc         = '';         // plugin description
    protected $version      = 0;          // plugin version number
    protected $order        = 0;          // plugin order number
    protected $requires     = '';         // string of plugin->version pairs
    protected $dependencies = array();    // array of plugin->version pairs
    protected $hooks        = array();    // array of plugin hooks
    protected $pluginBasics = array();    // holds basic plugin details
    
    public $db;                             // database object
    public $cage;                           // Inspekt object
    public $hotaru;                         // Hotaru object
    public $lang            = array();      // stores language file content
    public $current_user;                   // UserBase object

     /* ******************************************************************** 
     * 
     * ****************************************************************** */
     

    /**
     * Constructor - make a Plugin object
     */
    public function __construct($folder = '', $hotaru)
    {
        // We don't need to fill the object with anything other than the plugin folder name at this time:
        if ($folder) { 
            $this->folder = $folder; 
        }

        $this->hotaru           = $hotaru;
        $this->db               = $hotaru->db;
        $this->cage             = $hotaru->cage;
        $this->lang             = &$hotaru->lang;    // reference to main lang array
        $this->current_user     = $hotaru->current_user;
    }
    

    /**
     * Access modifier to set protected properties
     */
    public function __set($var, $val)
    {
        $this->$var = $val;  
    }
    
    
    /**
     * Access modifier to get protected properties
     */
    public function __get($var)
    {
        return $this->$var;
    }


    /* *************************************************************
     *              UNIQUE ACCESS MODIFIERS
     * ********************************************************** */
     
    
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
    public function install_plugin()
    {
        $this->includeLanguage('', $this->folder);
    }
    
    
    /**
     * Include language file if available
     */
    public function hotaru_header()
    {
        $this->includeLanguage('', $this->folder);
    }
     
     
    /**
     * Include All CSS and JavaScript files for this plugin
     */
    public function header_include()
    {
        // include a files that match the name of the plugin folder:
        $this->hotaru->includeJs('', $this->folder); // filename, folder name
        $this->hotaru->includeCss('', $this->folder);
    }
    
    
    /**
     * Include All CSS and JavaScript files for this plugin in Admin
     */
    public function admin_header_include()
    {
        // include a files that match the name of the plugin folder:
        $this->hotaru->includeJs('', $this->folder, true); // filename, folder name, admin
        $this->hotaru->includeCss('', $this->folder, true);
    }
    
    /**
     * Include code as a template before the closing </body> tag
     */
    public function pre_close_body()
    {
        $this->hotaru->displayTemplate($this->folder . '_footer', $this->folder);
    }
    

    /**
     * Display Admin sidebar link
     */
    public function admin_sidebar_plugin_settings()
    {
        echo "<li><a href='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=" . $this->folder . "'>" . make_name($this->folder) . "</a></li>";
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
        if (file_exists(PLUGINS . $this->folder . '/' . $this->folder . '_settings.php')) {
            include_once(PLUGINS . $this->folder . '/' . $this->folder . '_settings.php');
        }
        
        $settings_class = make_name($this->folder, '') . 'Settings'; // e.g. CategoriesSettings
        $settings_object = new $settings_class($this->folder, $this->hotaru);
        $settings_object->settings();   // call the settings function
        return true;
    }

}

?>