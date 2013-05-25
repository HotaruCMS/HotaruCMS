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

// Read Settings
define("SETTINGS", '../config/settings.php');

if (file_exists(SETTINGS)) {
    include_once(SETTINGS);
    $settings_file_exists = true;
} else {
    $settings_file_exists = false;
}

// define path constants
$path_constants = array(
    "BASE" => "/../",
    "ADMIN" => "/../admin/",
    "INSTALL" => "/",
    "CACHE" => "/../cache/",
    "LIBS" => "/../libs/",
    "EXTENSIONS" => "/../libs/extensions/",
    "FUNCTIONS" => "/../functions/",
    "THEMES" => "/../content/themes/",
    "PLUGINS" => "/../content/plugins/",
    "ADMIN_THEMES" => "/../content/admin_themes/",
    );

foreach ( $path_constants as $key => $value ) {
if (!defined($key))
    define($key, dirname(__FILE__) . $value);
}

require_once('install_tables.php');
require_once('install_functions.php');
require_once(BASE . 'Hotaru.php');
require_once(EXTENSIONS . 'csrf/csrf_class.php'); // protection against CSRF attacks
require_once(EXTENSIONS . 'Inspekt/Inspekt.php'); // sanitation
require_once(EXTENSIONS . 'ezSQL/ez_sql_core.php'); // database
require_once(EXTENSIONS . 'ezSQL/mysql/ez_sql_mysql.php'); // database
//$h  = new Hotaru('install'); // must come before language inclusion
$h  = new Hotaru('start');
require_once(INSTALL . 'install_language.php');    // language file for install

$version_number = $h->version;
$cage = init_inspekt_cage();

$step = $cage->get->getInt('step');        // Installation steps.
$action = $cage->get->getAlpha('action');    // Install or Upgrade.

switch ($step) {
	case 0:
		installation_welcome();     // "Welcome to Hotaru CMS.
		break;
	case 1: 
		if ($action == 'upgrade') {
			database_upgrade();
		} else {
			// Remove any cookies set in a previous installation:
			setcookie("hotaru_user", "", time()-3600, "/");
			setcookie("hotaru_key", "", time()-3600, "/");
			
			// database setup (DB name, user, password, prefix...)
			// use this direct call instead of $db = init_database() because db may not exist yet. We need to check and control the response
			$db = new ezSQL_mysql(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);

			if ($cage->get->getAlpha('type') == 'manual') { database_setup_manual(); } else { database_setup(); }
		}
		break;
	case 2:                
		if ($action == 'upgrade') {
		    database_upgrade();
		} else {
			$db = init_database();
			database_creation($h);        // Creates the database tables
		}
		break;
	case 3: 
		if ($action == 'upgrade') {
			upgrade_plugins();
		} else {
			$db = init_database();
			register_admin();           // Username and password for Admin user...
		}
		break;
	case 4:
		installation_complete();    // Delete "install" folder. Visit your site"
		break;
	default:
		// Anything other than step=1, 3 or 4 will return user to Step 0
		installation_welcome();
	
}

exit;


/*
 * function for calling templates with header and footer
 * 
 */
function template($h, $template, $args = array())
{
    global $lang;
    global $version_number;
    
    // check for any vars being passed in
    extract($args);

    include_once('templates/header.php');
    
    include_once('templates/' . $template);
    
    include_once('templates/footer.php');
}

// TODO
// remove header and footer when all functions have been put in templates
function html_install_header()
{
    global $lang;
    include_once('templates/header.php');
}

function html_footer()
{
    global $lang;
    global $version_number;    
    include_once('templates/footer.php');
}


/**
 * Step 0 of installation - Welcome message
 */
function installation_welcome()
{
	template($h, 'install/install_welcome.php');
}


/**Step 1 of installation
 *
 */
