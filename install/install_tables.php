<?php
/**
 * Install database tables for Hotaru CMS.
 * 
 * Steps through the set-up process, creating database tables and registering 
 * the Admin user. Note: You must delete this file after installation as it 
 * poses a serious security risk if left.
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

/**
 * Create database tables
 *
 * @param string $table_name
 *
 * Note: Deletes the table if it already exists, then makes it again
 */
function create_table($table_name)
{
    global $db, $lang;
    
    $sql = 'DROP TABLE IF EXISTS `' . DB_PREFIX . $table_name . '`;';
    $db->query($sql);

    // SETTINGS TABLE
    
    if ($table_name == "settings") {
        $sql = "CREATE TABLE `" . DB_PREFIX . $table_name . "` (
          `settings_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `settings_name` varchar(64) NOT NULL,
          `settings_value` text NOT NULL DEFAULT '',
          `settings_default` text NOT NULL DEFAULT '',
          `settings_note` text NOT NULL DEFAULT '',
          `settings_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          `settings_updateby` int(20) NOT NULL DEFAULT 0,
          UNIQUE KEY `key` (`settings_name`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Application Settings';";
        echo $lang['install_step3_creating_table'] . ": '" . $table_name . "'...<br />\n";
        $db->query($sql);
        
        // Default settings:
        
        // Friendly urls
        $sql = "INSERT INTO " . DB_PREFIX . $table_name . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
        $db->query($db->prepare($sql, 'SITE_OPEN', 'true', 'true', 'true/false'));
        
        // Site name
        $sql = "INSERT INTO " . DB_PREFIX . $table_name . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
        $db->query($db->prepare($sql, 'SITE_NAME', 'Hotaru CMS', 'Hotaru CMS', ''));
        
        // Main theme
        $sql = "INSERT INTO " . DB_PREFIX . $table_name . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
        $db->query($db->prepare($sql, 'THEME', 'default/', 'default/', 'You need the "\/"'));
        
        // Admin theme
        $sql = "INSERT INTO " . DB_PREFIX . $table_name . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
        $db->query($db->prepare($sql, 'ADMIN_THEME', 'admin_default/', 'admin_default/', 'You need the "\/"'));
        
        // Language_pack 
        /* Defined in hotaru_settings because we need it for this installation script, but here we check it has been defined, just in case.*/
        if (!isset($language_pack)) { $language_pack = 'default/'; }
        $sql = "INSERT INTO " . DB_PREFIX . $table_name . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
        $db->query($db->prepare($sql, 'LANGUAGE_PACK', $language_pack, 'language_default/', 'You need the "\/"'));
        
        // Friendly urls
        $sql = "INSERT INTO " . DB_PREFIX . $table_name . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
        $db->query($db->prepare($sql, 'FRIENDLY_URLS', 'false', 'false', 'true/false'));
        
        // Site email
        $sql = "INSERT INTO " . DB_PREFIX . $table_name . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
        $db->query($db->prepare($sql, 'SITE_EMAIL', 'admin@mysite.com', 'admin@mysite.com', 'Must be changed'));
        
        // Database cache
        $sql = "INSERT INTO " . DB_PREFIX . $table_name . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
        $db->query($db->prepare($sql, 'DB_CACHE_ON', 'false', 'false', 'true/false'));
        
        // Database cache duration (hours)
        $sql = "INSERT INTO " . DB_PREFIX . $table_name . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %d, %d, %s)";
        $db->query($db->prepare($sql, 'DB_CACHE_DURATION', 12, 12, 'Hours'));
        
        // RSS cache
        $sql = "INSERT INTO " . DB_PREFIX . $table_name . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
        $db->query($db->prepare($sql, 'RSS_CACHE_ON', 'true', 'true', 'true/false'));
        
        // RSS cache duration (hours)
        $sql = "INSERT INTO " . DB_PREFIX . $table_name . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %d, %d, %s)";
        $db->query($db->prepare($sql, 'RSS_CACHE_DURATION', 60, 60, 'Minutes'));
        
        // CSS/JavaScript cache
        $sql = "INSERT INTO " . DB_PREFIX . $table_name . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
        $db->query($db->prepare($sql, 'CSS_JS_CACHE_ON', 'true', 'true', 'true/false'));
        
        // Debug
        $sql = "INSERT INTO " . DB_PREFIX . $table_name . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
        $db->query($db->prepare($sql, 'DEBUG', 'false', 'false', 'true/false'));
    }
    
    
    // MISCDATA TABLE - for storing default permissions, etc.
    
    if ($table_name == "miscdata") {
        $sql = "CREATE TABLE `" . DB_PREFIX . $table_name . "` (
          `miscdata_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `miscdata_key` varchar(64) NOT NULL,
          `miscdata_value` text NOT NULL DEFAULT '',
          `miscdata_default` text NOT NULL DEFAULT '',
          `miscdata_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          `miscdata_updateby` int(20) NOT NULL DEFAULT 0
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Miscellaneous Data';";
        echo $lang['install_step3_creating_table'] . ": '" . $table_name . "'...<br />\n";
        $db->query($sql);

        // Default permissions
        $perms['options']['can_access_admin'] = array('yes', 'no');
        $perms['admin']['can_access_admin'] = 'yes';
        $perms['supermod']['can_access_admin'] = 'yes';
        $perms['default']['can_access_admin'] = 'no';
        $perms = serialize($perms);
        
        $sql = "INSERT INTO " . DB_PREFIX . $table_name . " (miscdata_key, miscdata_value, miscdata_default) VALUES (%s, %s, %s)";
        $db->query($db->prepare($sql, 'permissions', $perms, $perms));
    }
    
    // USERS TABLE
    
    if ($table_name == "users") {    
        $sql = "CREATE TABLE `" . DB_PREFIX . $table_name . "` (
          `user_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `user_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          `user_username` varchar(32) NOT NULL,
          `user_role` varchar(32) NOT NULL DEFAULT 'member',
          `user_date` timestamp NOT NULL,
          `user_password` varchar(64) NOT NULL DEFAULT '',
          `user_password_conf` varchar(128) NULL,
          `user_email` varchar(128) NOT NULL DEFAULT '',
          `user_email_valid` tinyint(3) NOT NULL DEFAULT 0,
          `user_email_conf` varchar(128) NULL,
          `user_permissions` text NOT NULL DEFAULT '',
          `user_ip` varchar(32) NOT NULL DEFAULT '0',
          `user_lastlogin` timestamp NULL,
          `user_updateby` int(20) NOT NULL DEFAULT 0,
          UNIQUE KEY `key` (`user_username`),
          KEY `user_email` (`user_email`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Users and Roles';";
        echo $lang['install_step3_creating_table'] . ": '" . $table_name . "'...<br />\n";
        $db->query($sql); 
    }
    
    // PLUGINS TABLE
    
    if ($table_name == "plugins") {
        $sql = "CREATE TABLE `" . DB_PREFIX . $table_name . "` (
          `plugin_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `plugin_enabled` tinyint(1) NOT NULL DEFAULT '0',
          `plugin_name` varchar(64) NOT NULL DEFAULT '',
          `plugin_folder` varchar(64) NOT NULL,
          `plugin_class` varchar(64) NOT NULL DEFAULT '',
          `plugin_desc` varchar(255) NOT NULL DEFAULT '',
          `plugin_requires` varchar(255) NOT NULL DEFAULT '',
          `plugin_version` varchar(32) NOT NULL DEFAULT '0.0',
          `plugin_order` int(11) NOT NULL DEFAULT 0,
          `plugin_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          `plugin_updateby` int(20) NOT NULL DEFAULT 0,
          UNIQUE KEY `key` (`plugin_folder`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Application Plugins';";
        echo $lang['install_step3_creating_table'] . ": '" . $table_name . "'...<br />\n";
        $db->query($sql);
    }
    
    // PLUGIN HOOKS TABLE
    
    if ($table_name == "pluginhooks") {
        $sql = "CREATE TABLE `" . DB_PREFIX . $table_name . "` (
          `phook_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `plugin_folder` varchar(64) NOT NULL DEFAULT '',
          `plugin_hook` varchar(128) NOT NULL DEFAULT '',
          `plugin_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          `plugin_updateby` int(20) NOT NULL DEFAULT 0,
          INDEX  (`plugin_folder`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Plugins Hooks';";
        echo $lang['install_step3_creating_table'] . ": '" . $table_name . "'...<br />\n";
        $db->query($sql);
    }
    
    // PLUGIN SETTINGS TABLE
    
    if ($table_name == "pluginsettings") {
        $sql = "CREATE TABLE `" . DB_PREFIX . $table_name . "` (
          `psetting_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `plugin_folder` varchar(64) NOT NULL,
          `plugin_setting` varchar(64) NULL,
          `plugin_value` text NULL,
          `plugin_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          `plugin_updateby` int(20) NOT NULL DEFAULT 0,
          INDEX  (`plugin_folder`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Plugins Settings';";
        echo $lang['install_step3_creating_table'] . ": '" . $table_name . "'...<br />\n";
        $db->query($sql);
    }
    
    
    // BLOCKED TABLE - blocked IPs, users, email types, etc...
    
    if ($table_name == "blocked") {
        $sql = "CREATE TABLE `" . DB_PREFIX . $table_name . "` (
          `blocked_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `blocked_type` varchar(64) NULL,
          `blocked_value` text NULL,
          `blocked_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          `blocked_updateby` int(20) NOT NULL DEFAULT 0,
          INDEX  (`blocked_type`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Blocked IPs, users, emails, etc';";
        echo $lang['install_step3_creating_table'] . ": '" . $table_name . "'...<br />\n";
        $db->query($sql);
    }
}
?>