<?php

/* ******************************************************************** 
 *  File: functions/funcs.files.php
 *  Purpose: A collection of functions to deal with files.
 *  Notes: ---
 ********************************************************************** */
 
 
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
?>