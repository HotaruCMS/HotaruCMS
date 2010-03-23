<?php
/**
 * File: /plugins/socialbar/socialbar_settings.php
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
 * @author    shibuya246 <blog@shibuya246.com>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

class SocialBarSettings
{
    /**
     * SocialBar Settings Page
     */
    public function settings($h) {

        // If the form has been submitted, go and save the data...
        if ($h->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings($h); 
        }    
        
        echo "<h1>" . $h->lang["socialbar_settings_header"] . "</h1>\n";
        
        // Get settings from the database if they exist...
        $socialbar_settings = unserialize($h->getSetting('socialbar_settings'));
        
        $logo_filename = $socialbar_settings['logo_filename'];
        
        //...otherwise set to blank or default:
        if (!$logo_filename) { $logo_filename = ''; }
        
        // A plugin hook so other plugin developers can add settings
        $h->pluginHook('socialbar_settings_get_values');
        
        // The form should be submitted to the admin_index.php page:
        echo "<form name='socialbar_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=socialbar' method='post'>\n";
        
        echo "<p><b>Logo for socialbar</b></p>";
        
        echo "<p>Upload<input type='text' size=5 name='socialbar_logo_filename' value='" . $logo_filename . "' /> <small> (Max size is 120px)</small></p>\n";
       
        
        echo "<br />\n";    
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $h->lang["main_form_save"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form>\n";
    }
    
    
    /**
     * Save SocialBar Settings
     */
    public function saveSettings($h) {

        $error = 0;
        
        // Get settings from the database if they exist...
        $socialbar_settings = unserialize($h->getSetting('socialbar_settings'));
        
        // Check file logo
        if ($h->cage->post->keyExists('socialbar_logo_filename')) {
            $logo_filename = $h->cage->post->testAlpha('socialbar_logo_filename');
        }    
    
        // Save new settings...          
        $socialbar_settings['socialbar_logo_filename'] = $logo_filename;
              
        $h->updateSetting('socialbar_settings', serialize($socialbar_settings));
        
        if ($error == 0) {
            $h->messages[$h->lang["main_settings_saved"]] = "green";
        }
        
        $h->showMessages();
        
        return true;    
    } 

}
?>
