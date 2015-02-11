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
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

require_once '../vendor/autoload.php';

// Managed directives
ini_set('default_charset', $charset='UTF-8');
ini_set('display_errors', 0);

// Turn off all errors for install script
error_reporting(0);

if(!ini_get('date.timezone')) {
    date_default_timezone_set('GMT');
}
                
// session - HotaruCMS_Install
if (!isset($_SESSION['HotaruCMS'])) {
    @session_start();
    $_SESSION['HotaruCMS'] = time();
}

// Read Settings
define("SETTINGS", '../config/');

if (file_exists(SETTINGS . 'settings.php')) {
    include_once(SETTINGS . 'settings.php');
    $settings_file_exists = true;
} else {
    $settings_file_exists = false;
    define('BASEURL', '/');
}

define("SITEURL", BASEURL);

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

foreach ($path_constants as $key => $value) {
    if (!defined($key)) {
        define($key, dirname(__FILE__) . $value);
    }
}
require_once('libs/install_tables.php');
require_once('libs/upgrade_tables.php');
require_once('libs/install_functions.php');

require_once(BASE . 'Hotaru.php');

$newH = false;
if ($settings_file_exists) {
    $settings = settingsFile($settings_file_exists);
    // Check whether database and tables exist on this server
    $db = new ezSQL_mysql($settings->dbuser_name, $settings->dbpassword_name, $settings->dbname_name, $settings->dbhost_name);
    $db->show_errors = false;
    
    $database_exists = $db->quick_connect($settings->dbuser_name, $settings->dbpassword_name, $settings->dbname_name, $settings->dbhost_name);	
    if ($database_exists) {
        $table_exists = $db->table_exists('miscdata');
        if ($table_exists) {
            $h = \Libs\Hotaru::instance();
            $h->start('install');
        } else {
            $newH = true;
        }
    } else {
        // no db at all
        $newH = true;
    }
} else {
    $newH = true; 
}

if ($newH) {
    //print 'newH';
    $h = new \Libs\Hotaru('start');
    $h->messages = array(); 
    $h->currentUser = false;
    $h->cage = init_inspekt_cage();
}

require_once(INSTALL .'language/install_language.php');    // language file for install

//set lang
$langSession = $h->cage->get->testAlnumLines('lang');
if ($langSession) {
    $_SESSION['lang'] = $langSession;
}

// install languages
$installLanguages = array('en' => 'English', 'ja-JP' => '日本語', 'cs-CZ' => 'Czech', 'ru' => 'Russian', 'ta' => 'Tamil', 'tr' => 'Turkish', 'uk' => 'Ukrainian');

// check session for set language
if (isset($_SESSION['lang'])) {
    $currentLang['code'] = $_SESSION['lang'];
    $lookupVal = str_replace('_', '-', $_SESSION['lang']);
    $currentLang['name'] = isset($installLanguages[$lookupVal]) ? $installLanguages[$lookupVal] : $_SESSION['lang'];
    $filename = INSTALL . 'language/install_language_' . $_SESSION['lang'] . '.php';
    if (file_exists($filename)) {
        include_once($filename);
    }
} else {
    $currentLang['name'] = 'English';
    $currentLang['code'] = 'en';
}

$step = $h->cage->get->getInt('step');        // Installation steps.
$action = $h->cage->get->getAlpha('action');    // Install or Upgrade.

// divert to step 1 if no database when trying to access upgrade
if ($action == 'upgrade' && $settings_file_exists && (!$database_exists || !$table_exists)) {
    $theMessage = !$database_exists ? $lang['install_step1_no_db_exists_failure'] : $lang['install_step1_no_table_exists_failure'];
    $h->messages[$theMessage] = 'red';
    $old_version = '';
    $show_next = false;
    $step = 0;  // rest to step if db not available
}

$activeInstall = $action == "install" ? 'active' : '';
$activeUpgrade = $action == "upgrade" ? 'active' : '';

// if we are upgrading and no settings file then show error
if ($step != 0 && $action == 'upgrade' && !$settings_file_exists) {
    $msg1 = 'Hotaru is having trouble starting.<br/>' .
                'You need to have a "settings.php" file in the config folder to <span class="label label-warning">upgrade</span><br/>' .
                'or maybe you wanted to <span class="label label-danger">Install</span> Hotaru instead.';        
        
    include('../error.php');
    exit;
}

if ($action == 'help') { $step = 90; }

