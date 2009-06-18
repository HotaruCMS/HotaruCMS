<?php

/* ******************************************************************** 
 *  File: /hotaru_settings.php
 *  Purpose: Configuration file for Hotaru CMS.
 *  Notes: ---
 ********************************************************************** */
 
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
define('baseurl', "http://localhost/hotarucms/");	//e.g. http://www.mysite.com/ or http://www.mysite.com/bookmarking/
							// IMPORTANT!!! Don't forget the trailing slash (/).

/* ****** Themes ***** */
define("current_theme", "default" . "/");	// change "default for the folder name of your theme.
define("current_admin_theme", "admin_default" . "/");	// change "default for the folder name of your admin theme.

/* ************************************************************************************ */
/* *************************** DON'T EDIT BELOW THIS POINT **************************** */
/* ************************************************************************************ */

// define shorthand paths
define("includes", dirname(__FILE__).'/includes/');
define("libraries", dirname(__FILE__).'/libraries/');
define("languages", dirname(__FILE__).'/languages/');
define("functions", dirname(__FILE__).'/functions/');
define("plugins", dirname(__FILE__).'/plugins/');
define("themes", dirname(__FILE__).'/themes/');
define("admin_themes", dirname(__FILE__).'/admin/themes/');

// define database tables
define("table_settings", db_prefix . "settings");
define("table_plugins", db_prefix . "plugins");
define("table_pluginmeta", db_prefix . "pluginmeta");

?>