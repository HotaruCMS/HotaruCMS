<?php

/* **************************************************************************************************** 
 *  File: /hotaru_header.php
 *  Purpose: Includes necessary files and sets globals.
 *  Notes: Included in all files.
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
 
ini_set('display_errors',1);
error_reporting(E_ALL);

session_start();

// include settings
require_once('hotaru_settings.php');


require_once('class.hotaru.php'); 

// include other essential libraries and functions
require_once(includes . 'Inspekt/Inspekt.php');		// for Input sanitation and validation
require_once(includes . 'ezSQL/ez_sql_core.php');		// for database usage
require_once(includes . 'ezSQL/mysql/ez_sql_mysql.php');	// for database usage
require_once('class.plugins.php');				// for plugins
require_once('funcs.urls.php');					// for default or friendly urls

if(file_exists(languages . 'main_' . strtolower(sitelanguage) . '.php')) {
	require_once(languages . 'main_' . strtolower(sitelanguage) . '.php');	// language file for main (not admin, installation or plugins)
} else {
	require_once(languages . 'main_english.php');	// English file if specified language doesn't exist
}

// Initialize database
if(!isset($db)) { $db = new ezSQL_mysql(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST); }

// Initialize Hotaru and start timer if debugging.
if(!isset($hotaru)) { $hotaru = new Hotaru(); }
$settings = $hotaru->read_settings();
foreach($settings as $setting) {
	define($setting->settings_name, $setting->settings_value);
}

if(debug == "on") {
	include_once('funcs.timers.php');
	$hotaru->is_debug = true;
	timer_start();
}

// Global Inspekt SuperCage
if(!isset($cage)) { $cage = Inspekt::makeSuperCage(); }

// Create objects
if(!isset($plugin)) { 
	$plugin = new Plugin(); 
} else {
	if(!is_object($plugin)) {
		$plugin = new Plugin(); 
	}
}

$results = $plugin->check_actions('hotaru_header');	// Enables plugins to define global settings, etc. 

/* The following extracts the results of check_actions - handy for making objects from plugins global */
if(isset($results) && is_array($results)) {
	foreach($results as $key => $value) {
		if(is_array($value)) { extract($value); }
	} 
}
?>
