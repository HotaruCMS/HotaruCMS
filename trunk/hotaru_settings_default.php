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

/* ****** Database Details ****** */
define("DB_USER", 'root');			// Add your own database details 
define("DB_PASSWORD", '');			
define("DB_NAME", 'hotaru');
define("DB_HOST", 'localhost');			// You probably won't need to change this

define("db_prefix", 'hotaru_');			// Database prefix, e.g. "hotaru_"
define("db_lang", 'en');			// Database language, e.g. "en"

/* ******Paths ******* */
define('baseurl', "http://www.mysite.com/");	//e.g. http://www.mysite.com/ or http://www.mysite.com/bookmarking/
						// IMPORTANT!!! Don't forget the trailing slash (/).

/* ************************************************************************************ */
/* *************************** DON'T EDIT BELOW THIS POINT **************************** */
/* ************************************************************************************ */

// define shorthand paths
define("admin", dirname(__FILE__).'/admin/');
define("install", dirname(__FILE__).'/install/');
define("includes", dirname(__FILE__).'/3rd_party/');
define("functions", dirname(__FILE__).'/functions/');
define("themes", dirname(__FILE__).'/content/themes/');
define("plugins", dirname(__FILE__).'/content/plugins/');
define("languages", dirname(__FILE__).'/content/language_packs/');
define("admin_themes", dirname(__FILE__).'/content/admin_themes/');

// define database tables
define("table_settings", db_prefix . "settings");
define("table_users", db_prefix . "users");
define("table_plugins", db_prefix . "plugins");
define("table_pluginhooks", db_prefix . "pluginhooks");
define("table_pluginsettings", db_prefix . "pluginsettings");

?>