<?php
/**
 * Friends functions, i.e. for following / unfollowing users
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
class Friends
{
	/**
	 * count followers / following
	 *
	 * @param int $user_id - get people following this user
	 * @param string $type - 'following' or 'follower'
	 * @return int $count
	 */
	public function countFriends($h, $user_id = 0, $type = 'follower')
	{
		if (!$user_id) { $user_id = $h->currentUser->id; }
		
		if ($type == 'follower') { 
			$where = "following_user_id"; // get users who are followING this user
		} else { 
			$where = "follower_user_id";  // get users who list this user as a followER
		}
		
		$sql = "SELECT count(*) FROM " . TABLE_FRIENDS . " WHERE " . $where . " = %s";
		$count = $h->db->get_var($h->db->prepare($sql, $user_id));		
		return $count;
	}

	
	 /**
	 * Get Followers / Following
	 *
	 * @param int $user_id - get people following this user
	 * @param string $type - 'following' or 'follower'
	 * @param string $return - 'array' or prepared 'query'
	 * @return array | string
	 */
	 public function getFriends($h, $user_id = 0, $type = 'follower', $return = 'array')
	 {
		if (!$user_id) { $user_id = $h->currentUser->id; }

		if ($type == 'follower') { 
			$type1 = "follower_user_id"; 
			$type2 = "following_user_id"; 
		} else { 
			$type1 = "following_user_id"; 
			$type2 = "follower_user_id"; 
		}
		
		$sql = "SELECT user_id, user_username FROM " . TABLE_USERS . " AS USERS JOIN " . TABLE_FRIENDS . " AS FOLLOW on FOLLOW." . $type1 . " = USERS.user_id WHERE FOLLOW." . $type2 . " = %d ORDER BY friends_updatedts DESC";
		$query = $h->db->prepare($sql, $user_id);

		if ($return == 'array') {
			$results = $h->db->get_results($query);
			return ($results) ? $results : false;
		} else {
			return $query;
		}
	 }
	 
	 
	/**
	 * Check if the specified user is already following or being followed by $h->currentUser
	 *
	 * @param string $type - 'following' or 'follower'
	 * @return bool
	 */
	 public function checkFriends($h, $user_id = 0, $type = 'follower')
	 {
		if (!$user_id) { return false; }
		
		if ($type == 'follower') { 
			$type1 = "following_user_id"; 
			$type2 = "follower_user_id"; 
		} else { 
			$type1 = "follower_user_id"; 
			$type2 = "following_user_id";
		}
	
		$sql = "SELECT count(*) FROM " . TABLE_FRIENDS . " WHERE " . $type1 ." = %d AND " . $type2 . " = %d";
		$result = $h->db->get_var($h->db->prepare($sql, $h->currentUser->id, $user_id));
	
		return ($result) ? true : false;
	 }


	/**
	 * Update Friend - follow or unfollow
	 *
	 * @param int $user_id - user to follow or unfollow
	 * @param string $action - 'follow' or 'unfollow'
	 * @return bool
	 */
	public function updateFriends($h, $user_id = 0, $action = 'follow')
	{
		if (!$user_id) { return false; }
		
		if ($action == 'follow')
		{
			// if already following, return false 
			if ($h->isFollowing($user_id)) { return false; }
			
			// start following
			$sql = "INSERT INTO " . TABLE_FRIENDS . " (follower_user_id, following_user_id) VALUES (%d, %d)";
			$h->db->query($h->db->prepare($sql, $h->currentUser->id, $user_id));
		}
		else
		{   
			// if not following anyway, return false 
			if (!$h->isFollowing($user_id)) { return false; }
			
			// stop following
			$sql = "DELETE FROM " . TABLE_FRIENDS . " WHERE (follower_user_id = %d AND following_user_id = %d)";
			$h->db->query($h->db->prepare($sql, $h->currentUser->id, $user_id));
		}		
		return true;
	}

}
?>