function database_setup() {
	global $lang;   //already included so Hotaru can't re-include it
	global $db;
	global $h;
	global $cage;
	global $settings_file_exists;

	//$h  = new Hotaru(); // overwrites current global with fully initialized Hotaru object
	$show_next = false;

	if ($cage->post->KeyExists('updated')) {

	    $error = 0;
	    // Test CSRF
//	    if (!$h->csrf('check', 'index')) {
//		    $h->messages[$lang['install_step3_csrf_error']] = 'red';
//		    $error = 1;
//	    }

	    // Test baseurl
	    $baseurl_name = $cage->post->testUri('baseurl');
	    if (!$baseurl_name) {
		    $h->messages[$lang['install_step1_baseurl_error']] = 'red';
		    $error = 1;
	    }

	    // Test dbname
	    $dbuser_name = $cage->post->testAlnumLines('dbuser');
	    if (!$dbuser_name) {
		    $h->messages[$lang['install_step1_dbuser_error']] = 'red';
		    $error = 1;
	    }

	    // Test dbpassword
	    $dbpassword_name = $cage->post->KeyExists('dbpassword');
	    if (!$dbpassword_name) {
		    $h->messages[$lang['install_step1_dbpassword_error']] = 'red';
		    $error = 1;
	    }

	    // Test dbname
	    $dbname_name = $cage->post->testAlnumLines('dbname');
	    if (!$dbname_name) {
		    $h->messages[$lang['install_step1_dbname_error']] = 'red';
		    $error = 1;
	    }

	    // Test dbprefix
	    $dbprefix_name = $cage->post->testAlnumLines('dbprefix');
	    if (!$dbprefix_name) {
		    $h->messages[$lang['install_step1_dbprefix_error']] = 'red';
		    $error = 1;
	    }

	    // Test dbhost
	    $dbhost_name = $cage->post->testAlpha('dbhost');
	    if (!$dbhost_name) {
		    $h->messages[$lang['install_step1_dbhost_error']] = 'red';
		    $error = 1;
	    }

	} else {
            if ($settings_file_exists) {
                $dbuser_name = DB_USER;
                $dbname_name = DB_NAME;
		$dbpassword_name = DB_PASSWORD;
                $dbprefix_name = DB_PREFIX;
                $dbhost_name = DB_HOST;
                $baseurl_name = BASEURL;
            } else {
                $dbuser_name = 'admin';
                $dbname_name = 'hotaru';
		$dbpassword_name = '';
                $dbprefix_name = 'hotaru_';
                $dbhost_name = 'localhost';
                $baseurl_name = "http://"; // . $cage->server->sanitizeTags('HTTP_HOST') . "/";
            }
	}

	// Show messages
	if ($cage->post->getAlpha('updated') == 'true') {
		if (!$error) {
		    // Try to write file to disk based on form inputs
		    $fputs = create_new_settings_file($dbuser_name, $dbpassword_name, $dbname_name, $dbprefix_name, $dbhost_name, $baseurl_name);
		    // if file written successfully then
		    if ($fputs) {
			$h->messages[$lang['install_step1_update_file_writing_success']] = 'green';					
			// if yes set warning message var
		    } else {
			$h->messages[$lang['install_step1_update_file_writing_failure']] = 'red';
		    }
		}
                @chmod(SETTINGS,0644);
	}

	// Check whether database and tables exist on this server
	$db->show_errors = false;
	$database_exists = $db->quick_connect($dbuser_name, $dbpassword_name, $dbname_name, $dbhost_name);	
	if (!$database_exists) {
	    $h->messages[$lang['install_step1_no_db_exists_failure']] = 'red';
	} else {
	    $show_next = true;	   
	    $table_exists = $db->table_exists('miscdata');	   
	}

	// Try to write the /config/settings.php file to disk
	//
        @chmod(SETTINGS,0777);

	$settings_file_writeable =  is_writeable(SETTINGS);

	if ($settings_file_writeable) {
            // show template
	    template($h, 'install/database_setup.php', array(
                'cage' => $cage,
                'table_exists' => $table_exists,
                'show_next' => $show_next,
                'baseurl_name' => $baseurl_name,
                'dbuser_name' => $dbuser_name,
                'dbpassword_name' => $dbpassword_name,
                'dbname_name' => $dbname_name,
                'dbprefix_name' => $dbprefix_name,
                'dbhost_name' => $dbhost_name
            ));

	} else {
            @chmod(SETTINGS,0644);
	    database_setup_manual();
	}

}


