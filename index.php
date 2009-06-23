<?php

/* ******************************************************************** 
 *  File: /index.php
 *  Purpose: Determines which page to display, and then displays it.
 *  Notes: ---
 ********************************************************************** */
	 
// includes
require_once('hotaru_header.php');
$hotaru->set_is_page_all_false();

$page = $cage->get->testRegex('page', '/^([a-z0-9_-])+$/i');
switch ($page) {
	case "user_settings":
		$hotaru->is_user_settings = true;
		break;
	case "":
		$hotaru->is_home = true;
		break;
	default:
		if(!$hotaru->is_page($page)) {
			$hotaru->is_home = true;	
		}
		break;
}

$hotaru->display_template('index');	// gets the name of the current page, e.g. index.php and displays the equivalent file from the themes folder.
?>
