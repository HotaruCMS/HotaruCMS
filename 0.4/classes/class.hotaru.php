<?php
/**
 * Used for the current environment, e.g. determining the page, etc.
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
class Hotaru
{
    var $is_debug       = false;    // show db queries and page loading time
    var $sidebar        = true;     // enable or diable the sidebar
    var $message        = '';       // message to display
    var $message_type   = 'green';  // green or red, color of message box
    var $messages       = array();  // for multiple messages
    var $title          = '';       // for the broswer's TITLE tags
    var $page_type      = '';       // what kind of page we're looking at
    
    
    /**
     * Returns all setting-value pairs
     */
    function read_settings()
    {
        global $db;
        
        $sql = "SELECT * FROM " . table_settings;
        $results = $db->get_results($db->prepare($sql));
        if ($results) { return $results; } else { return false; }
    }
    
    
    /**
     * Checks if current page (in url) matches the page parameter
     *
     * @param string $page page name
     *
     */
    function is_page($page = '')
    {
        global $cage;
        
        $real_page = $cage->get->testPage('page');
        
        if (!$real_page) { 
            /*  Possibly a post with multi-byte characters? 
                Try getMixedString2... */
            $real_page = $cage->get->getMixedString2('page');
        }
        
        // Try POST...
        if (!$real_page) { $real_page = $cage->post->testPage('page'); }
        
        if (!$real_page) { $real_page = "main"; }

        $real_page = rtrim($real_page, '/');    // remove trailing slash

        if ($real_page == $page) { return $page; } else { return false; }
    }
    
    
    /**
     * Gets the current page name
     */
    function get_page_name()
    {
        global $cage;
        
        // Try GET...
        $page = $cage->get->testPage('page');
        if (!$page) {
            /*  Possibly a post with multi-byte characters? 
                Try getMixedString2... */
            $page = $cage->get->getMixedString2('page');
        }
        
        // Try POST...
        if (!$page) { $page = $cage->post->testPage('page'); }

        if ($page) {
            $page = rtrim($page, '/');
            return $page;
        } else {
            return 'main';
        }
    }
    
    
    /**
     * Formats page name, e.g.'posts_list' into 'Posts list'
     *
     * @param string $page page name
     */
    function page_to_title($page)
    {
        $word_array = array();
        $word_array = explode('_', trim($page));
        $page       = ucfirst(implode(' ', $word_array));
                
        return $page;
    }
    
    
    /**
     * Formats page name, e.g.'posts_list' into 'Posts List'
     *
     * @param string $page page name
     */
    function page_to_title_caps($page)
    {
        $word_array = array();
        $word_array = explode('_', trim($page));
        $word_array = array_map('ucfirst', $word_array);
        $page       = implode(' ', $word_array);
                
        return $page;
    }
    
    
    /**
     * Includes a template to display
     *
     * @param string $page page name
     * @param string $plugin optional plugin name
     */
    function display_template($page = '', $plugin = '')
    {
        $page = $page . '.php';
                
        /* First check if there's a specified plugin for the file and load 
           the template from the plugin folder if it's there. */
        if ($plugin != '') {
            if (file_exists(plugins .  $plugin . '/templates/' . $page)) {
                if ($plugin == 'vote') {
                    // Special case, do not restrict to include once.
                    include(plugins . $plugin . '/templates/' . $page);
                } else {
                    include_once(plugins . $plugin . '/templates/' . $page);
                }
                return true;
                die();
            }
        }
        
        // Check the custom theme then the default theme...        
        if (file_exists(themes . theme . $page)) {
            include_once(themes . theme . $page);
        } elseif (file_exists(themes . 'default/' . $page)) {
            include_once(themes . 'default/' . $page);
        } else {
            include_once(themes . '404.php');
        }
    }
    
    
    /**
     * Displays an announcement at the top of the screen
     * @return array
     */
    function check_announcements() 
    {
        global $lang, $plugin;
        
        $announcements = array();

        // 1. "All plugins are currently disabled."
        if (!$plugin->num_active_plugins()) {
            array_push(
                $announcements, 
                $lang['main_announcement_plugins_disabled']
            );
        }

        // 2. User login and registration currently disabled.
        if (!is_array($announcements)) {
            return false;
        } else {
            return $announcements;
        }
    }
    
    
    /**
     * Includes the SimplePie RSS file and sets the cache
     *
     * @param string $feed
     * @param bool $cache
     * @param int $cache_duration
     *
     * @return object|false $sp
     */
    function new_simplepie($feed='', $cache=true, $cache_duration=10)
    {
        include_once(includes . "SimplePie/simplepie.inc");
        
        if ($feed != '') {
            $sp = new SimplePie();
            $sp->set_feed_url($feed);
            $sp->set_cache_location(includes . "SimplePie/cache/");
            $sp->set_cache_duration($cache_duration);
            $sp->enable_cache($cache);
            $sp->handle_content_type();
            return $sp;
        } else { 
            return false; 
        }
    }
    
    
    /**
     * Shows number of database queries and the time it takes for a page to load
     */
     
    function show_queries_and_time()
    {
        global $db;
        if ($this->is_debug) { 
            echo "<p class='debug'>" . $db->num_queries . " " . $lang['main_hotaru_queries_time'] . " " . timer_stop(1) . " " . 
            $lang['main_hotaru_seconds'] . "</p>"; 
        }
    }
    
    
    /**
     * Display a SINGLE success or failure message
     *
     * @param string $msg
     * @param string $msg_type ('green' or 'red')
     * 
     *  Usage:
     *    Longhand:
     *         $hotaru->message = "This is a message";
     *        $hotaru->message_type = "green";
     *        $hotaru->show_message();
     *        
     *    Shorthand:
     *        $hotaru->show_message("This is a message", "green");
     */
    function show_message($msg = '', $msg_type = 'green')
    {
        if ($msg != '') {
            echo "<div class='message " . $msg_type . "'>" . $msg . "</div>";
        } elseif ($this->message != '') {
            echo "<div class='message " . $this->message_type . "'>" . 
            $this->message . "</div>";
        }
    }
    
    
    /**
     * Displays ALL success or failure messages
     *
     *  Usage:
     *        $hotaru->messages['This is a message'] = "green";
     *        $hotaru->show_messages();
     */
    function show_messages()
    {
        if (!empty($this->messages)) {
            foreach ($this->messages as $msg => $msg_type) {
                echo "<div class='message " . $msg_type . "'>" . 
                $msg . "</div>";
            }
        }
    }
}
?>