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
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
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
	$filenames = array();
	$directory = new DirectoryIterator($folder);
	foreach( $directory as $file ) {
		if( !$file->isDot() && ($file->getFilename() != '.svn') ) {
			$filename = ($type === 'full') ? $folder.($file->getFilename()) : $file->getFilename();
			array_push($filenames, $file->getFilename());
		}
	}
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

	foreach( $fileNames as $fileName ) {
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

/**
 * Displaya filesize in a legible format
 *
 * @param int $filesize 
 * @return string
 * @link http://us3.php.net/manual/en/features.file-upload.php
 */
function display_filesize($filesize)
{
	if( is_numeric($filesize) ) {
		$decr = 1024;
		$step = 0;
		$prefix = array('Byte', 'KB', 'MB', 'GB', 'TB', 'PB');

		while( ($filesize / $decr) > 0.9 ) {
			$filesize = $filesize / $decr;
			$step++;
		}

		return round($filesize, 2).' '.$prefix[$step];
	}
	return 'Error displaying filesize';
}

/**
 * Start Hotaru from Ajax callback
 */
function startHotaru()
{
	$root = $_SERVER['DOCUMENT_ROOT'];

	if( strpos($root, 'C:/') !== false ) {
		$root = '../../..'; // a hack for Wamp. Works when calling this function from a theme or plugin folder
	}

	require_once($root.'/config/settings.php');
	require_once($root.'/Hotaru.php');

	$h = new Hotaru();
	$h->start();

	return $h;
}
?>