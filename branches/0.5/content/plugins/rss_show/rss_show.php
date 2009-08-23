<?php
 
/* ********** PLUGIN *********************************************************************************
 * name: RSS Show
 * description: Adds links in the sidebar to the latest posts from a specified RSS feed.
 * version: 0.1
 * folder: rss_show
 * prefix: rs
 * hooks: rss_show, install_plugin, hotaru_header, admin_header_include_raw, header_include, admin_sidebar_plugin_settings, admin_plugin_settings
 *
 * Usage: Add <?php $plugin->check_actions('rss_show'); ?> to your theme, wherever you want to show the links.
 *
 *  License:
 *
 *   This file is part of Hotaru CMS (http://www.hotarucms.org/).
 *
 *   Hotaru CMS is free software: you can redistribute it and/or modify it under the terms of the 
 *   GNU General Public License as published by the Free Software Foundation, either version 3 of 
 *   the License, or (at your option) any later version.
 *
 *   Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 *   even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License along with Hotaru CMS. If not, 
 *   see http://www.gnu.org/licenses/.
 *   
 *   Copyright (C) 2009 Hotaru CMS - http://www.hotarucms.org/
 *
 **************************************************************************************************** */
 
return false; die(); // We don't want to just drop into the file.


/* ******************************************************************** 
 *  Function: rs_rss_show
 *  Parameters: None
 *  Purpose: Displays the RSS feed.
 *  Notes: Uses Hotaru's built-in SimplePie library, but extra customization 
 *         to the feed is possible by inserting SimplePie calls before $feed->init();
 ********************************************************************** */

function rs_rss_show($ids)
{
    global $hotaru, $plugin, $lang;
    
        // if no feed id is specified in the plugin hook, we default to 1.
        if(empty($ids)) { $ids[0] = 1; }
               
    foreach($ids as $id) { 
        
        // Get settings from the database:
        $settings = unserialize($plugin->plugin_settings('rss_show', 'rss_show_' . $id . '_settings')); 
    
        // Feed settings:
        $feedurl = $settings['rss_show_feed'];
        if($settings['rss_show_cache']) { $cache = true; } else { $cache = false; }
        $cache_duration = $settings['rss_show_cache_duration'];
        
        // Get the feed...
        $feed = $hotaru->new_simplepie($feedurl, $cache, $cache_duration);
        
        if($feed) {
        
            // Limit the number of items:
            $max_items = $settings['rss_show_max_items'];
            
            // Feed is ready.
            $feed->init();
            
            $output = "";
            $item_count = 0;
            
            // SITE TITLE
            if($settings['rss_show_title']) { 
                $output .= "<h2 class='rss_show_feed_title'>";
                $output .= "<a href='" . $feed->subscribe_url() . "' title='" . $lang["rss_show_icon_anchor_title"] . "'><img src='" . baseurl . "content/themes/" . theme . "images/rss_16.png'></a>&nbsp;"; // RSS icon
                if($feed->get_link()) { $link = $feed->get_link(); } else { $link = $feed->subscribe_url(); }
                $output .= "<a href='" . $link . "' title='" . $lang["rss_show_title_anchor_title"] . "'>" . $settings['rss_show_title'] . "</a></h2>"; 
            }
                
            if($feed->data) { 
                $output .= "<ul class='rss_feed_item'>";
                foreach ($feed->get_items() as $item) {
                        
                        // POST TITLE
                        $output .= "<li class='rss_show_feed_item'>";
                        $output .= "<span class='rss_show_title'>";
                        $output .= "<a href='" . $item->get_permalink() . "'>" . $item->get_title() . "</a></span>";
                        
                        // AUTHOR / DATE
                    if(($settings['rss_show_author'] == 'yesauthor') || ($settings['rss_show_date'] == 'yesdate')) {
                            $output .= "<br /><span class='rss_show_author_date'><small>Posted";
                            if($settings['rss_show_author'] == 'yesauthor') {
                                $output .= " by ";
                                    foreach ($item->get_authors() as $author)  {
                                $output .= $author->get_name(); 
                            }
                        }
                        if($settings['rss_show_date'] == 'yesdate') {
                            $output .= " on " . $item->get_date('j F Y');
                        }
                        $output .= "</small></span><br />";
                    }
                    
                    // SUMMARY
                    if($settings['rss_show_content'] == 'summaries') {
                        $output .= "<p class='rss_show_content'>" . substr(strip_tags($item->get_content()), 0, 300);
                        $output .= "... ";
                        $output .= "<small><a href='" . $item->get_permalink() . "' title='" . $item->get_title() . "'>" . $lang["rss_show_read_more"] . "</a>";
                        $output .= "</small></p>";
                    }
                    
                    // FULL POST
                    if($settings['rss_show_content'] == 'full') {
                        $output .= "<p class='rss_show_content'>" . $item->get_content() . "</p>";
                    }
                    $output .= '</li>';
                    
                    $item_count++;
                    if($item_count >= $max_items) { break;}
                }
            }
        }
        
        // Display the whole thing:
        if(isset($output)) { echo $output . "</ul>"; }
    }
}


