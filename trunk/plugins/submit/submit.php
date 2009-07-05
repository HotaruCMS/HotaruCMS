<?php

/* ********** PLUGIN *********************************************************************************
 * name: Submit
 * description: Submit and manage stories.
 * version: 0.1
 * folder: submit
 * prefix: sub
 * hooks: submit, hotaru_header, header_include, install_plugin_starter_settings, navigation_last, theme_index_main
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
 
return false; die(); // die on direct access.


/* ******************************************************************** 
 *  Function: sub_submit
 *  Parameters: None
 *  Purpose: 
 *  Notes: 
 ********************************************************************** */

function sub_submit(&$parameters) {

}


/* ******************************************************************** 
 *  Function: sub_install_plugin_starter_settings
 *  Parameters: None
 *  Purpose: If they don't already exist, posts and postmeta tables are created
 *  Notes: Happens when the plugin is installed. The tables are never deleted.
 ********************************************************************** */
 
function sub_install_plugin_starter_settings() {
	global $db, $plugin;
	
	// Create a new empty table called "posts"
	$exists = $db->table_exists('posts');
	if(!$exists) {
		//echo "table doesn't exist. Stopping before creation."; exit;
		$sql = "CREATE TABLE `" . db_prefix . "posts` (
		  `post_id` int(20) NOT NULL auto_increment,
		  `post_author` int(20) NOT NULL default 0,
		  `post_category` int(20) NOT NULL default 0,
		  `post_status` varchar(32) NOT NULL default 'processing',
		  `post_date` timestamp NOT NULL,
		  `post_title` varchar(255) NULL, 
		  `post_orig_url` varchar(255) NULL, 
		  `post_url` varchar(255) NULL, 
		  `post_content` text NULL,
		  `post_tags` text NULL,
		  PRIMARY KEY  (`post_id`),
		  FULLTEXT (`post_title`, `post_url`, `post_content`, `post_tags`),
		) TYPE = MyISAM;";
		$db->query($sql); 
	}
	
	// Create a new empty table called "postmeta"
	$exists = $db->table_exists('postmeta');
	if(!$exists) {
		//echo "table doesn't exist. Stopping before creation."; exit;
		$sql = "CREATE TABLE `" . db_prefix . "postmeta` (
		  `postmeta_id` int(20) NOT NULL auto_increment,
		  `post_id` int(20) NOT NULL default 0,
		  `post_key` varchar(255) NULL,
		  `post_value` text NULL,
		  PRIMARY KEY  (`postmeta_id`),
		  INDEX  (`post_id`)
		) TYPE = MyISAM;";
		$db->query($sql); 
	}
}


/* ******************************************************************** 
 *  Function: sub_hotaru_header
 *  Parameters: None
 *  Purpose: Defines global "table_posts" and "table_postmeta" constants for referring to the db tables
 *  Notes: ---
 ********************************************************************** */
 
function sub_hotaru_header() {
	global $lang, $cage;
	
	define("table_posts", db_prefix . 'posts');
	define("table_postmeta", db_prefix . 'postmeta');
	
	// include submit language file
	if(file_exists(plugins . 'submit/languages/submit_' . strtolower(sitelanguage) . '.php')) {
		require_once(plugins . 'submit/languages/submit_' . strtolower(sitelanguage) . '.php');	// language file for admin
	} else {
		require_once(plugins . 'submit/languages/submit_english.php');	// English file if specified language doesn't exist
	}
	
	require_once(plugins . 'submit/class.post.php');
	require_once(includes . 'Paginated/Paginated.php');
	require_once(includes . 'Paginated/DoubleBarLayout.php');
		
	$post = new Post();
	$vars['post'] = $post; 
	return $vars; 
}


/* ******************************************************************** 
 *  Function: sub_navigation_last
 *  Parameters: None
 *  Purpose: Adds a "submit a story" link to the end of the navigation bar
 *  Notes: 
 ********************************************************************** */

