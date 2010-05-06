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
 * @copyright Copyright (c) 2010, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
class UserInfo extends UserBase
{
	/**
	 * Get the username for a given user id
	 *
	 * @param int $id user id
	 * @return string|false
	 */
	public function getUserNameFromId($h, $id = 0)
	{
		$sql = "SELECT user_username FROM " . TABLE_USERS . " WHERE user_id = %d LIMIT 1";
		
		$username = $h->db->get_var($h->db->prepare($sql, $id));
		if ($username) { return $username; } else { return false; }
	}
	
	
	/**
	 * Get the user id for a given username
	 *
	 * @param string $username
	 * @return int|false
	 */
	public function getUserIdFromName($h, $username = '')
	{
		$sql = "SELECT user_id FROM " . TABLE_USERS . " WHERE user_username = %s  LIMIT 1";
		
		$userid = $h->db->get_var($h->db->prepare($sql, $username));
		if ($userid) { return $userid; } else { return false; }
	}
	
	
	/**
	 * Get the email from user id
	 *
	 * @param int $userid
	 * @return string|false
	 */
	public function getEmailFromId($h, $userid = 0)
	{
		$sql = "SELECT user_email FROM " . TABLE_USERS . " WHERE user_id = %d  LIMIT 1";
		
		$email = $h->db->get_var($h->db->prepare($sql, $userid));
		if ($email) { return $email; } else { return false; }
	}
	
	
	/**
	 * Get the user id from email
	 *
	 * @param string $email
	 * @return string|false
	 */
	public function getUserIdFromEmail($h, $email = '')
	{
		$sql = "SELECT user_id FROM " . TABLE_USERS . " WHERE user_email = %s  LIMIT 1";
		
		$userid = $h->db->get_var($h->db->prepare($sql, $email));
		if ($userid) { return $userid; } else { return false; }
	}
	
	
	 /**
	 * Checks if the user has an 'admin' role
	 *
	 * @return bool
	 */
	public function isAdmin($db, $username)
	{
		$sql = "SELECT * FROM " . TABLE_USERS . " WHERE user_username = %s AND user_role = %s  LIMIT 1";
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
			if ($db->get_var($db->prepare("SELECT * FROM " . TABLE_USERS . " WHERE user_id = %d  LIMIT 1", $id))) {
				return 'id'; // id exists
			} 
		} 
		
		// name found
		if ($username != '') {
			if ($db->get_var($db->prepare("SELECT * FROM " . TABLE_USERS . " WHERE user_username = %s  LIMIT 1", $username))) {
				return 'name'; // username exists
			}
		} 
		
