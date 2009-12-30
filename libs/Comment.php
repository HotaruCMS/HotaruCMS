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
    protected $id           = 0;
    protected $parent       = 0;
    protected $postId       = 0;
    protected $author       = 0;
    protected $date         = '';
    protected $status       = 'approved';
    protected $votes        = 0;
    protected $content      = '';
    protected $type         = 'newcomment';   // or "editcomment"
    protected $subscribe    = 0;
    protected $levels       = 0;         // max nesting levels
    protected $depth        = 0;         // this nesting level
    protected $email        = '';
    protected $allowableTags = '';
    protected $itemsPerPage = 20;
    protected $pagination   = '';
    protected $thisForm     = '';
    protected $allForms     = 'checked';
    protected $avatars      = '';
    protected $voting       = '';
    protected $order        = 'asc';   // oldest comments first

    
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
     * @param bool $link - true used for "comments" link, false for top of actual comments
     * @return string - text to show in the link, e.g. "3 comments"
     */
    function countComments($h, $link = true)
    {
        $sql = "SELECT COUNT(comment_id) FROM " . TABLE_COMMENTS . " WHERE comment_post_id = %d AND comment_status = %s";
        $num_comments = $h->db->get_var($h->db->prepare($sql, $h->post->id, 'approved'));
        
        if ($num_comments == 1) {
            return "1 " . $h->lang['comments_singular_link'];
        } elseif ($num_comments > 1) {
            return $num_comments . " " . $h->lang['comments_plural_link'];
        } else {
            if (!$link) { 
                return $h->lang['comments_leave_comment'];  // shows "Leave a comment" above comment form when no comments
            }
            else
            {
                return $h->lang['comments_none_link']; // Shows "No comments"
            }
        }
    }
    
    
    /**
     * Read all comment parents
     *
     * @param int $post_id - the id of the post this comment is on
     * @param array|false
     */
    function readAllParents($h, $post_id, $order = "ASC")
    {
        $sql = "SELECT * FROM " . TABLE_COMMENTS . " WHERE comment_post_id = %d AND comment_parent = %d ORDER BY comment_date " . $order;
        $parents = $h->db->get_results($h->db->prepare($sql, $post_id, 0));
        
        if($parents) { return $parents; } else { return false; }
    }


    /**
     * Read all comment children
     *
     * @param int $parent - the id of the parent comment
     * @param array|false
     */
    function readAllChildren($h, $parent)
    {
        $sql = "SELECT * FROM " . TABLE_COMMENTS . " WHERE comment_parent = %d ORDER BY comment_date";
        $children = $h->db->get_results($h->db->prepare($sql, $parent));
        
        if($children) { return $children; } else { return false; }
    }
    
    
    /**
     * Get comment from database
     *
     * @param int $comment_id
     * @return array|false
     */
    function getComment($h, $comment_id)
    {
        $sql = "SELECT * FROM " . TABLE_COMMENTS . " WHERE comment_id = %d";
        $comment = $h->db->get_row($h->db->prepare($sql, $comment_id));
        
        if($comment) { return $comment; } else { return false; }
    }
    
    
    /**
     * Get all comments from database
     *
     * @param int $post_id - you can limit comments to a single post
     * @return array|false
     */
    function getAllComments($h, $post_id = 0, $order = "ASC", $limit = 0, $userid = 0)
    {
        // limiting is used in the rssFeed function. Other than that, pagination does limiting for us.
        if(!$limit) { $limit = ''; } else { $limit = " LIMIT "  .$limit; }
        
        if ($post_id) {
            // get all comments from specified post
            $sql = "SELECT * FROM " . TABLE_COMMENTS . " WHERE comment_post_id = %d AND comment_status = %s ORDER BY comment_date " . $order;
            $comments = $h->db->get_results($h->db->prepare($sql, $post_id, 'approved'));
        } else {
            // get all comments
            if ($userid) { 
                $sql = "SELECT * FROM " . TABLE_COMMENTS . " WHERE comment_status = %s AND comment_user_id = %d ORDER BY comment_date " . $order . $limit;
                $comments = $h->db->get_results($h->db->prepare($sql, 'approved', $userid));
            } else {
                $sql = "SELECT * FROM " . TABLE_COMMENTS . " WHERE comment_status = %s ORDER BY comment_date " . $order . $limit;
                $comments = $h->db->get_results($h->db->prepare($sql, 'approved'));
            }
        }
        
        if($comments) { return $comments; } else { return false; }
    }
    
    
    /**
     * Read comment
     *
     * @param array $comment
     */
    function readComment($h, $comment)
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
        
        $h->pluginHook('comment_read_comment');
    }
    
    
    /**
     * Add comment
     *
     * @return true
     */
    function addComment($h)
    {
        $h->pluginHook('comment_pre_add_comment');  // Akismet uses this to change the status
        
        $can_comment = $h->currentUser->getPermission('can_comment'); // This was already checked, but Akismet sometimes reverts the status, so we do it again.

        if ($can_comment == 'mod') { $this->status = 'pending'; } // forces all to 'pending' if user's comments are moderated
        
        // Get settings from database...
        $comments_settings = $h->getSerializedSettings('comments');

        $set_pending = $comments_settings['comment_set_pending'];
        $daily_limit = $comments_settings['comment_daily_limit'];
        $url_limit = $comments_settings['comment_url_limit'];
        
        if ($set_pending == 'some_pending') {
            $comments_approved = $this->commentsApproved($h, $h->currentUser->id);
            $x_comments_needed = $comments_settings['comment_x_comments'];
        }
        
        if ($h->currentUser->role == 'member') {
            if ($daily_limit && ($daily_limit < $this->countDailyComments($h))) { $this->status = 'pending'; } // exceeded daily limit, set to pending
            if ($url_limit && ($url_limit < $this->countUrls())) { $this->status = 'pending'; } // exceeded url limit, set to pending
        }
                    
        if ($set_pending == 'all_pending') {
            $this->status = 'pending'; 
        } elseif (($set_pending == 'some_pending') && ($comments_approved <= $x_comments_needed)) {
            $this->status = 'pending'; 
        } 
                
        $sql = "INSERT INTO " . TABLE_COMMENTS . " SET comment_post_id = %d, comment_user_id = %d, comment_parent = %d, comment_date = CURRENT_TIMESTAMP, comment_status = %s, comment_content = %s, comment_subscribe = %d, comment_updateby = %d";
                
        $h->db->query($h->db->prepare($sql, $this->postId, $this->author, $this->parent, $this->status, urlencode(trim(stripslashes($this->content))), $this->subscribe, $h->currentUser->id));
        
        $last_insert_id = $h->db->get_var($h->db->prepare("SELECT LAST_INSERT_ID()"));
        
        $this->id = $last_insert_id;
        $h->vars['last_insert_id'] = $last_insert_id;    // make it available outside this class
        
        $h->pluginHook('comment_post_add_comment');
        
        return true;
    }
    

    /**
     * Edit comment
     *
     * @return true
     */
    function editComment($h)
    {
        $sql = "UPDATE " . TABLE_COMMENTS . " SET comment_status = %s, comment_content = %s, comment_subscribe = %d, comment_updateby = %d WHERE comment_id = %d";
        $h->db->query($h->db->prepare($sql, $this->status, urlencode(trim(stripslashes($this->content))), $this->subscribe, $h->currentUser->id, $this->id));
        
        $h->comment->id = $this->id; // a small hack to get the id for use in plugins.
        $h->pluginHook('comment_update_comment');
        
        return true;
    }


    /**
     * Physically delete a comment from the database 
     *
     */    
    public function deleteComment($h)
    {
        $sql = "DELETE FROM " . TABLE_COMMENTS . " WHERE comment_id = %d";
        $h->db->query($h->db->prepare($sql, $this->id));
        
        // delete any votes for this comment
        //$sql = "DELETE FROM " . TABLE_COMMENTVOTES . " WHERE cvote_comment_id = %d";
        //$h->db->query($h->db->prepare($sql, $this->id));
        
        $h->comment->id = $this->id; // a small hack to get the id for use in plugins.
        $h->pluginHook('comment_delete_comment');
    }
    
    
    /**
     * Recurse through comment tree, deleting all
     *
     * @param int $comment_id - id of current comment
     * @return bool
     */
    public function deleteCommentTree($h, $comment_id)
    {
        while ($children = $this->readAllChildren($h, $comment_id)) {
            foreach ($children as $child) {
                $this->readComment($h, $child);
                $this->deleteComment($h);
                if ($this->deletecommentTree($h, $this->id)) {
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
    public function setPendingCommentTree($h, $comment_id)
    {
        while ($children = $this->readAllChildren($h, $comment_id)) {
            print_r($children);
            foreach ($children as $child) {
                $this->readComment($h, $child);
                $this->status = 'pending';
                $this->editComment($h);
                if ($this->setPendingCommentTree($h, $this->id)) {
                    return true;
                }
            }
            
            return false;
        }
    }
    
    
    /**
     * Determine if the comment form is open or closed
     *
     * @param int $post_id
     * @return string 'open' or 'closed'
     */
    function formStatus($h, $type)
    {
        if ($type == 'select') {
            $sql = "SELECT post_comments FROM " . TABLE_POSTS . " WHERE post_id = %d";
            $form_status = $h->db->get_var($h->db->prepare($sql, $h->post->id));
            
            if ($form_status) { return $form_status; } else { return 'open'; } // default 'open'
        }
        
        if ($type == 'open' || $type == 'closed') {
            $h->comment->form = $type;
            $sql = "UPDATE " . TABLE_POSTS . " SET post_comments = %s WHERE post_id = %d";
            $h->db->query($h->db->prepare($sql, $type, $h->post->id));
        }
    }
    
    
    /**
     * Unsubscribe from a thread
     *
     * @param int $post_id
     * @return true
     */
    function unsubscribe($h, $post_id)
    {
        $h->readPost($post_id);
            
        $sql = "UPDATE " . TABLE_COMMENTS . " SET comment_subscribe = %d WHERE comment_post_id = %d AND comment_user_id = %d";
        $h->db->query($h->db->prepare($sql, 0, $h->post->id, $h->currentUser->id));
               
        // Check if the currentUser is the post author
        if ($h->post->author == $h->currentUser->id) {
        // Check if the user subscribed to comments as a submitter
            if ($h->post->subscribe == 1) { 
                $sql = "UPDATE " . TABLE_POSTS . " SET post_subscribe = %d WHERE post_id = %d AND post_author = %d";
                $h->db->query($h->db->prepare($sql, 0, $h->post->id, $h->currentUser->id));
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
    function updateSubscribe($h, $post_id)
    {
        if ($this->comment_subscribe == 1)
        {
            $sql = "UPDATE " . TABLE_COMMENTS . " SET comment_subscribe = %d WHERE comment_post_id = %d AND comment_user_id = %d";
            $h->db->query($h->db->prepare($sql, 1, $h->post->id, $h->currentUser->id));
        } 
        else 
        {
            $this->unsubscribe($h, $post_id);
        }
    }
    
    
    /**
    * Send an email to thread subscribers
    *
    * @param int $post_id
    */
    function emailCommentSubscribers($h, $post_id)
    {
        $h->readPost($post_id);
    
        // build a list of subscribers
        $subscriber_ids = array();
        
        // Get id of post author if subscribed
        if ($h->post->subscribe == 1) {
            array_push($subscriber_ids, $h->post->author);
        }
        
        // Get ids of comment authors if subscribed
        $sql = "SELECT comment_user_id FROM " . TABLE_COMMENTS . " WHERE comment_subscribe = %d AND comment_post_id = %d";
        $comment_subscribers = $h->db->get_results($h->db->prepare($sql, 1, $h->post->id));
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
                $email = $h->db->get_var($h->db->prepare("SELECT user_email FROM " . TABLE_USERS . " WHERE user_id = %d", $subscriber_id));
                array_push($subscribers, $email);
            }
        }
        
        $send_to = trim(implode(",", $subscribers),",");
        
        $comment_author = $h->getUserNameFromId($this->author);
        
        //clean up content:
        $story_title = stripslashes(html_entity_decode(urldecode($h->post->title), ENT_QUOTES,'UTF-8'));
        $comment_content = stripslashes($h->comment->content);
        
        $subject = $comment_author . ' has commented on ' . $story_title;
        
        $message =  $comment_author . " has commented on a story you are subscribed to at " . SITE_NAME . ": \r\n\r\n";
        $message .= "Story Title: " . $story_title . "\r\n"; 
        $message .= "Story Link: " . $h->url(array('page'=>$h->post->id)) . "\r\n\r\n";
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
    
    
    /**
     * Publish content as an RSS feed
     * Uses the 3rd party RSS Writer class.
     */    
    public function rssFeed($h)
    {
        require_once(EXTENSIONS . 'RSSWriterClass/rsswriter.php');
        
        $select = '*';

        $limit = $h->cage->get->getInt('limit');
        $user = $h->cage->get->testUsername('user');

        if (!$limit) { $limit = 10; }
        if ($user) { 
            $userid = $h->getUserIdFromName($user);
        } else {
            $userid = 0;
        }
        
        $h->pluginHook('comment_rss_feed');
        
        $feed           = new RSS();
        $feed->title    = SITE_NAME;
        $feed->link     = BASEURL;
        
        if ($user) { 
            $feed->description = $h->lang["comment_rss_comments_from_user"] . " " . $user; 
        } else {
            $feed->description = $h->lang["comment_rss_latest_comments"] . SITE_NAME;
        }
        
        // fetch comments from the database        
        $comments = $this->getAllComments($h, 0, "desc", $limit, $userid);
        
        if ($comments) {
            foreach ($comments as $comment) 
            {
                $h->readPost($comment->comment_post_id);
                
                $author = $h->getUserNameFromId($comment->comment_user_id);
                
                $item = new RSSItem();
                if ($user) { 
                    $title = $h->lang["comment_rss_comment_on"] . html_entity_decode(urldecode($h->post->title), ENT_QUOTES,'UTF-8');
                } else {
                    $title = $author . $h->lang["comment_rss_commented_on"] . html_entity_decode(urldecode($h->post->title), ENT_QUOTES,'UTF-8');
                }
                $item->title = stripslashes($title);
                $item->link  = $h->url(array('page'=>$comment->comment_post_id)) . "#c" . $comment->comment_id;
                $item->setPubDate($comment->comment_date); 
                $item->description = "<![CDATA[ " . stripslashes(urldecode($comment->comment_content)) . " ]]>";
                $feed->addItem($item);
            }
        }
        echo $feed->serve();
    }
    
    
    /**
     * Count how many approved comments a user has had
     *
     * @param int $userid 
     * @return int 
     */
    public function commentsApproved($h, $userid)
    {
        $sql = "SELECT COUNT(*) FROM " . TABLE_COMMENTS . " WHERE comment_status = %s AND comment_user_id = %d";
        $count = $h->db->get_var($h->db->prepare($sql, 'approved', $userid));
        
        return $count;
    }
    
    
    /**
     * Count daily comments for this commenter
     *
     * @return int 
     */
    public function countDailyComments($h)
    {
        $start = date('YmdHis', strtotime("now"));
        $end = date('YmdHis', strtotime("-1 day"));
        $sql = "SELECT COUNT(comment_id) FROM " . TABLE_COMMENTS . " WHERE comment_archived = %s AND comment_user_id = %d AND (comment_date >= %s AND comment_date <= %s)";
        $count = $h->db->get_var($h->db->prepare($sql, 'N', $this->author, $end, $start));
        
        return $count;
    }
    
    
    /**
     * Count urls in comment
     *
     * @return int 
     * @link http://www.liamdelahunty.com/tips/php_url_count_check_for_comment_spam.php
     */
    public function countUrls()
    {
        $text = $this->content;
        
        echo "<br />" . $text . "</br >";
        
        //$http = substr_count($text, "http");
        $href = substr_count($text, "href");
        $url = substr_count($text, "[url");
        
        return $href + $url;
    }
    
    
    /**
     * Stats for Admin homepage
     *
     * @param string $stat_type
     * @return int
     */
    public function stats($h, $stat_type = '')
    {
        switch ($stat_type) {
            case 'total_comments':
                $sql = "SELECT count(comment_id) FROM " . TABLE_COMMENTS;
                $comments = $h->db->get_var($sql);
                break;
            case 'approved_comments':
                $sql = "SELECT count(comment_id) FROM " . TABLE_COMMENTS . " WHERE comment_status = %s";
                $comments = $h->db->get_var($h->db->prepare($sql, 'approved'));
                break;
            case 'pending_comments':
                $sql = "SELECT count(comment_id) FROM " . TABLE_COMMENTS . " WHERE comment_status = %s";
                $comments = $h->db->get_var($h->db->prepare($sql, 'pending'));
                break;
            default:
                $comments = '';
        }
        
        return $comments;
    }
}
?>