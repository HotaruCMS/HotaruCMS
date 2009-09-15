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

class VoteSettings extends Vote
{
    /**
     * Vote Settings Page
     */
    public function settings($folder) {
        global $hotaru, $cage, $lang;
        
         /* Allows us to call functions without specifying what plugin this is. */
        $this->setFolder($folder);
        
        // If the form has been submitted, go and save the data...
        if ($cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings(); 
        }    
        
        echo "<h1>" . $lang["vote_settings_header"] . "</h1>\n";
        
        // Get settings from the database if they exist...
        $vote_settings = unserialize($this->getSetting('vote_settings')); 
        
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
        $this->pluginHook('vote_settings_get_values');
        
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
        $this->pluginHook('vote_settings_form_1');
        
        echo "<br /><p><b>" . $lang["vote_settings_vote_promote_bury"] . "</b></p>";
        
        echo "<p>" . $lang["vote_settings_votes_to_promote"] . " <input type='text' size=5 name='vote_votes_to_promote' value='" . $votes_to_promote . "' /> <small> (Default: 5)</small></p>\n";
        echo "<p><input type='checkbox' name='vote_use_alerts' value='vote_use_alerts' " . $use_alerts . " > " . $lang["vote_settings_use_alerts"] . "</p>\n";
        echo "<p>" . $lang["vote_settings_alerts_to_bury"] . " <input type='text' size=5 name='vote_alerts_to_bury' value='" . $alerts_to_bury . "' /> <small> (Default: 5)</small></p>\n";
        
        echo "<p><input type='checkbox' id='vote_physical_delete' name='vote_physical_delete' " . $physical_delete . " /> " . $lang["vote_settings_physical_delete"] . "</p>";
            
        // A plugin hook so other plugin developers can show settings
        $this->pluginHook('vote_settings_form_2');
        
        echo "<br />\n";    
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $lang["vote_settings_save"] . "' />\n";
        echo "</form>\n";
    }
    
    
    /**
     * Save Vote Settings
     */
    public function saveSettings() {
        global $cage, $hotaru, $lang;
        
        $error = 0;
        
        // Get settings from the database if they exist...
        $vote_settings = unserialize($this->getSetting('vote_settings')); 
            
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
        $this->pluginHook('vote_save_settings');
        
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
        $this->updateSetting('vote_settings', serialize($vote_settings));
        
        if ($error == 0) {
            $hotaru->messages[$lang["vote_settings_saved"]] = "green";
        }
        
        $hotaru->showMessages();
        
        return true;    
    } 

}
?>
