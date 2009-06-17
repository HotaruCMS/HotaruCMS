<?php

/* ******************************************************************** 
 *  File: /index.php
 *  Purpose: Determines which page to display, and then displays it.
 *  Notes: ---
 ********************************************************************** */
	 
// includes
require_once('hotaru_header.php');
require_once(libraries . 'class.hotaru.php');

$hotaru = new Hotaru();
$hotaru->set_is_page_all_false();

$page = $cage->get->getAlnum('page');
switch ($page) {
	case "upcoming":
		break;
	default:
		$page = 'index';
		$hotaru->is_home = true;
		break;
}

$hotaru->display_template($page);	// gets the name of the current page, e.g. index.php and displays the equivalent file from the themes folder.
?>
