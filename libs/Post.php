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

    protected $id = 0;
    protected $origUrl = '';
    protected $domain = '';              // the domain of the submitted url
    protected $title = '';
    protected $content = '';
    protected $contentLength = 50;      // default min characters for content
    protected $summary = '';
    protected $summaryLength = 200;     // default max characters for summary
    protected $status = 'unsaved';
    protected $author = 0;
    protected $url = '';
    protected $date = '';
    protected $subscribe = 0;
    protected $postsPerPage = 10;
    
    protected $allowableTags = '';
    
    protected $templateName = '';
            
    protected $useSubmission = true;
    protected $useAuthor = true;
    protected $useDate = true;
    protected $useContent = true;
    protected $useSummary = true;

    public $vars = array();


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
     * Get post id
     *
     * @return int
     */    
    public function getId()
    {
        return $this->id;
    }
    

    /**
     * Get original url
     *
     * @return string
     */    
    public function getOrigUrl()
    {
        return $this->origUrl;
    }
    

    /**
     * Get original domain
     *
     * @return string
     */    
    public function getDomain()
    {
        return $this->domain;
    }
    

    /**
     * Get post title
     *
     * @return string
     */    
    public function getTitle()
    {
        return $this->title;
    }
    
    
    /**
     * Get post author
     *
     * @return int
     */    
    public function getAuthor()
    {
        return $this->author;
    }
    
    
    /**
     * Get post content
     *
     * @return string
     */    
    public function getContent()
    {
        return $this->content;
    }
    
    
    /**
     * Get post date
     *
     * @return string
     */    
    public function getDate()
    {
        return $this->date;
    }
    
    
    /**
     * Get post status
     *
     * @return string
     */    
    public function getStatus()
    {
        return $this->status;
    }
    
    
    /**
     * Set Summary Length
     *
     * @param int $length
     */    
    public function setSummaryLength($length)
    {
        $this->summaryLength = $length;
    }
    
    
    /**
     * Get Summary Length
     *
     * @return int $length
     */    
    public function getSummaryLength()
    {
        return $this->summaryLength;
    }
    
    
    /**
     * Set Content Length
     *
     * @param int $length
     */    
    public function setContentLength($length)
    {
        $this->contentLength = $length;
    }
    
    
    /**
     * Get Content Length
     *
     * @return int $length
     */    
    public function getContentLength()
    {
        return $this->contentLength;
    }
    
    
    /**
     * Set Posts Per Page
     *
     * @param int $num
     */    
    public function setPostsPerPage($num)
    {
        $this->postsPerPage = $num;
    }
    
    
    /**
     * Get Posts Per Page
     *
     * @return int $num
     */    
    public function getPostsPerPage()
    {
        return $this->postsPerPage;
    }
    
    
    /**
     * Set Allowable Tags
     *
     * @param string $allowed
     */    
    public function setAllowableTags($allowed)
    {
        $this->allowableTags = $allowed;
    }
    
    
    /**
     * Get Allowable Tags
     *
     * @return string $allowed
     */    
    public function getAllowableTags()
    {
        return $this->allowableTags;
    }
    
    
    /**
     * Set useSubmission
     *
     * @param bool $bool
     */    
    public function setUseSubmission($bool)
    {
        $this->useSubmission = $bool;
    }
    
    
    /**
     * Get useSubmission
     *
     * @return bool
     */    
    public function getUseSubmission()
    {
        return $this->useSubmission;
    }
    
    
    /**
     * Set useAuthor
     *
     * @param bool $bool
     */    
    public function setUseAuthor($bool)
    {
        $this->useAuthor = $bool;
    }
    
    
    /**
     * Get useAuthor
     *
     * @return bool
     */    
    public function getUseAuthor()
    {
        return $this->useAuthor;
    }
    
    
    /**
     * Set useDate
     *
     * @param bool $bool
     */    
    public function setUseDate($bool)
    {
        $this->useDate = $bool;
    }
    
    
    /**
     * Get useDate
     *
     * @return bool
     */    
    public function getUseDate()
    {
        return $this->useDate;
    }
    
    
    /**
     * Set useContent
     *
     * @param bool $bool
     */    
    public function setUseContent($bool)
    {
        $this->useContent = $bool;
    }
    
    
    /**
     * Get useContent
     *
     * @return bool
     */    
    public function getUseContent()
    {
        return $this->useContent;
    }
    
    
    /**
     * Set useSummary
     *
     * @param bool $bool
     */    
    public function setUseSummary($bool)
    {
        $this->useSummary = $bool;
    }
    
    
    /**
     * Get useSummary
     *
     * @return bool
     */    
    public function getUseSummary()
    {
        return $this->useSummary;
    }
    
    
    /**
     * Get all the settings for the current post
     *
     * @param int $post_id - Optional row from the posts table in the database
     * @return bool
     */    
    public function readPost($post_id = 0)
    {
        global $plugins, $post_row, $hotaru;
        
        // Get settings from database if they exist...
        $submit_settings = $plugins->getSerializedSettings('submit');
    
        // Assign settings to class member
        $this->setContentLength($submit_settings['post_content_length']);
        $this->setSummaryLength($submit_settings['post_summary_length']);
        $this->setPostsPerPage($submit_settings['post_posts_per_page']);
        $this->setAllowableTags($submit_settings['post_allowable_tags']);

        $use_submission = $submit_settings['post_enabled'];
        $use_author = $submit_settings['post_author'];
        $use_date = $submit_settings['post_date'];
        $use_content = $submit_settings['post_content'];
        $use_summary = $submit_settings['post_summary'];
        
        //enabled
        if ($use_submission == 'checked') { $this->setUseSubmission(true);  } else { $this->setUseSubmission(false);  }
        
        //author
        if ($use_author == 'checked') { $this->setUseAuthor(true); } else { $this->setUseAuthor(false);  }
        
        //date
        if ($use_date == 'checked') { $this->setUseDate(true); } else { $this->setUseDate(false);  }
        
        //content
        if ($use_content == 'checked') { $this->setUseContent(true); } else { $this->setUseContent(false);  }
        
        //summary
        if ($use_summary == 'checked') { $this->setUseSummary(true); } else { $this->setUseSummary(false); }

                
        $plugins->checkActions('post_read_post_1');
        
        if ($post_id != 0) {
            $post_row = $this->getPost($post_id);
            $this->title = stripslashes(urldecode($post_row->post_title));
            $this->content = stripslashes(urldecode($post_row->post_content));
            $this->id = $post_row->post_id;
            $this->origUrl = urldecode($post_row->post_orig_url);            
            $this->status = $post_row->post_status;
            $this->author = $post_row->post_author;
            $this->url = urldecode($post_row->post_url);
            $this->date = $post_row->post_date;
            $this->subscribe = $post_row->post_subscribe;
            
            $plugins->checkActions('post_read_post_2');
                        
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
    public function addPost()
    {
        global $db, $plugins, $last_insert_id, $current_user;
        
        $parsed = parse_url($this->post_orig_url);
        if (isset($parsed['scheme'])){ $this->post_domain = $parsed['scheme'] . "://" . $parsed['host']; }
            
        $sql = "INSERT INTO " . TABLE_POSTS . " SET post_orig_url = %s, post_domain = %s, post_title = %s, post_url = %s, post_content = %s, post_status = %s, post_author = %d, post_date = CURRENT_TIMESTAMP, post_subscribe = %d, post_updateby = %d";
        
        $db->query($db->prepare($sql, urlencode($this->post_orig_url), urlencode($this->post_domain), urlencode(trim($this->post_title)), urlencode(trim($this->post_url)), urlencode(trim($this->post_content)), $this->post_status, $this->post_author, $this->post_subscribe, $current_user->id));
        
        $last_insert_id = $db->get_var($db->prepare("SELECT LAST_INSERT_ID()"));
        
        $this->post_id = $last_insert_id;
                
        $plugins->check_actions('submit_class_post_add_post');
        
        return true;
    }
    
    
    /**
     * Update a post in the database
     *
     * @return true
     */    
    public function updatePost()
    {
        global $db, $plugins, $current_user;
        
        $parsed = parse_url($this->post_orig_url);
        if (isset($parsed['scheme'])){ $this->post_domain = $parsed['scheme'] . "://" . $parsed['host']; }
        
        $sql = "UPDATE " . TABLE_POSTS . " SET post_orig_url = %s, post_domain = %s, post_title = %s, post_url = %s, post_content = %s, post_status = %s, post_author = %d, post_subscribe = %d, post_updateby = %d WHERE post_id = %d";
        
        $db->query($db->prepare($sql, urlencode($this->post_orig_url), urlencode($this->post_domain), urlencode(trim($this->post_title)), urlencode(trim($this->post_url)), urlencode(trim($this->post_content)), $this->post_status, $this->post_author, $this->post_subscribe, $current_user->id, $this->post_id));
        
        $plugins->check_actions('submit_class_post_update_post');
        
        return true;
    }
    
    
    /**
     * Update a post's status
     *
     * @return true
     */    
    public function changeStatus($status = "processing")
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
    public function getPost($post_id = 0)
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
    public function deletePost()
    {
        global $db, $plugin;
        $sql = "DELETE FROM " . TABLE_POSTS . " WHERE post_id = %d";
        $db->query($db->prepare($sql, $this->post_id));
        
        $plugins->check_actions('submit_class_post_delete_post');
        
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
    public function filter($vars = array(), $limit = 0, $all = false, $select = '*', $orderby = 'post_date DESC')
    {
        global $db;
        
        if(!isset($filter)) { $filter = ''; }
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
        
        $sql = "SELECT " . $select . " FROM " . TABLE_POSTS . $filter . " ORDER BY " . $orderby . " " . $limit;
        
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
    public function getPosts($prepared_array = array())
    {
        global $db;
        
        if (!empty($prepared_array)) {
            if (empty($prepared_array[1])) {
                $posts = $db->get_results($prepared_array[0]); // ignoring the prepare function.
            } else {
                $posts = $db->get_results($db->prepare($prepared_array)); 
            }
            if ($posts) { return $posts; }
        }
        
        return false;
    }
    
    
    /**
     * Publish content as an RSS feed
     * Uses the 3rd party RSS Writer class.
     */    
    public function rssFeed()
    {
        global $db, $lang, $cage, $plugins, $current_user;
        require_once(INCLUDES . 'RSSWriterClass/rsswriter.php');
        
        $select = '';
        $orderby = '';
        
        $status = $cage->get->testAlpha('status');
        $limit = $cage->get->getInt('limit');
        $user = $cage->get->testUsername('user');
        $tag = $cage->get->noTags('tag');
        $category = $cage->get->noTags('category');
        $search = $cage->get->getMixedString2('search');
        
        //if (!$status) { $status = "top"; }
        if (!$limit) { $limit = 10; }
                    
        if ($status) { $filter['post_status = %s'] = $status; }
        if ($user) { $filter['post_author = %d'] = $current_user->get_user_id($cage->get->testUsername('user'));  }
        if ($tag) { $filter['post_tags LIKE %s'] = '%' . $tag . '%'; }
        if ($category && (FRIENDLY_URLS == "true")) { $filter['post_category = %d'] = get_cat_id($category); }
        if ($category && (FRIENDLY_URLS == "false")) { $filter['post_category = %d'] = $category; }
        if ($search && $plugins->plugin_active('search')) { 
            $prepared_search = prepare_search_filter($search); 
            extract($prepared_search);
            $orderby = "post_date DESC";    // override "relevance DESC" so the RSS feed updates with the latest related terms. 
        }
        
        $plugins->check_actions('submit_class_post_rss_feed');
        
        $feed = new RSS();
        $feed->title       = SITE_NAME;
        $feed->link        = BASEURL;
        
        if ($status == 'new') { $feed->description = $lang["submit_rss_latest_from"] . " " . SITE_NAME; }
        elseif ($status == 'top') { $feed->description = $lang["submit_rss_top_stories_from"] . " " . SITE_NAME; }
        elseif ($user) { $feed->description = $lang["submit_rss_stories_from_user"] . " " . $user; }
        elseif ($tag) { $feed->description = $lang["submit_rss_stories_tagged"] . " " . $tag; }
        elseif ($category && (FRIENDLY_URLS == "true")) { $feed->description = $lang["submit_rss_stories_in_category"] . " " . $category; }
        elseif ($category && (FRIENDLY_URLS == "false")) { $feed->description = $lang["submit_rss_stories_in_category"] . " " . get_cat_name($category); }
        elseif ($search) { $feed->description = $lang["submit_rss_stories_search"] . " " . stripslashes($search); }
                
        if (!isset($filter))  $filter = array();
        $prepared_array = $this->filter($filter, $limit, false, $select, $orderby);
        $results = $this->get_posts($prepared_array);

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
    public function urlExists($url = '')
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
    public function titleExists($title = '')
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
    public function isPostUrl($post_url = '')
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
    public function getUniqueStatuses() 
    {
        global $db;
        $sql = "SELECT DISTINCT post_status FROM " . TABLE_POSTS;
        $statuses = $db->get_results($db->prepare($sql));
        if ($statuses) { return $statuses; } else { return false; }
    }
    

    /**
     * Prepare filter and breadcrumbs for Posts List
     *
     * @return array
     */
    public function prepareList()
    {
        global $hotaru, $plugins, $cage, $filter, $lang, $page_title, $select, $orderby;
    
        $userbase = new UserBase();
        $this->templateName = "list";
                
        if (!$filter) { $filter = array(); }
        
        if ($cage->get->testPage('page') == 'latest') 
        {
            $filter['post_status = %s'] = 'new'; 
            $rss = "<a href='" . url(array('page'=>'rss', 'status'=>'new')) . "'>";
            $rss .= " <img src='" . BASEURL . "content/themes/" . THEME . "images/rss_10.png'></a>";
            $page_title = $lang["post_breadcrumbs_latest"] . $rss;
        } 
        else 
        {
            $filter['post_status = %s'] = 'top';
            $rss = "<a href='" . url(array('page'=>'rss')) . "'>";
            $rss .= " <img src='" . BASEURL . "content/themes/" . THEME . "images/rss_10.png'></a>";
            $page_title = $lang["post_breadcrumbs_top"] . $rss;
        }
        
        $plugins->checkActions('post_list_filter');
        
        // defaults
        if (!isset($select)) { $select = '*'; }
        if (!isset($orderby)) { $orderby = 'post_date DESC'; }
        
        $prepared_filter = $this->filter($filter, 0, true, $select, $orderby);
        $stories = $this->getPosts($prepared_filter);
        
        return $stories;
    }
    
    
    /**
     * Prepares and calls functions to send a trackback
     */
    public function sendTrackback()
    {
        global $lang, $post;
            
        // Scan content for trackback urls
        $tb_array = array();
        
        $trackback = $this->detectTrackback();
        
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
    public function detectTrackback()
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
    public function ping($trackback, $url, $title = "", $excerpt = "")
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