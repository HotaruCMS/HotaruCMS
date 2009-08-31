<?php
/**
 * Install Hotaru CMS
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

// Remove any cookies set in a previous installation:
setcookie("hotaru_user", "", time()-3600, "/");
setcookie("hotaru_key", "", time()-3600, "/");
// --------------------------------------------------

require_once('../hotaru_settings.php');
require_once(classes . 'class.hotaru.php');    // Needed for error and success messages
require_once(classes . 'class.userbase.php');  // Needed for login/registration
require_once(classes . 'class.inspekt.php');      // for custom Inspekt methods
$hotaru = new Hotaru();

// Clear the database cache in case of a re-install.
require_once('../admin/class.admin.php'); 
$admin = new Admin();
$admin->delete_files(includes . 'ezSQL/cache');

// Global Inspekt SuperCage
require_once(includes . 'Inspekt/Inspekt.php');
$hotaru->initialize_inspekt();

require_once(install . 'install_language.php');    // language file for install

$step = $cage->get->getInt('step');        // Installation steps.

if ($step > 2) { 
    require_once(includes . 'ezSQL/ez_sql_core.php');
    require_once(includes . 'ezSQL/mysql/ez_sql_mysql.php');
    if (!isset($db)) { 
        $db = new ezSQL_mysql(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST); 
    } 
}

switch ($step) {
    case 1:
        installation_welcome();     // "Welcome to Hotaru CMS. 
        break;
    case 2:
        database_setup();           // DB name, user, password, prefix...
        break;
    case 3:
        database_creation();        // Creates the database tables
        break;
    case 4:
        register_admin();           // Username and password for Admin user...
        break;
    case 5:
        installation_complete();    // Delete "install" folder. Visit your site"
        break;
    default:
        // Anything other than step=2, 3 or 4 will return user to step 1
        installation_welcome();
        break;        
}

exit;


/**
 * HTML header
 *
 * @return string returns the html output for the page header
 */
function html_header()
{
    global $lang;
    
    $header = "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 3.2//EN'>\n";
    $header .= "<HTML><HEAD>\n";
    $header .= "<meta http-equiv=Content-Type content='text/html; charset=UTF-8'>\n";
    
    // Title
    $header .= "<TITLE>" . $lang['install_title'] . "</TITLE>\n";
    $header .= "<META HTTP-EQUIV='Content-Type' CONTENT='text'>\n";
    $header .= "<link rel='stylesheet' href='" . baseurl . "3rd_party/YUI-CSS/reset-fonts-grids.css' type='text/css'>\n";
    $header .= "<link rel='stylesheet' type='text/css' href='" . baseurl . "install/install_style.css'>\n";
    $header .= "</HEAD>\n";
    
    // Body start
    $header .= "<BODY>\n";
    $header .= "<div id='doc' class='yui-t7 install'>\n";
    $header .= "<div id='hd' role='banner'>";
    $header .= "<img align='left' src='" . baseurl . "content/admin_themes/admin_default/images/hotaru.png' style='height:60px; width:69px;'>";
    $header .= "<h1>" . $lang['install_title'] . "</h1></div>\n"; 
    $header .= "<div id='bd' role='main'>\n";
    $header .= "<div class='yui-g'>\n";
    
    return $header;
}


/**
 * HTML footer
 *
 * @return string returns the html output for the page footer
 */
function html_footer()
{
    global $lang;
    
    $footer = "<div class='clear'></div>\n"; // clear floats
    
    // Footer content (a link to the forums)
    $footer .= "<div id='ft' role='contentinfo'>";
    $footer .= "<p>" . $lang['install_trouble'] . "</p>";
    $footer .= "</div>\n"; // close "ft" div
    
    $footer .= "</div>\n"; // close "yui-g" div
    $footer .= "</div>\n"; // close "main" div
    $footer .= "</div>\n"; // close "yui-t7 install" div
    
    $footer .= "</BODY>\n";
    $footer .= "</HTML>\n";
    
    return $footer;
}


/**
 * Step 1 of installation - Welcome message
 */
