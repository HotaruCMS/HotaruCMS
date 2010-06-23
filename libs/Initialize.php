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
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2010, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
class Initialize
{
	protected $db;                          // database object
	protected $cage;                        // Inspekt object
	protected $isDebug          = false;    // show db queries and page loading time
	
	
	/**
	 * Initialize Hotaru with the essentials
	 */
	public function __construct($h)
	{

		// session to be used by CSRF, etc.
		if (!isset($_SESSION['HotaruCMS'])) {
			session_start();
			$_SESSION['HotaruCMS'] = time();
		}

		// The order here is important!
		$this->setDefaultTimezone();
		$this->setTableConstants();				

		$this->getFiles();
		$this->cage = $this->initInspektCage();
		$this->db = $this->initDatabase();

		$this->getCurrentSiteID();
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
		if (!file_exists($filename)) {
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
			"TABLE_SITE"=>"site",
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
	 * Sets the current SiteID if multiple sites.
	 */
	public function getCurrentSiteID()
	{
		// read settings for default siteid=1 first to check whether MULTISITE is TRUE
		$sql = "SELECT settings_value FROM " . TABLE_SETTINGS . " WHERE settings_name = %s AND settings_siteid = %d";
		$multi_site = $this->db->get_var($this->db->prepare($sql, 'MULTI_SITE', 1));
		if (!defined('MULTI_SITE')) { define ('MULTI_SITE', $multi_site); }

	        if (MULTI_SITE == 'true') {
		    $url =  $this->cage->server->getRaw('HTTP_HOST');   // wanted to use sanitizeTags
		    $sql = "SELECT site_id, site_adminuser_id FROM " . TABLE_SITE . " WHERE site_url = %s";
		    $settings = $this->db->get_row($this->db->prepare($sql, $url));		 

		    if ($settings) {
			$siteid = $settings->site_id;
			$siteurl = "http://" . $url . "/";
		    } else {
			$siteid = 1;
			$siteurl = BASEURL;
		    }

		    if (!defined('MS_TABLES')) {
			$ms_tables = array();
			//get $h->$ms_tables
			foreach ( $this->db->get_col("SHOW TABLES",0) as $table_name )
			{
			    $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.Columns where TABLE_NAME = %s AND RIGHT(COLUMN_NAME,7) = %s";
			    $columns = $this->db->get_row($this->db->prepare($sql, $table_name, '%_siteid'));
			    //var_dump($columns);
			    $array1 = explode(DB_PREFIX, $table_name);
			    $tablename = $array1[1];
			    if ($columns) {
				$array2 = explode('_siteid', $columns->COLUMN_NAME);
				$ms_tables[$tablename] = $array2[0];
			    }			
			}
			//define('MS_TABLES','return ' . var_export($ms_tables, 1) . ';');
			 define('MS_TABLES',serialize($ms_tables));
		    }


		} else {
		    $siteid = 1;
		    $siteurl = BASEURL;
		}

		if (!defined('SITEID')) { define('SITEID', $siteid); }
		if (!defined('SITEURL')) { define("SITEURL", $siteurl); }
		
		if (!defined('CACHE')) {
		    
		    define("CACHE", BASE . "cache/" . $siteid . "/");

		    $dirs = array('', 'debug_logs/' , 'db_cache/', 'css_js_cache/', 'html_cache/', 'rss_cache/', 'lang_cache/');  // first array item is needed to create the SITEID base folder

		    foreach ($dirs as $dir) {
			if (!is_dir(CACHE . $dir)) {
			    mkdir(CACHE . $dir);
			}
		    }
		    
		}

		return false;
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
		require_once(EXTENSIONS . 'ezSQL/ez_sql_core.php'); // database
		require_once(EXTENSIONS . 'ezSQL/mysql/ez_sql_mysql.php'); // database
		
		// include functions
		require_once(FUNCTIONS . 'funcs.strings.php');
		require_once(FUNCTIONS . 'funcs.arrays.php');
		require_once(FUNCTIONS . 'funcs.times.php');
		require_once(FUNCTIONS . 'funcs.files.php');
	}
	
	
	/**
	 * Initialize Database
	 *
	 * @return object
	 */
	public function initDatabase()
	{
		$ezSQL = new Database(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
		$ezSQL->query("SET NAMES 'utf8'");
		
		return $ezSQL;
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
	 * @param <int> $siteid
	 * 
	 * @return <bool>
	 */
	public function readSettings() {
	    $sql = "SELECT settings_name, settings_value FROM " . TABLE_SETTINGS;
	    $settings = $this->db->get_results($this->db->prepare($sql));

	    if(!$settings) { return false; }

		// Make Hotaru settings global constants
		foreach ($settings as $setting)
		{
			if (!defined($setting->settings_name)) {
				if ($setting->settings_name != 'MULTI_SITE') { define($setting->settings_name, $setting->settings_value); }
			}
		}

		return true;
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
		// Start timer if debugging
		$global_js_var = "jQuery('document').ready(function($) {BASEURL = '". SITEURL ."'; SITEURL = '". SITEURL ."'; ADMIN_THEME = '" . ADMIN_THEME . "'; THEME = '" . THEME . "';});";
		$JsConstantsFile = "css_js_cache/JavascriptConstants.js";
	
		if (!file_exists(CACHE . $JsConstantsFile)) {
			$JsConstantsPath = CACHE . $JsConstantsFile;
			$JsConstantsfh = fopen($JsConstantsPath, 'w') or die ("Can't open file");	
			fwrite($JsConstantsfh, $global_js_var);
			fclose($JsConstantsfh);		
		}
		return false;
	}
}
?>
