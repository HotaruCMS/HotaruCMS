<?php
/**
 * File: /plugins/disqus/disqus_settings.php
 * Purpose: Admin settings for the disqus plugin
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
 
class DisqusSettings
{
     /**
     * Admin settings for disqus
     */
    public function settings($h)
    {
        // If the form has been submitted, go and save the data...
        if ($h->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings($h); 
        }
        
        echo "<h1>" . $h->lang["disqus_settings_header"] . "</h1>\n";
          
        // Get settings from database if they exist...
        $shortname = $h->getSetting('disqus_shortname');
    
        $h->pluginHook('disqus_settings_get_values');
        
        //...otherwise set to blank:
        if (!$shortname) { $shortname = 'subconcious'; }  // This is the default in Disqus' generic code
            
        echo "<form name='disqus_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=disqus' method='post'>\n";
        
        echo "<p>" . $h->lang["disqus_settings_instructions"] . "</p><br />";
            
        echo "<p>" . $h->lang["disqus_settings_shortname"] . " <input type='text' size=30 name='shortname' value='" . $shortname . "'><br />" . $h->lang["disqus_settings_shortname_note"] . "</p>\n";    
    
        $h->pluginHook('disqus_settings_form');
                
        echo "<br /><br />\n";    
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $h->lang["disqus_settings_save"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form>\n";
    }
    
    
     /**
     * Save admin settings for disqus
     *
     * @return true
     */
    public function saveSettings($h)
    {
        // short name
        if ($h->cage->post->keyExists('shortname')) { 
            $shortname = $h->cage->post->testAlnumLines('shortname');
        } else {
            $shortname = 'subconcious'; // This is the default in Disqus' generic code
        }
        
        $h->pluginHook('disqus_save_settings');
        
        $h->updateSetting('disqus_shortname', $shortname);
        
        $h->message = $h->lang["disqus_settings_saved"];
        $h->messageType = "green";
        $h->showMessage();
        
        return true;    
    }
    
}
?>
