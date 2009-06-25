<?php

/* **************************************************************************************************** 
 *  File: /index.php
 *  Purpose: Determines which page to display, and then displays it.
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
