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
    var $comment_type = 'newcomment';   // or "editcomment"
    var $comment_subscribe = 0;
    var $comment_levels = 0;         // max nesting levels
    var $comment_depth = 0;         // this nesting level
    var $comment_email = '';
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
     * Count comments
     *
     * @return string - text to show in the link, e.g. "3 comments"
     */
    function count_comments()
    {
        global $db, $post, $lang;
        
        $sql = "SELECT COUNT(comment_id) FROM " . TABLE_COMMENTS . " WHERE comment_post_id = %d";
        $num_comments = $db->get_var($db->prepare($sql, $post->post_id));
        
        if ($num_comments == 1) {
            return "1 " . $lang['comments_comments_singular_link'];
        } elseif ($num_comments > 1) {
            return $num_comments . " " . $lang['comments_comments_plural_link'];
        } else {
            return $lang['comments_comments_none_link'];
        }
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
     *
     * @return true
     */
    function add_comment()
    {
        global $db, $current_user;
            
        $sql = "INSERT INTO " . TABLE_COMMENTS . " SET comment_post_id = %d, comment_user_id = %d, comment_parent = %d, comment_date = CURRENT_TIMESTAMP, comment_content = %s, comment_subscribe = %d, comment_updateby = %d";
                
        $db->query($db->prepare($sql, $this->comment_post_id, $this->comment_author, $this->comment_parent, urlencode(trim($this->comment_content)), $this->comment_subscribe, $current_user->id));
        
        return true;
    }
    

    /**
     * Edit comment
     *
     * @return true
     */
    function edit_comment()
    {
        global $db, $current_user;
            
        $sql = "UPDATE " . TABLE_COMMENTS . " SET comment_content = %s, comment_subscribe = %d, comment_updateby = %d";
                
        $db->query($db->prepare($sql, urlencode(trim(stripslashes($this->comment_content))), $this->comment_subscribe, $current_user->id));
        
        return true;
    }

    /**
     * Unsubscribe from a thread
     *
     * @param int $post_id
     * @return true
     */
    function unsubscribe($post_id)
    {
        global $db, $current_user, $post;
        
        $post->read_post($post_id);
            
        $sql = "UPDATE " . TABLE_COMMENTS . " SET comment_subscribe = %d WHERE comment_post_id = %d AND comment_user_id = %d";
        $db->query($db->prepare($sql, 0, $post->post_id, $current_user->id));
               
        // Check if the current_user is the post author
        if ($post->post_author == $current_user->id) {
        // Check if the user subscribed to comments as a submitter
            if ($post->post_subscribe == 1) { 
                $sql = "UPDATE " . TABLE_POSTS . " SET post_subscribe = %d WHERE post_id = %d AND post_author = %d";
                $db->query($db->prepare($sql, 0, $post->post_id, $current_user->id));
            } 
        }
        return true;
    }
    
    
    /**
     * Update thread subscription 
     *
     * @param int $post_id
     * @return true
     */
    function update_subscribe($post_id)
    {
        global $db, $current_user, $post, $comment;
        
        if ($comment->comment_subscribe == 1)
        {
            $sql = "UPDATE " . TABLE_COMMENTS . " SET comment_subscribe = %d WHERE comment_post_id = %d AND comment_user_id = %d";
            $db->query($db->prepare($sql, 1, $post->post_id, $current_user->id));
        } 
        else 
        {
            $this->unsubscribe($post_id);
        }
    }
    
    
    /**
    * Send an email to thread subscribers
    *
    * @param int $post_id
    */
    function email_comment_subscribers($post_id)
    {
        global $db, $comment, $post, $userbase;
        
        $post->read_post($post_id);
    
        // build a list of subscribers
        $subscriber_ids = array();
        
        // Get id of post author if subscribed
        if ($post->post_subscribe == 1) {
            array_push($subscriber_ids, $post->post_author); 
        }
        
        // Get ids of comment authors if subscribed
        $sql = "SELECT comment_user_id FROM " . TABLE_COMMENTS . " WHERE comment_subscribe = %d AND comment_post_id = %d";
        $comment_subscribers = $db->get_results($db->prepare($sql, 1, $post->post_id));
        if ($comment_subscribers) {
            foreach ($comment_subscribers as $comment_subscriber) {
                array_push($subscriber_ids, $comment_subscriber->comment_user_id); 
            }
        }
        
        // Use the ids to make an array of unique email addresses
        $subscribers = array();
        $subscriber_ids = array_unique($subscriber_ids);
        foreach ($subscriber_ids as $subscriber_id) {
            // remove the current comment author so he/she doesn't get emailed his own comment
            //if ($subscriber_id != $comment->comment_author) {
                $email = $db->get_var($db->prepare("SELECT user_email FROM " . TABLE_USERS . " WHERE user_id = %d", $subscriber_id));
                array_push($subscribers, $email);
            //}
        }
        
        $send_to = trim(implode(",", $subscribers),",");
        
        $comment_author = $userbase->get_username($comment->comment_author);
        
        $subject = $comment_author . ' has commented on ' . $post->post_title;
        
        $message =  $comment_author . " has commented on a story you are subscribed to at " . SITE_NAME . ": \r\n\r\n";
        $message .= "Story Title: " . $post->post_title . "\r\n"; 
        $message .= "Story Link: " . url(array('page'=>$post->post_id)) . "\r\n\r\n";
        $message .= "Comment: " . $comment_author . "\r\n\r\n";
        $message .= "************************ \r\n";
        $message .= "Do not reply to this email. Please visit the above link and comment there. \r\n";
        $message .= "To unsubscribe, uncheck the \"Subscribe to comments\" box and submit an empty comment. ";
        
        $from = SITE_EMAIL;
        $to = $comment->comment_email;  // send email to address specified in Comment Settings; 
        if($send_to != "") {
            $bcc = "\r\nBCC: " . $send_to;    // BCC individual addresses;
        } else {
            $bcc = "";
        }
        $headers = "From: " . $from . $bcc . "\r\nReply-To: " . $from . "\r\nX-Priority: 3\r\n";
    
        /*
        echo "to: " . $to . "<br />";
        echo "bcc: " . $bcc . "<br />";
        echo "subject: " . $subject . "<br />";
        echo "message: " . $message . "<br />";
        echo "headers: " . $headers . "<br />";
        exit;
        */
    
        @mail($to, $subject, $message, $headers);
    }
    
}
?>