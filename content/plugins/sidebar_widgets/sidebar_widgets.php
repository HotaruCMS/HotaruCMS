<?php
/**
 * name: Sidebar Widgets
 * description: Manages the contents of the sidebar
 * version: 0.3
 * folder: sidebar_widgets
 * class: SidebarWidgets
 * hooks: install_plugin, hotaru_header, header_include, admin_header_include, admin_plugin_settings, admin_sidebar_plugin_settings, sidebar
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


class SidebarWidgets extends PluginFunctions
{ 

    /**
     * Set default settings for the sidebar
     */
    public function install_plugin()
    {
        global $db, $post;
        
        // Create a new empty table called "posts"
        $exists = $db->table_exists('widgets');
        if (!$exists) {
            //echo "table doesn't exist. Stopping before creation."; exit;
            $sql = "CREATE TABLE `" . DB_PREFIX . "widgets` (
              `widget_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `widget_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
              `widget_plugin` varchar(32) NOT NULL DEFAULT '',
              `widget_function` varchar(255) NULL, 
              `widget_args` varchar(255) NULL, 
              `widget_updateby` int(20) NOT NULL DEFAULT 0
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Widgets';";
            $db->query($sql); 
        }
        
        // A plugin hook so other plugin developers can add defaultsettings
        $this->pluginHook('sidebar_install_plugin');
        
        $this->includeLanguage();
    }
    
    
    /**
     * Set things up when the page is first loaded
     */
    public function hotaru_header()
    {
        global $hotaru, $sidebar, $lang;
        
        $this->includeLanguage();
        
        if (!defined('TABLE_WIDGETS')) { define("TABLE_WIDGETS", DB_PREFIX . 'widgets'); }
        
        if ($hotaru->getSidebar()) {
            // Create a new global object called "sidebar".
            $sidebar = new Sidebar();
            
            $sidebar->initializeSidebarWidgets();
            
            $vars['sidebar'] = $sidebar; 
            return $vars; 
        }
    }
    
     /**
     * ********************************************************************* 
     * ************ FUNCTIONS FOR SHOWING THE SIDEBAR CONTENT ************** 
     * *********************************************************************
     * ****************************************************************** */
    
    /**
     * This is the hook in the sidebar template. 
     *
     * It builds a new function name from the widget name and calls it.
     */
    public function sidebar($sidebar_id = array(1))
    {
        global $sidebar, $hotaru;
    
        $sidebar_id = $sidebar_id[0];
            
        $widgets = $sidebar->getSidebarWidgets();
        
        foreach ($widgets as $widget => $details) {
            $function_name = "sidebar_widget_" . $widget;
            
            // Only show widgets intended for this sidebar
            if (($details['sidebar'] == $sidebar_id) && $details['enabled']) {
            
                // Call this widget's function
                if (function_exists($function_name))
                {
                    echo $function_name . " exists<br />";
                    $function_name($details['args']);    // pass an argument, e.g. a feed ID for the RSS Show plugin
                } 
                elseif ($details['class'] && method_exists($details['class'], $function_name)) 
                {   
                    // must be a class object with a method that matches!
                    $class = new $details['class'];
                    $class->$function_name($details['args']);
                } 
                else 
                {
                    /* For multiple instances of widgets, we need to strip the id off the end and use the argument as the identifier.
                       E.g. CHANGE sidebar_widget_rss_show_1(1); 
                            TO     sidebar_widget_rss_show(1); */
                    
                    $function_name_array = explode('_', $function_name);
                    array_pop($function_name_array); 
                    $function_name = implode('_', $function_name_array);
                    if (function_exists($function_name))
                    {
                        $function_name($details['args']);    // pass an argument, e.g. a feed ID for the RSS Show plugin
                    } 
                    elseif ($details['class'])
                    {
                        // must be a class object!
                        $class = new $details['class'];
                        $class->$function_name($details['args']);
                    }
                }
            }
        }
    }
    
    
    
     /**
     * ********************************************************************* 
     * ******************* FUNCTIONS FOR ADMIN SETTINGS ******************** 
     * *********************************************************************
     * ****************************************************************** */
    
    
    /**
     * Sidebar Settings Page
     */
    public function admin_plugin_settings()
    {
        global $hotaru, $cage, $lang, $sidebar;
        
        echo "<h1>" . $lang["sidebar_settings_header"] . "</h1>\n";
        
        if ($cage->get->testAlpha('action')) {
        
            // Get sidebar settings from the database...
            $sidebar_settings = unserialize($this->getSetting('sidebar_settings')); 
            
            // Get the list of sidebar widgets...
            $widgets = $sidebar->getSidebarWidgets();
            $last = count($widgets);
                
            $this_widget_name = $cage->get->testAlnumLines('widget');
            $this_widget_order = $cage->get->testInt('order');
            $this_widget_sidebar = $cage->get->testInt('sidebar');
            
            if ($cage->get->testAlpha('action') == 'orderup') {
                if ($this_widget_order > 1) {
                    // find widget in the target spot...
                    foreach ($widgets as $widget => $details) {
                        if ($details['order'] == ($this_widget_order - 1)) {
                        
                            //Check if this widget and the target are in the same sidebar
                            if ($sidebar_settings['sidebar_widgets'][$widget]['sidebar'] == $sidebar_settings['sidebar_widgets'][$this_widget_name]['sidebar']) {
                            
                                $sidebar_settings['sidebar_widgets'][$widget]['order'] = $details['order'] + 1;
                                $sidebar_settings['sidebar_widgets'][$this_widget_name]['order'] = $this_widget_order - 1;
                                $hotaru->messages[$lang['sidebar_order_updated']] = 'green';
                                break;
                            } else {
                                // In different sidebars so don't change the order, just the sidebar value
                                $sidebar_settings['sidebar_widgets'][$this_widget_name]['sidebar']--;
                            }
                        }
                    }
                            
                } else {
                    // prevent moving into sidebar 0:
                    if (($sidebar->getLastSidebar($widgets) > 1) && ($sidebar_settings['sidebar_widgets'][$this_widget_name]['sidebar'] > 1)) {
                        $sidebar_settings['sidebar_widgets'][$this_widget_name]['sidebar']--;
                    } else {
                        $hotaru->messages[$lang['sidebar_order_already_first']] = 'red';
                    }
                }
                
            } elseif ($cage->get->testAlpha('action') == 'orderdown') {
                if ($this_widget_order < $last) {
                    // find widget in the target spot...
                    foreach ($widgets as $widget => $details) {
                        if ($details['order'] == ($this_widget_order + 1)) {
                            $sidebar_settings['sidebar_widgets'][$widget]['order'] = $details['order'] - 1;
                            $sidebar_settings['sidebar_widgets'][$this_widget_name]['order'] = $this_widget_order + 1;
                            $hotaru->messages[$lang['sidebar_order_updated']] = 'green';
                            break;
                        }
                    }
                } else {
                    $sidebar_settings['sidebar_widgets'][$this_widget_name]['sidebar']++;
                    //$hotaru->messages[$lang['sidebar_order_already_last']] = 'red';
                }        
            } 
            elseif ($cage->get->testAlpha('action') == 'enable') 
            {
                $sidebar_settings['sidebar_widgets'][$this_widget_name]['enabled'] = true;
                $hotaru->messages[$lang['sidebar_order_enabled']] = 'green';
            } 
            elseif ($cage->get->testAlpha('action') == 'disable') 
            {
                $sidebar_settings['sidebar_widgets'][$this_widget_name]['enabled'] = false;
                $hotaru->messages[$lang['sidebar_order_disabled']] = 'green';
            }
            
            // Save updated sidebar settings
            $this->updateSetting('sidebar_settings', serialize($sidebar_settings));
            
        }
        
        $hotaru->showMessages();
        $hotaru->displayTemplate('sidebar_ordering', 'sidebar_widgets');
    }

}

?>
