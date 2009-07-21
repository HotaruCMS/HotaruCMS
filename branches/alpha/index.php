<?php

/* **************************************************************************************************** 
 *  File: /index.php
 *  Purpose: Displays the index template
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

// Include language file for MAIN CORE
if(file_exists(languages . 'main_' . strtolower(sitelanguage) . '.php')) {
	require_once(languages . 'main_' . strtolower(sitelanguage) . '.php');	// language file for main (not admin, installation or plugins)
} else {
	require_once(languages . 'main_english.php');	// English file if specified language doesn't exist
}

// Include language file for MAIN THEMES
if(file_exists(themes . theme . 'languages/main_' . strtolower(sitelanguage) . '.php')) {
	require_once(themes . theme . 'languages/main_' . strtolower(sitelanguage) . '.php');	// language file for main (not admin, installation or plugins)
} else {
	require_once(themes . theme . 'languages/main_english.php');	// English file if specified language doesn't exist
}

$hotaru->display_template('index');
?>
