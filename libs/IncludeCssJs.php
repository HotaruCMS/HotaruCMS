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
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2009, Hotaru CMS
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
     public function includeCss($filename = '', $folder = '', $admin = false)
     {
        // If no filename provided, the filename is assigned the plugin name.
        if (!$filename) { $filename = $folder; }

        $file_location = $this->findCssFile($filename, $folder);
        
        // Add this css file to the global array of css_files
        $this->setCssIncludes($file_location, $admin);
        
        return $folder; // returned for testing purposes only
     }


    /**
     * Build an array of JavaScript files to combine
     *
     * @param $plugin - the folder name of the plugin
     * @param $filename - optional js file without an extension
     */
     public function includeJs($filename = '', $folder = '', $admin = false)
     {
        // If no filename provided, the filename is assigned the plugin name.
        if (!$filename) { $filename = $folder; }
        
        $file_location = $this->findJsFile($filename, $folder);
        
        // Add this css file to the global array of css_files
        $this->setJsIncludes($file_location, $admin);
        
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
     public function combineIncludes($hotaru, $type = 'css', $version = 0, $admin = false)
     {
        if ($admin) {
            $hotaru->pluginHook('admin_header_include');
            $prefix = 'hotaru_admin_';
        } else {
            $hotaru->pluginHook('header_include');
            $prefix = 'hotaru_';
        }

        $cache_length = 31356000;   // about one year
        $cache = CACHE . 'css_js_cache/';
        
        if($type == 'css') { 
            $content_type = 'text/css';
            $includes = $this->getCssIncludes($admin);
        } else { 
            $type = 'js'; 
            $content_type = 'text/javascript';
            $includes = $this->getJsIncludes($admin);
        }
        
        $includes = array_unique($includes);    // remove duplicate includes
        
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
                ($hotaru->cage->server->keyExists('HTTP_IF_MODIFIED_SINCE') && $hotaru->cage->server->testDate('HTTP_IF_MODIFIED_SINCE') == $sLastModified) ||
                ($hotaru->cage->server->keyExists('HTTP_IF_NONE_MATCH') && $hotaru->cage->server->testint('HTTP_IF_NONE_MATCH') == $iETag)
            ) {
                header("{$hotaru->cage->server->getRaw('SERVER_PROTOCOL')} 304 Not Modified");
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
                    if ($sFile) {
                        $aLastModifieds[] = filemtime($sFile);
                        $sCode .= file_get_contents($sFile);
                    }
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
                    header("{$hotaru->cage->server->getRaw('SERVER_PROTOCOL')} 404 Not Found");
                    exit;
                }
        
            }
        
            // send HTTP headers to ensure aggressive caching
            header('Expires: '.gmdate('D, d M Y H:i:s', time() + $cache_length).' GMT'); // 1 year from now
            header('Content-Type: ' . $content_type);
            //header('Content-Length: '.strlen($sCode)); // causes site loading delays: http://hotarucms.org/showthread.php?t=197
            header("Last-Modified: $sLastModified");
            header("ETag: $iETag");
            header('Cache-Control: max-age=' . $cache_length);
        
          // output merged code
          echo $sCode;
          exit; // we don't want to drop out and continue building Hotaru or Admin objects when we're just including a file!
          
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
     public function includeCombined($version_js = 0, $version_css = 0, $admin = false)
     {
        if ($admin) { $index = 'admin_index'; } else { $index = 'index'; }
        
        if ($version_js > 0) {
            echo "<script type='text/javascript' src='" . BASEURL . $index . ".php?combine=1&amp;type=js&amp;version=" . $version_js . "'></script>\n";
        }
        
        if ($version_css > 0) {
            echo "<link rel='stylesheet' href='" . BASEURL . $index . ".php?combine=1&amp;type=css&amp;version=" . $version_css . "' type='text/css' />\n";
        }

     }
}
?>