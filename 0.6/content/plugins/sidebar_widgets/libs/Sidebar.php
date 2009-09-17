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
     */
    public function initializeSidebarWidgets()
    {
        global $plugins;
        
        // Get settings from the database if they exist...
        $sidebar_settings = $this->getSidebarSettings();
            
        $widgets = $this->getWidgets();
        
        if ($widgets) {
            $count = 1;
            foreach ($widgets as $widget) {
            
                // Assign order number if not already assigned one.
                if (!isset($sidebar_settings['sidebar_widgets'][$widget->widget_plugin]['order'])) {
                    $sidebar_settings['sidebar_widgets'][$widget->widget_plugin]['order'] = $count;
                }
                
                // Assign widget number if not already assigned one.
                if (!isset($sidebar_settings['sidebar_widgets'][$widget->widget_plugin]['sidebar'])) {
                    $sidebar_settings['sidebar_widgets'][$widget->widget_plugin]['sidebar'] = 1;
                }
                
                // Enable the widget if enabled status is not currently set...
                if (!isset($sidebar_settings['sidebar_widgets'][$widget->widget_plugin]['enabled'])) {
                    $sidebar_settings['sidebar_widgets'][$widget->widget_plugin]['enabled'] = true;
                }
                
                // But! Disable it if the plugin for that widget is not currently active.
                if (!$plugins->isActive($widget->widget_plugin) ) {
                    $sidebar_settings['sidebar_widgets'][$widget->widget_plugin]['enabled'] = false;
                }

                // Add plugin name, function suffix and arguments to sidebar_settings:
                $sidebar_settings['sidebar_widgets'][$widget->widget_plugin]['class'] = $plugins->getClassName($widget->widget_plugin);
                $sidebar_settings['sidebar_widgets'][$widget->widget_plugin]['function'] = $widget->widget_function;
                $sidebar_settings['sidebar_widgets'][$widget->widget_plugin]['args'] = $widget->widget_args;

                $count++;
            }
            $plugins->updateSetting('sidebar_settings', serialize($sidebar_settings), 'sidebar_widgets');
        }
    }


    /**
     * Add widget
     *
     * @param string $plugin
     * @param string $function
     * @param string $value
     */
    public function addWidget($plugin = '', $function = '', $args = '')
    {
        global $db, $current_user;
        
        // Check if it exists so we don't add a duplicate
        $sql = "SELECT * FROM " . DB_PREFIX . "widgets WHERE widget_plugin = %s AND widget_function = %s AND widget_args = %s";
        $results = $db->get_results($db->prepare($sql, $plugin, $function, $args));
        
        if (!$results) {
            $sql = "INSERT INTO " . DB_PREFIX . "widgets (widget_plugin, widget_function, widget_args, widget_updateby) VALUES(%s, %s, %s, %d)";
            $db->query($db->prepare($sql, $plugin, $function, $args, $current_user->getId()));
        }
        
        $db->query("OPTIMIZE TABLE " . DB_PREFIX . "widgets");
    }
    
    
    /**
     * Get widgets from widget db table
     *
     * @return array - of widget settings
     */
    public function getWidgets()
    {
        global $db;
        
        // Get settings from the database if they exist...
        $sql = "SELECT * FROM " . DB_PREFIX . 'widgets';
        $widget_settings = $db->get_results($db->prepare($sql));
        return $widget_settings;
    }
    
    
    /**
     * Delete a widget from the widget db table
     *
     * @param string $function 
     */
    public function deleteWidget($function)
    {
        global $db;
        
        // Get settings from the database if they exist...
        $sql = "DELETE FROM " . DB_PREFIX . "widgets WHERE widget_function = %s";
        $db->query($db->prepare($sql, $function));
        
        $db->query("OPTIMIZE TABLE " . DB_PREFIX . "widgets");
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
        $sidebar_settings = unserialize($plugins->getSetting('sidebar_settings', 'sidebar_widgets'));         
        return $sidebar_settings;
    }
    
    
    /**
     * Get sidebar widgets from sidebar_settings array
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