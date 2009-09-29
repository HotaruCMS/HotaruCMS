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
    
class Comment {    

    protected $id = 0;
    protected $parent = 0;
    protected $postId = 0;
    protected $author = 0;
    protected $date = '';
    protected $votes = 0;
    protected $content = '';
    protected $type = 'newcomment';   // or "editcomment"
    protected $subscribe = 0;
    protected $levels = 0;         // max nesting levels
    protected $depth = 0;         // this nesting level
    protected $email = '';
    protected $allowableTags = '';
    protected $form = '';
    protected $avatars = '';
    protected $voting = '';
    
    public $vars = array();

    /**
     * PHP __set Magic Method
     * Plugins use this to set additonal member variables
     *
     * @param str $name - the name of the member variable
     * @param mixed $value - the value to set it to.
     */
    function __set($name, $value)
    {
        $this->vars[$name] = $value;
    }
        
        
    /**
     * PHP __get Magic Method
     * Plugins use this to read values of additonal member variables
     *
     * @param str $name - the name of the member variable
     */
    function __get($name)
    {
        if (array_key_exists($name, $this->vars)) {
            return $this->vars[$name];
        }
    }
    

    /**
     * Set comment ID
     *
     * @param int $id
     */    
    public function setId($id = 0)
    {
        $this->id = $id;
    }
    
    
    /**
     * Get comment id
     *
     * @return int
     */    
    public function getId()
    {
        return $this->id;
    }
    
    
    /**
     * Set comment parent
     *
     * @param int $parent
     */    
    public function setParent($parent = 0)
    {
        $this->parent = $parent;
    }
    
    
    /**
     * Get comment parent
     *
     * @return int
     */    
    public function getParent()
    {
        return $this->parent;
    }
    
    
    /**
     * Set post ID
     *
     * @param int $id
     */    
    public function setPostId($postid = 0)
    {
        $this->postId = $postid;
    }
    
    
    /**
     * Get post id
     *
     * @return int
     */    
    public function getPostId()
    {
        return $this->postId;
    }
    
    
    /**
     * Set author
     *
     * @param int $author
     */    
    public function setAuthor($author = 0)
    {
        $this->author = $author;
    }
    
    
    /**
     * Get author
     *
     * @return int
     */    
    public function getAuthor()
    {
        return $this->author;
    }
    
    
    /**
     * Set date
     *
     * @param string $date
     */    
    public function setDate($date = '')
    {
        $this->date = $date;
    }
    
    
    /**
     * Get date
     *
     * @return string
     */    
    public function getDate()
    {
        return $this->date;
    }
    
    
    /**
     * Set votes
     *
     * @param int $votes
     */    
    public function setVotes($votes = 0)
    {
        $this->votes = $votes;
    }
    
    
    /**
     * Get votes
     *
     * @return int
     */    
    public function getVotes()
    {
        return $this->votes;
    }
    
    
    /**
     * Set content
     *
     * @param string $content
     */    
    public function setContent($content = '')
    {
        $this->content = $content;
    }
    
    
    /**
     * Get content
     *
     * @return string
     */    
    public function getContent()
    {
        return $this->content;
    }
    
    
    /**
     * Set type
     *
     * @param string $type
     */    
    public function setType($type = '')
    {
        $this->type = $type;
    }
    
    
    /**
     * Get type
     *
     * @return string
     */    
    public function getType()
    {
        return $this->type;
    }
    
    
    /**
     * Set subscribe
     *
     * @param int $subscribe
     */    
    public function setSubscribe($subscribe = 0)
    {
        $this->subscribe = $subscribe;
    }
    
    
    /**
     * Get subscribe
     *
     * @return int
     */    
    public function getSubscribe()
    {
        return $this->subscribe;
    }
    
    /**
     * Set levels
     *
     * @param int $levels
     */    
    public function setLevels($levels = 0)
    {
        $this->levels = $levels;
    }
    
    
    /**
     * Get levels
     *
     * @return int
     */    
    public function getLevels()
    {
        return $this->levels;
    }
    
    /**
     * Set depth
     *
     * @param int $id
     */    
    public function setDepth($depth = 0)
    {
        $this->depth = $depth;
    }
    
    
    /**
     * Get depth
     *
     * @return int
     */    
    public function getDepth()
    {
        return $this->depth;
    }
    
