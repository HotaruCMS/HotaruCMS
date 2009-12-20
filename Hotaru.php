<?php
/**
 * The engine, powers everything :-)
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
class Hotaru
{
    protected $version              = "1.0";    // Hotaru CMS version
    protected $isDebug              = false;    // show db queries and page loading time
    protected $isAdmin              = false;    // flag to tell if we are in Admin or not
    protected $sidebars             = true;     // enable or disable the sidebars
    protected $csrfToken            = '';       // token for CSRF
    protected $lang                 = array();  // stores language file content
    
    // objects
    protected $db;                              // database object
    protected $cage;                            // Inspekt object
    protected $currentUser;                     // UserBase object
    protected $plugin;                          // Plugin object
    protected $post;                            // Post object
    protected $includes;                        // for CSS/JavaScript includes
    
    // page info
    protected $pageName             = '';       // e.g. top
    protected $pageTitle            = '';       // e.g. Top Stories
    protected $pageType             = '';       // e.g. list
    protected $pageTemplate         = '';       // e.g. list, tag_cloud

    // ALL plugins
    protected $pluginSettings       = array();  // contains all settings for all plugins
    protected $allPluginDetails     = array();  // contains details of all plugins
    
    // messages
    protected $message              = '';       // message to display
    protected $messageType          = 'green';  // green or red, color of message box
    protected $messages             = array();  // for multiple messages
    
    // miscellaneous
    protected $vars                 = array();  // multi-purpose
    
    /**
     * CONSTRUCTOR - Initialize
     */
    public function __construct()
    {
        // initialize Hotaru
        if (!isset($start)) { 
            require_once(LIBS . 'Initialize.php');
            $init = new Initialize($this);
            $this->db           = $init->db;            // database object
            $this->cage         = $init->cage;          // Inspekt cage
            $this->isDebug      = $init->isDebug;       // set debug
            $this->currentUser  = new UserAuth();       // the current user
            $this->plugin       = new Plugin();         // instantiate Plugin object
            $this->post         = new Post();           // instantiate Post object
            $this->includes     = new IncludeCssJs();   // instantiate Includes object
            $this->csrf('set');                         // set a csrfToken
        }
    }
    
    
/* *************************************************************
 *
 *  HOTARU FUNCTIONS
 *
 * *********************************************************** */


    /**
     * START - the top of "Hotaru", i.e. the page-building process
     */
    public function start($entrance = '')
    {
        // include "main" language pack
        $lang = new Language();
        $this->lang = $lang->includeLanguagePack($this->lang, 'main');
        
        $this->getPageName();   // fills $hotaru->pageName

        switch ($entrance) {
            case 'admin':
                $this->isAdmin = true;
                $this->lang = $lang->includeLanguagePack($this->lang, 'admin');
                require_once(LIBS . 'AdminAuth.php');       // include Admin class
                $admin = new AdminAuth();                   // new Admin object
                $page = $admin->adminInit($this);       // initialize Admin & get desired page
                $this->checkCookie();                   // check cookie reads user details
                $this->checkAccess();                   // site closed if no access permitted
                $this->checkCssJs();                    // check if we need to merge css/js
                $this->adminPages($page);               // Direct to desired Admin page
                break;
            default:
                $this->checkCookie();                   // log in user if cookie
                $this->checkAccess();                   // site closed if no access permitted
                $this->checkCssJs();                    // check if we need to merge css/js
                $this->displayTemplate('index');        // displays the index page
        }

        exit;
    }
    
    
/* *************************************************************
 *
 *  ACCESS MODIFIERS
 *
 * *********************************************************** */
 
 
    /**
     * Access modifier to set protected properties
     */
    public function __set($var, $val)
    {
        $this->$var = $val;
    }
    
    
    /**
     * Access modifier to get protected properties
     * The & is necessary (http://bugs.php.net/bug.php?id=39449)
     */
    public function &__get($var)
    {
        return $this->$var;
    }


