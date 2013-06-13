<?php
/**
 * UserActivity class
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
 *
 */
class UserActivity
{
	/**
	 * Get the latest site activity
	 *
	 * @param int $limit
	 * @param int $userid
	 * @param string $type blank or "count" or "query"
	 * @return array|false
	 */
	public function getLatestActivity($h, $limitCount = 0, $userid = 0, $type = '', $fromId = 0)
	{
		$limit = (!$limitCount) ? '' : "LIMIT " . $limitCount;
		
                // if we also know the post id then we should join it in and check for buried,banned
                // also we should select on the title
                // however since we dont know whether post_id will appear in first or second useract_key we would have to make a case statement here which is messy
                // TODO
                // consider reorganizing the activity table to have a activity_post column which can be easily looked up
                
		if (!$userid)
		{
                        $select = ($type == 'count') ? 'count(useract_id)' : 'UA.*, U.user_username';
			//$sql = "SELECT " . $select . " FROM " . TABLE_USERACTIVITY . " AS UA LEFT OUTER JOIN " . TABLE_USERS . " AS U ON UA.useract_userid = U.user_id WHERE UA.useract_archived = %s AND UA.useract_status = %s AND UA.useract_id > %d AND P.post_status <> %s AND P.post_status <> %s ORDER BY UA.useract_date DESC " . $limit;
			//$query = $h->db->prepare($sql, 'N', 'show', $fromId, 'pending', 'buried');
                        
                        $sql = "SELECT " . $select . " FROM " . TABLE_USERACTIVITY . " AS UA LEFT OUTER JOIN " . TABLE_USERS . " AS U ON UA.useract_userid = U.user_id WHERE UA.useract_archived = %s AND UA.useract_status = %s AND UA.useract_id > %d ORDER BY UA.useract_date DESC " . $limit;
                        $query = $h->db->prepare($sql, 'N', 'show', $fromId);
			
                        if ($type == 'query') { return $query; }
			$result = ($type == 'count') ? $h->db->get_var($query) : $h->db->get_results($query);
		} 
		else
		{
                        $select = ($type == 'count') ? 'count(useract_id)' : '*';
			//$sql = "SELECT " . $select . " FROM " . TABLE_USERACTIVITY . " WHERE useract_archived = %s AND useract_status = %s AND useract_userid = %d AND useract_id > %d AND P.post_status <> %s AND P.post_status <> %s ORDER BY useract_date DESC " . $limit;
			//$query = $h->db->prepare($sql, 'N', 'show', $userid, $fromId, 'pending', 'buried');
			
                        $sql = "SELECT " . $select . " FROM " . TABLE_USERACTIVITY . " WHERE useract_archived = %s AND useract_status = %s AND useract_userid = %d AND useract_id > %d ORDER BY useract_date DESC " . $limit;
                        $query = $h->db->prepare($sql, 'N', 'show', $userid, $fromId);
			
                        if ($type == 'query') { return $query; }
			$result = ($type == 'count') ? $h->db->get_var($query) : $h->db->get_results($query);
		}
		
		if ($result) { return $result; } else { return false; }
	}
	
	
	/**
	 * Check if an action already exists
	 *
	 * @param array $args e.g. array('userid'=>4, 'key'=>'post', 'value'=>'6408')
	 * @return bool
	 */
	public function activityExists($h, $args = array())
	{
		$prepare = array();
		$prepare[0] = "temp";
		
		$sql = "SELECT count(useract_id) FROM " . TABLE_USERACTIVITY . " WHERE ";
		
		if (isset($args['userid'])) { $sql = "useract_userid = %d AND "; array_push($prepare, $args['userid']); }
		if (isset($args['key'])) { $sql .= "useract_key = %s AND "; array_push($prepare, $args['key']); }
		if (isset($args['value'])) { $sql .= "useract_value = %s AND "; array_push($prepare, $args['value']); }
		if (isset($args['key2'])) { $sql .= "useract_key2 = %s AND "; array_push($prepare, $args['key2']); }
		if (isset($args['value2'])) { $sql .= "useract_value2 = %s AND "; array_push($prepare, $args['value2']); }
		
		$prepare[0] = rstrtrim($sql, " AND "); // replace "temp" with full $sql and trim trailing "AND"
		
		$count = $h->db->get_var($h->db->prepare($prepare));
		
		return ($count) ? true : false;
	}
	
	
	/**
	 * Insert new activity
	 *
	 * @param array $args e.g. array('userid'=>4, 'key'=>'post', 'value'=>'6408')
	 */
	public function insertActivity($h, $args = array())
	{
		if (isset($args['userid'])) { $userid = $args['userid']; } else { $userid = $h->currentUser->id; }
		if (isset($args['status'])) { $status = $args['status']; } else { $status = 'show'; }
		if (isset($args['key']))    { $key = $args['key'];       } else { $key = ''; }
		if (isset($args['value']))  { $value = $args['value'];   } else { $value = ''; }
		if (isset($args['key2']))   { $key2 = $args['key2'];     } else { $key2 = ''; }
		if (isset($args['value2'])) { $value2 = $args['value2']; } else { $value2 = ''; }
		
		$sql = "INSERT INTO " . TABLE_USERACTIVITY;
		$sql .= " (useract_userid, useract_status, useract_key, useract_value, useract_key2, useract_value2, useract_date, useract_updateby)";
		$sql .= " VALUES (%d, %s, %s, %s, %s, %s, CURRENT_TIMESTAMP, %d)";
		
		$h->db->query($h->db->prepare($sql, $userid, $status, $key, $value, $key2, $value2, $h->currentUser->id));
		
		$h->updateUserLastVisit($userid);
	}
	
	
	/**
	 * Update activity - notice the WHERE array (different to other SiteActivity functions)
	 *
	 * @param array $args e.g. array('userid'=>4, 'where'=>array('key'=>'post', 'value'=>455))
	 */
	public function updateActivity($h, $args = array())
	{
		$prepare = array();
		$prepare[0] = "temp";
		
		// UPDATE .. SET ..
		
		$sql = "UPDATE " . TABLE_USERACTIVITY . " SET ";
		if (isset($args['archived'])) { $sql .= "useract_archived = %s, "; array_push($prepare, $args['archived']); }
		if (isset($args['userid'])) { $sql .= "useract_userid = %s, "; array_push($prepare, $args['userid']); }
		if (isset($args['status'])) { $sql .= "useract_status = %s, "; array_push($prepare, $args['status']); }
		if (isset($args['key'])) { $sql .= "useract_key = %s, "; array_push($prepare, $args['key']); }
		if (isset($args['value'])) { $sql .= "useract_value = %s, "; array_push($prepare, $args['value']); }
		if (isset($args['key2'])) { $sql .= "useract_key2 = %s, "; array_push($prepare, $args['key2']); }
		if (isset($args['value2'])) { $sql .= "useract_value2 = %s, "; array_push($prepare, $args['value2']); }
		$sql .= "useract_updatedts = CURRENT_TIMESTAMP, "; 
		$sql .= "useract_updateby = %d"; array_push($prepare, $h->currentUser->id);
		
		// WHERE ..
		
		if (!isset($args['where'])) { return false; }
		$sql .= " WHERE ";
		
		if (isset($args['where']['archived'])) { $sql .= "useract_archived = %s AND "; array_push($prepare, $args['where']['archived']); }
		if (isset($args['where']['userid'])) { $sql .= "useract_userid = %s AND "; array_push($prepare, $args['where']['userid']); }
		if (isset($args['where']['status'])) { $sql .= "useract_status = %s AND "; array_push($prepare, $args['where']['status']); }
		if (isset($args['where']['key'])) { $sql .= "useract_key = %s AND "; array_push($prepare, $args['where']['key']); }
		if (isset($args['where']['value'])) { $sql .= "useract_value = %s AND "; array_push($prepare, $args['where']['value']); }
		if (isset($args['where']['key2'])) { $sql .= "useract_key2 = %s AND "; array_push($prepare, $args['where']['key2']); }
		if (isset($args['where']['value2'])) { $sql .= "useract_value2 = %s AND "; array_push($prepare, $args['where']['value2']); }
	
		$prepare[0] = rstrtrim($sql, " AND "); // replace "temp" with full $sql 

		$h->db->query($h->db->prepare($prepare));
	}
	

