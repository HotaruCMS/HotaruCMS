<?php
/**
 * File: /plugins/updown_voting/updown_voting_settings.php
 * Purpose: Admin settings for the Vote plugin
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

class UpdownVotingSettings
{
    /**
     * Vote Settings Page
     */
    public function settings($h) {

        // If the form has been submitted, go and save the data...
        if ($h->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings($h); 
        }    
        
        echo "<h1>" . $h->lang["vote_settings_header"] . "</h1>\n";
        
        // Get settings from the database if they exist...
        $updown_voting_settings = unserialize($h->getSetting('updown_voting_settings')); 
        
        $submit_vote = $updown_voting_settings['submit_vote'];
        $submit_vote_value = $updown_voting_settings['submit_vote_value'];
        $votes_to_promote = $updown_voting_settings['votes_to_promote'];
        $use_demote = $updown_voting_settings['use_demote'];
        $use_alerts = $updown_voting_settings['use_alerts'];
        $alerts_to_bury = $updown_voting_settings['alerts_to_bury'];
        $physical_delete = $updown_voting_settings['physical_delete'];
        $upcoming_duration = $updown_voting_settings['upcoming_duration'];
        $no_front_page = $updown_voting_settings['no_front_page'];
        $posts_widget = $updown_voting_settings['posts_widget'];
        
        //...otherwise set to blank or default:
        if (!$submit_vote) { $submit_vote = ''; }
        if (!$submit_vote_value) { $submit_vote_value = 1; }
        if (!$votes_to_promote) { $votes_to_promote = 5; }
        if (!isset($use_demote)) { $use_demote = ''; }
        if (!isset($use_alerts)) { $use_alerts = 'checked'; }
        if (!$alerts_to_bury) { $alerts_to_bury = 5; }
        if (!$physical_delete) { $physical_delete = ''; }
        if (!$upcoming_duration) { $upcoming_duration = 5; }
        if (!$no_front_page) { $no_front_page = 5; }
        if (!$posts_widget) { $posts_widget = 'checked'; }
        
        // A plugin hook so other plugin developers can add settings
        $h->pluginHook('updown_voting_settings_get_values');
        
        // The form should be submitted to the admin_index.php page:
        echo "<form name='updown_voting_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=updown_voting' method='post'>\n";
        
        echo "<p><b>" . $h->lang["vote_settings_vote_auto"] . "</b></p>";
        
        echo "<p><input type='checkbox' name='vote_submit_vote' value='vote_submit_vote' " . $submit_vote . " > " . $h->lang["vote_settings_submit_vote"] . "</p>\n";
        echo "<p>" . $h->lang["vote_settings_submit_vote_value"] . " <input type='text' size=5 name='vote_submit_vote_value' value='" . $submit_vote_value . "' /> <small> (Default: 1)</small></p>\n";
        
        // A plugin hook so other plugin developers can show settings
        $h->pluginHook('updown_voting_settings_form_1');
        
        echo "<br /><p><b>" . $h->lang["vote_settings_vote_promote_bury"] . "</b></p>";
        
        echo "<p>" . $h->lang["vote_settings_votes_to_promote"] . " <input type='text' size=5 name='vote_votes_to_promote' value='" . $votes_to_promote . "' /> <small> (Default: 5)</small></p>\n";
        echo "<p>" . $h->lang["vote_settings_upcoming_duration"] . " <input type='text' size=5 name='vote_upcoming_duration' value='" . $upcoming_duration . "' /> <small> (Default: 5)</small></p>\n";
        echo "<p>" . $h->lang["vote_settings_no_front_page"] . " <input type='text' size=5 name='vote_no_front_page' value='" . $no_front_page . "' /> <small> (Default: 5)</small></p>\n";
        echo "<p><input type='checkbox' name='vote_use_demote' value='vote_use_demote' " . $use_demote . " > " . $h->lang["vote_settings_back_to_latest"] . "</p>\n";
        echo "<p><input type='checkbox' name='vote_use_alerts' value='vote_use_alerts' " . $use_alerts . " > " . $h->lang["vote_settings_use_alerts"] . "</p>\n";
        echo "<p>" . $h->lang["vote_settings_alerts_to_bury"] . " <input type='text' size=5 name='vote_alerts_to_bury' value='" . $alerts_to_bury . "' /> <small> (Default: 5)</small></p>\n";
        
        echo "<p><input type='checkbox' id='vote_physical_delete' name='vote_physical_delete' " . $physical_delete . " /> " . $h->lang["vote_settings_physical_delete"] . "</p>";
        
        echo "<br /><p><b>" . $h->lang["vote_settings_other"] . "</b></p>";
        echo "<p><input type='checkbox' name='widget_votes' value='widget_votes' " . $posts_widget . ">&nbsp;&nbsp;" . $h->lang["vote_settings_posts_widget"] . "</p>\n";


        // A plugin hook so other plugin developers can show settings
        $h->pluginHook('updown_voting_settings_form_2');
        
        echo "<br />\n";    
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $h->lang["main_form_save"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form>\n";
    }
    
    
    /**
     * Save Vote Settings
     */
    public function saveSettings($h) {

        $error = 0;
        
        // Get settings from the database if they exist...
        $updown_voting_settings = unserialize($h->getSetting('updown_voting_settings')); 
            
        // Submit Vote
        if ($h->cage->post->keyExists('vote_submit_vote')) { 
            $submit_vote = 'checked'; 
        } else { 
            $submit_vote = ''; 
        }
        
        
        // Check submit_vote_value
        if ($h->cage->post->keyExists('vote_submit_vote_value')) {
            $submit_vote_value = $h->cage->post->testInt('vote_submit_vote_value'); 
            if ($submit_vote_value < 1) {
                $h->messages[$h->lang["vote_settings_submit_vote_value_invalid"]] = "red";
                $error = 1;
                $submit_vote_value = $updown_voting_settings['submit_vote_value'];
            }
        } else { 
            $h->messages[$h->lang["vote_settings_submit_vote_value_invalid"]] = "red";
            $error = 1;
            $submit_vote_value = $updown_voting_settings['submit_vote_value'];
        }
    
    
        // Check votes_to_promote
        if ($h->cage->post->keyExists('vote_votes_to_promote')) {
            $votes_to_promote = $h->cage->post->testInt('vote_votes_to_promote'); 
            if ($votes_to_promote < 1) {
                $h->messages[$h->lang["vote_settings_votes_to_promote_invalid"]] = "red";
                $error = 1;
                $votes_to_promote = $updown_voting_settings['votes_to_promote'];
            }
        } else { 
            $h->messages[$h->lang["vote_settings_votes_to_promote_invalid"]] = "red";
            $error = 1;
            $votes_to_promote = $updown_voting_settings['votes_to_promote'];
        }
        
        // Check upcoming duration
        if ($h->cage->post->keyExists('vote_upcoming_duration')) {
            $upcoming_duration = $h->cage->post->testInt('vote_upcoming_duration'); 
            if ($upcoming_duration < 1) {
                $h->messages[$h->lang["vote_settings_upcoming_duration_invalid"]] = "red";
                $error = 1;
                $upcoming_duration = $updown_voting_settings['upcoming_duration'];
            }
        } else { 
            $h->messages[$h->lang["vote_settings_upcoming_duration_invalid"]] = "red";
            $error = 1;
            $upcoming_duration = $updown_voting_settings['upcoming_duration'];
        }
        
        // Check no_front_page (deadline for front page)
        if ($h->cage->post->keyExists('vote_no_front_page')) {
            $no_front_page = $h->cage->post->testInt('vote_no_front_page'); 
            if ($no_front_page < 1) {
                $h->messages[$h->lang["vote_settings_no_front_page_invalid"]] = "red";
                $error = 1;
                $no_front_page = $updown_voting_settings['no_front_page'];
            }
        } else { 
            $h->messages[$h->lang["vote_settings_no_front_page_invalid"]] = "red";
            $error = 1;
            $no_front_page = $updown_voting_settings['no_front_page'];
        }
        
        // Use demote (back to Latest page)
        if ($h->cage->post->keyExists('vote_use_demote')) { 
            $use_demote = 'checked'; 
        } else { 
            $use_demote = ''; 
        }
        
        // Use alerts
        if ($h->cage->post->keyExists('vote_use_alerts')) { 
            $use_alerts = 'checked'; 
        } else { 
            $use_alerts = ''; 
        }
        
        
        // Check alerts_to_bury
        if ($h->cage->post->keyExists('vote_alerts_to_bury')) { 
            $alerts_to_bury = $h->cage->post->testInt('vote_alerts_to_bury'); 
            if ($alerts_to_bury < 1) {
                $h->messages[$h->lang["vote_settings_alerts_to_bury_invalid"] ] = "red";
                $error = 1;
                $alerts_to_bury = $updown_voting_settings['alerts_to_bury'];
            }
        } else { 
            $h->messages[$h->lang["vote_settings_alerts_to_bury_invalid"] ] = "red";
            $error = 1;
            $alerts_to_bury = $updown_voting_settings['alerts_to_bury'];
        }
        
        
        // Check the status of our checkbox for physical delete
        if ($h->cage->post->keyExists('vote_physical_delete')) { 
            $physical_delete = 'checked'; 
        } else { 
            $physical_delete = ''; 
        }
        
        
        // Votes in Sidebar Posts
        if ($h->cage->post->keyExists('widget_votes')) { 
            $posts_widget = 'checked';
        } else { 
            $posts_widget = '';
        }
        
        // A plugin hook so other plugin developers can save settings   
        $h->pluginHook('vote_save_settings');
        
        // Save new settings...    
        $updown_voting_settings['submit_vote'] = $submit_vote;
        $updown_voting_settings['submit_vote_value'] = $submit_vote_value;
        $updown_voting_settings['votes_to_promote'] = $votes_to_promote;
        $updown_voting_settings['use_demote'] = $use_demote;
        $updown_voting_settings['use_alerts'] = $use_alerts;
        $updown_voting_settings['alerts_to_bury'] = $alerts_to_bury;
        $updown_voting_settings['physical_delete'] = $physical_delete;
        $updown_voting_settings['upcoming_duration'] = $upcoming_duration;
        $updown_voting_settings['no_front_page'] = $no_front_page;
        $updown_voting_settings['posts_widget'] = $posts_widget;
       
        // parameters: plugin folder name, setting name, setting value
        $h->updateSetting('updown_voting_settings', serialize($updown_voting_settings));
        
        if ($error == 0) {
            $h->messages[$h->lang["main_settings_saved"]] = "green";
        }
        
        $h->showMessages();
        
        return true;    
    } 

}
?>