/* *************************************************************
 *
 *  DEFAULT PLUGIN HOOK ACTIONS
 *
 * *********************************************************** */
 
     
    /**
     * Include language file if available
     */
    public function install_plugin()
    {
        $this->includeLanguage($this->plugin->folder);
    }
     
     
    /**
     * Include All CSS and JavaScript files for this plugin
     */
    public function header_include()
    {
        // include a files that match the name of the plugin folder:
        $this->includeJs($this->plugin->folder); // filename, folder name
        $this->includeCss($this->plugin->folder);
    }
    
    
    /**
     * Include All CSS and JavaScript files for this plugin in Admin
     */
    public function admin_header_include()
    {
        // include a files that match the name of the plugin folder:
        $this->includeJs($this->plugin->folder, true); // filename, folder name, admin
        $this->includeCss($this->plugin->folder, true);
    }
    
    /**
     * Include code as a template before the closing </body> tag
     */
    public function pre_close_body()
    {
        $this->displayTemplate($this->plugin->folder . '_footer', $this->plugin->folder);
    }
    

    /**
     * Display Admin sidebar link
     */
    public function admin_sidebar_plugin_settings()
    {
        $vars['plugin'] = $this->plugin->folder;
        $vars['name'] = make_name($this->plugin->folder);
        return $vars;
    }
    
    
    /**
     * Display Admin settings page
     *
     * @return true
     */
    public function admin_plugin_settings()
    {
        // This requires there to be a file in the plugin folder called pluginname_settings.php
        // The file must contain a class titled PluginNameSettings
        // The class must have a method called "settings".
        if ($this->cage->get->testAlnumLines('plugin') != $this->plugin->folder) { return false; }
        
        if (file_exists(PLUGINS . $this->plugin->folder . '/' . $this->plugin->folder . '_settings.php')) {
            include_once(PLUGINS . $this->plugin->folder . '/' . $this->plugin->folder . '_settings.php');
        }
        $settings_class = make_name($this->plugin->folder, '_') . 'Settings'; // e.g. CategoriesSettings
        $settings_class = str_replace(' ', '', $settings_class); // strip spaces
        $settings_object = new $settings_class();
        $settings_object->settings($this);   // call the settings function
        return true;
    }
    
    
/* *************************************************************
 *
 *  PAGE HANDLING FUNCTIONS
 *
 * *********************************************************** */
 
    /**
     * Determine the title tags for the header
     *
     * @return string - the title
     */
    public function getTitle()
    {
        $pageHandling = new PageHandling();
        return $pageHandling->getTitle($this);
    }
    
    
    /**
     * Includes a template to display
     *
     * @param string $page page name
     * @param string $plugin optional plugin name
     * @param bool $include_once true or false
     */
    public function displayTemplate($page = '', $plugin = '', $include_once = true)
    {
        $pageHandling = new PageHandling();
        $pageHandling->displayTemplate($this, $page, $plugin, $include_once);
    }
    
    
    /**
     * Checks if current page (in url or form) matches the page parameter
     *
     * @param string $page page name
     */
    public function isPage($page = '')
    {
        $pageHandling = new PageHandling();
        return $pageHandling->getPageName($this, $page);
    }
    
    
    /**
     * Check to see if the Admin settings page we are looking at  
     * matches the plugin passed to this function.
     *
     * @param string $folder - plugin folder
     * @return bool
     *
     *  Notes: This is used in "admin_header_include" so we only include the css, 
     *         javascript etc. for the plugin we're trying to change settings for.
     *  Usage: $hotaru->isSettingsPage('sb_submit') returns true if 
     *         page=plugin_settings and plugin=sb_submit in the url.
     */
    public function isSettingsPage($folder = '')
    {
        $pageHandling = new PageHandling();
        return $pageHandling->isSettingsPage($this, $folder);
    }

    
    /**
     * Gets the current page name
     */
    public function getPageName()
    {
        $pageHandling = new PageHandling();
        $this->pageName = $pageHandling->getPageName($this);
        return $this->pageName;
    }
    
    
    /**
     * Converts a friendly url into a standard one
     *
     * @param string $friendly_url
     * return string $standard_url
     */
    public function friendlyToStandardUrl($friendly_url) 
    {
        $pageHandling = new PageHandling();
        return $pageHandling->friendlyToStandardUrl($this, $friendly_url);
    }
    
    
    /**
     * Generate either default or friendly urls
     *
     * @param array $parameters an array of pairs, e.g. 'page' => 'about' 
     * @param string $head either 'index' or 'admin'
     * @return string
     */
    public function url($parameters = array(), $head = 'index')
    {
        $pageHandling = new PageHandling();
        return $pageHandling->url($this, $parameters, $head);
    }
    
    
    /**
     * Prepare pagination
     *
     * @param array $items - array of all items to show
     * @param int $items_per_page
     * @param int $pg - current page number
     * @return object - object of type Paginated
     */
    public function pagination($items = array(), $items_per_page = 10, $pg = 0)
    {
        $pageHandling = new PageHandling();
        return $pageHandling->pagination($this, $items, $items_per_page, $pg);
    }
    
 
    /**
     * Return page numbers bar
     *
     * @param object $pageObject - current object of type Paginated
     * @return string - HTML for page number bar
     */
    public function pageBar($pageObject = NULL)
    {
        $pageHandling = new PageHandling();
        return $pageHandling->pageBar($this, $pageObject);
    }
    

