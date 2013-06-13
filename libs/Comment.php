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
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
    
class Comment
{
	protected $id           = 0;
	protected $parent       = 0;
	protected $postId       = 0;
        protected $postTitle    = ''; // from left join on post table
	protected $author       = 0;
        protected $authorname   = ''; // from left join on user table
	protected $date         = '';
	protected $status       = 'approved';
	protected $votes_up     = 0;
	protected $votes_down   = 0;
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
	protected $avatarSize   = 16;
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
	 * @param bool $digits_only - return just the count (if false, returns "3 comments", etc.)
	 * @param string $no_comments_text - e.g. "Leave a comment" or "No comments"
	 * @return string - text to show, e.g. "3 comments"
	 */
	function countComments($h, $digits_only = true, $no_comments_text = '')
	{
                if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50300 || !ACTIVERECORD) {
                    $sql = "SELECT COUNT(comment_id) FROM " . TABLE_COMMENTS . " WHERE comment_post_id = %d AND comment_status = %s";
                    $query = $h->db->prepare($sql, $h->post->id, 'approved');

                    $h->smartCache('on', 'comments', 60, $query); // start using cache
                    $num_comments = $h->db->get_var($query);
                    $h->smartCache('off'); // stop using cache		
                } else {                                       
                    $num_comments = models___Comments::count(array(
                        'conditions' => array('comment_post_id = ? AND comment_status = ?', $h->post->id, 'approved'))
                     );;
                }
                
		if ($digits_only) { return $num_comments; } // just return the number
		
		if ($num_comments == 1) {
			return "1 " . $h->lang('comments_singular_link');
		} elseif ($num_comments > 1) {
			return $num_comments . " " . $h->lang('comments_plural_link');
		} 
		
