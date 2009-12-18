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
 * @copyright Copyright (c) 2009, Hotaru CMS
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
    public function __construct($hotaru)
    {
        // session to be used by CSRF
        if (!isset($_SESSION['HotaruCMS'])) {
            session_start();
            $_SESSION['HotaruCMS'] = time();
        }
    
        // The order here is important!
        $this->errorReporting();
        $this->getFiles();
        $this->db = $this->initDatabase();
        $this->cage = $this->initInspektCage();
        
        $this->readSettings();
        $this->setUpDatabaseCache();
        $this->isDebug = $this->checkDebug();

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
        ini_set('display_errors',1);
        ini_set('log_errors',1);
        error_reporting(E_ALL);
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
        
        
        // include libraries
        require_once(LIBS . 'Language.php');
        require_once(LIBS . 'InspektExtras.php');   // for custom Inspekt methods
        require_once(LIBS . 'PageHandling.php');    // for page handling
        require_once(LIBS . 'IncludeCssJs.php');    // for including and mergeing css and javascript
        require_once(LIBS . 'Plugin.php');          // for plugin properties
        require_once(LIBS . 'PluginFunctions.php'); // for plugin functions
        require_once(LIBS . 'PluginSettings.php');  // for plugin settings
        require_once(LIBS . 'UserBase.php');        // for users, settings and permissions
        require_once(LIBS . 'UserAuth.php');        // for user authentication, login and registering
        require_once(LIBS . 'Post.php');            // for posts
        
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
        $ezSQL = new ezSQL_mysql(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
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
        $cage->addAccessor('getMixedString1');
        $cage->addAccessor('getMixedString2');
        $cage->addAccessor('getHtmLawed');
        
        return $cage;
    }
    
    
    /**
     * Returns all site settings
     *
     * @return bool
     */
    public function readSettings()
    {
        $sql = "SELECT * FROM " . TABLE_SETTINGS;
        $settings = $this->db->get_results($this->db->prepare($sql));
        
        if(!$settings) { return false; }
        
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
     * Set up database cache
     *
     * Note: Queries are still only cached following $this->db->cache_queries = true;
     */
    public function setUpDatabaseCache()
    {
        // Setup database cache
        $this->db->cache_timeout = DB_CACHE_DURATION; // Note: this is hours
        $this->db->cache_dir = CACHE . 'db_cache';
        if (DB_CACHE_ON == "true") {
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
            return true;
        }
        return false;
    }
        
}
?>