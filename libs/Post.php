<?php
/**
 * Post functions
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
class Post
{
    // individual posts
    protected $id = 0;
    protected $origUrl          = '';           // original url for the submitted post
    protected $domain           = '';           // the domain of the submitted url
    protected $title            = '';           // post title
    protected $content          = '';           // post description
    protected $contentLength    = 50;           // default min characters for content
    protected $summary          = '';           // truncated post description
    protected $summaryLength    = 200;          // default max characters for summary
    protected $status           = 'unsaved';    // initial status before database entry
    protected $author           = 0;            // post author
    protected $url              = '';           // post slug (needs BASEURL and category attached)
    protected $date             = '';           // post submission date
    protected $subscribe        = 0;            // is the post author subscribed to comments?


    /**
     * Access modifier to set protected properties
     */
    public function __set($var, $val)
    {
        $this->$var = $val;  
    }
    
    
    /**
     * Access modifier to get protected properties
     */
    public function &__get($var)
    {
        return $this->$var;
    }
    
    
    /**
     * Get all the settings for the current post
     *
     * @param int $post_id - Optional row from the posts table in the database
     * @param array $post_row - a post already fetched from the db, just needs reading
     * @return bool
     */    
    public function readPost($hotaru, $post_id = 0, $post_row = NULL)
    {
        $hotaru->vars['post_error'] = false; 
        
        if (!$post_id && !$post_row) {
            $post_id = $this->id;   // use the id already assigned to $hotaru->post
        }
        
        if ($post_id != 0) {
            $post_row = $hotaru->getPost($post_id);
            if (!$post_row) { $hotaru->vars['post_error'] = true; return false; }
        }
        
        if ($post_row) {
            $this->id = $post_row->post_id;
            $this->title = stripslashes(urldecode($post_row->post_title));
            $this->content = stripslashes(urldecode($post_row->post_content));
            $this->origUrl = urldecode($post_row->post_orig_url);
            $this->status = $post_row->post_status;
            $this->author = $post_row->post_author;
            $this->url = urldecode($post_row->post_url);
            $this->date = $post_row->post_date;
            $this->subscribe = $post_row->post_subscribe;
            
            $this->vars['post_row'] = $post_row;    // make available to plugins
            
            $hotaru->pluginHook('post_read_post');
                        
            return true;
        } else {
            return false;
        }
        
    }
    
    
    /**
     * Gets a single post from the database
     *
     * @param int $post_id - post id of the post to get
     * @return array|false
     */    
    public function getPost($hotaru, $post_id = 0)
    {
        // Build SQL
        $query = "SELECT * FROM " . TABLE_POSTS . " WHERE post_id = %d ORDER BY post_date DESC";
        $sql = $hotaru->db->prepare($query, $post_id);
        
        // Create temp cache array
        if (!isset($this->hotaru->vars['tempPostCache'])) { $hotaru->vars['tempPostCache'] = array(); }

        // If this query has already been read once this page load, we should have it in memory...
        if (array_key_exists($sql, $hotaru->vars['tempPostCache'])) {
            // Fetch from memory
            $post = $hotaru->vars['tempPostCache'][$sql];
        } else {
            // Fetch from database
            $post = $hotaru->db->get_row($sql);
            $hotaru->vars['tempPostCache'][$sql] = $post;
        }

        if ($post) { return $post; } else { return false; }
    }
    
    
    /**
     * Add a post to the database
     *
     * @return true
     */    
    public function addPost($hotaru)
    {
        $sql = "INSERT INTO " . TABLE_POSTS . " SET post_orig_url = %s, post_domain = %s, post_title = %s, post_url = %s, post_content = %s, post_status = %s, post_author = %d, post_date = CURRENT_TIMESTAMP, post_subscribe = %d, post_updateby = %d";
        
        $hotaru->db->query($hotaru->db->prepare($sql, urlencode($this->origUrl), urlencode($this->domain), urlencode(trim($this->title)), urlencode(trim($this->url)), urlencode(trim($this->content)), $this->status, $this->author, $this->subscribe, $hotaru->currentUser->id));
        
        $last_insert_id = $hotaru->db->get_var($hotaru->db->prepare("SELECT LAST_INSERT_ID()"));
        
        $this->id = $last_insert_id;
        $this->vars['last_insert_id'] = $last_insert_id;    // make it available outside this class
                
        $hotaru->pluginHook('post_add_post');
        
        return true;
    }
    
    
    /**
     * Update a post in the database
     *
     * @return true
     */    
    public function updatePost($hotaru)
    {
        if (strstr($this->origUrl, BASEURL)) {
            // original url contains our base url, so it must be an "editorial" post.
            // Therefore, it's essential we rebuild this source url to match the updated post title to avoid errors:
            $this->origUrl = $hotaru->url(array('page'=>$this->id)); // update the url with the real one
        }
        
        $parsed = parse_url($this->origUrl);
        if (isset($parsed['scheme'])){ $this->domain = $parsed['scheme'] . "://" . $parsed['host']; }
        
        $sql = "UPDATE " . TABLE_POSTS . " SET post_orig_url = %s, post_domain = %s, post_title = %s, post_url = %s, post_content = %s, post_status = %s, post_author = %d, post_subscribe = %d, post_updateby = %d WHERE post_id = %d";
        
        $hotaru->db->query($hotaru->db->prepare($sql, urlencode($this->origUrl), urlencode($this->domain), urlencode(trim($this->title)), urlencode(trim($this->url)), urlencode(trim($this->content)), $this->status, $this->author, $this->subscribe, $hotaru->currentUser->id, $this->id));
        
        $hotaru->post->id = $this->id; // a small hack to get the id for use in plugins.
        $hotaru->pluginHook('post_update_post');
        
        return true;
    }
    
    
    /**
     * Physically delete a post from the database 
     *
     * There's a plugin hook in here to delete their parts, e.g. votes, coments, tags, etc.
     */    
    public function deletePost($hotaru)
    {
        $sql = "DELETE FROM " . TABLE_POSTS . " WHERE post_id = %d";
        $hotaru->db->query($hotaru->db->prepare($sql, $this->id));
        
        $hotaru->post->id = $this->id; // a small hack to get the id for use in plugins.
        $hotaru->pluginHook('post_delete_post');
    }
    
    
    /**
     * Update a post's status
     *
     * @param string $status
     * @param int $post_id (optional)
     * @return true
     */    
    public function changePostStatus($hotaru, $status = "processing", $post_id = 0)
    {
        $this->status = $status;
        if (!$post_id) { $post_id = $this->id; }
            
        $sql = "UPDATE " . TABLE_POSTS . " SET post_status = %s WHERE post_id = %d";
        $hotaru->db->query($hotaru->db->prepare($sql, $this->status, $post_id));
        
        $hotaru->post->id = $post_id; // a small hack to get the id for use in plugins.
        $hotaru->pluginHook('post_change_status');
                
        return true;
    }
    
    
    /**
     * Checks for existence of a url
     *
     * @return array|false - array of posts
     */    
    public function urlExists($hotaru, $url = '')
    {
        $sql = "SELECT post_id, post_status FROM " . TABLE_POSTS . " WHERE post_orig_url = %s";
        $posts = $hotaru->db->get_results($hotaru->db->prepare($sql, urlencode($url)));

        if (!$posts) { return false; }
        
        // we know there's at least one post with the same url, so if it's processing, let's delete it:
        foreach ($posts as $post) {
            if ($post->post_status == 'processing') {
                $hotaru->post->id = $post->post_id;
                $hotaru->deletePost($hotaru);
            }
        }

        // One last check to see if a post is present:
        $sql = "SELECT count(post_id) FROM " . TABLE_POSTS . " WHERE post_orig_url = %s";
        $posts = $hotaru->db->get_var($hotaru->db->prepare($sql, urlencode($url)));
        
        if ($posts > 0) { return $posts; } else { return false; }
    }
    
    
    /**
     * Checks for existence of a post title
     *
     * @param str $title
     * @return int - id of post with matching title
     */
    public function titleExists($hotaru, $title = '')
    {
        $title = trim($title);
        $sql = "SELECT post_id FROM " . TABLE_POSTS . " WHERE post_title = %s";
        $post_id = $hotaru->db->get_var($hotaru->db->prepare($sql, urlencode($title)));
        if ($post_id) { return $post_id; } else { return false; }
    }
    
    
    /**
     * Checks for existence of a post with given post_url
     *
     * @param str $post_url (slug)
     * @return int - id of post with matching url
     */
    public function isPostUrl($hotaru, $post_url = '')
    {
        $sql = "SELECT post_id FROM " . TABLE_POSTS . " WHERE post_url = %s";
        $post_id = $hotaru->db->get_var($hotaru->db->prepare($sql, urlencode($post_url)));
        if ($post_id) { return $post_id; } else { return false; }
    }
    
    
    /**
     * Count how many approved posts a user has had
     *
     * @param int $userid (optional)
     * @return int 
     */
    public function postsApproved($hotaru, $user_id = 0)
    {
        if (!$user_id) { $user_id = $hotaru->currentUser->id; }
        
        $sql = "SELECT COUNT(*) FROM " . TABLE_POSTS . " WHERE (post_status = %s || post_status = %s) AND post_author = %d";
        $count = $hotaru->db->get_var($hotaru->db->prepare($sql, 'top', 'new', $user_id));
        
        return $count;
        
    }
    
    
    /**
     * Count posts in the last X hours/minutes for this user
     *
     * @param int $hours
     * @param int $minutes
     * @param int $user_id (optional)
     * @return int 
     */
    public function countPosts($hotaru, $hours = 0, $minutes = 0, $user_id = 0)
    {
        if (!$user_id) { $user_id = $hotaru->currentUser->id; }
        if ($hours) { 
            $time_ago = "-" . $hours . " Hours";
        } else {
            $time_ago = "-" . $minutes . " minutes";
        } 
        
        $start = date('YmdHis', strtotime("now"));
        $end = date('YmdHis', strtotime($time_ago));
        $sql = "SELECT COUNT(post_id) FROM " . TABLE_POSTS . " WHERE post_archived = %s AND post_author = %d AND (post_date >= %s AND post_date <= %s)";
        $count = $hotaru->db->get_var($hotaru->db->prepare($sql, 'N', $user_id, $end, $start));
        
        return $count;
    }
    
    
    /**
     * Get Unique Post Statuses
     *
     * @return array|false
     */
    public function getUniqueStatuses($hotaru) 
    {
        /* This function pulls all the different statuses from current links, 
        or adds some defaults if not present.*/

        $unique_statuses = array();
        
        // Some essentials:
        array_push($unique_statuses, 'new');
        array_push($unique_statuses, 'top');
        array_push($unique_statuses, 'pending');
        array_push($unique_statuses, 'buried');
        array_push($unique_statuses, 'processing');
        
        // Add any other statuses already in use:
        $sql = "SELECT DISTINCT post_status FROM " . TABLE_POSTS;
        $statuses = $hotaru->db->get_results($hotaru->db->prepare($sql));
        if ($statuses) {
            foreach ($statuses as $status) {
                if ($status->post_status && !in_array($status->post_status, $unique_statuses)) {
                    array_push($unique_statuses, $status->post_status);
                }
            }
        }
        
        if ($unique_statuses) { return $unique_statuses; } else { return false; }
    }
}
?>