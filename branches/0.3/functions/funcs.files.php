<?php
/**
 * A collection of functions to deal with files
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
 
/**
 * Get filenames or paths in a specified order
 * 
 * @param string $folder folder containing the files
 * @param string $type 'full' path or otherwise empty for the filename only 
 * @return array
 */
function getFilenames($folder, $type='full')
{
    $filenames  = array();
    $handle     = opendir($folder);
    
    while (false !== ($file = readdir($handle)))
    {
        if ($file != "." && $file != ".." && $file != ".svn") {
            if ($type == 'full') {
                      array_push($filenames, $folder . $file);    // full path
                  } else {
                      array_push($filenames, $file);        // filename only
                  }
              }
    }
    
    closedir($handle);
    return $filenames;
}


/**
 * Strip all extensions from files, e.g. .php, .js, .html
 * 
 * @param array $filenames array of filenames or paths
 * @return array
 */
function stripAllFileExtensions($fileNames)
{
    $stripped = array();
    
    foreach ($fileNames as $fileName) 
    {
        array_push($stripped, stripFileExtension($fileName));
    }
    return $stripped;
}


/**
 * Strip extensions from a single file, e.g. .php, .js, .html
 * @param string $filename 
 * @return string
 */
function stripFileExtension($fileName)
{
    return strtok($fileName, ".");
}

?>