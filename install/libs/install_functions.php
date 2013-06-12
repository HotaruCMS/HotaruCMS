<?php
/**
 * Install function for the Hotaru CMS installer.
 * 
 * Steps through the set-up process, creating database tables and registering 
 * the Admin user. Note: You must delete this file after installation as it 
 * poses a serious security risk if left.
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
 * Initialize Database
 *
 * @return object
 */
function init_database()
{
	$ezSQL = new ezSQL_mysql(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
	$ezSQL->query("SET NAMES 'utf8'");
	
	return $ezSQL;
}
    
    
/**
 * Initialize Inspekt
 *
 * @return object
 */
function init_inspekt_cage()
{
	$cage = Inspekt::makeSuperCage(); 
	
	// Add Hotaru custom methods
	$cage->addAccessor('testAlnumLines');
	$cage->addAccessor('testPage');
	$cage->addAccessor('testUsername');
	$cage->addAccessor('testPassword');
	$cage->addAccessor('getFriendlyUrl');
	$cage->addAccessor('sanitizeAll');
	$cage->addAccessor('sanitizeTags');
	$cage->addAccessor('getHtmLawed');
	
	return $cage;
}


/**
 * Delete plugin database table
 *
 * @param string $table_name - table to drop
 */
function drop_table($table_name)
{
	global $db;
	
	$db->query("DROP TABLE " . $table_name);
}