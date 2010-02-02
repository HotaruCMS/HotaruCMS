<?php
/**
 * Admin Email Settings
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
 
class adminEmailSettings extends adminEmail
{
     /**
     * Admin settings for the Admin Email plugin
     */
    public function settings($h)
    {
        $userlist = $h->userIdNameList(); // get all users IDs and names.
        
        // If the form has been submitted, go and save the data...
        if ($h->cage->post->getAlpha('submitted') == 'true') { 
            $error = $this->saveSettings($h); 
            if (!$error) { 
                $this->doAdminEmail($h);  // calls main function in admin_email.php
            } else {
                foreach ($error as $err) {
                    $h->message = $h->lang["admin_email_error_" . $err];
                    $h->messageType = 'red';
                    $h->showMessage();
                }
            }
        }
        
        if ($h->cage->get->getAlpha('status') == 'done') {
            $h->message = $h->lang["admin_email_sent"];
            $h->messageType = 'green';
            $h->showMessage();
        } 
        
        if ($h->cage->get->getAlpha('status') == 'simulated') {
            $h->message = $h->lang["admin_email_simulated"];
            $h->messageType = 'green';
            $h->showMessage();
        } 
                
        // Get settings from database if they exist...
        $admin_email_settings = $h->getSerializedSettings();
        $batch_size = $admin_email_settings['admin_email_batch_size'];
        $pause = $admin_email_settings['admin_email_pause'];
        $recipients = unserialize($admin_email_settings['admin_email_recipients']);
        $subject = $admin_email_settings['admin_email_subject'];
        $body = $admin_email_settings['admin_email_body'];
        $send_self = $admin_email_settings['admin_email_send_self'];
        $send_opted_out = $admin_email_settings['admin_email_send_opted_out'];
        $simulation = $admin_email_settings['admin_email_simulation'];
        
        if (!$batch_size) { $batch_size = 20; }
        if (!$pause) { $pause = 10; }
        if (!$recipients) { $recipients = array(); }
        if (!$subject) { $subject = ''; }
        if (!$body) { $body = ''; }
        if (!$send_self) { $send_self = ''; }
        if (!$send_opted_out) { $send_opted_out = ''; }
        if (!$simulation) { $simulation = ''; }
        
        // make recipients an array:
        if (is_object($recipients)) { 
            $recipients_array = array();
            foreach ($recipients as $recip) {
                array_push($recipients_array, $recip);
            }
            $recipients = $recipients_array;
        }
    
        $h->pluginHook('admin_email_get_values');

        echo "<h1>" . $h->lang["admin_email"] . "</h1>\n";
        
        echo "<form name='admin_email_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=admin_email' method='post'>\n";

        echo "<p>" . $h->lang["admin_email_instructions"] . "</p>\n";
                
        $groups = array('admin', 'supermod', 'moderator', 'member', 'undermod', 'banned', 'killspammed');

        echo "<p>" . $h->lang["admin_email_choose_recipients"] . "</p>";
        echo "<div style='float: right;'>\n";
        echo "<p><select name='recipients[]' multiple='yes' style='width: 12em;' size='28'>\n";
        
        foreach ($groups as $group => $gname) {
            if (in_array($gname, $recipients)) {
                echo "<option value='" . $gname . "' selected>" . $gname . " users</option>\n";
            } else {
                echo "<option value='" . $gname . "'>" . $gname . " users</option>\n";
            }
        }
        echo "<option value='' disabled>-----</option>\n";
        if ($userlist) {
            foreach ($userlist as $user) {
                if (in_array($user->user_id, $recipients)) {
                    echo "<option value='" . $user->user_id . "' selected>" . $user->user_username . "</option>\n";
                } else {
                    echo "<option value='" . $user->user_id . "'>" . $user->user_username . "</option>\n";
                }
            }
        }
        echo "</select>\n</div>\n";
        
        echo "<p><input type='text' size=5 name='batch_size' value='" . $batch_size . "' /> " . $h->lang["admin_email_batch_size"] . "</p>\n";
        echo "<p><input type='text' size=5 name='pause' value='" . $pause . "' /> " . $h->lang["admin_email_pause"] . "</p>\n";
        
        echo "<p>" . $h->lang["admin_email_body_tip"] . "</p>";
        
        echo "<table id='admin_email_table'>";
        echo "<tr><td>" . $h->lang["admin_email_subject"] . "&nbsp;";
        echo "<input type='text' id='subject' name='subject' size='42' value='" . $subject . "' required='yes' /></td></tr>\n";
        echo "<tr><td><textarea cols='66' rows='15' id='body' name='body' value='' required='yes' />" . $body . "</textarea></td></tr>\n";
        echo "</table>";
        
        echo "<p><input type='checkbox' name='send_self' value='send_self' " . $send_self . " >&nbsp;&nbsp;" . $h->lang["admin_email_send_to_self"] . "</p>\n";    
        echo "<p><input type='checkbox' name='send_opted_out' value='send_opted_out' " . $send_opted_out . " >&nbsp;&nbsp;" . $h->lang["admin_email_send_to_opted_out"] . "</p>\n";    
        echo "<p><input type='checkbox' name='simulation' value='simulation' " . $simulation . " >&nbsp;&nbsp;" . $h->lang["admin_email_simulation"] . "</p>\n";
                            
        $h->pluginHook('admin_email_settings_form');
                
        echo "<br />\n";    
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $h->lang["admin_email_send"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form>\n";
    }
    
    
    /**
     * Save all
     */
    public function saveSettings($h)
    {
        $error = array(); 
        $i=0;
        
        // Get settings from database if they exist...
        $admin_email_settings = $h->getSerializedSettings();
        
        // save batch size
        if ($h->cage->post->keyExists('batch_size')) { $batch_size = $h->cage->post->testInt('batch_size'); } 
        if ($batch_size) { $admin_email_settings['admin_email_batch_size'] = $batch_size; } else { $error[$i] = 'size'; $i++; }

        // save pause
        if ($h->cage->post->keyExists('pause')) { $pause = $h->cage->post->testInt('pause'); } 
        if ($pause) { $admin_email_settings['admin_email_pause'] = $pause; } else { $error[$i] = 'pause'; $i++; }
        
        // save recipients
        if ($h->cage->post->keyExists('recipients')) { 
            $admin_email_settings['admin_email_recipients'] = serialize($h->cage->post->keyExists('recipients'));
        } else { $error[$i] = 'recipients'; $i++; }
        
        // save subject
        $subject = sanitize($h->cage->post->getHtmLawed('subject'), 'all');
        if ($subject) { $admin_email_settings['admin_email_subject'] = $subject; } else { $error[$i] = 'subject'; $i++; }
        
        // save body
        $body = sanitize($h->cage->post->getHtmLawed('body'), 'all');
        if ($body) { $admin_email_settings['admin_email_body'] = $body; } else { $error[$i] = 'body'; $i++; }
        
        // save send self
        if ($h->cage->post->keyExists('send_self')) { 
            $admin_email_settings['admin_email_send_self'] = 'checked';
        } else {
            $admin_email_settings['admin_email_send_self'] = '';
        }
        
        // save send self
        if ($h->cage->post->keyExists('send_opted_out')) { 
            $admin_email_settings['admin_email_send_opted_out'] = 'checked';
        } else {
            $admin_email_settings['admin_email_send_opted_out'] = '';
        }
        
        // save simulation
        if ($h->cage->post->keyExists('simulation')) { 
            $admin_email_settings['admin_email_simulation'] = 'checked';
        } else {
            $admin_email_settings['admin_email_simulation'] = '';
        }
                
        $h->pluginHook('admin_email_save_settings');
        
        $h->updateSetting('admin_email_settings', serialize($admin_email_settings));
        
        return $error;
    }
}
?>
