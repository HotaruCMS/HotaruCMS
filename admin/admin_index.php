<?php

/* ******************************************************************** 
 *  File: /admin/admin_index.php
 *  Purpose: Determines which page of Admin should be shown.
 *  Notes: ---
 ********************************************************************** */
 
// includes
require_once('../hotaru_header.php');
require_once(libraries . 'class.hotaru.php');

$hotaru = new Hotaru();
$hotaru->set_is_page_all_false();

$page = $cage->get->getAlnum('page');
switch ($page) {
	case "plugins":
		$hotaru->is_admin_plugins = true;
		require_once(libraries . 'class.plugins.php');
		$plugins = new Plugin();
		break;
	default:
		$hotaru->is_admin_home = true;
		break;
}

$hotaru->display_admin_template('index');	// gets the name of the current page, e.g. index.php and displays the equivalent file from the themes folder.
?>
