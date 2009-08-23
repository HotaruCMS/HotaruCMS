<?php
/**
 * Combine plugin css and js files into one big file for each.
 *
 * Based on Ed Eliot's work here: http://www.ejeliot.com/blog/72
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
 
if(file_exists('hotaru_header.php')) { 
    include_once('hotaru_header.php'); 
} elseif(file_exists('../hotaru_header.php')) {
    include_once('../hotaru_header.php'); 
}

global $hotaru, $cage, $plugin, $admin;

/* NOTE: This file is called twice from a theme's header or admin header section.
The first time it just gets the time the last file was modified and returns that 
as a version number (very bottom of this file). That's used to build the url of the 
file that we are including. The second time does the actual inclusion, but since 
this "merge_includes" file is pulled in separately to other Hotaru files, we need to 
padd "admin" and "type" as url parameters instead of using the $plugin object which 
we use on the first pass. */

// Plugin hook for "admin_header_include"
if ($hotaru->page_type == 'admin' || $cage->get->keyExists('admin')) {
    
    // Need the Admin class when including this file via JavaScript
    if(!is_object($admin)) {
        require_once('../admin/class.admin.php');
        $admin = new Admin();
    }
    $plugin->check_actions('admin_header_include');
    
// Standard plugin hook for "header_include"
} else {
    $plugin->check_actions('header_include');
}

$cache_length = 31356000;   // about one year
$cache = cache . 'css_js_cache/';

if(($plugin->include_type == 'css') || ($cage->get->testAlpha('type') == 'css')) { 
    $type = 'css'; 
    $content_type = 'text/css';
    $includes = $plugin->include_css;
} else { 
    $type = 'js'; 
    $content_type = 'text/javascript';
    $includes = $plugin->include_js;
}

 /*
    if etag parameter is present then the script is being called directly, otherwise we're including it in 
    another script with require or include. If calling directly we return code othewise we return the etag 
    representing the latest files
*/
if ($cage->get->keyExists('version')) {

    $iETag = $cage->get->testInt('version');
    $sLastModified = gmdate('D, d M Y H:i:s', $iETag).' GMT';
    
    // see if the user has an updated copy in browser cache
    if (
        ($cage->server->keyExists('HTTP_IF_MODIFIED_SINCE') && $cage->server->testDate('HTTP_IF_MODIFIED_SINCE') == $sLastModified) ||
        ($cage->server->keyExists('HTTP_IF_NONE_MATCH') && $cage->server->testint('HTTP_IF_NONE_MATCH') == $iETag)
    ) {
        header("{$cage->server->getRaw('SERVER_PROTOCOL')} 304 Not Modified");
        exit;
    }

    // create a directory for storing current and archive versions
    if (!is_dir($cache)) {
        mkdir($cache);
    }
       
    // get code from archive folder if it exists, otherwise grab latest files, merge and save in archive folder
    if (file_exists($cache . 'hotaru_' . $type . '_' . $iETag . '.cache')) {
        $sCode = file_get_contents($cache . 'hotaru_' . $type . '_' . $iETag . '.cache');
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
            $oFile = fopen($cache . 'hotaru_' . $type . '_' . $iETag . '.cache', 'w');
            if (flock($oFile, LOCK_EX)) {
                fwrite($oFile, $sCode);
                flock($oFile, LOCK_UN);
            }
            fclose($oFile);
        } else {
            // archive file no longer exists or invalid etag specified
            header("{$cage->server->getRaw('SERVER_PROTOCOL')} 404 Not Found");
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

    // get file last modified dates
    $aLastModifieds = array();
    foreach ($includes as $sFile) {
        $aLastModifieds[] = filemtime($sFile);
    }
    // sort dates, newest first
    rsort($aLastModifieds);
    
    // output latest timestamp
    echo $aLastModifieds[0];

}

?>