	/**
	 * Remove activity
	 *
	 * @param array $args e.g. array('userid'=>4, 'key'=>'post', 'value'=>'6408')
	 */
	public function removeActivity($h, $args = array())
	{
		if (!isset($args['userid'])) { $args['userid'] = $h->currentUser->id; }
		
		$prepare = array();
		$prepare[0] = "temp";
		
		$sql = "DELETE FROM " . TABLE_USERACTIVITY . " WHERE ";
		if (isset($args['archived'])) { $sql .= "useract_archived = %s AND "; array_push($prepare, $args['archived']); }
		if (isset($args['userid'])) { $sql .= "useract_userid = %s AND "; array_push($prepare, $args['userid']); }
		if (isset($args['status'])) { $sql .= "useract_status = %s AND "; array_push($prepare, $args['status']); }
		if (isset($args['key'])) { $sql .= "useract_key = %s AND "; array_push($prepare, $args['key']); }
		if (isset($args['value'])) { $sql .= "useract_value = %s AND "; array_push($prepare, $args['value']); }
		if (isset($args['key2'])) { $sql .= "useract_key2 = %s AND "; array_push($prepare, $args['key2']); }
		if (isset($args['value2'])) { $sql .= "useract_value2 = %s AND "; array_push($prepare, $args['value2']); }

		$prepare[0] = rstrtrim($sql, " AND "); // replace "temp" with full $sql 

		return $h->db->query($h->db->prepare($prepare));
	}
}
?>
