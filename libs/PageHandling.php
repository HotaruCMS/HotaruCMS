<?php
/**
 * Functions for handling pages, e.g. titles and templates
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
class PageHandling
{
    /**
     * Checks if current page (in url or form) matches the page parameter
     *
     * @param string $page page name
     */
    public function isPage($hotaru, $page = '')
    {
        $real_page = $hotaru->cage->get->testPage('page');
        
        if (!$real_page) { 
            /*  Possibly a post with multi-byte characters? 
                Try getMixedString2... */
            $real_page = $hotaru->cage->get->getMixedString2('page');
        }
        
        // Try POST...
        if (!$real_page) { $real_page = $hotaru->cage->post->testPage('page'); }
        
        if (!$real_page) { return false; }

        if ($real_page == $page) { return true; } else { return false; }
    }
    
    
    /**
     * Gets the current page name
     *
     * @return string|false $page
     */
    public function getPageName($hotaru)
    {
        if ($hotaru->pageName) { return $hotaru->pageName; }

        // Try GET...
        $page = $hotaru->cage->get->testPage('page');
        if (!$page) {
            /*  Possibly a post with multi-byte characters? 
                Try getMixedString2... */
            $page = $hotaru->cage->get->getMixedString2('page');
        }
        
        // Try POST...
        if (!$page) { $page = $hotaru->cage->post->testPage('page'); }
        
        // Analyze the URL:
        if (!$page) {
            $host = $hotaru->cage->server->getMixedString2('HTTP_HOST');
            $uri = $hotaru->cage->server->getMixedString2('REQUEST_URI');
            $path = "http://" . $host  . $uri;
            
            if (FRIENDLY_URLS == 'true') {
                $path = $hotaru->friendlyToStandardUrl($path);
            }
            
            $query_args = parse_url($path, PHP_URL_QUERY);  // get all query vars
            
            if (!$query_args) { // no query vars - must be the home page
                return ($hotaru->isAdmin) ? 'admin_index' : 'index';
            }

            parse_str($query_args, $parsed_query_args); // split query vars into key->value pairs
            
            // we'll need a plugin hook here so we can parse the query vars to plugins and let them 
            // determine and return the page name.
        } 
        
        if ($page) {
            $page = rtrim($page, '/');
            return $page;
        } else {
            return false;
        }
    }
    
    
    /**
     * Determine the title tags for the header
     *
     * @return string - the title
     */
    public function getTitle($hotaru)
    {
        // if the title is already set...
        if ($hotaru->pageTitle != "")
        {
            // if this is the index page...
            if($hotaru->pageName == 'index') {
                // title only (set by plugins, e.g. sb_base)
                return $hotaru->pageTitle;
            } else {
                // title followed by site name
                return $hotaru->pageTitle . " &laquo; " . SITE_NAME;
            }
        }
        // fetch the page name...
        elseif ($hotaru->getPageName())
        {
            // make a title from it...
            $hotaru->pageTitle = make_name($hotaru->pageName);
            return $hotaru->pageTitle . " &laquo; " . SITE_NAME;
        }
        else
        { 
            // there's no title and no page name - assume "page not found"
            $hotaru->pageTitle = $hotaru->lang['main_theme_page_not_found'];
            return $hotaru->pageTitle  . " &laquo; " . SITE_NAME;
        } 
    }
    
    
    /**
     * Includes a template to display
     *
     * @param string $page page name
     * @param string $plugin optional plugin name
     * @param bool $include_once true or false
     */
    public function displayTemplate($hotaru, $page = '', $plugin = '', $include_once = true)
    {
        $hotaru->pageTemplate = $page;
        
        // if no plugin folder, provide it:
        if (!$plugin) { $plugin = $hotaru->plugin->folder; }
        
        if ($hotaru->isAdmin) { 
            $themes = ADMIN_THEMES; $theme = ADMIN_THEME; 
        } else {
            $themes = THEMES; $theme = THEME; 
        } 
        
        $page = $page . '.php';

        /* 
            1. Check the custom theme
            2. Check the default theme
            3. Check the plugin folder
            4. Show the 404 Not Found page from the theme
            5. Show the 404 Not Found page from "themes" folder
        */
        if (file_exists($themes . $theme . $page))
        {
            if (!$include_once) {
                // Special case, do not restrict to include once.
                include($themes . $theme . $page);
            } else {
                include_once($themes . $theme . $page);
            }
        } 
        elseif (file_exists($themes . 'default/' . $page))
        {
            if (!$include_once) {
                // Special case, do not restrict to include once.
                include($themes . 'default/' . $page);
            } else {
                include_once($themes . 'default/' . $page);
            }
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
        elseif (file_exists($themes . $theme . '404error.php')) 
        {
            include_once($themes . $theme . '404error.php');
        }
        else
        {
            include_once($themes . '404error.php');
        }
    }
    
    
    /**
     * Generate either default or friendly urls
     *
     * @param array $parameters an array of pairs, e.g. 'page' => 'about' 
     * @param string $head either 'index' or 'admin'
     * @return string
     */
    public function url($hotaru, $parameters = array(), $head = 'index')
    {
        if (FRIENDLY_URLS == "false") {
        
            if ($head == 'index') {
                $url = BASEURL . 'index.php?';
            } elseif ($head == 'admin') {
                $url = BASEURL . 'admin_index.php?';    
            } else {
                // Error. $head must be index or admin
            }
            
            if (empty($parameters)) { 
                $url = rtrim($url, '?'); 
                return $url; 
            } 
    
            foreach ($parameters as $key => $value) {
                $url .= $key . '=' . $value . '&amp;';
            }
            return rstrtrim($url, '&amp;');    
            
        } 
        
        if (FRIENDLY_URLS == "true") {
        
            if ($head == 'index') { 
                $url = BASEURL;
            } elseif ($head == 'admin') {
                $url = BASEURL . 'admin/';    
            } else {
                $url = BASEURL . $head . '/';
            }
            
            foreach ($parameters as $key => $value) {
            
                if ($key == 'page' && is_numeric($value) ) {
                
                    // must be a post title, let's get the post_url...
                    $value = $this->post->url;
                    
                    // if we're using categories and the category is not "all"...
                    if (isset($this->post->vars['useCategories']) && $this->post->vars['category'] != 1) {
                        $url .= $this->post->vars['catSafeName'] . '/';
                    }
                    
                    $url .= $value . '/';
                    
                } elseif ($key == 'category' && is_numeric($value) ) {
                    
                    require_once(PLUGINS . 'categories/libs/Category.php');
                    $cat = new Category($this->db);
                    $url .= $key . '/' . $cat->getCatSafeName($value) . '/';
                        
                } elseif ($key == 'page') {
                
                    // don't show "page" in the url, only the value
                    $url .= $value . '/';    
                                    
                } else {
                    $url .= $key . '/' . $value . '/';
                }
            }
            return $url;
        }
        
    }
    
    
    /**
     * Prepare pagination and display page numbers bar
     *
     * @param array $items - array of all items to show
     * @param int $items_per_page
     * @param int $pg - current page number
     * @return object - object of type Paginated
     */
    public function pagination($hotaru, $items = array(), $items_per_page = 10, $pg = 0)
    {
        if (!$items) { return false; }
        
        require_once(EXTENSIONS . 'Paginated/Paginated.php');
        require_once(EXTENSIONS . 'Paginated/DoubleBarLayout.php');

        $pg = $hotaru->cage->get->getInt('pg');
        return new Paginated($items, $items_per_page, $pg);
    }
    
    
    /**
     * Return page numbers bar
     *
     * @param object $pageObject - current object of type Paginated
     * @return string - HTML for page number bar
     */
    public function pageBar($pageObject = array())
    {
        require_once(EXTENSIONS . 'Paginated/Paginated.php');
        require_once(EXTENSIONS . 'Paginated/DoubleBarLayout.php');
        
        $pageObject->setLayout(new DoubleBarLayout());
        return $pageObject->fetchPagedNavigation($hotaru);
    }
    
    
    /**
     * Converts a friendly url into a standard one
     *
     * @param object $hotaru
     * @param string $friendly_url
     * return string $standard_url
     */
    public function friendlyToStandardUrl($hotaru, $friendly_url = '') 
    {
        $path = $friendly_url;
        
        if ($hotaru->isAdmin) { 
            $head = 'admin_index.php?';
        } else {
            $head = 'index.php?';
        }
        
        /* Many pages use standard urls even if friendly urls is enabled. For example,
           admin pages, pagination or other complex urls. Therefore, we have to do an
           extra test to make sure this is a truly friendly url, and if not, send back
           the one we have because it's already standard. */
           
        if ($path == BASEURL || strrpos($path, $head)) { return $path; }
        
        // strip off BASEURL and trailing slash
        $url = str_replace(BASEURL, '', $friendly_url);
        $url = rtrim($url, '/');

        // start the standard url
        $standard_url = BASEURL . $head;
        
        // parts will hold the query vars
        $parts = array();
        $parts = explode('/', $url);
        
        // if odd number of query vars, the first is the page
        if (count($parts) % 2 == 1) {
             $page = array_shift($parts);
             $standard_url .= 'page=' . $page;
             if (!empty($parts)) { $standard_url .= '&'; }
        }
        
        // if query vars still in array, add them
        while (!empty($parts)) {
            $key = array_shift($parts);
            $value = array_shift($parts);
            $standard_url .= $key . '=' . $value;
            if (!empty($parts)) { $standard_url .= '&'; }
        }
        
        return $standard_url;
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
    public function isSettingsPage($hotaru, $folder = '')
    {
        if (!$folder) { $folder = $hotaru->plugin->folder; }
        
        if ($hotaru->isPage('plugin_settings') && $hotaru->cage->get->testAlnumLines('plugin') == $folder) {
            return true;
        } else {    
            return false;
        }
    }
    
}
?>