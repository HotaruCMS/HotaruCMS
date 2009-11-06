<?php
/**
 * file: plugins/vote/functions.php
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
require_once('../../../libs/Hotaru.php');    // Not the cleanest way of getting to the root...

$hotaru = new Hotaru('no_template');  // the constructor includes everything we need.

$hotaru->plugins->includeLanguage('vote_simple', 'vote_simple');

if ($hotaru->cage->post->keyExists('post_id')) {
    $post_id = $hotaru->cage->post->testInt('post_id');
    $user_ip = $hotaru->cage->post->testIp('user_ip');
    $vote_rating = $hotaru->cage->post->testAlnumLines('rating');
        
    //get vote settings
    $vote_settings = unserialize($hotaru->plugins->getSetting('vote_settings', 'vote_simple')); 
    
    // Only proceed if the user is logged in OR anonyous votes are allowed
    if ($hotaru->current_user->loggedIn) {
            
        $user_id = $hotaru->current_user->id;
        
        // get vote history for this post:
        
        $sql = "SELECT vote_rating FROM " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND vote_user_id = %d AND vote_rating != %s";
        $voted = $hotaru->db->get_var($hotaru->db->prepare($sql, $post_id, $user_id, 'alert'));
        
        if ($voted == $vote_rating) {
            // Repeat vote. Must be from a double-click. Return false and 
            $json_array = array('result'=>$hotaru->lang['vote_already_voted']);
            echo json_encode($json_array);
            return false;
        }
        
        if ($vote_rating == 'positive')
        {
            // get current vote count and status
            $sql = "SELECT post_votes_up, post_status, post_date FROM " . TABLE_POSTS . " WHERE post_id = %d";
            $result = $hotaru->db->get_row($hotaru->db->prepare($sql, $post_id));
            
            // Change the status to 'top' if we have enough votes and are within the time limit to hit the front page:
            $front_page_deadline = "-" . $vote_settings['vote_no_front_page'] . " days"; // default: -5 days
            $sql_deadline = date('YmdHis', strtotime($front_page_deadline)); // should be negative
            if ((($result->post_votes_up + 1) >= $vote_settings['vote_votes_to_promote'])
                && ($result->post_date >= $sql_deadline)) { $post_status = 'top'; } else { $post_status = $result->post_status; }
            
            // Update Posts table
            $sql = "UPDATE " . TABLE_POSTS . " SET post_status = %s, post_votes_up = post_votes_up + 1 WHERE post_id = %d";
            $hotaru->db->query($hotaru->db->prepare($sql, $post_status, $post_id));
                
            // Update Postvotes table
            $sql = "INSERT INTO " . TABLE_POSTVOTES . " (vote_post_id, vote_user_id, vote_user_ip, vote_date, vote_type, vote_rating, vote_updateby) VALUES (%d, %d, %s, CURRENT_TIMESTAMP, %s, %s, %d)";
            $hotaru->db->query($hotaru->db->prepare($sql, $post_id, $user_id, $user_ip, 'vote_simple', $vote_rating, $user_id));
            
            $hotaru->plugins->pluginHook('vote_positive_vote', true, '', array('user' => $user_id, 'post'=>$post_id));
        } 
        else // negative vote
        {
            // REMOVE POSITIVE VOTE, i.e. undo a vote if the user is changing his/her mind...
            if ($voted && $voted == 'positive')
            {
                // Update Posts table
                $sql = "UPDATE " . TABLE_POSTS . " SET post_votes_up=post_votes_up-1 WHERE post_id = %d";
                $hotaru->db->query($hotaru->db->prepare($sql, $post_id));

                // Update Postvotes table
                $sql = "DELETE FROM  " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND vote_user_id = %d AND vote_rating = %s";
                $hotaru->db->query($hotaru->db->prepare($sql, $post_id, $user_id, $voted));
                
                $hotaru->plugins->pluginHook('vote_negative_vote', true, '', array('user' => $user_id, 'post'=>$post_id));
            }
        }
        
        $sql = "SELECT post_votes_up, post_votes_down FROM " . TABLE_POSTS . " WHERE post_id = %d";
        $votes = $hotaru->db->get_row($hotaru->db->prepare($sql, $post_id));
        
        $json_array = array('votes'=>$votes->post_votes_up);
        
        echo json_encode($json_array);
    }
}

?>