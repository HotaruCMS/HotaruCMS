<?php
/**
 * file: plugins/vote/vote_functions.php
 * purpose: Voting functions that are performed behind the scenes with Ajax
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

//$json_array = array('result'=>'test_okay');
//echo json_encode($json_array); exit;

require_once('../../../hotaru_settings.php');
require_once('../../../Hotaru.php');    // Not the cleanest way of getting to the root...

$h = new Hotaru();
$h->start();

$h->includeLanguage('vote', 'vote');

if ($h->cage->post->keyExists('post_id')) {
    $post_id = $h->cage->post->testInt('post_id');
    $vote_rating = $h->cage->post->testInt('rating');
    $user_ip = $h->cage->server->testIp('REMOTE_ADDR');
    $referer = $h->cage->post->testAlnum('referer');

    //get vote settings
    $vote_settings = unserialize($h->getSetting('vote_settings', 'vote'));
    
    vote($h, $post_id, $vote_rating, $user_ip, $referer, $vote_settings);
}

function vote($h, $post_id, $vote_rating, $user_ip, $referer, $vote_settings) {

    if ($vote_settings['vote_anon_vote'] && !$h->currentUser->loggedIn) {
	$user_id = 0;  // include user_id = 0 since if regd user votes after anon at same ip, we dont want to delete both votes later if anon user unvotes
	$sql = "SELECT vote_rating FROM " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND vote_user_id = %d AND vote_user_ip = %s AND vote_rating != %d";
	$voted = $h->db->get_var($h->db->prepare($sql, $post_id, $user_id, $user_ip, -999));
    } else {
	// Only proceed if the user is logged in
	if (!$h->currentUser->loggedIn) { return false; }
	$user_id = $h->currentUser->id;

	// get vote history for this post:
	$sql = "SELECT vote_rating FROM " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND vote_user_id = %d AND vote_rating != %d";
	$voted = $h->db->get_var($h->db->prepare($sql, $post_id, $user_id, -999));
    }

    if ($voted == $vote_rating) {
        // Repeat vote. Must be from a double-click. Return false and
        $json_array = array('result'=>$h->lang['vote_already_voted']);
        echo json_encode($json_array);
        return false;
    }

    // get current vote count and status
    $sql = "SELECT post_votes_up, post_status, post_date FROM " . TABLE_POSTS . " WHERE post_id = %d";
    $result = $h->db->get_row($h->db->prepare($sql, $post_id));

    if ($vote_rating > 0)
    {
        // Change the status to 'top' if we have enough votes and are within the time limit to hit the front page:
        $front_page_deadline = "-" . $vote_settings['no_front_page'] . " days"; // default: -5 days
        $sql_deadline = date('Y-m-d H:i:s', strtotime($front_page_deadline)); // should be negative
        if ((($result->post_votes_up + 1) >= $vote_settings['votes_to_promote'])
            && ($result->post_date >= $sql_deadline) && $result->post_status != 'top') { 
            $post_status = 'top'; 
            $sql = "UPDATE " . TABLE_POSTS . " SET post_status = %s, post_pub_date = CURRENT_TIMESTAMP, post_votes_up = post_votes_up + 1 WHERE post_id = %d";
        } else { 
            $post_status = $result->post_status; 
            $sql = "UPDATE " . TABLE_POSTS . " SET post_status = %s, post_votes_up = post_votes_up + 1 WHERE post_id = %d";
        }

        // Update Posts table
        $h->db->query($h->db->prepare($sql, $post_status, $post_id));


        // Update Postvotes table
        $sql = "INSERT INTO " . TABLE_POSTVOTES . " (vote_post_id, vote_user_id, vote_user_ip, vote_date, vote_type, vote_rating, vote_updateby) VALUES (%d, %d, %s, CURRENT_TIMESTAMP, %s, %d, %d)";
        $h->db->query($h->db->prepare($sql, $post_id, $user_id, $user_ip, 'vote', $vote_rating, $user_id));

        $h->pluginHook('vote_positive_vote', '', array('user' => $user_id, 'post'=>$post_id));
    }
    else // negative vote
    {
        // REMOVE POSITIVE VOTE, i.e. undo a vote if the user is changing his/her mind...
        if ($voted && $voted > 0)
        {
            // Update Posts table
            $sql = "UPDATE " . TABLE_POSTS . " SET post_votes_up=post_votes_up-1 WHERE post_id = %d";
            $h->db->query($h->db->prepare($sql, $post_id));

            // Change status to "new" if demoting a post
            if ($vote_settings['use_demote'] && (($result->post_votes_up - 1) < $vote_settings['votes_to_promote'])) {
                $sql = "UPDATE " . TABLE_POSTS . " SET post_status = %s WHERE post_id = %d";
                $h->db->query($h->db->prepare($sql, 'new', $post_id));
            }

	    // Update Postvotes table
	    if ($vote_settings['vote_anon_vote'] && !$h->currentUser->loggedIn) {
		$sql = "DELETE FROM  " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND vote_user_id = %d AND vote_user_ip = %s AND vote_rating = %d";
		$h->db->query($h->db->prepare($sql, $post_id, 0, $user_ip, $voted));
	    } else {
		$sql = "DELETE FROM  " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND vote_user_id = %d AND vote_rating = %d";
		$h->db->query($h->db->prepare($sql, $post_id, $user_id, $voted));
	    }

            $h->pluginHook('vote_negative_vote', '', array('user' => $user_id, 'post'=>$post_id));
        }
    }

    if ($referer == "link") {
       // $h->readPost($post_id);
       // header("Location: " . $h->post->origUrl);
       }

    $sql = "SELECT post_votes_up, post_votes_down FROM " . TABLE_POSTS . " WHERE post_id = %d";
    $votes = $h->db->get_row($h->db->prepare($sql, $post_id));

    $json_array = array('votes'=>$votes->post_votes_up);

    echo json_encode($json_array);
}

?>