function installation_welcome()
{
    global $lang;
    
    echo html_header();
    
    // Step title
    echo "<h2>" . $lang['install_step1'] . "</h2>\n";
    
    // Step content
    echo "<div class='install_content'>" . $lang['install_step1_welcome'] . "</div>\n";
    
    // Next button
    echo "<div class='next'><a href='install.php?step=2'>" . $lang['install_next'] . "</a></div>\n";
    
    echo html_footer();
}


/**
 * Step 2 of installation - asks to put database info in hotaru_settings.php 
 */
function database_setup()
{
    global $lang;
    
    echo html_header();
    
    // Step title
    echo "<h2>" . $lang['install_step2'] . "</h2>\n";
    
    // Step content
    echo "<div class='install_content'>" . $lang['install_step2_instructions'] . ":</div>\n";
    
    echo "<ol class='install_content'>\n";
    echo "<li>" . $lang['install_step2_instructions1'] . "</li>\n";
    echo "<li>" . $lang['install_step2_instructions2'] . "</li>\n";
    echo "<li>" . $lang['install_step2_instructions3'] . "</li>\n";
    echo "<li>" . $lang['install_step2_instructions4'] . "</li>\n";
    echo "</ol>\n";

    // Warning message
    echo "<div class='install_content'><span style='color: red;'>" . $lang['install_step2_warning'] . "</span>: " . $lang['install_step2_warning_note'] . "</div>\n";

    // Previous/Next buttons
    echo "<div class='back'><a href='install.php?step=1'>" . $lang['install_back'] . "</a></div>\n";
    echo "<div class='next'><a href='install.php?step=3'>" . $lang['install_next'] . "</a></div>\n";
    
    echo html_footer();
}


/**
 * Step 3 of installation - Creates database tables
 */
function database_creation()
{
    global $db, $lang;
    
    echo html_header();
    
    // Step title
    echo "<h2>" . $lang['install_step3'] . "</h2>\n";
    
    $skip = 0;
    $tables = array('settings', 'users', 'plugins', 'pluginhooks', 'pluginsettings');

    foreach ($tables as $table_name) {
        create_table($table_name);
    } 

    // Step content
    echo "<div class='install_content'>" . $lang['install_step3_success'] . "</div>\n";

    // Previous/Next buttons
    echo "<div class='back'><a href='install.php?step=2'>" . $lang['install_back'] . "</a></div>\n";
    echo "<div class='next'><a href='install.php?step=4'>" . $lang['install_next'] . "</a></div>\n";
    
    echo html_footer();
}


/**
 * Step 4 of installation - registers the site Admin.
 */
