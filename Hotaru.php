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
 * @copyright Copyright (c) 2010, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
class Hotaru
{
	protected $version              = "1.5.0";  // Hotaru CMS version
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
	protected $avatar;                          // Avatar object
	protected $comment;                         // Comment object
	protected $includes;                        // for CSS/JavaScript includes
	protected $debug;                           // Debug object
	protected $email;                           // Email object
	protected $pageHandling;                    // PageHandling object
	
	// page info
	protected $home                 = '';       // name for front page
	protected $pageName             = '';       // e.g. index, category
	protected $pageTitle            = '';       // e.g. Top Stories
	protected $pageType             = '';       // e.g. post, list
	protected $pageTemplate         = '';       // e.g. sb_list, tag_cloud
	protected $subPage              = '';       // e.g. category (if pageName is "index")

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
	public function __construct($start = '')
	{ 
		// define shorthand paths
		if (!defined('BASE')) {
			define("BASE", dirname(__FILE__). '/');
			define("CACHE", dirname(__FILE__).'/cache/');
			define("ADMIN", dirname(__FILE__).'/admin/');
			define("INSTALL", dirname(__FILE__).'/install/');
			define("LIBS", dirname(__FILE__).'/libs/');
			define("EXTENSIONS", dirname(__FILE__).'/libs/extensions/');
			define("FUNCTIONS", dirname(__FILE__).'/functions/');
			define("CONTENT", dirname(__FILE__).'/content/');
			define("THEMES", dirname(__FILE__).'/content/themes/');
			define("PLUGINS", dirname(__FILE__).'/content/plugins/');
			define("ADMIN_THEMES", dirname(__FILE__).'/content/admin_themes/');
			define("SITEURL", BASEURL);
		}

		require_once(EXTENSIONS . 'SmartLoader/smartloader.class.php');

		// initialize Hotaru
		if (!$start) {
			
			$init = new Initialize($this);

			$this->db           = $init->db;            // database object
			$this->cage         = $init->cage;          // Inspekt cage
			$this->isDebug      = $init->isDebug;       // set debug			
			$this->currentUser  = new UserAuth();       // the current user
			$this->plugin       = new Plugin();         // instantiate Plugin object
			$this->post         = new Post();           // instantiate Post object
			$this->includes     = new IncludeCssJs();   // instantiate Includes object
			$this->pageHandling = new PageHandling();   // instantiate PageHandling object
			$this->debug        = new Debug();          // instantiate Debug object
			
			$this->csrf('set');                         // set a csrfToken
			$this->db->setHotaru($this);                // pass $h object to EzSQL for error reporting
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
		
		$this->getPageName();                   // fills $h->pageName
		
		switch ($entrance) {
			case 'admin':
				$this->isAdmin = true;
				$this->lang = $lang->includeLanguagePack($this->lang, 'admin');				
				$admin = new AdminAuth();               // new Admin object
				$this->checkCookie();                   // check cookie reads user details
				$this->checkAccess();                   // site closed if no access permitted
				$page = $admin->adminInit($this);       // initialize Admin & get desired page
				$this->adminPages($page);               // Direct to desired Admin page
				break;
			default:
				$this->isAdmin = false;
				$this->checkCookie();                   // log in user if cookie
				$this->checkAccess();                   // site closed if no access permitted
				if (!$entrance) { return false; }       // stop here if entrance not defined
				$this->displayTemplate('index');        // displays the index page
		}
		
		$lang->writeLanguageCache($this);
		
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
		if ($this->isAdmin) { return false; }
		
		// include a files that match the name of the plugin folder:
		$this->includeJs($this->plugin->folder); // folder name, filename
		$this->includeCss($this->plugin->folder);
	}
    
    
	/**
	 * Include All CSS and JavaScript files for this plugin in Admin
	 */
	public function admin_header_include()
	{
		if (!$this->isAdmin) { return false; }
		
		// include a files that match the name of the plugin folder:
		$this->includeJs($this->plugin->folder); // folder name, filename
		$this->includeCss($this->plugin->folder);
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
		$vars['name'] = $this->plugin->name;
		//$vars['name'] = make_name($this->plugin->folder);
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
		if (($this->cage->get->testAlnumLines('plugin') != $this->plugin->folder)
			&& ($this->cage->post->testAlnumLines('plugin') != $this->plugin->folder)) 
		{ 
			return false; 
		}
		
		if (file_exists(PLUGINS . $this->plugin->folder . '/' . $this->plugin->folder . '_settings.php')) {
		    include_once(PLUGINS . $this->plugin->folder . '/' . $this->plugin->folder . '_settings.php');
		
		    $settings_class = make_name($this->plugin->folder, '_') . 'Settings'; // e.g. CategoriesSettings
		    $settings_class = str_replace(' ', '', $settings_class); // strip spaces
		    $settings_object = new $settings_class();
		    $settings_object->settings($this);   // call the settings function		
		}
		else {
		    echo $this->lang["admin_theme_plugins_filenotfound"] . "<br/><br/>";
		    echo $this->lang["admin_theme_plugins_checkforfile"] . PLUGINS . $this->plugin->folder . '/' . $this->plugin->folder . '_settings.php';
		}
		return true;
	}
    
    
/* *************************************************************
 *
 *  PAGE HANDLING FUNCTIONS
 *
 * *********************************************************** */

	/**
	 * Set the homepage (and set page name)
	 *
	 * @param string $home
	 * @param string $pagename
	 */
	public function setHome($home = '', $pagename = '')
	{
		$this->pageHandling->setHome($this, $home, $pagename);
	}

	/**
	 * Test if the current url is the *true* homepage, i.e. equal to SITEURL
	 *
	 * @return bool
	 */
	public function isHome()
	{
		return $this->pageHandling->isHome($this);
	}
	
	
	/**
	 * Determine the title tags for the header
	 *
	 * @param bool $raw -return the title only
	 * @return string - the title
	 */
	public function getTitle($delimiter = ' &laquo; ', $raw = false)
	{
		return $this->pageHandling->getTitle($this, $delimiter, $raw);
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
		$this->pageHandling->displayTemplate($this, $page, $plugin, $include_once);
	}
    
    
	/**
	 * Checks if current page (in url or form) matches the page parameter
	 *
	 * @param string $page page name
	 */
	public function isPage($page = '')
	{
		return $this->pageHandling->isPage($this, $page);
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
	 *  Usage: $h->isSettingsPage('submit') returns true if 
	 *         page=plugin_settings and plugin=submit in the url.
	 */
	public function isSettingsPage($folder = '')
	{
		return $this->pageHandling->isSettingsPage($this, $folder);
	}

    
	/**
	 * Gets the current page name
	 */
	public function getPageName()
	{
		$this->pageName = $this->pageHandling->getPageName($this);
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
		return $this->pageHandling->friendlyToStandardUrl($this, $friendly_url);
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
		return $this->pageHandling->url($this, $parameters, $head);
	}
    
    
	/**
	 * Pagination with query and row count (better for large sets of data)
	 *
	 * @param string $query - SQL query
	 * @param int $total_items - total row count
	 * @param int $items_per_page
	 * @param string $cache_table - must provide a table, e.g. "posts" for caching to be used
	 * @return object|false - object
	 */
	public function pagination($query, $total_items, $items_per_page = 10, $cache_table = '')
	{
		$paginator = new Paginator();
		return $paginator->pagination($this, $query, $total_items, $items_per_page, $cache_table);
	}
    

	/**
	 * Pagination with full dataset (easier for small sets of data)
	 *
	 * @param array $data - array of results for paginating
	 * @param int $items_per_page
	 * @return object|false - object
	 */
	public function paginationFull($data, $items_per_page = 10)
	{
		$paginator = new Paginator();
		return $paginator->paginationFull($this, $data, $items_per_page);
	}
    
 
	/**
	 * Return page numbers bar
	 *
	 * @param object $paginator - current object of type Paginator
	 * @return string - HTML for page number bar
	 */
	public function pageBar($paginator = NULL)
	{
		return $paginator->pageBar($this);
	}
    

/* *************************************************************
 *
 *  BREADCRUMB FUNCTIONS
 *
 * *********************************************************** */
 
 
	/**
	 * Build breadcrumbs
	 */
	public function breadcrumbs($sep = "&raquo;")
	{
		$breadcrumbs = new Breadcrumbs();
		return $breadcrumbs->buildBreadcrumbs($this, $sep);
	}
    
    
	/**
	 * prepares the RSS link found in breadcrumbs
	 *
	 * @param string $status - post status, e.g. new, top, etc.
	 * @param array $vars - array of key -> value pairs
	 * @return string
	 */    
	public function rssBreadcrumbsLink($status = '', $vars = array())
	{
		$breadcrumbs = new Breadcrumbs();
		return $breadcrumbs->rssBreadcrumbsLink($this, $status, $vars);
	}
    
 
 /* *************************************************************
 *
 *  USERAUTH FUNCTIONS / USERBASE FUNCTIONS
 *
 * *********************************************************** */
 
	/* UserBase & UserAuth functions should be called directly if you want to 
	   retain the user object being used. E.g.
		
		$user = new UserAuth();
		$user->getUserBasic($h);
		$user->updateUserBasic($h);
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
	 * Get full user details (i.e. permissions and settings, too)
	 *
	 * @param int $userid 
	 * @param string $username
	 * @param bool $no_cache - set true to disable caching of SQl results
	 * @return array|false
	 *
	 * Note: Needs either userid or username, not both
	 */
	public function getUser($userid = 0, $username = '', $no_cache = false)
	{
		$userbase = new UserBase();
		return $userbase->getUser($this, $userid, $username, $no_cache);
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
	 * @param bool $remove - false if adding perms, true if deleting them
	 */
	public function updateDefaultPermissions($new_perms = array(), $defaults = 'both', $remove = false) 
	{
		$userbase = new UserBase();
		return $userbase->updateDefaultPermissions($this, $new_perms, $defaults, $remove);
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
	
	
	/**
	 * Get a user's profile or settings data
	 *
	 * @return array|false
	 */
	public function getProfileSettingsData($type = 'user_profile', $userid = 0, $check_exists_only = false)
	{
		$userbase = new UserBase();
		return $userbase->getProfileSettingsData($this, $type, $userid, $check_exists_only);
	}
	
	
	/**
	 * Physically delete a user
	 * Note: You should delete all their posts, comments, etc. first
	 *
	 * @param int $user_id (optional)
	 */
	public function deleteUser($user_id = 0) 
	{
		$userbase = new UserBase();
		return $userbase->deleteUser($this, $user_id);
	}


	/**
	 * Update user_lastvisit field
	 *
	 * @param int $user_id (optional)
	 */
	public function updateUserLastVisit($user_id = 0) 
	{
		return $this->currentUser->updateUserLastVisit($this, $user_id);
	}
	

	/**
	 * Get User Roles - returns an array of role names
	 *
	 * @param string $type 'all', 'default', or 'custom'
	 * @return array|false
	 */
	public function getRoles($type = 'all') 
	{
		return $this->currentUser->getRoles($this, $type);
	}


	/**
	 * Get Unique User Roles - DEPRECATED Hotaru 1.4.1 - Plugins should be updated to use above getRoles() instead
	 *
	 * @return array|false
	 */
	public function getUniqueRoles() 
	{
		return $this->getRoles();
	}

	
 /* *************************************************************
 *
 *  USERINFO FUNCTIONS
 *
 * *********************************************************** */
	
	
	/**
	 * Get the username for a given user id
	 *
	 * @param int $id user id
	 * @return string|false
	 */
	public function getUserNameFromId($id = 0)
	{
		$userInfo = new UserInfo();
		return $userInfo->getUserNameFromId($this, $id);
	}
	
	
	/**
	 * Get the user id for a given username
	 *
	 * @param string $username
	 * @return int|false
	 */
	public function getUserIdFromName($username = '')
	{
		$userInfo = new UserInfo();
		return $userInfo->getUserIdFromName($this, $username);
	}
	
	
	/**
	 * Get the email from user id
	 *
	 * @param int $userid
	 * @return string|false
	 */
	public function getEmailFromId($userid = 0)
	{
		$userInfo = new UserInfo();
		return $userInfo->getEmailFromId($this, $userid);
	}
	
	
	/**
	 * Get the user id from email
	 *
	 * @param string $email
	 * @return string|false
	 */
	public function getUserIdFromEmail($email = '')
	{
		$userInfo = new UserInfo();
		return $userInfo->getUserIdFromEmail($this, $email);
	}
	
	
	 /**
	 * Checks if the user has an 'admin' role
	 *
	 * @return bool
	 */
	public function isAdmin($username)
	{
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
		$userInfo = new UserInfo();
		return $userInfo->userExists($this->db, $id, $username, $email);
	}
	
	
	/**
	 * Check if an username exists in the database (used in forgotten password)
	 *
	 * @param string $username user username
	 * @param string $role user role (optional)
	 * @param int $exclude - exclude a user
	 * @return string|false
	 */
	public function nameExists($username = '', $role = '', $exclude = 0)
	{
		$userInfo = new UserInfo();
		return $userInfo->nameExists($this, $username, $role, $exclude);
	}
	
	
	/**
	 * Check if an email exists in the database (used in forgotten password)
	 *
	 * @param string $email user email
	 * @param string $role user role (optional)
	 * @param int $exclude - exclude a user
	 * @return string|false
	 */
	public function emailExists($email = '', $role = '', $exclude = 0)
	{
		$userInfo = new UserInfo();
		return $userInfo->emailExists($this, $email, $role, $exclude);
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
		$userInfo = new UserInfo();
		return $userInfo->getMods($this, $permission, $value);
	}
	
	
	/**
	 * Get the ids and names of all users or those with a specified role, sorted alphabetically
	 *
	 * @param string $role - optional user role to filter to
	 * @return array
	 */
	public function userIdNameList($role = '')
	{
		$userInfo = new UserInfo();
		return $userInfo->userIdNameList($this, $role);
	}
	
	
	/**
	 * Get full details of all users or batches of users, sorted alphabetically
	 *
	 * @param array $id_array - optional array of user ids
	 * @param int $start - LIMIT $start $range (optional)
	 * @param int $range - LIMIT $start $range (optional)
	 * @return array
	 */
	public function userListFull($id_array = array(), $start = 0, $range = 0)
	{
		$userInfo = new UserInfo();
		return $userInfo->userListFull($this, $id_array, $start, $range);
	}
	
	
	/**
	 * Get settings for all users
	 *
	 * @param int $userid - optional user id 
	 * @return array
	 */
	public function userSettingsList($userid = 0)
	{
		$userInfo = new UserInfo();
		return $userInfo->userSettingsList($this, $userid);
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
	 * @param string $folder - plugin folder name, else $h->plugin->folder is used
	 * @return array - $key array object, e.g. $key->plugin_id
	 */
	public function readPlugin($folder = '')
	{
		$pluginFunctions = new PluginFunctions();
		return $pluginFunctions->readPlugin($this, $folder);
	}
	
	
	/**
	 * Get a single property from a specified plugin
	 *
	 * @param string $property - plugin property, e.g. "plugin_version"
	 * @param string $folder - plugin folder name, else $h->plugin->folder is used
	 * @param string $field - an alternative field to use instead of $folder
	 */
	public function getPluginProperty($property = '', $folder = '', $field = '')
	{
		$pluginFunctions = new PluginFunctions();
		return $pluginFunctions->getPluginProperty($this, $property, $folder, $field);
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
		return $this->getPluginProperty('plugin_version', $folder);
	}
	
	
	/**
	 * Get a plugin's actual name from its folder name
	 *
	 * @param string $folder plugin folder name
	 * @return string
	 */
	public function getPluginName($folder = '')
	{
		return $this->getPluginProperty('plugin_name', $folder);
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
		return $this->getPluginProperty('plugin_class', $folder);
	}
	
	
	/**
	 * Determines if a plugin "type" is enabled, if not, plugin "folder"
	 *
	 * @param string $type plugin type or folder name
	 * @return bool
	 */
	public function isActive($type = '')
	{
		$pluginFunctions = new PluginFunctions();
		//return $pluginFunctions->isActive($this, $type); // dropped in favor of cache:
		$result = $this->getPluginProperty('plugin_enabled', $type, 'type');
		if (!$result) { $result = $this->getPluginProperty('plugin_enabled', $type); }
		return $result;
	}
	
	
	/**
	 * Determines if a specific plugin is installed
	 *
	 * @param string $folder folder name
	 * @return bool
	 */
	public function isInstalled($folder = '')
	{
		$pluginFunctions = new PluginFunctions();
		$result = $this->getPluginProperty('plugin_id', $folder);
		return $result;
	}
	
	
	/**
	 * Determines if a plugin has a settings page or not
	 *
	 * @param object $h
	 * @param string $folder plugin folder name (optional)
	 * @return bool
	 */
	public function hasSettings($folder = '')
	{
		$pluginFunctions = new PluginFunctions();
		return $pluginFunctions->hasSettings($this, $folder);
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
	public function getSerializedSettings($folder = '', $settings_name = '')
	{
		$pluginSettings = new PluginSettings();
		return $pluginSettings->getSerializedSettings($this, $folder, $settings_name);
	}
	
	
	/**
	 * Get and store all plugin settings in $h->pluginSettings
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
 *  THEME SETTINGS FUNCTIONS
 *
 * *********************************************************** */

	/**
	 * Read and return plugin info from top of a plugin file.
	 *
	 * @param string $theme - theme folder
	 * @return array|false
	 */
	public function readThemeMeta($theme = 'default')
	{
		$themeSettings = new ThemeSettings();
		return $themeSettings->readThemeMeta($this, $theme);
	}
	
	
	/**
	 * Get and unserialize serialized settings
	 *
	 * @param string $theme theme folder name
	 * @param string $return 'value' or 'default'
	 * @return array - of theme settings
	 */
	public function getThemeSettings($theme = '', $return = 'value')
	{
		$themeSettings = new ThemeSettings();
		return $themeSettings->getThemeSettings($this, $theme, $return);
	}
	
	
	/**
	 * Update theme settings
	 *
	 * @param array $settings array of settings
	 * @param string $theme theme folder name
	 * @param string $column 'value', 'default' or 'both'
	
	 */
	public function updateThemeSettings($settings = array(), $theme = '', $column = 'value')
	{
		$themeSettings = new ThemeSettings();
		return $themeSettings->updateThemeSettings($this, $settings, $theme, $column);
	}


 /* *************************************************************
 *
 *  INCLUDE CSS & JAVASCRIPT FUNCTIONS
 *
 * *********************************************************** */
 

	/**
	 * Do Includes (called from template header.php)
	 */
	 public function doIncludes()
	 {
		$version_js = $this->includes->combineIncludes($this, 'js');
		$version_css = $this->includes->combineIncludes($this, 'css');
		$this->includes->includeCombined($this, $version_js, $version_css, $this->isAdmin);
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
	 
	 
	/**
	 * Include individual CSS files, not merged into the CSS archive
	 *
	 * @param $files- array of files to include (no extensions)
	 * @param $folder - optional pluin folder
	 */
	 public function includeOnceCss($files = array(), $folder = '')
	 {
		return $this->includes->includeOnceCss($this, $files, $folder);
	 }
	 
	 
	/**
	 * Include individual JavaScript files, not merged into the JavaScript archive
	 *
	 * @param $files- array of files to include (no extensions)
	 * @param $folder - optional pluin folder
	 */
	 public function includeOnceJs($files = array(), $folder = '')
	 {
		return $this->includes->includeOnceJs($this, $files, $folder);
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
		$messages = new Messages();
		$messages->showMessage($this, $msg, $msg_type);
	}
	
	
	/**
	 * Displays ALL success or failure messages
	 */
	public function showMessages()
	{
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
	 *
	 * @param string $announcement - optional for non-admin pages
	 * @return array
	 */
	public function checkAnnouncements($announcement = '') 
	{
		$announce = new Announcements();
		if ($this->isAdmin) {
			return $announce->checkAdminAnnouncements($this);
		} else {
			return $announce->checkAnnouncements($this, $announcement);
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
		$this->debug->showQueriesAndTime($this);
	}
	
	/**
	 * Open file for logging
	 *
	 * @param string $type "speed", "error", etc.
	 * @param string $mode e.g. 'a' or 'w'. 
	 * @link http://php.net/manual/en/function.fopen.php
	 */
	public function openLog($type = 'debug', $mode = 'a+')
	{
		$this->debug->openLog($type, $mode);
	}
	
	
	/**
	 * Log performance and errors
	 *
	 * @param string $type "speed", "error", etc.
	 */
	public function writeLog($type = 'error', $string = '')
	{
		$this->debug->writeLog($type, $string);
	}
	
	
	/**
	 * Close log file
	 *
	 * @param string $type "speed", "error", etc.
	 */
	public function closeLog($type = 'error')
	{
		$this->debug->closeLog($type);
	}
	
	
	/**
	 * Generate a system report
	 *
	 * @param string $type "log" or "object"
	 */
	public function generateReport($type = 'log')
	{
		if (!is_object($this->debug)) { 
			$this->debug = new Debug();
		}
		return $this->debug->generateReport($this, $type);
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
	public function newSimplePie($feed='', $cache=RSS_CACHE, $cache_duration=RSS_CACHE_DURATION)
	{
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
		$feeds = new Feeds();
		$feeds->adminNews($this->lang, $max_items, $items_with_content, $max_chars);
	}


	 /**
	 * Create an RSS Feed
	 *
	 * @param string $title - feed title
	 * @param string $link - url feed title should point to
	 * @param string $description - feed description
	 * @param array $items - $items[0] = array('title'=>TITLE, 'link'=>URL, 'date'=>TIMESTAMP, 'description'=>DESCRIPTION)
	 */
	public function rss($title = '', $link = '', $description = '', $items = array())
	{
		$feeds = new Feeds();
		$feeds->rss($this, $title, $link, $description, $items);
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
		$admin = new AdminAuth();
		return ($action == 'login') ? $admin->adminLogin($this) : $admin->adminLogout($this);
	}
	
	
	 /**
	 * Admin login form
	 */
	public function adminLoginForm()
	{
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
	 * @param object $h
	 */
	public function checkAccess()
	{
		if (SITE_OPEN == 'true') { return true; }   // site is open, go back and continue
		
		// site closed, but user has admin access so go back and continue as normal
		if ($this->currentUser->getPermission('can_access_admin') == 'yes') { return true; }
		
		if ($this->pageName == 'admin_login') { return true; }
		
		$maintenance = new Maintenance();
		return $maintenance->siteClosed($this, $this->lang); // displays "Site Closed for Maintenance"
	}
	
	
	/**
	 * Open or close the site for maintenance
	 *
	 * @param string $switch - 'open' or 'close'
	 */
	public function openCloseSite($switch = 'open')
	{
		$maintenance = new Maintenance();
		$maintenance->openCloseSite($this, $switch);
	}
	
	
	/**
	 * Optimize all database tables
	 */
	public function optimizeTables()
	{
		$maintenance = new Maintenance();
		$maintenance->optimizeTables($this);
	}
	
	
	/**
	 * Empty plugin database table
	 *
	 * @param string $table_name - table to empty
	 * @param string $msg - show "emptied" message or not
	 */
	public function emptyTable($table_name = '', $msg = true)
	{
		$maintenance = new Maintenance();
		$maintenance->emptyTable($this, $table_name, $msg);
	}
	
	
	/**
	 * Delete plugin database table
	 *
	 * @param string $table_name - table to drop
	 * @param string $msg - show "dropped" message or not
	 */
	public function dropTable($table_name = '', $msg = true)
	{
		$maintenance = new Maintenance();
		$maintenance->dropTable($this, $table_name, $msg);
	}
	
	
	/**
	 * Remove plugin settings
	 *
	 * @param string $folder - plugin folder name
	 * @param bool $msg - show "Removed" message or not
	 */
	public function removeSettings($folder = '', $msg = true)
	{
		$maintenance = new Maintenance();
		$maintenance->removeSettings($this, $folder, $msg);
	}
	
	
	/**
	 * Deletes rows from pluginsettings that match a given setting or plugin
	 *
	 * @param string $setting name of the setting to remove
	 * @param string $folder name of plugin folder
	 */
	public function deleteSettings($setting = '', $folder = '')
	{
		$maintenance = new Maintenance();
		$maintenance->deleteSettings($this, $setting, $folder);
	}
	
	
	/**
	 * Delete all files in the specified directory except placeholder.txt
	 *
	 * @param string $dir - path to the cache folder
	 * @return bool
	 */    
	public function deleteFiles($dir = '')
	{
		$maintenance = new Maintenance();
		return $maintenance->deleteFiles($dir);
	}
	
	
	/**
	 * Calls the delete_files function, then displays a message.
	 *
	 * @param string $folder - path to the cache folder
	 * @param string $msg - show "cleared" message or not
	 */
	public function clearCache($folder = '', $msg = true)
	{
		$maintenance = new Maintenance();
		return $maintenance->clearCache($this, $folder, $msg);
	}
	
	
	/**
	 * Get all files in the specified directory except placeholder.txt
	 *
	 * @param string $dir - path to the folder
	 * @param array $exclude - array of file/folder names to exclude
	 * @return array
	 */    
	public function getFiles($dir = '', $exclude = array())
	{
		$maintenance = new Maintenance();
		return $maintenance->getFiles($dir, $exclude);
	}
	
	
	/** 
	 * System Report is under Debug Functions
	 */
    
    
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
	 * @param string $html_sql output as HTML, or an SQL query
	 * @param string $label optional label to append to filename
	 * @return bool
	 */
	public function smartCache($switch = 'off', $table = '', $timeout = 0, $html_sql = '', $label = '')
	{
		$caching = new Caching();
		return $caching->smartCache($this, $switch, $table, $timeout, $html_sql, $label);
	}
	
	
	/**
	 * Cache HTML without checking for database updates
	 *
	 * This function caches blocks of HTML code
	 *
	 * @param int $timeout timeout in minutes before cache file is deleted
	 * @param string $html block of HTML to cache
	 * @param string $label name to identify the cached file
	 * @return bool
	 */
	public function cacheHTML($timeout = 0, $html = '', $label = '')
	{
		$caching = new Caching();
		return $caching->cacheHTML($this, $timeout, $html, $label);
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
		$blocked = new Blocked();
		return $blocked->isBlocked($this->db, $type, $value, $operator);
	}
	
	
	 /**
	 * Add or update blocked items 
	 *
	 * @param string $type - e.g. url, email, ip
	 * @param string $value - item to block
	 * @param bool $msg - show a success/failure message on Maintenance page
	 * @return bool
	 */
	public function addToBlockedList($type = '', $value = 0, $msg = false)
	{
		$blocked = new Blocked();
		return $blocked->addToBlockedList($this, $type, $value, $msg);
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
		$language = new Language();
		$language->includeLanguage($this, $folder, $filename);
	}
    
    
	/**
	 * Include a language file for a theme
	 *
	 * @param string $filename optional filename without '_language.php' file extension
	 *
	 * Note: the language file should be in a plugin folder named 'languages'.
	 * '_language.php' is appended automatically to the folder of file name.
	 */    
	public function includeThemeLanguage($filename = 'main')
	{
		$language = new Language();
		$language->includeThemeLanguage($this, $filename);
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
	public function csrf($type = 'check', $script = '', $life = 60)
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
	 * @return int $last_insert_id
	 */
	public function addPost()
	{
		return $this->post->addPost($this);
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
	 * Physically delete all posts by a specified user
	 *
	 * @param array $user_id
	 * @return bool
	 */
	public function deletePosts($user_id = 0) 
	{
		return $this->post->deletePosts($this, $user_id);
	}
	
	
	/**
	 * Delete posts with "processing" status that are older than 30 minutes
	 * This is called automatically when a new post is submitted
	 */
	public function deleteProcessingPosts()
	{
		$this->post->deleteProcessingPosts($this);
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
	 * @param int $post_type (optional)
	 * @return int 
	 */
	public function postsApproved($userid = 0, $post_type = 'news')
	{
		return $this->post->postsApproved($this, $userid, $post_type);
	}
	
	
	/**
	 * Count posts in the last X hours/minutes for this user
	 *
	 * @param int $hours
	 * @param int $minutes
	 * @param int $user_id (optional)
	 * @param int $post_type (optional)
	 * @return int 
	 */
	public function countPosts($hours = 0, $minutes = 0, $user_id = 0, $post_type = 'news')
	{
		return $this->post->countPosts($this, $hours, $minutes, $user_id, $post_type);
	}
	
	
	/**
	 * Checks for existence of a url
	 *
	 * @return array|false - array containing existing post
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
	 * Checks for existence of a post with given post_url
	 *
	 * @param str $post_url (slug)
	 * @return int - id of post with matching url
	 */
	public function isPostUrl($post_url = '')
	{
		return $this->post->isPostUrl($this, $post_url);
	}
	
	
	/**
	 * Get Unique Post Statuses
	 *
	 * @return array|false
	 */
	public function getUniqueStatuses() 
	{
		return $this->post->getUniqueStatuses($this);
	}
	
	
	/**
	 * Prepares and calls functions to send a trackback
	 * Uses $h->post->id
	 */
	public function sendTrackback()
	{
		$trackback = new Trackback();
		return $trackback->sendTrackback($this);
	}
    
    
/* *************************************************************
 *
 *  AVATAR FUNCTIONS
 *
 * *********************************************************** */
 

	/**
	 * setAvatar
	 *
	 * @param $user_id
	 * @param $size avatar size in pixels
	 * @param $rating avatar rating (g, pg, r or x in Gravatar)
	 * @return bool
	 */
	public function setAvatar($user_id = 0, $size = 32, $rating = 'g', $img_class = '')
	{
		return $this->avatar = new Avatar($this, $user_id, $size, $rating, $img_class);
	}
	
	
	/**
	 * get the plain avatar with no surrounding HTML div
	 *
	 * @return return the avatar
	 */
	public function getAvatar()
	{
		return $this->avatar->getAvatar($this);
	}
	
	
	/**
	 * option to display the avatar linked to ther user's profile
	 *
	 * @return return the avatar
	 */
	public function linkAvatar()
	{
		return $this->avatar->linkAvatar($this);
	}
	
	
	/**
	 * option to display the profile-linked avatar wrapped in a div
	 *
	 * @return return the avatar
	 */
	public function wrapAvatar()
	{
		return $this->avatar->wrapAvatar($this);
	}
    
    
/* *************************************************************
 *
 *  CATEGORY FUNCTIONS
 *
 * *********************************************************** */

	/**
	 * Returns the category id for a given category safe name.
	 *
	 * @param string $cat_name
	 * @return int
	 */
	public function getCatId($cat_safe_name = '')
	{
		$category = new Category();
		return $category->getCatId($this, $cat_safe_name);
	}
	
	
	/**
	 * Returns the category name for a given category id or safe name.
	 *
	 * @param int $cat_id
	 * @param string $cat_safe_name
	 * @return string
	 */
	public function getCatName($cat_id = 0, $cat_safe_name = '')
	{
		$category = new Category();
		return $category->getCatName($this, $cat_id, $cat_safe_name);
	}
	
	
	/**
	 * Returns the category safe name for a given category id 
	 *
	 * @param int $cat_id
	 * @return string
	 */
	public function getCatSafeName($cat_id = 0)
	{
		$category = new Category();
		return $category->getCatSafeName($this, $cat_id);
	}
	
	
	/**
	 * Returns parent id
	 *
	 * @param int $cat_id
	 * @return int
	 */
	public function getCatParent($cat_id = 0)
	{
		$category = new Category();
		return $category->getCatParent($this, $cat_id);
	}
	
	
	/**
	 * Returns child ids
	 *
	 * @param int $cat_parent_id
	 * @return int
	 */
	public function getCatChildren($cat_parent_id = 0)
	{
		$category = new Category();
		return $category->getCatChildren($this, $cat_parent_id);
	}
	
	 /**
	 * Returns Category list ids
	 *
	 * @param array $args
	 * @return int
	 */
	public function getCategories($args = array())
	{
		$category = new Category();
		return $category->getCategories($this, $args);
	}
	
	
	/**
	 * Returns meta description and keywords for the category (if available)
	 *
	 * @param int $cat_id
	 * @return array|false
	 */
	public function getCatMeta($cat_id = 0)
	{
		$category = new Category();
		return $category->getCatMeta($this, $cat_id);
	}


	/**
	 * Add a new category
	 *
	 * @param int $parent
	 * @param string $new_cat_name
	 * @return bool
	 */
	public function addCategory($parent = 0, $new_cat_name = '')
	{
		$category = new Category();
		return $category->addCategory($this, $parent, $new_cat_name);
	}


	/**
	 * rebuild the category tree
	 *
	 * @param int $parent_id
	 * @param int $left
	 * @return int
	 */
	public function rebuildTree($parent_id = 0, $left = 0)
	{
		$category = new Category();
		return $category->rebuildTree($this, $parent_id, $left);
	}


	/**
	 * Delete a category
	 *
	 * @param int $delete_category
	 * @return bool
	 */
	function deleteCategory($delete_category = 0)
	{
		$category = new Category();
		return $category->deleteCategory($this, $delete_category);
	}


/* *************************************************************
 *
 *  COMMENT FUNCTIONS
 *
 * *********************************************************** */

	/**
	 * Count comments
	 *
	 * @param bool $digits_only - return just the count (if false, returns "3 comments", etc.)
	 * @param string $no_comments_text - e.g. "Leave a comment" or "No comments"
	 * @return string - text to show, e.g. "3 comments"
	 */
	function countComments($digits_only = true, $no_comments_text = '')
	{
		$comment = new Comment();
		return $comment->countComments($this, $digits_only, $no_comments_text);
	}
	
	
	/**
	 * Count all user comments
	 *
	 * @param int $user_id
	 * @return int
	 */
	function countUserComments($user_id = 0)
	{
		$comment = new Comment();
		return $comment->countUserComments($this, $user_id);
	}
	
	
	/**
	 * Physically delete all comments by a specified user (and responses)
	 *
	 * @param array $user_id
	 * @return bool
	 */
	public function deleteComments($user_id) 
	{
		$comment = new Comment();
		return $comment->deleteComments($this, $user_id);
	}
	
	
	/**
	 * Get comment from database
	 *
	 * @param int $comment_id
	 * @return array|false
	 */
	function getComment($comment_id = 0)
	{
		$comment = new Comment();
		return $comment->getComment($this, $comment_id);
	}
	
	
	/**
	 * Read comment
	 *
	 * @param array $comment_row pulled from database
	 */
	function readComment($comment_row = array())
	{
		$comment = new Comment();
		return $comment->readComment($this, $comment_row);
	}
    
    
/* *************************************************************
 *
 *  WIDGET FUNCTIONS
 *
 * *********************************************************** */

	/**
	 * Add widget
	 *
	 * @param string $plugin
	 * @param string $function
	 * @param string $value
	 */
	public function addWidget($plugin = '', $function = '', $args = '')
	{
		$widget = new Widget();
		$widget->addWidget($this, $plugin, $function, $args);
	}
	

	/**
	 * Get widgets from widgets_settings array
	 *
	 * USAGE: foreach ($widgets as $widget=>$details) 
	 * { echo "Name: " . $widget; echo $details['order']; echo $details['args']; } 
	 * 
	 * @param $widget_name - optional for a single widget
	 * @return array - of widgets
	 */
	public function getArrayWidgets($widget_name = '')
	{
		$widget = new Widget();
		return $widget->getArrayWidgets($this, $widget_name);
	}
	
	
	/**
	 * Delete a widget from the widget db table
	 *
	 * @param string $function
	 * @param string $plugin - plugin folder (optional: used for double checking the plugin is uninstalled)
	 */
	public function deleteWidget($function = '', $plugin = '')
	{
		$widget = new Widget();
		$widget->deleteWidget($this, $function, $plugin);
	}
	
	
	/**
	 * Get plugin name from widget function name
	 *
	 * @return string
	 */
	public function getPluginFromFunction($function)
	{
		$widget = new Widget();
		return $widget->getPluginFromFunction($this, $function);
	}
    
    
/* *************************************************************
 *
 *  EMAIL FUNCTIONS
 *
 * *********************************************************** */
 
	/**
	 * Send emails
	 *
	 * @param string $to - defaults to SITE_EMAIL
	 * @param string $subject - defaults to "No Subject";
	 * @param string $body - returns false if empty
	 * @param string $headers default is "From: " . SITE_EMAIL . "\r\nReply-To: " . SITE_EMAIL . "\r\nX-Priority: 3\r\n";
	 * @param string $type - default is "email", but you can write to a "log" file, print to "screen" or "return" an array of the content
	 * @return array|false - only if $type = "return"
	 */
	public function email($to = '', $subject = '', $body = '', $headers = '', $type = 'email')
	{
		if (!is_object($this->email)) { 
			$this->email = new EmailFunctions();
		}
		
		$this->email->to = $to;
		$this->email->subject = $subject;
		$this->email->body = $body;
		$this->email->headers = $headers;
		$this->email->type = $type;
		
		return $this->email->doEmail();
	}
	
	
/* *************************************************************
 *
 *  FRIEND FUNCTIONS
 *
 * *********************************************************** */

	/**
	 * count followers
	 *
	 * @param int $user_id - get people following this user
	 * @return int
	 */
	public function countFollowers($user_id = 0)
	{
		$friends = new Friends();
		return $friends->countFriends($this, $user_id, 'follower');
	}
	
	
	/**
	 * count following
	 *
	 * @param int $user_id - get people following this user
	 * @return int
	 */
	public function countFollowing($user_id = 0)
	{
		$friends = new Friends();
		return $friends->countFriends($this, $user_id, 'following');
	}
	
	
	/**
	 * get followers
	 *
	 * @param int $user_id - get this user's followers
	 * @param string $return - return 'array' of users of prepared 'query'
	 * @return array|string
	 */
	public function getFollowers($user_id = 0, $return = 'array')
	{
		$friends = new Friends();
		return $friends->getFriends($this, $user_id, 'follower', $return);
	}
	
	
	/**
	 * get people this user is following
	 *
	 * @param int $user_id
	 * @param string $return - return 'array' of users of prepared 'query'
	 * @return array|string
	 */
	public function getFollowing($user_id = 0, $return = 'array')
	{
		$friends = new Friends();
		return $friends->getFriends($this, $user_id, 'following', $return);
	}
	
	
	/**
	 * Is current user being followed by user X?
	 *
	 * @param int $user_id - user X
	 * @return bool
	 */
	public function isFollower($user_id = 0)
	{
		$friends = new Friends();
		return $friends->checkFriends($this, $user_id, 'follower');
	}
	
	
	/**
	 * Is current user following user X?
	 *
	 * @param int $user_id - user X
	 * @return bool
	 */
	public function isFollowing($user_id = 0)
	{
		$friends = new Friends();
		return $friends->checkFriends($this, $user_id, 'following');
	}
	
	
	/**
	 * Follow / become a fan of user X
	 *
	 * @param int $follow - user to follow
	 * @return bool
	 */
	public function follow($user_id = 0)
	{
		$friends = new Friends();
		return $friends->updateFriends($this, $user_id, 'follow');
	}
	
	
	/**
	 * Unfollow / stop being a fan of user X
	 *
	 * @param int $unfollow - user to stop following
	 * @return bool
	 */
	public function unfollow($user_id = 0)
	{
		$friends = new Friends();
		return $friends->updateFriends($this, $user_id, 'unfollow');
	}
	
	
 /* *************************************************************
 *
 *  SITE ACTIVITY FUNCTIONS
 *
 * *********************************************************** */
 
 
	/**
	 * Get the latest site activity
	 *
	 * @param int $limit
	 * @param int $userid
	 * @param string $type blank or "count" or "query"
	 * @return array|false
	 */
	public function getLatestActivity($limit = 0, $userid = 0, $type = '')
	{
		$activity = new UserActivity();
		return $activity->getLatestActivity($this, $limit, $userid, $type);
	}
	
	
	/**
	 * Check if an action already exists
	 *
	 * @param array $args e.g. array('userid'=>4, 'key'=>'post', 'value'=>'6408')
	 * @return bool
	 */
	public function activityExists($args = array())
	{
		$activity = new UserActivity();
		return $activity->activityExists($this, $args);
	}
	
	
	/**
	 * Insert new activity
	 *
	 * @param array $args e.g. array('userid'=>4, 'key'=>'post', 'value'=>'6408')
	 */
	public function insertActivity($args = array())
	{
		$activity = new UserActivity();
		return $activity->insertActivity($this, $args);
	}
	
	
	/**
	 * Update activity
	 *
	 * @param array $args e.g. array('userid'=>4, 'key'=>'post', 'value'=>'6408')
	 */
	public function updateActivity($args = array())
	{
		$activity = new UserActivity();
		return $activity->updateActivity($this, $args);
	}
	
	
	/**
	 * Remove activity
	 *
	 * @param array $args e.g. array('userid'=>4, 'key'=>'post', 'value'=>'6408')
	 */
	public function removeActivity($args = array())
	{
		$activity = new UserActivity();
		return $activity->removeActivity($this, $args);
	}
	
	
 /* *************************************************************
 *
 *  MESSAGING FUNCTIONS
 *
 * *********************************************************** */
 

	/**
	 * Get Messages
	 *
	 * @param string $box "inbox" or "outbox"
	 * @param string $type blank or "count" or "query"
	 * @return int | array | false
	 */
	public function getMessages($box = 'inbox', $type = '')
	{
		$pm = new PrivateMessaging();
		return $pm->getMessages($this, $box, $type);
	}
	
	
	/**
	 * Get Message
	 *
	 * @param int $message_id
	 * @return array
	 */
	public function getMessage($message_id = 0)
	{
		$pm = new PrivateMessaging();
		return $pm->getMessage($this, $message_id);
	}
	 
	 
	/**
	 * Mark message as read
	 *
	 * @param int $message_id
	 */
	public function markRead($message_id = 0)
	{
		$pm = new PrivateMessaging();
		$pm->markRead($this, $message_id);
	}
	
	
	/**
	 * Delete Message
	 *
	 * @param int $message_id
	 * @param string $box "inbox" or "outbox"
	 * @return bool
	 */
	public function deleteMessage($message_id = 0, $box = 'inbox')
	{
		$pm = new PrivateMessaging();
		$pm->deleteMessage($this, $message_id, $box);
	}
	
	
	/**
	 * Send Message
	 *
	 * @param string $to
	 * @param string $from
	 * @param string $subject
	 * @param string $body
	 * @return int | array (int on success, array on failure)
	 */
	public function sendMessage($to = '', $from = '', $subject = '', $body = '')
	{
		$pm = new PrivateMessaging();
		return $pm->sendMessage($this, $to, $from, $subject, $body);
	}


 /* *************************************************************
 *
 *  VOTE FUNCTIONS
 *
 * *********************************************************** */
 

	/**
	 * Get Individual Vote Rating 
	 *
	 * @param int $post_id
	 * @param int $user_id
	 * @param string $ip
	 * @param bool $anon
	 * @return int - vote rating e.g. 10, -10
	 */
	public function getVoteRating($post_id = 0, $user_id = 0, $ip = '', $anon = false)
	{
		return VoteFunctions::getVoteRating($this, $post_id, $user_id, $ip, $anon);
	}


	/**
	 * Get Post Vote Info
	 *
	 * @param int $post_id
	 * @return array|false
	 */
	public function getPostVoteInfo($post_id = 0)
	{
		return VoteFunctions::getPostVoteInfo($this, $post_id);
	}


	/**
	 * Add Vote to PostVotes table
	 *
	 * @param int $post_id
	 * @param int $user_id
	 * @param string $ip
	 * @param int $rating
	 * @param string $type - usually the plugin name
	 */
	public function addVote($post_id = 0, $user_id = 0, $ip = '', $rating = 0, $type = 'vote')
	{
		VoteFunctions::addVote($this, $post_id, $user_id, $ip, $rating, $type);
	}


	/**
	 * Update Post Vote Info
	 *
	 * @param int $post_id
	 * @param int $post_votes_up - either -1, 0 or 1
	 * @param int $post_votes_down - either -1, 0 or 1
	 * @param string $post_status
	 * @param bool $pub_date - set to TRUE to update to current time
	 */
	public function updatePostVoteInfo($post_id = 0, $post_votes_up = 0, $post_votes_down = 0, $post_status = '', $pub_date = FALSE)
	{
		VoteFunctions::updatePostVoteInfo($this, $post_id, $post_votes_up, $post_votes_down, $post_status, $pub_date);
	}


	/**
	 * Delete Vote from PostVotes table
	 *
	 * @param int $post_id
	 * @param int $user_id
	 * @param int $rating
	 * @param string $ip
	 * @param bool $anon
	 */
	public function deleteVote($post_id = 0, $user_id = 0, $rating = 0, $ip = '', $anon = FALSE)
	{
		VoteFunctions::deleteVote($this, $post_id, $user_id, $rating, $ip, $anon);
	}


	/**
	 * Count votes by a user
	 *
	 * @param string $type - 'all', 'pos', 'neg', 'flags' (flags not included in 'all')
	 * @param int $user_id
	 * return int|false
	 */
	public function countUserVotes($type = 'all', $user_id = 0)
	{
		return VoteFunctions::countUserVotes($this, $type, $user_id);
	}
}
?>