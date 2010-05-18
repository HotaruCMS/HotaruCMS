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


include_once('../hotaru_settings.php');
require_once('install_tables.php');
require_once('install_functions.php');
require_once(BASE . 'Hotaru.php');
require_once(EXTENSIONS . 'csrf/csrf_class.php'); // protection against CSRF attacks
require_once(EXTENSIONS . 'Inspekt/Inspekt.php'); // sanitation
require_once(EXTENSIONS . 'ezSQL/ez_sql_core.php'); // database
require_once(EXTENSIONS . 'ezSQL/mysql/ez_sql_mysql.php'); // database
//$h  = new Hotaru('install'); // must come before language inclusion
$h  = new Hotaru;
require_once(INSTALL . 'install_language.php');    // language file for install

$version_number = "1.2";
$db = init_database();
$cage = init_inspekt_cage();

$step = $cage->get->getInt('step');        // Installation steps.
$action = $cage->get->getAlpha('action');    // Install or Upgrade.

switch ($step) {
	case 0:
		installation_welcome();     // "Welcome to Hotaru CMS.
		break;
	case 1:
		if ($action == 'upgrade')
		    database_upgrade();
		else
		    if ($cage->get->getAlpha('type') == 'manual') database_setup_manual(); else database_setup();           // DB name, user, password, prefix...
		break;
	case 2:
		if ($action == 'upgrade')
		    database_upgrade();
		else
		    database_creation();        // Creates the database tables
		break;
	case 3:
		register_admin();           // Username and password for Admin user...
		break;
	case 4:
		installation_complete();    // Delete "install" folder. Visit your site"
		break;
	default:
		// Anything other than step=1, 3 or 4 will return user to Step 0
		installation_welcome();
	
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
	global $version_number;

	$header = "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 3.2//EN'>\n";
	$header .= "<HTML><HEAD>\n";
	$header .= "<meta http-equiv=Content-Type content='text/html; charset=UTF-8'>\n";

	// Title
	$header .= "<TITLE>" . $lang['install_title'] . "</TITLE>\n";
	$header .= "<META HTTP-EQUIV='Content-Type' CONTENT='text'>\n";
	$header .= "<link rel='stylesheet' type='text/css' href='reset-fonts-grids.css' type='text/css'>\n";
	$header .= "<link rel='stylesheet' type='text/css' href='install_style.css'>\n";
	$header .= "</HEAD>\n";

	// Body start
	$header .= "<BODY>\n";
	$header .= "<div id='doc' class='yui-t7 install'>\n";
	$header .= "<div id='hd' role='banner'>";
	$header .= "<img align='left' src='../content/admin_themes/admin_default/images/hotaru.png' style='height:60px; width:60px;'>";
	$header .= "<h1>" . $lang['install_title'] . " v." . $version_number ."</h1></div>\n";
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
 * Step 0 of installation - Welcome message
 */
function installation_welcome()
{
	global $lang;

	echo html_header();

	// Step title
	echo "<h2>" . $lang['install_step0'] . "</h2>\n";

	// Step content
	echo "<div class='install_content'>" . $lang['install_step0_welcome'] . "</div>\n";
	echo "<div class='install_content'>" . $lang['install_step0_select'] . "</div>\n";

	// Splash image
	echo "<img align='center' src='hotarucms_splash.jpg' style='height:170px; margin:50px 0;'><br/>";

	// Step content
	echo "<div class='center clearfix'>";
	echo "<a class='select button floatright' href='index.php?step=1&action=install'>" . $lang['install_new'] . "</a>";
	echo "<a class='select button floatright' href='index.php?step=1&action=upgrade'>" . $lang['install_upgrade'] . "</a>";
	echo "</div>";

	// Next button
	//echo "<div class='next button''><a href='index.php?step=1'>" . $lang['install_next'] . "</a></div>\n";

	echo html_footer();
}


/**Step 1 of installation
 *
 */
function database_setup() {
	global $lang;   //already included so Hotaru can't re-include it
	//global $db;
	global $h;

	//$h  = new Hotaru(); // overwrites current global with fully initialized Hotaru object

	$settings_file_exists="";

	if ($h->cage->post->getAlpha('updated') == 'true') {

	    $error = 0;
	    // Test CSRF
	    if (!$h->csrf()) {
		    $h->messages[$lang['install_step3_csrf_error']] = 'red';
		    $error = 1;
	    }

	    // Test baseurl
	    $baseurl_name = $h->cage->post->testUri('baseurl');
	    if (!$baseurl_name) {
		    $h->messages[$lang['install_step1_baseurl_error']] = 'red';
		    $error = 1;
	    }

	    // Test dbname
	    $dbuser_name = $h->cage->post->testAlnumLines('dbuser');
	    if (!$dbuser_name) {
		    $h->messages[$lang['install_step1_dbuser_error']] = 'red';
		    $error = 1;
	    }

	    // Test dbpassword
	    $dbpassword_name = $h->cage->post->KeyExists('dbpassword');
	    if (!$dbpassword_name) {
		    $h->messages[$lang['install_step1_dbpassword_error']] = 'red';
		    $error = 1;
	    }

	    // Test dbname
	    $dbname_name = $h->cage->post->testAlnumLines('dbname');
	    if (!$dbname_name) {
		    $h->messages[$lang['install_step1_dbname_error']] = 'red';
		    $error = 1;
	    }

	    // Test dbprefix
	    $dbprefix_name = $h->cage->post->testAlnumLines('dbprefix');
	    if (!$dbprefix_name) {
		    $h->messages[$lang['install_step1_dbprefix_error']] = 'red';
		    $error = 1;
	    }

	    // Test dbhost
	    $dbhost_name = $h->cage->post->testAlpha('dbhost');
	    if (!$dbhost_name) {
		    $h->messages[$lang['install_step1_dbhost_error']] = 'red';
		    $error = 1;
	    }

	    if ($error) $settings_file_exists =  getcwd() . "/../hotaru_settings.php";

	} else {
	    $dbuser_name = 'admin';
	    $dbname_name = 'hotaru';
	    $dbprefix_name = 'hotaru_';
	    $dbhost_name = 'localhost';
	    $baseurl_name = "http://" . $h->cage->server->sanitizeTags('HTTP_HOST') . "/";

	    // Check whether file already exists
	    $settings_file_exists =  getcwd() . "/../hotaru_settings.php";
	}

	// Show messages
	if ($h->cage->post->getAlpha('updated') == 'true') {
		if (!$error) {
		    // Try to write file to disk based on form inputs
		    $fputs = create_new_settings_file($dbuser_name, $dbpassword_name, $dbname_name, $dbprefix_name, $dbhost_name, $baseurl_name);
		    // if file written successfully then
		    if ($fputs) {
			$h->messages[$lang['install_step1_update_file_writing_success']] = 'green';
			// Check whether database and tables exist on this server
			$sql = "SELECT miscdata_value FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s";
			$old_version = $h->db->get_var($h->db->prepare($sql, "hotaru_version"));
			if ($old_version) $database_exists = true;
			// if yes set warning message var
		    } else {
			$h->messages[$lang['install_step1_update_file_writing_failure']] = 'red';
		    }
		}

	}



	// Try to write the /hotaru_settings.php file to disk
	//
	$settings_file_writeable =  is_writeable(getcwd() . "/../hotaru_settings.php");

	if ($settings_file_writeable) {
	    global $lang;

	    echo html_header();

	    $h->showMessages();

	    // Step title
	    echo "<h2>" . $lang['install_step1'] . "</h2>\n";

	    // Splash image
	    echo "<img align='center' src='../content/admin_themes/admin_default/images/create_db.png' style='float:left;'>";

	    // Step content
	    echo "<div class='install_content clearfix'>" . $lang['install_step1_instructions_create_db'] . "</div>\n";

	    //Manual creation link
	    echo "<div class='install_content clearfix' style='margin-left:54px;'>" . $lang['install_step1_instructions_manual_setup'] . "&nbsp;<a href='?step=1&action=install&type=manual'>" . $lang['install_step1_instructions_manual_setup_click'] . "</a>.";

	    // Registration form
	    echo "<form name='install_admin_reg_form' action='../install/index.php?step=1' method='post'>\n";

	    echo "<br/><table>";

	    // BASEURL
	    echo "<tr><td>" . $lang["install_step1_baseurl"] . "&nbsp; </td><td><input type='text' size=30 name='baseurl' value='" . $baseurl_name . "' />&nbsp;<small>" . $lang["install_step1_baseurl_explain"] . "</small></td></tr>\n";

	    // DB_USER
	    echo "<tr><td>" . $lang["install_step1_dbuser"] . "&nbsp; </td><td><input type='text' size=30 name='dbuser' value='" . $dbuser_name . "' />&nbsp;<small>" . $lang["install_step1_dbuser_explain"] . "</small></td></tr>\n";

	    // DB_PASSWORD
	    echo "<tr><td>" . $lang["install_step1_dbpassword"] . "&nbsp; </td><td><input type='password' size=30 name='dbpassword' value='' />&nbsp;<small>" . $lang["install_step1_dbpassword_explain"] . "</small></td></tr>\n";

	    // DB_NAME
	    echo "<tr><td>" . $lang["install_step1_dbname"] . "&nbsp; </td><td><input type='text' size=30 name='dbname' value='" . $dbname_name . "' />&nbsp;<small>" . $lang["install_step1_dbname_explain"] . "</small></td></tr>\n";

	    // DB_PREFIX
	    echo "<tr><td>" . $lang["install_step1_dbprefix"] . "&nbsp; </td><td><input type='text' size=30 name='dbprefix' value='" . $dbprefix_name . "' />&nbsp;<small>" . $lang["install_step1_dbprefix_explain"] . "</small></td></tr>\n";

	    // DB_HOST
	    echo "<tr><td>" . $lang["install_step1_dbhost"] . "&nbsp; </td><td><input type='text' size=30 name='dbhost' value='" . $dbhost_name . "' />&nbsp;<small>" . $lang["install_step1_dbhost_explain"] . "</small></td></tr>\n";


	    echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
	    echo "<input type='hidden' name='step' value='2' />\n";
	    echo "<input type='hidden' name='updated' value='true' />\n";

	    // Update button
	    echo "<tr><td>&nbsp;</td><td style='text-align:right;'><input class='update button' class='button' type='submit' value='" . $lang['install_step3_form_update'] . "' /></td></tr>\n";

	    echo "</table>";
	    echo "</form>\n";

	    if ($settings_file_exists) {
		// Alert if Settings file already exists
		echo "<br/><img align='center' src='../content/admin_themes/admin_default/images/delete.png' style='float:left;'>";
		echo $lang["install_step1_settings_file_already_exists"] . "</div><br/>";
	    }

	    if (isset($database_exists)) {
		// Alert if database already exists
		echo "<br/><img align='center' src='../content/admin_themes/admin_default/images/delete.png' style='float:left;'>";
		echo $lang["install_step1_settings_db_already_exists"] . "</div><br/>";
	    }

	    // Previous/Next buttons
	    echo "<div class='back button''><a href='index.php?step=0'>" . $lang['install_back'] . "</a></div>\n";
	    if ($h->cage->post->getAlpha('updated') == 'true' && isset($fputs)) {
		    // active "next" link if settings file has been created
		    echo "<div class='next button''><a href='index.php?step=2'>" . $lang['install_next'] . "</a></div>\n";
	    } else {
		    // link disbaled until "update" button pressed
		    echo "<div class='next button''>" . $lang['install_next'] . "</div>\n";
	    }

	    echo html_footer();

	} else {
	    database_setup_manual();
	}

}


/**
 * Step 1a of installation - asks to put database info in hotaru_settings.php
 */
function database_setup_manual()
{
	global $lang;

	echo html_header();

	// Step title
	echo "<h2>" . $lang['install_step1'] . "</h2>\n";

	// Step content
	echo "<div class='install_content'>" . $lang['install_step1_instructions'] . ":</div>\n";

	echo "<ol class='install_content'>\n";
	echo "<li>" . $lang['install_step1_instructions1'] . "</li>\n";
	echo "<li>" . $lang['install_step1_instructions2'] . "</li>\n";
	echo "<li>" . $lang['install_step1_instructions3'] . "</li>\n";
	echo "<li>" . $lang['install_step1_instructions4'] . "</li>\n";
	echo "<li>" . $lang['install_step1_instructions5'] . "</li>\n";
	echo "</ol>\n";

	// Warning message
	echo "<br/><img align='center' src='../content/admin_themes/admin_default/images/delete.png' style='float:left;'>";
	echo "<div class='install_content'><span style='color: red;'>" . $lang['install_step1_warning'] . "</span>: " . $lang['install_step1_warning_note'] . "</div><br/>\n";

	// Previous/Next buttons
	echo "<div class='back button''><a href='index.php?step=0'>" . $lang['install_back'] . "</a></div>\n";
	echo "<div class='next button''><a href='index.php?step=2'>" . $lang['install_next'] . "</a></div>\n";

	echo html_footer();
}

/**
 * Step 1 of Upgrade - update database tables
 */
function database_upgrade()
{
    include_once('install-upgrade.php');
}

/**
 * Step 2 of installation - Creates database tables
 */
function database_creation()
{
	global $lang;
	global $db;
	global $cage;
	
	$sql = "SELECT miscdata_value FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s";		
	$old_version = $db->get_var($db->prepare($sql, "hotaru_version"));

	$delete = $cage->get->getAlpha('del');        // Confirm delete.
	$show_next = false;

	echo html_header();
	
	// Step title
	echo "<h2>" . $lang['install_step2'] . "</h2>\n";
	
	if ($old_version && $delete != 'DELETE') {
		// Warning message
		echo "<br/><img align='center' src='../content/admin_themes/admin_default/images/delete.png' style='float:left;'>";
		echo "<div class='install_content'><span style='color: red;'>" . $lang['install_step1_warning'] . "</span>: " . $lang['install_step2_existing_db'] . "</div>\n";
		echo "<div class='install_content'>" . $lang['install_step2_existing_confirm'] . "</div>\n";

		// Confirm delete and continue install
		 echo "<form name='install_admin_reg_form' action='index.php?step=2' method='get'>\n";
		echo "<div class='center clearfix'>&nbsp; <input type='text' size=10 name='del' value='' />";
		echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
		echo "<input type='hidden' name='step' value='2' />\n";
	    	    
		echo "<input class='update button' class='button' type='submit' value='" . $lang['install_step2_form_delete_confirm'] . "' />";
		echo "</div></form>\n";

		echo "<div class='install_content'>" . $lang['install_step2_existing_go_upgrade1'];
		echo "<a href='?step=1&action=upgrade'>" . $lang['install_step2_existing_go_upgrade2'] . "</a></div>\n";
	}
	else {	   
	    // delete existing cache
	    delete_files(CACHE . 'db_cache');
	    delete_files(CACHE . 'css_js_cache');
	    delete_files(CACHE . 'rss_cache');

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
	    echo "<div class='install_content'>" . $lang['install_step2_success'] . "</div>\n";

	    $show_next = true;
	}	

	// Previous/Next buttons
	echo "<div class='back button''><a href='index.php?step=1'>" . $lang['install_back'] . "</a></div>\n";
	if ($show_next) {
		// active "next" link
		echo "<div class='next button''><a href='index.php?step=3'>" . $lang['install_next'] . "</a></div>\n";
	} else {
		// link disbaled
		echo "<div class='next button''>" . $lang['install_next'] . "</div>\n";
	}

	echo html_footer();
}


/**
 * Step 3 of installation - registers the site Admin.
 */
function register_admin()
{
	global $lang;   //already included so Hotaru can't re-include it
	global $db;
	global $h;

	$h  = new Hotaru(); // overwrites current global with fully initialized Hotaru object

	echo html_header();

	// Step title
	echo "<h2>" . $lang['install_step3'] . "</h2>\n";

	// Step content
	echo "<div class='install_content'>" . $lang['install_step3_instructions'] . ":<br />\n";

	$error = 0;
	if ($h->cage->post->getInt('step') == 4)
	{
		// Test CSRF
		if (!$h->csrf()) {
			$h->message = $lang['install_step3_csrf_error'];
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
			$h->message = $lang['install_step3_username_error'];
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
				$h->message = $lang['install_step3_password_match_error'];
				$h->messageType = 'red';
				$h->showMessage();
				$error = 1;
			}
		} else {
			$password_check = "";
			$password2_check = "";
			$h->message = $lang['install_step3_password_error'];
			$h->messageType = 'red';
			$h->showMessage();
			$error = 1;
		}

		// Test email
		$email_check = $h->cage->post->testEmail('email');
		if ($email_check) {
			$user_email = $email_check;
		} else {
			$h->message = $lang['install_step3_email_error'];
			$h->messageType = 'red';
			$h->showMessage();
			$error = 1;
		}
	}

	// Show success message
	if (($h->cage->post->getInt('step') == 4) && $error == 0) {
		$h->message = $lang['install_step3_update_success'];
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
	echo "<form name='install_admin_reg_form' action='index.php?step=3' method='post'>\n";

	echo "<table>";

	// Username
	echo "<tr><td>" . $lang["install_step3_username"] . "&nbsp; </td><td><input type='text' size=30 name='username' value='" . $user_name . "' /></td></tr>\n";

	// Email
	echo "<tr><td>" . $lang["install_step3_email"] . "&nbsp; </td><td><input type='text' size=30 name='email' value='" . $user_email . "' /></td></tr>\n";

	// Password
	echo "<tr><td>" . $lang["install_step3_password"] . "&nbsp; </td><td><input type='password' size=30 name='password' value='' /></td></tr>\n";

	// Password verify
	echo "<tr><td>" . $lang["install_step3_password_verify"] . "&nbsp; </td><td><input type='password' size=30 name='password2' value='' /></td></tr>\n";

	echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
	echo "<input type='hidden' name='step' value='4' />\n";
	echo "<input type='hidden' name='updated' value='true' />\n";

	// Update button
	echo "<tr><td>&nbsp;</td><td style='text-align:right;'><input class='update button' class='button' type='submit' value='" . $lang['install_step3_form_update'] . "' /></td></tr>\n";

	echo "</table>";
	echo "</form>\n";

	// Make note of password message
	echo $lang["install_step3_make_note"] . "</div>\n";

	// Previous/Next buttons
	echo "<div class='back button''><a href='index.php?step=2'>" . $lang['install_back'] . "</a></div>\n";
	if ($h->cage->post->getAlpha('updated') == 'true' && isset($next_button)) {
		// active "next" link if user has been updated
		echo "<div class='next button''><a href='index.php?step=4'>" . $lang['install_next'] . "</a></div>\n";
	} else {
		// link disbaled until "update" button pressed
		echo "<div class='next button''>" . $lang['install_next'] . "</div>\n";
	}

	echo html_footer();
}


/**
 * Step 4 of installation - shows completion.
 */
function installation_complete()
{
	global $lang;
	global $cage;
	global $h;

	$h  = new Hotaru(); // overwrites current global with fully initialized Hotaru object
	
	$phpinfo = $cage->post->getAlpha('phpinfo');        // delete install folder.
	$delete = $cage->post->getAlpha('delete');        // delete install folder.
	
	$folder_deleted = 0;

	if ($delete) {
	    // try to delete the folder
	    //$folder_deleted = delTree('install');
	    $folder_deleted = 2;
	    // if was deleted then redirect to baseurl
	    if ($folder_deleted == 1) header("Location: /index.php" );
	}

	echo html_header();

	// Step title
	echo "<h2>" . $lang['install_step4'] . "</h2>\n";

	// Step content
	
	if ($folder_deleted == 0) echo "<div class='install_content'>" . $lang['install_step4_installation_complete'] . "</div>\n";
	echo "<div class='install_content'>" . $lang['install_step4_installation_delete'] . "</div>\n";


	if ($phpinfo) {
	    echo '<br/>';
	    $php_version = phpversion();
	    $modules = get_loaded_extensions();
	    $php_module_not_found = false;

	    $required = array('bcmath' => 'http://php.net/manual/en/book.bc.php',
			      'mysql'=>'http://php.net/manual/en/book.mysql.php',
			      'filter'=>'http://php.net/manual/en/book.filter.php');

	    foreach ($required as $module => $url) {
		if (!in_array($module, $modules)) {
		    echo $h->showMessage($lang['install_step4_form_check_php_warning'] . '<a href="' . $url . '" target="_blank">' . $module . '</a><br/>','yellow');
		    $php_module_not_found = true;
		}
	    }
	    // check for correct version number of php
	    if (version_compare($php_version, '5.2.5', '<')) { echo $h->showMessage($lang['install_step4_form_check_php_version'], 'yellow'); }

	    // success of modules
	    if (!$php_module_not_found) {
		echo $h->showMessage($lang['install_step4_form_check_php_success'], 'green');
	    }
	} else {
	    echo "<form name='install_admin_reg_form' action='index.php?step=4' method='post'>\n";	    
	    echo "<input type='hidden' name='phpinfo' value='true' />";
	    echo "<input type='hidden' name='step' value='4' />";
	    echo "<input class='update button' type='submit' value='" . $lang['install_step4_form_check_php'] . "' />";
	    echo "</div></form>\n";
	}

	if ($folder_deleted == 0) {
	    // Confirm delete and continue install
	    echo "<div class='install_content'>" . $lang['install_step4_installation_delete_folder'] . "</div>\n";
	    echo "<form name='install_admin_reg_form' action='index.php?step=4' method='post'>\n";	    
	    echo "<input type='hidden' name='delete' value='folder' />";
	    echo "<input type='hidden' name='step' value='4' />";

	    echo "<input class='update button' type='submit' value='" . $lang['install_step4_form_delete_folder'] . "' />";
	    echo "</div></form>\n";
	} else {
	    echo "<br/><img src='../content/admin_themes/admin_default/images/delete.png' style='float:left; margin-left:12px;'>";
	    echo "<div class='install_content'><span style='color: red;'>" . $lang['install_step1_warning'] . "</span>: " . $lang['install_step4_installation_delete_failed'] . "</div>\n";
	}

	echo "<br/><div class='install_content'>" . $lang['install_step4_installation_go_play'] . "</div><br/><br/>\n";

	// Previous/Next buttons
	echo "<div class='back button''><a href='index.php?step=3'>" . $lang['install_back'] . "</a></div>\n";
	echo "<div class='next button''><a href='" . BASEURL . "'>" . $lang['install_home'] . "</a></div>\n";

	echo html_footer();
}

/**
 * create new settings file
 */
function create_new_settings_file($dbuser_name, $dbpassword_name, $dbname_name, $dbprefix_name, $dbhost_name, $baseurl_name) {

    ob_start();

   ?>

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
define("DB_USER", '<?php echo $dbuser_name; ?>');          			// Add your own database details
define("DB_PASSWORD", '<?php echo $dbpassword_name; ?>');
define("DB_NAME", '<?php echo $dbname_name; ?>');
define("DB_HOST", '<?php echo $dbhost_name; ?>');     			// You probably won't need to change this

define("DB_PREFIX", '<?php echo $dbprefix_name; ?>');     			// Database prefix, e.g. "hotaru_"
define("DB_LANG", 'en');            			// Database language, e.g. "en"
define("DB_ENGINE", 'MyISAM');					// Database Engine, e.g. "MyISAM"
define('DB_CHARSET', 'utf8');					// Database Character Set (UTF8 is Recommended), e.g. "utf8"
define("DB_COLLATE", 'utf8_unicode_ci');		// Database Collation (UTF8 is Recommended), e.g. "utf8_unicode_ci"

// Paths
define('BASEURL', "<?php echo $baseurl_name; ?>");    // e.g. http://www.mysite.com/

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


?>

<?php
   $page = "<?php" . ob_get_contents();
   ob_end_clean();
   $page = str_replace("\n", "", $page);
   $cwd = getcwd();
   $file = $cwd . "/../hotaru_settings.php";
   @chmod($file,0777);
   $fw = fopen($file, "w");
   $fputs = fputs($fw,$page, strlen($page));
   @chmod($file,0644);
   fclose($fw);

   return $fputs;

}

?>