/**
 * Step 1a of installation - asks to put database info in settings.php
 */
function database_setup_manual()
{
	template($h, 'install/database_setup_manual.php');        
}

/**
 * Step 2 of Upgrade - update database tables
 */
function database_upgrade()
{
    if (file_exists(SETTINGS)) {
        include_once('install-upgrade.php');
    }
    else {
        echo 'You need to have a "settings.php" file in the config folder to upgrade Hotaru.';
    }
}


/**
 * Step 2 of installation - Creates database tables
 */
function database_creation($h)
{
	global $lang;
	global $db;
	global $cage;
       
	$delete = $cage->get->getAlpha('del');        // Confirm delete.
	$show_next = false;	
	
	$table_exists = $db->table_exists('miscdata');	 
        
        if ($table_exists && $delete != 'DELETE') {
            
            template($h,'install/database_creation.php', array('show_next' => $show_next));
	
	} else {	   
	    
	    $tables = array('blocked', 'categories', 'comments', 'commentvotes', 'friends', 'messaging', 'miscdata', 'plugins', 'pluginhooks', 'pluginsettings', 'posts', 'postmeta', 'postvotes', 'settings', 'site', 'tags', 'tempdata', 'tokens', 'users', 'usermeta', 'useractivity', 'widgets');

	    // delete *all* tables in db:
	    $db->selectDB(DB_NAME);

            template($h,'install/database_creation_2.php', array(
                'db'=>$db,
                'show_next' => $show_next,
                'tables' => $tables
             ));
        }
}


/**
 * Step 3 of installation - registers the site Admin.
 */
