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

//use Illuminate\Database\Capsule\Manager as Capsule;
//use Illuminate\Events\Dispatcher;
//use Illuminate\Container\Container;
//use Illuminate\Cache\CacheManager;
//use Illuminate\Filesystem\Filesystem;

namespace Libs;

class Initialize extends Prefab
{
        protected $isDebug              = false;    // show db queries and page loading time
        protected $isTest               = false;    // show page files for testing
        protected $profiling            = false;            
	protected $adminPage            = false;    // flag to tell if we are in Admin or not
	protected $sidebars             = true;     // enable or disable the sidebars
	protected $csrfToken            = '';       // token for CSRF
	protected $lang                 = array();  // stores language file content
	
	// objects
	protected $db;                              // database object
        //protected $mdb;                             // meekro database object
	protected $cage;                            // Inspekt object
        protected $includes;                        // for CSS/JavaScript includes
	protected $debug;                           // Debug object
        protected $email;                           // Email object
	protected $pageHandling;                    // PageHandling object   
        protected $profilePoint;
        
        // data objects
	protected $avatar;                          // Avatar object
	protected $comment;                         // Comment object
        
        // settings
        protected $settings;                        // Site settings (instead of global define vars)
        protected $systemJobs;
        
        // users
        protected $currentUser;                     // UserBase object
        protected $displayUser;                     // the user being displayed i.e. for messaging or following
        protected $users;                           // List of users and their basic info loaded into memory
        //protected $miscdata;                        // settings, data from miscdata table
        
        // posts
        protected $post;                            // Post object
        protected $postList;                        // List of posts shown in list
        protected $currentPost;                     // Current Post
        
        // categories
        protected $categories;                      // List of categories
	protected $categoriesBySafeName;            // Index of categories by safename
        protected $categoriesById;                  // Index of categories by Id
        //protected $categoriesDisplay;               // List containing the <li> codes for displaying menu

	// plugins
        protected $plugin;                          // Plugin object
	protected $pluginSettings       = array();  // contains all settings for all plugins
        protected $plugins              = array();  // contains list of active types and active folders
	protected $allPluginDetails     = array();  // contains details of all plugins
        
        // templates
        protected $fileExists           = array();
	
        // page info
	protected $home                 = '';       // name for front page
	protected $pageName             = '';       // e.g. index, category
	protected $pageTitle            = '';       // e.g. Top Stories
	protected $pageType             = '';       // e.g. post, list
	protected $pageTemplate         = '';       // e.g. sb_list, tag_cloud
	protected $subPage              = '';       // e.g. category (if pageName is "index")
        //
	// messages
	protected $message              = '';       // message to display
	protected $messageType          = 'green';  // green or red, color of message box
        protected $messageRole          = '';       // the Role that this message will display for
	protected $messages             = array();  // for multiple messages
	
        // global cache
        protected $memCache;                        // memcache object
        
	// vars
        protected $vars                 = array();  // multi-purpose 
	
