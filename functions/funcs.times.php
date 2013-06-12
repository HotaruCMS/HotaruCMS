<?php

/**
 * A collection of functions to deal with time
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
 * Start timer for calculating page loading time
 *
 * @return true
 *
 * Note: Function borrowed from Wordpress.org
 */
function timer_start()
{
	global $timestart;

	$timestart = microtime(TRUE);
	return TRUE;

}

/**
 * Stop the timer
 *
 * @param int $precision number of digits after the decimal point
 * @return int $timetotal total timed
 * 
 *  Notes: Measured in seconds / Function borrowed from Wordpress.org
 */
function timer_stop($precision = 4)
{
	//if called like timer_stop(1), will echo $timetotal

	global $timestart;

	$timetotal = microtime(TRUE) - $timestart;
	$r = (function_exists('number_format_i18n')) ? number_format_i18n($timetotal, $precision) : number_format($timetotal, $precision);
	return $r;

}

/**
 * Calculate time difference between now and a given time
 *
 * @param string $from time
 * @return string
 *
 *  Note: Adapted from Pligg & SWCMS' txt_time_diff() function
 */
function time_difference($from, $lang)
{
	$output = '';
	$now = time();
	$diff = $now - $from;
	$days = intval($diff / 86400);
	$diff = $diff % 86400;
	$hours = intval($diff / 3600);
	$diff = $diff % 3600;
	$minutes = intval($diff / 60);

	if( $days > 1 ) {
		$output .= $days." ".$lang['main_times_days']." ";
	} elseif( $days == 1 ) {
		$output .= $days." ".$lang['main_times_day']." ";
	}

	if( $days < 2 ) {
		if( $hours > 1 ) {
			$output .= $hours." ".$lang['main_times_hours']." ";
		} elseif( $hours == 1 ) {
			$output .= $hours." ".$lang['main_times_hour']." ";
		}

		if( $hours < 3 ) {
			if( $minutes > 1 ) {
				$output .= $minutes." ".$lang['main_times_minutes']." ";
			} elseif( $minutes == 1 ) {
				$output .= $minutes." ".$lang['main_times_minute']." ";
			}
		}
	}

	if( $output == '' ) {
		$output = $lang['main_times_seconds']." ";
	}

	return $output;

}

/**
 * Converts a timestamp into a number
 *
 * @param string $timestamp
 * @return string
 *
 * Note: Borrowed from Pligg & SWCMS
 */
function unixtimestamp($timestamp)
{
	if( strlen($timestamp) == 0 ) {
		return 0;
	}

	if( strlen($timestamp) == 14 ) {
		$time = strptime($timestamp, '%Y%m%d%H%M%S');
		return mktime($time['tm_hour'], $time['tm_min'], $time['tm_sec'],1,$time['tm_yday']+1,$time['tm_year']+1900);
	}

	return strtotime($timestamp);

}

/**
 * Returns the next time block of the day, e.g. now: 1:25, so returns 2:00 (if block = 1 hour)
 *
 * @param int $block - time in HOURS
 * @return int - time in SECONDS
 */
function time_block($block = 1)
{
	$block = ($block * 60) * 60; // change into seconds
	$time_now = time();
	$start_of_current_block = floor($time_now / $block);
	$start_of_current_block *= $block;

	$start_of_next_block = $start_of_current_block + $block;

	return $start_of_next_block;

}


function time_ago($i){
    $i = strtotime($i);
    
    $m = time()-$i; $o='just now';
    $t = array('year'=>31556926,'month'=>2629744,'week'=>604800,
'day'=>86400,'hour'=>3600,'minute'=>60,'second'=>1);
    foreach($t as $u=>$s){
        if($s<=$m){$v=floor($m/$s); $o="$v $u".($v==1?'':'s').' ago'; break;}
    }
    return $o;
}

?>