<?php
/**
 * name: RSS Show
 * description: Adds links in the sidebar to the latest posts from a specified RSS feed.
 * version: 0.5
 * folder: rss_show
 * class: RssShow
 * requires: sidebar_widgets 0.4
 * hooks: rss_show, install_plugin, hotaru_header, admin_header_include_raw, admin_sidebar_plugin_settings, admin_plugin_settings
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
 
return false; die(); // We don't want to just drop into the file.

class RssShow extends PluginFunctions
{
    /**
     * Displays the RSS feed
     *
     * Uses Hotaru's built-in SimplePie library, but extra customization 
     * to the feed is possible by inserting SimplePie calls before $feed->init();
     */
    public function rss_show($ids)
    {
            // if no feed id is specified in the plugin hook, we default to 1.
            if (empty($ids)) { $ids[0] = 1; }
                   
        foreach ($ids as $id) { 
            
            // Get settings from the database:
            $settings = unserialize($this->getSetting('rss_show_' . $id . '_settings', 'rss_show')); 
        
            // Feed settings:
            $feedurl = $settings['rss_show_feed'];
            if ($settings['rss_show_cache']) { $cache = true; } else { $cache = false; }
            $cache_duration = $settings['rss_show_cache_duration'];
            
            // Get the feed...
            $feed = $this->hotaru->newSimplePie($feedurl, $cache, $cache_duration);
            
            if ($feed) {
            
                // Limit the number of items:
                $max_items = $settings['rss_show_max_items'];
                
                // Feed is ready.
                $feed->init();
                
                $output = "";
                $item_count = 0;
                
                // SITE TITLE
                if ($settings['rss_show_title']) { 
                    $output .= "<h2 class='sidebar_widget_head rss_show_feed_title'>";
                    $output .= "<a href='" . $feed->subscribe_url() . "' title='" . $this->hotaru->lang["rss_show_icon_anchor_title"] . "'><img src='" . BASEURL . "content/themes/" . THEME . "images/rss_16.png'></a>&nbsp;"; // RSS icon
                    if ($feed->get_link()) { $link = $feed->get_link(); } else { $link = $feed->subscribe_url(); }
                    $output .= "<a href='" . $link . "' title='" . $this->hotaru->lang["rss_show_title_anchor_title"] . "'>" . $settings['rss_show_title'] . "</a></h2>"; 
                }
                    
                if ($feed->data) { 
                    $output .= "<ul class='sidebar_widget_body rss_feed_item'>";
                    foreach ($feed->get_items() as $item) {
                            
                            // POST TITLE
                            $output .= "<li class='rss_show_feed_item'>";
                            $output .= "<span class='rss_show_title'>";
                            $output .= "<a href='" . $item->get_permalink() . "'>" . $item->get_title() . "</a></span>";
                            
                            // AUTHOR / DATE
                        if (($settings['rss_show_author'] == 'yesauthor') || ($settings['rss_show_date'] == 'yesdate')) {
                                $output .= "<br /><span class='rss_show_author_date'><small>Posted";
                                if ($settings['rss_show_author'] == 'yesauthor') {
                                    $output .= " by ";
                                        foreach ($item->get_authors() as $author)  {
                                    $output .= $author->get_name(); 
                                }
                            }
                            if ($settings['rss_show_date'] == 'yesdate') {
                                $output .= " on " . $item->get_date('j F Y');
                            }
                            $output .= "</small></span><br />";
                        }
                        
                        // SUMMARY
                        if ($settings['rss_show_content'] == 'summaries') {
                            $output .= "<p class='rss_show_content'>" . substr(strip_tags($item->get_content()), 0, 300);
                            $output .= "... ";
                            $output .= "<small><a href='" . $item->get_permalink() . "' title='" . $item->get_title() . "'>" . $this->hotaru->lang["rss_show_read_more"] . "</a>";
                            $output .= "</small></p>";
                        }
                        
                        // FULL POST
                        if ($settings['rss_show_content'] == 'full') {
                            $output .= "<p class='rss_show_content'>" . $item->get_content() . "</p>";
                        }
                        $output .= '</li>';
                        
                        $item_count++;
                        if ($item_count >= $max_items) { break;}
                    }
                }
            }
            
            // Display the whole thing:
            if (isset($output)) { echo $output . "</ul>"; }
        }
    }
    
    
    /**
     * Redirects to the main RSS Show function
     *
     * This isn't a plugin hook, but a function call created in the Sidebar plugin. 
     */
    function sidebar_widget_rss_show($args)
    {
        $this->rss_show(array($args));
    }

    
    /* *************************************
     * ********** ADMIN FUNCTIONS **********
     * ************************************* */
    
    
    /**
     * Include jQuery for hiding and showing "cache duration" in plugin settings
     */
    public function admin_header_include_raw()
    {
        $admin = new Admin();
        
        if ($admin->isSettingsPage('rss_show')) {
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
     * Put a link to the settings page in the Admin sidebar under Plugin Settings
     */
    public function admin_sidebar_plugin_settings()
    {
        echo "<li><a href='" . $this->hotaru->url(array('page'=>'plugin_settings', 'plugin'=>'rss_show'), 'admin') . "'>" . $this->hotaru->lang["rss_show"] . "</a></li>";
    }
    
    
    /**
     * Install plugin with default settings
     */
    public function install_plugin($id)
    {
        if (!$id || !is_int($id)) { $id = 1; }
        
        $settings['rss_show_feed'] = 'http://feeds2.feedburner.com/hotarucms';
        $settings['rss_show_title'] = 'Hotaru CMS Forums';
        $settings['rss_show_cache'] = 'on';
        $settings['rss_show_cache_duration'] = 10;
        $settings['rss_show_max_items'] = 10;
        $settings['rss_show_author'] = "noauthor";
        $settings['rss_show_date'] = "nodate";
        $settings['rss_show_content'] = "none";
        
        // parameters: plugin folder name, setting name, setting value
        $this->updateSetting('rss_show_' . $id . '_settings', serialize($settings), 'rss_show');
        
        require_once(PLUGINS . 'sidebar_widgets/libs/Sidebar.php');
        $sidebar = new Sidebar($this->hotaru);
        $sidebar->addWidget('rss_show', 'rss_show_' . $id, $id); // plugin name, function name, optional arguments
        
        // Include language file. Also included in hotaru_header, but needed here so 
        // that the link in the Admin sidebar shows immediately after installation.
        $this->includeLanguage('rss_show', 'rss_show');
    }
    
    
    /**
     * Display the contents of the plugin settings page.
     */
    public function admin_plugin_settings()
    {
        $this->get_params();    // get any arguments passed from the form
        $this->hotaru->showMessage();    // display any success or failure messages

        require_once(PLUGINS . 'rss_show/rss_show_settings.php');
        $rs = new RssShowSettings($this->folder, $this->hotaru);
        $rs->settings();
    }
    
    
    /**
     * Get parameters passed by URL, e.g. a saved feed url. then save
     */
    public function get_params()
    {
        if ($action = $this->cage->get->testAlnumLines('action')) {
            if ($action == 'new_feed') {
                $id = $this->cage->get->getInt('id');
                $this->install_plugin($id);
                $this->hotaru->message = $this->hotaru->lang["rss_show_feed_added"];
                $this->hotaru->messageType = "green";
                
            } elseif ($action == 'delete_feed') {
                $id = $this->cage->get->getInt('id');
                
                // delete from pluginsettings table:
                $this->deleteSettings('rss_show_' . $id . '_settings'); // setting
                
                // delete from widgets table:
                require_once(PLUGINS . 'sidebar_widgets/libs/Sidebar.php');
                $sidebar = new Sidebar($this->hotaru);
                $sidebar->deleteWidget('rss_show_' . $id); // function suffix
                
                // delete from "sidebar_settings" in pluginsettings table;
                $sidebar_settings = $sidebar->getSidebarSettings();
                unset($sidebar_settings['sidebar_widgets']['rss_show_' . $id]);
                $this->updateSetting('sidebar_settings', serialize($sidebar_settings), 'sidebar_widgets');
                
                $this->hotaru->message = $this->hotaru->lang["rss_show_feed_removed"];
                $this->hotaru->messageType = "green";
            }
        } elseif ($id = $this->cage->get->getInt('rss_show_id')) {
            $parameters = array();
            $parameters['rss_show_feed'] = $this->cage->get->noTags('rss_show_feed');
            $parameters['rss_show_title'] = $this->cage->get->noTags('rss_show_title');
            $parameters['rss_show_cache'] = $this->cage->get->getAlpha('rss_show_cache');
            $parameters['rss_show_cache_duration'] = $this->cage->get->getInt('rss_show_cache_duration');
            $parameters['rss_show_max_items'] = $this->cage->get->getInt('rss_show_max_items');
            $parameters['rss_show_author'] = $this->cage->get->getAlpha('rss_show_author');
            $parameters['rss_show_date'] = $this->cage->get->getAlpha('rss_show_date');
            $parameters['rss_show_content'] = $this->cage->get->getAlpha('rss_show_content');
            $this->save_settings($id, $parameters);
        }
    }
    
    
    /**
     * Save new or modified settings for this plugin
     *
     * @param int $id
     * @param array $parameters - an array of key-value pairs
     */
    public function save_settings($id, &$parameters)
    {
        $this->hotaru->message = "";
        if ($parameters) {
            if ($parameters['rss_show_feed'] == "") {
                    $this->hotaru->message = $this->hotaru->lang["rss_show_no_changes"];
                    $this->hotaru->messageType = "red";
            }
                
            if ($this->hotaru->message == "") {
                $values = serialize($parameters);
                $this->hotaru->message = $this->hotaru->lang["rss_show_update_successful"];
                $this->hotaru->messageType = "green";
                $this->updateSetting('rss_show_' . $id . '_settings', $values, 'rss_show');    
            }
        }
        
    }

}
?>