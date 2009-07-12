<?php

/* ********** PLUGIN CLASSES**************************************************************************
 * name: Post
 * description: Class for functions related to submitting and organizing posts
 * file: /plugins/submit/libraries/class.post.php
 *
 *  License:
 *
 *   This file is part of Hotaru CMS (http://www.hotarucms.org/).
 *
 *   Hotaru CMS is free software: you can redistribute it and/or modify it under the terms of the 
 *   GNU General Public License as published by the Free Software Foundation, either version 3 of 
 *   the License, or (at your option) any later version.
 *
 *   Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 *   even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License along with Hotaru CMS. If not, 
 *   see http://www.gnu.org/licenses/.
 *   
 *   Copyright (C) 2009 Hotaru CMS - http://www.hotarucms.org/
 *
 **************************************************************************************************** */
 
class Post {	

	var $post_id = 0;
	var $post_orig_url = '';
	var $post_title = '';
	var $post_content = '';
	var $post_content_length = 20;	// min characters for content
	var $post_tags = '';
	var $post_max_tags = 50;	// max characters for tags
	var $post_status = 'processing';
	var $post_author = 0;
	var $post_url = '';
	
	// Settings
	
	var $use_author = false;
	var $use_date = false;
	var $use_content = false;
	var $use_tags = false;


	/* ******************************************************************** 
	 *  Function: read_post
	 *  Parameters: Optional row from the posts table in the database
	 *  Purpose: Get all the settings for the current post
	 *  Notes: ---
	 ********************************************************************** */	
	 
	function read_post($post_id = 0) {
		global $plugin;
		
		//author
		$this->post_author = $plugin->plugin_settings('submit', 'submit_author');
		if($plugin->plugin_settings('submit', 'submit_author') == 'checked') { $this->use_author = true; }
		
		//date
		$this->post_date = $plugin->plugin_settings('submit', 'submit_date');
		if($plugin->plugin_settings('submit', 'submit_date') == 'checked') { $this->use_date = true; }
		
		//content
		if($plugin->plugin_settings('submit', 'submit_content') == 'checked') { $this->use_content = true; }
		$content_length =  $plugin->plugin_settings('submit', 'submit_content_length');
		if(!empty($content_length)) { $this->post_content_length = $content_length; }
		
		//tags
		if($plugin->plugin_settings('submit', 'submit_tags') == 'checked') { $this->use_tags = true; }
		$max_tags = $plugin->plugin_settings('submit', 'submit_max_tags');
		if(!empty($max_tags)) { $this->post_max_tags = $max_tags; }
		
		if($post_id != 0) {
			$post_row = $this->get_post($post_id);
			$this->post_id = $post_row->post_id;
			$this->post_orig_url = urldecode($post_row->post_orig_url);
			$this->post_title = urldecode($post_row->post_title);
			$this->post_content = urldecode($post_row->post_content);
			$this->post_tags = urldecode($post_row->post_tags);
			$this->post_status = $post_row->post_status;
			$this->post_author = $post_row->post_author;
			$this->post_url = urldecode($post_row->post_url);
			$this->post_date = $post_row->post_date;
		}

		return true;
	}
	
	
	/* ******************************************************************** 
	 *  Function: add_post
	 *  Parameters: None
	 *  Purpose: Adds a post to the database
	 *  Notes: ---
	 ********************************************************************** */	
	 
	function add_post() {
		global $db;
		$sql = "INSERT INTO " . table_posts . " SET post_orig_url = %s, post_title = %s, post_url = %s, post_content = %s, post_tags = %s, post_status = %s, post_author = %d";
		$db->query($db->prepare($sql, urlencode($this->post_orig_url), urlencode(trim($this->post_title)), urlencode(trim($this->post_url)), urlencode(trim($this->post_content)), urlencode(trim($this->post_tags)), $this->post_status, $this->post_author));
		return true;
	}


	/* ******************************************************************** 
	 *  Function: get_post
	 *  Parameters: None
	 *  Purpose: Gets a single post from the database
	 *  Notes: ---
	 ********************************************************************** */	
	 	
	function get_post($post_id = 0) {
		global $db;
		$sql = "SELECT * FROM " . table_posts . " WHERE post_id = %d ORDER BY post_date DESC";
		$post = $db->get_row($db->prepare($sql, $post_id));
		if($post) { return $post; } else { return false; }
	}
	
	
	/* ******************************************************************** 
	 *  Function: get_posts
	 *  Parameters: None
	 *  Purpose: Gets all the posts from the database
	 *  Notes: ---
	 ********************************************************************** */	
	 	
	function get_posts() {
		global $db;
		$sql = "SELECT * FROM " . table_posts . " ORDER BY post_date DESC";
		$posts = $db->get_results($db->prepare($sql));
		if($posts) { return $posts; } else { return false; }
	}
	
	
	/* ******************************************************************** 
	 *  Function: url_exists
	 *  Parameters: url
	 *  Purpose: Checks for existence of a url
	 *  Notes: ---
	 ********************************************************************** */	
	 	
	function url_exists($url = '') {
		global $db;
		$sql = "SELECT count(post_id) FROM " . table_posts . " WHERE post_orig_url = %s";
		$posts = $db->get_var($db->prepare($sql, urlencode($url)));
		if($posts > 0) { return $posts; } else { return false; }
	}
	
	
	/* ******************************************************************** 
	 *  Function: title_exists
	 *  Parameters: title
	 *  Purpose: Checks for existence of a title
	 *  Notes: ---
	 ********************************************************************** */	
	 	
	function title_exists($title = '') {
		global $db;
		$title = trim($title);
		$sql = "SELECT count(post_id) FROM " . table_posts . " WHERE post_title = %s";
		$posts = $db->get_var($db->prepare($sql, urlencode($title)));
		if($posts > 0) { return $posts; } else { return false; }
	}
	
	
	
	/* ******************************************************************** 
	 *  Function: is_post_url
	 *  Parameters: page_name
	 *  Purpose: Checks for existence of a post with given name
	 *  Notes: Returns the post_id if true, false otherwise
	 ********************************************************************** */	
	 	
	function is_post_url($page = '') {
		global $db;
		$sql = "SELECT post_id FROM " . table_posts . " WHERE post_url = %s";
		$post_id = $db->get_var($db->prepare($sql, urlencode($page)));
		if($post_id) { return $post_id; } else { return false; }
	}
}

?>