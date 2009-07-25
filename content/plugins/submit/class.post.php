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
	var $post_status = 'processing';
	var $post_author = 0;
	var $post_url = '';
	var $post_date = '';
			
	var $use_author = true;
	var $use_date = true;
	var $use_content = true;

	var $post_vars = array();


	/* ******************************************************************** 
	 *  Functions: PHP __set Magic Method
	 *  Parameters: The name of the member variable and the value to set it to.
	 *  Purpose: Plugins use this to set additonal member variables
	 *  Notes: ---
	 ********************************************************************** */
	 			
	function __set($name, $value) {
        	$this->post_vars[$name] = $value;
    	}
    	
    	
	/* ******************************************************************** 
	 *  Functions: PHP __get Magic Method
	 *  Parameters: The name of the member variable to retrieve.
	 *  Purpose: Plugins use this to read values of additonal member variables
	 *  Notes: ---
	 ********************************************************************** */
    	
	function __get($name) {
		if (array_key_exists($name, $this->post_vars)) {
			return $this->post_vars[$name];
		}
    	}


	/* ******************************************************************** 
	 *  Function: read_post
	 *  Parameters: Optional row from the posts table in the database
	 *  Purpose: Get all the settings for the current post
	 *  Notes: ---
	 ********************************************************************** */	
	 
	function read_post($post_id = 0) {
		global $plugin, $post_row;
		
		//author
		$this->post_author = $plugin->plugin_settings('submit', 'submit_author');
		if($plugin->plugin_settings('submit', 'submit_author') == 'checked') { $this->use_author = true; } else { $this->use_author = false; }
		
		//date
		$this->post_date = $plugin->plugin_settings('submit', 'submit_date');
		if($plugin->plugin_settings('submit', 'submit_date') == 'checked') { $this->use_date = true; } else { $this->use_date = false; }
		
		//content
		if($plugin->plugin_settings('submit', 'submit_content') == 'checked') { $this->use_content = true; } else { $this->use_content = false; }
		$content_length =  $plugin->plugin_settings('submit', 'submit_content_length');
		if(!empty($content_length)) { $this->post_content_length = $content_length; }
				
		$plugin->check_actions('submit_class_post_read_post_1');
		
		if($post_id != 0) {
			$post_row = $this->get_post($post_id);
			$this->post_id = $post_row->post_id;
			$this->post_orig_url = urldecode($post_row->post_orig_url);
			$this->post_title = htmlentities(stripslashes(urldecode($post_row->post_title)));
			$this->post_content = htmlentities(stripslashes(urldecode($post_row->post_content)));
			$this->post_status = $post_row->post_status;
			$this->post_author = $post_row->post_author;
			$this->post_url = urldecode($post_row->post_url);
			$this->post_date = $post_row->post_date;
			$plugin->check_actions('submit_class_post_read_post_2');
			return true;
		} else {
			return false;
		}
	}
	
	
	/* ******************************************************************** 
	 *  Function: add_post
	 *  Parameters: None
	 *  Purpose: Adds a post to the database
	 *  Notes: ---
	 ********************************************************************** */	
	 
	function add_post() {
		global $db, $plugin, $last_insert_id, $current_user;
		$sql = "INSERT INTO " . table_posts . " SET post_orig_url = %s, post_title = %s, post_url = %s, post_content = %s, post_status = %s, post_author = %d, post_date = CURRENT_TIMESTAMP, post_updateby = %d";
		
		$db->query($db->prepare($sql, urlencode($this->post_orig_url), urlencode(trim($this->post_title)), urlencode(trim($this->post_url)), urlencode(trim($this->post_content)), $this->post_status, $this->post_author, $current_user->id));
		
		$last_insert_id = $db->get_var($db->prepare("SELECT LAST_INSERT_ID()"));
				
		$plugin->check_actions('submit_class_post_add_post');
		
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
	 *  Parameters: array of search parameters
	 *  Purpose: Gets all the posts from the database
	 *  Notes: Example usage: $post->get_posts(array('post_tags LIKE %s' => '%tokyo%'));
	 ********************************************************************** */	
	 	
	function get_posts($vars = array()) {
		global $db;
		
		$filter = '';
		$prepare_array = array();
		$prepare_array[0] = "temp";	// placeholder to be later filled with the SQL query.
		
		if(!empty($vars)) {
			$filter = " WHERE ";
			foreach($vars as $key => $value) {
				$filter .= $key . " AND ";	// e.g. " post_tags LIKE %s "
				array_push($prepare_array, $value);
			}
			$filter = rstrtrim($filter, "AND ");
		}
		
		$sql = "SELECT * FROM " . table_posts . $filter . " ORDER BY post_date DESC";
				
		$prepare_array[0] = $sql;
				
		$posts = $db->get_results($db->prepare($prepare_array));
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
	
	
	
	/* ******************************************************************** 
	 *  Function: rss_feed
	 *  Parameters: 
	 *  Purpose: Publishes content as an RSS feed
	 *  Notes: Uses the 3rd party RSS Writer class.
	 ********************************************************************** */	
	 	
	function rss_feed() {
		global $db, $lang, $cage;
		require_once(includes . 'RSSWriterClass/rsswriter.php');
		
		$status = $cage->get->testAlpha('status');
		$limit = $cage->get->getInt('limit');
		
		if(!$status) { $status = "new"; }
		if(!$limit) { $limit = 10; }
		
		$feed = new RSS();
		$feed->title       = site_name;
		$feed->link        = baseurl;
		$feed->description = $lang["submit_rss_latest_from"] . " " . site_name;

		$sql = "SELECT * from " . table_posts . " WHERE post_status = %s ORDER BY post_date DESC LIMIT %d";
		$results = $db->get_results($db->prepare($sql, $status, $limit));
		foreach($results as $result) 
		{
			$item = new RSSItem();
			$item->title = stripslashes(urldecode($result->post_title));
			$item->link  = urldecode($result->post_url);
			$item->setPubDate($result->post_date); 
			$item->description = "<![CDATA[ " . stripslashes(urldecode($result->post_content)) . " ]]>";
			$feed->addItem($item);
		}
		echo $feed->serve();
	}
}

?>