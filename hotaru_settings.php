<?php

/* **************************************************************************************************** 
 *  File: /hotaru_settings.php
 *  Purpose: Configuration file for Hotaru CMS.
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
 
/* ************************************************************************************ */
/* *************************** EDIT THE FOLLOWING ONLY ******************************** */
/* ************************************************************************************ */

/* ****** Language ****** */
define("sitelanguage", 'English');		// Used for choosing the right language file - must match the name of the language folder (case insensitive)

/* ****** Database Details ****** */
define("DB_USER", 'root');			// Add your own database details 
define("DB_PASSWORD", '');			
define("DB_NAME", 'hotaru');
define("DB_HOST", 'localhost');			// You probably won't need to change this

define("db_prefix", 'hotaru_');			// Better leave this for now
define("db_lang", 'en');			// Better leave this for now

/* ****** Names ****** */
define("sitename", 'Hotaru CMS');

/* ******Paths ******* */
define('baseurl', "http://localhost/hotaru/branches/trunk");	//e.g. http://www.mysite.com/ or http://www.mysite.com/bookmarking/
							// IMPORTANT!!! Don't forget the trailing slash (/).
/* ****** FRIENDLY URLS ******* */
define('friendly_urls', false);			// true for links like:  /category/finance/
						// false for links like: index.php?category=3

/* ****** Themes ***** */
define("current_theme", "default" . "/");	// change "default for the folder name of your theme.
define("current_admin_theme", "admin_default" . "/");	// change "default for the folder name of your admin theme.

/* ****** Debug ****** */
define("debug", "off");				// "on" will show the number of database queries and page loading time in the template footer

/* ************************************************************************************ */
/* *************************** DON'T EDIT BELOW THIS POINT **************************** */
/* ************************************************************************************ */

// define shorthand paths
define("includes", dirname(__FILE__).'/includes/');
define("install", dirname(__FILE__).'/install/');
define("libraries", dirname(__FILE__).'/libraries/');
define("languages", dirname(__FILE__).'/languages/');
define("functions", dirname(__FILE__).'/functions/');
define("plugins", dirname(__FILE__).'/plugins/');
define("themes", dirname(__FILE__).'/themes/');
define("admin_themes", dirname(__FILE__).'/admin/themes/');

// define database tables
define("table_settings", db_prefix . "settings");
define("table_users", db_prefix . "users");
define("table_plugins", db_prefix . "plugins");
define("table_pluginhooks", db_prefix . "pluginhooks");
define("table_pluginsettings", db_prefix . "pluginsettings");

?>