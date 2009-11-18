<?php
/**
 * File: /plugins/vote/vote_settings.php
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

class VoteSimpleSettings extends VoteSimple
{
    /**
     * Vote Settings Page
     */
    public function settings() {

        // If the form has been submitted, go and save the data...
        if ($this->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings(); 
        }    
        
        echo "<h1>" . $this->lang["vote_settings_header"] . "</h1>\n";
        
        // Get settings from the database if they exist...
        $vote_settings = unserialize($this->getSetting('vote_settings')); 
        
        $submit_vote = $vote_settings['vote_submit_vote'];
        $submit_vote_value = $vote_settings['vote_submit_vote_value'];
        $votes_to_promote = $vote_settings['vote_votes_to_promote'];
        $use_alerts = $vote_settings['vote_use_alerts'];
        $alerts_to_bury = $vote_settings['vote_alerts_to_bury'];
        $physical_delete = $vote_settings['vote_physical_delete'];
        $upcoming_duration = $vote_settings['vote_upcoming_duration'];
        $no_front_page = $vote_settings['vote_no_front_page'];
        $sidebar_votes = $vote_settings['vote_sidebar_posts'];
        
        //...otherwise set to blank or default:
        if (!$submit_vote) { $submit_vote = ''; }
        if (!$submit_vote_value) { $submit_vote_value = 1; }
        if (!$votes_to_promote) { $votes_to_promote = 5; }
        if (!isset($use_alerts)) { $use_alerts = 'checked'; }
        if (!$alerts_to_bury) { $alerts_to_bury = 5; }
        if (!$physical_delete) { $physical_delete = ''; }
        if (!$upcoming_duration) { $upcoming_duration = 5; }
        if (!$no_front_page) { $no_front_page = 5; }
        if (!$sidebar_votes) { $sidebar_votes = 'checked'; }
        
        // A plugin hook so other plugin developers can add settings
        $this->pluginHook('vote_settings_get_values');
        
        // The form should be submitted to the admin_index.php page:
        echo "<form name='vote_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=vote_simple' method='post'>\n";
        
        echo "<p><b>" . $this->lang["vote_settings_vote_auto"] . "</b></p>";
        
        echo "<p><input type='checkbox' name='vote_submit_vote' value='vote_submit_vote' " . $submit_vote . " > " . $this->lang["vote_settings_submit_vote"] . "</p>\n";
        echo "<p>" . $this->lang["vote_settings_submit_vote_value"] . " <input type='text' size=5 name='vote_submit_vote_value' value='" . $submit_vote_value . "' /> <small> (Default: 1)</small></p>\n";
        
        // A plugin hook so other plugin developers can show settings
        $this->pluginHook('vote_settings_form_1');
        
        echo "<br /><p><b>" . $this->lang["vote_settings_vote_promote_bury"] . "</b></p>";
        
        echo "<p>" . $this->lang["vote_settings_votes_to_promote"] . " <input type='text' size=5 name='vote_votes_to_promote' value='" . $votes_to_promote . "' /> <small> (Default: 5)</small></p>\n";
        echo "<p>" . $this->lang["vote_settings_upcoming_duration"] . " <input type='text' size=5 name='vote_upcoming_duration' value='" . $upcoming_duration . "' /> <small> (Default: 5)</small></p>\n";
        echo "<p>" . $this->lang["vote_settings_no_front_page"] . " <input type='text' size=5 name='vote_no_front_page' value='" . $no_front_page . "' /> <small> (Default: 5)</small></p>\n";
        echo "<p><input type='checkbox' name='vote_use_alerts' value='vote_use_alerts' " . $use_alerts . " > " . $this->lang["vote_settings_use_alerts"] . "</p>\n";
        echo "<p>" . $this->lang["vote_settings_alerts_to_bury"] . " <input type='text' size=5 name='vote_alerts_to_bury' value='" . $alerts_to_bury . "' /> <small> (Default: 5)</small></p>\n";
        
        echo "<p><input type='checkbox' id='vote_physical_delete' name='vote_physical_delete' " . $physical_delete . " /> " . $this->lang["vote_settings_physical_delete"] . "</p>";
        
        echo "<br /><p><b>" . $this->lang["vote_settings_other"] . "</b></p>";
echo "<p><input type='checkbox' name='sb_votes' value='sb_votes' " . $sidebar_votes . ">&nbsp;&nbsp;" . $this->lang["vote_settings_sidebar_posts"] . "</p>\n"; 
            
        // A plugin hook so other plugin developers can show settings
        $this->pluginHook('vote_settings_form_2');
        
        echo "<br />\n";    
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $this->lang["vote_settings_save"] . "' />\n";
        echo "</form>\n";
    }
    
    
    /**
     * Save Vote Settings
     */
    public function saveSettings() {

        $error = 0;
        
        // Get settings from the database if they exist...
        $vote_settings = unserialize($this->getSetting('vote_settings')); 
            
        // Submit Vote
        if ($this->cage->post->keyExists('vote_submit_vote')) { 
            $submit_vote = 'checked'; 
        } else { 
            $submit_vote = ''; 
        }
        
        
        // Check submit_vote_value
        if ($this->cage->post->keyExists('vote_submit_vote_value')) {
            $submit_vote_value = $this->cage->post->testInt('vote_submit_vote_value'); 
            if ($submit_vote_value < 1) {
                $this->hotaru->messages[$this->lang["vote_settings_submit_vote_value_invalid"]] = "red";
                $error = 1;
                $submit_vote_value = $vote_settings['vote_submit_vote_value'];
            }
        } else { 
            $this->hotaru->messages[$this->lang["vote_settings_submit_vote_value_invalid"]] = "red";
            $error = 1;
            $submit_vote_value = $vote_settings['vote_submit_vote_value'];
        }
    
    
        // Check votes_to_promote
        if ($this->cage->post->keyExists('vote_votes_to_promote')) {
            $votes_to_promote = $this->cage->post->testInt('vote_votes_to_promote'); 
            if ($votes_to_promote < 1) {
                $this->hotaru->messages[$this->lang["vote_settings_votes_to_promote_invalid"]] = "red";
                $error = 1;
                $votes_to_promote = $vote_settings['vote_votes_to_promote'];
            }
        } else { 
            $this->hotaru->messages[$this->lang["vote_settings_votes_to_promote_invalid"]] = "red";
            $error = 1;
            $votes_to_promote = $vote_settings['vote_votes_to_promote'];
        }
        
        // Check upcoming duration
        if ($this->cage->post->keyExists('vote_upcoming_duration')) {
            $upcoming_duration = $this->cage->post->testInt('vote_upcoming_duration'); 
            if ($upcoming_duration < 1) {
                $this->hotaru->messages[$this->lang["vote_settings_upcoming_duration_invalid"]] = "red";
                $error = 1;
                $upcoming_duration = $vote_settings['vote_upcoming_duration'];
            }
        } else { 
            $this->hotaru->messages[$this->lang["vote_settings_upcoming_duration_invalid"]] = "red";
            $error = 1;
            $upcoming_duration = $vote_settings['vote_upcoming_duration'];
        }
        
        // Check no_front_page (deadline for front page)
        if ($this->cage->post->keyExists('vote_no_front_page')) {
            $no_front_page = $this->cage->post->testInt('vote_no_front_page'); 
            if ($no_front_page < 1) {
                $this->hotaru->messages[$this->lang["vote_settings_no_front_page_invalid"]] = "red";
                $error = 1;
                $no_front_page = $vote_settings['vote_no_front_page'];
            }
        } else { 
            $this->hotaru->messages[$this->lang["vote_settings_no_front_page_invalid"]] = "red";
            $error = 1;
            $no_front_page = $vote_settings['vote_no_front_page'];
        }
        
        // Use alerts
        if ($this->cage->post->keyExists('vote_use_alerts')) { 
            $use_alerts = 'checked'; 
        } else { 
            $use_alerts = ''; 
        }
        
        
        // Check alerts_to_bury
        if ($this->cage->post->keyExists('vote_alerts_to_bury')) { 
            $alerts_to_bury = $this->cage->post->testInt('vote_alerts_to_bury'); 
            if ($alerts_to_bury < 1) {
                $this->hotaru->messages[$this->lang["vote_settings_alerts_to_bury_invalid"] ] = "red";
                $error = 1;
                $alerts_to_bury = $vote_settings['vote_alerts_to_bury'];
            }
        } else { 
            $this->hotaru->messages[$this->lang["vote_settings_alerts_to_bury_invalid"] ] = "red";
            $error = 1;
            $alerts_to_bury = $vote_settings['vote_alerts_to_bury'];
        }
        
        
        // Check the status of our checkbox for physical delete
        if ($this->cage->post->keyExists('vote_physical_delete')) { 
            $physical_delete = 'checked'; 
        } else { 
            $physical_delete = ''; 
        }
        
        
        // Votes in Sidebar Posts
        if ($this->cage->post->keyExists('sb_votes')) { 
            $sidebar_votes = 'checked';
        } else { 
            $sidebar_votes = '';
        }
        
        // A plugin hook so other plugin developers can save settings   
        $this->pluginHook('vote_save_settings');
        
        // Save new settings...    
        $vote_settings['vote_submit_vote'] = $submit_vote;
        $vote_settings['vote_submit_vote_value'] = $submit_vote_value;
        $vote_settings['vote_votes_to_promote'] = $votes_to_promote;
        $vote_settings['vote_use_alerts'] = $use_alerts;
        $vote_settings['vote_alerts_to_bury'] = $alerts_to_bury;
        $vote_settings['vote_physical_delete'] = $physical_delete;
        $vote_settings['vote_upcoming_duration'] = $upcoming_duration;
        $vote_settings['vote_no_front_page'] = $no_front_page;
        $vote_settings['vote_sidebar_posts'] = $sidebar_votes;
        
        // parameters: plugin folder name, setting name, setting value
        $this->updateSetting('vote_settings', serialize($vote_settings));
        
        if ($error == 0) {
            $this->hotaru->messages[$this->lang["vote_settings_saved"]] = "green";
        }
        
        $this->hotaru->showMessages();
        
        return true;    
    } 

}
?>