function register_admin()
{
    global $lang, $cage, $db, $userbase, $hotaru;
    
    $userbase = new Userbase();
    
    echo html_header();
    
    // Step title
    echo "<h2>" . $lang['install_step4'] . "</h2>\n";

    // Step content
    echo "<div class='install_content'>" . $lang['install_step4_instructions'] . ":<br />\n";
    
    $error = 0;
    if ($cage->post->getInt('step') == 4) 
    {
        // Test username
        $name_check = $cage->post->testUsername('username');
        // alphanumeric, dashes and underscores okay, case insensitive
        if ($name_check) {
            $user_name = $name_check;
        } else {
            $hotaru->message = $lang['install_step4_username_error'];
            $hotaru->message_type = 'red';
            $hotaru->show_message();
            $error = 1;
        }

        // Test password
        $password_check = $cage->post->testPassword('password');    
        if ($password_check) {
            $password2_check = $cage->post->testPassword('password2');
            if ($password_check == $password2_check) {
                // success
                $user_password = $userbase->generateHash($password_check);
            } else {
                $hotaru->message = $lang['install_step4_password_match_error'];
                $hotaru->message_type = 'red';
                $hotaru->show_message();
                $error = 1;
            }
        } else {
            $password_check = "";
            $password2_check = "";
            $hotaru->message = $lang['install_step4_password_error'];
            $hotaru->message_type = 'red';
            $hotaru->show_message();
            $error = 1;
        }

        // Test email
        $email_check = $cage->post->testEmail('email');
        if ($email_check) {
            $user_email = $email_check;
        } else {
            $hotaru->message = $lang['install_step4_email_error'];
            $hotaru->message_type = 'red';
            $hotaru->show_message();
            $error = 1;
        }
    }

    // Show success message
    if (($cage->post->getInt('step') == 4) && $error == 0) {
        $hotaru->message = $lang['install_step4_update_success'];
        $hotaru->message_type = 'green';
        $hotaru->show_message();
    }
    
    if ($error == 0) {
        if (!$admin_name = $userbase->admin_exists())
        {
            // Insert default settings
            $sql = "INSERT INTO " . table_users . " (user_username, user_role, user_date, user_password, user_email) VALUES (%s, %s, CURRENT_TIMESTAMP, %s, %s)";
            $db->query($db->prepare($sql, 'admin', 'admin', 'password', 'admin@mysite.com'));
            $user_name = 'admin';
            $user_email = 'admin@mysite.com';
            $user_password = 'password';
        } 
        else 
        {
            $user_info = $userbase->get_user_basic(0, $admin_name);
            // On returning to this page via back or next, the fields are empty at this point, so...
            if (!isset($user_name)) { $user_name = ""; }
            if (!isset($user_email)){ $user_email = ""; } 
            if (!isset($user_password)) { $user_password = ""; }
            if (($user_name != "") && ($user_email != "") && ($user_password != "")) {
                // There's been a change so update...
                $sql = "UPDATE " . table_users . " SET user_username = %s, user_role = %s, user_date = CURRENT_TIMESTAMP, user_password = %s, user_email = %s, user_email_valid = %d WHERE user_role = %s";
                $db->query($db->prepare($sql, $user_name, 'admin', $user_password, $user_email, 1, 'admin'));
            } else {
                $user_id = $user_info->user_id;
                $user_name = $user_info->user_username;
                $user_email = $user_info->user_email;
                $user_password = $user_info->user_password;
            }
        }
    }

    // Registration form
    echo "<form name='install_admin_reg_form' action='" . baseurl . "install/install.php?step=4' method='post'>\n";

    echo "<table>";

    // Username
    echo "<tr><td>" . $lang["install_step4_username"] . "&nbsp; </td><td><input type='text' size=30 name='username' value='" . $user_name . "' /></td></tr>\n";

    // Email
    echo "<tr><td>" . $lang["install_step4_email"] . "&nbsp; </td><td><input type='text' size=30 name='email' value='" . $user_email . "' /></td></tr>\n";
    
    // Password
    echo "<tr><td>" . $lang["install_step4_password"] . "&nbsp; </td><td><input type='password' size=30 name='password' value='' /></td></tr>\n";

    // Password verify
    echo "<tr><td>" . $lang["install_step4_password_verify"] . "&nbsp; </td><td><input type='password' size=30 name='password2' value='' /></td></tr>\n";

    echo "<input type='hidden' name='step' value='4' />\n";
    
    // Update button
    echo "<tr><td>&nbsp;</td><td style='text-align:right;'><input type='submit' value='" . $lang['install_step4_form_update'] . "' /></td></tr>\n";
    
    echo "</table>";
    echo "</form>\n";

    // Make note of password message
    echo $lang["install_step4_make_note"] . "</div>\n";

    // Previous/Next buttons
    echo "<div class='back'><a href='install.php?step=3'>" . $lang['install_back'] . "</a></div>\n";
    echo "<div class='next'><a href='install.php?step=5'>" . $lang['install_next'] . "</a></div>\n";
    
    echo html_footer();
}
    
    
/**
 * Step 5 of installation - shows completion.
 */
