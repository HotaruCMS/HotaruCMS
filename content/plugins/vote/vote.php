<?php
/**
 * name: Vote
 * description: Adds voting ability to posted stories.
 * version: 0.3
 * folder: vote
 * class: Vote
 * requires: submit 0.6, users 0.4
 * hooks: install_plugin, hotaru_header, submit_hotaru_header_1, post_read_post_1, post_read_post_2, header_include, submit_pre_show_post, admin_plugin_settings, admin_sidebar_plugin_settings, post_add_post, navigation, submit_show_post_extra_fields, submit_show_post_extras
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

class Vote extends PluginFunctions
{
    /**
     * Add vote fields to the post table and make a dedicated Votes table.
     */
    public function install_plugin() {
        global $db;
        
        // Create a new table column called "post_votes_up" if it doesn't already exist
        $exists = $db->column_exists('posts', 'post_votes_up');
        if (!$exists) {
            $db->query("ALTER TABLE " . TABLE_POSTS . " ADD post_votes_up smallint(11) NOT NULL DEFAULT '0' AFTER post_content");
        } 
        
        // Create a new table column called "post_votes_down" if it doesn't already exist
        $exists = $db->column_exists('posts', 'post_votes_down');
        if (!$exists) {
            $db->query("ALTER TABLE " . TABLE_POSTS . " ADD post_votes_down smallint(11) NOT NULL DEFAULT '0' AFTER post_votes_up");
        } 
        
        // Create a new empty table called "votes" if it doesn't already exist
        $exists = $db->table_exists('postvotes');
        if (!$exists) {
            //echo "table doesn't exist. Stopping before creation."; exit;
            $sql = "CREATE TABLE `" . DB_PREFIX . "postvotes` (
              `vote_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
              `vote_post_id` int(11) NOT NULL DEFAULT '0',
              `vote_user_id` int(11) NOT NULL DEFAULT '0',
              `vote_user_ip` varchar(32) NOT NULL DEFAULT '0',
              `vote_date` timestamp NOT NULL,
              `vote_type` varchar(32) NULL,
              `vote_rating` enum('positive','negative','alert') NULL,
              `vote_reason` tinyint(3) NOT NULL DEFAULT 0,
              `vote_updateby` int(20) NOT NULL DEFAULT 0,
               INDEX  (`vote_post_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Post Votes';";
            $db->query($sql); 
        }   
        
        // Default settings
        
        $vote_settings['vote_vote_unvote'] = "checked";
        $vote_settings['vote_up_down'] = "";
        $vote_settings['vote_yes_no'] = "";
        $vote_settings['vote_anonymous_votes'] = "";
        $vote_settings['vote_submit_vote'] = "checked";
        $vote_settings['vote_submit_vote_value'] = 1;
        $vote_settings['vote_votes_to_promote'] = 5;
        $vote_settings['vote_use_alerts'] = "checked";
        $vote_settings['vote_alerts_to_bury'] = 5;
        $vote_settings['vote_physical_delete'] = "";
        
        // parameters: plugin folder name, setting name, setting value
        $this->updateSetting('vote_settings', serialize($vote_settings));
        
        // Include language file. Also included in hotaru_header, but needed here so 
        // that the link in the Admin sidebar shows immediately after installation.
        $this->includeLanguage();
       
    }  
    
    
    /**
     * Set things up when the page is first loaded
     */
    public function hotaru_header() {
        global $post;
        
        if (!defined('TABLE_POSTVOTES')) { define("TABLE_POSTVOTES", DB_PREFIX . 'postvotes'); }
        
        $this->includeLanguage();    
    }
    
    /**
     * Adds additional member variables when the $post object is read in the Submit plugin
     */
    public function submit_hotaru_header_1() {
        global $post, $hotaru, $cage;
            
        $post->vars['votesUp'] = 0;
        $post->vars['votesDown'] = 0;
        $post->vars['voteAnonymousVotes'] = '';    
        $post->vars['useAlerts'] = 'checked';    
    }
    
    /**
     * Read vote settings
     */
    public function post_read_post_1() {
        global $post;
        
        if ($this->isActive()) { 
        
            // Get settings from the database if they exist...
            $vote_settings = unserialize($this->getSetting('vote_settings')); 
        
            // Determine vote type
            if ($vote_settings['vote_vote_unvote'] == 'checked') {
                $post->vars['voteType'] = "vote_unvote";
            } elseif ($vote_settings['vote_up_down'] == 'checked') {
                $post->vars['voteType'] = "up_down";
            } else {
                $post->vars['voteType'] = "yes_no";
            }
            
            // Enable anonymous voters?
            $post->vars['voteAnonymousVotes'] = $vote_settings['vote_anonymous_votes'];
            
            // Use alerts?
            $post->vars['vote_use_alerts'] = $vote_settings['vote_use_alerts'];
            
            // Prevent Hotaru from merging "top" and "new" posts on the same page:
            $post->setUseLatest(true); 

        }
    }
    
    
    /**
     * Read number of votes if post exists.
     */
    public function post_read_post_2() {
        global $post, $post_row;
        $post->vars['votesUp'] = $post_row->post_votes_up;
        $post->vars['votesDown'] = $post_row->post_votes_down;
    }
    
    
    /**
     * Includes css and javascript for the vote buttons.
     */
    public function header_include()
    {
        global $lang;
        
        $this->includeCss();
        $this->includeJs();
        $this->includeJs('json2.min');
    }
    
    
     /**
     * ********************************************************************* 
     * *********************** FUNCTIONS FOR VOTING ************************ 
     * *********************************************************************
     * ****************************************************************** */
     
     
    /**
     * If auto-vote is enabled, the new post is automatically voted for by the person who submitted it.
     */
    public function post_add_post()
    {
         global $db, $current_user, $post, $cage;
         
         //get vote settings
        $vote_settings = unserialize($this->getSetting('vote_settings')); 
        $submit_vote = $vote_settings['vote_submit_vote'];
        $submit_vote_value = $vote_settings['vote_submit_vote_value'];
        
        // Automatically vote for a post when it's submitted...
        if ($submit_vote == 'checked') {
            // Determine vote type
            if ($vote_settings['vote_vote_unvote'] == 'checked') {
                $post->vars['voteType'] = "vote_unvote";
            } elseif ($vote_settings['vote_up_down'] == 'checked') {
                $post->vars['voteType'] = "up_down";
            } else {
                $post->vars['voteType'] = "yes_no";
            }
            
            //update the vote count
            $sql = "UPDATE " . TABLE_POSTS . " SET post_votes_up=post_votes_up+%d WHERE post_id = %d";
            $db->query($db->prepare($sql, $submit_vote_value, $post->id));
        
            //Insert one vote for each of $submit_vote_value;
            for ($i=0; $i<$submit_vote_value; $i++) {
                $sql = "INSERT INTO " . TABLE_POSTVOTES . " (vote_post_id, vote_user_id, vote_user_ip, vote_date, vote_type, vote_rating, vote_updateby) VALUES (%d, %d, %s, CURRENT_TIMESTAMP, %s, %s, %d)";
                $db->query($db->prepare($sql, $post->id, $current_user->getId(), $cage->server->testIp('REMOTE_ADDR'), $post->vars['voteType'], 'positive', $current_user->getId()));
            }    
        }            
                    
    }
     
    
     /**
     * ********************************************************************* 
     * ******************* FUNCTIONS FOR SHOWING VOTES ********************* 
     * *********************************************************************
     * ****************************************************************** */
     
    
    /**
     * Adds "Top Posts" and "Latest" links to the navigation bar
     *
     * Notes: If you can automatically vote for a story you submit, and that value is equal to or greater than the number of votes you need to get on the Top Stories page, then there's no need for a "Latest" page at all. In that case, we don't add anything to the navigation bar because the "Home" link will show all the stories. HOWEVER, any old posts with "new" status instead of "top" status will become inaccessible.
     */
    public function navigation() {    
        global $lang, $hotaru;
        
        //get vote settings
        $vote_settings = unserialize($this->getSetting('vote_settings')); 
        
        if (($vote_settings['vote_submit_vote'] == "checked") && ($vote_settings['vote_submit_vote_value'] >= $vote_settings['vote_votes_to_promote'])) {
            // these settings make the latest page unnecessary so the "Home" link is sufficient, otherwise...
        } else {
        
            if ($hotaru->getTitle() == 'latest') { $status = "id='navigation_active'"; } else { $status = ""; }
            echo "<li><a " . $status . " href='" . url(array('page'=>'latest')) . "'>" . $lang["vote_navigation_latest"] . "</a></li>\n";
        }
    }
    
     /**
     * Displays the vote button.
     */
    public function submit_pre_show_post() {
        global $hotaru, $db, $post, $current_user, $voted, $cage, $lang;
        
        if ($post->getStatus() == 'new' && $post->vars['vote_use_alerts'] == "checked") {
            // CHECK TO SEE IF THIS POST IS BEING FLAGGED AND IF SO, ADD IT TO THE DATABASE
            if ($cage->get->keyExists('alert') && $current_user->getLoggedIn()) {
                // Check if already flagged...
                $sql = "SELECT vote_rating FROM " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND vote_user_id = %d AND vote_rating = %s";
                $flagged = $db->get_var($db->prepare($sql, $post->getId(), $current_user->getId(), 'alert'));
                if (!$flagged) {
                    $sql = "INSERT INTO " . TABLE_POSTVOTES . " (vote_post_id, vote_user_id, vote_user_ip, vote_date, vote_type, vote_rating, vote_reason, vote_updateby) VALUES (%d, %d, %s, CURRENT_TIMESTAMP, %s, %s, %d, %d)";
                    $db->query($db->prepare($sql, $post->getId(), $current_user->getId(), $cage->server->testIp('REMOTE_ADDR'), $post->vars['voteType'], 'alert', $cage->get->testInt('alert'), $current_user->getId()));
                }
                else
                {
                    $hotaru->message = $lang["vote_alert_already_flagged"];
                    $hotaru->messageType = "red";
                    $hotaru->showMessage();
                }
            }
            
            // CHECK TO SEE IF THIS POST HAS BEEN FLAGGED AND IF SO, SHOW THE ALERT STATUS
        
            // Get settings from the database if they exist...
            $vote_settings = unserialize($this->getSetting('vote_settings')); 
            
            // Check if already flagged...
            $sql = "SELECT * FROM " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND vote_rating = %s";
            $flagged = $db->get_results($db->prepare($sql, $post->id, 'alert'));
            if ($flagged) {
                $flag_count = 0;
                $reasons = array();
                foreach ($flagged as $flag) {
                    array_push($reasons, $flag->vote_reason);
                    $flag_count++;
                }
                
                // Buries or Deletes a post if this new flag sends it over the limit set in Vote Settings
                if ($cage->get->keyExists('alert') && $flag_count >= $vote_settings['vote_alerts_to_bury']) {
                    if ($vote_settings['vote_physical_delete']) { 
                        $sql = "DELETE FROM " . TABLE_POSTVOTES . " WHERE vote_post_id = %d";
                        $db->query($db->prepare($sql, $post->id));
                        $post->delete_post($post->id); 
                    } else {
                        $post->change_status('buried');
                    }
                    
                    $hotaru->message = $lang["vote_alert_post_buried"];
                    $hotaru->messageType = "red";
                    $hotaru->showMessage();
                    return true; // This will stop the post from showing    
                }
                
                if ($flag_count > 1) { 
                    echo "<p class='alert_message'>" . $lang["vote_alert_flagged_message_1"] . " " . $flag_count . " " . $lang["vote_alert_flagged_message_users"]  . " " . $lang["vote_alert_flagged_message_2"] . " <i>";
                } else {
                    echo "<p class='alert_message'>" . $lang["vote_alert_flagged_message_1"] . " " . $flag_count . " " . $lang["vote_alert_flagged_message_user"]  . " " . $lang["vote_alert_flagged_message_2"] . " <i>";
                }
                
                $why_list = "";
                foreach ($reasons as $why) {
                    $alert_lang = "vote_alert_reason_" . $why;
                    $why_list .= $lang[$alert_lang] . ", ";
                }
                $why_list = rstrtrim($why_list, ", ");    // removes trailing comma
                echo $why_list . "</i></p>";
            }
        }
        
        
        // CHECK TO SEE IF THE CURRENT USER HAS VOTED FOR THIS POST
         if ($current_user->getLoggedIn()) {
            $sql = "SELECT vote_rating FROM " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND (vote_user_id = %d OR vote_user_ip = %s) AND vote_rating != %s";
            $voted = $db->get_var($db->prepare($sql, $post->getId(), $current_user->getId(), $cage->server->testIp('REMOTE_ADDR'), 'alert'));
        } 
        
        // CHECK TO SEE IF THIS ANONYMOUS USER HAS VOTED FOR THIS POST
        if (!$current_user->getLoggedIn() && ($post->vars['voteAnonymousVotes'] == 'checked')) {
            $sql = "SELECT vote_rating FROM " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND vote_user_ip = %s AND vote_rating != %s";
            $voted = $db->get_var($db->prepare($sql, $post->id, $cage->server->testIp('REMOTE_ADDR'), 'alert'));
        }
               
         $hotaru->displayTemplate('vote_button', 'vote', false);
    }
    
     /**
     * Add an "alert" link below the story
     */
    public function submit_show_post_extra_fields() {
        global $post, $lang, $current_user;
        
        // Only show the Alert link ("Flag it") on new posts, not top stories
        if ($current_user->getLoggedIn() && $post->status == "new" && ($post->vars['vote_use_alerts'] == "checked")) {
            echo "<li><a class='alert_link' href='#'>" . $lang["vote_alert"]  . "</a></li>";
        }
    }
    
    
     /**
     * List of alert reasons to choose from.
     */
    public function submit_show_post_extras() {
        global $post, $lang;
    
        if ($post->status == "new" && ($post->vars['vote_use_alerts'] == "checked")) {
            echo "<div class='alert_choices' style='display: none;'>";
                echo "<h3>" . $lang["vote_alert_reason_title"]  . "</h3>";
                echo "<ul>";
                echo "<li><a href='" . url(array('page'=>$post->id, 'alert'=>1)) . "'>" . $lang["vote_alert_reason_1"]  . "</a></li>";
                echo "<li><a href='" . url(array('page'=>$post->id, 'alert'=>2)) . "'>" . $lang["vote_alert_reason_2"]  . "</a></li>";
                echo "<li><a href='" . url(array('page'=>$post->id, 'alert'=>3)) . "'>" . $lang["vote_alert_reason_3"]  . "</a></li>";
                echo "<li><a href='" . url(array('page'=>$post->id, 'alert'=>4)) . "'>" . $lang["vote_alert_reason_4"]  . "</a></li>";
                echo "<li><a href='" . url(array('page'=>$post->id, 'alert'=>5)) . "'>" . $lang["vote_alert_reason_5"]  . "</a></li>";
                echo "<li><a href='" . url(array('page'=>$post->id, 'alert'=>6)) . "'>" . $lang["vote_alert_reason_6"]  . "</a></li>";
                echo "</ul>";
            echo "</div>";
        }
    }
    
    
     /**
     * ********************************************************************* 
     * ******************* FUNCTIONS FOR ADMIN SETTINGS ******************** 
     * *********************************************************************
     * ****************************************************************** */
    
    /**
     * Call the settings function
     */
    public function admin_plugin_settings() {
        require_once(PLUGINS . 'vote/vote_settings.php');
        $voteSettings = new VoteSettings();
        $voteSettings->settings($this->folder);
        return true;
    }
    
}

?>