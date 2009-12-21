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
    
    
    /**
     * Get all users with permission to access admin
     */
    public function getMods($hotaru, $permission = 'can_access_admin', $value = 'yes')
    {
        $sql = "SELECT user_id FROM " . TABLE_USERS . " WHERE (user_role = %s) || (user_role = %s) || (user_role = %s)";
        $users = $hotaru->db->get_results($hotaru->db->prepare($sql, 'admin', 'supermod', 'moderator'));
        
        if (!$users) { return false; }
        
        $mods = array();
        
        foreach ($users as $user) {
            $details = new UserBase();
            $details->getUserBasic($hotaru, $user->user_id);
            if ($details->getPermission($permission) == $value) {
                $mods[$details->id]['id'] = $details->id;
                $mods[$details->id]['role'] = $details->role;
                $mods[$details->id]['name'] = $details->name;
                $mods[$details->id]['email'] = $details->email;
            }
        }
        return $mods;
    }
    
    
    /**
     * Get the ids and names of all users or those with a specified role, sorted alphabetically
     *
     * @return array
     */
    public function userIdNameList($hotaru, $role = '')
    {
        if ($role) { 
            $sql = "SELECT user_id, user_username FROM " . TABLE_USERS . " WHERE user_role = %s ORDER BY user_username ASC";
            $results = $hotaru->db->get_results($hotaru->db->prepare($sql, $role));
        } else {
            $sql = "SELECT user_id, user_username FROM " . TABLE_USERS . " ORDER BY user_username ASC";
            $results = $hotaru->db->get_results($sql);
        }
        
        return $results;
    }
    
    
    /**
     * Get settings for all users
     *
     * @return array
     */
    public function userSettingsList($hotaru, $userid = 0)
    {
        if ($userid) { 
            $settings = $hotaru->getProfileSettingsData($type = 'user_settings', $userid);
            return $settings;
        } else {
            $sql = "SELECT usermeta_userid, usermeta_value FROM " . DB_PREFIX . "usermeta WHERE usermeta_key = %s";
            $results = $hotaru->db->get_results($hotaru->db->prepare($sql, 'user_settings'));
        }
        
        return $results;
    }
    
    
    /**
     * Get full details of all users or batches of users, sorted alphabetically
     *
     * @return array
     */
    public function userListFull($hotaru, $id_array = array(), $start = 0, $range = 0)
    {
        if (!$id_array) {
            // get all users
            $sql = "SELECT * FROM " . TABLE_USERS . " ORDER BY user_username ASC";
            $results = $hotaru->db->get_results($sql);
        } else {
            // for grabbing 
            if ($range) { $limit = " LIMIT " . $start . ", " . $range; }
            $sql = "SELECT * FROM " . TABLE_USERS . " WHERE ";
            for ($i=0; $i < count($id_array); $i++) {
                $sql .= "user_id = %d OR ";
            }
            $sql = rstrtrim($sql, "OR "); // strip trailing OR
            $sql .= " ORDER BY user_username ASC" . $limit;

            $prepare_array[0] = $sql;
            $prepare_array = array_merge($prepare_array, $id_array);
            $results = $hotaru->db->get_results($hotaru->db->prepare($prepare_array));
        }
        return $results;
    }
    
    
    /**
     * Stats for Admin homepage
     *
     * @param string $stat_type
     * @return int
     */
    public function stats($hotaru, $stat_type = '')
    {
        switch ($stat_type) {
            case 'admins':
                $sql = "SELECT count(user_id) FROM " . TABLE_USERS . " WHERE user_role = %s";
                $users = $hotaru->db->get_var($hotaru->db->prepare($sql, 'admin'));
                break;
            case 'supermods':
                $sql = "SELECT count(user_id) FROM " . TABLE_USERS . " WHERE user_role = %s";
                $users = $hotaru->db->get_var($hotaru->db->prepare($sql, 'supermod'));
                break;
            case 'moderators':
                $sql = "SELECT count(user_id) FROM " . TABLE_USERS . " WHERE user_role = %s";
                $users = $hotaru->db->get_var($hotaru->db->prepare($sql, 'moderator'));
                break;
            case 'members':
                $sql = "SELECT count(user_id) FROM " . TABLE_USERS . " WHERE user_role = %s";
                $users = $hotaru->db->get_var($hotaru->db->prepare($sql, 'member'));
                break;
            case 'total_users':
                $sql = "SELECT count(user_id) FROM " . TABLE_USERS;
                $users = $hotaru->db->get_var($sql);
                break;
            case 'approved_users':
                $sql = "SELECT count(user_id) FROM " . TABLE_USERS . " WHERE user_role = %s OR user_role = %s OR user_role = %s OR  user_role = %s";
                $users = $hotaru->db->get_var($hotaru->db->prepare($sql, 'admin', 'supermod', 'moderator', 'member'));
                break;
            case 'undermod_users':
                $sql = "SELECT count(user_id) FROM " . TABLE_USERS . " WHERE user_role = %s";
                $users = $hotaru->db->get_var($hotaru->db->prepare($sql, 'undermod'));
                break;
            case 'banned_users':
                $sql = "SELECT count(user_id) FROM " . TABLE_USERS . " WHERE user_role = %s";
                $users = $hotaru->db->get_var($hotaru->db->prepare($sql, 'banned'));
                break;
            case 'killspammed_users':
                $sql = "SELECT count(user_id) FROM " . TABLE_USERS . " WHERE user_role = %s";
                $users = $hotaru->db->get_var($hotaru->db->prepare($sql, 'killspammed'));
                break;
            default:
                $users = 0;
        }
        
        return $users;
    }
}