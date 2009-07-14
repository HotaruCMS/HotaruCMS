<?php

/* ********** PLUGIN *********************************************************************************
 * name: Submit
 * description: Submit and manage stories.
 * version: 0.1
 * folder: submit
 * prefix: sub
 * hooks: submit, hotaru_header, header_include, install_plugin_starter_settings, navigation_last, theme_index_main, admin_plugin_settings, admin_sidebar_plugin_settings
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
	global $hotaru, $lang, $cage, $plugin, $post;
	
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
	
	$plugin->check_actions('submit_hotaru_header');
	
	if(is_numeric($hotaru->get_page_name())) {
		// Page name is a number so it must be a post with non-friendly urls
		$post->read_post($hotaru->get_page_name());	// read current post
		
	} elseif($post_id = $post->is_post_url($hotaru->get_page_name())) {
		// Page name belongs to a story
		$post->read_post($post_id);	// read current post
		
	} else {
		$post->read_post();	// read current post settings only
	}
		
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
	global $hotaru, $cage, $post, $current_user;
		
	// Pages you have to be logged in for...
	if($current_user->logged_in) {
		 if($hotaru->is_page('submit')) {
		  	
		 	// Include the form if we haven't already...
		 	require_once(plugins . 'submit/submit_form_1.php');
		 	
	 		// Nothing submitted yet, show the submission form...
			$post_orig_url = sub_submit_form_1();
			if($post_orig_url) { 
				header("Location: " . baseurl . "index.php?page=submit2&sourceurl=" . $post_orig_url);  
			} 
			return true;
			
		} elseif($hotaru->is_page('submit2')) {
		 	
		 	// Include submit_form_2...
		 	require_once(plugins . 'submit/submit_form_2.php');
		 	
		 	// Pass the source url to submit_form_2...
		 	$post_orig_url = $cage->get->testUri('sourceurl');
		 	$post_orig_title = sub_fetch_title($post_orig_url);
			$success = sub_submit_form_2($post_orig_url, $post_orig_title);
			if($success) { 
				sub_process_submission($post_orig_url);
				header("Location: " . baseurl);	// Go home  
			} 
			return true;
							
		} elseif($hotaru->is_page('main')) {
			// Show the list of posts
			$hotaru->display_template('posts_list', 'submit');
			return true;
			
		} elseif(is_numeric($hotaru->get_page_name())) {
			// Page name is a number so it must be a post with non-friendly urls
			$hotaru->display_template('post_page', 'submit');
			return true;
			
		} elseif($post->is_post_url($hotaru->get_page_name())) {
			// Page name belongs to a story
			$hotaru->display_template('post_page', 'submit');
			return true;
			
		} else {		
			return false;
		}
	}

	return false;
}


/* ******************************************************************** 
 *  Function: sub_admin_sidebar_plugin_settings
 *  Parameters: None
 *  Purpose: Puts a link to the settings page in the Admin sidebar under Plugin Settings
 *  Notes: ---
 ********************************************************************** */
 
function sub_admin_sidebar_plugin_settings() {
	echo "<li><a href='" . url(array('page'=>'plugin_settings', 'plugin'=>'submit'), 'admin') . "'>Submit</a></li>";
}


 /* ******************************************************************** 
 *  Function: sub_admin_plugin_settings
 *  Parameters: None
 *  Purpose: Calls the function for displaying Admin settings
 *  Notes: ---
 ********************************************************************** */
 
function sub_admin_plugin_settings() {
	require_once(plugins . 'submit/submit_settings.php');
	sub_settings();
	return true;
}


 /* ******************************************************************** 
 *  Function: sub_fetch_title
 *  Parameters: None
 *  Purpose: Scrapes the title from the page being submitted
 *  Notes: ---
 ********************************************************************** */
 
function sub_fetch_title($url) {
	global $cage;
	
	require_once(includes . 'SWCMS/class.httprequest.php');
	
	if($url != 'http://' && $url != ''){
		$r = new HTTPRequest($url);
		$string = $r->DownloadToString();
	} else {
		$string = '';
	}
	
	if(preg_match('/charset=([a-zA-Z0-9-_]+)/i', $string , $matches)) {
		$encoding=trim($matches[1]);
		//you need iconv to encode to utf-8
		if(function_exists("iconv"))
		{
			if(strcasecmp($encoding, 'utf-8') != 0) {
				//convert the html code into utf-8 whatever encoding it is using
				$string=iconv($encoding, 'UTF-8//IGNORE', $string);
			}
		}
	}
		
	
	if(mb_ereg("<title>([^<].*)</title>", $string, $matches)) {
		$title = trim($matches[1]);
	} else {
		$title = "No title found...";
	}
	
	return $title;
}
	
?>