		// email found
		if ($email != '') {
			if ($db->get_var($db->prepare("SELECT * FROM " . TABLE_USERS . " WHERE user_email = %s  LIMIT 1", $email))) {
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
	 * Check if an username exists in the database (used in forgotten password)
	 *
	 * @param string $username user username
	 * @param string $role user role (optional)
	 * @param int $exclude - exclude a user
	 * @return string|false
	 */
	public function nameExists($h, $username = '', $role = '', $exclude = 0)
	{
		if (!$username) {  return false; }
		
		if (!$exclude) {
			if ($role) { 
				$sql = "SELECT user_username FROM " . TABLE_USERS . " WHERE user_username = %s AND user_role = %s  LIMIT 1";
				$valid_username = $h->db->get_var($h->db->prepare($sql, $username, $role));
			} else {
				$sql = "SELECT user_username FROM " . TABLE_USERS . " WHERE user_username = %s  LIMIT 1";
				$valid_username = $h->db->get_var($h->db->prepare($sql, $username));
			}
		} else {
			if ($role) { 
				$sql = "SELECT user_username FROM " . TABLE_USERS . " WHERE user_username = %s AND user_role = %s AND user_id != %d  LIMIT 1";
				$valid_username = $h->db->get_var($h->db->prepare($sql, $username, $role, $exclude));
			} else {
				$sql = "SELECT user_username FROM " . TABLE_USERS . " WHERE user_username = %s AND user_id != %d  LIMIT 1";
				$valid_username = $h->db->get_var($h->db->prepare($sql, $username, $exclude));
			}
		}
	
		if ($valid_username) { return $valid_username; } else { return false; }
	}
	
	
	/**
	 * Check if an email exists in the database (used in forgotten password)
	 *
	 * @param string $email user email
	 * @param string $role user role (optional)
	 * @param int $exclude - exclude a user
	 * @return string|false
	 */
	public function emailExists($h, $email = '', $role = '', $exclude = 0)
	{
		if (!$email) {  return false; }
		
		if (!$exclude) {
			if ($role) { 
				$sql = "SELECT user_email FROM " . TABLE_USERS . " WHERE user_email = %s AND user_role = %s  LIMIT 1";
				$valid_email = $h->db->get_var($h->db->prepare($sql, $email, $role));
			} else {
				$sql = "SELECT user_email FROM " . TABLE_USERS . " WHERE user_email = %s  LIMIT 1";
				$valid_email = $h->db->get_var($h->db->prepare($sql, $email));
			}
		} else {
			if ($role) { 
				$sql = "SELECT user_email FROM " . TABLE_USERS . " WHERE user_email = %s AND user_role = %s AND user_id != %d  LIMIT 1";
				$valid_email = $h->db->get_var($h->db->prepare($sql, $email, $role, $exclude));
			} else {
				$sql = "SELECT user_email FROM " . TABLE_USERS . " WHERE user_email = %s AND user_id != %d  LIMIT 1";
				$valid_email = $h->db->get_var($h->db->prepare($sql, $email, $exclude));
			}
		}
		
		if ($valid_email) { return $valid_email; } else { return false; }
	}
	
	
	/**
	 * Get all users with permission to access admin
	 */
	public function getMods($h, $permission = 'can_access_admin', $value = 'yes')
	{
		$sql = "SELECT user_id FROM " . TABLE_USERS . " WHERE (user_role = %s) || (user_role = %s) || (user_role = %s)";
		$users = $h->db->get_results($h->db->prepare($sql, 'admin', 'supermod', 'moderator'));
	
		if (!$users) { return false; }
		
		$mods = array();
		
		foreach ($users as $user) {
			$details = new UserBase();
			$details->getUserBasic($h, $user->user_id);
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
	 * @param string $role - optional user role to filter to
	 * @return array
	 */
	public function userIdNameList($h, $role = '')
	{
		if ($role) { 
			$sql = "SELECT user_id, user_username FROM " . TABLE_USERS . " WHERE user_role = %s ORDER BY user_username ASC";
			$results = $h->db->get_results($h->db->prepare($sql, $role));
		} else {
			$sql = "SELECT user_id, user_username FROM " . TABLE_USERS . " ORDER BY user_username ASC";
			$results = $h->db->get_results($sql);
		}
		
		return $results;
	}
	
	
	/**
	 * Get settings for all users
	 *
	 * @return array
	 */
	public function userSettingsList($h, $userid = 0)
	{
		if ($userid) { 
			$settings = $h->getProfileSettingsData($type = 'user_settings', $userid);
			return $settings;
		} else {
			$sql = "SELECT usermeta_userid, usermeta_value FROM " . DB_PREFIX . "usermeta WHERE usermeta_key = %s";
			$results = $h->db->get_results($h->db->prepare($sql, 'user_settings'));
		}
		
		return $results;
	}
	
	
	/**
	 * Get full details of all users or batches of users, sorted alphabetically
	 *
	 * @param array $id_array - optional array of user ids
	 * @param int $start - LIMIT $start $range (optional)
	 * @param int $range - LIMIT $start $range (optional)
	 * @return array
	 */
	public function userListFull($h, $id_array = array(), $start = 0, $range = 0)
	{
		if (!$id_array) {
			// get all users
			$sql = "SELECT * FROM " . TABLE_USERS . " ORDER BY user_username ASC";
			$results = $h->db->get_results($sql);
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
			$results = $h->db->get_results($h->db->prepare($prepare_array));
		}
		return $results;
	}
	
	
	/**
	 * Stats for Admin homepage
	 *
	 * @param string $stat_type
	 * @return int
	 */
	public function stats($h, $stat_type = '')
	{
		switch ($stat_type) {
			case 'admins':
				$sql = "SELECT count(user_id) FROM " . TABLE_USERS . " WHERE user_role = %s  LIMIT 1";
				$users = $h->db->get_var($h->db->prepare($sql, 'admin'));
				break;
			case 'supermods':
				$sql = "SELECT count(user_id) FROM " . TABLE_USERS . " WHERE user_role = %s  LIMIT 1";
				$users = $h->db->get_var($h->db->prepare($sql, 'supermod'));
				break;
			case 'moderators':
				$sql = "SELECT count(user_id) FROM " . TABLE_USERS . " WHERE user_role = %s  LIMIT 1";
				$users = $h->db->get_var($h->db->prepare($sql, 'moderator'));
				break;
			case 'members':
				$sql = "SELECT count(user_id) FROM " . TABLE_USERS . " WHERE user_role = %s  LIMIT 1";
				$users = $h->db->get_var($h->db->prepare($sql, 'member'));
				break;
			case 'total_users':
				$sql = "SELECT count(user_id) FROM " . TABLE_USERS;
				$users = $h->db->get_var($sql);
				break;
			case 'approved_users':
				$sql = "SELECT count(user_id) FROM " . TABLE_USERS . " WHERE user_role = %s OR user_role = %s OR user_role = %s OR  user_role = %s  LIMIT 1";
				$users = $h->db->get_var($h->db->prepare($sql, 'admin', 'supermod', 'moderator', 'member'));
				break;
			case 'pending_users':
				$sql = "SELECT count(user_id) FROM " . TABLE_USERS . " WHERE user_role = %s  LIMIT 1";
				$users = $h->db->get_var($h->db->prepare($sql, 'pending'));
				break;
			case 'undermod_users':
				$sql = "SELECT count(user_id) FROM " . TABLE_USERS . " WHERE user_role = %s  LIMIT 1";
				$users = $h->db->get_var($h->db->prepare($sql, 'undermod'));
				break;
			case 'banned_users':
				$sql = "SELECT count(user_id) FROM " . TABLE_USERS . " WHERE user_role = %s  LIMIT 1";
				$users = $h->db->get_var($h->db->prepare($sql, 'banned'));
				break;
			case 'killspammed_users':
				$sql = "SELECT count(user_id) FROM " . TABLE_USERS . " WHERE user_role = %s  LIMIT 1";
				$users = $h->db->get_var($h->db->prepare($sql, 'killspammed'));
				break;
			default:
				$users = 0;
		}
		
		return $users;
	}
	
	
	/**
	 * Get Unique Roles
	 *
	 * @return array|false
	 */
	public function getUniqueRoles($h) 
	{
		/* This function pulls all the different user roles from the database, 
		or adds some defaults if not present.*/
		
		$unique_roles = array();
		
		// Some essentials:
		array_push($unique_roles, 'admin');
		array_push($unique_roles, 'supermod');
		array_push($unique_roles, 'moderator');
		array_push($unique_roles, 'member');
		array_push($unique_roles, 'undermod');
		array_push($unique_roles, 'pending');
		array_push($unique_roles, 'suspended');
		array_push($unique_roles, 'banned');
		array_push($unique_roles, 'killspammed');
		
		// Add any other roles already in use:
		$sql = "SELECT DISTINCT user_role FROM " . TABLE_USERS;
		$roles = $h->db->get_results($h->db->prepare($sql));
		if ($roles) {
			foreach ($roles as $role) {
				if (!in_array($role->user_role, $unique_roles)) { 
					array_push($unique_roles, $role->user_role);
				}
			}
		}
		
		if ($unique_roles) { return $unique_roles; } else { return false; }
	}
}
