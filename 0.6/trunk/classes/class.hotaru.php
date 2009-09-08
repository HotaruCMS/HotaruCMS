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
     * Initialize Inspekt
     *
     * @return object
     */
    function initialize_inspekt()
    {
        global $cage;
        // Global Inspekt SuperCage
        if (!isset($cage)) { 
            $cage = Inspekt::makeSuperCage(); 
        
            // Add Hotaru custom methods
            $cage->addAccessor('testAlnumLines');
            $cage->addAccessor('testPage');
            $cage->addAccessor('testUsername');
            $cage->addAccessor('testPassword');
            $cage->addAccessor('getFriendlyUrl');
            $cage->addAccessor('getMixedString1');
            $cage->addAccessor('getMixedString2');
        }
    }
    
    
    /**
     * Returns all setting-value pairs
     */
    function read_settings()
    {
        global $db;
        
        $sql = "SELECT * FROM " . TABLE_SETTINGS;
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
     * Formats page name, e.g.'page_name' into 'Page name'
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
     * Formats page name, e.g.'page_name' into 'Page Name'
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
     * @param bool $include_once true or false
     */
    function display_template($page = '', $plugin = '', $include_once = true)
    {
        $page = $page . '.php';
                
        /* 
            1. Check the custom theme
            2. Check the default theme
            3. Check the plugin folder
            4. Show the 404 Not Found page
        */
        if (file_exists(THEMES . THEME . $page))
        {
            include_once(THEMES . THEME . $page);
        } 
        elseif (file_exists(THEMES . 'default/' . $page))
        {
            include_once(THEMES . 'default/' . $page);
        }
        elseif ($plugin != '' && file_exists(PLUGINS .  $plugin . '/templates/' . $page))
        {
                if (!$include_once) {
                    // Special case, do not restrict to include once.
                    include(PLUGINS . $plugin . '/templates/' . $page);
                } else {
                    include_once(PLUGINS . $plugin . '/templates/' . $page);
                }
                return true;
                die();
        }
        else 
        {
            include_once(THEMES . '404.php');
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
    function new_simplepie($feed='', $cache=RSS_CACHE_ON, $cache_duration=RSS_CACHE_DURATION)
    {
        include_once(INCLUDES . "SimplePie/simplepie.inc");
        
        if ($feed != '') {
            $sp = new SimplePie();
            $sp->set_feed_url($feed);
            $sp->set_cache_location(CACHE . "rss_cache/");
            $sp->set_cache_duration($cache_duration);
            if ($cache == "true") { 
                $sp->enable_cache(true);
            } else {
                $sp->enable_cache(false);
            }
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
    
    
    /**
     * Combine Included CSS & JSS files
     *
     * @param string $type either 'css' or 'js'
     * @return int version number or echo output to cache file
     * @link http://www.ejeliot.com/blog/72 Based on work by Ed Eliot
     */
     function combine_includes($type = 'css', $version = 0)
     {
        global $cage, $plugin;
        
        if ($this->page_type == 'admin') {
            $plugin->check_actions('admin_header_include');
            $prefix = 'hotaru_admin_';
        } else {
            $plugin->check_actions('header_include');
            $prefix = 'hotaru_';
        }
        
        $cache_length = 31356000;   // about one year
        $cache = CACHE . 'css_js_cache/';
        
        if($type == 'css') { 
            $content_type = 'text/css';
            $includes = $plugin->include_css;
        } else { 
            $type = 'js'; 
            $content_type = 'text/javascript';
            $includes = $plugin->include_js;
        }
        
        if(empty($includes)) { return false; }
        
         /*
            if etag parameter is present then the script is being called directly, otherwise we're including it in 
            another script with require or include. If calling directly we return code othewise we return the etag 
            representing the latest files
        */
        if ($version > 0) {
        
            $iETag = $version;
            $sLastModified = gmdate('D, d M Y H:i:s', $iETag).' GMT';
            
            // see if the user has an updated copy in browser cache
            if (
                ($cage->server->keyExists('HTTP_IF_MODIFIED_SINCE') && $cage->server->testDate('HTTP_IF_MODIFIED_SINCE') == $sLastModified) ||
                ($cage->server->keyExists('HTTP_IF_NONE_MATCH') && $cage->server->testint('HTTP_IF_NONE_MATCH') == $iETag)
            ) {
                header("{$cage->server->getRaw('SERVER_PROTOCOL')} 304 Not Modified");
                exit;
            }
        
            // create a directory for storing current and archive versions
            if (!is_dir($cache)) {
                mkdir($cache);
            }
               
            // get code from archive folder if it exists, otherwise grab latest files, merge and save in archive folder
            if ((CSS_JS_CACHE_ON == "true") && file_exists($cache . $prefix . $type . '_' . $iETag . '.cache')) {
                $sCode = file_get_contents($cache . $prefix . $type . '_' . $iETag . '.cache');
            } else {
                // get and merge code
                $sCode = '';
                $aLastModifieds = array();
        
                foreach ($includes as $sFile) {
                    $aLastModifieds[] = filemtime($sFile);
                    $sCode .= file_get_contents($sFile);
                }
                // sort dates, newest first
                rsort($aLastModifieds);
             
                if ($iETag == $aLastModifieds[0]) { // check for valid etag, we don't want invalid requests to fill up archive folder
                    $oFile = fopen($cache . $prefix . $type . '_' . $iETag . '.cache', 'w');
                    if (flock($oFile, LOCK_EX)) {
                        fwrite($oFile, $sCode);
                        flock($oFile, LOCK_UN);
                    }
                    fclose($oFile);
                } else {
                    // archive file no longer exists or invalid etag specified
                    header("{$cage->server->getRaw('SERVER_PROTOCOL')} 404 Not Found");
                    exit;
                }
        
            }
        
            // send HTTP headers to ensure aggressive caching
            header('Expires: '.gmdate('D, d M Y H:i:s', time() + $cache_length).' GMT'); // 1 year from now
            header('Content-Type: ' . $content_type);
            header('Content-Length: '.strlen($sCode));
            header("Last-Modified: $sLastModified");
            header("ETag: $iETag");
            header('Cache-Control: max-age=' . $cache_length);
        
          // output merged code
          echo $sCode;
          
        } else {
        
            // get file last modified dates
            $aLastModifieds = array();
            foreach ($includes as $sFile) {
                $aLastModifieds[] = filemtime($sFile);
            }
            // sort dates, newest first
            rsort($aLastModifieds);
            
            // output latest timestamp
            return $aLastModifieds[0];
        
        }
     }
        

    /**
     * Included combined files
     *
     * @param int $version_js 
     * @param int $version_css 
     * @param string $page e.g. admin_settings 
     * @param string $plugin e.g. category_manager
     */
     function include_combined($version_js = 0, $version_css = 0, $page = '', $folder = '')
     {
        if ($this->page_type == 'admin') { $index = 'admin/admin_index'; } else { $index = 'index'; }
        if ($page && $folder) { 
            $page = 'page=' . $page; 
            $folder = '&plugin=' . $folder . "&";
        }
        
        if ($version_js > 0) {
            echo "<script type='text/javascript' src='" . BASEURL . $index . ".php?" . $page . "&" . $folder . "combine=1&type=js&version=" . $version_js . "'></script>\n";
        }
        
        if ($version_css > 0) {
            echo "<link rel='stylesheet' href='" . BASEURL . $index . ".php?" . $page . "&" . $folder . "combine=1&type=css&version=" . $version_css . "' type='text/css'>\n";
        }

     }
     
}
?>