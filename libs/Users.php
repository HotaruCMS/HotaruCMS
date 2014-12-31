<?php
/**
 * Users functions
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
namespace Libs;

class Users extends Prefab
{
/**
	 * Get the latest site activity
	 *
	 * @param int $limit
	 * @param int $userid
	 * @param string $type blank or "count" or "query"
	 * @return array|false
	 */
	public function getUsers($h, $limitCount = 0, $type = '', $fromId = 0)
	{
		$limit = (!$limitCount) ? '' : "LIMIT " . $limitCount;
		
                $select = ($type == 'count') ? 'count(user_id)' : 'U.* '; //user_id, U.user_username, U.user_email, U.user_role, U.user_date ';
                //$sql = "SELECT " . $select . " FROM " . TABLE_USERACTIVITY . " AS UA LEFT OUTER JOIN " . TABLE_USERS . " AS U ON UA.useract_userid = U.user_id WHERE UA.useract_archived = %s AND UA.useract_status = %s AND UA.useract_id > %d AND P.post_status <> %s AND P.post_status <> %s ORDER BY UA.useract_date DESC " . $limit;
                //$query = $h->db->prepare($sql, 'N', 'show', $fromId, 'pending', 'buried');

                $sql = "SELECT " . $select . " FROM " . TABLE_USERS . " AS U WHERE U.user_role <> %s AND U.user_role <>%s AND U.user_role <>%s AND U.user_role <>%s AND U.user_role <>%s ORDER BY U.user_date ASC " . $limit;
                $query = $h->db->prepare($sql, 'N', 'pending', 'suspend', 'banned', 'killspammed');

                if ($type == 'query') { return $query; }
                $result = ($type == 'count') ? $h->db->get_var($query) : $h->db->get_results($query);

		if ($result) { return $result; } else { return false; }
	}
}