function register_admin()
{
	global $lang;   //already included so Hotaru can't re-include it
	global $db;

	// Make sure that the cache folders have been created before we call $h for the first time
	// Since we have defined CACHE in install script, the normal Initialize script will think folders are already present
	$dirs = array('debug_logs/' , 'db_cache/', 'css_js_cache/', 'html_cache/', 'rss_cache/', 'lang_cache/'); 

	foreach ($dirs as $dir) {
	    //print "checking where dir exists at " . CACHE . $dir . '<br/>';
	    if (!is_dir(CACHE . $dir)) {
		//print "trying to create " . CACHE . $dir . '<br/>';
		mkdir(CACHE . $dir);
	    }
	}

	$h  = new Hotaru(); // overwrites current global with fully initialized Hotaru object


	echo html_install_header();

	// Step title
	echo "<legend>" . $lang['install_step3'] . "</legend>\n";

	// Complete Step Progress Bar
	echo "<!-- Complete Step Progress Bar -->
	<div class=\"progress progress-info\">
		<div class=\"bar\" style=\"width: 75%\"></div>
	</div>";
	
	// Step content
	echo "<div class='well'><h4>" . $lang['install_step3_instructions'] . ":</h4>\n";
	
	// Make note of password message
	echo $lang["install_step3_make_note"] . "<br />\n";

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
			$user_info = $h->currentUser->getUser($h, 0, $admin_name);
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
	echo "<tr><td>&nbsp;</td><td style='text-align:right;'><input class='btn btn-primary' type='submit' value='" . $lang['install_step3_form_update'] . "' /></td></tr>\n";

	echo "</table>";
	echo "</form>\n";
	echo "</div>";
	
	// Previous/Next buttons
	echo "<a class='btn' href='index.php?step=2'>" . $lang['install_back'] . "</a>\n";
	if ($h->cage->post->getAlpha('updated') == 'true' && isset($next_button)) {
		// active "next" link if user has been updated
		echo "<a class='btn btn-primary' href='index.php?step=4'>" . $lang['install_next'] . "</a>\n";
	} else {
		// link disbaled until "update" button pressed
		echo "<a class='btn btn-primary disabled'>" . $lang['install_next'] . "</a>\n";
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

	$h  = new Hotaru(); // overwrites current global with fully initialized Hotaru object
	
	$phpinfo = $cage->post->getAlpha('phpinfo');        // delete install folder.

	if (!$phpinfo) { 
		//send feedback report 
		$systeminfo = new SystemInfo(); 
		$systeminfo->hotaru_feedback($h); 
	}

	echo html_install_header();

	// Step title
	echo "<legend>" . $lang['install_step4'] . "</legend>\n";
	
	echo "
	<div class=\"alert alert-success\">
		<strong>" . $lang['install_step4_installation_complete'] . "</strong><br />
		<!-- Complete Step Progress Bar -->
		<div class=\"progress progress-success\">
			<div class=\"bar\" style=\"width: 100%\"></div>
		</div>
	</div>";

	// Step content
	echo "
	<div class=\"alert alert-error\">
		<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>
		" . $lang['install_step4_installation_delete'] . "
	</div>\n";


	if ($phpinfo) {
	    echo '<br/>';
	    $php_version = phpversion();
	    $modules = get_loaded_extensions();
	    $php_module_not_found = false;

	    $required = array(
			'mysql'=>'http://php.net/manual/en/book.mysql.php',
			'filter'=>'http://php.net/manual/en/book.filter.php',
			'curl'=>'http://php.net/manual/en/book.curl.php',
			'mbstring'=>'http://www.php.net/manual/en/book.mbstring.php');

		/* No longer required: 'bcmath' => 'http://php.net/manual/en/book.bc.php' */

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
	    echo "</form>\n";
	}

	echo "<br/><div class='well'>" . $lang['install_step4_installation_go_play'] . "</div><br/><br/>\n";

	// Previous/Next buttons
	echo "<a class='btn' href='index.php?step=3'>" . $lang['install_back'] . "</a>\n";
	echo "<a class='btn btn-primary' href='" . BASEURL . "index.php'>" . $lang['install_home'] . "</a>\n";

	echo html_footer();
}


/**
 * Step 3 of upgrade - shows completion.
 */
function upgrade_plugins()
{
	global $lang;
	global $cage;
	$h = new Hotaru();
	
        template($h, 'upgrade/upgrade_plugins.php');
}

/**
 * create new settings file
 */
function create_new_settings_file($dbuser_name, $dbpassword_name, $dbname_name, $dbprefix_name, $dbhost_name, $baseurl_name) {

    ob_start();

   ?>

 /* Configuration file for Hotaru CMS. */

// Paths
define("BASEURL", '<?php echo $baseurl_name; ?>');    // e.g. http://www.mysite.com/    Needs trailing slash (/)

// Database details
define("DB_USER", '<?php echo $dbuser_name; ?>');          		// Add your own database details
define("DB_PASSWORD", '<?php echo $dbpassword_name; ?>');
define("DB_NAME", '<?php echo $dbname_name; ?>');
define("DB_HOST", '<?php echo $dbhost_name; ?>');     			// You probably won't need to change this

// You probably don't need to change these
define("DB_PREFIX", '<?php echo $dbprefix_name; ?>');     		// Database prefix, e.g. "hotaru_"
define("DB_LANG", 'en');            			// Database language, e.g. "en"
define("DB_ENGINE", 'MyISAM');				// Database Engine, e.g. "MyISAM"
define('DB_CHARSET', 'utf8');				// Database Character Set (UTF8 is Recommended), e.g. "utf8"
define("DB_COLLATE", 'utf8_unicode_ci');		// Database Collation (UTF8 is Recommended), e.g. "utf8_unicode_ci"

?><?php  // leave this line squashed up here as we dont want any blank lines at the end of the settings file
   $page = "<?php" . ob_get_contents();
   ob_end_clean();
   //$page = str_replace("\n", "", $page);
   $cwd = getcwd();
   $file = $cwd . "/../config/settings.php";
   @chmod($file,0777);
   $fw = fopen($file, "w");
   $fputs = fputs($fw,$page, strlen($page));
   @chmod($file,0644);
   fclose($fw);

   return $fputs;

}

function add_DBPREFIX($table) {   
    return DB_PREFIX . $table;
}
?>