<?php
/**
 * name: RSS Show
 * description: Adds links in a widget block to the latest posts from a specified RSS feed.
 * version: 0.8
 * folder: rss_show
 * class: RssShow
 * requires: widgets 0.6
 * hooks: rss_show, install_plugin, hotaru_header, admin_header_include_raw, admin_sidebar_plugin_settings, admin_plugin_settings
 * author: Nick Ramsay
 * authorurl: http://hotarucms.org/member.php?1-Nick
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
 
class RssShow
{
    /**
     * Displays the RSS feed
     *
     * Uses Hotaru's built-in SimplePie library, but extra customization 
     * to the feed is possible by inserting SimplePie calls before $feed->init();
     */
    public function rss_show($h, $ids)
    {
            // if no feed id is specified in the plugin hook, we default to 1.
            if (empty($ids)) { $ids[0] = 1; }
                   
        foreach ($ids as $id) { 
            
            // Get settings from the database:
            $settings = unserialize($h->getSetting('rss_show_' . $id . '_settings', 'rss_show')); 
        
            // Feed settings:
            $feedurl = $settings['feed'];
            if ($settings['cache']) { $cache = true; } else { $cache = false; }
            $cache_duration = $settings['cache_duration'];
            
            // Get the feed...
            $feed = $h->newSimplePie($feedurl, $cache, $cache_duration);
            
            if ($feed) {
            
                // Limit the number of items:
                $max_items = $settings['max_items'];
                
                // Feed is ready.
                $feed->init();
                
                $output = "";
                $item_count = 0;
                
                // SITE TITLE
                if ($settings['title']) { 
                    $output .= "<h2 class='widget_head rss_show_feed_title'>\n";
                    if ($feed->get_link()) { $link = $feed->get_link(); } else { $link = $feed->subscribe_url(); }
                    $output .= "<a href='" . $link . "' title='" . $h->lang["rss_show_title_anchor_title"] . "'>" . $settings['title'] . "</a>";
                    $output .= "<a href='" . $feed->subscribe_url() . "' title='" . $h->lang["rss_show_icon_anchor_title"] . "'>\n";
                    $output .= "<img src='" . BASEURL . "content/themes/" . THEME . "images/rss_16.png' alt='RSS'/></a>\n"; // RSS icon
                    $output .= "</h2>\n"; 
                }
                    
                if ($feed->data) { 
                    $output .= "<ul class='widget_body rss_feed_item'>\n";
                    foreach ($feed->get_items() as $item) {
                            
                            // POST TITLE
                            $output .= "<li class='rss_show_feed_item'>";
                            $output .= "<span class='rss_show_title'>";
                            $output .= "<a href='" . $item->get_permalink() . "'>" . sanitize($item->get_title(), 'tags') . "</a></span>\n";
                            
                            // AUTHOR / DATE
                        if (($settings['author'] == 'yesauthor') || ($settings['date'] == 'yesdate')) {
                                $output .= "<br /><span class='rss_show_author_date'><small>Posted";
                                if ($settings['author'] == 'yesauthor') {
                                    $output .= " by ";
                                        foreach ($item->get_authors() as $author)  {
                                    $output .= sanitize($author->get_name(), 'ents'); 
                                }
                            }
                            if ($settings['date'] == 'yesdate') {
                                $output .= " on " . $item->get_date('j F Y');
                            }
                            $output .= "</small></span><br />";
                        }
                        
                        // SUMMARY
                        if ($settings['content'] == 'summaries') {
                            $output .= "<p class='rss_show_content'>" . substr(sanitize($item->get_content(), 'tags'), 0, 300);
                            $output .= "... ";
                            $output .= "<small><a href='" . $item->get_permalink() . "' title='" . sanitize($item->get_title(), 'tags') . "'>" . $h->lang["rss_show_read_more"] . "</a>";
                            $output .= "</small></p>";
                        }
                        
                        // FULL POST
                        if ($settings['content'] == 'full') {
                            $output .= "<p class='rss_show_content'>" . $item->get_content() . "</p>";
                        }
                        $output .= "</li>\n";
                        
                        $item_count++;
                        if ($item_count >= $max_items) { break;}
                    }
                }
            }
            
            // Display the whole thing:
            if (isset($output)) { echo $output . "</ul>\n"; }
        }
    }
    
    
    /**
     * Redirects to the main RSS Show function
     *
     * This isn't a plugin hook, but a function call created in the Sidebar plugin. 
     */
    function widget_rss_show($h, $args)
    {
        $this->rss_show($h, array($args));
    }

    
    /* *************************************
     * ********** ADMIN FUNCTIONS **********
     * ************************************* */
    
    
    /**
     * Include jQuery for hiding and showing "cache duration" in plugin settings
     */
    public function admin_header_include_raw($h)
    {
        if ($h->isSettingsPage('rss_show')) {
            echo "<script type='text/javascript'>\n";
            echo "$(document).ready(function(){\n";
                echo "$('#rs_cache').click(function () {\n";
                echo "$('#rs_cache_duration').slideToggle();\n";
                echo "});\n";
            echo "});\n";
            echo "</script>\n";
        }
    }
    
    
    /**
     * Install plugin with default settings
     */
    public function install_plugin($h, $id)
    {
        if (!$id || !is_int($id)) { $id = 1; }
        
        $rss_show_settings['feed'] = 'http://feeds2.feedburner.com/hotarucms';
        $rss_show_settings['title'] = 'Hotaru CMS Forums';
        $rss_show_settings['cache'] = 'on';
        $rss_show_settings['cache_duration'] = 10;
        $rss_show_settings['max_items'] = 10;
        $rss_show_settings['author'] = "noauthor";
        $rss_show_settings['date'] = "nodate";
        $rss_show_settings['content'] = "none";
        
        // Add settinsg if they don't already exist:
        if (!$h->getSetting('rss_show_' . $id . '_settings')) { 
            $h->updateSetting('rss_show_' . $id . '_settings', serialize($rss_show_settings), 'rss_show');
        }

        // widget
        $h->addWidget('rss_show', 'rss_show_' . $id, $id);  // plugin name, function name, optional arguments
    }
    
    
    /**
     * Display the contents of the plugin settings page.
     */
    public function admin_plugin_settings($h)
    {
        $this->get_params($h);    // get any arguments passed from the form
        $h->showMessage();    // display any success or failure messages

        require_once(PLUGINS . 'rss_show/rss_show_settings.php');
        $rs = new RssShowSettings();
        $rs->settings($h);
        return true;
    }
    
    
    /**
     * Get parameters passed by URL, e.g. a saved feed url. then save
     */
    public function get_params($h)
    {
        if ($action = $h->cage->get->testAlnumLines('action')) {
            if ($action == 'new_feed') {
                $id = $h->cage->get->getInt('id');
                $this->install_plugin($h, $id);
                $h->message = $h->lang["rss_show_feed_added"];
                $h->messageType = "green";
                
            } elseif ($action == 'delete_feed') {
                $id = $h->cage->get->getInt('id');
                
                // delete from pluginsettings table:
                $h->deleteSettings('rss_show_' . $id . '_settings'); // setting
                
                // delete from widgets table:
                $h->deleteWidget('rss_show_' . $id); // function suffix
                
                // delete from "widgets_settings" in pluginsettings table;
                $widgets_settings = $h->getSerializedSettings('widgets'); 
                unset($widgets_settings['widgets']['rss_show_' . $id]);
                $h->updateSetting('widgets_settings', serialize($widgets_settings), 'widgets');
                
                $h->message = $h->lang["rss_show_feed_removed"];
                $h->messageType = "green";
            }
        } elseif ($id = $h->cage->get->getInt('rss_show_id')) {
            $parameters = array();
            $parameters['feed'] = $h->cage->get->noTags('rss_show_feed');
            $parameters['title'] = $h->cage->get->noTags('rss_show_title');
            $parameters['cache'] = $h->cage->get->getAlpha('rss_show_cache');
            $parameters['cache_duration'] = $h->cage->get->getInt('rss_show_cache_duration');
            $parameters['max_items'] = $h->cage->get->getInt('rss_show_max_items');
            $parameters['author'] = $h->cage->get->getAlpha('rss_show_author');
            $parameters['date'] = $h->cage->get->getAlpha('rss_show_date');
            $parameters['content'] = $h->cage->get->getAlpha('rss_show_content');
            $this->save_settings($h, $id, $parameters);
        }
    }
    
    
    /**
     * Save new or modified settings for this plugin
     *
     * @param int $id
     * @param array $parameters - an array of key-value pairs
     */
    public function save_settings($h, $id, &$parameters)
    {
        $h->message = "";
        if ($parameters) {
            if ($parameters['feed'] == "") {
                    $h->message = $h->lang["rss_show_no_changes"];
                    $h->messageType = "red";
            }
                
            if ($h->message == "") {
                $values = serialize($parameters);
                $h->message = $h->lang["rss_show_update_successful"];
                $h->messageType = "green";
                $h->updateSetting('rss_show_' . $id . '_settings', $values, 'rss_show');    
            }
        }
        
    }

}
?>