/* ******************************************************************** 
 *  Function: rs_sidebar_show_rss_show
 *  Parameters: None
 *  Purpose: Redirects to the main RSS Show function.
 *  Notes: This isn't a plugin hook, but a function call created in the Sidebar plugin. 
 ********************************************************************** */

function sidebar_widget_rss_show($args) {

    rs_rss_show(array($args));
}

/* ******************************************************************** 
 *  Function: rs_hotaru_header()
 *  Parameters: None
 *  Purpose: Includes the RSS Show language file
 *  Notes: ---
 ********************************************************************** */
 
function rs_hotaru_header() {
    global $plugin, $lang;
    
    $plugin->include_language('rss_show');
}
    
    
/* ******************************************************************** 
 *  Function: rs_header_include()
 *  Parameters: None
 *  Purpose: Includes the RSS Show css file
 *  Notes: ---
 ********************************************************************** */
 
function rs_header_include() {
    global $plugin;
    
    $plugin->include_css('rss_show');
}



/* *************************************
 * ********** ADMIN FUNCTIONS **********
 * ************************************* */


/* ******************************************************************** 
 *  Function: rs_admin_sidebar_plugin_settings
 *  Parameters: None
 *  Purpose: Includes jQuery for hiding and showing "cache duration" in plugin settings
 *  Notes: ---
 ********************************************************************** */
 
function rs_admin_header_include_raw() {
    global $admin;
    
    if($admin->is_settings_page('rss_show')) {
        echo "<script type='text/javascript'>\n";
        echo "$(document).ready(function(){\n";
            echo "$('#rs_cache').click(function () {\n";
            echo "$('#rs_cache_duration').slideToggle();\n";
            echo "});\n";
        echo "});\n";
        echo "</script>\n";
    }
}

/* ******************************************************************** 
 *  Function: rs_admin_sidebar_plugin_settings
 *  Parameters: None
 *  Purpose: Puts a link to the settings page in the Admin sidebar under Plugin Settings
 *  Notes: ---
 ********************************************************************** */
 
function rs_admin_sidebar_plugin_settings() {
    global $lang;
    echo "<li><a href='" . url(array('page'=>'plugin_settings', 'plugin'=>'rss_show'), 'admin') . "'>" . $lang["rss_show"] . "</a></li>";
}


/* ******************************************************************** 
 *  Function: rs_install_plugin
 *  Parameters: None
 *  Purpose: When the plugin is installed, this function inserts some prelimnary 
 *           settings into the pluginsettings table.
 *  Notes: All database queries should use the prepare function.
 ********************************************************************** */
 
