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
			$redirect = html_entity_decode($h->url(array('page'=>'journal', 'user'=>$username, 'error'=>$error)), ENT_QUOTES,'UTF-8');
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
		$error = array();
		
		if ($h->cage->post->getAlpha('post_process') == 'editpost') {
			$post_id = $h->cage->post->testInt('post_id');
			if ($post_id) { $h->readPost($post_id); }
		}
		
		// get author
		$h->post->author = $h->cage->post->testInt('post_author');
		
		// get title - take first 60 chars from content if no title provided
		$h->post->title = $h->cage->post->getHtmLawed('post_title');
		if (!$h->post->title) {
			$h->post->title = truncate($h->post->content, 60);
		}
		
		// make title into a url
		$title = html_entity_decode($h->post->title, ENT_QUOTES, 'UTF-8');
		$h->post->url = make_url_friendly($title);
		
		// get content - return false if no content
		$h->post->content = sanitize($h->cage->post->getHtmLawed('post_content'), 'tags', $h->post->allowableTags);
		if (!$h->post->content) { array_push($error, 'content'); }

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
	 * Show journal posts
	 */
	public function showJournalPosts($h, $userid = 0)
	{
		$h->vars['pagedResults'] = $this->getJournalPosts($h, $userid);
		
		if (!$h->vars['pagedResults']) { return false; }
		
		$h->displayTemplate('journal_list');

		echo $h->pageBar($h->vars['pagedResults']);
	}
	
	
	/**
	 * Show journal post
	 */
	public function showJournalPost($h)
	{
		$h->displayTemplate('journal_post', 'journal', false);
	}
	
	
	/**
	 * Editing post
	 */
	public function getJournalPosts($h, $userid = 0)
	{
		// build SQL to get posts
		$sql = "SELECT * FROM " . TABLE_POSTS  . " WHERE post_archived = %s AND post_type = %s ";
		if ($userid) { $sql .= "AND post_author = %d "; }
		$sql .= "ORDER BY post_date DESC";
		
		// get the number of total posts and prepare the $sql query
		if ($userid) {
			$count_sql = "SELECT count(*) FROM " . TABLE_POSTS  . " WHERE post_archived = %s AND post_type = %s AND post_author = %d";
			$count = $h->db->get_var($h->db->prepare($count_sql, 'N', 'blog', $userid));
			$query = $h->db->prepare($sql, 'N', 'blog', $userid);
		} else {
			$count_sql = "SELECT count(*) FROM " . TABLE_POSTS  . " WHERE post_archived = %s AND post_type = %s ";
			$count = $h->db->get_var($h->db->prepare($count_sql, 'N', 'blog'));
			$query = $h->db->prepare($sql, 'N', 'blog');
		}
		
		$cache_table = 'posts';
		
		// get the results for this page
		$pagedResults = $h->pagination($query, $count, 2, $cache_table);
		
		return $pagedResults;
	}
}
?>