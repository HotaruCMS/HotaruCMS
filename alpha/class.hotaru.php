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
 
// includes

class Hotaru {
	
	var $is_debug = false;
	var $sidebar = true;
	
	
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
		$real_page = $cage->get->testRegex('page', '/^([a-z0-9\/_-])+$/i');
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
		$page = $cage->get->testRegex('page', '/^([a-z0-9\/_-])+$/i');
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
		
		/* First tries to load the template from the user specified custom theme, 
		   and falls back on the default theme if not found. */
		$page = $page . '.php';
		
		/* First check if there's a specified plugin for the file and load 
		   the template from the plugin folder if it's there. */
		if($plugin != '') {
			if(file_exists(plugins .  $plugin . $page)) {
				include_once(plugins . $plugin . $page);
				return;
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
		
		// 1. User login and registration currently disabled.
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
	 *  Function: display_stories
	 *  Parameters: Number of main stories to show and sort order
	 *  Purpose: Displays stories on the index, upcoming, etc. pages.
	 *  Notes: ---
	 ********************************************************************** */
	 
	function display_stories($limit=10, $sort="latest")  {
		// call a function to return story links.
		// TEMP:
		$stories = '';
		for($i=0; $i<$limit; $i++) {
			$stories .= "This is story number " . ($i+1) . ". The real stories will be pulled in from a separate template so they can be styled more easily.<br /><br />\n";
		}
		return $stories;
	}
	
	
	/* ******************************************************************** 
	 *  Function: display_story_links
	 *  Parameters: Number of story links to show, type (e.g. upcoming, top sories) and HTML tags. 
	 *  Purpose: Displays a list of story links in the sidebar (for example).
	 *  Notes: ---
	 ********************************************************************** */
	 		
	function display_story_links($limit=10, $type="upcoming", $before="<li>", $after="</li>")  {
		// call a function to return story links.
		// TEMP:
		$story_links = '';
		for($i=0; $i<$limit; $i++) {
			$story_links .= $before . "story link " . ($i+1) . $after . "\n";
		}
		return $story_links;
	}
	
	
	/* ******************************************************************** 
	 *  Function: get_simplepie
	 *  Parameters: None
	 *  Purpose: includes the SimplePie RSS file and sets the cache
	 *  Notes: ---
	 ********************************************************************** */
	 		
	function new_simplepie($feed='', $cache=true, $cache_duration=10)  {
		include_once(includes . "SimplePie/simplepie.inc");
		
		$sp = new SimplePie();
		$sp->set_feed_url($feed);
		$sp->set_cache_location(includes . "SimplePie/cache/");
		$sp->set_cache_duration($cache_duration);
		$sp->enable_cache($cache);
		$sp->handle_content_type();
		return $sp;
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
			echo "<p>" . $db->num_queries . " database queries and a page load time of " . timer_stop(1) . " seconds.</p>"; 
		} 
	}	
	
	
	
	/* **********************************************************************
	 * **********************************************************************
			ADMIN SECTION
	 * **********************************************************************
	 * ********************************************************************** */
	
	
	/* ******************************************************************** 
	 *  Function: display_admin_template
	 *  Parameters: page name (filename without.php)
	 *  Purpose: First looks in the user's chosen admin theme directory, if not there, gets the file from the default admin theme.
	 *  Notes: ---
	 ********************************************************************** */
	 
	function display_admin_template($page = '', $plugin = '')  {
		/* First tries to load the template from the user specified custom theme, 
		   and falls back on the default theme if not found. */
		$page = $page . '.php';
		
		/* First check if there's a specified plugin for the file and load 
		   the template from the plugin folder if it's there. */
		if($plugin != '') {
			if(file_exists(plugins .  $plugin . $page)) {
				include_once(plugins . $plugin . $page);
				return;
			}
		}
		
		// Check the custom theme then the default theme...		
		if(file_exists(admin_themes . admin_theme . $page)) {
			include_once(admin_themes . admin_theme . $page);
		} elseif(file_exists(admin_themes . 'admin_default/' . $page)) {
			include_once(admin_themes . 'admin_default/' . $page);
		} else {
			include_once(admin_themes . '404.php');
		}
	}
	
	
	/* ******************************************************************** 
	 *  Function: display_admin_links
	 *  Parameters: --- 
	 *  Purpose: Displays a list of admin links in the admin sidebar (for example).
	 *  Notes: ---
	 ********************************************************************** */
	 
	function display_admin_links()  {
		// call a function to return story links.
		// TEMP:
		$admin_links = '';
		for($i=0; $i<10; $i++) {
			$admin_links .= "<li>" . "admin link " . ($i+1) . "</li>" . "\n";
		}
		return $admin_links;
	}
	
	
	/* ******************************************************************** 
	 *  Function: check_admin_announcements
	 *  Parameters: --- 
	 *  Purpose: Returns an announcement for display at the top of Admin.
	 *  Notes: ---
	 ********************************************************************** */
	 
	function check_admin_announcements() {
		global $lang, $plugin;
		// Check if the install file has been deleted:
		
		$announcements = array();
		
		// 1. Check if install file has been deleted
		$filename = install . 'install.php';
		if(file_exists($filename)) {
			array_push($announcements, $lang['admin_announcement_delete_install']);
		} 
		
		/*
		// 2. Please install the Users plugin
		if (!$plugin->plugin_active('users')) {
			array_push($announcements, $lang['admin_announcement_users_disabled']);	
		} 
		*/
		
		if(!is_array($announcements)) {
			return false;
		} else {
			return $announcements;
		}
	}
}

?>