		return $no_comments_text;  // shows "Leave a comment" above comment form when no comments
	}
	
	
	/**
	 * Count all user comments
	 *
	 * @param int $user_id
	 * @return int
	 */
	function countUserComments($h, $user_id = 0)
	{
		if (!$user_id) { $user_id = $h->currentUser->id; }
		
		$sql = "SELECT COUNT(comment_id) FROM " . TABLE_COMMENTS . " WHERE comment_user_id = %d AND comment_status = %s";
		$query = $h->db->prepare($sql, $user_id , 'approved');
		
		$h->smartCache('on', 'comments', 60, $query); // start using cache
		$num_comments = $h->db->get_var($query);
		$h->smartCache('off'); // stop using cache
		
		return $num_comments; 
	}
	
	
	/**
	 * Read all comment parents
	 *
	 * @param int $post_id - the id of the post this comment is on
	 * @param array|false
	 */
	function readAllParents($h, $post_id, $order = "ASC")
	{
		$sql = "SELECT C.*, U.user_username, P.post_title FROM " . TABLE_COMMENTS . " AS C LEFT OUTER JOIN " . TABLE_USERS . " AS U ON C.comment_user_id = U.user_id LEFT JOIN " . TABLE_POSTS  . " AS P ON C.comment_post_id = P.post_id WHERE C.comment_post_id = %d AND C.comment_parent = %d AND C.comment_status = %s ORDER BY C.comment_date " . $order;
		$query = $h->db->prepare($sql, $post_id, 0, 'approved');
		
		$h->smartCache('on', 'comments', 60, $query); // start using cache
		$parents = $h->db->get_results($query);
		$h->smartCache('off'); // stop using cache
                
                //$parents = $h->mdb->query($sql, $post_id, 0, 'approved');
		
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
		//$fields = "comment_id, comment_post_id, comment_user_id, comment_parent, comment_date, comment_status, comment_content, comment_votes_up, comment_votes_down, comment_subscribe";
		
		$sql = "SELECT C.*, U.user_username, P.post_title FROM " . TABLE_COMMENTS . " AS C LEFT OUTER JOIN " . TABLE_USERS . " AS U ON C.comment_user_id = U.user_id LEFT JOIN " . TABLE_POSTS  . " AS P ON C.comment_post_id = P.post_id WHERE C.comment_parent = %d AND C.comment_status = %s ORDER BY C.comment_date";
		$query = $h->db->prepare($sql, $parent, 'approved');
		
		$h->smartCache('on', 'comments', 60, $query); // start using cache
		$children = $h->db->get_results($query);
		$h->smartCache('off'); // stop using cache
		
		if($children) { return $children; } else { return false; }
	}
	
	
	/**
	 * Get comment from database
	 *
	 * @param int $comment_id
	 * @return array|false
	 */
	function getComment($h, $comment_id = 0)
	{
		$sql = "SELECT C.*, U.user_username, P.post_title FROM " . TABLE_COMMENTS . " AS C LEFT OUTER JOIN " . TABLE_USERS . " AS U ON C.comment_user_id = U.user_id LEFT JOIN " . TABLE_POSTS  . " AS P ON C.comment_post_id = P.post_id WHERE C.comment_id = %d";
		$query = $h->db->prepare($sql, $comment_id);
		
		$h->smartCache('on', 'comments', 60, $query); // start using cache
		$comment = $h->db->get_row($query);
		$h->smartCache('off'); // stop using cache
		
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
			$sql = "SELECT C.*, U.user_username, P.post_title FROM " . TABLE_COMMENTS . " AS C LEFT OUTER JOIN " . TABLE_USERS . " AS U ON C.comment_user_id = U.user_id LEFT JOIN " . TABLE_POSTS  . " AS P ON C.comment_post_id = P.post_id WHERE C.comment_post_id = %d AND C.comment_status = %s AND P.post_status <> %s AND P.post_status <> %s ORDER BY C.comment_date " . $order;
			$query = $h->db->prepare($sql, $post_id, 'approved', 'buried', 'pending');
			$h->smartCache('on', 'comments', 60, $query); // start using cache
			$comments = $h->db->get_results($query);
		} else {
			// get all comments
			if ($userid) { 
				$sql = "SELECT C.*, U.user_username, P.post_title FROM " . TABLE_COMMENTS . " AS C LEFT OUTER JOIN " . TABLE_USERS . " AS U ON C.comment_user_id = U.user_id LEFT JOIN " . TABLE_POSTS  . " AS P ON C.comment_post_id = P.post_id WHERE C.comment_archived = %s AND C.comment_status = %s AND C.comment_user_id = %d AND P.post_status <> %s AND P.post_status <> %s ORDER BY C.comment_date " . $order . $limit;
				$query = $h->db->prepare($sql, 'N', 'approved', $userid, 'buried', 'pending');
				$h->smartCache('on', 'comments', 60, $query); // start using cache
				$comments = $h->db->get_results($query);
			} else {
				$sql = "SELECT C.*, U.user_username, P.post_title FROM " . TABLE_COMMENTS . " AS C LEFT OUTER JOIN " . TABLE_USERS . " AS U ON C.comment_user_id = U.user_id LEFT JOIN " . TABLE_POSTS  . " AS P ON C.comment_post_id = P.post_id WHERE C.comment_archived = %s AND C.comment_status = %s AND P.post_status <> %s AND P.post_status <> %s ORDER BY C.comment_date " . $order . $limit;
				$query = $h->db->prepare($sql, 'N', 'approved', 'buried', 'pending');
				$h->smartCache('on', 'comments', 60, $query ); // start using cache
				$comments = $h->db->get_results($query );
			}
		}
		$h->smartCache('off'); // stop using cache
		
		if($comments) { return $comments; } else { return false; }
	}
	
	
	/**
	 * Get all comments from database
	 *
	 * @param int $post_id - you can limit comments to a single post
	 * @return array|false
	 */
	function getAllCommentsCount($h, $order = "ASC", $userid = 0)
	{
		// get all comments
		if ($userid) { 
			$sql = "SELECT count(comment_id) AS number FROM " . TABLE_COMMENTS . " WHERE comment_archived = %s AND comment_status = %s AND comment_user_id = %d ORDER BY comment_date " . $order;
			$query = $h->db->prepare($sql, 'N', 'approved', $userid);
			$h->smartCache('on', 'comments', 60, $query); // start using cache
			$comment_count = $h->db->get_var($query);
		} else {
			$sql = "SELECT count(comment_id) AS number FROM " . TABLE_COMMENTS . " WHERE comment_archived = %s AND comment_status = %s ORDER BY comment_date " . $order;
			$query = $h->db->prepare($sql, 'N', 'approved');
			$h->smartCache('on', 'comments', 60, $query); // start using cache
			$comment_count = $h->db->get_var($query);
		}
		$h->smartCache('off'); // stop using cache
		
		if($comment_count) { return $comment_count; } else { return false; }
	}
	
	
	/**
	 * Get all comments from database
	 *
	 * @param int $post_id - you can limit comments to a single post
	 * @return array|false
	 */
	function getAllCommentsQuery($h, $order = "ASC", $userid = 0)
	{
		// get all comments
		if ($userid) { 
			$sql = "SELECT C.*, U.user_username, P.post_title FROM " . TABLE_COMMENTS . " AS C LEFT OUTER JOIN " . TABLE_USERS . " AS U ON C.comment_user_id = U.user_id LEFT JOIN " . TABLE_POSTS  . " AS P ON C.comment_post_id = P.post_id WHERE C.comment_status = %s AND C.comment_user_id = %d ORDER BY C.comment_date " . $order;
			$query = $h->db->prepare($sql, 'approved', $userid);
		} else {
			$sql = "SELECT C.*, U.user_username, P.post_title FROM " . TABLE_COMMENTS . " AS C LEFT OUTER JOIN " . TABLE_USERS . " AS U ON C.comment_user_id = U.user_id LEFT JOIN " . TABLE_POSTS  . " AS P ON C.comment_post_id = P.post_id WHERE C.comment_status = %s ORDER BY C.comment_date " . $order;
			$query = $h->db->prepare($sql, 'approved');
		}
		
		if($query) { return $query; } else { return false; }
	}
	
	
	/**
	 * Read comment
	 *
	 * @param array $comment
	 */
	function readComment($h, $comment = array())
	{
		$this->id = $comment->comment_id;
		$this->parent = $comment->comment_parent;
		$this->postId = $comment->comment_post_id;
                $this->postTitle = stripslashes(urldecode($comment->post_title));                
		$this->author = $comment->comment_user_id;
                $this->authorname = $comment->user_username;
		$this->date = $comment->comment_date;
		$this->status = $comment->comment_status;
		$this->votes_up = $comment->comment_votes_up;
		$this->votes_down = $comment->comment_votes_down;
		$this->content = urldecode($comment->comment_content);
		$this->subscribe = $comment->comment_subscribe;
		
		$h->pluginHook('comment_read_comment');
		
		return $this;
	}
	
	
	/**
	 * Add comment
	 *
	 * @return true
	 */
	function addComment($h)
	{
		$sql = "INSERT INTO " . TABLE_COMMENTS . " SET comment_post_id = %d, comment_user_id = %d, comment_parent = %d, comment_date = CURRENT_TIMESTAMP, comment_status = %s, comment_content = %s, comment_subscribe = %d, comment_updateby = %d";
		
		$h->db->query($h->db->prepare($sql, $this->postId, $this->author, $this->parent, $this->status, urlencode(trim(stripslashes($this->content))), $this->subscribe, $h->currentUser->id));
		
		$last_insert_id = $h->db->get_var($h->db->prepare("SELECT LAST_INSERT_ID()"));
		
		$this->id = $last_insert_id;
		$h->vars['last_insert_id'] = $last_insert_id;    // make it available outside this class
		
		$h->pluginHook('comment_post_add_comment');
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
	public function deleteComment($h, $comment_id = 0)
	{
		if (!$comment_id) { $comment_id = $this->id; }
		if (!$comment_id) { return false; }
		
		$sql = "DELETE FROM " . TABLE_COMMENTS . " WHERE comment_id = %d";
		$h->db->query($h->db->prepare($sql, $comment_id));
		
		// delete any votes for this comment
		$sql = "DELETE FROM " . TABLE_COMMENTVOTES . " WHERE cvote_comment_id = %d";
		$h->db->query($h->db->prepare($sql, $this->id));
		
		$h->comment->id = $comment_id; // a small hack to get the id for use in plugins.
		$h->pluginHook('comment_delete_comment');
		
		// Need to clear both these caches to be sure related items are updated in widgets, etc.:
		$h->clearCache('html_cache', false); 
		$h->clearCache('db_cache', false); 
	}
	
	
	/**
	 * Physically delete all comments by a specified user (and responses)
	 *
	 * @param array $user_id
	 * @return bool
	 */
	public function deleteComments($h, $user_id = 0) 
	{
		if (!$user_id) { return false; }
		
		$sql = "SELECT comment_id FROM " . DB_PREFIX . "comments WHERE comment_user_id = %d";
		$results = $h->db->get_results($h->db->prepare($sql, $user_id));
		
		if ($results) {
			foreach ($results as $r) {
				$h->comment->id = $r->comment_id;   // used by other plugins in "comment_delete_comment" function/hook
				$this->deleteComment($h, $h->comment->id);    // delete parent comment
				$this->deleteCommentTree($h, $h->comment->id);  // delete all children of that comment regardless of user
			}
		}
		
		return true;
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
				$this->deleteComment($h, $this->id);
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
		if ($this->subscribe == 1)
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
	 * Count how many approved comments a user has had
	 *
	 * @param int $userid 
	 * @return int 
	 */
	public function commentsApproved($h, $userid)
	{
		$sql = "SELECT COUNT(comment_id) FROM " . TABLE_COMMENTS . " WHERE comment_status = %s AND comment_user_id = %d";
		$query = $h->db->prepare($sql, 'approved', $userid);
		
		$h->smartCache('on', 'comments', 60, $query); // start using cache
		$count = $h->db->get_var($query);
		$h->smartCache('off'); // stop using cache
		
		return $count;
	}
	
	
	/**
	 * Count daily comments for this commenter
	 *
	 * @return int 
	 */
	public function countDailyComments($h)
	{
		$start = date('YmdHis', time_block());
		$end = date('YmdHis', strtotime("-1 day"));
		$sql = "SELECT COUNT(comment_id) FROM " . TABLE_COMMENTS . " WHERE comment_archived = %s AND comment_user_id = %d AND (comment_date >= %s AND comment_date <= %s)";
		$query = $h->db->prepare($sql, 'N', $this->author, $end, $start);
		
		$h->smartCache('on', 'comments', 60, $query); // start using cache
		$count = $h->db->get_var($query);
		$h->smartCache('off'); // stop using cache
		
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
		    default:
			$sql = "SELECT comment_status, count(comment_id) FROM " . TABLE_COMMENTS . " GROUP BY comment_status";
			$query = $h->db->prepare($sql);
			$h->smartCache('on', 'comments', 60, $query); // start using cache
			$comments = $h->db->get_results($query, ARRAY_N);
			break;
		    case 'archived':
			$sql = "SELECT count(comment_id) FROM " . TABLE_COMMENTS . " WHERE comment_archived = %s";
			$query = $h->db->prepare($sql, 'Y');
			$h->smartCache('on', 'comments', 60, $query); // start using cache
			$comments = $h->db->get_var($query);
			break;
		}

		$h->smartCache('off'); // stop using cache

		return $comments;

	}
}
?>
