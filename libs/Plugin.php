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

// Include the generic_pmd class that reads post metadata from the a plugin
require_once(EXTENSIONS . 'GenericPHPConfig/class.metadata.php');

class Plugin extends generic_pmd
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
    
    protected $cssIncludes    = array();  // a list of css files to include
    protected $jsIncludes     = array();  // a list of js files to include
    protected $includeType   = '';       // 'css' or 'js'

     /* ******************************************************************** 
     * 
     * ****************************************************************** */
     
    /**
     * Include language file if available
     */
    public function hotaru_header()
    {
        global $plugins;

        $plugins->includeLanguage('', $this->folder);
    }
    

    /**
     * Include All CSS and JavaScript files for this plugin
     */
    public function header_include()
    {
        global $plugins, $admin, $cage;
        
        // include a files that match the name of the plugin folder:
        $plugins->includeJs('', $this->folder); // filename, folder name
        $plugins->includeCss('', $this->folder);
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
        
        $hotaru->displayTemplate($this->folder . '_footer', $this->folder);
    }
    

     /* ******************************************************************** 
     * ********************************************************************* 
     * ************************ METHODS FOR ADMIN ************************** 
     * *********************************************************************
     * ****************************************************************** */
    
    /**
     * Display Admin sidebar link
     */
    public function admin_sidebar_plugin_settings()
    {
        echo "<li><a href='" . url(array('page'=>'plugin_settings', 'plugin'=>$this->folder), 'admin') . "'>" . $this->name . "</a></li>";
    }
    
    
    /**
     * Display Admin settings page
     *
     * @return true
     */
    public function admin_plugin_settings()
    {
        if (file_exists(PLUGINS . $this->folder . '/' . $this->folder . '_settings.php')) {
            include_once(PLUGINS . $this->folder . '/' . $this->folder . '_settings.php');
        }
        
        $settings_function = $this->folder . '_settings';
        $settings_function();   // call the settings function
        return true;
    }
    
}

?>