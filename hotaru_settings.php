<?php

/* ************************************************************************************ */
/* *************************** EDIT THE FOLLOWING ************************************* */
/* ************************************************************************************ */

/* ****** Names ****** */
define("sitename", 'Hotaru CMS');

/* ****** Themes ***** */
define("current_theme", "default" . "/");	// change "default for the folder name of your theme.

/* ****** Database Details ****** */
define("DB_USER", 'root');			// Add your own database details 
define("DB_PASSWORD", '');			// *** DATABASES NOT USED YET 6/15/2009 ***
define("DB_NAME", 'hotaru');
define("DB_HOST", 'localhost');


/* ************************************************************************************ */
/* *************************** DON'T EDIT BELOW THIS POINT **************************** */
/* ************************************************************************************ */

/* Work out the paths */
define('baseurl', "http://" . $_SERVER["HTTP_HOST"]);
$pos = strrpos($_SERVER["SCRIPT_NAME"], "/");
$path = substr($_SERVER["SCRIPT_NAME"], 0, $pos);
if ($path == "/"){$path = "";}
define('hotarupath', $path);

// define shorthand paths
define("base", dirname(__FILE__).'/');
define("includes", dirname(__FILE__).'/includes/');
define("libraries", dirname(__FILE__).'/libraries/');
define("functions", dirname(__FILE__).'/functions/');
define("images", dirname(__FILE__).'/images/');
define("plugins", dirname(__FILE__).'/plugins/');
define("themes", dirname(__FILE__).'/themes/');

// include essential libraries
require_once(includes . 'Inspekt/Inspekt.php');			// for Input sanitation and validation
require_once(includes . 'ezSQL/ez_sql_core.php');		// for database usage
require_once(includes . 'ezSQL/mysql/ez_sql_mysql.php');	// for database usage

// Initialize database
$db = new ezSQL_mysql(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
?>