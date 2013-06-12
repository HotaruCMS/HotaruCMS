<?php
/**
 * Initialize Hotaru
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
class Initialize
{
	protected $db;                          // database object
        protected $mdb;                         // meekro database object
	protected $cage;                        // Inspekt object
	protected $isDebug          = false;    // show db queries and page loading time
	
	/**
	 * Initialize Hotaru with the essentials
	 */
	public function __construct($h)
	{
		// session to be used by CSRF, etc.
		if (!isset($_SESSION['HotaruCMS'])) {
			@session_start();
			$_SESSION['HotaruCMS'] = time();
		}

		// The order here is important!
		$this->setDefaultTimezone();
		$this->setTableConstants();

		$this->MakeCacheFolders();

		$this->getFiles();
		$this->cage = $this->initInspektCage();
		$this->db = $this->initDatabase();
                $this->mdb = $this->initDatabase('mdb');

		$this->errorReporting();

                $this->readSettings();
                $this->setUpDatabaseCache();
                $this->isDebug = $this->checkDebug();
                $this->setUpJsConstants();                
                
		return $this;
	}
	
	
	/**
	 * Access modifier to set protected properties
	 */
	public function __set($var, $val)
	{
		$this->$var = $val;  
	}
	
	
	/**
	 * Access modifier to get protected properties
	 */
	public function __get($var)
	{
		return $this->$var;
	}
	
	
	/**
	 * Error reporting
	 */
	public function errorReporting()
	{
		// display errors
		ini_set('display_errors', 1); // Gets disabled later in checkDebug()
		error_reporting(E_ALL);
		
		// error log filename
		$filename = CACHE . 'debug_logs/error_log.php';
		
		// delete file if over 500KB
		if (file_exists($filename) && (filesize($filename) > 500000)) {
			unlink($filename); 
		}
		
		// If doesn't exist, create a new file with die() at the top
		if (!file_exists($filename))
		{
			// write the error log
			$fh = fopen($filename, 'w') or die("Sorry, I can't open cache/debug_logs/error_log.php");
			fwrite($fh, "<?php die(); ?>\r\n");
			fclose($fh);
		}
		
		// point PHP to our error log
		ini_set('error_log', $filename);
	}


	/**
	 * Table Constants
	 */
	public function setTableConstants()
	{
		// define database tables
		$tableConstants = array(
			"TABLE_BLOCKED" => "blocked",
			"TABLE_CATEGORIES"=>"categories",
			"TABLE_COMMENTS"=>"comments",
			"TABLE_COMMENTVOTES"=>"commentvotes",
			"TABLE_FRIENDS"=>"friends",
			"TABLE_MESSAGING"=>"messaging",
			"TABLE_MISCDATA"=>"miscdata",
			"TABLE_PLUGINS"=>"plugins",
			"TABLE_PLUGINHOOKS"=>"pluginhooks",
			"TABLE_PLUGINSETTINGS"=>"pluginsettings",
			"TABLE_POSTS"=>"posts",
			"TABLE_POSTMETA"=>"postmeta",
			"TABLE_POSTVOTES"=>"postvotes",
			"TABLE_SETTINGS"=>"settings",
			"TABLE_TAGS"=>"tags",
			"TABLE_TEMPDATA"=>"tempdata",
			"TABLE_USERS"=>"users",
			"TABLE_USERMETA"=>"usermeta",
			"TABLE_USERACTIVITY"=>"useractivity",
			"TABLE_WIDGETS"=>"widgets"
		);

		foreach ( $tableConstants as $key => $value ) {
			if (!defined($key)) {
				define($key, DB_PREFIX . $value);
			}
		}
	}

	/**
	 * Set the timezone
	 */
	public function setDefaultTimezone()
	{
		// set timezone
		$version = explode('.', phpversion());
		if($version[0] > 4){
			$tmz = date_default_timezone_get();
			date_default_timezone_set($tmz);
		}
	}
	
	
	/**
	 * Include necessary files
	 */
	public function getFiles()
	{
		// include third party libraries
		require_once(EXTENSIONS . 'csrf/csrf_class.php'); // protection against CSRF attacks
		
                require_once(EXTENSIONS . 'Inspekt/Inspekt.php'); // sanitation
                require_once(LIBS       . 'InspektExtras.php'); // sanitation
                
                //require_once('Log.php'); // PEAR function
                
		require_once(EXTENSIONS . 'ezSQL/ez_sql_core.php'); // database  
                
                if (! function_exists ('mysqli_connect')) {
                    require_once(LIBS . 'Database_mysql.php');
                } else {                    
                    require_once(LIBS . 'Database.php');
                    
                }
                require_once(EXTENSIONS . 'meekrodb/hmeek.php');
                                
		// include functions
		require_once(FUNCTIONS . 'funcs.strings.php');
		require_once(FUNCTIONS . 'funcs.arrays.php');
		require_once(FUNCTIONS . 'funcs.times.php');
		require_once(FUNCTIONS . 'funcs.files.php');
                require_once(FUNCTIONS . 'funcs.build.php');
		require_once(FUNCTIONS . 'funcs.http.php'); 
	}
	
	
	/**
	 * Initialize Database
	 *
	 * @return object
	 */
	public function initDatabase($type = '')
	{                
                if ($type == 'mdb') {
                    $port = '';
                    $db = new hDB(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, $port, DB_CHARSET);
                    //$db->debugMode('my_debugmode_handler');
                    $db->error_handler = 'my_error_handler';
                    $db->nonsql_error_handler = 'my_nonsql_error_handler';                    
                } else {
                    $db = new Database(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
                    //$db->query("SET NAMES 'utf8'");
                    //mysqli_set_charset($link, "utf8");    
                }

		return $db;
        }
	
	
	/**
	 * Initialize Inspekt
	 *
	 * @return object
	 */
	public function initInspektCage()
	{
		$cage = Inspekt::makeSuperCage(); 
		
		// Add Hotaru custom methods
		$cage->addAccessor('testAlnumLines');
		$cage->addAccessor('testPage');
		$cage->addAccessor('testUsername');
		$cage->addAccessor('testPassword');
		$cage->addAccessor('getFriendlyUrl');
		$cage->addAccessor('sanitizeAll');
		$cage->addAccessor('sanitizeTags');
		$cage->addAccessor('sanitizeEnts');
		$cage->addAccessor('getHtmLawed');
		
		return $cage;
	}
	
	
	/**
	 * Returns all site settings
	 * 
	 * @return <bool>
	 */
	public function readSettings() {	    
            
                if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50300 || !ACTIVERECORD) {                    
                    $sql = "SELECT settings_name, settings_value FROM " . TABLE_SETTINGS;                    
                    $settings = $this->db->get_results($this->db->prepare($sql));                   
                } else {                    
                    $sql = "SELECT settings_name, settings_value FROM " . TABLE_SETTINGS;
                    $settings = $this->mdb->query($sql);  
//                    $settings = $this->db->query($sql);
                    //$settings = models___Settings::all(array('select' => 'settings_name, settings_value'));
                }
                
                if(!$settings) { 
                    $default_settings = array('THEME'=>'default/', 'SITE_NAME'=>'Hotaru CMS', 'FRIENDLY_URLS'=>false, 'LANG_CACHE'=>false, 'SITE_OPEN'=>false, 'DB_CACHE_DURATION'=>0, 'DB_CACHE'=>false, 'DEBUG'=>false);
                    foreach ($default_settings as $setting => $value) {
                        if (!defined($setting)) define ($setting, $value);
                    }                    
                    return false; 
                }
                
                /**
                 * override the theme if admin and ?themePreview is set on url                
                 */                
                $themePreview = $this->cage->get->testAlnumLines('themePreview');
                if ($themePreview) $settings[2] = array('settings_name' => 'THEME', 'settings_value' => $themePreview . '/');
                
		// Make Hotaru settings global constants
		foreach ($settings as $setting)
		{                    
			if (!defined($setting->settings_name)) {
				define($setting->settings_name, $setting->settings_value);
			}                                               
		}                                

		return true;
	}
        
        
	/**
	 * Make cache folders if they don't already exist
	 */
	public function MakeCacheFolders()
	{
		// create a debug_logs folder if one doesn't exist.
		if (!is_dir(CACHE . 'debug_logs')) { mkdir(CACHE . 'debug_logs'); }

		// create a db_cache folder if one doesn't exist.
		if (!is_dir(CACHE . 'db_cache')) { mkdir(CACHE . 'db_cache'); }

		// create a css_js_cache folder if one doesn't exist.
		if (!is_dir(CACHE . 'css_js_cache')) { mkdir(CACHE . 'css_js_cache'); }

		// create a lang_cache folder if one doesn't exist.
		if (!is_dir(CACHE . 'lang_cache')) { mkdir(CACHE . 'lang_cache'); }

		// create an rss_cache folder if one doesn't exist.
		if (!is_dir(CACHE . 'rss_cache')) { mkdir(CACHE . 'rss_cache'); }

		// create an html_cache folder if one doesn't exist.
		if (!is_dir(CACHE . 'html_cache')) { mkdir(CACHE . 'html_cache'); }
	}


	/**
	 * Set up database cache
	 *
	 * Note: Queries are still only cached following $this->db->cache_queries = true;
	 */
	public function setUpDatabaseCache()
	{
		// Setup database cache
		$this->db->cache_timeout = DB_CACHE_DURATION; // Note: this is hours
		$this->db->cache_dir = CACHE . 'db_cache';

		if (DB_CACHE == "true") {
			$this->db->use_disk_cache = true;
			return true;
		} else {
			$this->db->use_disk_cache = false;
			return false;
		}
	} 
	
	
	/**
	 * Debug timer
	 *
	 * @ return bool 
	 */
	public function checkDebug()
	{
		// Start timer if debugging
		if (DEBUG == "true") {
			require_once(FUNCTIONS . 'funcs.times.php');
			timer_start();
			ini_set('display_errors', 1); // show errors
			return true;
		} else {
			ini_set('display_errors', 0); // hide errors
		}
		
		return false;
	}
	
	/**
	 * Get JQuery Globals
	 *
	 *  
	 */
	public function setUpJsConstants()
	{
		if (!defined('SITEURL')) { define("SITEURL", BASEURL); }

		// Start timer if debugging
		$global_js_var = "jQuery('document').ready(function($) {BASE = '" . BASE . "'; BASEURL = '". SITEURL ."'; SITEURL = '". SITEURL ."'; ADMIN_THEME = '" . ADMIN_THEME . "'; THEME = '" . THEME . "';});";
		$JsConstantsFile = "css_js_cache/JavascriptConstants.js";
	
		if (!file_exists(CACHE . $JsConstantsFile)) 
		{
			// write the JavascriptConstants file
			$JsConstantsPath = CACHE . $JsConstantsFile;
			$JsConstantsfh = fopen($JsConstantsPath, 'w') or die ("Can't open file");	
			fwrite($JsConstantsfh, $global_js_var);
			fclose($JsConstantsfh);		
		}
		return false;
	}
}
?>