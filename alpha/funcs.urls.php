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
	global $post;
	
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
		return rstrtrim($url, '&amp;');	
		
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
		
			if($key == 'page' && is_numeric($value) ) {
			
				// must be a post title, let's get the post_url...
				$value = $post->post_url;
				if(isset($post->post_vars['post_category']) && $post->post_vars['post_category'] != 1) {
					$url .= $post->post_vars['post_cat_safe_name'] . '/';
				}
				$url .= $value . '/';
				
			} elseif($key == 'category' && is_numeric($value) ) {
			
				//function call to plugins/categories/categories.php
				$url .= $key . '/' . get_cat_safe_name($value) . '/';
					
			} elseif($key == 'page') {
			
				// don't show "page" in the url, only the value
				$url .= $value . '/';	
								
			} else {
				$url .= $key . '/' . $value . '/';
			}
		}
		return $url;
	}
	
}

?>