switch ($step) {
	case 0:
                //  Show the choice of upgrade or install screen
		installation_welcome($h, $settings_file_exists);     // Welcome to Hotaru CMS.
		break;
	case 1: 
		if ($action == 'upgrade') {
                    $old_version = $h->miscdata('hotaru_version');
                    upgrade_check($h, $old_version, true);
		} else {	
                    // Tell user to setup database
                    if ($h->cage->get->getAlpha('type') == 'manual') {
                        database_setup_manual($h);
                    } else { 
                        database_setup($h, $settings_file_exists);
                    }
		}
		break;
	case 2:                
		if ($action == 'upgrade') {
                    $old_version = $h->miscdata('hotaru_version');
		    do_upgrade($h, $old_version);
                    upgrade_database($h);
		} else {
                    $db = init_database();
                    // Create the database tables
                    database_tables_creation($h);
		}
		break;
	case 3: 
                // Remove any cookies set in a previous installation:
                setcookie("hotaru_user", "", time()-3600, "/");
                setcookie("hotaru_key", "", time()-3600, "/");
                        
                // remove cookies from whole domain just in case of 1.4.2 cookies issue
                $parsed = parse_url(BASEURL); 
                setcookie("hotaru_user", "", time()-3600, "/", "." . $parsed['host']);
                setcookie("hotaru_key", "", time()-3600, "/", "." . $parsed['host']);
                
		if ($action == 'upgrade') {
			upgrade_step_3($h);
		} else {
			//$db = init_database();              
			register_admin($h);           // Username and password for Admin user...
		}
		break;
	case 4:
                if ($action == 'upgrade') {
			upgrade_plugins($h);
		} else {
                        installation_complete($h);    // Delete "install" folder. Visit your site"
                }
		break;
        case 90:
                template($h, 'help.php');
                break;
        case 99:
            $dbuser_name = $h->cage->get->testAlnumLines('dbuser_name');
            $dbpassword_name = $h->cage->get->KeyExists('dbpassword_name');
            $dbname_name = $h->cage->get->testAlnumLines('dbname_name');
            $dbhost_name = $h->cage->get->testAlnumLines('dbhost_name');
            if (!$settings_file_exists) {
                $db = new ezSQL_mysql($dbuser_name, $dbpassword_name, $dbname_name, $dbhost_name);
                $db->show_errors = false;
            }
            $database_exists = $db->quick_connect($dbuser_name, $dbpassword_name, $dbname_name, $dbhost_name);	
            //print $dbpassword_name . ' : ' . $dbuser_name . ' : ' . $dbname_name . ' : ' . $dbhost_name;
            if ($database_exists) {
                echo json_encode(array('error' => false));
            } else {
                echo json_encode(array('error' => true));
            }
            die();
	default:
		// Anything other than step=1, 3 or 4 will return user to Step 0
		installation_welcome($h);
	
}

exit;





/**
 * Step 0 of installation - Welcome message
 */
function installation_welcome($h, $settings_file_exists)
{
	template($h, 'install/install_welcome.php', array('settings_file_exists' => $settings_file_exists));
}


/**Step 1 of installation
 *
 */
