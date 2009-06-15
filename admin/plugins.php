<?php

// includes
require_once('../hotaru_header.php');
require_once(libraries . 'class.hotaru.php');
require_once(libraries . 'class.plugins.php');

$plugins = new Plugin();
$hotaru = new Hotaru();
$hotaru->display_admin_template('index.php');	// gets the name of the current page, e.g. index.php and displays the equivalent file from the themes folder.

?>