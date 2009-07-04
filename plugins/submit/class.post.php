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

	var $source_url = '';
	var $post_title = '';
	var $post_status = 'processing';
	var $post_author = 0;


	/* ******************************************************************** 
	 *  Function: add_post
	 *  Parameters: None
	 *  Purpose: Adds a post to the database
	 *  Notes: ---
	 ********************************************************************** */	
	 
	function add_post() {
		global $db;
		$sql = "INSERT INTO " . table_posts . " SET post_orig_url = %s, post_title = %s, post_status = %s, post_author = %d";
		$db->query($db->prepare($sql, $this->source_url, trim($this->post_title), $this->post_status, $this->post_author));
		return true;
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
		$posts = $db->get_var($db->prepare($sql, $url));
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
		$posts = $db->get_var($db->prepare($sql, $title));
		if($posts > 0) { return $posts; } else { return false; }
	}
}

?>