function rs_install_plugin($id) {
    global $db, $plugin, $lang;
    
    if(!$id || !is_int($id)) { $id = 1; }
    
    $settings['rss_show_feed'] = 'http://feeds2.feedburner.com/hotarucms';
    $settings['rss_show_title'] = 'Hotaru CMS Forums';
    $settings['rss_show_cache'] = 'on';
    $settings['rss_show_cache_duration'] = 10;
    $settings['rss_show_max_items'] = 10;
    $settings['rss_show_author'] = "noauthor";
    $settings['rss_show_date'] = "nodate";
    $settings['rss_show_content'] = "none";
    
    // parameters: plugin folder name, setting name, setting value
    $plugin->plugin_settings_update('rss_show', 'rss_show_' . $id . '_settings', serialize($settings));
    $plugin->plugin_settings_update('sidebar_widgets', 'rss_show_' . $id, $id);
    
    // Include language file. Also included in hotaru_header, but needed here so 
    // that the link in the Admin sidebar shows immediately after installation.
    $plugin->include_language('rss_show');
}


/* ******************************************************************** 
 *  Function: rs_admin_sidebar_settings
 *  Parameters: None
 *  Purpose: Displays the contents of the plugin settings page.
 *  Notes: ---
 ********************************************************************** */
 
function rs_admin_plugin_settings() {
    global $hotaru, $plugin;
    
    rs_get_params();    // get any arguments passed from the form
    $hotaru->show_message();    // display any success or failure messages
    $hotaru->display_template('rss_show_settings', 'rss_show');
}


/* ******************************************************************** 
 *  Function: rs_get_params
 *  Parameters: None
 *  Purpose: Retrieves parameters passed by URL, e.g. a saved feed url, and calls the appropriate functions
 *  Notes: Access to $_GET and $_POST is disabled for security reasons. Please use Inspekt to 
 *         access those parameters. See http://funkatron.com/inspekt/user_docs/
 *         Hotaru uses $cage, an instance of Inspekt's SuperCage object.
 ********************************************************************** */
 
function rs_get_params() {
    global $cage, $hotaru, $plugin, $lang;
    if($action = $cage->get->testAlnumLines('action')) {
        if($action == 'new_feed') {
            $id = $cage->get->getInt('id');
            rs_install_plugin($id);
            $hotaru->message = $lang["rss_show_feed_added"];
            $hotaru->message_type = "green";
            
        } elseif($action == 'delete_feed') {
            $id = $cage->get->getInt('id');
            $plugin->plugin_settings_remove_setting('rss_show_' . $id . '_settings');
            $plugin->plugin_settings_remove_setting('sidebar_widgets', 'rss_show_' . $id);
            $hotaru->message = $lang["rss_show_feed_removed"];
            $hotaru->message_type = "green";
        }
    } elseif($id = $cage->get->getInt('rss_show_id')) {
        $parameters = array();
        $parameters['rss_show_feed'] = $cage->get->noTags('rss_show_feed');
        $parameters['rss_show_title'] = $cage->get->noTags('rss_show_title');
        $parameters['rss_show_cache'] = $cage->get->getAlpha('rss_show_cache');
        $parameters['rss_show_cache_duration'] = $cage->get->getInt('rss_show_cache_duration');
        $parameters['rss_show_max_items'] = $cage->get->getInt('rss_show_max_items');
        $parameters['rss_show_author'] = $cage->get->getAlpha('rss_show_author');
        $parameters['rss_show_date'] = $cage->get->getAlpha('rss_show_date');
        $parameters['rss_show_content'] = $cage->get->getAlpha('rss_show_content');
        rs_save_settings($id, $parameters);
    }
}


/* ******************************************************************** 
 *  Function: rs_save_settings
 *  Parameters: The parameters from the form as an array of key-value pairs
 *  Purpose: Saves new or modified settings for this plugin
 *  Notes: Returns to the plugin_settings page via a redirect. 
 ********************************************************************** */
 
function rs_save_settings($id, &$parameters) {
    global $plugin, $hotaru, $lang;
    $hotaru->message = "";
    if($parameters) {
        if($parameters['rss_show_feed'] == "") {
                $hotaru->message = $lang["rss_show_no_changes"];
                $hotaru->message_type = "red";
        }
            
        if($hotaru->message == "") {
            $values = serialize($parameters);
            $hotaru->message = $lang["rss_show_update_successful"];
            $hotaru->message_type = "green";
            $plugin->plugin_settings_update('rss_show', 'rss_show_' . $id . '_settings', $values);    
        }
    }
    
}
     
?>