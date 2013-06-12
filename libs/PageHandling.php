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
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
class PageHandling
{
	protected $default = 'default/';
	protected $adminDefault = 'admin_default/';
	
	
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
	
	
	/**
	 * Set the homepage (and set page name)
	 *
	 * @param string $home
	 * @param string $pagename
	 */
	public function setHome($h, $home = '', $pagename = '')
	{
		$h->home = $home;
		$h->pageName = $h->getPageName(); 
		
		if (!$h->pageName) { 
			$h->pageName = $pagename; // force pageName (optional)
		}
	}


	/**
	 * Test if the current url is the *true* homepage, i.e. equal to SITEURL
	 *
	 * @return bool
	 */
	public function isHome($h)
	{
		if ($h->pageName != $h->home) { return false; }
	
		/*  Sometimes $h->home is not unique. E.g. if $h->home is "popular", then 
			a category page filtered to popular will match $h->home. We need to test
			for the true home page. If it's the true homepage, the current url will 
			match either SITEURL, or SITEURL + index.php */
	
		// get full url from address bar
		$host = $h->cage->server->sanitizeTags('HTTP_HOST');
		$uri = $h->cage->server->sanitizeTags('REQUEST_URI');
		$path = "http://" . $host  . $uri;
	
		switch ($path) {
			case BASEURL:
			case BASEURL . 'index.php':
				return true;
				break;
			default:
				return false;
		}
	}
	
	
	/**
	 * Checks if current page (in url or form) matches the page parameter
	 *
	 * @param string $page page name
	 */
	public function isPage($h, $page = '')
	{
		$real_page = $h->cage->get->testPage('page');
		
		if (!$real_page) { 
			/*  Possibly a post with multi-byte characters? 
				Try sanitizeTags... */
			$real_page = $h->cage->get->sanitizeTags('page');
		}
		
		// Try POST...
		if (!$real_page) { $real_page = $h->cage->post->testPage('page'); }
		
		if (!$real_page) { return false; }
		
		if ($real_page == $page) { return true; } else { return false; }
	}
	
	
	/**
	 * Gets the current page name
	 *
	 * @return string|false $page
	 */
	public function getPageName($h)
	{
		if ($h->pageName) { return $h->pageName; }
		
		// Try GET...
		$page = $h->cage->get->testPage('page');
		if (!$page) {
			/*  Possibly a post with multi-byte characters? 
				Try sanitizeTags... */
			$page = $h->cage->get->sanitizeTags('page');
		}
		
		// Try POST...
		if (!$page) { $page = $h->cage->post->testPage('page'); }

		// Analyze the URL:
		if (!$page) {
			$host = $h->cage->server->sanitizeTags('HTTP_HOST');
			$uri = $h->cage->server->sanitizeTags('REQUEST_URI');
			$path = "http://" . $host  . $uri;
			
			if (FRIENDLY_URLS == 'true') {
				$path = $h->friendlyToStandardUrl($path);
			}
			
			$query_args = parse_url($path, PHP_URL_QUERY);  // get all query vars
			
			if (!$query_args) { // no query vars - must be the home page
				$index = ($h->home) ? $h->home : ''; 
				return ($h->adminPage) ? 'admin_index' : $index;
			}

			parse_str($query_args, $parsed_query_args); // split query vars into key->value pairs
			
			$results = $h->pluginHook('pagehandling_getpagename', '', $parsed_query_args);
			if ($results) {
				foreach ($results as $key => $value) {
					if ($value) { $page = $value; } // assigns the LAST value returned to this hook
				}
			}
		} 
		
		if ($page) {
			$page = str_replace('..', '', $page); // prevents access outside the current folder
			$page = rtrim($page, '/');
			return $page;
		} else {
			return false;
		}
	}
	
	
	/**
	 * Determine the title tags for the header
	 *
	 * @param bool $raw -return the title only
	 * @return string - the title
	 */
	public function getTitle($h, $delimiter = ' | ', $raw = false)
	{
		// if the title is already set...
		if ($h->pageTitle)
		{
			// replace [delimiter] text with the specified delimiter:
			$h->pageTitle = str_replace('[delimiter]', $delimiter, $h->pageTitle);
			
			// return the title only
			if ($raw) { return $h->pageTitle; }
			
			// if this is the home page...
			if ($h->pageName == $h->home) {
				// title only (set by plugins, e.g. bookmarking)
				return $h->pageTitle;
			} else {
				// title followed by site name
				return $h->pageTitle . $delimiter . SITE_NAME;
			}
		}
		// fetch the page name...
		elseif ($h->getPageName())
		{
			// make a title from it...
			$h->pageTitle = make_name($h->pageName);
			
			// return the title only
			if ($raw) { return $h->pageTitle; }
			
			// return just the site name for the homepage
			if ($h->pageName == $h->home) { return SITE_NAME; }
			
			// return with site name
			return $h->pageTitle . $delimiter . SITE_NAME;
		}
		else
		{ 
			// there's no title and no page name - assume "page not found"
			$h->pageTitle = $h->lang('main_theme_page_not_found');
			
			// return the title only
			if ($raw) { return $h->pageTitle; }
			
			return $h->pageTitle  . $delimiter . SITE_NAME;
		} 
	}
	
	
	/**
	 * Includes a template to display
	 *
	 * @param string $page page name
	 * @param string $plugin optional plugin name
	 * @param bool $include_once true or false
	 */
	public function template($h, $page = '', $plugin = '', $include_once = true)
	{
		$h->pageTemplate = $page;
		
		// if no plugin folder, provide it:
		if (!$plugin) { $plugin = $h->plugin->folder; }
		
		if ($h->adminPage) { 
			$themes = ADMIN_THEMES; 
			$theme = ADMIN_THEME;
			$default = $this->adminDefault;
		} else {
			$themes = THEMES;
			$theme = THEME;
			$default = $this->default;                        
		} 
                
                $page = str_replace('..', '', $page); // prevents access outside the current folder
		$page = $page . '.php';

                // Include this for testing
                if ($h->isTest) print "plugin = " . $plugin . ' : pages = ' . $page . '<br/>';
                
		/* 
			1. Check the custom theme (skip step 1 if plugin is "pages")
			2. Check the default theme (skip step 2 if plugin is "pages")
			3. Check the plugin folder
			4. Show the 404 Not Found page from the theme
			5. Show the 404 Not Found page from "themes" folder
		*/

		if ($plugin != 'pages' && file_exists($themes . $theme . $page))
		{ 
			if (!$include_once) {
				// Special case, do not restrict to include once.
                                if ((include $themes . $theme . $page) == 1) {
                                    //
                                } else {
                                    echo 'ERROR : ' . $themes . $theme . $page . ' not included';
                                    
                                } 				
			} else {
				if ((include_once $themes . $theme . $page) == 1) {
                                    //
                                } else {
                                    echo 'ERROR : ' . $themes . $theme . $page . ' not included';                                    
                                }                                
			}
		} 
		elseif ($plugin != 'pages' && file_exists($themes . $default . $page)) 
		{
			if (!$include_once) {
				// Special case, do not restrict to include once.
				include($themes . $default . $page);
			} else {
				include_once($themes . $default . $page);
			}
		}
                elseif ($h->adminPage && file_exists($themes . $theme . 'views/' . $page)) 
                {
                        include_once($themes . $theme . 'views/' . $page);
                }
		elseif ($plugin == 'pages' && file_exists(CONTENT . 'pages/' . $page))
		{
			include_once(CONTENT . 'pages/' . $page);
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
	 * Includes a view template to display
	 *
	 * @param string $page page name
	 * @param array $data optional array of data
	 */
	public function render($h, $page = '', $data = array())
	{
                if (!$h->adminPage) return false;
                
		$page = str_replace('..', '', $page); // prevents access outside the current folder
                $page = $page . '.php';
                
                $folder = ADMIN_THEMES . ADMIN_THEME;                
                 
                if (file_exists($folder . 'views/' . $page))
                {
                    include_once($folder . 'views/' . $page);			
                }                
        }
	
	/**
	 * Generate either default or friendly urls
	 *
	 * @param array $parameters an array of pairs, e.g. 'page' => 'about' 
	 * @param string $head either 'index' or 'admin'
	 * @return string
	 */
	public function url($h, $parameters = array(), $head = 'index')
	{
                $url = '';
		if (FRIENDLY_URLS == "false") {
		
			if ($head == 'index') {
				$url = SITEURL . 'index.php?';
			} elseif ($head == 'admin') {
				$url = SITEURL . 'admin_index.php?';    
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
				$url = SITEURL;
			} elseif ($head == 'admin') {
				$url = SITEURL . 'admin/';    
			} else {
				$url = SITEURL . $head . '/';
			}
			
			foreach ($parameters as $key => $value) {
			
				if ($key == 'page' && is_numeric($value) ) {
				
					// must be a post, let's get the post_url after we've read the post (if necessary)
					if (!$h->post->url) {
						$h->readPost($value);
					}
					$value = $h->post->url;
					
					// if we're using categories and the category is not "all"...
					if ($h->isActive('categories') && $h->post->category > 1) {
						$url .= $h->getCatSafeName($h->post->category) . '/';
					}
					
					$url .= $value . '/';
				
				} elseif ($key == 'category' && is_numeric($value) ) {
				
					$url .= $key . '/' . $h->getCatSafeName($value) . '/';
				
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
	 * Return page numbers bar
	 *
	 * @param object $pageObject - current object of type Paginated
	 * @return string - HTML for page number bar
	 */
	public function pageBar($h, $pageObject = NULL)
	{
		require_once(EXTENSIONS . 'Paginated/Paginated.php');
		require_once(EXTENSIONS . 'Paginated/DoubleBarLayout.php');
		
		$pageObject->setLayout(new DoubleBarLayout());
		return $pageObject->fetchPagedNavigation($h);
	}
	
	
	/**
	 * Converts a friendly url into a standard one
	 *
	 * @param object $h
	 * @param string $friendly_url
	 * return string $standard_url
	 */
	public function friendlyToStandardUrl($h, $friendly_url = '') 
	{
		$path = $friendly_url;
		
		if ($h->adminPage) { 
			$head = 'admin_index.php?';
		} else {
			$head = 'index.php?';
		}
		
		/*	Many pages use standard urls even if friendly urls is enabled. For example,
			admin pages, pagination or other complex urls. Therefore, we have to do an
			extra test to make sure this is a truly friendly url, and if not, send back
			the one we have because it's already standard. */

		if ($path == SITEURL || strrpos($path, $head)) { return $path; }
		
		// strip off SITEURL and trailing slash
		$url = str_replace(SITEURL, '', $friendly_url);
		$url = rtrim($url, '/');
		
		// start the standard url
		$standard_url = SITEURL . $head;
		
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
	 *  Usage: $h->isSettingsPage('submit') returns true if 
	 *         page=plugin_settings and plugin=submit in the url.
	 */
	public function isSettingsPage($h, $folder = '')
	{
		if (!$folder) { $folder = $h->plugin->folder; }
		
		if ($h->isPage('plugin_settings') && $h->cage->get->testAlnumLines('plugin') == $folder) {
			return true;
		} else {
			return false;
		}
	}
	
}
?>