/* *************************************************************
 *
 *  BREADCRUMB FUNCTIONS
 *
 * *********************************************************** */
 
 
    /**
     * Build breadcrumbs
     */
    public function breadcrumbs()
    {
        require_once(LIBS . 'Breadcrumbs.php');
        $breadcrumbs = new Breadcrumbs();
        return $breadcrumbs->buildBreadcrumbs($this);
    }
    
 
 /* *************************************************************
 *
 *  USERAUTH FUNCTIONS / USERBASE FUNCTIONS
 *
 * *********************************************************** */
 
    /* UserBase & UserAuth functions should be called directly if you want to 
       retain the user object being used. E.g.
       
       $user = new UserAuth();
       $user->getUserBasic($hotaru);
       $user->updateUserBasic($hotaru);
    */
    
    
    /**
     * check cookie and log in
     *
     * @return bool
     */
    public function checkCookie()
    {
        $this->currentUser->checkCookie($this);
    }
    
    
    /**
     * Get basic user details
     *
     * @param int $userid 
     * @param string $username
     * @param bool $no_cache - set true to disable caching of SQl results
     * @return array|false
     *
     * Note: Needs either userid or username, not both
     */    
    public function getUserBasic($userid = 0, $username = '', $no_cache = false)
    {
        $userbase = new UserBase();
        return $userbase->getUserBasic($this, $userid, $username, $no_cache);
    }
    
    
    /**
     * Default permissions
     *
     * @param string $role or 'all'
     * @param string $field 'site' for site defaults and 'base' for base defaults
     * @param book $options_only returns just the options if true
     * @return array $perms
     */
    public function getDefaultPermissions($role = '', $defaults = 'site', $options_only = false) 
    {
        $userbase = new UserBase();
        return $userbase->getDefaultPermissions($this, $role, $defaults, $options_only);
    }
    
    
    /**
     * Update Default permissions
     *
     * @param array $new_perms from a plugin's install function
     * @param string $defaults - either "site", "base" or "both" 
     */
    public function updateDefaultPermissions($new_perms = array(), $defaults = 'both') 
    {
        $userbase = new UserBase();
        return $userbase->updateDefaultPermissions($this, $new_perms, $defaults);
    }
    
    
    /**
     * Get the default user settings
     *
     * @param string $type either 'site' or 'base' (base for the originals)
     * @return array
     */
    public function getDefaultSettings($type = 'site')
    {
        $userbase = new UserBase();
        return $userbase->getDefaultSettings($this, $type);
    }
    
    
    /**
     * Update the default user settings
     *
     * @param array $settings 
     * @param string $type either 'site' or 'base' (base for the originals)
     * @return array
     */
    public function updateDefaultSettings($settings, $type = 'site')
    {
        $userbase = new UserBase();
        return $userbase->updateDefaultSettings($this, $settings, $type);
    }


 /* *************************************************************
 *
 *  USERINFO FUNCTIONS
 *
 * *********************************************************** */
    
    
     /**
     * Checks if the user has an 'admin' role
     *
     * @return bool
     */
    public function isAdmin($username)
    {
        require_once(LIBS . 'UserInfo.php');
        $userInfo = new UserInfo();
        return $userInfo->isAdmin($this->db, $username);
    }
    
    
    /**
     * Check if a user exists
     *
     * @param int $userid 
     * @param string $username
     * @return int
     *
     * Notes: Returns 'no' if a user doesn't exist, else field under which found
     */
    public function userExists($id = 0, $username = '', $email = '')
    {
        require_once(LIBS . 'UserInfo.php');
        $userInfo = new UserInfo();
        return $userInfo->userExists($this->db, $id, $username, $email);
    }
    
    
    /**
     * Get all users with permission to (access admin)
     *
     * @param string $permission
     * @param string $value - value for the permission, usually yes, no, own or mod
     * @return array
     */
    public function getMods($permission = 'can_access_admin', $value = 'yes')
    {
        require_once(LIBS . 'UserInfo.php');
        $userInfo = new UserInfo();
        return $userInfo->getMods($this, $permission, $value);
    }

    
 /* *************************************************************
 *
 *  PLUGIN FUNCTIONS
 *
 * *********************************************************** */
 
 
    /**
     * Look for and run actions at a given plugin hook
     *
     * @param string $hook name of the plugin hook
     * @param bool $perform false to check existence, true to actually run
     * @param string $folder name of plugin folder
     * @param array $parameters mixed values passed from plugin hook
     * @return array | bool
     */
    public function pluginHook($hook = '', $folder = '', $parameters = array(), $exclude = array())
    {
        $pluginFunctions = new PluginFunctions();
        return $pluginFunctions->pluginHook($this, $hook, $folder, $parameters, $exclude);
    }
    
    
    /**
     * Get a single plugin's details for Hotaru
     *
     * @param string $folder - plugin folder name, else $hotaru->plugin->folder is used
     */
    public function readPlugin($folder = '')
    {
        $pluginFunctions = new PluginFunctions();
        $this->readPlugin = $pluginFunctions->readPlugin($this, $folder);
    }
    
    
    /**
     * Get number of active plugins
     *
     * @return int|false
     */
    public function numActivePlugins()
    {
        $pluginFunctions = new PluginFunctions();
        return $pluginFunctions->numActivePlugins($this->db);
    }
    
    
    /**
     * Get version number of plugin if active
     *
     * @param string $folder plugin folder name
     * @return string|false
     */
    public function getPluginVersion($folder = '')
    {
        $this->readPlugin($folder);
        return $this->plugin->version;
    }
    
    
    /**
     * Get a plugin's actual name from its folder name
     *
     * @param string $folder plugin folder name
     * @return string
     */
    public function getPluginName($folder = '')
    {
        $this->readPlugin($folder);
        return $this->plugin->name;
    }
    

    /**
     * Get a plugin's folder from its class name
     *
     * @param string $class plugin class name
     * @return string|false
     */
    public function getPluginFolderFromClass($class = '')
    {
        $pluginFunctions = new PluginFunctions();
        $this->plugin->folder = $pluginFunctions->getPluginFolderFromClass($this, $class);
    }
    
    
    /**
     * Get a plugin's class from its folder name
     *
     * @param string $folder plugin folder name
     * @return string|false
     */
    public function getPluginClass($folder = '')
    {
        $this->readPlugin($folder);
        return $this->plugin->class;
    }
    

    /**
     * Determines if a plugin is enabled or not
     *
     * @param string $folder plugin folder name
     * @return bool
     */
    public function isActive($folder = '')
    {
        $pluginFunctions = new PluginFunctions();
        return $pluginFunctions->isActive($this, $folder);
    }
    
 
 /* *************************************************************
 *
 *  PLUGIN SETTINGS FUNCTIONS
 *
 * *********************************************************** */
 
 
    /**
     * Get the value for a given plugin and setting
     *
     * @param string $folder name of plugin folder
     * @param string $setting name of the setting to retrieve
     * @return string|false
     *
     * Notes: If there are multiple settings with the same name,
     * this will only get the first.
     */
    public function getSetting($setting = '', $folder = '')
    {
        $pluginSettings = new PluginSettings();
        return $pluginSettings->getSetting($this, $setting, $folder);
    }
    
    
    /**
     * Get an array of settings for a given plugin
     *
     * @param string $folder name of plugin folder
     * @return array|false
     *
     * Note: Unlike "getSetting", this will get ALL settings with the same name.
     */
    public function getSettingsArray($folder = '')
    {
        $pluginSettings = new PluginSettings();
        return $pluginSettings->getSettingsArray($this, $folder);
    }
    
    
    /**
     * Get and unserialize serialized settings
     *
     * @param string $folder plugin folder name
     * @param string $settings_name optional settings name if different from folder
     * @return array - of submit settings
     */
    public function getSerializedSettings($settings_name = '', $folder = '')
    {
        $pluginSettings = new PluginSettings();
        return $pluginSettings->getSerializedSettings($this, $settings_name, $folder);
    }
    
    
    /**
     * Get and store all plugin settings in $hotaru->pluginSettings
     *
     * @return array - all settings
     */
    public function getAllPluginSettings()
    {
        $pluginSettings = new PluginSettings();
        $this->pluginSettings = $pluginSettings->getAllPluginSettings($this);
        return $this->pluginSettings;
    }
    
    
    /**
     * Determine if a plugin setting already exists
     *
     * @param string $folder name of plugin folder
     * @param string $setting name of the setting to retrieve
     * @return string|false
     */
    public function isSetting($setting = '', $folder = '')
    {
        $pluginSettings = new PluginSettings();
        return $pluginSettings->isSetting($this, $setting, $folder);
    }


    /**
     * Update a plugin setting
     *
     * @param string $folder name of plugin folder
     * @param string $setting name of the setting
     * @param string $setting stting value
     */
    public function updateSetting($setting = '', $value = '', $folder = '')
    {
        $pluginSettings = new PluginSettings();
        return $pluginSettings->updateSetting($this, $setting, $value, $folder);
    }


 /* *************************************************************
 *
 *  INCLUDE CSS & JAVASCRIPT FUNCTIONS
 *
 * *********************************************************** */
 

    /**
     * Check if we need to combine CSS and JavaScript files (from start function )
     */
     public function checkCssJs()
     {
        if (!$this->cage->get->keyExists('combine')) { return false; }

        $type = $this->cage->get->testAlpha('type');
        $version = $this->cage->get->testInt('version');
        $this->includes->combineIncludes($this, $type, $version);
     }
     
     
    /**
     * Do Includes (called from template header.php)
     */
     public function doIncludes()
     {
        $version_js = $this->includes->combineIncludes($this, 'js');
        $version_css = $this->includes->combineIncludes($this, 'css');
        $this->includes->includeCombined($version_js, $version_css, $this->isAdmin);
     }
     
     
    /**
     * Build an array of css files to combine
     *
     * @param $folder - the folder name of the plugin
     * @param $filename - optional css file without an extension
     */
     public function includeCss($folder = '', $filename = '')
     {
        return $this->includes->includeCss($this, $folder, $filename);
     }


    /**
     * Build an array of JavaScript files to combine
     *
     * @param $folder - the folder name of the plugin
     * @param $filename - optional js file without an extension
     */
     public function includeJs($folder = '', $filename = '')
     {
        return $this->includes->includeJs($this, $folder, $filename);
     }
     
     
 /* *************************************************************
 *
 *  MESSAGE FUNCTIONS (success/error messages)
 *
 * *********************************************************** */
 
 
    /**
     * Display a SINGLE success or failure message
     *
     * @param string $msg
     * @param string $msg_type ('green' or 'red')
     */
    public function showMessage($msg = '', $msg_type = 'green')
    {
        require_once(LIBS . 'Messages.php');
        $messages = new Messages();
        $messages->showMessage($this, $msg, $msg_type);
    }
    
    
    /**
     * Displays ALL success or failure messages
     */
    public function showMessages()
    {
        require_once(LIBS . 'Messages.php');
        $messages = new Messages();
        $messages->showMessages($this);
    }
    
    
 /* *************************************************************
 *
 *  ANNOUNCEMENT FUNCTIONS
 *
 * *********************************************************** */
 
 
    /**
     * Displays an announcement at the top of the screen
     */
    public function checkAnnouncements() 
    {
        require_once(LIBS . 'Announcements.php');
        $announce = new Announcements();
        if ($this->isAdmin) {
            return $announce->checkAdminAnnouncements($this);
        } else {
            return $announce->checkAnnouncements($this);
        }
    }
    
    
 /* *************************************************************
 *
 *  DEBUG FUNCTIONS
 *
 * *********************************************************** */
 
 
    /**
     * Shows number of database queries and the time it takes for a page to load
     */
    public function showQueriesAndTime()
    {
        require_once(LIBS . 'Debug.php');
        $debug = new Debug();
        $debug->showQueriesAndTime($this);
    }
    
    
     
    
 /* *************************************************************
 *
 *  RSS FEED FUNCTIONS
 *
 * *********************************************************** */
 
 
    /**
     * Includes the SimplePie RSS file and sets the cache
     *
     * @param string $feed
     * @param bool $cache
     * @param int $cache_duration
     *
     * @return object|false $sp
     */
    public function newSimplePie($feed='', $cache=RSS_CACHE_ON, $cache_duration=RSS_CACHE_DURATION)
    {
        require_once(LIBS . 'Feeds.php');
        $feeds = new Feeds();
        return $feeds->newSimplePie($feed, $cache, $cache_duration);
    }
    
    
     /**
     * Display Hotaru forums feed on Admin front page
     *
     * @param int $max_items
     * @param int $items_with_content
     * @param int $max_chars
     */
    public function adminNews($max_items = 10, $items_with_content = 3, $max_chars = 300)
    {
        require_once(LIBS . 'Feeds.php');
        $feeds = new Feeds();
        $feeds->adminNews($this->lang, $max_items, $items_with_content, $max_chars);
    }
    
    
 /* *************************************************************
 *
 *  ADMIN FUNCTIONS
 *
 * *********************************************************** */
 
 
     /**
     * Admin Pages
     */
    public function adminPages($page = 'admin_login')
    {
        require_once(LIBS . 'AdminPages.php');
        $admin = new AdminPages();
        $admin->pages($this, $page);
    }
    
    
     /**
     * Admin login/logout
     *
     * @param string $action
     */
    public function adminLoginLogout($action = 'logout')
    {
        require_once(LIBS . 'AdminAuth.php');
        $admin = new AdminAuth();
        return ($action == 'login') ? $admin->adminLogin($this) : $admin->adminLogout($this);
    }
    
    
     /**
     * Admin login form
     */
    public function adminLoginForm()
    {
        require_once(LIBS . 'AdminAuth.php');
        $admin = new AdminAuth();
        $admin->adminLoginForm($this);
    }
    
    
 /* *************************************************************
 *
 *  MAINTENANCE FUNCTIONS
 *
 * *********************************************************** */
 
 
    /**
     * Check if site is open or closed. Exit if closed
     *
     * @param object $hotaru
     */
    public function checkAccess()
    {
        if (SITE_OPEN == 'true') { return true; }   // site is open, go back and continue
        
        // site closed, but user has admin access so go back and continue as normal
        if ($this->currentUser->getPermission('can_access_admin') == 'yes') { return true; }
        
        require_once(LIBS . 'Maintenance.php');
        $maintenance = new Maintenance();
        return $maintenance->siteClosed($this->lang); // displays "Site Closed for Maintenance"
    }
    
    
    /**
     * Open or close the site for maintenance
     *
     * @param string $switch - 'open' or 'close'
     */
    public function openCloseSite($switch = 'open')
    {
        require_once(LIBS . 'Maintenance.php');
        $maintenance = new Maintenance();
        $maintenance->openCloseSite($this, $switch);
    }
    
    
    /**
     * Optimize all database tables
     */
    public function optimizeTables()
    {
        require_once(LIBS . 'Maintenance.php');
        $maintenance = new Maintenance();
        $maintenance->optimizeTables($this);
    }
    
    
    /**
     * Empty plugin database table
     *
     * @param string $table_name - table to empty
     * @param string $msg - show "emptied" message or not
     */
    public function emptyTable($table_name, $msg = true)
    {
        require_once(LIBS . 'Maintenance.php');
        $maintenance = new Maintenance();
        $maintenance->emptyTable($this, $table_name, $msg);
    }
    
    
    /**
     * Delete plugin database table
     *
     * @param string $table_name - table to drop
     * @param string $msg - show "dropped" message or not
     */
    public function dropTable($table_name, $msg = true)
    {
        require_once(LIBS . 'Maintenance.php');
        $maintenance = new Maintenance();
        $maintenance->dropTable($this, $table_name, $msg);
    }
    
    
    /**
     * Remove plugin settings
     *
     * @param string $plugin_name - settings to remove
     * @param string $msg - show "removed" message or not
     */
    public function removeSettings($plugin_name, $msg = true)
    {
        require_once(LIBS . 'Maintenance.php');
        $maintenance = new Maintenance();
        $maintenance->removeSettings($this, $plugin_name, $msg);
    }
    
    
    /**
     * Delete all files in the specified directory except placeholder.txt
     *
     * @param string $dir - path to the cache folder
     * @return bool
     */    
    public function deleteFiles($dir)
    {
        require_once(LIBS . 'Maintenance.php');
        $maintenance = new Maintenance();
        $maintenance->deleteFiles($dir);
    }
    
    
    /**
     * Calls the delete_files function, then displays a message.
     *
     * @param string $folder - path to the cache folder
     * @param string $msg - show "cleared" message or not
     */
    public function clearCache($folder, $msg = true)
    {
        require_once(LIBS . 'Maintenance.php');
        $maintenance = new Maintenance();
        $maintenance->clearCache($this, $folder, $msg);
    }
    
    
 /* *************************************************************
 *
 *  CACHING FUNCTIONS (Note: "clearCache" is in Maintenance above)
 *
 * *********************************************************** */


    /**
     * Hotaru CMS Smart Caching
     *
     * This function does one query on the database to get the last updated time for a 
     * specified table. If that time is more recent than the $timeout length (e.g. 10 minutes),
     * the database will be used. If there hasn't been an update, any cached results from the 
     * last 10 minutes will be used.
     *
     * @param string $switch either "on", "off" or "html"
     * @param string $table DB table name
     * @param int $timeout time before DB cache expires
     * @param string $html output as HTML
     * @param string $label optional label to append to filename
     * @return bool
     */
    public function smartCache($switch = 'off', $table = '', $timeout = 0, $html = '', $label = '')
    {
        require_once(LIBS . 'Caching.php');
        $caching = new Caching();
        $caching->smartCache($hotaru, $switch, $table, $timeout, $html, $label);
    }
    
    
 /* *************************************************************
 *
 *  BLOCKED FUNCTIONS (i.e. Admin's Blocked list)
 *
 * *********************************************************** */
 
     /**
     * Check if a value is blocked from registration and post submission)
     *
     * @param string $type - i.e. ip, url, email, user
     * @param string $value
     * @param bool $like - used for LIKE sql if true
     * @return bool
     */
    public function isBlocked($type = '', $value = '', $operator = '=')
    {
        require_once(LIBS . 'Blocked.php');
        $blocked = new Blocked();
        return $blocked->isBlocked($this->db, $type, $value, $operator);
    }
    
    
 /* *************************************************************
 *
 *  LANGUAGE FUNCTIONS
 *
 * *********************************************************** */


    /**
     * Include a language file in a plugin
     *
     * @param string $folder name of plugin folder
     * @param string $filename optional filename without file extension
     *
     * Note: the language file should be in a plugin folder named 'languages'.
     * '_language.php' is appended automatically to the folder of file name.
     */    
    public function includeLanguage($folder = '', $filename = '')
    {
        require_once(LIBS . 'Language.php');
        $language = new Language();
        $language->includeLanguage($this, $folder, $filename);
    }
    
    
