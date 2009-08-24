<?php
/**
 * name: Sidebar
 * description: Manages the contents of the sidebar
 * version: 0.1
 * folder: sidebar
 * prefix: sidebar
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


 /**
 * ********************************************************************* 
 * ********************* FUNCTIONS FOR POST CLASS ********************** 
 * *********************************************************************
 * ****************************************************************** */
 
 
/**
 * Set default settings for the sidebar
 */
function sidebar_install_plugin()
{
    global $db, $plugin, $post;
    
    // A plugin hook so other plugin developers can add defaultsettings
    $plugin->check_actions('sidebar_install_plugin');
    
    $plugin->include_language('sidebar');
}


/**
 * Set things up when the page is first loaded
 */
function sidebar_hotaru_header()
{
    global $hotaru, $plugin, $sidebar, $lang;
    
    $plugin->include_language('sidebar');
    
    if ($hotaru->sidebar) {
        require_once(PLUGINS . 'sidebar/class.sidebar.php');
        // Create a new global object called "sidebar".
        $sidebar = new Sidebar();
        
        $sidebar->initialize_sidebar_widgets();
        
        $vars['sidebar'] = $sidebar; 
        return $vars; 
    }
}


/**
 * Include css.
 */
function sidebar_header_include()
{ 
    global $plugin; 
    
    $plugin->include_css('sidebar'); 
}


/**
 * Include css in Admin
 */
function sidebar_admin_header_include()
{ 
    global $plugin, $admin;
    
    $plugin->include_css('sidebar');
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
function sidebar_sidebar($sidebar_id = array(1))
{
    global $plugin, $sidebar, $hotaru;

    $sidebar_id = $sidebar_id[0];
        
    $widgets = $sidebar->get_sidebar_widgets();

    foreach ($widgets as $widget => $details) {
        $function_name = "sidebar_widget_" . $widget;
        
        // Only show widgets intended for this sidebar
        if (($details['sidebar'] == $sidebar_id) && $details['enabled']) {
        
            // Call this widget's function
            if (function_exists($function_name)) {
                $function_name($details['args']);    // pass an argument, e.g. a feed ID for the RSS Show plugin
            } else {
                /* For multiple instances of widgets, we need to strip the id off the end and use the argument as the identifier.
                   E.g. CHANGE sidebar_widget_rss_show_1(1); 
                        TO     sidebar_widget_rss_show(1); */
                
                $function_name_array = explode('_', $function_name);
                array_pop($function_name_array); 
                $function_name = implode('_', $function_name_array);
                $function_name($details['args']);    // pass an argument, e.g. a feed ID for the RSS Show plugin
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
 * Link to settings page in the Admin sidebar
 */
function sidebar_admin_sidebar_plugin_settings()
{
    global $lang, $plugin;
    
    if (!isset($lang["sidebar_admin_sidebar"])) {
        $plugin->include_language('sidebar');
    }
    echo "<li><a href='" . url(array('page'=>'plugin_settings', 'plugin'=>'sidebar'), 'admin') . "'>" . $lang["sidebar_admin_sidebar"] . "</a></li>";
}


/**
 * Sidebar Settings Page
 */
function sidebar_admin_plugin_settings()
{
    global $hotaru, $plugin, $cage, $lang, $sidebar;
    
    echo "<h1>" . $lang["sidebar_settings_header"] . "</h1>\n";
    
    if ($cage->get->testAlpha('action')) {
    
        // Get sidebar settings from the database...
        $sidebar_settings = unserialize($plugin->plugin_settings('sidebar', 'sidebar_settings')); 
        
        // Get the list of sidebar widgets...
        $widgets = $sidebar->get_sidebar_widgets();
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
                if (($sidebar->get_last_sidebar($widgets) > 1) && ($sidebar_settings['sidebar_widgets'][$this_widget_name]['sidebar'] > 1)) {
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
        $plugin->plugin_settings_update('sidebar', 'sidebar_settings', serialize($sidebar_settings));
        
    }
    
    $hotaru->show_messages();
    $hotaru->display_template('sidebar_ordering', 'sidebar');
}


/**
 * Save Sidebar Settings
 *
 * @return true
 */
function sidebar_save_settings()
{
    global $cage, $hotaru, $plugin, $lang;
    
    $error = 0;
    
    // Get settings from the database if they exist...
    $sidebar_settings = unserialize($plugin->plugin_settings('sidebar', 'sidebar_settings')); 
        
    // A plugin hook so other plugin developers can save settings   
    $plugin->check_actions('sidebar_save_settings');
    
    // Save new settings...    

    
    // parameters: plugin folder name, setting name, setting value
    $plugin->plugin_settings_update('sidebar', 'sidebar_settings', serialize($sidebar_settings));
    
    if ($error == 0) {
        $hotaru->messages[$lang["sidebar_settings_saved"]] = "green";
    }
    
    $hotaru->show_messages();
    
    return true;    
} 

?>