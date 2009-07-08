<?php

/* **************************************************************************************************** 
 *  File: /admin/functions/funcs.times.php
 *  Purpose: A collection of functions to deal with time.
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
 *  Function: timer_start
 *  Parameters: None
 *  Purpose: Starts the timer, for debugging purposes.
 *  Notes: Function borrowed from Wordpress.org
 ********************************************************************** */
 
function timer_start() {
	global $timestart;
	$mtime = explode(' ', microtime() );
	$mtime = $mtime[1] + $mtime[0];
	$timestart = $mtime;
	return true;
}


 /* ******************************************************************** 
 *  Function: timer_stop
 *  Parameters: Precision - no. of digits after the decimal point
 *  Purpose: Stops the debugging timer.
 *  Notes: Measured in seconds / Function borrowed from Wordpress.org
 ********************************************************************** */

function timer_stop($precision = 3) { //if called like timer_stop(1), will echo $timetotal
	global $timestart, $timeend;
	$mtime = microtime();
	$mtime = explode(' ',$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$timeend = $mtime;
	$timetotal = $timeend-$timestart;
	$r = ( function_exists('number_format_i18n') ) ? number_format_i18n($timetotal, $precision) : number_format($timetotal, $precision);
	return $r;
}


 /* ******************************************************************** 
 *  Function: time_difference
 *  Parameters: "from" time
 *  Purpose: To show ow long ago a post was submitted
 *  Notes: Adapted from Pligg & SWCMS' txt_time_diff() function
 ********************************************************************** */
 
function time_difference($from){
	global $lang;
	
	$output = '';
	$now = time();
	$diff=$now-$from;
	$days=intval($diff/86400);
	$diff=$diff%86400;
	$hours=intval($diff/3600);
	$diff=$diff%3600;
	$minutes=intval($diff/60);

	if($days>1) $output .= $days . " " . $lang['main_times_days'] . " ";
	elseif ($days==1) $output .= $days . " " . $lang['main_times_day'] . " ";

	if($days < 2){
		if($hours>1) $output .= $hours . " " . $lang['main_times_hours'] . " ";
		else if ($hours==1) $output .= $hours . " " . $lang['main_times_hour'] . " ";
	
		if($hours < 3){
			if($minutes>1) $output .= $minutes . " " . $lang['main_times_minutes'] . " ";
			else if ($minutes==1) $output .= $minutes . " " . $lang['main_times_min'] . " ";
		}
	}
	
	if($output=='') $output = $lang['main_times_seconds'] . " ";
	return $output;
}


 /* ******************************************************************** 
 *  Function: unixtimestamp
 *  Parameters: a timestamp
 *  Purpose: converts a timestamp into a number
 *  Notes: Borrowed from Pligg & SWCMS
 ********************************************************************** */
 
function unixtimestamp($timestamp){
	if(strlen($timestamp) == 14) {
		$time = substr($timestamp,0,4)."-".substr($timestamp,4,2)."-".substr($timestamp,6,2);
		$time .= " ";
		$time .=  substr($timestamp,8,2).":".substr($timestamp,10,2).":".substr($timestamp,12,2);
		return strtotime($time);
	} else {
		if(strlen($timestamp) == 0) {
			return 0;
		} else {
			return strtotime($timestamp);
		}
	}
}
?>