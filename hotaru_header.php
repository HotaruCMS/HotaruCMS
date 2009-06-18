<?php

/* ******************************************************************** 
 *  File: /hotaru_header.php
 *  Purpose: Includes necessary files and sets globals.
 *  Notes: Included in all files.
 ********************************************************************** */
 
ini_set('display_errors',1);
error_reporting(E_ALL);

// include settings
require_once('hotaru_settings.php');

// include essential libraries
require_once(includes . 'Inspekt/Inspekt.php');			// for Input sanitation and validation
require_once(includes . 'ezSQL/ez_sql_core.php');		// for database usage
require_once(includes . 'ezSQL/mysql/ez_sql_mysql.php');	// for database usage
require_once(libraries . 'class.hotaru.php');
require_once(libraries . 'class.plugins.php');

if(file_exists(languages . 'main/main_' . strtolower(sitelanguage) . '.php')) {
	require_once(languages . 'main/main_' . strtolower(sitelanguage) . '.php');	// language file for main (not admin, installation or plugins)
} else {
	require_once(languages . 'main/main_english.php');	// English file if specified language doesn't exist
}

// Global Inspekt SuperCage
$cage = Inspekt::makeSuperCage();

// Initialize database
$db = new ezSQL_mysql(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
$hotaru = new Hotaru();
$plugin = new Plugin();

?>
