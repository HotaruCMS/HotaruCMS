<?php
/**
 * Private Messaging functions
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
class PrivateMessaging
{
	protected $to           = '';
	protected $from         = '';
	protected $subject      = '';
	protected $body         = '';
	protected $errors       = array();
	
	/**
	 * Access modifier to set protected properties
	 */
	public function __set($var, $val)
	{
		$this->$var = $val;
	}
	
	
	/**
	 * Access modifier to get protected properties
	 * The & is necessary (http://bugs.php.net/bug.php?id=39449)
	 */
	public function &__get($var)
	{
		return $this->$var;
	}
	
	
	/**
	 * Get Messages
	 *
	 * @param string $box "inbox" or "outbox"
	 * @param string $type blank or "count" or "query"
	 * @return int | array | false
	 */
	public function getMessages($h, $box = 'inbox', $type = '')
	{
		$select = ($type == 'count') ? 'count(*)' : '*';
		
		if ($box == 'inbox') { $direction = "to"; } else { $direction = "from"; }
		
		$sql = "SELECT " . $select . " FROM " . TABLE_MESSAGING . " WHERE message_archived = %s AND message_" . $direction . " = %d AND message_" . $box . " = %d";
		
		if ($type != 'count') { $sql .= " ORDER BY message_date DESC"; }
		
		$query = $h->db->prepare($sql, 'N', $h->currentUser->id, 1);
		
		// if we just want the prepared query, e.g. for pagination, return now
		if ($type == 'query') { return $query; }
		
		// run the query and return either the count or actual results
		$result = ($type == 'count') ? $h->db->get_var($query) : $h->db->get_results($query);
		
		return ($result) ? $result : false;
	}
	
	
	/**
	 * Get Message
	 *
	 * @param int $message_id
	 * @return array
	 */
	public function getMessage($h, $message_id = 0)
	{
		if (!$message_id) { return false; }
		
		$sql = "SELECT * FROM " . TABLE_MESSAGING . " WHERE message_id = %d";
		$message = $h->db->get_row($h->db->prepare($sql, $message_id));
		
		return ($message) ? $message : false;
	}
	 
	 
	/**
	 * Mark message as read
	 *
	 * @param int $message_id
	 */
	public function markRead($h, $message_id = 0)
	{
		if (!$message_id) { return false; }
		
		$sql = "UPDATE " . TABLE_MESSAGING . " SET message_read = %d WHERE message_id = %d";
		$h->db->query($h->db->prepare($sql, 1, $message_id));
	}
	 
	 
	/**
	 * Delete Message
	 *
	 * @param int $message_id
	 * @param string $box "inbox" or "outbox"
	 * @return bool
	 */
	public function deleteMessage($h, $message_id = 0, $box = 'inbox')
	{
		if (!$message_id) { return false; }
		
		$sql = "UPDATE " . TABLE_MESSAGING . " SET message_" . $box . " = %d WHERE message_id = %d";
		return $h->db->query($h->db->prepare($sql, 0, $message_id));
	}
	 
	 
	/**
	 * Send Message
	 *
	 * @param string $to
	 * @param string $from
	 * @param string $subject
	 * @param string $body
	 * @return int | array (int on success, array on failure)
	 */
	public function sendMessage($h, $to = '', $from = '', $subject = '', $body = '')
	{
		// assign values to object
		if ($to) { $this->to = $to; }
		if ($from) { $this->from = $from; }
		if ($subject) { $this->subject = $subject; }
		if ($body) { $this->body = $body; }
		
		// check for errors
		if (!$this->to) { 
			array_push($this->errors, 'no_to'); 
		}
		
		if (!$this->subject) { 
			array_push($this->errors, 'no_subject'); 
		}
		
		if (!$this->body) { 
			array_push($this->errors, 'no_body');
		}
		
		if ($h->userExists(0, $this->to) == "no") { 
			array_push($this->errors, 'no_user'); 
		}
		
		// if no From field, assume current user
		if (!$this->from) { 
			$this->from = $h->currentUser->name;
		}
		
		if (empty($this->errors))
		{
			// save to database
			return $this->saveMessage($h); // returns last insert id
		} else {
			return $this->errors;	// returns errors array
		}
	}
	
	
	/**
	 * Save to database
	 *
	 * @return int - last insert id
	 */
	private function saveMessage($h)
	{
		// we did checks in sendMessage so we know the data is okay, 
		// and this function is private in case anyone tries to use it directly
		
		// get ids
		$from_id = $h->getUserIdFromName($this->from);  // get the ID of the sender
		$to_id = $h->getUserIdFromName($this->to);  // get the ID of the recipient
		
		// SQL
		$sql = "INSERT INTO " . TABLE_MESSAGING;
		$sql .= " (message_from, message_to, message_date, message_subject, message_content, message_updateby) ";
		$sql .= "VALUES(%d, %d, CURRENT_TIMESTAMP, %s, %s, %d)";
		
		// prepare the query
		$query = $h->db->prepare($sql, $from_id, $to_id, urlencode($this->subject), urlencode($this->body), $h->currentUser->id);
		
		// save to database
		$h->db->query($query);
		
		// get last insert id (appended to link in email notification)
		$this->id = $h->db->get_var($h->db->prepare("SELECT LAST_INSERT_ID()"));
		return $this->id;
	}
}
?>
