<?php

/* ********** PLUGIN *********************************************************************************
 * name: Submit
 * description: Submit and manage stories.
 * version: 0.1
 * folder: submit
 * prefix: sub
 * hooks: hotaru_header, header_include, install_plugin, navigation, theme_index_replace, theme_index_main, admin_plugin_settings, admin_sidebar_plugin_settings, submit_show_post_extras
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
 *  Function: sub_install_plugin
 *  Parameters: None
 *  Purpose: If they don't already exist, posts and postmeta tables are created
 *  Notes: Happens when the plugin is installed. The tables are never deleted.
 ********************************************************************** */
 
function sub_install_plugin() {
	global $db, $plugin, $post;
	
	// Create a new empty table called "posts"
	$exists = $db->table_exists('posts');
	if(!$exists) {
		//echo "table doesn't exist. Stopping before creation."; exit;
		$sql = "CREATE TABLE `" . db_prefix . "posts` (
		  `post_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  `post_author` int(20) NOT NULL DEFAULT 0,
		  `post_category` int(20) NOT NULL DEFAULT 1,
		  `post_status` varchar(32) NOT NULL DEFAULT 'processing',
		  `post_date` timestamp NULL,
		  `post_title` varchar(255) NULL, 
		  `post_orig_url` varchar(255) NULL, 
		  `post_url` varchar(255) NULL, 
		  `post_content` text NULL,
		  `post_tags` text NULL,
		  `post_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
 		  `post_updateby` int(20) NOT NULL DEFAULT 0, 
		  FULLTEXT (`post_title`, `post_url`, `post_content`, `post_tags`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Story Posts';";
		$db->query($sql); 
	}
	
	// Create a new empty table called "postmeta"
	$exists = $db->table_exists('postmeta');
	if(!$exists) {
		//echo "table doesn't exist. Stopping before creation."; exit;
		$sql = "CREATE TABLE `" . db_prefix . "postmeta` (
		  `postmeta_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  `postmeta_postid` int(20) NOT NULL DEFAULT 0,
		  `postmeta_key` varchar(255) NULL,
		  `postmeta_value` text NULL,
		  `postmeta_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
 		  `postmeta_updateby` int(20) NOT NULL DEFAULT 0, 
		  INDEX  (`postmeta_postid`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Post Meta';";
		$db->query($sql); 
	}
	
	// Default settings (Note: we can't use $post because it hasn't been filled yet.)
	$plugin->plugin_settings_update('submit', 'submit_author', 'checked');	
	$plugin->plugin_settings_update('submit', 'submit_date', 'checked');
	$plugin->plugin_settings_update('submit', 'submit_content', 'checked');	
	$plugin->plugin_settings_update('submit', 'submit_content_length', 50);	
	
	// Include language file. Also included in hotaru_header, but needed here so 
	// that the link in the Admin sidebar shows immediately after installation.
	$plugin->include_language_file('submit');	
	
}


/* ******************************************************************** 
 *  Function: sub_hotaru_header
 *  Parameters: None
 *  Purpose: Defines global "table_posts" and "table_postmeta" constants for referring to the db tables
 *  Notes: ---
 ********************************************************************** */
 
function sub_hotaru_header() {
	global $hotaru, $lang, $cage, $plugin, $post;
	
	if(!defined('table_posts')) { define("table_posts", db_prefix . 'posts'); }
	if(!defined('table_postmeta')) { define("table_postmeta", db_prefix . 'postmeta'); }

	// include language file
	$plugin->include_language_file('submit');
	
	require_once(plugins . 'submit/class.post.php');
	require_once(includes . 'Paginated/Paginated.php');
	require_once(includes . 'Paginated/DoubleBarLayout.php');
		
	$post = new Post();
	
	$plugin->check_actions('submit_hotaru_header_1');
	
	if(is_numeric($hotaru->get_page_name())) {
		// Page name is a number so it must be a post with non-friendly urls
		$post->read_post($hotaru->get_page_name());	// read current post
		
	} elseif($post_id = $post->is_post_url($hotaru->get_page_name())) {
		// Page name belongs to a story
		$post->read_post($post_id);	// read current post
		
	} else {
		$post->read_post();	// read current post settings only
	}
	
	$plugin->check_actions('submit_hotaru_header_2');
		
	$vars['post'] = $post; 
	return $vars; 
}


/* ******************************************************************** 
 *  Function: sub_navigation
 *  Parameters: None
 *  Purpose: Adds a "submit a story" link to the navigation bar
 *  Notes: 
 ********************************************************************** */

function sub_navigation() {	
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
}


/* ******************************************************************** 
 *  Function: sub_theme_index_replace
 *  Parameters: None
 *  Purpose: Checks results from submit form 2.
 *  Notes: ---
 ********************************************************************** */
 
function sub_theme_index_replace() {
	global $hotaru, $cage, $post, $plugin, $current_user;
	
	if($hotaru->is_page('submit2')) {
	 	
		if($current_user->logged_in) {
		 	
		 	if($cage->post->getAlpha('submit2') == 'true') {		 	
		 		// Include submit_form_2...
		 		require_once(plugins . 'submit/submit_step2.php');		 		
			 	if(!sub_check_for_errors_2()) { 
			 		$post_orig_url = $cage->post->testUri('post_orig_url'); 
					sub_process_submission($post_orig_url);
					header("Location: " . baseurl);	// Go home  
					die();
				}
			}
		}
	}

	return false;
}


/* ******************************************************************** 
 *  Function: sub_theme_index_main
 *  Parameters: None
 *  Purpose: Determines which submit page to display
 *  Notes: ---
 ********************************************************************** */
 
function sub_theme_index_main() {
	global $hotaru, $cage, $post, $plugin, $current_user;
		
	 if($hotaru->is_page('submit')) {
	  	
	  	if($current_user->logged_in) {
	  		
	  		require_once(plugins . 'submit/submit_step1.php');
	  		require_once(plugins . 'submit/submit_step2.php');
	  		
	  		if($cage->post->getAlpha('submit1') == 'true') {
				if(!sub_check_for_errors_1()) { 
					// No errors found, proceed to step 2
					$post_orig_url = $cage->post->testUri('post_orig_url'); 
					$post_orig_title = sub_fetch_title($post_orig_url);
					sub_submit_form_2($post_orig_url, $post_orig_title);
					return true;
					
				} else {
					// Errors found, go back to step 1
					$post_orig_url = $cage->post->testUri('post_orig_url');
					sub_submit_form_1($post_orig_url);
					return true;
				}
			} else {
				// First time to step 1...
				sub_submit_form_1();
				return true;
			}
		} else {
			return false;
		}
		
	} elseif($hotaru->is_page('submit2')) {
	 	
		if($current_user->logged_in) {
		 	
		 	if($cage->post->getAlpha('submit2') == 'true') {		 	
		 		// Include submit_form_2...
		 		require_once(plugins . 'submit/submit_step2.php');
		 		$post_orig_url = $cage->post->testUri('post_orig_url'); 		 		
			 	sub_submit_form_2($post_orig_url);
			 	return true;
			}
		}
											
	} elseif($hotaru->is_page('main')) {
	
		// Plugin hook
		$result = $plugin->check_actions('submit_is_page_main');
		if($result && is_array($result)) { return true; }
	
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
		
	
	if(preg_match("'<title>([^<]*?)</title>'", $string, $matches)) {
		$title = trim($matches[1]);
	} else {
		$title = "No title found...";
	}
	
	return $title;
}


 /* ******************************************************************** 
 *  Function: sub_submit_show_post_extras
 *  Parameters: None
 *  Purpose: Adds a permalink
 *  Notes: ---
 ********************************************************************** */
 
function sub_submit_show_post_extras() {
	global $post, $plugin, $cage;
	
	if(!$plugin->plugin_active('categories')) {
		echo "<a href='" . url(array('page'=>$post->post_id)) . "'>Permalink</a>";
	}
}

	
?>