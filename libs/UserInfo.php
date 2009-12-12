<?php
/**
 * Functions for retrieving information about users
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
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
class UserInfo extends UserBase
{
     /**
     * Checks if the user has an 'admin' role
     *
     * @return bool
     */
    public function isAdmin($db, $username)
    {
        $sql = "SELECT * FROM " . TABLE_USERS . " WHERE user_username = %s AND user_role = %s";
        $role = $db->get_row($db->prepare($sql, $username, 'admin'));
        
        if ($role) { return true; } else { return false; }
    }
    
    
    /**
     * Check if a user exists
     *
     * @param int $userid 
     * @param string $username
     * @return int
     *
     * Notes: Returns 'no' if a user doesn't exist, else field under which found
     */
    public function userExists($db, $id = 0, $username = '', $email = '')
    {
        // id found
        if ($id != 0) {
            if ($db->get_var($db->prepare("SELECT * FROM " . TABLE_USERS . " WHERE user_id = %d", $id))) {
                return 'id'; // id exists
            } 
        } 
        
        // name found
        if ($username != '') {
            if ($db->get_var($db->prepare("SELECT * FROM " . TABLE_USERS . " WHERE user_username = %s", $username))) {
                return 'name'; // username exists
            }         
        } 
        
        // email found
        if ($email != '') {
            if ($db->get_var($db->prepare("SELECT * FROM " . TABLE_USERS . " WHERE user_email = %s", $email))) {
                return 'email'; // email exists
            }         
        } 
        
        // Error - no arguments provided
        if (($id == 0) && ($username == '') && ($email == '')) {
            return 'error'; // no arguments provided
        } 
        
        return 'no'; // User doesn't exist
    }
}