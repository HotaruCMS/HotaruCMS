<?php

/* The sp (secret project) class is used of the current environment. It contains methods that deal with pages, etc. */

// includes
require_once('hotaru_header.php');
require_once(functions . 'funcs.page.php');

class Hotaru {
	
	function get_page()  {
		$current_page = curPageName();
		return $current_page;
	}
	
	function display_template($page)  {
		
		/* First tries to load the template from the user specified custom theme, 
		   and falls back on the default theme if not found. */
		if(file_exists(themes . current_theme . $page)) {
			include_once(themes . current_theme . $page);
		} elseif(file_exists(themes . $page)) {
			include_once(themes . $page);
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
}

