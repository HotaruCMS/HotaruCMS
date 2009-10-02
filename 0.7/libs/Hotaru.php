<?php
/**
 * Used for the current environment, e.g. determining the page, etc.
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
    public $db;                         // database object
    public $cage;                       // Inspekt object
    public $plugins;                    // Inspekt object
    public $lang            = array();  // stores language file content
    public $current_user;               // UserBase object
    
    public $message         = '';       // message to display
    public $messageType     = 'green';  // green or red, color of message box
    public $messages        = array();  // for multiple messages
    
    protected $isDebug      = false;    // show db queries and page loading time
    protected $sidebar      = true;     // enable or diable the sidebar
    protected $title        = '';       // for the broswer's TITLE tags
    protected $pageType     = '';       // what kind of page we're looking at
    
    protected $cssIncludes  = array();  // a list of css files to include
    protected $jsIncludes   = array();  // a list of js files to include
    protected $includeType  = '';       // 'css' or 'js'
    
    public $vars            = array();  // multi-purpose
    
    
    /**
     * Build a $hotaru object containing $db and $cage
     */
    public function __construct($entrance = 'main')
    {
        // Initialize
        require_once(LIBS . 'HotaruStart.php');
        $start = new HotaruStart();
        $this->isDebug  = $start->isDebug;
        $this->db       = $start->db;
        $this->cage     = $start->cage;
        
        switch ($entrance) {
            case 'admin':
                $this->includeLanguagePack('admin');
                break;
            case 'install':
                $this->includeLanguagePack('install');
                break;
            case 'no_template':
                return true;
                break;
            default:
                $this->includeLanguagePack('main');
                break;
        }
        
        $this->plugins  = new PluginFunctions('', $this);
        
        $this->checkCookie();   // Log in user if cookie
        $this->hotaruHeader($entrance);  // plugin hook method
    }
    
    
    
    
    /**
     * Set hotaru sidebar status
     *
     * @param bool $bool
     */    
    public function setSidebar($bool)
    {
        $this->sidebar = $bool;
    }
    
    
    /**
     * Get hotaru sidebar status
     *
     * @return bool
     */    
    public function getSidebar()
    {
        return $this->sidebar;
    }
    
    
    /**
     * Set hotaru title
     *
     * @param string $title
     */    
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    
    /**
     * Get hotaru title
     *
     * @return string
     */    
    public function getTitle()
    {
        return $this->title;
    }
    
    
    /**
     * Set page type
     *
     * @param string $type
     */    
    public function setPageType($type)
    {
        $this->pageType = $type;
    }
    
    
    /**
     * Get page type
     *
     * @return string
     */    
    public function getPageType()
    {
        return $this->pageType;
    }
    
    
    /**
     * setCssIncludes
     *
     * @param string $file - full path to the CSS file
     */
    public function setCssIncludes($file)
    {
        array_push($this->cssIncludes, $file);
    }
    

    /**
     * getCssIncludes
     */
    public function getCssIncludes()
    {
        return $this->cssIncludes;
    }
    
    
    /**
     * setJsIncludes
     *
     * @param string $file - full path to the JS file
     */
    public function setJsIncludes($file)
    {
        array_push($this->jsIncludes, $file);
    }
    

    /**
     * getJsIncludes
     */
    public function getJsIncludes()
    {
        return $this->jsIncludes;
    }
    
    
    /**
     * getIncludeType
     */
    public function getIncludeType()
    {
        return $this->includeType;
    }
    
    
    /**
     * check cookie and log in
     *
     * @return bool
     */
    public function checkCookie()
    {
        $this->current_user = new UserBase($this);
        
        // Check for a cookie. If present then the user is logged in.
        $hotaru_user = $this->cage->cookie->testUsername('hotaru_user');
        
        if((!$hotaru_user) || (!$this->cage->cookie->keyExists('hotaru_key'))) { return false; }
        
        $user_info=explode(":", base64_decode($this->cage->cookie->getRaw('hotaru_key')));
        
        if (($hotaru_user != $user_info[0]) || (crypt($user_info[0], 22) != $user_info[1])) { return false; }

        $this->current_user->setName($hotaru_user);
        $this->current_user->getUserBasic(0, $this->current_user->getName());
        $this->current_user->setLoggedIn(true);
        
        return true;
    }
    
    
    /**
     * check cookie and log in
     *
     * @return bool
     */
    public function hotaruHeader($entrance = 'main')
    {

        // Include combined css and js files
        if ($this->cage->get->keyExists('combine')) {
            $type = $this->cage->get->testAlpha('type');
            $version = $this->cage->get->testInt('version');
            $this->combineIncludes($type, $version);
        }
        
        // Enable plugins to define global settings, etc. 
        $results = $this->plugins->pluginHook('hotaru_header');
        
        // NOTE TO SELF - THE FOLLOWING CREATES GLOBALS WHICH ARE TRAPPED IN THIS METHOD
        
        /*  The following extracts the results of pluginHook which is 
            handy for making global objects with plugins */
        if (isset($results) && is_array($results)) 
        {
            foreach ($results as $key => $value) {
                if (is_array($value)) { extract($value); }
            } 
        }
        
        if (!$entrance || $entrance == 'main') {
            $this->displayTemplate('index', $this);
        }
    }

    
    /**
     * Include main or admin language pack
     *
     * @param string $pack
     */
    public function includeLanguagePack($pack = 'main')
    {
        if ($pack == 'install') {
            include_once(INSTALL . 'install_language.php');    // language file for install
        } 
        elseif (file_exists(LANGUAGES . LANGUAGE_PACK . $pack . '_language.php'))
        {
            // language file from the chosen language pack
            include_once(LANGUAGES . LANGUAGE_PACK . $pack . '_language.php');
        }
        else 
        {
           // try the default language pack
            require_once(LANGUAGES . 'language_default/' . $pack . '_language.php'); 
        }
        
        // Add new language to our lang property
        if ($lang) {
            foreach($lang as $l => $text) {
                $this->lang[$l] = $text;
            }
        }
    }
    

    /**
     * Checks if current page (in url) matches the page parameter
     *
     * @param string $page page name
     *
     */
    public function isPage($page = '')
    {
        $real_page = $this->cage->get->testPage('page');
        
        if (!$real_page) { 
            /*  Possibly a post with multi-byte characters? 
                Try getMixedString2... */
            $real_page = $this->cage->get->getMixedString2('page');
        }
        
        // Try POST...
        if (!$real_page) { $real_page = $this->cage->post->testPage('page'); }
        
        if (!$real_page) { $real_page = "main"; }

        $real_page = rtrim($real_page, '/');    // remove trailing slash

        if ($real_page == $page) { return $page; } else { return false; }
    }
    
    
    /**
     * Gets the current page name
     */
    public function getPageName()
    {
        // Try GET...
        $page = $this->cage->get->testPage('page');
        if (!$page) {
            /*  Possibly a post with multi-byte characters? 
                Try getMixedString2... */
            $page = $this->cage->get->getMixedString2('page');
        }
        
        // Try POST...
        if (!$page) { $page = $this->cage->post->testPage('page'); }

        if ($page) {
            $page = rtrim($page, '/');
            return $page;
        } else {
            return 'main';
        }
    }
    
    
    /**
     * Formats page name, e.g.'page_name' into 'Page name'
     *
     * @param string $page page name
     * @param string $delimiter
     */
    public function pageToTitle($page, $delimiter = '_')
    {
        $word_array = array();
        $word_array = explode($delimiter, trim($page));
        $page       = ucfirst(implode(' ', $word_array));
                
        return $page;
    }
    
    
    /**
     * Formats page name, e.g.'page_name' into 'Page Name'
     *
     * @param string $page page name
     * @param string $delimiter
     */
    public function pageToTitleCaps($page, $delimiter = '_')
    {
        $word_array = array();
        $word_array = explode($delimiter, trim($page));
        $word_array = array_map('ucfirst', $word_array);
        $page       = implode(' ', $word_array);
                
        return $page;
    }
    
    
    /**
     * Includes a template to display
     *
     * @param string $page page name
     * @param array $args - usually an array of objects
     * @param string $plugin optional plugin name
     * @param bool $include_once true or false
     */
    public function displayTemplate($page = '', $hotaru, $plugin = '', $include_once = true)
    {
        $page = $page . '.php';

        /* 
            1. Check the custom theme
            2. Check the default theme
            3. Check the plugin folder
            4. Show the 404 Not Found page
        */
        if (file_exists(THEMES . THEME . $page))
        {
            include_once(THEMES . THEME . $page);
        } 
        elseif (file_exists(THEMES . 'default/' . $page))
        {
            include_once(THEMES . 'default/' . $page);
        }
        elseif ($plugin != '' && file_exists(PLUGINS .  $plugin . '/templates/' . $page))
        {
                if (!$include_once) {
                    // Special case, do not restrict to include once.
                    include(PLUGINS . $plugin . '/templates/' . $page);
                } else {
                    include_once(PLUGINS . $plugin . '/templates/' . $page);
                }
                return true;
                die();
        }
        else 
        {
            include_once(THEMES . '404.php');
        }
    }
    
    
    /**
     * Displays an announcement at the top of the screen
     * @return array
     */
    public function checkAnnouncements() 
    {
        $announcements = array();
        
        // 1. "All plugins are currently disabled."
        if (!$this->plugins->numActivePlugins()) {
            array_push(
                $announcements, 
                $this->lang['main_announcement_plugins_disabled']
            );
        }

        // 2. User login and registration currently disabled.
        if (!is_array($announcements)) {
            return false;
        } else {
            return $announcements;
        }
    }
    
    
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
        include_once(EXTENSIONS . "SimplePie/simplepie.inc");
        
        if ($feed != '') {
            $sp = new SimplePie();
            $sp->set_feed_url($feed);
            $sp->set_cache_location(CACHE . "rss_cache/");
            $sp->set_cache_duration($cache_duration);
            if ($cache == "true") { 
                $sp->enable_cache(true);
            } else {
                $sp->enable_cache(false);
            }
            $sp->handle_content_type();
            return $sp;
        } else { 
            return false; 
        }
    }
    
    
    /**
     * Shows number of database queries and the time it takes for a page to load
     */
     
    public function showQueriesAndTime()
    {
        if ($this->isDebug) { 
            echo "<p class='debug'>" . $this->db->num_queries . " " . $this->lang['main_hotaru_queries_time'] . " " . timer_stop(1) . " " . 
            $this->lang['main_hotaru_seconds'] . "</p>"; 
        }
    }
    
    
    /**
     * Display a SINGLE success or failure message
     *
     * @param string $msg
     * @param string $msg_type ('green' or 'red')
     * 
     *  Usage:
     *    Longhand:
     *        $this->hotaru->message = "This is a message";
     *        $this->hotaru->messageType = "green";
     *        $this->hotaru->showMessage();
     *        
     *    Shorthand:
     *        $this->hotaru->showMessage("This is a message", "green");
     */
    public function showMessage($msg = '', $msg_type = 'green')
    {
        if ($msg != '') {
            echo "<div class='message " . $msg_type . "'>" . $msg . "</div>";
        } elseif ($this->message != '') {
            echo "<div class='message " . $this->messageType . "'>" . 
            $this->message . "</div>";
        }
    }
    
    
    /**
     * Displays ALL success or failure messages
     *
     *  Usage:
     *        $this->hotaru->messages['This is a message'] = "green";
     *        $this->hotaru->showMessages();
     */
    public function showMessages()
    {
        if (!empty($this->messages)) {
            foreach ($this->messages as $msg => $msg_type) {
                echo "<div class='message " . $msg_type . "'>" . 
                $msg . "</div>";
            }
        }
    }
    
    
    /**
     * Build an array of css files to combine
     *
     * @param $folder - the folder name of the plugin
     * @param $filename - optional css file without an extension
     */
     public function includeCss($filename = '', $folder = '')
     {
        // If no filename provided, the filename is assigned the plugin name.
        if (!$filename) { $filename = $folder; }

        $file_location = $this->findCssFile($filename, $folder);
        
        // Add this css file to the global array of css_files
        $this->setCssIncludes($file_location);
        
        return $folder; // returned for testing purposes only
     }


    /**
     * Build an array of JavaScript files to combine
     *
     * @param $plugin - the folder name of the plugin
     * @param $filename - optional js file without an extension
     */
     public function includeJs($filename = '', $folder = '')
     {
        // If no filename provided, the filename is assigned the plugin name.
        if (!$filename) { $filename = $folder; }
        
        $file_location = $this->findJsFile($filename, $folder);
        
        // Add this css file to the global array of css_files
        $this->setJsIncludes($file_location);
        
        return $folder; // returned for testing purposes only
     }
     
     
    /**
     * Find CSS file
     *
     * @param string $folder name of plugin folder
     * @param string $filename optional filename without file extension
     *
     * Note: the css file should be in a folder named 'css' and a file of 
     * the format plugin_name.css, e.g. rss_show.css
     */    
    public function findCssFile($filename = '', $folder = '')
    {
        if ($folder) {

            // If filename not given, make the plugin name the file name
            if (!$filename) { $filename = $folder; }
            
            // First look in the theme folder for a css file...     
            if (file_exists(THEMES . THEME . 'css/' . $filename . '.css')) {    
                $file_location = THEMES . THEME . 'css/' . $filename . '.css';
            
            // If not found, look in the default theme folder for a css file...     
            } elseif (file_exists(THEMES . 'default/css/' . $filename . '.css')) {    
                $file_location = THEMES . 'default/css/' . $filename . '.css';
            
            // If still not found, look in the plugin folder for a css file... 
            } elseif (file_exists(PLUGINS . $folder . '/css/' . $filename . '.css')) {
                $file_location = PLUGINS . $folder . '/css/' . $filename . '.css';
            }
             
            if (isset($file_location)) {
                return $file_location;
            }
        }
    }


    /**
     * Find JavaScript file
     *
     * @param string $folder name of plugin folder
     * @param string $filename optional filename without file extension
     *
     * Note: the js file should be in a folder named 'javascript' and a file of the format plugin_name.js, e.g. category_manager.js
     */    
    public function findJsFile($filename = '', $folder = '')
    {
        if ($folder) {

            // If filename not given, make the plugin name the file name
            if (!$filename) { $filename = $folder; }
            
            // First look in the theme folder for a js file...     
            if (file_exists(THEMES . THEME . 'javascript/' . $filename . '.js')) {    
                $file_location = THEMES . THEME . 'javascript/' . $filename . '.js';
                
            // If not found, look in the default theme folder for a js file...     
            } elseif (file_exists(THEMES . 'default/javascript/' . $filename . '.js')) {    
                $file_location = THEMES . 'default/javascript/' . $filename . '.js';
                
            // If still not found, look in the plugin folder for a js file... 
            } elseif (file_exists(PLUGINS . $folder . '/javascript/' . $filename . '.js')) {
                $file_location = PLUGINS . $folder . '/javascript/' . $filename . '.js';
            }
             
            if (isset($file_location)) {
                return $file_location;
            }
        }
    }

    /**
     * Combine Included CSS & JSS files
     *
     * @param string $type either 'css' or 'js'
     * @param string $prefix either 'hotaru_' or ''hotaru_admin_'
     * @return int version number or echo output to cache file
     * @link http://www.ejeliot.com/blog/72 Based on work by Ed Eliot
     */
     public function combineIncludes($type = 'css', $version = 0)
     {
        if ($this->pageType == 'admin') {
            $this->plugins->pluginHook('admin_header_include');
            $prefix = 'hotaru_admin_';
        } else {
            $this->plugins->pluginHook('header_include');
            $prefix = 'hotaru_';
        }

        $cache_length = 31356000;   // about one year
        $cache = CACHE . 'css_js_cache/';
        
        if($type == 'css') { 
            $content_type = 'text/css';
            $includes = $this->getCssIncludes();
        } else { 
            $type = 'js'; 
            $content_type = 'text/javascript';
            $includes = $this->getJsIncludes();
        }

        if(empty($includes)) { return false; }

         /*
            if version parameter is present then the script is being called directly, otherwise we're including it in 
            another script with require or include. If calling directly we return code othewise we return the etag 
            (version number) representing the latest files
        */
        
        if ($version > 0) {
        
            // GET ACTUAL CODE - IF IT'S CACHED, SHOW THE CACHED CODE, OTHERWISE, GET INCLUDE FILES, BUILD AN ARCHIVE AND SHOW IT
        
            $iETag = $version;
            $sLastModified = gmdate('D, d M Y H:i:s', $iETag).' GMT';
            
            // see if the user has an updated copy in browser cache
            if (
                ($this->cage->server->keyExists('HTTP_IF_MODIFIED_SINCE') && $this->cage->server->testDate('HTTP_IF_MODIFIED_SINCE') == $sLastModified) ||
                ($this->cage->server->keyExists('HTTP_IF_NONE_MATCH') && $this->cage->server->testint('HTTP_IF_NONE_MATCH') == $iETag)
            ) {
                header("{$this->cage->server->getRaw('SERVER_PROTOCOL')} 304 Not Modified");
                exit;
            }
        
            // create a directory for storing current and archive versions
            if (!is_dir($cache)) {
                mkdir($cache);
            }
               
            // get code from archive folder if it exists, otherwise grab latest files, merge and save in archive folder
            if ((CSS_JS_CACHE_ON == "true") && file_exists($cache . $prefix . $type . '_' . $iETag . '.cache')) {
                $sCode = file_get_contents($cache . $prefix . $type . '_' . $iETag . '.cache');
            } else {
                // get and merge code
                $sCode = '';
                $aLastModifieds = array();
        
                foreach ($includes as $sFile) {
                    $aLastModifieds[] = filemtime($sFile);
                    $sCode .= file_get_contents($sFile);
                }
                // sort dates, newest first
                rsort($aLastModifieds);
             
                if ($iETag == $aLastModifieds[0]) { // check for valid etag, we don't want invalid requests to fill up archive folder
                    $oFile = fopen($cache . $prefix . $type . '_' . $iETag . '.cache', 'w');
                    if (flock($oFile, LOCK_EX)) {
                        fwrite($oFile, $sCode);
                        flock($oFile, LOCK_UN);
                    }
                    fclose($oFile);
                } else {
                    // archive file no longer exists or invalid etag specified
                    header("{$this->cage->server->getRaw('SERVER_PROTOCOL')} 404 Not Found");
                    exit;
                }
        
            }
        
            // send HTTP headers to ensure aggressive caching
            header('Expires: '.gmdate('D, d M Y H:i:s', time() + $cache_length).' GMT'); // 1 year from now
            header('Content-Type: ' . $content_type);
            header('Content-Length: '.strlen($sCode));
            header("Last-Modified: $sLastModified");
            header("ETag: $iETag");
            header('Cache-Control: max-age=' . $cache_length);
        
          // output merged code
          echo $sCode;
          
        } else {
        
            // get last modified dates for all files to include
            $aLastModifieds = array();
            foreach ($includes as $sFile) {
                $aLastModifieds[] = filemtime($sFile);
            }
            // sort dates, newest first
            rsort($aLastModifieds);
            
            // return latest timestamp, i.e. the most recently updated include file
            return $aLastModifieds[0];
        
        }
     }
        

    /**
     * Included combined files
     *
     * @param int $version_js 
     * @param int $version_css 
     * @param string $page e.g. admin_settings 
     * @param string $plugin e.g. category_manager
     */
     public function includeCombined($version_js = 0, $version_css = 0, $page = '', $folder = '')
     {
        if ($this->pageType == 'admin') { $index = 'admin_index'; } else { $index = 'index'; }
        if ($page && $folder) { 
            $page = 'page=' . $page; 
            $folder = 'plugin=' . $folder . "&";
        }
        
        if ($version_js > 0) {
            echo "<script type='text/javascript' src='" . BASEURL . $index . ".php?" . $page . "&" . $folder . "combine=1&type=js&version=" . $version_js . "'></script>\n";
        }
        
        if ($version_css > 0) {
            echo "<link rel='stylesheet' href='" . BASEURL . $index . ".php?" . $page . "&" . $folder . "combine=1&type=css&version=" . $version_css . "' type='text/css'>\n";
        }

     }

}
?>