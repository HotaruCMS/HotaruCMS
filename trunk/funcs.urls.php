<?php

/* **************************************************************************************************** 
 *  File: /admin/functions/funcs.urls.php
 *  Purpose: A collection of functions for making friendly urls.
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
 *  Function: url
 *  Parameters: an array of pairs, e.g. 'page' => 'about' and a $head, either 'index' or 'admin'
 *  Purpose: Generates either default or friendly urls
 *  Notes: ---
 ********************************************************************** */
 
function url($parameters = array(), $head = 'index') {	
	
	if(friendly_urls == "false") {
	
		if($head == 'index') {
			$url = baseurl . 'index.php?';
		} elseif($head == 'admin') {
			$url = baseurl . 'admin/admin_index.php?';	
		} else {
			// Error. $head must be index or admin
		}
		
		if(empty($parameters)) { 
			$url = rtrim($url, '?'); 
			return $url; 
		} 

		foreach($parameters as $key => $value) {
			$url .= $key . '=' . $value . '&amp;';
		}
		return rtrim($url, '&amp;');	
		
	} 
	
	if(friendly_urls == "true") {
	
		if($head == 'index') { 
			$url = baseurl;
		} elseif($head == 'admin') {
			$url = baseurl . 'admin/';	
		} else {
			$url = baseurl . $head . '/';
		}
		
		foreach($parameters as $key => $value) {
			$url .= $value . '/';
		}
		return $url;
	}
	
}


/* ******************************************************************** 
 *  Function: rewrite
 *  Parameters: The requested url, an array delimeter and a a pair delimiter
 *  Purpose: Breaks a friendly url into its parts
 *  Notes: Adapted from: http://www.roscripts.com/Mod_rewrite_and_PHP_functions-47.html
 ********************************************************************** */
/*	 
function rewrite( $request, $array_delim, $pair_delim ) {
	global $_GET, $HTTP_GET_VARS, $_REQUEST;
	
	echo $request . "<br />";
	$value_pairs = explode( $array_delim, $request );
	$make_global = array();
	
	foreach( $value_pairs as $pair ) {
		if($pair) {
			$pair = explode( $pair_delim, $pair );
			echo "pair0: " . $pair[0] . ": " . "pair1: " . $pair[1] . '<br />';
			$_GET[$pair[0]] = $pair[1];
			$_REQUEST[$pair[0]] = $pair[1];
			$HTTP_GET_VARS[$pair[0]] = $pair[1];
		}
	}
}
*/
?>