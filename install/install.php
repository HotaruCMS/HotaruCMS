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
 * @copyright Copyright (c) 2010, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

// start session:
session_start();

// Remove any cookies set in a previous installation:
setcookie("hotaru_user", "", time()-3600, "/");
setcookie("hotaru_key", "", time()-3600, "/");
// --------------------------------------------------

require_once('../hotaru_settings.php');
require_once('install_tables.php');
require_once('install_functions.php');
require_once(BASE . 'Hotaru.php');
require_once(EXTENSIONS . 'csrf/csrf_class.php'); // protection against CSRF attacks
require_once(EXTENSIONS . 'Inspekt/Inspekt.php'); // sanitation
require_once(EXTENSIONS . 'ezSQL/ez_sql_core.php'); // database
require_once(EXTENSIONS . 'ezSQL/mysql/ez_sql_mysql.php'); // database
$h  = new Hotaru('install'); // must come before language inclusion
require_once(INSTALL . 'install_language.php');    // language file for install
    
$db = init_database();
$cage = init_inspekt_cage();

$step = $cage->get->getInt('step');        // Installation steps.

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
	$header .= "<link rel='stylesheet' type='text/css' href='" . BASEURL . "install/reset-fonts-grids.css' type='text/css'>\n";
	$header .= "<link rel='stylesheet' type='text/css' href='" . BASEURL . "install/install_style.css'>\n";
	$header .= "</HEAD>\n";
	
	// Body start
	$header .= "<BODY>\n";
	$header .= "<div id='doc' class='yui-t7 install'>\n";
	$header .= "<div id='hd' role='banner'>";
	$header .= "<img align='left' src='" . BASEURL . "content/admin_themes/admin_default/images/hotaru.png' style='height:60px; width:60px;'>";
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
	echo "<li>" . $lang['install_step2_instructions5'] . "</li>\n";
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
	global $lang;
	
	// delete existing cache
	delete_files(CACHE . 'db_cache');
	delete_files(CACHE . 'css_js_cache');
	delete_files(CACHE . 'rss_cache');
	
	echo html_header();
	
	// Step title
	echo "<h2>" . $lang['install_step3'] . "</h2>\n";
	
	// delete *all* plugin tables:
	$plugin_tables = list_plugin_tables();
	foreach ($plugin_tables as $pt) {
		drop_table($pt); // table name
	}
	
	//create tables
	$tables = array('blocked', 'categories', 'comments', 'commentvotes', 'friends', 'messaging', 'miscdata', 'plugins', 'pluginhooks', 'pluginsettings', 'posts', 'postmeta', 'postvotes', 'settings', 'tags', 'tempdata', 'tokens', 'users', 'usermeta', 'useractivity', 'widgets');
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
	global $lang;   //already included so Hotaru can't re-include it
	global $db;
	global $h;

	$h  = new Hotaru(); // overwrites current global with fully initialized Hotaru object
	
	echo html_header();
	
	// Step title
	echo "<h2>" . $lang['install_step4'] . "</h2>\n";
	
	// Step content
	echo "<div class='install_content'>" . $lang['install_step4_instructions'] . ":<br />\n";
	
	$error = 0;
	if ($h->cage->post->getInt('step') == 4) 
	{
		// Test CSRF
		if (!$h->csrf()) {
			$h->message = $lang['install_step4_csrf_error'];
			$h->messageType = 'red';
			$h->showMessage();
			$error = 1;
		}
		
		// Test username
		$name_check = $h->cage->post->testUsername('username');
		// alphanumeric, dashes and underscores okay, case insensitive
		if ($name_check) {
			$user_name = $name_check;
		} else {
			$h->message = $lang['install_step4_username_error'];
			$h->messageType = 'red';
			$h->showMessage();
			$error = 1;
		}

		// Test password
		$password_check = $h->cage->post->testPassword('password');    
		if ($password_check) {
			$password2_check = $h->cage->post->testPassword('password2');
			if ($password_check == $password2_check) {
				// success
				$user_password = $h->currentUser->generateHash($password_check);
			} else {
				$h->message = $lang['install_step4_password_match_error'];
				$h->messageType = 'red';
				$h->showMessage();
				$error = 1;
			}
		} else {
			$password_check = "";
			$password2_check = "";
			$h->message = $lang['install_step4_password_error'];
			$h->messageType = 'red';
			$h->showMessage();
			$error = 1;
		}

		// Test email
		$email_check = $h->cage->post->testEmail('email');
		if ($email_check) {
			$user_email = $email_check;
		} else {
			$h->message = $lang['install_step4_email_error'];
			$h->messageType = 'red';
			$h->showMessage();
			$error = 1;
		}
	}

	// Show success message
	if (($h->cage->post->getInt('step') == 4) && $error == 0) {
		$h->message = $lang['install_step4_update_success'];
		$h->messageType = 'green';
		$h->showMessage();
	}

	if ($error == 0) {
	
		$sql = "SELECT user_username FROM " . TABLE_USERS . " WHERE user_role = %s";
		
		if (!$admin_name = $h->db->get_var($h->db->prepare($sql, 'admin')))
		{
			// Insert default settings
			$sql = "INSERT INTO " . TABLE_USERS . " (user_username, user_role, user_date, user_password, user_email, user_permissions) VALUES (%s, %s, CURRENT_TIMESTAMP, %s, %s, %s)";
			$h->db->query($h->db->prepare($sql, 'admin', 'admin', 'password', 'admin@example.com', serialize($h->currentUser->getDefaultPermissions($h, 'admin'))));
			$user_name = 'admin';
			$user_email = 'admin@example.com';
			$user_password = 'password';
		} 
		else 
		{
			$user_info = $h->currentUser->getUserBasic($h, 0, $admin_name);
			// On returning to this page via back or next, the fields are empty at this point, so...
			if (!isset($user_name)) { $user_name = ""; }
			if (!isset($user_email)){ $user_email = ""; } 
			if (!isset($user_password)) { $user_password = ""; }
			if (($user_name != "") && ($user_email != "") && ($user_password != "")) {
				// There's been a change so update...
				$sql = "UPDATE " . TABLE_USERS . " SET user_username = %s, user_role = %s, user_date = CURRENT_TIMESTAMP, user_password = %s, user_email = %s, user_email_valid = %d WHERE user_role = %s";
				$h->db->query($h->db->prepare($sql, $user_name, 'admin', $user_password, $user_email, 1, 'admin'));
				$next_button = true;
			} else {
				$user_id = $user_info->user_id;
				$user_name = $user_info->user_username;
				$user_email = $user_info->user_email;
				$user_password = $user_info->user_password;
			}
		}
	}

	// Registration form
	echo "<form name='install_admin_reg_form' action='" . BASEURL . "install/install.php?step=4' method='post'>\n";
	
	echo "<table>";
	
	// Username
	echo "<tr><td>" . $lang["install_step4_username"] . "&nbsp; </td><td><input type='text' size=30 name='username' value='" . $user_name . "' /></td></tr>\n";
	
	// Email
	echo "<tr><td>" . $lang["install_step4_email"] . "&nbsp; </td><td><input type='text' size=30 name='email' value='" . $user_email . "' /></td></tr>\n";
	
	// Password
	echo "<tr><td>" . $lang["install_step4_password"] . "&nbsp; </td><td><input type='password' size=30 name='password' value='' /></td></tr>\n";
	
	// Password verify
	echo "<tr><td>" . $lang["install_step4_password_verify"] . "&nbsp; </td><td><input type='password' size=30 name='password2' value='' /></td></tr>\n";
	
	echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
	echo "<input type='hidden' name='step' value='4' />\n";
	echo "<input type='hidden' name='updated' value='true' />\n";
	
	// Update button
	echo "<tr><td>&nbsp;</td><td style='text-align:right;'><input id='update' type='submit' value='" . $lang['install_step4_form_update'] . "' /></td></tr>\n";
	
	echo "</table>";
	echo "</form>\n";

	// Make note of password message
	echo $lang["install_step4_make_note"] . "</div>\n";
	
	// Previous/Next buttons
	echo "<div class='back'><a href='install.php?step=3'>" . $lang['install_back'] . "</a></div>\n";
	if ($h->cage->post->getAlpha('updated') == 'true' && isset($next_button)) {
		// active "next" link if user has been updated
		echo "<div class='next'><a href='install.php?step=5'>" . $lang['install_next'] . "</a></div>\n";
	} else {
		// link disbaled until "update" button pressed
		echo "<div class='next'>" . $lang['install_next'] . "</div>\n";
	}
	
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
	echo "<div class='next'><a href='" . BASEURL . "'>" . $lang['install_home'] . "</a></div>\n";
	
	echo html_footer();
}

?>
