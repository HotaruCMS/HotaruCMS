<?php
/**
 * Functions for including and merging CSS and JavaScript
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
class IncludeCssJs
{
	protected $cssIncludes          = array();  // a list of css files to include
	protected $cssIncludesAdmin     = array();  // a list of css files to include in Admin
	protected $jsIncludes           = array();  // a list of js files to include
	protected $jsIncludesAdmin      = array();  // a list of js files to include in Admin
	protected $includeType          = '';       // 'css' or 'js'
	
	
	/**
	 * Include individual CSS files, not merged into the CSS archive
	 *
	 * @param $files- array of files to include (no extensions)
	 * @param $folder - optional pluin folder
	 */
	 public function includeOnceCss($h, $files = array(), $folder = '')
	 {
		if (empty($files)) { return false; }
		
		if (!$folder) { $folder = $h->plugin->folder; }
		
		if (!$folder) { return false; }
		
		foreach ($files as $file) {
			if (file_exists(THEMES . THEME . 'css/' . $file . '.css')) {
				echo "<link rel='stylesheet' href='" . SITEURL . "content/themes/" . THEME . "css/" . $file . ".css' type='text/css' />\n";
			} else {
				echo "<link rel='stylesheet' href='" . SITEURL . "content/plugins/" . $folder . "/css/" . $file . ".css' type='text/css' />\n";
			}
		}
	
		return true;
	}
	
	
	/**
	 * Include individual JavaScript files, not merged into the JavaScript archive
	 *
	 * @param $files- array of files to include (no extensions)
	 * @param $folder - optional pluin folder
	 */
	 public function includeOnceJs($h, $files = array(), $folder = '')
	 {
		if (empty($files)) { return false; }
		
		if (!$folder) { $folder = $h->plugin->folder; }
		
		if (!$folder) { return false; }
		
		foreach ($files as $file) {
			if (file_exists(THEMES . THEME . 'javascript/' . $file . '.js')) {
				echo "<script src='" . SITEURL . "content/themes/" . THEME . "javascript/" . $file . ".js' type='text/javascript' language='javascript'></script>\n";
			} else {
				echo "<script src='" . SITEURL . "content/plugins/" . $folder . "/javascript/" . $file . ".js' type='text/javascript' language='javascript'></script>\n";
			}
		}
		return true;
	}
	 
	 
	/**
	 * setCssIncludes
	 *
	 * @param string $file - full path to the CSS file
	 */
	public function setCssIncludes($file, $admin = false)
	{                
		if ($admin) {                         
                            array_push($this->cssIncludesAdmin, $file);
		} else {                        
                            array_push($this->cssIncludes, $file);                        
		}
	}
	
	
	/**
	 * getCssIncludes
	 */
	public function getCssIncludes($admin = false)
	{
		if ($admin) {
			return $this->cssIncludesAdmin;
		} else {
			return $this->cssIncludes;
		}
	}
	
	
	/**
	 * setJsIncludes
	 *
	 * @param string $file - full path to the JS file
	 */
	public function setJsIncludes($file, $admin = false)
	{        
		if ($admin) {                         
                            array_push($this->jsIncludesAdmin, $file);        
		} else {                       
                            array_push($this->jsIncludes, $file);
		}
	}
	
	
	/**
	 * getJsIncludes
	 */
	public function getJsIncludes($admin = false)
	{    
		if ($admin) {            
			return $this->jsIncludesAdmin;            
		} else {
			return $this->jsIncludes;
		}
	}
	
	
	/**
	 * Build an array of css files to combine
	 *
	 * @param $folder - the folder name of the plugin
	 * @param $filename - optional css file without an extension
	 */
	 public function includeCss($h, $folder = '', $filename = '')
	 {
		if (!$folder) { $folder = $h->plugin->folder; }
		
		// If no filename provided, the filename is assigned the plugin name.
		if (!$filename) { $filename = $folder; }
		
		$file_location = $this->findCssFile($folder, $filename);
		
		// Add this css file to the global array of css_files
		$this->setCssIncludes($file_location, $h->adminPage);
		
		return $folder; // returned for testing purposes only
	 }
	
	
	/**
	 * Build an array of JavaScript files to combine
	 *
	 * @param $folder - the folder name of the plugin
	 * @param $filename - optional js file without an extension
	 */
	 public function includeJs($h, $folder = '', $filename = '')
	 {
		if (!$folder) { $folder = $h->plugin->folder; }
		
		// If no filename provided, the filename is assigned the plugin name.
		if (!$filename) { $filename = $folder; }
		
		$file_location = $this->findJsFile($folder, $filename);
		
		// Add this js file to the global array of js_files
		$this->setJsIncludes($file_location, $h->adminPage);
		
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
	public function findCssFile($folder = '', $filename = '')
	{
		if (!$folder) { return false; }
		
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
                
                // If still not found, look in the full given folder itself for a js file... 
		} elseif (file_exists($folder . '/css/' . $filename . '.css')) {
			$file_location = $folder . '/css/' . $filename . '.css';
		}
		 
		if (isset($file_location)) {
			return $file_location;
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
	public function findJsFile($folder = '', $filename = '')
	{    
		if (!$folder) { return false; }
		
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
		
		// If still not found, look in the full given folder itself for a js file... 
		} elseif (file_exists($folder . $filename . '.js')) {
			$file_location = $folder . $filename . '.js';
		}

		if (isset($file_location)) {
			return $file_location;
		}
	}
	
	
	/**
	 * Combine Included CSS & JSS files
	 *
	 * @param string $type either 'css' or 'js'
	 * @return bool
	 * @link http://www.ejeliot.com/blog/72 Based on work by Ed Eliot
	 */
	 public function combineIncludes($h, $type = 'css')
	 {
		// set up cache
		$cache_length = 31356000;   // about one year
		$cache = CACHE . 'css_js_cache/';
		
	 	// run plugin functions to include css/js files
		if ($h->adminPage) {
			$h->pluginHook('admin_header_include');
			$prefix = 'hotaru_admin_';
		} else {
			$h->pluginHook('header_include');
			$prefix = 'hotaru_';
		}
		
		// append css or js to cache filename
		$prefix .= ($type == 'css') ? 'css_' : 'js_'; 
		
		// fill "includes" array with all the files we need to merge
		if($type == 'css') { 
			$content_type = 'text/css';
			$includes = $this->getCssIncludes($h->adminPage);
		} else { 
			$type = 'js'; 
			$content_type = 'text/javascript';
			//don't forget to get the globals js file as well            
			$this->includeJs($h, $cache, 'JavascriptConstants')    ;
			$this->includeJs($h, BASE . 'javascript/' , "hotaru");        
			if ($h->adminPage) {
				$this->includeJs($h, ADMIN_THEMES . ADMIN_THEME. "javascript/" , rtrim(ADMIN_THEME, "/"));
			}
			
			$includes = $this->getJsIncludes($h->adminPage);
		}
		
		// remove duplicate include files
		$includes = array_unique($includes);
		if(empty($includes)) { return false; }
		
		// get last modified dates for all files to include
		$aLastModifieds = array();
		foreach ($includes as $sFile) {
			$aLastModifieds[] = filemtime($sFile);
		}
		// sort dates, newest first
		rsort($aLastModifieds);
		
		// the most recently updated include file
		$last_modified_include_file = $aLastModifieds[0];

		// GET ACTUAL CODE - IF IT'S CACHED, SHOW THE CACHED CODE, OTHERWISE, GET INCLUDE FILES, BUILD AN ARCHIVE AND SHOW IT

		// get code from archive folder if it exists, otherwise grab latest files, merge and save in archive folder
		if ((CSS_JS_CACHE == "true") && file_exists($cache . $prefix . $last_modified_include_file . '.' . $type))
		{
			// use the exiting cache file
			return $last_modified_include_file;
		} 

		// get and merge code
		$sCode = '';
		$aLastModifieds = array();
		
		// if not in debug mode, get the Jsmin class
		if (!$h->isDebug) {
			require_once(EXTENSIONS . 'Jsmin/Jsmin.php');
		}
		
		foreach ($includes as $sFile) {
			if ($sFile) {
				$aLastModifieds[] = filemtime($sFile);
				if ($h->isDebug) {
					$sCode .= "/* Open: " . $sFile . " */\n\n";
					$sCode .= file_get_contents($sFile); // don't minify files when debugging
					$sCode .= "\n\n/* Close: " . $sFile . " */\n\n";
				} else {
					$sCode .= JSMin::minify(file_get_contents($sFile)); // minify files
				}
			}
		}
		
		// sort dates, newest first
		rsort($aLastModifieds);
		
		 // check for valid file time, we don't want invalid requests to fill up archive folder
		if ($last_modified_include_file == $aLastModifieds[0])
		{
			$oFile = fopen($cache . $prefix . $last_modified_include_file . '.' . $type, 'w');
			if (flock($oFile, LOCK_EX)) {
				fwrite($oFile, $sCode);
				flock($oFile, LOCK_UN);
			}
			fclose($oFile);
		} 
		else 
		{
			// archive file no longer exists or invalid etag specified
			header("{$h->cage->server->getRaw('SERVER_PROTOCOL')} 404 Not Found");
			exit;
		}
	
		// cache file written, return the time so we can add it within SCRIPT in the header
		return $last_modified_include_file;

	 }

	
	/**
	 * Included combined files
	 *
	 * @param int $version_js 
	 * @param int $version_css 
	 * @param bool $admin 
	 */
	 public function includeCombined($h, $version_js = 0, $version_css = 0, $admin = false)
	 {
		if ($admin) { $prefix = 'hotaru_admin_'; } else { $prefix = 'hotaru_'; }
		
		if ($version_js > 0) {
			echo "<script type='text/javascript' async src='" . SITEURL . "cache/css_js_cache/" . $prefix  . "js_" . $version_js . ".js'></script>\n";
		}
		
		if ($version_css > 0) {
			echo "<link rel='stylesheet' href='" . SITEURL . "cache/css_js_cache/" . $prefix  . "css_" . $version_css . ".css' type='text/css' />\n";
		}
		
	 }
}
?>
