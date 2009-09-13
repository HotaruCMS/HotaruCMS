<?php
/**
 * Includes necessary files and sets globals.
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

// error reporting
ini_set('display_errors',1);
ini_set('log_errors',1);
error_reporting(E_ALL);

// include settings
require_once('hotaru_settings.php');

// include other essential libraries and functions

// for Input sanitation and validation
require_once(EXTENSIONS . 'Inspekt/Inspekt.php');

// for database usage
require_once(EXTENSIONS . 'ezSQL/ez_sql_core.php');
require_once(EXTENSIONS . 'ezSQL/mysql/ez_sql_mysql.php');

// for default or friendly urls
require_once(FUNCTIONS . 'funcs.urls.php');

// for manipulating strings
require_once(FUNCTIONS . 'funcs.strings.php');

// for manipulating arrays
require_once(FUNCTIONS . 'funcs.arrays.php');

// for everything related to time
require_once(FUNCTIONS . 'funcs.times.php');

// for everything related to files
require_once(FUNCTIONS . 'funcs.files.php');

// include libraries
require_once(LIBS . 'Hotaru.php');          // for environment
require_once(LIBS . 'HotaruInspekt.php');   // for custom Inspekt methods

// automatically load other classes as needed...
function __autoload($class_name) {
        if(file_exists(LIBS . $class_name . '.php')) {
            require LIBS . $class_name . '.php';
        }
}

// Initialize database
if (!isset($db)) { 
    $db = new ezSQL_mysql(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST); 
    $db->query("SET NAMES 'utf8'");
}

// Initialize Hotaru
if (!isset($hotaru)) { $hotaru = new Hotaru(); }
$settings = $hotaru->readSettings();
foreach ($settings as $setting)
{
    if (!defined($setting->settings_name)) { 
        define($setting->settings_name, $setting->settings_value);
    }
}

// Setup database cache
$db->cache_timeout = DB_CACHE_DURATION; // Note: this is hours
$db->cache_dir = CACHE . 'db_cache';
if (DB_CACHE_ON == "true") {
    $db->use_disk_cache = true;
} else {
    $db->use_disk_cache = false;
}   
// Note: Queries are still only cached following $db->cache_queries = true;

// Start timer if debugging
if (DEBUG == "true") {
    $hotaru->is_debug = true;
    timer_start();
}

// Initialize Inspekt
$hotaru->initializeInspekt();

// Create objects
if (!isset($plugins)) { 
    $plugins = new PluginFunctions(); 
} else {
    if (!is_object($plugins)) {
        $plugins = new PluginFunctions(); 
    }
}


$current_user = new UserBase();

// Check for a cookie. If present then the user is logged in.
$hotaru_user = $cage->cookie->testUsername('hotaru_user');
if (($hotaru_user) && ($cage->cookie->keyExists('hotaru_key'))) {

    $user_info=explode(":", base64_decode($cage->cookie->getRaw('hotaru_key')));
    
    if (    ($hotaru_user == $user_info[0]) 
        &&  (crypt($user_info[0], 22) == $user_info[1])
    ) {
        $current_user->userName = $hotaru_user;
        $current_user->getUserBasic(0, $current_user->userName);
        $current_user->loggedIn = true;
    }
}

// Enable plugins to define global settings, etc. 
$results = $plugins->pluginHook('hotaru_header');

/*  The following extracts the results of pluginHook which is 
    handy for making objects from plugins global */
if (isset($results) && is_array($results)) 
{
    foreach ($results as $key => $value) {
        if (is_array($value)) { extract($value); }
    } 
}

?>
