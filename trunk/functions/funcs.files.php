<?php

/* **************************************************************************************************** 
 *  File: /funcs.files.php
 *  Purpose: A collection of functions to deal with files.
 *  Notes: ---
 *  License:
 *
 *   This file is part of Hotaru CMS (http://www.hotarucms.org/).
 *
 *   Hotaru CMS is free software: you can redistribute it and/or modify it under the terms of the 
 *   GNU General Public License as published by the Free Software Foundation, either version 3 of 
 *   the License, or (at your option) any later version.
 *
 *   Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 *   even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License along with Hotaru CMS. If not, 
 *   see http://www.gnu.org/licenses/.
 *   
 *   Copyright (C) 2009 Hotaru CMS - http://www.hotarucms.org/
 *
 **************************************************************************************************** */
 
/* ******************************************************************** 
 *  Function: getFilenames
 *  Parameters: folder and type ('full' path or otherwise just the filename) 
 *  Purpose: Returns all the filenames/paths in a specified folder.
 *  Notes: ---
 ********************************************************************** */
	 
function getFilenames($folder, $type='full') {	// Returns an array containing all the filenames in a folder
	$filenames = array();
	$handle = opendir($folder);
	while (false !== ($file = readdir($handle))) {
		if ($file != "." && $file != ".." && $file != ".svn") {
			if($type == 'full') {
	      			array_push($filenames, $folder . $file);	// 'full'
	      		} else {
	      			array_push($filenames, $file);		// 'short'
	      		}
	      	}
	}
	closedir($handle);
	return $filenames;
}


/* ******************************************************************** 
 *  Function: stripAllFileExtensions
 *  Parameters: an array of filenames/paths
 *  Purpose: Strips extensions from all files, e.g. .php, .js, .html
 *  Notes: ---
 ********************************************************************** */
 
function stripAllFileExtensions($fileNames) {	// Takes an array of filenames, returns them without extensions
	$stripped = array();
	foreach($fileNames as $fileName) {
		array_push($stripped, stripFileExtension($fileName));
	}
	return $stripped;
}


/* ******************************************************************** 
 *  Function: stripFileExtension
 *  Parameters: a single filename/path
 *  Purpose: Strips extensions from a single file, e.g. .php, .js, .html
 *  Notes: ---
 ********************************************************************** */
 
function stripFileExtension($fileName) {	// Takes a single filename, returns it without an extension
	return strtok($fileName, ".");
}


?>