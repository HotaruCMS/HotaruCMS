<?php
/**
 * File: /plugins/stop_spam/stop_spam_settings.php
 * Purpose: Admin settings for the Stop Spam plugin
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
 
class StopSpamSettings extends StopSpam
{
     /**
     * Admin settings for stop_spam
     */
    public function settings()
    {
        $this->includeLanguage(); 
        
        // If the form has been submitted, go and save the data...
        if ($this->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings(); 
        }
        
        echo "<h1>" . $this->lang["stop_spam_settings_header"] . "</h1>\n";
          
        // Get settings from database if they exist...
        $stop_spam_key = $this->getSetting('stop_spam_key');
        $stop_spam_type = $this->getSetting('stop_spam_type');
    
        $this->pluginHook('stop_spam_settings_get_values');
        
        //...otherwise set to blank:
        if (!$stop_spam_key) { $stop_spam_key = ''; } 
        if (!$stop_spam_type) { $stop_spam_type = 'go_pending'; }
        
        // determine which radio button is checked
        if ($stop_spam_type == 'go_pending') { 
            $go_pending = 'checked'; 
            $block_reg = ''; 
        } else {
            $go_pending = ''; 
            $block_reg = 'checked'; 
        }
            
        echo "<form name='stop_spam_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=stop_spam' method='post'>\n";
        
        echo "<p>" . $this->lang["stop_spam_settings_instructions"] . "</p><br />";
            
        echo "<p>" . $this->lang["stop_spam_settings_key"] . " <input type='text' size=30 name='stop_spam_key' value='" . $stop_spam_key . "'></p>\n";    
        
        echo "<p><input type='radio' name='ss_type' value='go_pending' " . $go_pending . "> " . $this->lang["stop_spam_settings_go_pending"] . "</p>\n";    
        echo "<p><input type='radio' name='ss_type' value='block_reg' " . $block_reg . "> " . $this->lang["stop_spam_settings_block_reg"] . "</p>\n";    
    
        $this->pluginHook('stop_spam_settings_form');
                
        echo "<br /><br />\n";    
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $this->lang["stop_spam_settings_save"] . "' />\n";
        echo "<input type='hidden' name='token' value='" . $this->hotaru->token . "' />\n";
        echo "</form>\n";
    }
    
    
     /**
     * Save admin settings for akismet
     *
     * @return true
     */
    public function saveSettings()
    {
        $error = 0;
        
        // stop forum spam key
        if ($this->cage->post->keyExists('stop_spam_key')) { 
            $stop_spam_key = $this->cage->post->testAlnumLines('stop_spam_key');
            $error = 0;
        } else {
            $stop_spam_key = ''; 
            $error = 1;
            $this->hotaru->message = $this->lang["stop_spam_settings_no_key"];
            $this->hotaru->messageType = "red";
        }
        
    
        // stop forum spam type
        if ($this->cage->post->keyExists('ss_type')) { 
            $stop_spam_type = $this->cage->post->testAlnumLines('ss_type');
        } else {
            $stop_spam_type = ''; 
        }
        
        
        $this->pluginHook('stop_spam_save_settings');
        
        if ($error == 0) {
            // save settings
            $this->updateSetting('stop_spam_key', $stop_spam_key);
            $this->updateSetting('stop_spam_type', $stop_spam_type);
            
            $this->hotaru->message = $this->lang["stop_spam_settings_saved"];
            $this->hotaru->messageType = "green";
        }
        $this->hotaru->showMessage();
        
        return true;    
    }
    
}
?>