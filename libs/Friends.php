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
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
class Friends
{
	/**
	 * count followers / following
	 *
	 * @param int $userid - get people following this user
	 * @param string $type - 'following' or 'follower'
	 *
	 * @return int $count
	 */
	public function countFriends($h, $userid = 0, $type = 'follower')
	{
		if (!$userid) { $userid = $h->currentUser->id; }
		
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
	 * @param int $userid - get people following this user
	 * @param string $type - 'following' or 'follower'
	 * @param string $return - 'array' or prepared 'query'
	 * @return array | string
	 */
	 public function getFriends($h, $user_id = 0, $type = 'follower', $return = 'array')
	 {
		if (!$userid) { $userid = $h->currentUser->id; }

		if ($type == 'follower') { 
			$type1 = "follower_user_id"; 
			$type2 = "following_user_id"; 
		} else { 
			$type1 = "following_user_id"; 
			$type2 = "follower_user_id"; 
		}
		
		$sql = "SELECT user_id, user_username FROM " . TABLE_USERS . " AS USERS JOIN " . TABLE_FRIENDS . " AS FOLLOW on FOLLOW." . $type1 . " = USERS.user_id WHERE FOLLOW." . $type2 . " = %d";
		$query = $h->db->prepare($sql, $user_id);

		if ($return == 'array') {
			$results = $h->db->get_results($query);
			return ($results) ? $results : false;
		} else {
			return $query;
		}
	 }
	 
	 
	/**
	 * Follow / become a fan of user X
	 *
	 * @param int $userid - user to follow
	 */
	public function follow($h, $userid = 0)
	{

	}
	
	
	/**
	 * Unfollow / stop being a fan of user X
	 *
	 * @param int $userid - user to stop following
	 */
	public function unfollow($h, $userid = 0)
	{

	}
}
?>
