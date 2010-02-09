<?php
/**
 * file: plugins/comment_voting/comment_voting_functions.php
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

if ($h->cage->post->keyExists('comment_id')) {
    $user_ip = $h->cage->post->testIp('user_ip');
    $post_id = $h->cage->post->testInt('post_id');
    $comment_id = $h->cage->post->testInt('comment_id');
    $cvote_rating = $h->cage->post->testInt('rating');
        
    //get comment_voting settings
    $comments_settings = $h->getSerializedSettings('comments');
    if (isset($comments_settings) && isset($comments_settings['comment_bury'])) {
        $bury = $comments_settings['comment_bury'];
    }
    
    // Only proceed if the user is logged in
    if ($h->currentUser->loggedIn) {
            
        $user_id = $h->currentUser->id;
        
        // get comment_voting history for this comment:
        $sql = "SELECT cvote_rating FROM " . TABLE_COMMENTVOTES . " WHERE cvote_comment_id = %d AND cvote_user_id = %d";
        $comment_voting = $h->db->get_var($h->db->prepare($sql, $comment_id, $user_id));
        
        if ($comment_voting == $cvote_rating) {
            // Repeat comment vote. Must be from a double-click. Return false and 
            $json_array = array('result'=>$h->lang['comment_voting_already_voted']);
            echo json_encode($json_array);
            return false;
        }
        
        // get current status and down votes
        $sql = "SELECT comment_votes_down, comment_status FROM " . TABLE_COMMENTS . " WHERE comment_id = %d";
        $c_row = $h->db->get_row($h->db->prepare($sql, $comment_id));
            
        if ($cvote_rating > 0)
        {
            // Update comments table
            $sql = "UPDATE " . TABLE_COMMENTS . " SET comment_votes_up = comment_votes_up + 1 WHERE comment_id = %d";
            $h->db->query($h->db->prepare($sql, $comment_id));
                
            // Update commentvotes table
            $sql = "INSERT INTO " . TABLE_COMMENTVOTES . " (cvote_post_id, cvote_comment_id, cvote_user_id, cvote_user_ip, cvote_date, cvote_rating, cvote_updateby) VALUES (%d, %d, %d, %s, CURRENT_TIMESTAMP, %d, %d)";
            $h->db->query($h->db->prepare($sql, $post_id, $comment_id, $user_id, $user_ip, $cvote_rating, $user_id));
            
            $h->pluginHook('comment_voting_funcs_positive', '', array('user' => $user_id, 'comment'=>$comment_id));
        } 
        else // negative comment_voting
        {
            if ($cvote_rating && ($cvote_rating < 0))
            {
                // Increase down votes and set to buried
                if (isset($bury) && ($c_row->comment_votes_down+1 >= $bury) && ($c_row->comment_status != 'buried')) {
                    $sql = "UPDATE " . TABLE_COMMENTS . " SET comment_votes_down=comment_votes_down + 1, comment_status = %s WHERE comment_id = %d";
                    $h->db->query($h->db->prepare($sql, 'buried', $comment_id));
                } else {
                    // Just increase the down votes
                    $sql = "UPDATE " . TABLE_COMMENTS . " SET comment_votes_down=comment_votes_down + 1 WHERE comment_id = %d";
                    $h->db->query($h->db->prepare($sql, $comment_id));
                }

                // Update commentvotes table
                $sql = "INSERT INTO " . TABLE_COMMENTVOTES . " (cvote_post_id, cvote_comment_id, cvote_user_id, cvote_user_ip, cvote_date, cvote_rating, cvote_updateby) VALUES (%d, %d, %d, %s, CURRENT_TIMESTAMP, %d, %d)";
                $h->db->query($h->db->prepare($sql, $post_id, $comment_id, $user_id, $user_ip, $cvote_rating, $user_id));
                
                $h->pluginHook('comment_voting_funcs_negative', '', array('user' => $user_id, 'post'=>$post_id, 'comment'=>$comment_id));
            }
        }
        
        $sql = "SELECT comment_votes_up, comment_votes_down FROM " . TABLE_COMMENTS . " WHERE comment_id = %d";
        $votes = $h->db->get_row($h->db->prepare($sql, $comment_id));
        
        $json_array = array('comments_up'=>$votes->comment_votes_up, 'comments_down'=>$votes->comment_votes_down);
        
        echo json_encode($json_array);
    }
}

?>