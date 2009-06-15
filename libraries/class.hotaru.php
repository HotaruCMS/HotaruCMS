<?php

/* The sp (secret project) class is used of the current environment. It contains methods that deal with pages, etc. */

// includes
if(file_exists('hotaru_header.php')) {
	require_once('hotaru_header.php');	// assumes we are in the root directory
} else {
	require_once('../hotaru_header.php');	// assumes we are one level deep, e.g. in the admin directory
}

require_once(functions . 'funcs.page.php');

class Hotaru {
	
	function get_page()  {
		$current_page = curPageName();
		return $current_page;
	}
	
	/* ***************************************************************************
	******************************************************************************
	******************************************************************************
	******************************************************************************
	
			FRONT END
			
	******************************************************************************
	******************************************************************************
	*************************************************************************** */

	function display_template($page)  {
		
		/* First tries to load the template from the user specified custom theme, 
		   and falls back on the default theme if not found. */
		if(file_exists(themes . current_theme . $page)) {
			include_once(themes . current_theme . $page);
		} elseif(file_exists(themes . 'default/' . $page)) {
			include_once(themes . 'default/' . $page);
		} else {
			include_once(themes . '404.php');
		}
	}

	function display_stories($limit=10, $sort="latest")  {
		// call a function to return story links.
		// TEMP:
		$stories = '';
		for($i=0; $i<$limit; $i++) {
			$stories .= "This is story number " . ($i+1) . ". The real stories will be pulled in from a separate template so they can be styled more easily.<br /><br />\n";
		}
		return $stories;
	}
		
	function display_story_links($limit=10, $type="upcoming", $before="<li>", $after="</li>")  {
		// call a function to return story links.
		// TEMP:
		$story_links = '';
		for($i=0; $i<$limit; $i++) {
			$story_links .= $before . "story link " . ($i+1) . $after . "\n";
		}
		return $story_links;
	}
	
	/* ***************************************************************************
	******************************************************************************
	******************************************************************************
	******************************************************************************
	
			ADMIN SECTION
			
	******************************************************************************
	******************************************************************************
	*************************************************************************** */
	
	function display_admin_template($page)  {
		/* First tries to load the template from the user specified custom theme, 
		   and falls back on the default theme if not found. */
		if(file_exists(admin_themes . current_admin_theme . $page)) {
			include_once(admin_themes . current_admin_theme . $page);
		} elseif(file_exists(admin_themes . 'default/' . $page)) {
			include_once(admin_themes . 'default/' . $page);
		} else {
			include_once(admin_themes . '404.php');
		}
	}
	
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

