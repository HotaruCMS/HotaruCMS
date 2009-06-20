<?php

/* ******************************************************************** 
 *  File: functions/funcs.forms.php
 *  Purpose: Takes parameters passed through a form using Inspekt for sanitation, then passes them back to the original script via a hook.
 *  Notes: ALthough we're using getRaw, I think it still provides basic sanitation - must check.
 ********************************************************************** */
 
// Includes
require_once('../hotaru_header.php');
$params = filter_form_input();
$plugin->check_actions('rss_sidebar', $params);
die();

/* ******************************************************************** 
 *  Function: filter_form_input
 *  Parameters: None
 *  Purpose: Uses Inspekt to santize form input.
 *  Notes: ---
 ********************************************************************** */
 
function filter_form_input() {
	global $cage;
 	$params = array();
	
	if($cage->get) {
		foreach($cage->get as $item) {
			if($item) {
				foreach($item as $key => $value) {
					$params[$key] = $cage->get->getRaw($key);
				}
			}
		} 
	}
	 
	if($cage->post) {
		foreach($cage->post as $item) {
			if($item) {
				foreach($item as $key => $value) {
					$params[$key] = $cage->post->getRaw($key);
				}
			}
		} 
	}
	
	return $params;
}

?>