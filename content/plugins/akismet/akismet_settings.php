<?php
/**
 * File: /plugins/akismet/akismet_settings.php
 * Purpose: Admin settings for the akismet plugin
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
 
class AkismetSettings extends HotaruAkismet
{
     /**
     * Admin settings for akismet
     */
    public function settings()
    {
        $this->includeLanguage(); // include it here because it's only used for the settings page
        
        // If the form has been submitted, go and save the data...
        if ($this->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings(); 
        }
        
        echo "<h1>" . $this->lang["akismet_settings_header"] . "</h1>\n";
          
        // Get settings from database if they exist...
        $akismet_settings = $this->getSerializedSettings();
        $akismetposts = $akismet_settings['akismet_use_posts'];
        $akismetcomments = $akismet_settings['akismet_use_comments'];
        $akismetkey = $akismet_settings['akismet_key'];
    
        $this->pluginHook('akismet_settings_get_values');
        
        //...otherwise set to blank:
        if (!$akismetuse) { $akismetuse = ''; } else { $akismetuse = 'checked'; }
        if (!$akismetkey) { $akismetkey = ''; } 
            
        echo "<form name='akismet_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=akismet' method='post'>\n";
        
        echo "<p>" . $this->lang["akismet_settings_instructions"] . "</p><br />";
            
        echo "<p><input type='checkbox' name='akismet_use_posts' value='akismet_use_posts' " . $akismetposts . " >&nbsp;&nbsp;";
        echo $this->lang["akismet_settings_use_posts"] . "</p>\n"; 
        echo "<p><input type='checkbox' name='akismet_use_comments' value='akismet_use_comments' " . $akismetcomments . " >&nbsp;&nbsp;";
        echo $this->lang["akismet_settings_use_comments"] . "</p>\n"; 
        echo "<p>" . $this->lang["akismet_settings_key"] . " <input type='text' size=30 name='akismet_key' value='" . $akismetkey . "'></p>\n";    
    
        $this->pluginHook('akismet_settings_form');
                
        echo "<br /><br />\n";    
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $this->lang["akismet_settings_save"] . "' />\n";
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
            
        // use akismet for posts
        if ($this->cage->post->keyExists('akismet_use_posts')) { 
            $akismetposts = "checked";
        } else {
            $akismetposts = "";
        }
        
        // use akismet for comments
        if ($this->cage->post->keyExists('akismet_use_comments')) { 
            $akismetcomments = "checked";
        } else {
            $akismetcomments = "";
        }
        
        // akismet key
        if ($this->cage->post->keyExists('akismet_key')) { 
            $akismetkey = $this->cage->post->testAlnumLines('akismet_key');
            $error = 0;
        } else {
            $akismetkey = ''; 
            $error = 1;
            $this->hotaru->message = $this->lang["akismet_settings_no_key"];
            $this->hotaru->messageType = "red";
        }
        
        $this->pluginHook('akismet_save_settings');
        
        if ($error == 0) {
            // save settings
            $akismet_settings['akismet_use_posts'] = $akismetposts;
            $akismet_settings['akismet_use_comments'] = $akismetcomments;
            $akismet_settings['akismet_key'] = $akismetkey;
            $this->updateSetting('akismet_settings', serialize($akismet_settings));
            
            $this->hotaru->message = $this->lang["akismet_settings_saved"];
            $this->hotaru->messageType = "green";
        }
        $this->hotaru->showMessage();
        
        return true;    
    }
    
}
?>