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
				return ($h->isAdmin) ? 'admin_index' : 'index';
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
	public function getTitle($h, $delimiter = ' &laquo; ', $raw = false)
	{
		// if the title is already set...
		if ($h->pageTitle != "")
		{
			// replace [delimiter] text with the specified delimiter:
			$h->pageTitle = str_replace('[delimiter]', $delimiter, $h->pageTitle);
			
			// return the title only
			if ($raw) { return $h->pageTitle; }
			
			// if this is the index page...
			if ($h->pageName == 'index') {
				// title only (set by plugins, e.g. sb_base)
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
			
			// return with site name
			return $h->pageTitle . $delimiter . SITE_NAME;
		}
		else
		{ 
			// there's no title and no page name - assume "page not found"
			$h->pageTitle = $h->lang['main_theme_page_not_found'];
			
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
	public function displayTemplate($h, $page = '', $plugin = '', $include_once = true)
	{
		$h->pageTemplate = $page;
		
		// if no plugin folder, provide it:
		if (!$plugin) { $plugin = $h->plugin->folder; }
		
		if ($h->isAdmin) { 
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
	public function url($h, $parameters = array(), $head = 'index')
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
				
					// must be a post, let's get the post_url after we've read the post (if necessary)
					if (!$h->post->url) {
						$h->readPost($value);
					}
					$value = $h->post->url;
					
					// if we're using categories and the category is not "all"...
					if ($h->isActive('categories') && $h->post->category != 1) {
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
		
		if ($h->isAdmin) { 
			$head = 'admin_index.php?';
		} else {
			$head = 'index.php?';
		}
		
		/*	Many pages use standard urls even if friendly urls is enabled. For example,
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