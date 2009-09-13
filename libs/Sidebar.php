<?php
/**
 * The Sidebar class contains some useful methods when using a sidebar
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
    
class Sidebar {    

    protected $vars = array();

    /**
     * PHP __set Magic Method
     * Plugins use this to set additonal member variables
     *
     * @param str $name - the name of the member variable
     * @param mixed $value - the value to set it to.
     */
    public function __set($name, $value)
    {
        $this->vars[$name] = $value;
    }
        
        
    /**
     * PHP __get Magic Method
     * Plugins use this to read values of additonal member variables
     *
     * @param str $name - the name of the member variable
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->vars)) {
            return $this->vars[$name];
        }
    }


    /**
     * Initialize sidebar widgets
     *
     * Find which plugins have "plugin_settings" for "sidebar_widgets", give 
     * them an initial order and serialize them in "sidebar_settings"
     */
    public function initializeSidebarWidgets()
    {
        global $plugins;
        
        // Get settings from the database if they exist...
        $sidebar_settings = $this->getSidebarSettings();
            
        $sidebar_widgets = $plugins->getSettingsArray('sidebar_widgets');
        
        if ($sidebar_widgets) {
            $count = 1;
            foreach ($sidebar_widgets as $widget) {
                // Only reset order if it doesn't already exist.
                if (!isset($sidebar_settings['sidebar_widgets'][$widget->plugin_setting]['order'])) {
                    $sidebar_settings['sidebar_widgets'][$widget->plugin_setting]['order'] = $count;
                }
                // Only reset sidebar_id if it doesn't already exist.
                if (!isset($sidebar_settings['sidebar_widgets'][$widget->plugin_setting]['sidebar'])) {
                    $sidebar_settings['sidebar_widgets'][$widget->plugin_setting]['sidebar'] = 1;
                }
                // Only reset enabled if it doesn't already exist.
                if (!isset($sidebar_settings['sidebar_widgets'][$widget->plugin_setting]['enabled'])) {
                    $sidebar_settings['sidebar_widgets'][$widget->plugin_setting]['enabled'] = true;
                }
                // Only reset enabled if it doesn't already exist.
                if (!isset($sidebar_settings['sidebar_widgets'][$widget->plugin_setting]['class'])) {
                    $sidebar_settings['sidebar_widgets'][$widget->plugin_setting]['class'] = $plugins->pluginClass($widget->plugin_setting);
                }
                $sidebar_settings['sidebar_widgets'][$widget->plugin_setting]['args'] = $widget->plugin_value;
                $count++;
            }
            $plugins->updateSetting('sidebar', 'sidebar_settings', serialize($sidebar_settings));
        }
    }


    /**
     * Get sidebar settings
     *
     * @return array - of sidebar settings
     */
    public function getSidebarSettings()
    {
        global $plugins;
        
        // Get settings from the database if they exist...
        $sidebar_settings = unserialize($plugins->getSetting('sidebar_settings', 'sidebar'));         
        return $sidebar_settings;
    }
    
    
    /**
     * Get sidebar widgets
     *
     * USAGE: foreach ($widgets as $widget=>$details) 
     * { echo "Name: " . $widget; echo $details['order']; echo $details['args']; } 
     * 
     * @return array - of sidebar widgets
     */
    public function getSidebarWidgets()
    {
        global $plugins;
        
        // Get settings from the database if they exist...
        $sidebar_settings = $this->getSidebarSettings();
        
        if ($sidebar_settings['sidebar_widgets']) {
            $widgets = $sidebar_settings['sidebar_widgets'];    // associative array
                    
            $widgets = $this->orderSidebarWidgets($widgets);    // sorts plugins by "order"
    
            return $widgets;
        }
    }

    /**
     * Sort the widgets by order number
     *
     * @param array $widgets
     * @return array - sorted widgets
     */
    public function orderSidebarWidgets($widgets)
    {
        return sksort($widgets, "order", "int", true);
    }
    
    /**
     * Get last sidebar
     *
     * @param array $widgets
     * @return int the highest sidebar value of all the widgets, i.e. the number of sidebars. 
     */
    public function getLastSidebar($widgets)
    {
        global $plugins;
            
        $highest = 1;
        foreach ($widgets as $widget => $details) {
            if (isset($details['sidebar']) && ($details['sidebar'] > $highest)) { $highest = $details['sidebar']; }
        }
        return $highest;
    }
}

?>