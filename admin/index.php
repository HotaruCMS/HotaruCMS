<?php

// includes
require_once('../hotaru_header.php');
require_once(libraries . 'class.hotaru.php');

$hotaru = new Hotaru();
$hotaru->display_admin_template($hotaru->get_page());	// gets the name of the current page, e.g. index.php and displays the equivalent file from the themes folder.
?>
