<?php
/**
 * The Comment class contains some useful methods for using comments
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
    
class Comment
{
    public $db;                         // database object
    public $cage;                       // Inspekt object
    public $hotaru;                     // Hotaru object
    public $lang            = array();  // stores language file content
    public $plugins;                    // PluginFunctions object
    public $current_user;               // UserBase object

    protected $id = 0;
    protected $parent = 0;
    protected $postId = 0;
    protected $author = 0;
    protected $date = '';
    protected $status = 'approved';
    protected $votes = 0;
    protected $content = '';
    protected $type = 'newcomment';   // or "editcomment"
    protected $subscribe = 0;
    protected $levels = 0;         // max nesting levels
    protected $depth = 0;         // this nesting level
    protected $email = '';
    protected $allowableTags = '';
    protected $setPending = '';
    protected $form = '';
    protected $avatars = '';
    protected $voting = '';
    
    public $vars = array();


    /**
     * Build a $plugins object containing $db and $cage
     */
    public function __construct($hotaru)
    {
        $this->hotaru           = $hotaru;
        $this->db               = $hotaru->db;
        $this->cage             = $hotaru->cage;
        $this->lang             = &$hotaru->lang;   // reference to main lang array
        $this->plugins          = $hotaru->plugins;
        $this->current_user     = $hotaru->current_user;
    }
    
    
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
    public function __get($var)
    {
        return $this->$var;
    }

    
    /**
     * Count comments
     *
     * @param bool $link - true used for comment links, false for header of comment tree
     * @return string - text to show in the link, e.g. "3 comments"
     */
    function countComments($link = true)
    {
        $sql = "SELECT COUNT(comment_id) FROM " . TABLE_COMMENTS . " WHERE comment_post_id = %d AND comment_status = %s";
        $num_comments = $this->db->get_var($this->db->prepare($sql, $this->hotaru->post->id, 'approved'));
        
        if ($num_comments == 1) {
            return "1 " . $this->lang['comments_singular_link'];
        } elseif ($num_comments > 1) {
            return $num_comments . " " . $this->lang['comments_plural_link'];
        } else {
            if (!$link) { 
                return $this->lang['comments_leave_comment'];  // shows "Leave a comment" above comment form when no comments
            }
            else
            {
                return $this->lang['comments_none_link']; // Shows "No comments"
            }
        }
    }
    
    
    /**
     * Read all comment parents
     *
     * @param int $post_id - the id of the post this comment is on
     * @param array|false
     */
    function readAllParents($post_id)
    {
        $sql = "SELECT * FROM " . TABLE_COMMENTS . " WHERE comment_post_id = %d AND comment_parent = %d ORDER BY comment_date";
        $parents = $this->db->get_results($this->db->prepare($sql, $post_id, 0));
        
        if($parents) { return $parents; } else { return false; }
    }


    /**
     * Read all comment children
     *
     * @param int $parent - the id of the parent comment
     * @param array|false
     */
    function readAllChildren($parent)
    {
        $sql = "SELECT * FROM " . TABLE_COMMENTS . " WHERE comment_parent = %d ORDER BY comment_date";
        $children = $this->db->get_results($this->db->prepare($sql, $parent));
        
        if($children) { return $children; } else { return false; }
    }
    
    
    /**
     * Get comment from database
     *
     * @param int $comment_id
     * @return array|false
     */
    function getComment($comment_id)
    {
        $sql = "SELECT * FROM " . TABLE_COMMENTS . " WHERE comment_id = %d";
        $comment = $this->db->get_row($this->db->prepare($sql, $comment_id));
        
        if($comment) { return $comment; } else { return false; }
    }
    
    
    /**
     * Read comment
     *
     * @param array $comment
     */
    function readComment($comment)
    {
        $this->id = $comment->comment_id;
        $this->parent = $comment->comment_parent;
        $this->postId = $comment->comment_post_id;
        $this->author = $comment->comment_user_id;
        $this->date = $comment->comment_date;
        $this->status = $comment->comment_status;
        $this->votes = $comment->comment_votes;
        $this->content = urldecode($comment->comment_content);
        $this->subscribe = $comment->comment_subscribe;
        
        $this->plugins->pluginHook('comment_read_comment');
    }
    
    
    /**
     * Add comment
     *
     * @return true
     */
    function addComment()
    {
        $this->plugins->pluginHook('comment_pre_add_comment');  // Akismet uses this to change the status
        
        $can_comment = $this->hotaru->current_user->getPermission('can_comment'); // This was already check, but Akismet undoes it! So we do it again.

        if ($can_comment == 'mod') { $this->status = 'pending'; } // forces all to 'pending' if user's comments are moderated
        
        if ($this->setPending == 'checked') { $status = 'pending'; } else { $status = $this->status; } // forces all to 'pending' if setPending enabled
                
        $sql = "INSERT INTO " . TABLE_COMMENTS . " SET comment_post_id = %d, comment_user_id = %d, comment_parent = %d, comment_date = CURRENT_TIMESTAMP, comment_status = %s, comment_content = %s, comment_subscribe = %d, comment_updateby = %d";
                
        $this->db->query($this->db->prepare($sql, $this->postId, $this->author, $this->parent, $status, urlencode(trim(stripslashes($this->content))), $this->subscribe, $this->current_user->id));
        
        $last_insert_id = $this->db->get_var($this->db->prepare("SELECT LAST_INSERT_ID()"));
        
        $this->id = $last_insert_id;
        $this->vars['last_insert_id'] = $last_insert_id;    // make it available outside this class
        
        $this->plugins->pluginHook('comment_post_add_comment');
        
        return true;
    }
    

    /**
     * Edit comment
     *
     * @return true
     */
    function editComment()
    {
        $sql = "UPDATE " . TABLE_COMMENTS . " SET comment_status = %s, comment_content = %s, comment_subscribe = %d, comment_updateby = %d WHERE comment_id = %d";
        $this->db->query($this->db->prepare($sql, $this->status, urlencode(trim(stripslashes($this->content))), $this->subscribe, $this->current_user->id, $this->id));
        
        $this->plugins->pluginHook('comment_update_comment');
        
        return true;
    }


    /**
     * Physically delete a comment from the database 
     *
     */    
    public function deleteComment()
    {
        $sql = "DELETE FROM " . TABLE_COMMENTS . " WHERE comment_id = %d";
        $this->db->query($this->db->prepare($sql, $this->id));
        
        // delete any votes for this comment
        $sql = "DELETE FROM " . TABLE_COMMENTVOTES . " WHERE cvote_comment_id = %d";
        $this->db->query($this->db->prepare($sql, $this->id));
        
        $this->plugins->pluginHook('comment_delete_comment');
    }
    
    
    /**
     * Recurse through comment tree, deleting all
     *
     * @param int $comment_id - id of current comment
     * @return bool
     */
    public function deleteCommentTree($comment_id)
    {
        while ($children = $this->readAllChildren($comment_id)) {
            foreach ($children as $child) {
                $this->readComment($child);
                $this->deleteComment();
                if ($this->deletecommentTree($this->id)) {
                    return true;
                }
            }
            
            return false;
        }
    }
    
    
    /**
     * Recurse through comment tree, setting all to 'pending'
     *
     * @param int $comment_id - id of current comment
     * @return bool
     */
    public function setPendingCommentTree($comment_id)
    {
        while ($children = $this->readAllChildren($comment_id)) {
            print_r($children);
            foreach ($children as $child) {
                $this->readComment($child);
                $this->status = 'pending';
                $this->editComment();
                if ($this->setPendingCommentTree($this->id)) {
                    return true;
                }
            }
            
            return false;
        }
    }
    
    
    /**
     * Unsubscribe from a thread
     *
     * @param int $post_id
     * @return true
     */
    function unsubscribe($post_id)
    {
        $this->hotaru->post->readPost($post_id);
            
        $sql = "UPDATE " . TABLE_COMMENTS . " SET comment_subscribe = %d WHERE comment_post_id = %d AND comment_user_id = %d";
        $this->db->query($this->db->prepare($sql, 0, $this->hotaru->post->id, $this->current_user->id));
               
        // Check if the current_user is the post author
        if ($this->hotaru->post->author == $this->current_user->id) {
        // Check if the user subscribed to comments as a submitter
            if ($this->hotaru->post->subscribe == 1) { 
                $sql = "UPDATE " . TABLE_POSTS . " SET post_subscribe = %d WHERE post_id = %d AND post_author = %d";
                $this->db->query($this->db->prepare($sql, 0, $this->hotaru->post->id, $this->current_user->id));
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
    function updateSubscribe($post_id)
    {
        if ($this->comment_subscribe == 1)
        {
            $sql = "UPDATE " . TABLE_COMMENTS . " SET comment_subscribe = %d WHERE comment_post_id = %d AND comment_user_id = %d";
            $this->db->query($this->db->prepare($sql, 1, $this->hotaru->post->id, $this->current_user->id));
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
    function emailCommentSubscribers($post_id)
    {
        $this->hotaru->post->readPost($post_id);
    
        // build a list of subscribers
        $subscriber_ids = array();
        
        // Get id of post author if subscribed
        if ($this->hotaru->post->subscribe == 1) {
            array_push($subscriber_ids, $this->hotaru->post->author);
        }
        
        // Get ids of comment authors if subscribed
        $sql = "SELECT comment_user_id FROM " . TABLE_COMMENTS . " WHERE comment_subscribe = %d AND comment_post_id = %d";
        $comment_subscribers = $this->db->get_results($this->db->prepare($sql, 1, $this->hotaru->post->id));
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
            if ($subscriber_id != $this->author) {
                $email = $this->db->get_var($this->db->prepare("SELECT user_email FROM " . TABLE_USERS . " WHERE user_id = %d", $subscriber_id));
                array_push($subscribers, $email);
            }
        }
        
        $send_to = trim(implode(",", $subscribers),",");
        
        $comment_author = $this->current_user->getUserNameFromId($this->author);
        
        //clean up content:
        $story_title = stripslashes(html_entity_decode(urldecode($this->hotaru->post->title), ENT_QUOTES,'UTF-8'));
        $comment_content = stripslashes($this->hotaru->comment->content);
        
        $subject = $comment_author . ' has commented on ' . $story_title;
        
        $message =  $comment_author . " has commented on a story you are subscribed to at " . SITE_NAME . ": \r\n\r\n";
        $message .= "Story Title: " . $story_title . "\r\n"; 
        $message .= "Story Link: " . $this->hotaru->url(array('page'=>$this->hotaru->post->id)) . "\r\n\r\n";
        $message .= "Comment: " . $comment_content . "\r\n\r\n";
        $message .= "************************ \r\n";
        $message .= "Do not reply to this email. Please visit the above link and comment there. \r\n";
        $message .= "To unsubscribe, uncheck the \"Subscribe to comments\" box and submit an empty comment. ";
        
        $from = SITE_EMAIL;
        $to = $this->email;  // send email to address specified in Comment Settings; 
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