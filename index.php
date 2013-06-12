<?php

/**
 * Includes settings and constructs Hotaru.
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
// includes
if(file_exists('config/settings.php') ) {
	require_once('config/settings.php');
	require_once('Hotaru.php');
	$h = new Hotaru();
        $h->start('main');
} else {
        	
	if(file_exists('install/index.php') ) {
            $msg1 = 'Hotaru is having trouble starting.<br/>You may need to install the system before you can proceed further.<br/><br/>';		
	} else {
            $msg1 = 'Hotaru is having trouble starting.<br/>The install files need to be downloaded before you can proceed further.<br/><br/>';
	}

        include('error.php'); 
}
?>