function database_setup($h, $settings_file_exists) {
	global $lang;   //already included so Hotaru can't re-include it

	if ($h->cage->post->KeyExists('updated')) {

            $settings = new \stdClass();
            
	    $error = 0;
	    // Test CSRF
//	    if (!$h->csrf('check', 'index')) {
//		    $h->messages[$lang['install_step3_csrf_error']] = 'red';
//		    $error = 1;
//	    }

	    // Test baseurl
	    $settings->baseurl_name = $h->cage->post->testUri('baseurl');
	    if (!$settings->baseurl_name) {
		    $h->messages[$lang['install_step1_baseurl_error']] = 'red';
		    $error = 1;
	    }

	    // Test dbname
	    $settings->dbuser_name = $h->cage->post->testAlnumLines('dbuser');
	    if (!$settings->dbuser_name) {
		    $h->messages[$lang['install_step1_dbuser_error']] = 'red';
		    $error = 1;
	    }

	    // Test dbpassword
	    $settings->dbpassword_name = $h->cage->post->KeyExists('dbpassword');
	    if (!$settings->dbpassword_name) {
		    $h->messages[$lang['install_step1_dbpassword_error']] = 'red';
		    $error = 1;
	    }

	    // Test dbname
	    $settings->dbname_name = $h->cage->post->testAlnumLines('dbname');
	    if (!$settings->dbname_name) {
		    $h->messages[$lang['install_step1_dbname_error']] = 'red';
		    $error = 1;
	    }

	    // Test dbprefix
	    $settings->dbprefix_name = $h->cage->post->testAlnumLines('dbprefix');
	    if (!$settings->dbprefix_name) {
		    $h->messages[$lang['install_step1_dbprefix_error']] = 'red';
		    $error = 1;
	    }

	    // Test dbhost
	    $settings->dbhost_name = $h->cage->post->testAlpha('dbhost');
	    if (!$settings->dbhost_name) {
		    $h->messages[$lang['install_step1_dbhost_error']] = 'red';
		    $error = 1;
	    }

	} else {
            $settings = settingsFile($settings_file_exists);
	}

	// Show messages
	if ($h->cage->post->getAlpha('updated') == 'true') {
		if (!$error) {
		    // Try to write file to disk based on form inputs
		    $fputs = create_new_settings_file($settings->dbuser_name, $settings->dbpassword_name, $settings->dbname_name, $settings->dbprefix_name, $settings->dbhost_name, $settings->baseurl_name);
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
        $db = new ezSQL_mysql($settings->dbuser_name, $settings->dbpassword_name, $settings->dbname_name, $settings->dbhost_name);
        $db->show_errors = false;
	
        $database_exists = $db->quick_connect($settings->dbuser_name, $settings->dbpassword_name, $settings->dbname_name, $settings->dbhost_name);	
	$table_exists = $database_exists ? table_exists($db, 'miscdata', $settings->dbprefix_name) : false;
	$show_next = $database_exists ? true : false;
        
        if ($settings->dbpassword_name && !$database_exists) {
	    $h->messages[$lang['install_step1_no_db_exists_failure']] = 'red';
        }

	// Try to write the /config/settings.php file to disk
	//
        @chmod(SETTINGS,0777);

	$settings_file_writeable =  is_writeable(SETTINGS);

	if ($settings_file_writeable) {
            // show template
	    template($h, 'install/database_setup.php', array(
                'settings_file_exists' => $settings_file_exists,
                'cage' => $h->cage,
                'table_exists' => $table_exists,
                'show_next' => $show_next,
                'baseurl_name' => $settings->baseurl_name,
                'dbuser_name' => $settings->dbuser_name,
                'dbpassword_name' => $settings->dbpassword_name,
                'dbname_name' => $settings->dbname_name,
                'dbprefix_name' => $settings->dbprefix_name,
                'dbhost_name' => $settings->dbhost_name
            ));

	} else {
            @chmod(SETTINGS,0644);
	    database_setup_manual($h);
	}
}


/**
 * Step 1a of installation - asks to put database info in settings.php
 */
function database_setup_manual($h)
{
	template($h, 'install/database_setup_manual.php');        
}


/**
 * Step 2 of installation - Creates database tables
 */
function database_tables_creation($h)
{
	global $lang;
	global $db;
	
	$delete = $h->cage->get->getAlpha('del');        // Confirm delete.
	$show_next = false;	
	
	$table_exists = $db->table_exists('miscdata');	 
        
        if ($table_exists && $delete != 'DELETE') {
            template($h,'install/database_creation.php', array('show_next' => $show_next));
	} else {
	    $tables = array('blocked', 'categories', 'comments', 'commentvotes', 'friends', 'messaging', 'miscdata', 'plugins', 'pluginhooks', 'pluginsettings', 'posts', 'postmeta', 'postvotes', 'settings', 'spamlog', 'site', 'tags', 'tempdata', 'tokens', 'users', 'userlogin', 'userclaim', 'usermeta', 'useractivity', 'widgets');

	    // delete *all* tables in db:
	    $db->selectDB(DB_NAME);

            // reset session
            if (session_id()) {
                session_destroy(); 
            }
            
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
function register_admin($h)
{
	global $lang;   //already included so Hotaru can't re-include it
	
	// Make sure that the cache folders have been created before we call $h for the first time
	// Since we have defined CACHE in install script, the normal Initialize script will think folders are already present
	createCacheFolders();

	//$h = new \Libs\Hotaru(); // overwrites current global with fully initialized Hotaru object

        // save default admin user if none already present in db
        $sql = "SELECT user_username FROM " . TABLE_USERS . " WHERE user_role = %s";
        $admin_name = $h->db->get_var($h->db->prepare($sql, 'admin'));
        
        if (!$admin_name) {
                // Insert default settings
                $user_name = 'admin';
                $user_email = 'admin@example.com';
                $user_password = 'password';
                $defaultAdminPermission = serialize($h->currentUser->getDefaultPermissions($h, 'admin'));
                $passwordHash = password_hash($user_password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO " . TABLE_USERS . " (user_username, user_role, user_date, user_password, user_email, user_permissions) VALUES (%s, %s, CURRENT_TIMESTAMP, %s, %s, %s)";
                $h->db->query($h->db->prepare($sql, $user_name, 'admin', $passwordHash, $user_email, $defaultAdminPermission));
        }
                
        $next_button = false;
	$error = 0;
        $step = $h->cage->post->getInt('step');

	if ($step == 4) {
            // Test CSRF
            // if (!$h->csrf()) {
            //	$h->message = $lang['install_step3_csrf_error'];			;
            //	$h->messages[$lang['install_step3_csrf_error']] = 'red';
            //	$error = 1;
            //}

            if ($h->cage->post->getAlpha('updated') == 'forum') {
                // Test username
		$forumUsernameCheck = $h->cage->post->testUsername('forumUsername');
		// alphanumeric, dashes and underscores okay, case insensitive
		if ($forumUsernameCheck) {
                    $forumUsername = $forumUsernameCheck;
		} else {
                    $h->message = $lang['install_step3_username_error'];
                    $h->messages[$lang['install_step3_username_error']] = 'red';			
                    $error = 1;
		}
                
                // Test password
                $forumPasswordCheck = $h->cage->post->testPassword('forumPassword');
		if ($forumPasswordCheck) {
                    $forumPassword = $forumPasswordCheck; // $h->currentUser->generateHash($password_check);
                } else {				
                    $h->messages[$lang['install_step3_password_match_error']] = 'red';				
                    $error = 1;
                }
                
                // save
                \Hotaru\Models2\Setting::makeUpdate($h, 'FORUM_USERNAME', $forumUsername);
                \Hotaru\Models2\Setting::makeUpdate($h, 'FORUM_PASSWORD', $forumPassword);
                
                // TODO give a check/confirmation button 
                
            } else {
                // Test username
		$name_check = $h->cage->post->testUsername('username');
		// alphanumeric, dashes and underscores okay, case insensitive
		if ($name_check) {
			$user_name = $name_check;
		} else {
			$h->message = $lang['install_step3_username_error'];
			$h->messages[$lang['install_step3_username_error']] = 'red';			
			$error = 1;
		}

		// Test password
		$password_check = $h->cage->post->testPassword('password');
		if ($password_check) {
                    $password2_check = $h->cage->post->testPassword('password2');
                    if ($password_check == $password2_check) {
                        // success
                        $user_password = $password_check; // $h->currentUser->generateHash($password_check);
                    } else {				
                        $h->messages[$lang['install_step3_password_match_error']] = 'red';				
                        $error = 1;
                    }
		} else {
                    $password_check = "";
                    $password2_check = "";			
                    $h->messages[$lang['install_step3_password_error']] = 'red';			
                    $error = 1;
		}

		// Test email
		$email_check = $h->cage->post->testEmail('email');
                if ($email_check) {
                    $user_email = $email_check;
                    // also use this email address as the site notification email address
                    \Hotaru\Models2\Setting::makeUpdate($h, 'SITE_EMAIL', $user_email);
		} else {			
                    $h->messages[$lang['install_step3_email_error']] = 'red';			
                    $error = 1;
		}
            }
        }
                
        if ($error == 0) {
            $user_info = $h->currentUser->getUser($h, 0, $admin_name);
            // On returning to this page via back or next, the fields are empty at this point, so...
            $user_name = isset($user_name) ? $user_name : "";
            $user_email = isset($user_email) ? $user_email : "";
            $user_password = isset($user_password) ? $user_password : ""; 
            if ($user_name != "" && $user_email != "" && $user_password != "") {
                    // There's been a change so update...
                    $h->currentUser->name = $user_name;
                    $h->currentUser->email = $user_email;
                    $h->currentUser->password = $user_password;
                    $h->currentUser->role = 'admin';
                    $h->currentUser->updateUserBasic($h);
                    $h->currentUser->savePassword($h);

                    // auto login admin user as well, but no cookie
                    unset($h->users[$user_name]);
                    $h->loginCheck($user_name, $user_password);

                    $next_button = true;
            } else {
                    $user_id = $user_info->user_id;
                    $user_name = $user_info->user_username;
                    $user_email = $user_info->user_email;
                    //$user_password = $user_info->user_password;
            }
        }

	// Show success message
	if ($step == 4 && $error == 0) {
		$h->messages[$lang['install_step3_update_success']] = 'green';		
	}
        
        template($h, 'install/register_admin.php', array(
            'next_button' => $next_button,
            'user_name' => $user_name,
            'user_email' => $user_email
        ));
}


/**
 * Step 4 of installation - shows completion.
 */
function installation_complete($h)
{
	global $lang;
	
	//$h  = new \Libs\Hotaru(); // overwrites current global with fully initialized Hotaru object
	
	$phpinfo = $h->cage->post->getAlpha('phpinfo');        // delete install folder.

	if (!$phpinfo) { 
		//send feedback report 
		$systeminfo = new \Libs\SystemInfo(); 
		$systeminfo->hotaru_feedback($h); 
	} else {	   
                $php_version = phpversion();
                $modules = get_loaded_extensions();
                $php_module_not_found = false;

                $required = array(
			'mysqli'=>'http://php.net/manual/en/book.mysqli.php',
			'filter'=>'http://php.net/manual/en/book.filter.php',
			'curl'=>'http://php.net/manual/en/book.curl.php',
			'mbstring'=>'http://www.php.net/manual/en/book.mbstring.php'
                    );
                    /* No longer required: 'bcmath' => 'http://php.net/manual/en/book.bc.php' */
                
                foreach ($required as $module => $url) {
                    if (!in_array($module, $modules)) {
                        $h->messages[$lang['install_step4_form_check_php_warning'] . '<a href="' . $url . '" target="_blank">' . $module . '</a><br/>'] = 'red';
                        $php_module_not_found = true;
                    }
                }
                
                // check for correct version number of php
                if (version_compare($php_version, '5.2.5', '<')) { $h->messages[$lang['install_step4_form_check_php_version']] = 'yellow'; }

                // success of modules
                if (!$php_module_not_found) {
                    $h->messages[$lang['install_step4_form_check_php_success']] = 'blue';                    
                }
        }
                
	template($h, 'install/install_complete.php', array(
            'phpinfo' => $phpinfo           
        ));
}

   
/**
 * Step 2 of upgrade
 */
function upgrade_database($h)
{
        template($h, 'upgrade/upgrade_step_2.php');          
}

/**
 * Step 3 of upgrade
 */
function upgrade_step_3($h)
{
        //$h = new \Libs\Hotaru();
        
        template($h, 'upgrade/upgrade_step_3.php');          
}


/**
 * Step 3 of upgrade - shows completion.
 */
function upgrade_plugins($h)
{
	//$h = new \Libs\Hotaru();
        
        $plugman = new \Libs\PluginManagement();
        $plugman->refreshPluginOrder($h);
        $plugman->sortPluginHooks($h);
        
        template($h, 'upgrade/upgrade_plugins.php');
        
        //send feedback report
	$systeminfo = new \Libs\SystemInfo();
	$systeminfo->hotaru_feedback($h);
        
        $systeminfo->plugin_version_getAll($h);
}

/**
 * create new settings file
 */
function create_new_settings_file($dbuser_name, $dbpassword_name, $dbname_name, $dbprefix_name, $dbhost_name, $baseurl_name) {
    $checkSlash = substr($baseurl_name, -1);
    if ($checkSlash !== '/') { $baseurl_name .= '/'; }
    
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
define('DB_CHARSET', 'utf8');				// Database Character Set (UTF8 is Recommended), e.g. "utf8"
define("DB_COLLATE", 'utf8_unicode_ci');		// Database Collation (UTF8 is Recommended), e.g. "utf8_unicode_ci"

define("LANGUAGE_ADMIN", 'en');
define("LANGUAGE_MAIN", 'en');
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


/**
 * Displays ALL success or failure messages
 */
function showMessages($h)
{
        $messages = \Libs\Messages::instance();
        $messages->showMessages($h);
}


function table_exists($db, $table2check, $prefix) {
        $tables = $db->get_col("SHOW TABLES",0);
        if (in_array($prefix . $table2check, $tables)) {
            return true;
        }
        return false;
}


function settingsFile($settings_file_exists)
{
        $s = new \stdClass();
        
        if ($settings_file_exists) {
            $s->dbuser_name = DB_USER;
            $s->dbname_name = DB_NAME;
            $s->dbpassword_name = DB_PASSWORD;
            $s->dbprefix_name = DB_PREFIX;
            $s->dbhost_name = DB_HOST;
            $s->baseurl_name = BASEURL;
        } else {
            $s->dbuser_name = 'admin';
            $s->dbname_name = 'hotaru';
            $s->dbpassword_name = '';
            $s->dbprefix_name = 'hotaru_';
            $s->dbhost_name = 'localhost';
            $s->baseurl_name = "http://";
        }
        
        return $s;
}
