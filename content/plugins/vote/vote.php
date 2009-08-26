<?php
/**
 * name: Vote
 * description: Adds voting ability to posted stories.
 * version: 0.1
 * folder: vote
 * prefix: vote
 * requires: submit 0.1, users 0.1
 * hooks: install_plugin, hotaru_header, submit_hotaru_header_1, submit_class_post_read_post_1, submit_class_post_read_post_2, header_include, submit_pre_show_post, admin_plugin_settings, admin_sidebar_plugin_settings, submit_class_post_add_post, navigation, submit_show_post_extra_fields, submit_show_post_extras
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

/**
 * Add vote fields to the post table and make a dedicated Votes table.
 */
function vote_install_plugin() {
    global $db, $plugin;
    
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
    $plugin->plugin_settings_update('vote', 'vote_settings', serialize($vote_settings));
    
    // Include language file. Also included in hotaru_header, but needed here so 
    // that the link in the Admin sidebar shows immediately after installation.
    $plugin->include_language('vote');
   
}  


/**
 * Set things up when the page is first loaded
 */
function vote_hotaru_header() {
    global $plugin, $post;
    
    if (!defined('TABLE_POSTVOTES')) { define("TABLE_POSTVOTES", DB_PREFIX . 'postvotes'); }
    
    $plugin->include_language('vote');    
}

/**
 * Adds additional member variables when the $post object is read in the Submit plugin
 */
function vote_submit_hotaru_header_1() {
    global $post, $hotaru, $plugin, $cage;
        
    $post->post_vars['post_votes_up'] = 0;
    $post->post_vars['post_votes_down'] = 0;
    $post->post_vars['vote_anonymous_votes'] = '';    
    $post->post_vars['vote_use_alerts'] = 'checked';    
}

/**
 * Read vote settings
 */
function vote_submit_class_post_read_post_1() {
    global $plugin, $post;
    
    if ($plugin->plugin_active('vote')) { 
    
        // Get settings from the database if they exist...
        $vote_settings = unserialize($plugin->plugin_settings('vote', 'vote_settings')); 
    
        // Determine vote type
        if ($vote_settings['vote_vote_unvote'] == 'checked') {
            $post->post_vars['vote_type'] = "vote_unvote";
        } elseif ($vote_settings['vote_up_down'] == 'checked') {
            $post->post_vars['vote_type'] = "up_down";
        } else {
            $post->post_vars['vote_type'] = "yes_no";
        }
        
        // Enable anonymous voters?
        $post->post_vars['vote_anonymous_votes'] = $vote_settings['vote_anonymous_votes'];
        
        // Use alerts?
        $post->post_vars['vote_use_alerts'] = $vote_settings['vote_use_alerts'];
    }
}


/**
 * Read number of votes if post exists.
 */
function vote_submit_class_post_read_post_2() {
    global $post, $post_row;
    $post->post_vars['post_votes_up'] = $post_row->post_votes_up;
    $post->post_vars['post_votes_down'] = $post_row->post_votes_down;
}


/**
 * Includes css and javascript for the vote buttons.
 */
function vote_header_include()
{
    global $plugin, $lang, $hotaru;
    
    $plugin->include_css('vote');
    $plugin->include_js('vote');
    $plugin->include_js('vote', 'json2.min');
}


 /**
 * ********************************************************************* 
 * *********************** FUNCTIONS FOR VOTING ************************ 
 * *********************************************************************
 * ****************************************************************** */
 
 
/**
 * If auto-vote is enabled, the new post is automatically voted for by the person who submitted it.
 */
