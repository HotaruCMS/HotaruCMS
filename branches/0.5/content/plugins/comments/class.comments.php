<?php
/**
 * name: Comments
 * description: Class to manage comments
 * file: /plugins/comments/class.comments.php
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
    
class Comment {    

    var $comment_id = 0;
    var $comment_parent = 0;
    var $comment_post_id = 0;
    var $comment_author = 0;
    var $comment_date = '';
    var $comment_votes = 0;
    var $comment_content = '';
    var $comment_subscribe = 0;
    var $comment_depth = 0;         // used for nesting comments
    var $comment_allowable_tags = '';
    var $comment_form = '';
    var $comment_avatars = '';
    var $comment_voting = '';
    
    var $comment_vars = array();

    /**
     * PHP __set Magic Method
     * Plugins use this to set additonal member variables
     *
     * @param str $name - the name of the member variable
     * @param mixed $value - the value to set it to.
     */
    function __set($name, $value)
    {
        $this->comment_vars[$name] = $value;
    }
        
        
    /**
     * PHP __get Magic Method
     * Plugins use this to read values of additonal member variables
     *
     * @param str $name - the name of the member variable
     */
    function __get($name)
    {
        if (array_key_exists($name, $this->comment_vars)) {
            return $this->comment_vars[$name];
        }
    }
    
    
    /**
     * Get comments settings
     *
     * @return array - of comments settings
     */
    function get_comment_settings()
    {
        global $plugin;
        
        // Get settings from the database if they exist...
        $comment_settings = unserialize($plugin->plugin_settings('comments', 'comment_settings'));         
        return $comment_settings;
    }


    /**
     * Read all comment parents
     *
     * @param int $post_id - the id of the post this comment is on
     * @param array|false
     */
    function read_all_parents($post_id)
    {
        global $db, $post;
        
        $sql = "SELECT * FROM " . TABLE_COMMENTS . " WHERE comment_post_id = %d AND comment_parent = %d";
        $parents = $db->get_results($db->prepare($sql, $post->post_id, 0));
        
        if($parents) { return $parents; } else { return false; }
    }


    /**
     * Read all comment children
     *
     * @param int $post_id - the id of the post this comment is on
     * @param array|false
     */
    function read_all_children($post_id, $parent)
    {
        global $db, $post;
        
        $sql = "SELECT * FROM " . TABLE_COMMENTS . " WHERE comment_post_id = %d AND comment_parent = %d";
        $children = $db->get_results($db->prepare($sql, $post->post_id, $parent));
        
        if($children) { return $children; } else { return false; }
    }
    
    
    /**
     * Read comment
     *
     * @param array $comment
     */
    function read_comment($comment)
    {
        $this->comment_id           = $comment->comment_id;
        $this->comment_parent       = $comment->comment_parent;
        $this->comment_post_id      = $comment->comment_post_id;
        $this->comment_author       = $comment->comment_user_id;
        $this->comment_date         = $comment->comment_date;
        $this->comment_votes        = $comment->comment_votes;
        $this->comment_content      = urldecode($comment->comment_content);
        $this->comment_subscribe    = $comment->comment_subscribe;
    }
    
    
    /**
     * Add comment
     */
    function add_comment()
    {
        global $db, $current_user;
            
        $sql = "INSERT INTO " . TABLE_COMMENTS . " SET comment_post_id = %d, comment_user_id = %d, comment_parent = %d, comment_date = CURRENT_TIMESTAMP, comment_content = %s, comment_subscribe = %d, comment_updateby = %d";
                
        $db->query($db->prepare($sql, $this->comment_post_id, $this->comment_author, $this->comment_parent, urlencode(trim($this->comment_content)), $this->comment_subscribe, $current_user->id));
        
        return true;
    }
    
}
?>