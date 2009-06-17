<?php

/* ******************************************************************** 
 *  File: /hotaru_settings.php
 *  Purpose: Configuration file for Hotaru CMS.
 *  Notes: ---
 ********************************************************************** */
 
/* ************************************************************************************ */
/* *************************** EDIT THE FOLLOWING ONLY ******************************** */
/* ************************************************************************************ */

/* ****** Names ****** */
define("sitename", 'Hotaru CMS');

/* ******Paths ******* */
define('baseurl', "http://localhost/hotarucms/");	//e.g. http://www.mysite.com/ or http://www.mysite.com/bookmarking/

/* ****** Themes ***** */
define("current_theme", "default" . "/");	// change "default for the folder name of your theme.
define("current_admin_theme", "admin_default" . "/");	// change "default for the folder name of your admin theme.

/* ****** Database Details ****** */
define("DB_USER", 'root');			// Add your own database details 
define("DB_PASSWORD", '');			
define("DB_NAME", 'hotaru');
define("DB_HOST", 'localhost');

define("db_prefix", 'hotaru_');			// Better leave this for now
define("db_lang", 'en');			// Better leave this for now


/* ************************************************************************************ */
/* *************************** DON'T EDIT BELOW THIS POINT **************************** */
/* ************************************************************************************ */

// define shorthand paths
//define("base", dirname(__FILE__).'/');
define("includes", dirname(__FILE__).'/includes/');
define("libraries", dirname(__FILE__).'/libraries/');
define("functions", dirname(__FILE__).'/functions/');
//define("images", dirname(__FILE__).'/images/');
define("plugins", dirname(__FILE__).'/plugins/');
define("themes", dirname(__FILE__).'/themes/');
//define("admin", dirname(__FILE__).'/admin/');
define("admin_themes", dirname(__FILE__).'/admin/themes/');

// define database tables
define("table_plugins", db_prefix . "plugins");

?>