function vote_submit_class_post_add_post()
{
     global $db, $current_user, $post, $plugin, $cage;
     
     //get vote settings
    $vote_settings = unserialize($plugin->plugin_settings('vote', 'vote_settings')); 
    $submit_vote = $vote_settings['vote_submit_vote'];
    $submit_vote_value = $vote_settings['vote_submit_vote_value'];
    
    // Automatically vote for a post when it's submitted...
    if ($submit_vote == 'checked') {
        // Determine vote type
        if ($vote_settings['vote_vote_unvote'] == 'checked') {
            $post->post_vars['vote_type'] = "vote_unvote";
        } elseif ($vote_settings['vote_up_down'] == 'checked') {
            $post->post_vars['vote_type'] = "up_down";
        } else {
            $post->post_vars['vote_type'] = "yes_no";
        }
        
        //update the vote count
        $sql = "UPDATE " . TABLE_POSTS . " SET post_votes_up=post_votes_up+%d WHERE post_id = %d";
        $db->query($db->prepare($sql, $submit_vote_value, $post->post_id));
    
        //Insert one vote for each of $submit_vote_value;
        for ($i=0; $i<$submit_vote_value; $i++) {
            $sql = "INSERT INTO " . TABLE_POSTVOTES . " (vote_post_id, vote_user_id, vote_user_ip, vote_date, vote_type, vote_rating, vote_updateby) VALUES (%d, %d, %s, CURRENT_TIMESTAMP, %s, %s, %d)";
            $db->query($db->prepare($sql, $post->post_id, $current_user->id, $cage->server->testIp('REMOTE_ADDR'), $post->post_vars['vote_type'], 'positive', $current_user->id));
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
function vote_navigation() {    
    global $lang, $plugin, $hotaru;
    
    //get vote settings
    $vote_settings = unserialize($plugin->plugin_settings('vote', 'vote_settings')); 
    
    if (($vote_settings['vote_submit_vote'] == "checked") && ($vote_settings['vote_submit_vote_value'] >= $vote_settings['vote_votes_to_promote'])) {
        // these settings make the latest page unnecessary so the "Home" link is sufficient, otherwise...
    } else {
    
        if ($hotaru->title == 'latest') { $status = "id='navigation_active'"; } else { $status = ""; }
        echo "<li><a " . $status . " href='" . url(array('page'=>'latest')) . "'>" . $lang["vote_navigation_latest"] . "</a></li>\n";
    }
}

 /**
 * Displays the vote button.
 */
function vote_submit_pre_show_post() {
    global $hotaru, $db, $post, $current_user, $voted, $cage, $lang, $plugin;
    
    if ($post->post_status == 'new' && $post->post_vars['vote_use_alerts'] == "checked") {
        // CHECK TO SEE IF THIS POST IS BEING FLAGGED AND IF SO, ADD IT TO THE DATABASE
        if ($cage->get->keyExists('alert') && $current_user->logged_in) {
            // Check if already flagged...
            $sql = "SELECT vote_rating FROM " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND vote_user_id = %d AND vote_rating = %s";
            $flagged = $db->get_var($db->prepare($sql, $post->post_id, $current_user->id, 'alert'));
            if (!$flagged) {
                $sql = "INSERT INTO " . TABLE_POSTVOTES . " (vote_post_id, vote_user_id, vote_user_ip, vote_date, vote_type, vote_rating, vote_reason, vote_updateby) VALUES (%d, %d, %s, CURRENT_TIMESTAMP, %s, %s, %d, %d)";
                $db->query($db->prepare($sql, $post->post_id, $current_user->id, $cage->server->testIp('REMOTE_ADDR'), $post->post_vars['vote_type'], 'alert', $cage->get->testInt('alert'), $current_user->id));
            }
            else
            {
                $hotaru->message = $lang["vote_alert_already_flagged"];
                $hotaru->message_type = "red";
                $hotaru->show_message();
            }
        }
        
        // CHECK TO SEE IF THIS POST HAS BEEN FLAGGED AND IF SO, SHOW THE ALERT STATUS
    
        // Get settings from the database if they exist...
        $vote_settings = unserialize($plugin->plugin_settings('vote', 'vote_settings')); 
        
        // Check if already flagged...
        $sql = "SELECT * FROM " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND vote_rating = %s";
        $flagged = $db->get_results($db->prepare($sql, $post->post_id, 'alert'));
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
                    $db->query($db->prepare($sql, $post->post_id));
                    $post->delete_post($post->post_id); 
                } else {
                    $post->change_status('buried');
                }
                
                $hotaru->message = $lang["vote_alert_post_buried"];
                $hotaru->message_type = "red";
                $hotaru->show_message();
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
     if ($current_user->logged_in) {
        $sql = "SELECT vote_rating FROM " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND (vote_user_id = %d OR vote_user_ip = %s) AND vote_rating != %s";
        $voted = $db->get_var($db->prepare($sql, $post->post_id, $current_user->id, $cage->server->testIp('REMOTE_ADDR'), 'alert'));
    } 
    
    // CHECK TO SEE IF THIS ANONYMOUS USER HAS VOTED FOR THIS POST
    if (!$current_user->logged_in && ($post->post_vars['vote_anonymous_votes'] == 'checked')) {
        $sql = "SELECT vote_rating FROM " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND vote_user_ip = %s AND vote_rating != %s";
        $voted = $db->get_var($db->prepare($sql, $post->post_id, $cage->server->testIp('REMOTE_ADDR'), 'alert'));
    }
           
     $hotaru->display_template('vote_button', 'vote', false);
}

 /**
 * Add an "alert" link below the story
 */
function vote_submit_show_post_extra_fields() {
    global $post, $lang, $current_user;
    
    // Only show the Alert link ("Flag it") on new posts, not top stories
    if ($current_user->logged_in && $post->post_status == "new" && ($post->post_vars['vote_use_alerts'] == "checked")) {
        echo "<a class='alert_link' href='#'>" . $lang["vote_alert"]  . "</a> &nbsp;&nbsp;";
    }
}


 /**
 * List of alert reasons to choose from.
 */
function vote_submit_show_post_extras() {
    global $post, $lang;

    if ($post->post_status == "new" && ($post->post_vars['vote_use_alerts'] == "checked")) {
        echo "<div class='alert_choices' style='display: none;'>";
            echo "<h3>" . $lang["vote_alert_reason_title"]  . "</h3>";
            echo "<ul>";
            echo "<li><a href='" . url(array('page'=>$post->post_id, 'alert'=>1)) . "'>" . $lang["vote_alert_reason_1"]  . "</a></li>";
            echo "<li><a href='" . url(array('page'=>$post->post_id, 'alert'=>2)) . "'>" . $lang["vote_alert_reason_2"]  . "</a></li>";
            echo "<li><a href='" . url(array('page'=>$post->post_id, 'alert'=>3)) . "'>" . $lang["vote_alert_reason_3"]  . "</a></li>";
            echo "<li><a href='" . url(array('page'=>$post->post_id, 'alert'=>4)) . "'>" . $lang["vote_alert_reason_4"]  . "</a></li>";
            echo "<li><a href='" . url(array('page'=>$post->post_id, 'alert'=>5)) . "'>" . $lang["vote_alert_reason_5"]  . "</a></li>";
            echo "<li><a href='" . url(array('page'=>$post->post_id, 'alert'=>6)) . "'>" . $lang["vote_alert_reason_6"]  . "</a></li>";
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
 * Link to settings page in the Admin sidebar
 */
function vote_admin_sidebar_plugin_settings() {
    global $lang;
    
    echo "<li><a href='" . url(array('page'=>'plugin_settings', 'plugin'=>'vote'), 'admin') . "'>" . $lang["vote_admin_sidebar"] . "</a></li>";
}


/**
 * Vote Settings Page
 */
function vote_admin_plugin_settings() {
    global $hotaru, $plugin, $cage, $lang;
    
    // If the form has been submitted, go and save the data...
    if ($cage->post->getAlpha('submitted') == 'true') { 
        vote_save_settings(); 
    }    
    
    echo "<h1>" . $lang["vote_settings_header"] . "</h1>\n";
    
    // Get settings from the database if they exist...
    $vote_settings = unserialize($plugin->plugin_settings('vote', 'vote_settings')); 
    
    $vote_unvote = $vote_settings['vote_vote_unvote'];
    $up_down = $vote_settings['vote_up_down'];
    $yes_no = $vote_settings['vote_yes_no'];
    $anonymous_votes = $vote_settings['vote_anonymous_votes'];
    $submit_vote = $vote_settings['vote_submit_vote'];
    $submit_vote_value = $vote_settings['vote_submit_vote_value'];
    $votes_to_promote = $vote_settings['vote_votes_to_promote'];
    $use_alerts = $vote_settings['vote_use_alerts'];
    $alerts_to_bury = $vote_settings['vote_alerts_to_bury'];
    $physical_delete = $vote_settings['vote_physical_delete'];
    
    //...otherwise set to blank or default:
    if (!$vote_unvote) { $vote_unvote = ''; }
    if (!$up_down) { $up_down = ''; }
    if (!$yes_no) { $yes_no = ''; }
    if (!$anonymous_votes) { $anonymous_votes = ''; }
    if (!$submit_vote) { $submit_vote = ''; }
    if (!$submit_vote_value) { $submit_vote_value = 1; }
    if (!$votes_to_promote) { $votes_to_promote = 5; }
    if (!isset($use_alerts)) { $use_alerts = 'checked'; }
    if (!$alerts_to_bury) { $alerts_to_bury = 5; }
    if (!$physical_delete) { $physical_delete = ''; }
    
    // A plugin hook so other plugin developers can add settings
    $plugin->check_actions('vote_settings_get_values');
    
    // The form should be submitted to the admin_index.php page:
    echo "<form name='vote_settings_form' action='" . BASEURL . "admin/admin_index.php?page=plugin_settings&amp;plugin=vote' method='post'>\n";
    
    echo "<p><b>" . $lang["vote_settings_vote_type"] . "</b></p>";
    
    echo "<p><input type='radio' name='vote_type' value='vote_unvote' " . $vote_unvote . " >&nbsp;&nbsp;" . $lang["vote_settings_vote_unvote"] . "</p>\n";    
    echo "<p><input type='radio' name='vote_type' value='up_down' " . $up_down . " >&nbsp;&nbsp;" . $lang["vote_settings_up_down"] . "</p>\n"; 
    echo "<p><input type='radio' name='vote_type' value='yes_no' " . $yes_no . " >&nbsp;&nbsp;" . $lang["vote_settings_yes_no"] . "</p>\n"; 
    
    echo "<br /><p><b>" . $lang["vote_settings_vote_auto"] . "</b></p>";
    
    echo "<p><input type='checkbox' name='vote_submit_vote' value='vote_submit_vote' " . $submit_vote . " > " . $lang["vote_settings_submit_vote"] . "</p>\n";
    echo "<p>" . $lang["vote_settings_submit_vote_value"] . " <input type='text' size=5 name='vote_submit_vote_value' value='" . $submit_vote_value . "' /> <small> (Default: 1)</small></p>\n";
    
    echo "<br /><p><b>" . $lang["vote_settings_vote_anonymous"] . "</b></p>";
    echo "<p><input type='checkbox' name='vote_anonymous_votes' value='vote_anonymous_votes' " . $anonymous_votes . " > " . $lang["vote_settings_anonymous_votes"] . "</p>\n";
    
    // A plugin hook so other plugin developers can show settings
    $plugin->check_actions('vote_settings_form_1');
    
    echo "<br /><p><b>" . $lang["vote_settings_vote_promote_bury"] . "</b></p>";
    
    echo "<p>" . $lang["vote_settings_votes_to_promote"] . " <input type='text' size=5 name='vote_votes_to_promote' value='" . $votes_to_promote . "' /> <small> (Default: 5)</small></p>\n";
    echo "<p><input type='checkbox' name='vote_use_alerts' value='vote_use_alerts' " . $use_alerts . " > " . $lang["vote_settings_use_alerts"] . "</p>\n";
    echo "<p>" . $lang["vote_settings_alerts_to_bury"] . " <input type='text' size=5 name='vote_alerts_to_bury' value='" . $alerts_to_bury . "' /> <small> (Default: 5)</small></p>\n";
    
    echo "<p><input type='checkbox' id='vote_physical_delete' name='vote_physical_delete' " . $physical_delete . " /> " . $lang["vote_settings_physical_delete"] . "</p>";
        
    // A plugin hook so other plugin developers can show settings
    $plugin->check_actions('vote_settings_form_2');
    
    echo "<br />\n";    
    echo "<input type='hidden' name='submitted' value='true' />\n";
    echo "<input type='submit' value='" . $lang["vote_settings_save"] . "' />\n";
    echo "</form>\n";
}


/**
 * Save Vote Settings
 */
function vote_save_settings() {
    global $cage, $hotaru, $plugin, $lang;
    
    $error = 0;
    
    // Get settings from the database if they exist...
    $vote_settings = unserialize($plugin->plugin_settings('vote', 'vote_settings')); 
        
    // Check the status of our radio buttons for vote type
    if ($cage->post->keyExists('vote_type')) { 
        $selected = $cage->post->testAlnumLines('vote_type'); 
        switch($selected) {
            case 'vote_unvote':
                $vote_unvote = "checked";
                $up_down = "";
                $yes_no = "";
                break;
            case 'up_down':
                $vote_unvote = "";
                $up_down = "checked";
                $yes_no = "";
                break;
            case 'yes_no':
                $vote_unvote = "";
                $up_down = "";
                $yes_no = "checked";
                break;
            default:
                $vote_unvote = "checked";
                $up_down = "";
                $yes_no = "";
                break;
        }

    }


    // Submit Vote
    if ($cage->post->keyExists('vote_submit_vote')) { 
        $submit_vote = 'checked'; 
    } else { 
        $submit_vote = ''; 
    }
    
    
    // Check the content for submit_vote_value
    if ($cage->post->keyExists('vote_submit_vote_value')) {
        $submit_vote_value = $cage->post->testInt('vote_submit_vote_value'); 
        if ($submit_vote_value < 1) {
            $hotaru->messages[$lang["vote_settings_submit_vote_value_invalid"]] = "red";
            $error = 1;
            $submit_vote_value = $vote_settings['vote_submit_vote_value'];
        }
    } else { 
        $hotaru->messages[$lang["vote_settings_submit_vote_value_invalid"]] = "red";
        $error = 1;
        $submit_vote_value = $vote_settings['vote_submit_vote_value'];
    }
    
    
    // Anonymous Vote
    if ($cage->post->keyExists('vote_anonymous_votes')) { 
        $anonymous_votes = 'checked'; 
    } else { 
        $anonymous_votes = ''; 
    }
    
        
    // Check the content for votes_to_promote
    if ($cage->post->keyExists('vote_votes_to_promote')) {
        $votes_to_promote = $cage->post->testInt('vote_votes_to_promote'); 
        if ($votes_to_promote < 1) {
            $hotaru->messages[$lang["vote_settings_votes_to_promote_invalid"]] = "red";
            $error = 1;
            $votes_to_promote = $vote_settings['vote_votes_to_promote'];
        }
    } else { 
        $hotaru->messages[$lang["vote_settings_votes_to_promote_invalid"]] = "red";
        $error = 1;
        $votes_to_promote = $vote_settings['vote_votes_to_promote'];
    }
    
    
    // Use alerts
    if ($cage->post->keyExists('vote_use_alerts')) { 
        $use_alerts = 'checked'; 
    } else { 
        $use_alerts = ''; 
    }
    
    
    // Check the content for alerts_to_bury
    if ($cage->post->keyExists('vote_alerts_to_bury')) { 
        $alerts_to_bury = $cage->post->testInt('vote_alerts_to_bury'); 
        if ($alerts_to_bury < 1) {
            $hotaru->messages[$lang["vote_settings_alerts_to_bury_invalid"] ] = "red";
            $error = 1;
            $alerts_to_bury = $vote_settings['vote_alerts_to_bury'];
        }
    } else { 
        $hotaru->messages[$lang["vote_settings_alerts_to_bury_invalid"] ] = "red";
        $error = 1;
        $alerts_to_bury = $vote_settings['vote_alerts_to_bury'];
    }
    
    
    // Check the status of our checkbox for physical delete
    if ($cage->post->keyExists('vote_physical_delete')) { 
        $physical_delete = 'checked'; 
    } else { 
        $physical_delete = ''; 
    }
    
    // A plugin hook so other plugin developers can save settings   
    $plugin->check_actions('vote_save_settings');
    
    // Save new settings...    
    $vote_settings['vote_vote_unvote'] = $vote_unvote;
    $vote_settings['vote_up_down'] = $up_down;
    $vote_settings['vote_yes_no'] = $yes_no;
    $vote_settings['vote_anonymous_votes'] = $anonymous_votes;
    $vote_settings['vote_submit_vote'] = $submit_vote;
    $vote_settings['vote_submit_vote_value'] = $submit_vote_value;
    $vote_settings['vote_votes_to_promote'] = $votes_to_promote;
    $vote_settings['vote_use_alerts'] = $use_alerts;
    $vote_settings['vote_alerts_to_bury'] = $alerts_to_bury;
    $vote_settings['vote_physical_delete'] = $physical_delete;
    
    // parameters: plugin folder name, setting name, setting value
    $plugin->plugin_settings_update('vote', 'vote_settings', serialize($vote_settings));
    
    if ($error == 0) {
        $hotaru->messages[$lang["vote_settings_saved"]] = "green";
    }
    
    $hotaru->show_messages();
    
    return true;    
} 

?>