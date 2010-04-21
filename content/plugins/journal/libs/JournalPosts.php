<?php
/**
 * SB Base functions
 * Notes: This file is part of the SB Submit plugin.
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

class JournalPosts extends JournalBase
{
	/**
	 * Make a post
	 *
	 * @param string $type 'newpost' or 'editpost'
	 */
	public function doPost($h, $type = 'newpost')
	{
		$error = array();
		
		if (!$h->currentUser->loggedIn) { return false; }
		
		// check if this is a new post and process it
		if ($type == 'newpost') { $error = $this->newPost($h); }
		
		// check and process a post being edited
		if ($type == 'editpost') { $error = $this->editPost($h); }
		
		// the url function adds &amp; so we'll decode html entities before redirecting or the url will break
		
		if (!empty($error)) {
			$error = implode('-', $error);
			$username = $h->getUserNameFromId($h->post->author);
			if ($type == 'newpost') {
				$redirect = html_entity_decode($h->url(array('page'=>'journal', 'user'=>$username, 'error'=>$error)), ENT_QUOTES,'UTF-8');
			} else {
				$redirect = html_entity_decode($h->url(array('page'=>$h->post->id, 'error'=>$error)), ENT_QUOTES,'UTF-8');
			}
		} else {
			$redirect = html_entity_decode($h->url(array('page'=>$h->post->id)), ENT_QUOTES,'UTF-8');
		}
		
		header("Location: " . $redirect);    // Go to the post
		die();
	}
	
	
	/**
	 * Fill post object
	 */
	public function fillPostObject($h)
	{
		$edit = false;
		
		if ($h->cage->post->getAlpha('post_process') == 'editpost') {
			$post_id = $h->cage->post->testInt('post_id');
			if ($post_id) { 
				$h->readPost($post_id);
				$edit = true; 
			}
		}
		
		// get author
		$h->post->author = $h->cage->post->testInt('post_author');
		
		// get the allowed tas:
		$journal_settings = $h->getSerializedSettings('journal');
		$allowed_tags = $journal_settings['allowable_tags'];
		
		// get content - return false if no content
		$h->post->content = sanitize($h->cage->post->getHtmLawed('post_content'), 'tags', $allowed_tags);
		
		// get title - take first 60 chars from content if no title provided
		$h->post->title = $h->cage->post->getHtmLawed('post_title');
		if (!$h->post->title) {
			$h->post->title = truncate($h->post->content, 60);
		}
		
		// make title into a url
		$title = html_entity_decode($h->post->title, ENT_QUOTES, 'UTF-8');
		if (!$title) { $title = $h->lang['journal_no_title']; }
		$h->post->url = make_url_friendly($title);
		$num = 2;
		while (!$edit && $h->isPostUrl($h->post->url)) {
			//prevent duplicate url slugs by appending a number
			$h->post->url = $h->post->url . "-" . $num;
			$num++;
		}
		
		$error = $this->checkErrors($h, $edit);

		return $error;
	}
	
	
	/**
	 * New post
	 */
	public function newPost($h)
	{
		if ($h->cage->post->getAlpha('post_process') != 'newpost') { return false; }

		// fill the post object
		$error = $this->fillPostObject($h);
		
		if (empty($error)) {
			$h->post->status = 'new';
			$h->post->type = 'blog';
			$h->addPost();
		}
		else
		{
			// if the only error is the user has "mod" permissions for "can_journal" then set pending
			if (in_array('pending', $error)) {
				if (sizeof($error) == 1) {
					$h->post->status = 'pending';
					$h->post->type = 'blog';
					$h->addPost();
				} else {
					// remove "pending" because it should only be shown if it's the only error
					foreach ($error as $key => $val) {
						if ($val == 'pending') { unset($error[$key]); }
					}
				}
			}
		}

		return $error;
	}
	
	
	/**
	 * Editing post
	 */
	public function editPost($h)
	{
		if ($h->cage->post->getAlpha('post_process') != 'editpost') { return false; }

		// fill the post object
		$error = $this->fillPostObject($h);
		
		if (empty($error)) {
			$h->updatePost();
		}
		
		return $error;
	}
	
	
	/**
	 * Check errors in journal post
	 */
	public function checkErrors($h, $edit = false)
	{
		$error = array();
		
		// get the settings we need:
		$journal_settings = $h->getSerializedSettings('journal');
		$min_content_length = $journal_settings['content_length'];
		
		// cross site request forgery?
		if (!$h->csrf('check', 'journal')) { array_push($error, 'csrf'); }
		
		// does user have permission to post journal entries?
		if ($h->currentUser->getPermission('can_journal') == 'no') { array_push($error, 'no_perms'); }
		
		// does user have permission to post journal entries?
		if ($h->currentUser->getPermission('can_journal') == 'mod') { array_push($error, 'pending'); }
		
		// is there a title?
		if (!$h->post->title) { array_push($error, 'title'); }
		
		// is there any content?
		if (!$h->post->content) { array_push($error, 'content'); }
		
		// is the content too short?
		if ($h->post->content && (strlen($h->post->content) < $min_content_length)) { array_push($error, 'too_short'); }

		// allow plugins to add their own checks
		$h->vars['journal_check_error'] = $error;
		$h->pluginHook('journal_check_errors');
		$error = $h->vars['journal_check_error'];
		
		return $error;
	}
	
	
	/**
	 * Show journal posts
	 */
	public function showJournalPosts($h, $userid = 0)
	{
		// get the allowed tas:
		$journal_settings = $h->getSerializedSettings('journal');
		$per_page = $journal_settings['items_per_page'];
		$h->vars['summary'] = $journal_settings['summary'];
		$h->vars['summary_length'] = $journal_settings['summary_length'];
		
		$h->vars['pagedResults'] = $this->getJournalPosts($h, $userid, $per_page);
		
		if (!$h->vars['pagedResults']) { return false; }
		
		$h->displayTemplate('journal_list');

		echo $h->pageBar($h->vars['pagedResults']);
	}
	
	
	/**
	 * Show journal post
	 */
	public function showJournalPost($h)
	{
		// defaults:
		$buried = false; $pending = false;
		
		// check if buried:
		if ($h->post->status == 'buried') {
			$buried = true;
			$h->messages[$h->lang["sb_base_post_buried"]] = "red";
		} 
		
		// check if pending:
		if ($h->post->status == 'pending') { 
			$pending = true;
			$h->messages[$h->lang["sb_base_post_pending"]] = "yellow";
		}
		
		$h->showMessages();

		if (!$buried && !$pending) {
			$h->displayTemplate('journal_post', 'journal', false);
		}
	}
	
	
	/**
	 * Get Journal Posts
	 *
	 * @param int $userid
	 * @param int $item_count
	 */
	public function getJournalPosts($h, $userid = 0, $item_count = 10)
	{
		// build SQL to get posts
		$sql = "SELECT * FROM " . TABLE_POSTS  . " WHERE post_archived = %s AND post_type = %s AND (post_status = %s OR post_status = %s) ";
		if ($userid) { $sql .= "AND post_author = %d "; }
		$sql .= "ORDER BY post_date DESC";
		
		// get the number of total posts and prepare the $sql query
		if ($userid) {
			$count_sql = "SELECT count(*) FROM " . TABLE_POSTS  . " WHERE post_archived = %s AND post_type = %s AND (post_status = %s OR post_status = %s) AND post_author = %d ";
			$count = $h->db->get_var($h->db->prepare($count_sql, 'N', 'blog', 'new', 'top', $userid));
			$query = $h->db->prepare($sql, 'N', 'blog', 'new', 'top', $userid);
		} else {
			$count_sql = "SELECT count(*) FROM " . TABLE_POSTS  . " WHERE post_archived = %s AND post_type = %s AND (post_status = %s OR post_status = %s) ";
			$count = $h->db->get_var($h->db->prepare($count_sql, 'N', 'blog', 'new', 'top'));
			$query = $h->db->prepare($sql, 'N', 'blog', 'new', 'top');
		}
		
		$cache_table = 'posts';
		
		// get the results for this page
		$pagedResults = $h->pagination($query, $count, $item_count, $cache_table);
		
		return $pagedResults;
	}
	
	
	/**
	 * journal RSS feed
	 */
	public function journalRssFeed($h, $username = '')
	{
		// get the allowed tas:
		$journal_settings = $h->getSerializedSettings('journal');
		$rss_size = $journal_settings['rss_items'];
		
		if ($username) {
			$title = $h->lang['journal_rss_user_title'] . $username;
			$link = $h->url(array('page'=>'journal', 'user'=>$username));
			$description = $h->lang['journal_rss_user_description'] . $username;
			$userid = $h->getUserIdFromName($username);
		} else {
			$title = $h->lang['journal_rss_title'];
			$link = $h->url(array('page'=>'journals'));
			$description = $h->lang['journal_rss_description'];
			$userid = 0;
		}
		
		$results = $this->getJournalPostsRSS($h, $userid, $rss_size);
		$items = array();
		if ($results) {
			foreach ($results as $item)
			{
				$new_item = array(
					'title'=>$item->post_title, 
					'link'=>$h->url(array('page'=>$item->post_id)), 
					'date'=>$item->post_date, 
					'description'=>$item->post_content
				);
				
				array_push($items, $new_item);
			}
		}

		$h->rss($title, $link, $description, $items);
	}
	

	/**
	 * Get Journal Posts for RSS
	 *
	 * @param int $userid
	 * @param int $item_count
	 */
	public function getJournalPostsRSS($h, $userid = 0, $item_count = 10)
	{
		// build SQL to get posts
		$sql = "SELECT * FROM " . TABLE_POSTS  . " WHERE post_archived = %s AND post_type = %s AND (post_status = %s OR post_status = %s) ";
		if ($userid) { $sql .= "AND post_author = %d "; }
		$sql .= "ORDER BY post_date DESC LIMIT " . $item_count;
		
		// prepare the $sql query
		if ($userid) {
			$query = $h->db->prepare($sql, 'N', 'blog', 'new', 'top', $userid);
		} else {
			$query = $h->db->prepare($sql, 'N', 'blog', 'new', 'top');
		}
		
		return $h->db->get_results($query);
	}
}
?>