/* *************************************************************
 *
 *  CSRF FUNCTIONS
 *
 * *********************************************************** */
 
 
    /**
     * Shortcut for CSRF functions
     *
     * @param string $type - either "set" or "check" CSRF key
     * @param string $script - optional name of page using the key
     * @param int $life - minutes before the token expires
     * @return string $key (if using $type "fetch")
     */
    public function csrf($type = 'check', $script = '', $life = 10)
    {
        $csrf = new csrf();
        return $csrf->csrfInit($this, $type, $script, $life);
    }
    
    
/* *************************************************************
 *
 *  POST FUNCTIONS
 *
 * *********************************************************** */
 

    /**
     * Get all the parameters for the current post
     *
     * @param int $post_id - Optional row from the posts table in the database
     * @param array $post_row - a post already fetched from the db, just needs reading
     * @return bool
     */    
    public function readPost($post_id = 0, $post_row = NULL)
    {
        return $this->post->readPost($this, $post_id, $post_row);
    }
    
    
    /**
     * Gets a single post from the database
     *
     * @param int $post_id - post id of the post to get
     * @return array|false
     */    
    public function getPost($post_id = 0)
    {
        return $this->post->getPost($this, $post_id);
    }
    
    
    /**
     * Add a post to the database
     *
     * @return true
     */    
    public function addPost()
    {
        $this->post->addPost($this);
    }
    
    
    /**
     * Update a post in the database
     *
     * @return true
     */    
    public function updatePost()
    {
        $this->post->updatePost($this);
    }
    
    
    /**
     * Physically delete a post from the database 
     *
     * There's a plugin hook in here to delete their parts, e.g. votes, coments, tags, etc.
     */    
    public function deletePost()
    {
        $this->post->deletePost($this);
    }
    
    
    /**
     * Update a post's status
     *
     * @param string $status
     * @param int $post_id (optional)
     * @return true
     */    
    public function changePostStatus($status = "processing", $post_id = 0)
    {
        return $this->post->changePostStatus($this, $status, $post_id);
    }
    
    
    /**
     * Count how many approved posts a user has had
     *
     * @param int $userid (optional)
     * @return int 
     */
    public function postsApproved($userid)
    {
        return $this->post->postsApproved($this, $userid);
    }
    
    
    /**
     * Count posts in the last X hours/minutes for this user
     *
     * @param int $hours
     * @param int $minutes
     * @param int $user_id (optional)
     * @return int 
     */
    public function countPosts($hours = 0, $minutes = 0, $user_id = 0)
    {
        return $this->post->countPosts($hours, $minutes, $user_id);
    }


    /**
     * Checks for existence of a url
     *
     * @return array|false - array of posts
     */    
    public function urlExists($url = '')
    {
        return $this->post->urlExists($this, $url);
    }
    
    
    /**
     * Checks for existence of a title
     *
     * @param str $title
     * @return int - id of post with matching title
     */
    public function titleExists($title = '')
    {
        return $this->post->titleExists($this, $title);
    }
    
    
    /**
     * Prepares and calls functions to send a trackback
     * Uses $hotaru->post->id
     */
    public function sendTrackback()
    {
        require_once(LIBS . 'Trackback.php');
        $trackback = new Trackback();
        return $trackback->sendTrackback($this);
    }
}
?>
