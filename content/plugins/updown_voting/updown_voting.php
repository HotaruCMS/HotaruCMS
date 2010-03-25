<?php
/**
 * name: Up Down Voting
 * description: Adds voting ability to posted stories.
 * version: 0.2
 * folder: updown_voting
 * class: UpdownVoting
 * type: vote
 * requires: submit 1.9, users 1.1, sb_base 0.4
 * hooks: install_plugin, theme_index_top, post_read_post, header_include, sb_base_show_post_title, sb_base_pre_show_post, admin_plugin_settings, admin_sidebar_plugin_settings, post_add_post, submit_confirm_pre_trackback, sb_base_show_post_extra_fields, sb_base_show_post_extras, post_delete_post
 * author: Nick Ramsay
 * authorurl: http://hotarucms.org/member.php?1-Nick
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

class UpdownVoting
{
    /**
     * Add vote fields to the post table and make a dedicated Votes table.
     */
    public function install_plugin($h)
    {
        // Default settings
        $updown_voting_settings = $h->getSerializedSettings();
        if (!isset($updown_voting_settings['submit_vote'])) { $updown_voting_settings['submit_vote'] = "checked"; }
        if (!isset($updown_voting_settings['submit_vote_value'])) { $updown_voting_settings['submit_vote_value'] = 1; }
        if (!isset($updown_voting_settings['votes_to_promote'])) { $updown_voting_settings['votes_to_promote'] = 5; }
        if (!isset($updown_voting_settings['use_demote'])) { $updown_voting_settings['use_demote'] = ""; }
        if (!isset($updown_voting_settings['use_alerts'])) { $updown_voting_settings['use_alerts'] = "checked"; }
        if (!isset($updown_voting_settings['alerts_to_bury'])) { $updown_voting_settings['alerts_to_bury'] = 5; }
        if (!isset($updown_voting_settings['physical_delete'])) { $updown_voting_settings['physical_delete'] = ""; }
        if (!isset($updown_voting_settings['upcoming_duration'])) { $updown_voting_settings['upcoming_duration'] = 5; }
        if (!isset($updown_voting_settings['no_front_page'])) { $updown_voting_settings['no_front_page'] = 5; }
        if (!isset($updown_voting_settings['posts_widget'])) { $updown_voting_settings['posts_widget'] = 'checked'; }
        
        $h->updateSetting('updown_voting_settings', serialize($updown_voting_settings));
    }  
    
    
    /**
     * Determine if we're using alerts or not
     */
    public function theme_index_top($h)
    {
        $updown_voting_settings = $h->getSerializedSettings();
        $h->vars['useAlerts'] = $updown_voting_settings['use_alerts'];
    }
    
    
    /**
     * Read number of votes if post exists.
     */
    public function post_read_post($h)
    {
        if (!isset($h->post->vars['post_row'])) { return false; }
        
        $post_row = $h->post->vars['post_row'];
        $h->vars['votesUp'] = $post_row->post_votes_up;
    }
    
    
     /**
     * ********************************************************************* 
     * *********************** FUNCTIONS FOR VOTING ************************ 
     * *********************************************************************
     * ****************************************************************** */
     
     
    /**
     * If auto-vote is enabled, the new post is automatically voted for by the person who submitted it.
     */
    public function post_add_post($h)
    {
         //get vote settings
        $updown_voting_settings = $h->getSerializedSettings('updown_voting'); 
        $submit_vote = $updown_voting_settings['submit_vote'];
        $submit_vote_value = $updown_voting_settings['submit_vote_value'];
        
        // Automatically vote for a post when it's submitted...
        if ($submit_vote == 'checked') {
            
            //update the vote count
            $sql = "UPDATE " . TABLE_POSTS . " SET post_votes_up=post_votes_up+%d WHERE post_id = %d";
            $h->db->query($h->db->prepare($sql, $submit_vote_value, $h->post->id));
        
            //Insert one vote for each of $submit_vote_value;
            for ($i=0; $i<$submit_vote_value; $i++) {
                $sql = "INSERT INTO " . TABLE_POSTVOTES . " (vote_post_id, vote_user_id, vote_user_ip, vote_date, vote_type, vote_rating, vote_updateby) VALUES (%d, %d, %s, CURRENT_TIMESTAMP, %s, %s, %d)";
                $h->db->query($h->db->prepare($sql, $h->post->id, $h->currentUser->id, $h->cage->server->testIp('REMOTE_ADDR'), 'vote', 10, $h->currentUser->id));
            }
        }            
                    
    }
    
    
    /**
     * Check if auto-vote on submission can push the story to the front page
     */
    public function submit_confirm_pre_trackback($h)
    {
        // get settings (cached at this point)
        $updown_voting_settings = $h->getSerializedSettings('updown_voting'); 
        
        // get current vote count and status
        $sql = "SELECT post_votes_up, post_status FROM " . TABLE_POSTS . " WHERE post_id = %d";
        $result = $h->db->get_row($h->db->prepare($sql, $h->post->id));
        
        // check if the automatically added votes are enough to immediately push the story to Top Stories
        // only do this if the status is "new"
        if ((($result->post_votes_up) >= $updown_voting_settings['votes_to_promote']) 
            && $result->post_status == 'new') 
        { 
            $post_status = 'top'; 
            $h->vars['submit_redirect'] = BASEURL; // so we can redirect to the home page instead of Latest
        } else { 
            $post_status = $result->post_status;
        }
        
        //update the post status
        $sql = "UPDATE " . TABLE_POSTS . " SET post_status = %s WHERE post_id = %d";
        $h->db->query($h->db->prepare($sql, $post_status, $h->post->id));
    }
     
    
     /**
     * Displays the vote button.
     */
    public function sb_base_pre_show_post($h)
    {
        $h->vars['flagged'] = false;
        if ($h->post->status == 'new' && $h->vars['useAlerts'] == "checked") {
            // CHECK TO SEE IF THIS POST IS BEING FLAGGED AND IF SO, ADD IT TO THE DATABASE
            if ($h->cage->get->keyExists('alert') && $h->currentUser->loggedIn) {
                // Check if already flagged...
                $sql = "SELECT vote_rating FROM " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND vote_user_id = %d AND vote_rating = %d";
                $flagged = $h->db->get_var($h->db->prepare($sql, $h->post->id, $h->currentUser->id, -999));
                if (!$flagged) {
                    $sql = "INSERT INTO " . TABLE_POSTVOTES . " (vote_post_id, vote_user_id, vote_user_ip, vote_date, vote_type, vote_rating, vote_reason, vote_updateby) VALUES (%d, %d, %s, CURRENT_TIMESTAMP, %s, %d, %d, %d)";
                    $h->db->query($h->db->prepare($sql, $h->post->id, $h->currentUser->id, $h->cage->server->testIp('REMOTE_ADDR'), 'vote', -999, $h->cage->get->testInt('alert'), $h->currentUser->id));
                    
                    $h->pluginHook('vote_flag_insert');
                }
                else
                {
                    $h->messages[$h->lang["vote_alert_already_flagged"]] = "red";
                }
            }
            
            // CHECK TO SEE IF THIS POST HAS BEEN FLAGGED AND IF SO, SHOW THE ALERT STATUS
        
            // Get settings from the database if they exist...
            $updown_voting_settings = unserialize($h->getSetting('updown_voting_settings')); 
            
            // Check if already flagged...
            $sql = "SELECT * FROM " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND vote_rating = %d";
            $flagged = $h->db->get_results($h->db->prepare($sql, $h->post->id, -999));
            if ($flagged) {
                $h->vars['flag_count'] = 0;
                $h->vars['reasons'] = array();
                foreach ($flagged as $flag) {
                    array_push($h->vars['reasons'], $flag->vote_reason);
                    $h->vars['flag_count']++;
                }
                
                // Buries or Deletes a post if this new flag sends it over the limit set in Vote Settings
                if ($h->cage->get->keyExists('alert') && $h->vars['flag_count'] >= $updown_voting_settings['alerts_to_bury'])
                {
                    $h->readPost($h->post->id); //make sure we've got all post details
                    
                    if ($updown_voting_settings['physical_delete']) { 
                        $h->deletePost(); // Akismet uses those details to report the post as spam
                    } else {
                        $h->changePostStatus('buried');
                        $h->pluginHook('vote_post_status_buried'); // Akismet hooks in here to report the post as spam
                    }
                    
                    $h->messages[$h->lang["vote_alert_post_buried"]] = "red";
                }
                
                $h->vars['flagged'] = true;
            }
        }
        
        
        // CHECK TO SEE IF THE CURRENT USER HAS VOTED FOR THIS POST
         if ($h->currentUser->loggedIn) {
            $sql = "SELECT vote_rating FROM " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND vote_user_id = %d AND vote_rating != %d ORDER BY vote_updatedts DESC LIMIT 1";
            $h->vars['voted'] = $h->db->get_var($h->db->prepare($sql, $h->post->id, $h->currentUser->id, -999));
        } 

        // determine where to return the user to after logging in:
        if (!$h->cage->get->keyExists('return')) {
            $host = $h->cage->server->sanitizeTags('HTTP_HOST');
            $uri = $h->cage->server->sanitizeTags('REQUEST_URI');
            $return = 'http://' . $host . $uri;
            $return = urlencode(htmlentities($return,ENT_QUOTES,'UTF-8'));
        } else {
            $return = $h->cage->get->testUri('return'); // use existing return parameter
        }
        $h->vars['vote_login_url'] = BASEURL . "index.php?page=login&amp;return=" . $return;
        $h->displayTemplate('updown_voting_button', 'updown_voting', false);
    }
    
    
     /**
     * Displays the flags next to the post title.
     */
    public function sb_base_show_post_title($h)
    {
        if (!isset($h->vars['flagged']) || !$h->vars['flagged']) { return false; }
        
        $why_list = "";
        foreach ($h->vars['reasons'] as $why) {
            $alert_lang = "vote_alert_reason_" . $why;
            if (isset($h->lang[$alert_lang])) {
                $why_list .= $h->lang[$alert_lang] . ", ";
            }
        }
        $why_list = rstrtrim($why_list, ", ");    // removes trailing comma

        // $h->vars['flag_count'] got from above function
        $h->vars['flag_why'] = $why_list;
        $h->displayTemplate('updown_voting_alert', 'updown_voting', false);
    }


     /**
     * Add an "alert" link below the story
     */
    public function sb_base_show_post_extra_fields($h)
    {
        // Only show the Alert link ("Flag it") on new posts, not top stories
        if ($h->currentUser->loggedIn && $h->post->status == "new" && ($h->vars['useAlerts'] == "checked")) {
            echo "<li><a class='alert_link' href='#'>" . $h->lang["vote_alert"]  . "</a></li>";
        }
    }
    
    
     /**
     * List of alert reasons to choose from.
     */
    public function sb_base_show_post_extras($h)
    {
        if ($h->post->status == "new" && ($h->vars['useAlerts'] == "checked")) {
            echo "<div class='alert_choices' style='display: none;'>";
                echo $h->lang["vote_alert_reason_title"] . "<br />";
                echo "<ul>";
                echo "<li><a href='" . $h->url(array('page'=>$h->post->id, 'alert'=>1)) . "'>" . $h->lang["vote_alert_reason_1"]  . "</a></li>\n";
                echo "<li><a href='" . $h->url(array('page'=>$h->post->id, 'alert'=>2)) . "'>" . $h->lang["vote_alert_reason_2"]  . "</a></li>\n";
                echo "<li><a href='" . $h->url(array('page'=>$h->post->id, 'alert'=>3)) . "'>" . $h->lang["vote_alert_reason_3"]  . "</a></li>\n";
                echo "<li><a href='" . $h->url(array('page'=>$h->post->id, 'alert'=>4)) . "'>" . $h->lang["vote_alert_reason_4"]  . "</a></li>\n";
                echo "<li><a href='" . $h->url(array('page'=>$h->post->id, 'alert'=>5)) . "'>" . $h->lang["vote_alert_reason_5"]  . "</a></li>\n";
                echo "<li><a href='" . $h->url(array('page'=>$h->post->id, 'alert'=>6)) . "'>" . $h->lang["vote_alert_reason_6"]  . "</a></li>\n";
                echo "</ul>";
            echo "</div>";
        }
    }
    
    
    /**
     * Delete votes when post deleted
     */
    public function post_delete_post($h)
    {
        $sql = "DELETE FROM " . TABLE_POSTVOTES . " WHERE vote_post_id = %d";
        $h->db->query($h->db->prepare($sql, $h->post->id));
    }
}

?>
