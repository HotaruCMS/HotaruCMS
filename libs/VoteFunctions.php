<?php
/**
 * The VoteFunctions class handles database calls needed for voting
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

class VoteFunctions
{
	/**
	 * Get Individual Vote Rating 
	 *
	 * @param int $post_id
	 * @param int $user_id
	 * @param string $ip
	 * @param bool $anon
	 * @return int - vote rating e.g. 10, -10
	 */
	public function getVoteRating($h, $post_id = 0, $user_id = 0, $ip = '', $anon = FALSE)
	{
		if ($anon && !$h->currentUser->loggedIn)
		{
			/*  include user_id = 0 since if registered user votes after anon at same ip, 
				we dont want to delete both votes later if anon user unvotes*/
			$user_id = 0;
                                                        
                        if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50300 || !ACTIVERECORD) {
                            $sql = "SELECT vote_rating FROM " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND vote_user_id = %d AND vote_user_ip = %s AND vote_rating != %d LIMIT 1";
                            $voted = $h->db->get_var($h->db->prepare($sql, $post_id, $user_id, $ip, -999)); // exclude flags                            
                        } else {
                            $voted = models___Postvotes::find('first', array(
                                'select' => 'vote_rating',
                                'conditions' => array('vote_post_id=? and vote_user_id=? and vote_user_ip=? and vote_rating != ?', $post_id, $user_id, $ip, -999)
                            ));
                        }
		}
		else 
		{
			// Only proceed if the user is logged in
			if (!$h->currentUser->loggedIn) { return FALSE; }
			if (!$user_id) { $user_id = $h->currentUser->id; }
		
			// get vote history for this post:                        
                        if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50300 || !ACTIVERECORD) {
                            $sql = "SELECT vote_rating FROM " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND vote_user_id = %d AND vote_rating != %d LIMIT 1";
                            $voted = $h->db->get_var($h->db->prepare($sql, $post_id, $user_id, -999)); // exclude flags
                        } else {
                            $voted = models___Postvotes::find('first', array(
                                'select' => 'vote_rating',
                                'conditions' => array('vote_post_id=? and vote_user_id=? and and vote_rating != ?', $post_id, $user_id, -999)
                            ));
                        }    
		}

		return ($voted) ? $voted : FALSE;
	}


	/**
	 * Get Post Vote Info
	 *
	 * @param int $post_id
	 * @return array|false
	 */
	public function getPostVoteInfo($h, $post_id = 0)
	{
		if (!$post_id) { return FALSE; }

                if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50300 || !ACTIVERECORD) {
                    $sql = "SELECT post_votes_up, post_votes_down, post_status, post_date FROM " . TABLE_POSTS . " WHERE post_id = %d LIMIT 1";
                    $info = $h->db->get_row($h->db->prepare($sql, $post_id));
                } else {
                    $info = models___Postvotes::find('first', array(
                        'select' => 'post_votes_up, post_votes_down, post_status, post_date',
                        'conditions' => array('vote_post_id=?', $post_id)
                     ));                
                }

		return ($info) ? $info : FALSE;
	}


	/**
	 * Update Post Vote Info
	 *
	 * @param int $post_id
	 * @param int $post_votes_up - either -1, 0 or 1
	 * @param int $post_votes_down - either -1, 0 or 1
	 * @param string $post_status
	 * @param bool $pub_date - set to TRUE to update to current time
	 * @return bool
	 */
	public function updatePostVoteInfo($h, $post_id = 0, $post_votes_up = 0, $post_votes_down = 0, $post_status = '', $pub_date = FALSE)
	{
		$post_status_sql = ($post_status) ? "post_status = %s" : "";
		$post_pub_date = ($pub_date) ? "post_pub_date = CURRENT_TIMESTAMP" : "";
		
		// Increment or decrement the UP votes
		switch ($post_votes_up) {
			case 1:
				$post_votes_up = "post_votes_up = post_votes_up + 1";
				break;
			case -1:
				$post_votes_up = "post_votes_up = post_votes_up - 1";
				break;
			default:
				$post_votes_up = "";
				break;
		}

		// Increment or decrement the DOWN votes
		switch ($post_votes_down) {
			case 1:
				$post_votes_down = "post_votes_down = post_votes_down + 1";
				break;
			case -1:
				$post_votes_down = "post_votes_down = post_votes_down - 1";
				break;
			default:
				$post_votes_down = "";
				break;
		}

		$set = array($post_status_sql, $post_pub_date, $post_votes_up, $post_votes_down);
		$set = array_filter($set); // remove blanks
		$set_string = implode(', ', $set);
		if (!$set_string) { return FALSE; }

		$sql = "UPDATE " . TABLE_POSTS . " SET " . $set_string . " WHERE post_id = %d";

		if ($post_status ) { 
			$h->db->query($h->db->prepare($sql, $post_status, $post_id));
		} else {
			$h->db->query($h->db->prepare($sql, $post_id));
		}

		return TRUE;
	}


	/**
	 * Add Vote to PostVotes table
	 *
	 * @param int $post_id
	 * @param int $user_id
	 * @param string $ip
	 * @param int $rating
	 * @param string $type - usually the plugin name
	 */
	public function addVote($h, $post_id = 0, $user_id = 0, $ip = '', $rating = 0, $type = 'vote')
	{
		if (!$post_id || !$rating) { return FALSE; }

        $sql = "INSERT INTO " . TABLE_POSTVOTES . " (vote_post_id, vote_user_id, vote_user_ip, vote_date, vote_type, vote_rating, vote_updateby) VALUES (%d, %d, %s, CURRENT_TIMESTAMP, %s, %d, %d)";
        $h->db->query($h->db->prepare($sql, $post_id, $user_id, $ip, $type, $rating, $user_id));
	}


	/**
	 * Delete Vote from PostVotes table
	 *
	 * @param int $post_id
	 * @param int $user_id
	 * @param int $rating
	 * @param string $ip
	 * @param bool $anon
	 */
	public function deleteVote($h, $post_id = 0, $user_id = 0, $rating = 0, $ip = '', $anon = FALSE)
	{
		if (!$post_id || !$rating) { return FALSE; }

		if ($anon) 
		{
			if (!$ip) { return FALSE; } // anonymous users MUST provide an IP

			$sql = "DELETE FROM  " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND vote_user_id = %d AND vote_user_ip = %s AND vote_rating = %d";
			$h->db->query($h->db->prepare($sql, $post_id, 0, $ip, $rating));
		}
		else 
		{
			$sql = "DELETE FROM  " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND vote_user_id = %d AND vote_rating = %d";
			$h->db->query($h->db->prepare($sql, $post_id, $user_id, $rating));
		}
	}


	/**
	 * Count votes by a user
	 *
	 * @param string $type - 'all', 'pos', 'neg', 'flags' (flags not included in 'all')
	 * @param int $user_id
	 * return int|false
	 */
	public function countUserVotes($h, $type = 'all', $user_id = 0)
	{
		if (!$user_id) { $user_id = $h->currentUser->id; }

		switch ($type) {
			case 'pos':
				$rating = "vote_rating > %d";
				$vote_rating = 0;
				break;
			case 'neg':
				$rating = "vote_rating < %d";
				$vote_rating = 0;
				break;
			case 'flags':
				$rating = "vote_rating = %d";
				$vote_rating = -999;
				break;
			default:
				$rating = "vote_rating != %d";
				$vote_rating = -999;
		}

                if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50300 || !ACTIVERECORD) {
                    $sql = "SELECT count(vote_rating) FROM " . TABLE_POSTVOTES . " WHERE vote_user_id = %d AND " . $rating;
                    $votes = $h->db->get_var($h->db->prepare($sql, $user_id, $vote_rating));
                } else {
                    $votes = models___Postvotes::count_by_vote_user_id_and_rating($user_id, $vote_rating);
                }                		

		return ($votes) ? $votes : FALSE;
	}
}

?>