    /**
     * Set email
     *
     * @param string $email
     */    
    public function setEmail($email = '')
    {
        $this->email = $email;
    }
    
    
    /**
     * Get email
     *
     * @return string
     */    
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * Set allowable tags
     *
     * @param string $tags
     */    
    public function setAllowableTags($tags = '')
    {
        $this->allowableTags = $tags;
    }
    
    
    /**
     * Get allowable tags
     *
     * @return string
     */    
    public function getAllowableTags()
    {
        return $this->allowableTags;
    }
    
    
    /**
     * Set form
     *
     * @param string $id
     */    
    public function setForm($form = '')
    {
        $this->form = $form;
    }
    
    
    /**
     * Get form
     *
     * @return string
     */    
    public function getForm()
    {
        return $this->form;
    }
    
    
    /**
     * Set avatars
     *
     * @param string $id
     */    
    public function setAvatars($avatars = '')
    {
        $this->avatars = $avatars;
    }
    
    
    /**
     * Get avatars
     *
     * @return string
     */    
    public function getAvatars()
    {
        return $this->avatars;
    }
    
    
    /**
     * Set voting
     *
     * @param string $id
     */    
    public function setVoting($voting = '')
    {
        $this->voting = $voting;
    }
    
    
    /**
     * Get voting
     *
     * @return string
     */    
    public function getVoting()
    {
        return $this->voting;
    }
    
    
    /**
     * Count comments
     *
     * @param bool $link - true used for comment links, false for header of comment tree
     * @return string - text to show in the link, e.g. "3 comments"
     */
    function countComments($link = true)
    {
        global $db, $post, $lang;
        
        $sql = "SELECT COUNT(comment_id) FROM " . TABLE_COMMENTS . " WHERE comment_post_id = %d";
        $num_comments = $db->get_var($db->prepare($sql, $post->getId()));
        
        if ($num_comments == 1) {
            return "1 " . $lang['comments_singular_link'];
        } elseif ($num_comments > 1) {
            return $num_comments . " " . $lang['comments_plural_link'];
        } else {
            if (!$link) { 
                return $lang['comments_leave_comment'];  // shows "Leave a comment" above comment form when no comments
            }
            else
            {
                return $lang['comments_none_link']; // Shows "No comments"
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
        global $db;
        
        $sql = "SELECT * FROM " . TABLE_COMMENTS . " WHERE comment_post_id = %d AND comment_parent = %d ORDER BY comment_date";
        $parents = $db->get_results($db->prepare($sql, $post_id, 0));
        
        if($parents) { return $parents; } else { return false; }
    }


    /**
     * Read all comment children
     *
     * @param int $post_id - the id of the post this comment is on
     * @param array|false
     */
    function readAllChildren($post_id, $parent)
    {
        global $db;
        
        $sql = "SELECT * FROM " . TABLE_COMMENTS . " WHERE comment_post_id = %d AND comment_parent = %d ORDER BY comment_date";
        $children = $db->get_results($db->prepare($sql, $post_id, $parent));
        
        if($children) { return $children; } else { return false; }
    }
    
    
    /**
     * Read comment
     *
     * @param array $comment
     */
    function readComment($comment)
    {
        $this->setId($comment->comment_id);
        $this->setParent($comment->comment_parent);
        $this->setPostId($comment->comment_post_id);
        $this->setAuthor($comment->comment_user_id);
        $this->setDate($comment->comment_date);
        $this->setVotes($comment->comment_votes);
        $this->setContent(urldecode($comment->comment_content));
        $this->setSubscribe($comment->comment_subscribe);
    }
    
    
    /**
     * Add comment
     *
     * @return true
     */
    function addComment()
    {
        global $db, $current_user;
            
        $sql = "INSERT INTO " . TABLE_COMMENTS . " SET comment_post_id = %d, comment_user_id = %d, comment_parent = %d, comment_date = CURRENT_TIMESTAMP, comment_content = %s, comment_subscribe = %d, comment_updateby = %d";
                
        $db->query($db->prepare($sql, $this->getPostId(), $this->getAuthor(), $this->getParent(), urlencode(trim(stripslashes($this->getContent()))), $this->getSubscribe(), $current_user->getId()));
        
        return true;
    }
    

    /**
     * Edit comment
     *
     * @return true
     */
    function editComment()
    {
        global $db, $current_user;
            
        $sql = "UPDATE " . TABLE_COMMENTS . " SET comment_content = %s, comment_subscribe = %d, comment_updateby = %d WHERE comment_id = %d";
        $db->query($db->prepare($sql, urlencode(trim(stripslashes($this->getContent()))), $this->getSubscribe(), $current_user->getId(), $this->getId()));
        
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
        
        $post->readPost($post_id);
            
        $sql = "UPDATE " . TABLE_COMMENTS . " SET comment_subscribe = %d WHERE comment_post_id = %d AND comment_user_id = %d";
        $db->query($db->prepare($sql, 0, $post->getId(), $current_user->getId()));
               
        // Check if the current_user is the post author
        if ($post->post_author == $current_user->getId()) {
        // Check if the user subscribed to comments as a submitter
            if ($post->getSubscribe() == 1) { 
                $sql = "UPDATE " . TABLE_POSTS . " SET post_subscribe = %d WHERE post_id = %d AND post_author = %d";
                $db->query($db->prepare($sql, 0, $post->getId(), $current_user->getId()));
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
        global $db, $current_user, $post, $comment;
        
        if ($comment->comment_subscribe == 1)
        {
            $sql = "UPDATE " . TABLE_COMMENTS . " SET comment_subscribe = %d WHERE comment_post_id = %d AND comment_user_id = %d";
            $db->query($db->prepare($sql, 1, $post->getId(), $current_user->getId()));
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
        global $db, $comment, $post, $userbase;
        
        $post->readPost($post_id);
    
        // build a list of subscribers
        $subscriber_ids = array();
        
        // Get id of post author if subscribed
        if ($post->getSubscribe() == 1) {
            array_push($subscriber_ids, $post->getAuthor());
        }
        
        // Get ids of comment authors if subscribed
        $sql = "SELECT comment_user_id FROM " . TABLE_COMMENTS . " WHERE comment_subscribe = %d AND comment_post_id = %d";
        $comment_subscribers = $db->get_results($db->prepare($sql, 1, $post->getId()));
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
        
        $comment_author = $userbase->getUserNameFromId($comment->getAuthor());
        
        $subject = $comment_author . ' has commented on ' . $post->post_title;
        
        $message =  $comment_author . " has commented on a story you are subscribed to at " . SITE_NAME . ": \r\n\r\n";
        $message .= "Story Title: " . $post->post_title . "\r\n"; 
        $message .= "Story Link: " . url(array('page'=>$post->getId())) . "\r\n\r\n";
        $message .= "Comment: " . $comment_author . "\r\n\r\n";
        $message .= "************************ \r\n";
        $message .= "Do not reply to this email. Please visit the above link and comment there. \r\n";
        $message .= "To unsubscribe, uncheck the \"Subscribe to comments\" box and submit an empty comment. ";
        
        $from = SITE_EMAIL;
        $to = $comment->getEmail();  // send email to address specified in Comment Settings; 
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