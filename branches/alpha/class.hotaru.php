<?php

/* **************************************************************************************************** 
 *  File: /libraries/class.hotaru.php
 *  Purpose: The Hotaru class is used for the current environment. It contains methods that deal with pages, etc.
 *  Notes: ---
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
 
class Hotaru {
	
	var $is_debug = false;
	var $sidebar = true;
	var $message = '';
	var $message_type = 'green';	// green or red, color of message box
	var $messages = array();	// for multiple messages
	var $template_name = '';	// e.g. 'posts_list'
	
	
	/* ******************************************************************** 
	 *  Function: read_settings
	 *  Parameters: None
	 *  Purpose: Returns all setting-value pairs
	 *  Notes: ---
	 ********************************************************************** */
	 
	function read_settings() {
		global $db;
		$sql = "SELECT * FROM " . table_settings;
		$results = $db->get_results($db->prepare($sql));
		if($results) { return $results; } else { return false; }
	}
	
	
	/* ******************************************************************** 
	 *  Function: is_page
	 *  Parameters: a page name (filename without .php)
	 *  Purpose: Checks to see if the page we are checking for is the one we're actually on
	 *  Notes: E.g. $hotaru->is_page('login') returns true if page=login in the url.
	 ********************************************************************** */
	 
	function is_page($page = '') {
		global $cage;
		$real_page = $cage->get->testPage('page');
		
		if(!$real_page) { 
			// Possibly a post with multi-byte characters? Try getMixedString2...
			$real_page = $cage->get->getMixedString2('page');
		}
			
		// Try POST...
		if(!$real_page) { $real_page = $cage->post->testPage('page'); }
		
		if(!$real_page) { $real_page = "main"; }

		$real_page = rtrim($real_page, '/');	// remove trailing slash

		if($real_page == $page) {
			return $page;
		} else {
			return false;
		}
	}
	
	
	/* ******************************************************************** 
	 *  Function: get_page_name
	 *  Parameters: None
	 *  Purpose: Returns the page name, e.g. login, user_settings, etc.
	 *  Notes: This only works if there is a page=name in the url, else defaults to 'main' (which might not be accurate). 
	 ********************************************************************** */
	 
	function get_page_name() {
		global $cage;
		
		// Try GET...
		$page = $cage->get->testPage('page');
		if(!$page) {
			// Possibly a post with multi-byte characters? Try getMixedString2...
			$page = $cage->get->getMixedString2('page');
		}
		
		// Try POST...
		if(!$page) { $page = $cage->post->testPage('page'); }

		if($page) {
			$page = rtrim($page, '/');
			return $page;
		} else {
			return 'main';
		}
	}
	

	/* ******************************************************************** 
	 *  Function: display_template
	 *  Parameters: page name (filename without.php)
	 *  Purpose: First looks in the user's chosen theme directory, if not there, gets the file from the default theme.
	 *  Notes: ---
	 ********************************************************************** */

	function display_template($page = '', $plugin = '')  {
		
		$page = $page . '.php';
				
		/* First check if there's a specified plugin for the file and load 
		   the template from the plugin folder if it's there. */
		if($plugin != '') {
			if(file_exists(plugins .  $plugin . '/templates/' . $page)) {
				include_once(plugins . $plugin . '/templates/' . $page);
				return true;
				die();
			}
		}
		
		// Check the custom theme then the default theme...		
		if(file_exists(themes . theme . $page)) {
			include_once(themes . theme . $page);
		} elseif(file_exists(themes . 'default/' . $page)) {
			include_once(themes . 'default/' . $page);
		} else {
			include_once(themes . '404.php');
		}
	}
	
	
	/* ******************************************************************** 
	 *  Function: check_announcements
	 *  Parameters: --- 
	 *  Purpose: Returns an announcement for display at the top of each page.
	 *  Notes: ---
	 ********************************************************************** */
	 
	function check_announcements() {
		global $lang, $plugin;
		
		$announcements = array();

		// 1. "All plugins are currently disabled."
		if(!$plugin->num_active_plugins()) {
			array_push($announcements, $lang['main_announcement_plugins_disabled']);	
		}
				
		// 2. User login and registration currently disabled.
		/*
		if(!$plugin->plugin_active('users')) {
			array_push($announcements, $lang['main_announcement_users_disabled']);	
		}
		*/
		 
		if(!is_array($announcements)) {
			return false;
		} else {
			return $announcements;
		}
	}
	
	
	/* ******************************************************************** 
	 *  Function: get_simplepie
	 *  Parameters: None
	 *  Purpose: includes the SimplePie RSS file and sets the cache
	 *  Notes: ---
	 ********************************************************************** */
	 		
	function new_simplepie($feed='', $cache=true, $cache_duration=10)  {
		include_once(includes . "SimplePie/simplepie.inc");
		
		if($feed != '') {
			$sp = new SimplePie();
			$sp->set_feed_url($feed);
			$sp->set_cache_location(includes . "SimplePie/cache/");
			$sp->set_cache_duration($cache_duration);
			$sp->enable_cache($cache);
			$sp->handle_content_type();
			return $sp;
		} else { 
			return false; 
		}
	}
	
	
	/* ******************************************************************** 
	 *  Function: show_queries_and_time
	 *  Parameters: None
	 *  Purpose: Shows number of database queries and the time ittakes for a page to load
	 *  Notes: ---
	 ********************************************************************** */
	 
	function show_queries_and_time() {
		global $db;
		if($this->is_debug) { 
			echo "<p>" . $db->num_queries . " " . $lang['main_hotaru_queries_time'] . " " . timer_stop(1) . " " . $lang['main_hotaru_seconds'] . "</p>"; 
		} 
	}	
	
	
	/* ******************************************************************** 
	 *  Function: show_message
	 *  Parameters: Optional message and message type ('green' or 'red')
	 *  Purpose: Displays a SINGLE success or failure message
	 *  Notes: Two ways to use this: 
	 *	Longhand:
	 *	 	$hotaru->message = "This is a message";
	 *		$hotaru->message_type = "green";
	 *		$hotaru->show_message();
	 *		
	 *	Shorthand:
	 *		$hotaru->show_message("This is a message", "green");
	 ********************************************************************** */
	 	
	function show_message($msg = '', $msg_type = 'green') {
		if($msg != '') {
			echo "<div class='message " . $msg_type . "'>" . $msg . "</div>";
		} elseif($this->message != '') {
			echo "<div class='message " . $this->message_type . "'>" . $this->message . "</div>";
		}
	}
	
	
	/* ******************************************************************** 
	 *  Function: show_messages
	 *  Parameters: None
	 *  Purpose: Displays ALL success or failure messages
	 *  Notes: Use like this:
	 *	 	$hotaru->messages['This is a message'] = "green";
	 *		$hotaru->show_messages();
	 ********************************************************************** */
	 	
	function show_messages() {
		if(!empty($this->messages)) {
			foreach($this->messages as $msg => $msg_type) {
				echo "<div class='message " . $msg_type . "'>" . $msg . "</div>";
			}
		}
	}
}
?>