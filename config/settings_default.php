<?php
/* Configuration file for Hotaru CMS */

// Paths
define('BASEURL', "http://example.com/");		// e.g. http://example.com/ (needs trailing slash)

// Database details
define("DB_USER", 'root');						// Add your own database details 
define("DB_PASSWORD", '');
define("DB_NAME", 'hotaru');
define("DB_HOST", 'localhost');					// You probably won't need to change this

// You probably don't need to change these
define("DB_PREFIX", 'hotaru_');					// Database prefix, e.g. "hotaru_"
define("DB_LANG", 'en');			 			// Database language, e.g. "en"
define("DB_ENGINE", 'MyISAM');					// Database Engine, e.g. "MyISAM"
define('DB_CHARSET', 'utf8');					// Database Character Set (UTF8 is Recommended), e.g. "utf8"
define("DB_COLLATE", 'utf8_unicode_ci');		// Database Collation (UTF8 is Recommended), e.g. "utf8_unicode_ci"
?>