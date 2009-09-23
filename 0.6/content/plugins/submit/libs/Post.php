<?php
/**
 * The Post class contains some useful methods for using posts
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
            
    protected $useLatest = false;       // Split posts into "Top" and "Latest" pages
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
     * Set post ID
     *
     * @param int $id
     */    
    public function setId($id)
    {
        return $this->id = $id;
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
     * Set source url
     *
     * @param string $orig_url
     */    
    public function setOrigUrl($orig_url)
    {
        return $this->origUrl = $orig_url;
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
     * Set post domain
     *
     * @param string $domain
     */    
    public function setDomain($domain)
    {
        return $this->domain = $domain;
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
     * Set post title
     *
     * @param string $title
     */    
    public function setTitle($title)
    {
        return $this->title = $title;
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
     * Set post author
     *
     * @param string $author
     */    
    public function setAuthor($author)
    {
        return $this->author = $author;
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
     * Set post url
     *
     * @param string $url
     */    
    public function setUrl($url)
    {
        return $this->url = $url;
    }
    
    
    /**
     * Get url
     *
     * @return string
     */    
    public function getUrl()
    {
        return $this->url;
    }
    
    
    /**
     * Set post content
     *
     * @param string $content
     */    
    public function setContent($content)
    {
        return $this->content = $content;
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
     * Set post date
     *
     * @param string $date
     */    
    public function setDate($date)
    {
        return $this->date = $date;
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
     * Set post status
     *
     * @param string $status
     */    
    public function setStatus($status)
    {
        return $this->status = $status;
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
     * Enable/Disable latest page
     *
     * @param bool $bool
     */    
    public function setUseLatest($bool)
    {
        return $this->useLatest = $bool;
    }
    
    
    /**
     * Get latest page status
     *
     * @return bool
     */    
    public function getUseLatest()
    {
        return $this->useLatest;
    }
    
    
    /**
     * Set Subscribe
     *
     * @param bool $num - should be 1 (subscribed) or 0
     */    
    public function setSubscribe($num)
    {
        $this->subscribe = $num;
    }
    
    
    /**
     * Get Subscribe
     *
     * @return int
     */    
    public function getSubscribe()
    {
        return $this->subscribe;
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
     * @return int
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
                
        $plugins->pluginHook('post_read_post_1');
        
        if ($post_id != 0) {
            $post_row = $this->getPost($post_id);
            $this->setTitle(stripslashes(urldecode($post_row->post_title)));
            $this->setContent(stripslashes(urldecode($post_row->post_content)));
            $this->setId($post_row->post_id);
            $this->setOrigUrl(urldecode($post_row->post_orig_url));            
            $this->setStatus($post_row->post_status);
            $this->setAuthor($post_row->post_author);
            $this->setUrl(urldecode($post_row->post_url));
            $this->setDate($post_row->post_date);
            $this->setSubscribe($post_row->post_subscribe);
            
            $plugins->pluginHook('post_read_post_2');
                        
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
        
        $parsed = parse_url($this->origUrl);
        if (isset($parsed['scheme'])){ $this->domain = $parsed['scheme'] . "://" . $parsed['host']; }
            
        $sql = "INSERT INTO " . TABLE_POSTS . " SET post_orig_url = %s, post_domain = %s, post_title = %s, post_url = %s, post_content = %s, post_status = %s, post_author = %d, post_date = CURRENT_TIMESTAMP, post_subscribe = %d, post_updateby = %d";
        
        $db->query($db->prepare($sql, urlencode($this->origUrl), urlencode($this->domain), urlencode(trim($this->title)), urlencode(trim($this->url)), urlencode(trim($this->content)), $this->status, $this->author, $this->subscribe, $current_user->getId()));
        
        $last_insert_id = $db->get_var($db->prepare("SELECT LAST_INSERT_ID()"));
        
        $this->id = $last_insert_id;
                
        $plugins->pluginHook('post_add_post');
        
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
        
        $parsed = parse_url($this->origUrl);
        if (isset($parsed['scheme'])){ $this->domain = $parsed['scheme'] . "://" . $parsed['host']; }
        
        $sql = "UPDATE " . TABLE_POSTS . " SET post_orig_url = %s, post_domain = %s, post_title = %s, post_url = %s, post_content = %s, post_status = %s, post_author = %d, post_subscribe = %d, post_updateby = %d WHERE post_id = %d";
        
        $db->query($db->prepare($sql, urlencode($this->origUrl), urlencode($this->domain), urlencode(trim($this->title)), urlencode(trim($this->url)), urlencode(trim($this->content)), $this->status, $this->author, $this->subscribe, $current_user->getId(), $this->id));
        
        $plugins->pluginHook('post_update_post');
        
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
            
        $this->status = $status;
            
        $sql = "UPDATE " . TABLE_POSTS . " SET post_status = %s WHERE post_id = %d";
        $db->query($db->prepare($sql, $this->status, $this->id));        
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
        global $db, $plugins;
        $sql = "DELETE FROM " . TABLE_POSTS . " WHERE post_id = %d";
        $db->query($db->prepare($sql, $this->id));
        
        $plugins->pluginHook('post_delete_post');
        
    }
    
    
    /**
     * Gets all the posts from the database
     *
     * @param array $vars - search parameters
     * @param int $limit - no. of rows to retrieve
     * @param bool $all - true to retrieve ALL rows, else default 20
     * @param string $select - the select clause
     * @param string $orderby - the order by clause
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
        
        if ($orderby) { $orderby = "ORDER BY " . $orderby; }
        
        $sql = "SELECT " . $select . " FROM " . TABLE_POSTS . $filter . " " . $orderby . " " . $limit;
        
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
        require_once(EXTENSIONS . 'RSSWriterClass/rsswriter.php');
        
        $select = '*';
        
        $status = $cage->get->testAlpha('status');
        $limit = $cage->get->getInt('limit');
        $user = $cage->get->testUsername('user');
        $tag = $cage->get->noTags('tag');
        $search = $cage->get->getMixedString2('search');
        $category = $cage->get->noTags('category');
        if ($category) { 
            // so we can use a couple of functions from the Category class
            require_once(PLUGINS . 'categories/libs/Category.php');
            $cat = new Category(); 
        } 
                
        //if (!$status) { $status = "top"; }
        if (!$limit) { $limit = 10; }
                    
        if ($status) { $filter['post_status = %s'] = $status; }
        if ($user) { $filter['post_author = %d'] = $current_user->getUserIdFromName($cage->get->testUsername('user'));  }
        if ($tag) { $filter['post_tags LIKE %s'] = '%' . $tag . '%'; }
        if ($category && (FRIENDLY_URLS == "true")) { $filter['post_category = %d'] = $cat->getCatId($category); }
        if ($category && (FRIENDLY_URLS == "false")) { $filter['post_category = %d'] = $category; }
        if ($search && $plugins->plugin_active('search')) { 
            $prepared_search = prepare_search_filter($search); 
            extract($prepared_search);
            $orderby = "post_date DESC";    // override "relevance DESC" so the RSS feed updates with the latest related terms. 
        }
        
        $plugins->pluginHook('post_rss_feed');
        
        $feed           = new RSS();
        $feed->title    = SITE_NAME;
        $feed->link     = BASEURL;
        
        if ($status == 'new') { $feed->description = $lang["submit_rss_latest_from"] . " " . SITE_NAME; }
        elseif ($status == 'top') { $feed->description = $lang["submit_rss_top_stories_from"] . " " . SITE_NAME; }
        elseif ($user) { $feed->description = $lang["submit_rss_stories_from_user"] . " " . $user; }
        elseif ($tag) { $feed->description = $lang["submit_rss_stories_tagged"] . " " . $tag; }
        elseif ($category && (FRIENDLY_URLS == "true")) { $feed->description = $lang["submit_rss_stories_in_category"] . " " . $category; }
        elseif ($category && (FRIENDLY_URLS == "false")) { $feed->description = $lang["submit_rss_stories_in_category"] . " " . $cat->getCatName($category); }
        elseif ($search) { $feed->description = $lang["submit_rss_stories_search"] . " " . stripslashes($search); }
                
        if (!isset($filter))  $filter = array();
        $prepared_array = $this->filter($filter, $limit, false, $select);
        
        $results = $this->getPosts($prepared_array);

        if ($results) {
            foreach ($results as $result) 
            {
                $item = new RSSItem();
                $item->title = stripslashes(urldecode($result->post_title));
                $item->link  = url(array('page'=>$result->post_id)); 
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
        
        /* This function pulls all the different statuses from current links, 
        or adds some defaults if not present.*/

        $unique_statuses = array();
        $sql = "SELECT DISTINCT post_status FROM " . TABLE_POSTS;
        $statuses = $db->get_results($db->prepare($sql));
        if ($statuses) {
            foreach ($statuses as $status) {
                array_push($unique_statuses, $status->post_status);
            }
        }
        // Some essentials if not already included:
        if (!in_array('new', $unique_statuses)) { array_push($unique_statuses, 'new'); }
        if (!in_array('top', $unique_statuses)) { array_push($unique_statuses, 'top'); }
        if (!in_array('buried', $unique_statuses)) { array_push($unique_statuses, 'buried'); }
        if (!in_array('pending', $unique_statuses)) { array_push($unique_statuses, 'pending'); }
        
        if ($unique_statuses) { return $unique_statuses; } else { return false; }
    }
    
    
    /**
     * Scrapes the title from the page being submitted
     */
    public function fetch_title($url)
    {
        global $cage, $lang;
        
        require_once(EXTENSIONS . 'SWCMS/class.httprequest.php');
        
        if ($url != 'http://' && $url != ''){
            $r = new HTTPRequest($url);
            $string = $r->DownloadToString();
        } else {
            $string = '';
        }
        
        if (preg_match('/charset=([a-zA-Z0-9-_]+)/i', $string , $matches)) {
            $encoding=trim($matches[1]);
            //you need iconv to encode to utf-8
            if (function_exists("iconv"))
            {
                if (strcasecmp($encoding, 'utf-8') != 0) {
                    //convert the html code into utf-8 whatever encoding it is using
                    $string=iconv($encoding, 'UTF-8//IGNORE', $string);
                }
            }
        }
            
        
        if (preg_match("'<title>([^<]*?)</title>'", $string, $matches)) {
            $title = trim($matches[1]);
        } else {
            $title = $lang["submit_form_not_found"];
        }
        
        return $title;
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
            // Filters page to "new" stories only
            $filter['post_status = %s'] = 'new'; 
            $rss = "<a href='" . url(array('page'=>'rss', 'status'=>'new')) . "'>";
            $rss .= " <img src='" . BASEURL . "content/themes/" . THEME . "images/rss_10.png'></a>";
            $page_title = $lang["post_breadcrumbs_latest"] . $rss;
        } 
        elseif ($this->getUseLatest())
        {
            // Filters page to "top" stories only
            $filter['post_status = %s'] = 'top';
            $rss = "<a href='" . url(array('page'=>'rss')) . "'>";
            $rss .= " <img src='" . BASEURL . "content/themes/" . THEME . "images/rss_10.png'></a>";
            $page_title = $lang["post_breadcrumbs_top"] . $rss;
        }
        else
        {
            // Filters page to "all" stories
            $rss = "<a href='" . url(array('page'=>'rss')) . "'>";
            $rss .= " <img src='" . BASEURL . "content/themes/" . THEME . "images/rss_10.png'></a>";
            $page_title = $lang["post_breadcrumbs_all"] . $rss;
        }
        
        $plugins->pluginHook('post_list_filter');
        
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
        
        if (!$trackback) { return false; } // No trackback url found
        
        // Clean up the title and description...
        $title = htmlspecialchars(strip_tags($post->getTitle()));
        $title = (strlen($post->getTitle()) > 150) ? substr($post->getTitle(), 0, 150) . '...' : $post->getTitle();
        $excerpt = strip_tags($post->getContent());
        $excerpt = (strlen($excerpt) > 200) ? substr($excerpt, 0, 200) . '...' : $excerpt;

        if ($this->ping($trackback, url(array('page'=>$post->getId())), $title, $excerpt)) {
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
        
        include_once(EXTENSIONS . 'SWCMS/class.httprequest.php');
        
        // Fetch the content of the original url...
        $url = $post->getOrigUrl();
        
        if ($url != 'http://' && $url != ''){
        $r = new HTTPRequest($url);
        $content = $r->DownloadToString();
        } else {
            $content = '';
        }
        
        $trackback = '';
        
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
            return '$post->ping: Tring to send a trackback but can\'t connect to: ' . $tb_sock . '.';
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