function installation_complete()
{
    global $lang;
    
    echo html_header();

    // Step title
    echo "<h2>" . $lang['install_step5'] . "</h2>\n";
    
    // Step content
    echo "<div class='install_content'>" . $lang['install_step5_installation_complete'] . "</div>\n";
    echo "<div class='install_content'>" . $lang['install_step5_installation_delete'] . "</div>\n";
    echo "<div class='install_content'>" . $lang['install_step5_installation_go_play'] . "</div>\n";

    // Previous/Next buttons
    echo "<div class='back'><a href='install.php?step=4'>" . $lang['install_back'] . "</a></div>\n";
    echo "<div class='next'><a href='" . baseurl . "'>" . $lang['install_home'] . "</a></div>\n";
    
    echo html_footer();    
}


    
    
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

    $sql = 'DROP TABLE IF EXISTS `' . db_prefix . $table_name . '`;';
    $db->query($sql);

    // SETTINGS TABLE
    
    if ($table_name == "settings") {
        $sql = "CREATE TABLE `" . db_prefix . $table_name . "` (
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
        
        // Site name
        $sql = "INSERT INTO " . db_prefix . $table_name . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
        $db->query($db->prepare($sql, 'site_name', 'Hotaru CMS', 'Hotaru CMS', ''));
        
        // Main theme
        $sql = "INSERT INTO " . db_prefix . $table_name . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
        $db->query($db->prepare($sql, 'theme', 'default/', 'default/', 'You need the "\/"'));
        
        // Admin theme
        $sql = "INSERT INTO " . db_prefix . $table_name . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
        $db->query($db->prepare($sql, 'admin_theme', 'admin_default/', 'admin_default/', 'You need the "\/"'));
        
        // Language_pack 
        /* Defined in hotaru_settings because we need it for this installation script, but here we check it has been defined, just in case.*/
        if (!isset($language_pack)) { $language_pack = 'default/'; }
        $sql = "INSERT INTO " . db_prefix . $table_name . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
        $db->query($db->prepare($sql, 'language_pack', $language_pack, 'language_default/', 'You need the "\/"'));
        
        // Friendly urls
        $sql = "INSERT INTO " . db_prefix . $table_name . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
        $db->query($db->prepare($sql, 'friendly_urls', 'false', 'false', ''));
        
        // Site email
        $sql = "INSERT INTO " . db_prefix . $table_name . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
        $db->query($db->prepare($sql, 'site_email', 'admin@hotarucms.org', 'admin@hotarucms.org', 'Must be changed'));
        
        // Debug
        $sql = "INSERT INTO " . db_prefix . $table_name . " (settings_name, settings_value, settings_default, settings_note) VALUES (%s, %s, %s, %s)";
        $db->query($db->prepare($sql, 'debug', 'false', 'false', ''));
    }
    
    // USERS TABLE
    
    if ($table_name == "users") {    
        $sql = "CREATE TABLE `" . db_prefix . $table_name . "` (
          `user_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `user_username` varchar(32) NOT NULL,
          `user_role` varchar(32) NOT NULL DEFAULT 'member',
          `user_date` timestamp NULL,
          `user_password` varchar(64) NOT NULL DEFAULT '',
          `user_email` varchar(128) NOT NULL DEFAULT '',
          `user_email_valid` tinyint(3) NOT NULL DEFAULT 0,
          `user_email_conf` varchar(128) NULL,
          `user_lastlogin` timestamp NULL,
          `user_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          `user_updateby` int(20) NOT NULL DEFAULT 0,
          UNIQUE KEY `key` (`user_username`),
          KEY `user_email` (`user_email`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Users and Roles';";
        echo $lang['install_step3_creating_table'] . ": '" . $table_name . "'...<br />\n";
        $db->query($sql); 
    }
    
    // PLUGINS TABLE
    
    if ($table_name == "plugins") {
        $sql = "CREATE TABLE `" . db_prefix . $table_name . "` (
          `plugin_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `plugin_enabled` tinyint(1) NOT NULL DEFAULT '0',
          `plugin_name` varchar(64) NOT NULL DEFAULT '',
          `plugin_prefix` varchar(16) NOT NULL DEFAULT '',
          `plugin_folder` varchar(64) NOT NULL,
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
        $sql = "CREATE TABLE `" . db_prefix . $table_name . "` (
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
        $sql = "CREATE TABLE `" . db_prefix . $table_name . "` (
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
}
?>