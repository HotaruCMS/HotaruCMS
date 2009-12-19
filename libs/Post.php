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
    
    // general
    protected $postsPerPage     = 10;           // Number of posts to show on list pages
    protected $allowableTags    = '';           // allowable HTML tags
    protected $useSummary       = true;         // truncate the post description on list pages


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
                $hotaru->id = $post->post_id;
                $hotaru->deletePost();
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
}
?>