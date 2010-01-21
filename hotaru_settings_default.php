<?php
/**
 * Configuration file for Hotaru CMS.
 *
 * PHP version 5
 *
 * LICENSE: Hotaru CMS is free software: you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License as 
 * published by the Free Software Foundation, either version 3 of 
 * the License, or (at your option) any later version. 
 *
 * Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE. 
 *
 * You should have received a copy of the GNU General Public License along 
 * with Hotaru CMS. If not, see http://www.gnu.org/licenses/.
 * 
 * @category  Content Management System
 * @package   HotaruCMS
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

// EDIT THE FOLLOWING ONLY

// Database details
define("DB_USER", 'root');          			// Add your own database details 
define("DB_PASSWORD", '');
define("DB_NAME", 'hotaru');
define("DB_HOST", 'localhost');     			// You probably won't need to change this

define("DB_PREFIX", 'hotaru_');     			// Database prefix, e.g. "hotaru_"
define("DB_LANG", 'en');            			// Database language, e.g. "en"
define("DB_ENGINE", 'MyISAM');					// Database Engine, e.g. "MyISAM"
define('DB_CHARSET', 'utf8');					// Database Character Set (UTF8 is Recommended), e.g. "utf8"
define("DB_COLLATE", 'utf8_unicode_ci');		// Database Collation (UTF8 is Recommended), e.g. "utf8_unicode_ci"

// Paths
define('BASEURL', "http://www.mysite.com/");    // e.g. http://www.mysite.com/

                                                // Needs trailing slash (/)

// DON'T EDIT ANYTHING BEYOND THIS POINT

// define shorthand paths
define("BASE", dirname(__FILE__). '/');
define("ADMIN", dirname(__FILE__).'/admin/');
define("CACHE", dirname(__FILE__). '/cache/');
define("INSTALL", dirname(__FILE__).'/install/');
define("LIBS", dirname(__FILE__).'/libs/');
define("EXTENSIONS", dirname(__FILE__).'/libs/extensions/');
define("FUNCTIONS", dirname(__FILE__).'/functions/');
define("THEMES", dirname(__FILE__).'/content/themes/');
define("PLUGINS", dirname(__FILE__).'/content/plugins/');
define("ADMIN_THEMES", dirname(__FILE__).'/content/admin_themes/');

// define database tables
define("TABLE_BLOCKED", DB_PREFIX . "blocked");
define("TABLE_CATEGORIES", DB_PREFIX . 'categories');
define("TABLE_COMMENTS", DB_PREFIX . 'comments');
define("TABLE_COMMENTVOTES", DB_PREFIX . 'commentvotes');
define("TABLE_MISCDATA", DB_PREFIX . "miscdata");
define("TABLE_PLUGINS", DB_PREFIX . "plugins");
define("TABLE_PLUGINHOOKS", DB_PREFIX . "pluginhooks");
define("TABLE_PLUGINSETTINGS", DB_PREFIX . "pluginsettings");
define("TABLE_POSTS", DB_PREFIX . 'posts');
define("TABLE_POSTMETA", DB_PREFIX . 'postmeta');
define("TABLE_POSTVOTES", DB_PREFIX . 'postvotes');
define("TABLE_SETTINGS", DB_PREFIX . "settings");
define("TABLE_TAGS", DB_PREFIX . 'tags');
define("TABLE_TEMPDATA", DB_PREFIX . 'tempdata');
define("TABLE_USERS", DB_PREFIX . "users");
define("TABLE_USERMETA", DB_PREFIX . "usermeta");
define("TABLE_USERACTIVITY", DB_PREFIX . "useractivity");
define("TABLE_WIDGETS", DB_PREFIX . 'widgets');

?>