	/**
	 * Initialize Hotaru with the essentials
	 */
	public function __construct()
	{
		// session to be used by CSRF, etc.
		if (!isset($_SESSION['HotaruCMS'])) {
			@session_start();
			$_SESSION['HotaruCMS'] = time();
		}

                // these items can be defined in settings but if not here are the defaults
                if (!defined("DB_ENGINE")) { define("DB_ENGINE", 'InnoDB'); }
                if (!defined("MEMCACHED_HOST")) { define("MEMCACHED_HOST", '127.0.0.1'); }
                if (!defined("MEMCACHED_PORT")) { define("MEMCACHED_PORT", 11211); }
                
                // passwordhash < 5.4
                require_once(EXTENSIONS . 'passwordHash/password.php');
                
                $this->getFunctionFiles();
                
		// The order here is important!
		$this->setDefaultTimezone();
		$this->setTableConstants();

		$this->MakeCacheFolders();
		$this->loadFiles();
                
		$this->cage = $this->initInspektCage();
		$this->db = $this->initDatabase();
                //$this->mdb = $this->initDatabase('mdb');
                //$this->initEloquent();
                
                $this->memCache = $this->setMemCache(MEMCACHED_HOST, MEMCACHED_PORT);

		$this->errorReporting();
                
                //$this->loadMiscData();
                $this->loadSystemJobs();
                $this->loadCategories();
                $this->readSettings();
                $this->readAllPluginSettings();
                $this->getPluginActiveTypesAndFolders();
                
                $this->setUpDatabaseCache();
                $this->isDebug = $this->checkDebug();
                $this->setUpJsConstants();        
                        
                // TODO 
                // get Widget blocks ? - speed test it first

                // include "main" language pack
		$lang = Language::instance();
		$this->lang = $lang->includeLanguagePack($this->lang, 'main');
                $lang->writeLanguageCache($this);
                
		return true;
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
	
        
        // to get protected properties
        public function __isset($name)
        {
                //echo "Is '$name' set?\n";
                return isset($this->data[$name]);
        }
        
	
	/**
	 * Error reporting
	 */
	private function errorReporting()
	{
		// display errors already set at top of Hotaru
		//ini_set('display_errors', 1); // Gets disabled later in checkDebug()
		//error_reporting(E_ALL);
		
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
	private function setTableConstants()
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
                        "TABLE_SPAMLOG"=>"spamlog",
			"TABLE_TAGS"=>"tags",
			"TABLE_TEMPDATA"=>"tempdata",
			"TABLE_USERS"=>"users",
                        "TABLE_USERCLAIMS"=>"userclaim",
                        "TABLE_USERLOGINS"=>"userlogin",
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
	private function setDefaultTimezone()
	{
		
	}
	
	
	/**
	 * Include necessary files
	 */
	private function loadFiles()
	{
//                if (! function_exists ('mysqli_connect')) {
//                    require_once(LIBS . 'Database_mysql.php');
//                } else {                    
//                    require_once(LIBS . 'Database.php');
//                }
	}
	
//	private function initEloquent()
//        {
//                /* Setup Eloquent. */
//                $capsule = new Capsule;
//                $capsule->addConnection([
//                    'driver' => 'mysql',
//                    'host' => DB_HOST,
//                    'database' => DB_NAME,
//                    'username' => DB_USER,
//                    'password' => DB_PASSWORD,
//                    'collation' => DB_COLLATE,
//                    'prefix' => DB_PREFIX,
//                    "charset"   => DB_CHARSET
//                ]);
//
//                $capsule->setEventDispatcher(new Dispatcher(new Container));
//
//                $app = array(
//                    'files' => new FileSystem(),
//                    'config' => array(
//                        'cache.driver' => 'file',
//                        'cache.path' => CACHE . 'db_cache',
//                        'cache.prefix' => 'hotaru_'
//                    )
//                );
//                
//                $cacheManager = new CacheManager($app);
//                $capsule->setCacheManager($cacheManager);
//
//                // Make this Capsule instance available globally via static methods... (optional)
//                $capsule->setAsGlobal();
//
//                $capsule->bootEloquent();
//
//                //$queries = $capsule->connection()->getQueryLog();
//        }
        
	/**
	 * Initialize Database
	 *
	 * @return object
	 */
	private function initDatabase($type = '')
	{                
                if ($type == 'mdb') {
                    $port = '';
                    $db = new hDB(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, $port, DB_CHARSET);                    
                    //$db->debugMode('my_debugmode_handler');
                    $db->error_handler = 'my_error_handler';
                    $db->nonsql_error_handler = 'my_nonsql_error_handler';    
                    
                    //$db->debugMode(true);
                    
                } else {
                    $db = new Database(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
                    $db->query("SET NAMES 'utf8'");   
                }
                
		return $db;
        }
	
	
	/**
	 * Initialize Inspekt
	 *
	 * @return object
	 */
	private function initInspektCage()
	{
		$cage = \Inspekt::makeSuperCage(); 
		
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
         *  set global memcache object
         *  test for whether memcache or memcached methods are available on server within this myMemcache call
         */
        private function setMemCache($host, $port) 
        {   
return false;
            
            $memCache = new \myMemcache(array('host'=>$host, 'port'=>$port));
            
            //$memCache->flush();   // for flush on testing
return false;
            return $memCache;
        }
	
        
        private function loadSystemJobs()
        {
            if (!isset($this->systemJobs)) {
                $systemJobs = \Hotaru\Models2\Miscdata::getCurrentSettings($this, 'system_jobs');
                try {
                    $this->systemJobs = unserialize($systemJobs);
                } catch(Exception $e) {
                    //
                }
            }
        }
        
        
//        private function loadMiscData()
//        {
//            // deprecated since there are too many other settings for other themes etc in miscdata
//            // better to call each required setting indivdually
//            if (!isset($this->miscdata)) {
//                //$this->miscdata = HotaruModels\Miscdata::getAll();
//                $this->miscdata = \Hotaru\Models2\Miscdata::getAll($this);
//            }
//        }
        
        
        private function loadCategories()
        {
                if ($this->memCache) {
                    $memCacheCategories = $this->memCache->read('categories');
                    $memCacheCategoriesBySafeName = $this->memCache->read('categoriesBySafeName');
                    $memCacheCategoriesById = $this->memCache->read('categoriesById');
                    if ($memCacheCategories && $memCacheCategoriesBySafeName && $memCacheCategoriesById) {
                        $this->categories = $memCacheCategories;
                        $this->categoriesBySafeName = $memCacheCategoriesBySafeName;
                        $this->categoriesById = $memCacheCategoriesById;
                        return true;
                    }
                }

                //if ($this->isTest) { timer_start('cats'); }
                if (!isset($this->categories)) {
                    //$this->categories = HotaruModels\Category::getAllOrderForNavBar();
                    $this->categories = \Hotaru\Models2\Category::getAllOrderForNavBar($this);
                }

                // index of categories
                if ($this->categories) {
                    // This is like making an index on db, only here it is in memory
                    foreach ($this->categories as $category)
                    {
                        $this->categoriesBySafeName[$category->category_safe_name] = $category;
                        $this->categoriesById[$category->category_id]  = $category;
                    }
                    //if ($this->isTest) { print timer_stop(4, 'cats'); }
                    // timetests averaging 0.0009, 0.0010, 0.0013 - Sep 21, 2014
                    // timetests avergaing 0.0093, 0.0091, 0.0082 - Sep 24, 2014
                }

                if ($this->memCache) {
                    $this->memCache->write('categories', $this->categories, 10000);
                    $this->memCache->write('categoriesBySafeName', $this->categoriesBySafeName, 10000);
                    $this->memCache->write('categoriesById', $this->categoriesById, 10000);
                }
        }


        /**
	 * Returns all site settings
	 * 
	 * @return <bool>
	 */
	private function readSettings()
        {
                // TODO sort out this hard code define problem
                
                if ($this->memCache) {
                    $memCacheSettings = $this->memCache->read('settings');
                    if ($memCacheSettings) {
                        $settings = $memCacheSettings;
                    } else {
                        $settings = \Hotaru\Models2\Setting::getValues($this);
                        //$settings = HotaruModels\Setting::getValues();
                        $this->memCache->write('settings', $settings, 10000);
                    }
                } else {
                    $settings = \Hotaru\Models2\Setting::getValues($this);
                }
                
                if(!$settings) { 
                    $default_settings = array('THEME'=>'default/', 'SITE_NAME'=>'Hotaru CMS', 'FRIENDLY_URLS'=>false, 'LANG_CACHE'=>false, 'SITE_OPEN'=>false, 'DB_CACHE_DURATION'=>0, 'DB_CACHE'=>false, 'DEBUG'=>false, 'MINIFY_JS'=>false, 'MINIFY_CSS'=>false);
                    foreach ($default_settings as $setting => $value) {
                        $this->settings[$setting] = $value;
                        if (!defined($setting)) { 
                            define ($setting, $value);
                        }
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
        
        
        private function getPluginActiveTypesAndFolders()
        {
                if ($this->memCache) {
                    $memCacheAllPluginDetails = $this->memCache->read('allPluginDetails');
                    $memCachePlugins = $this->memCache->read('plugins');
                    if ($memCacheAllPluginDetails && $memCachePlugins) {
                        $this->allPluginDetails = $memCacheAllPluginDetails;
                        $this->plugins = $memCachePlugins;
                        return true;
                    }
                }
            
                $pluginsRawData = \Hotaru\Models2\Plugin::getAllDetails($this);

                $this->allPluginDetails['pluginData'] = array();
                if ($pluginsRawData) {
                    foreach ($pluginsRawData as $plugin) {
                        $this->allPluginDetails['pluginData'][$plugin->plugin_folder] = $plugin;
                        $this->allPluginDetails['pluginFolderIndexOnClass'][$plugin->plugin_class] = $plugin->plugin_folder;
                    }
                }

                // hooks
                //$h->allPluginDetails['hooks'] = \Hotaru\Models\Pluginhook::getAllEnabled();
                $this->allPluginDetails['hooks'] = \Hotaru\Models2\Pluginhook::getAllEnabled($this);

                // turn this into an index based array as it runs faster than later calling an array_in func
                // we are going to be using isset funcs later with this
                // http://nickology.com/2012/07/03/php-faster-array-lookup-than-using-in_array/

                if ($this->allPluginDetails['hooks']) {
                    foreach ($this->allPluginDetails['hooks'] as $hook) {
                        $this->allPluginDetails['hookdata'][$hook->plugin_hook][$hook->plugin_folder] = 1;
                    }
                }

                // this was the old function here originally just getting the active plugins
                //$plugins = \Hotaru\Models\Plugin::getAllActiveNames()->toArray();
                //$plugins = \Hotaru\Models2\Plugin::getAllActiveNames($this);

                $plugins = $this->allPluginDetails['pluginData'];//[$plugin->plugin_folder] = $plugin;
                foreach ($plugins as $plugin) {
                    if ($plugin->plugin_enabled) {
                        if ($plugin->plugin_type) {
                            $this->plugins['activeTypes'][$plugin->plugin_type] = 1;
                        }
                        $this->plugins['activeFolders'][$plugin->plugin_folder] = 1;
                    }
                }
            
                if ($this->memCache) {
                    $this->memCache->write('allPluginDetails', $this->allPluginDetails, 10000);
                    $this->memCache->write('plugins', $this->plugins, 10000);
                }
        }
        
        
        /**
	 * Returns all plugin settings
         * public because we may need to call it again later if plugins get updated, like on cron updates
	 * 
	 * @return <bool>
	 */
        public function readAllPluginSettings($forceUpdate = false)
        {
                // TODO
                // run timetest again on just the enabled plugins rather than all of them in db
                // $pluginsSetting = \Hotaru\Models\PuginSetting::getAll()->toArray();
            
                if ($this->memCache) {
                    $memCachePluginsSetting = $this->memCache->read('pluginsSetting');
                    if (!$forceUpdate && $memCachePluginsSetting) {
                        $this->pluginSettings = $memCachePluginsSetting;
                        return true;
                    }
                }
            
                //$pluginsSetting = \Hotaru\Models\PuginSetting::getAllWhereEnabled()->toArray();
                $pluginsSetting = \Hotaru\Models2\PuginSetting::getAllWhereEnabled($this);
                // timetests when retireving as object averaging 0.0010, 0.0014, 0.0012 - Sep 23, 2014
                // timetests when retireving as array averaging  0.0019, 0.0020, 0.0018 - Sep 23, 2014
                
                //if (1==1) { timer_start('cats'); }
                foreach ($pluginsSetting as $setting) {
                    if ($setting['plugin_setting'] == $setting['plugin_folder'] . '_settings') {
                    //if (is_serialized($setting['plugin_value'])) {
                        $this->pluginSettings[$setting['plugin_folder']] = unserialize($setting['plugin_value']);
                    } else {
                        $this->pluginSettings[$setting['plugin_folder']][$setting['plugin_setting']] = $setting['plugin_value'];
                    }                    
                }
                //if (1==1) { print timer_stop(4, 'cats'); }
                // timetests when retrieving as object averaging 0.0023, 0.0022, 0.0023 - Sep 23, 2014
                // timetests when retrieving as array averaging 0.0004, 0.0004, 0.0004 - Sep 23, 2014
                // timetests when retrieving as array and not using is_serliazed check averaging 0.0002, 0.0002, 0.0002 - Sep 23, 2014
                
                //print 'sizeofvar pluginSettings: ' . sizeofvar($this->pluginSettings) . '<br/>';
                
                if ($this->memCache) {
                    $this->memCache->write('pluginsSetting', $this->pluginSettings, 10000);
                }
                
                return true;
        }
        
        
	/**
	 * Make cache folders if they don't already exist
	 */
	private function MakeCacheFolders()
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
	private function setUpDatabaseCache()
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
	private function checkDebug()
	{
		// Start timer if debugging
		if (DEBUG == "true") {
			timer_start('hotaru');
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
	private function setUpJsConstants()
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
        
        private function getFunctionFiles()
        {
            // include functions
		require_once(FUNCTIONS . 'funcs.strings.php');
		require_once(FUNCTIONS . 'funcs.arrays.php');
		require_once(FUNCTIONS . 'funcs.times.php');
		require_once(FUNCTIONS . 'funcs.files.php');
                require_once(FUNCTIONS . 'funcs.build.php');
		require_once(FUNCTIONS . 'funcs.http.php'); 
        }
}
