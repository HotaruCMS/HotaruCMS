<?php

/* ******************************************************************** 
 *  File: /libraries/class.hotaru.php
 *  Purpose: The Hotaru class is used for the current environment. It contains methods that deal with pages, etc.
 *  Notes: ---
 ********************************************************************** */
 
// includes
if(file_exists('hotaru_header.php')) {
	require_once('hotaru_header.php');	// assumes we are in the root directory
} else {
	require_once('../hotaru_header.php');	// assumes we are one level deep, e.g. in the admin directory
}

require_once(functions . 'funcs.files.php');

class Hotaru {
	
	var $is_home = false;
	var $is_admin_home = false;
	var $is_admin_plugins = false;
	
	
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
}