function sub_navigation_last() {	
	global $current_user;
	
	if($current_user->logged_in) {
		echo "<li><a href='" . url(array('page'=>'submit')) . "'>Submit a Story</a></li>\n";
	}
}


/* ******************************************************************** 
 *  Function: sub_header_include
 *  Parameters: None
 *  Purpose: Includes javascript for fetching remote url content.
 *  Notes: ---
 ********************************************************************** */
 
function sub_header_include() {

	echo "<script language='JavaScript' src='" . baseurl . "javascript/hotaru_ajax.js'></script>\n";
	echo "<link rel='stylesheet' href='" . baseurl . "plugins/submit/submit.css' type='text/css'>\n";
}


/* ******************************************************************** 
 *  Function: usr_theme_index_display
 *  Parameters: None
 *  Purpose: Echos the login form to index.php 
 *  Notes: Previously directed to a login.php template file included in this plugin, but decided a function was better. (Nick)
 ********************************************************************** */
 
function sub_theme_index_main() {
	global $hotaru, $cage, $current_user;
		
	// Pages you have to be logged in for...
	if($current_user->logged_in) {
		 if($hotaru->is_page('submit')) {
		 
		 	// Include the form if we haven't already...
		 	require_once(plugins . 'submit/submitform.php');
		 	
	 		// Nothing submitted yet, show the submission form...
			$success = sub_submitform();
			if($success) { 
				sub_process_submission(); // Save story
				header("Location: " . baseurl);  // Go home
			} 
			return true;

		} elseif($hotaru->is_page('main')) {
			// First time here, let's show the form...
			$hotaru->display_template('show_post', 'submit');
			return true;
		} else {
			return false;
		}
	}
	return false;
}


/* ******************************************************************** 
 *  Function: sub_process_submission
 *  Parameters: None
 *  Purpose: Saves the submitted story to the database
 *  Notes: ---
 ********************************************************************** */
 
function sub_process_submission() {
	global $hotaru, $cage, $current_user, $post;
		
	$post->source_url = $cage->post->testUri('source_url');
	$post->post_title = $cage->post->noTags('post_title');
	$post->post_status = "new";
	$post->post_author = $current_user->id;
	$post->add_post();

}


/* ******************************************************************** 
 *  Function: sub_check_for_errors
 *  Parameters: None
 *  Purpose: Checks a submitted form for errors
 *  Notes: ---
 ********************************************************************** */

function sub_check_for_errors() {
	global $hotaru, $post, $cage, $lang;

	// ******** CHECK URL ********
	
	$source_url_check = $cage->post->testUri('source_url');
	if(!$source_url_check) {
		// No url present...
		$hotaru->message = $lang['submit_submitform_url_not_present_error'];
		$hotaru->message_type = 'red';
		$hotaru->show_message();
		$error = 1;
	} elseif($post->url_exists($source_url_check)) {
		// URL already exists...
		$hotaru->message = $lang['submit_submitform_url_already_exists_error'];
		$hotaru->message_type = 'red';
		$hotaru->show_message();
		$error = 1;
	} else {
		// URL is okay.
		$error = 0;
	}
	
	// ******** CHECK TITLE ********
	
	$story_title_check = $cage->post->noTags('post_title');	
	if(!$story_title_check) {
		// No title present...
		$hotaru->message = $lang['submit_submitform_title_not_present_error'];
		$hotaru->message_type = 'red';
		$hotaru->show_message();
		$error = 1;
	} elseif($post->title_exists($story_title_check)) {
		// URL already exists...
		$hotaru->message = $lang['submit_submitform_title_already_exists_error'];
		$hotaru->message_type = 'red';
		$hotaru->show_message();
		$error = 1;
	} else {
		// title is okay.
		$error = 0;
	}
	
	// Return true if error is found
	if($error == 1) { return true; } else { return false; }
}



?>