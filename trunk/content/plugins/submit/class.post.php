<?php
/**
 * name: Post
 * description: Class for functions related to submitting and organizing posts
 * file: /plugins/submit/libraries/class.post.php
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
    
class Post {    

    var $post_id = 0;
    var $post_orig_url = '';
    var $post_domain = '';              // the domain of the submitted url
    var $post_title = '';
    var $post_content = '';
    var $post_content_length = 50;      // default min characters for content
    var $post_summary = '';
    var $post_summary_length = 200;     // default max characters for summary
    var $post_status = 'unsaved';
    var $post_author = 0;
    var $post_url = '';
    var $post_date = '';
    var $post_subscribe = 0;
    var $posts_per_page = '10';
    
    var $allowable_tags = '';
    
    var $template_name = '';
            
    var $use_submission = true;
    var $use_author = true;
    var $use_date = true;
    var $use_content = true;
    var $use_summary = true;

    var $post_vars = array();


    /**
     * PHP __set Magic Method
     * Plugins use this to set additonal member variables
     *
     * @param str $name - the name of the member variable
     * @param mixed $value - the value to set it to.
     */
    function __set($name, $value)
    {
        $this->post_vars[$name] = $value;
    }


    /**
     * PHP __get Magic Method
     * Plugins use this to read values of additonal member variables
     *
     * @param str $name - the name of the member variable
     */
    function __get($name)
    {
        if (array_key_exists($name, $this->post_vars)) {
            return $this->post_vars[$name];
        }
    }


    /**
     * Get all the settings for the current post
     *
     * @param int $post_id - Optional row from the posts table in the database
     * @return bool
     */    
    function read_post($post_id = 0)
    {
        global $plugin, $post_row, $hotaru;
        
        //enabled
        if ($plugin->plugin_settings('submit', 'submit_enabled') == 'checked') { $this->use_submission = true; } else { $this->use_submission = false; }
        
        //author
        if ($plugin->plugin_settings('submit', 'submit_author') == 'checked') { $this->use_author = true; } else { $this->use_author = false; }
        
        //date
        if ($plugin->plugin_settings('submit', 'submit_date') == 'checked') { $this->use_date = true; } else { $this->use_date = false; }
        
        //content
        if ($plugin->plugin_settings('submit', 'submit_content') == 'checked') { $this->use_content = true; } else { $this->use_content = false; }
        $content_length =  $plugin->plugin_settings('submit', 'submit_content_length');
        if (!empty($content_length)) { $this->post_content_length = $content_length; }
        
        //summary
        if ($plugin->plugin_settings('submit', 'submit_summary') == 'checked') { $this->use_summary = true; } else { $this->use_summary = false; }
        $summary_length =  $plugin->plugin_settings('submit', 'submit_summary_length');
        if (!empty($summary_length)) { $this->post_summary_length = $summary_length; }
        
        //posts_per_page
        $posts_per_page =  $plugin->plugin_settings('submit', 'submit_posts_per_page');
        if (!empty($posts_per_page)) { $this->posts_per_page = $posts_per_page; }
        
        //allowable_tags
        $allowable_tags =  $plugin->plugin_settings('submit', 'submit_allowable_tags');
        if (!empty($allowable_tags)) { $this->allowable_tags = $allowable_tags; }
                
        $plugin->check_actions('submit_class_post_read_post_1');
        
        if ($post_id != 0) {
            $post_row = $this->get_post($post_id);
            $this->post_title = stripslashes(urldecode($post_row->post_title));
            $this->post_content = stripslashes(urldecode($post_row->post_content));
            $this->post_id = $post_row->post_id;
            $this->post_orig_url = urldecode($post_row->post_orig_url);            
            $this->post_status = $post_row->post_status;
            $this->post_author = $post_row->post_author;
            $this->post_url = urldecode($post_row->post_url);
            $this->post_date = $post_row->post_date;
            $this->post_subscribe = $post_row->post_subscribe;
            
            $plugin->check_actions('submit_class_post_read_post_2');
                        
            return true;
        } else {
            return false;
        }
        
    }
    
    
    /**
     * Add a post to the database
     *
     * @return true
     */    
    function add_post()
    {
        global $db, $plugin, $last_insert_id, $current_user;
        
        $parsed = parse_url($this->post_orig_url);
        if (isset($parsed['scheme'])){ $this->post_domain = $parsed['scheme'] . "://" . $parsed['host']; }
            
        $sql = "INSERT INTO " . TABLE_POSTS . " SET post_orig_url = %s, post_domain = %s, post_title = %s, post_url = %s, post_content = %s, post_status = %s, post_author = %d, post_date = CURRENT_TIMESTAMP, post_subscribe = %d, post_updateby = %d";
        
        $db->query($db->prepare($sql, urlencode($this->post_orig_url), urlencode($this->post_domain), urlencode(trim($this->post_title)), urlencode(trim($this->post_url)), urlencode(trim($this->post_content)), $this->post_status, $this->post_author, $this->post_subscribe, $current_user->id));
        
        $last_insert_id = $db->get_var($db->prepare("SELECT LAST_INSERT_ID()"));
        
        $this->post_id = $last_insert_id;
                
        $plugin->check_actions('submit_class_post_add_post');
        
        return true;
    }
    
    
    /**
     * Update a post in the database
     *
     * @return true
     */    
    function update_post()
    {
        global $db, $plugin, $current_user;
        
        $parsed = parse_url($this->post_orig_url);
        if (isset($parsed['scheme'])){ $this->post_domain = $parsed['scheme'] . "://" . $parsed['host']; }
        
        $sql = "UPDATE " . TABLE_POSTS . " SET post_orig_url = %s, post_domain = %s, post_title = %s, post_url = %s, post_content = %s, post_status = %s, post_author = %d, post_subscribe = %d, post_updateby = %d WHERE post_id = %d";
        
        $db->query($db->prepare($sql, urlencode($this->post_orig_url), urlencode($this->post_domain), urlencode(trim($this->post_title)), urlencode(trim($this->post_url)), urlencode(trim($this->post_content)), $this->post_status, $this->post_author, $this->post_subscribe, $current_user->id, $this->post_id));
        
        $plugin->check_actions('submit_class_post_update_post');
        
        return true;
    }
    
    
    /**
     * Update a post's status
     *
     * @return true
     */    
    function change_status($status = "processing")
    {
        global $db;
            
        $this->post_status = $status;
            
        $sql = "UPDATE " . TABLE_POSTS . " SET post_status = %s WHERE post_id = %d";
        $db->query($db->prepare($sql, $this->post_status, $this->post_id));        
        return true;
    }


    /**
     * Gets a single post from the database
     *
     * @return array|false
     */    
    function get_post($post_id = 0)
    {
        global $db;
        $sql = "SELECT * FROM " . TABLE_POSTS . " WHERE post_id = %d ORDER BY post_date DESC";
        $post = $db->get_row($db->prepare($sql, $post_id));
        if ($post) { return $post; } else { return false; }
    }
    

    /**
     * Physically delete a post from the database 
     *
     * There's a plugin hook in here to delete their parts, e.g. votes, coments, tags, etc.
     */    
    function delete_post()
    {
        global $db, $plugin;
        $sql = "DELETE FROM " . TABLE_POSTS . " WHERE post_id = %d";
        $db->query($db->prepare($sql, $this->post_id));
        
        $plugin->check_actions('submit_class_post_delete_post');
        
    }
    
    
    /**
     * Gets all the posts from the database
     *
     * @param array $vars - search parameters
     * @param int $limit - no. of rows to retrieve
     * @param bool $all - true to retrieve ALL rows, else default 20
     * @return array|false $prepare_array is the prepared SQL statement
     *
     * Example usage: $post->filter(array('post_tags LIKE %s' => '%tokyo%'), 10);
     */    
    function filter($vars = array(), $limit = 0, $all = false)
    {
        global $db;
        
        $filter = '';
        $prepare_array = array();
        $prepare_array[0] = "temp";    // placeholder to be later filled with the SQL query.
        
        if (!empty($vars)) {
            $filter = " WHERE ";
            foreach ($vars as $key => $value) {
                $filter .= $key . " AND ";    // e.g. " post_tags LIKE %s "
                array_push($prepare_array, $value);
            }
            $filter = rstrtrim($filter, "AND ");
        }
        
        if ($all == true) {
            $limit = '';
        } elseif ($limit == 0) { 
            $limit = "LIMIT 20"; 
        } else { 
            $limit = "LIMIT " . $limit; 
        }
        
        $sql = "SELECT * FROM " . TABLE_POSTS . $filter . " ORDER BY post_date DESC " . $limit;
                
        $prepare_array[0] = $sql;
                
        // $prepare_array needs to be passed to $db->prepare, i.e. $db->get_results($db->prepare($prepare_array));
                
        if ($prepare_array) { return $prepare_array; } else { return false; }
    }
    
    
    /**
     * Gets all the posts from the database
     *
     * @param array $prepared array - prepared SQL statement from filter()
     * @return array|false - array of posts
     */    
    function get_posts($prepared_array = array())
    {
        global $db;
        
        if (!empty($prepared_array)) {                
            $posts = $db->get_results($db->prepare($prepared_array));
            if ($posts) { return $posts; }
        } 
        
        return false;
    }
    
    
    /**
     * Publish content as an RSS feed
     * Uses the 3rd party RSS Writer class.
     */    
    function rss_feed()
    {
        global $db, $lang, $cage, $plugin, $current_user;
        require_once(INCLUDES . 'RSSWriterClass/rsswriter.php');
        
        $status = $cage->get->testAlpha('status');
        $limit = $cage->get->getInt('limit');
        $user = $cage->get->testUsername('user');
        $tag = $cage->get->noTags('tag');
        $category = $cage->get->noTags('category');
        
        //if (!$status) { $status = "top"; }
        if (!$limit) { $limit = 10; }
                    
        if ($status) { $filter['post_status = %s'] = $status; }
        if ($user) { $filter['post_author = %d'] = $current_user->get_user_id($cage->get->testUsername('user'));  }
        if ($tag) { $filter['post_tags LIKE %s'] = '%' . $tag . '%'; }
        if ($category && (FRIENDLY_URLS == "true")) { $filter['post_category = %d'] = get_cat_id($category); }
        if ($category && (FRIENDLY_URLS == "false")) { $filter['post_category = %d'] = $category; }
        
        $plugin->check_actions('submit_class_post_rss_feed');
        
        $feed = new RSS();
        $feed->title       = SITE_NAME;
        $feed->link        = BASEURL;
        
        if ($status == 'new') { $feed->description = $lang["submit_rss_latest_from"] . " " . SITE_NAME; }
        elseif ($status == 'top') { $feed->description = $lang["submit_rss_top_stories_from"] . " " . SITE_NAME; }
        elseif ($user) { $feed->description = $lang["submit_rss_stories_from_user"] . " " . $user; }
        elseif ($tag) { $feed->description = $lang["submit_rss_stories_tagged"] . " " . $tag; }
        elseif ($category && (FRIENDLY_URLS == "true")) { $feed->description = $lang["submit_rss_stories_in_category"] . " " . $category; }
        elseif ($category && (FRIENDLY_URLS == "false")) { $feed->description = $lang["submit_rss_stories_in_category"] . " " . get_cat_name($category); }
                
        if (!isset($filter))  $filter = array();
        $prepared_array = $this->filter($filter, $limit);
        $results = $db->get_results($db->prepare($prepared_array));

        if ($results) {
            foreach ($results as $result) 
            {
                $item = new RSSItem();
                $item->title = stripslashes(urldecode($result->post_title));
                $item->link  = urldecode($result->post_url);
                $item->setPubDate($result->post_date); 
                $item->description = "<![CDATA[ " . stripslashes(urldecode($result->post_content)) . " ]]>";
                $feed->addItem($item);
            }
        }
        echo $feed->serve();
    }
    
    
    /**
     * Checks for existence of a url
     *
     * @return array|false - array of posts
     */    
    function url_exists($url = '')
    {
        global $db;
        $sql = "SELECT count(post_id) FROM " . TABLE_POSTS . " WHERE post_orig_url = %s";
        $posts = $db->get_var($db->prepare($sql, urlencode($url)));
        if ($posts > 0) { return $posts; } else { return false; }
    }
    
    
    /**
     * Checks for existence of a title
     *
     * @param str $title
     * @return int - id of post with matching title
     */
    function title_exists($title = '')
    {
        global $db;
        $title = trim($title);
        $sql = "SELECT post_id FROM " . TABLE_POSTS . " WHERE post_title = %s";
        $post_id = $db->get_var($db->prepare($sql, urlencode($title)));
        if ($post_id) { return $post_id; } else { return false; }
    }
    
    
    /**
     * Checks for existence of a post with given post_url
     *
     * @param str $post_url
     * @return int - id of post with matching url
     */
    function is_post_url($post_url = '')
    {
        global $db;
        $sql = "SELECT post_id FROM " . TABLE_POSTS . " WHERE post_url = %s";
        $post_id = $db->get_var($db->prepare($sql, urlencode($post_url)));
        if ($post_id) { return $post_id; } else { return false; }
    }
    
    /**
     * Get Unique Post Statuses
     *
     * @return array|false
     */
    function get_unique_statuses() 
    {
        global $db;
        $sql = "SELECT DISTINCT post_status FROM " . TABLE_POSTS;
        $statuses = $db->get_results($db->prepare($sql));
        if ($statuses) { return $statuses; } else { return false; }
    }
    
        
    /**
     * Prepares and calls functions to send a trackback
     */
    function send_trackback()
    {
        global $lang, $post;
            
        // Scan content for trackback urls
        $tb_array = array();
        
        $trackback = $this->detect_trackback();
        
        // Clean up the title and description...
        $title = htmlspecialchars(strip_tags($post->post_title));
        $title = (strlen($post->post_title) > 150) ? substr($post->post_title, 0, 150) . '...' : $post->post_title;
        $excerpt = strip_tags($post->post_content);
        $excerpt = (strlen($excerpt) > 200) ? substr($excerpt, 0, 200) . '...' : $excerpt;

        if ($this->ping($trackback, url(array('page'=>$post->post_id)), $title, $excerpt)) {
            echo "Trackback sent successfully...";
        } else {
            echo "Error sending trackback....";
        }
    }
    
    
    
    /**
     * Scan content of source url for a trackback url
     *
     * @return str - trackback url
     *
     * Adapted from Pligg.com and SocialWebCMS.com
     */
    function detect_trackback()
    {
        global $post;
        
        include_once(INCLUDES . 'SWCMS/class.httprequest.php');
        
        // Fetch the content of the original url...
        $url = $post->post_orig_url;
        if ($url != 'http://' && $url != ''){
        $r = new HTTPRequest($url);
        $content = $r->DownloadToString();
        } else {
            $content = '';
        }
        
        if (preg_match('/trackback:ping="([^"]+)"/i', $content, $matches) ||
            preg_match('/trackback:ping +rdf:resource="([^>]+)"/i', $content, $matches) ||
            preg_match('/<trackback:ping>([^<>]+)/i', $content, $matches)) {
                $trackback = trim($matches[1]);
                
        } elseif (preg_match('/<a[^>]+rel="trackback"[^>]*>/i', $content, $matches)) {
            if (preg_match('/href="([^"]+)"/i', $matches[0], $matches2)) {
                $trackback = trim($matches2[1]);
            }
            
        } elseif (preg_match('/<a[^>]+href=[^>]+>trackback<\/a>/i', $content, $matches)) {
            if (preg_match('/href="([^"]+)"/i', $matches[0], $matches2)) {
                $trackback = trim($matches2[1]);
            }
        } elseif (preg_match('/http:([^ ]+)trackback.php([^<|^ ]+)/i', $content, $matches)) {
                $trackback = trim($matches[1]);
                
        } elseif (preg_match('/trackback:ping="([^"]+)"/', $content, $matches)) {
                $trackback = trim($matches[1]);
        }
                
        return $trackback;
    }
    
    

    /**
     * Send a trackback to the source url
     *
     * @param str $trackback - url of source
     * @param str $url - of Hotaru post
     * @param str $title
     * @param str $excerpt
     * @link http://phptrackback.sourceforge.net/docs/
     */
    function ping($trackback, $url, $title = "", $excerpt = "")
    {
        global $lang;
        
        $response = "";
        $reason = ""; 
        
        // Set default values
        if (empty($title)) {
            $title = SITE_NAME;
        } 
        if (empty($excerpt)) {
            // If no excerpt show "This article has been featured on Site Name".
            $excerpt = $lang['submit_trackback_excerpt'] . " " . SITE_NAME;
        } 
        // Parse the target
        $target = parse_url($trackback);
        
        if ((isset($target["query"])) && ($target["query"] != "")) {
            $target["query"] = "?" . $target["query"];
        } else {
            $target["query"] = "";
        } 
    
            if ((isset($target["port"]) && !is_numeric($target["port"])) || (!isset($target["port"]))) {
                $target["port"] = 80;
            } 
            // Open the socket
            $tb_sock = fsockopen($target["host"], $target["port"]); 
            // Something didn't work out, return
            if (!is_resource($tb_sock)) {
                return '$post->ping: Tring to send a trackback but can\'t connect to: ' . $tb . '.';
                exit;
            } 
            
            // Put together the things we want to send
            $tb_send = "url=" . rawurlencode($url) . "&title=" . rawurlencode($title) . "&blog_name=" . rawurlencode(SITE_NAME) . "&excerpt=" . rawurlencode($excerpt); 
             
            // Send the trackback
            fputs($tb_sock, "POST " . $target["path"] . $target["query"] . " HTTP/1.1\r\n");
            fputs($tb_sock, "Host: " . $target["host"] . "\r\n");
            fputs($tb_sock, "Content-type: application/x-www-form-urlencoded\r\n");
            fputs($tb_sock, "Content-length: " . strlen($tb_send) . "\r\n");
            fputs($tb_sock, "Connection: close\r\n\r\n");
            fputs($tb_sock, $tb_send); 
            // Gather result
            while (!feof($tb_sock)) {
                $response .= fgets($tb_sock, 128);
            } 
    
            // Close socket
            fclose($tb_sock); 
            // Did the trackback ping work
            strpos($response, '<error>0</error>') ? $return = true : $return = false;
            // send result
            return $return;
    } 
}

?>