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

include_once('../../../hotaru_header.php');    // Not the cleanest way of getting to the root...

global $cage, $db, $current_user, $post, $lang, $plugins;

$plugins->includeLanguage('vote');

if ($cage->post->keyExists('post_id')) {
    $post_id = $cage->post->testInt('post_id');
    $user_ip = $cage->post->testIp('user_ip');
    $vote_type = $cage->post->testAlnumLines('type');
    $vote_rating = $cage->post->testAlnumLines('rating');
        
    //get vote settings
    $vote_settings = unserialize($plugins->pluginSettings('vote', 'vote_settings')); 
    
    // Only proceed if the user is logged in OR anonyous votes are allowed
    if ($current_user->getLoggedIn() || ($post->vars['voteAnonymousVotes'] == 'checked')) {
            
        // get vote history for this post:
        
        if ($current_user->getLoggedIn()) {
            $sql = "SELECT vote_rating FROM " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND (vote_user_id = %d OR vote_user_ip = %s AND vote_rating != %s)";
            $voted = $db->get_var($db->prepare($sql, $post_id, $current_user->getId(), $user_ip, 'alert'));
        } else {
            $sql = "SELECT vote_rating FROM " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND vote_user_ip = %s AND vote_rating != %s";
            $voted = $db->get_var($db->prepare($sql, $post_id, $user_ip, 'alert'));
        }
        
        if ($voted == $vote_rating) {
            // Repeat vote. Must be from a double-click. Return false and 
            $json_array = array('result'=>$lang['vote_already_voted']);
            echo json_encode($json_array);
            return false;
        }
        
        if ($current_user->getLoggedIn()) {
            $user_id = $current_user->getId();
        } else {
            $user_id = 0;    // anonymous users get id 0.
        }
         
        if ($vote_rating == 'positive') {
            if (!($voted && $vote_type == 'up_down')) {
                // skip this if we are undoing a vote using up_down voting
                            
                // Get status and positive vote count to determine if the post should be promoted or not...
                $sql = "SELECT post_status, postVotesUp FROM " . TABLE_POSTS . " WHERE post_id = %d";
                $result = $db->get_row($db->prepare($sql, $post_id));
                if (($result->postVotesUp+1) >= $vote_settings['vote_votes_to_promote']) { $post_status = 'top'; } else { $post_status = $result->post_status; }
                
                // Update the post with the new vote count and possible a new status...
                $sql = "UPDATE " . TABLE_POSTS . " SET post_status = %s, postVotesUp=postVotesUp+1 WHERE post_id = %d";
                $db->query($db->prepare($sql, $post_status, $post_id));
            }
            $sql = "INSERT INTO " . TABLE_POSTVOTES . " (vote_post_id, vote_user_id, vote_user_ip, vote_date, vote_type, vote_rating, vote_updateby) VALUES (%d, %d, %s, CURRENT_TIMESTAMP, %s, %s, %d)";
            $db->query($db->prepare($sql, $post_id, $user_id, $user_ip, $vote_type, $vote_rating, $user_id));
            
            // Remove negative vote, i.e. undo a vote if the user is changing his/her mind...
            if ($voted && $voted == 'negative') {
                $sql = "UPDATE " . TABLE_POSTS . " SET postVotesDown=postVotesDown-1 WHERE post_id = %d";
                $db->query($db->prepare($sql, $post_id));
                $sql = "DELETE FROM  " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND vote_user_id = %d AND vote_user_ip = %s AND vote_type = %s AND vote_rating = %s";
                $db->query($db->prepare($sql, $post_id, $user_id, $user_ip, $vote_type, $voted));
            }
        } else {
            if (!($voted && $vote_type == 'up_down')) {
                // skip this if we are undoing a vote using up_down voting
                $sql = "UPDATE " . TABLE_POSTS . " SET postVotesDown=postVotesDown+1 WHERE post_id = %d";
                $db->query($db->prepare($sql, $post_id));
            }
            if (!($voted && $vote_type == 'vote_unvote')) {
                // skip this if we are undoing a vote using vote_unvote voting
                $sql = "INSERT INTO " . TABLE_POSTVOTES . " (vote_post_id, vote_user_id, vote_user_ip, vote_date, vote_type, vote_rating, vote_updateby) VALUES (%d, %d, %s, CURRENT_TIMESTAMP, %s, %s, %d)";
                $db->query($db->prepare($sql, $post_id, $user_id, $user_ip, $vote_type, $vote_rating, $current_user->getId()));
            }
            // Remove positive vote, i.e. undo a vote if the user is changing his/her mind...
            if ($voted && $voted == 'positive') {
                $sql = "UPDATE " . TABLE_POSTS . " SET postVotesUp=postVotesUp-1 WHERE post_id = %d";
                $db->query($db->prepare($sql, $post_id));
                $sql = "DELETE FROM  " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND vote_user_id = %d AND vote_user_ip = %s AND vote_type = %s AND vote_rating = %s";
                $db->query($db->prepare($sql, $post_id, $user_id, $user_ip, $vote_type, $voted));
            }
        }
        
        $sql = "SELECT postVotesUp, postVotesDown FROM " . TABLE_POSTS . " WHERE post_id = %d";
        $votes = $db->get_row($db->prepare($sql, $post_id));
        
        if ($vote_type == 'vote_unvote') {
            $json_array = array('votes'=>$votes->postVotesUp);
    
        } elseif ($vote_type == 'up_down') {
            $json_array = array('votes'=>($votes->postVotesUp - $votes->postVotesDown));
    
        } elseif ($vote_type == 'yes_no' && $vote_rating == 'positive') {
            $json_array = array('votes_yes'=>$votes->postVotesUp, 'votes_no'=>$votes->postVotesDown);
        } elseif ($vote_type == 'yes_no' && $vote_rating == 'negative') {
            $json_array = array('votes_no'=>$votes->postVotesDown, 'votes_yes'=>$votes->postVotesUp);
            
        }
        
        echo json_encode($json_array);
    }
}

?>