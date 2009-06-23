<?php

/* ******************************************************************** 
 *  File: /libraries/class.hotaru.php
 *  Purpose: The Hotaru class is used for the current environment. It contains methods that deal with pages, etc.
 *  Notes: ---
 ********************************************************************** */
 
// includes


class Hotaru {
	
	var $is_debug = false;
	var $is_home = false;
	var $is_admin_home = false;
	var $is_admin_plugins = false;
	var $is_admin_plugin_settings = false;
	var $is_user_settings = false;
	
	
	/* ******************************************************************** 
	 *  Function: set_is_page_all_false
	 *  Parameters: None
	 *  Purpose: When a new page is loaded, all others are set to false.
	 *  Notes: ---
	 ********************************************************************** */
	
	function set_is_page_all_false() {
		$this->is_home = false;
		$this->is_admin_home = false;
		$this->is_admin_plugins = false;
		$this->is_admin_plugin_settings = false;
		$this->is_user_settings = false;
	}
	
	
	/* ******************************************************************** 
	 *  Function: is_custom_page
	 *  Parameters: Custom page name (filename without .php)
	 *  Purpose: Checks to see if the page we are checking for is the on we're actually on
	 *  Notes: E.g. $hotaru->is_custom_page('custom2') returns true if page=custom2 in the url.
	 ********************************************************************** */
	 
	function is_custom_page($page = '') {
		global $cage;
		$real_page = $cage->get->testRegex('page', '/^([a-z0-9_-])+$/i');

		if($real_page == $page) {
			return true;
		} else {
			return false;
		}
	}
	

	/* ******************************************************************** 
	 *  Function: display_template
	 *  Parameters: page name (filename without.php)
	 *  Purpose: First looks in the user's chosen theme directory, if not there, gets the file from the default theme.
	 *  Notes: ---
	 ********************************************************************** */

	function display_template($page)  {
		
		/* First tries to load the template from the user specified custom theme, 
		   and falls back on the default theme if not found. */
		$page = $page . '.php';
		if(file_exists(themes . current_theme . $page)) {
			include_once(themes . current_theme . $page);
		} elseif(file_exists(themes . 'default/' . $page)) {
			include_once(themes . 'default/' . $page);
		} else {
			include_once(themes . '404.php');
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
	 
	function display_admin_template($page)  {
		/* First tries to load the template from the user specified custom theme, 
		   and falls back on the default theme if not found. */
		$page = $page . '.php';
		if(file_exists(admin_themes . current_admin_theme . $page)) {
			include_once(admin_themes . current_admin_theme . $page);
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
	 *  Function: check_announcements
	 *  Parameters: --- 
	 *  Purpose: Returns an announcement for display at the top of Admin.
	 *  Notes: Currently only checks if the install folder has been deleted.
	 ********************************************************************** */
	 
	function check_announcements() {
		global $lang;
		// Check if the install file has been deleted:
		$filename = install . 'install.php';
		if(file_exists($filename)) {
			return $lang['admin_announcement_delete_install'];
		} else {
			return false;
		}
	}
}

