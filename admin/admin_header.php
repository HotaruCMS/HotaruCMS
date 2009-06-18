<?php

/* ******************************************************************** 
 *  File: /admin/admin_header.php
 *  Purpose: Includes necessary files and sets globals.
 *  Notes: Included in all admin files.
 ********************************************************************** */
 
ini_set('display_errors',1);
error_reporting(E_ALL);

// include settings
require_once('../hotaru_settings.php');

// include essential libraries
require_once(includes . 'Inspekt/Inspekt.php');			// for Input sanitation and validation
require_once(includes . 'ezSQL/ez_sql_core.php');		// for database usage
require_once(includes . 'ezSQL/mysql/ez_sql_mysql.php');	// for database usage
if(file_exists(languages . 'admin/admin_' . strtolower(sitelanguage) . '.php')) {
	require_once(languages . 'admin/admin_' . strtolower(sitelanguage) . '.php');	// language file for admin
} else {
	require_once(languages . 'admin/admin_english.php');	// English file if specified language doesn't exist
}

// Global Inspekt SuperCage
$cage = Inspekt::makeSuperCage();

// Initialize database
$db = new ezSQL_mysql(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);

?>
