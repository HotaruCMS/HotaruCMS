<?php
/**
 * File: /plugins/who_voted/who_voted_settings.php
 * Purpose: Admin settings for the Who's Online plugin
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
 
class WhoVotedSettings
{
     /**
     * Admin settings for who's online
     */
    public function settings($h)
    {
        // If the form has been submitted, go and save the data...
        if ($h->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings($h); 
        }
        
        echo "<h1>" . $h->lang["who_voted_settings_header"] . "</h1>\n";
        echo "<p>" . $h->lang["who_voted_settings_note"] . "</p>";
          
        // Get settings from database if they exist...
        $who_voted_settings = $h->getSerializedSettings('who_voted');
        $limit = $who_voted_settings['who_voted_num'];
        $avatars = $who_voted_settings['who_voted_avatars'];
        $avatar_size = $who_voted_settings['who_voted_avatar_size'];
        $names = $who_voted_settings['who_voted_names'];
        $show_title = $who_voted_settings['who_voted_widget_title'];
        
        if (!$limit) { $limit = 0; }
        
        $h->pluginHook('who_voted_settings_get_values');
            
        echo "<form name='who_voted_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=who_voted' method='post'>\n";
        
        echo "<p><input type='checkbox' name='show_title' value='show_title' " . $show_title . ">&nbsp;&nbsp;";
            echo $h->lang["who_voted_settings_show_widget_title"] . "</p>\n";
        echo "<p><input type='text' size=5 name='limit' value='" . $limit . "'> - " . $h->lang["who_voted_settings_num_visitors"] . "</p>\n";

        echo "<p><input type='checkbox' name='avatars' value='avatars' " . $avatars . ">&nbsp;&nbsp;";
            echo $h->lang["who_voted_settings_avatars"] . "&nbsp;&nbsp; ";
            echo $h->lang["who_voted_settings_avatar_size"];
            echo " <input type='text' size=5 name='avatar_size' value='" . $avatar_size . "'></p>\n";
            
        echo "<p><input type='checkbox' name='names' value='names' " . $names . ">&nbsp;&nbsp;";
            echo $h->lang["who_voted_settings_names"] . "</p>\n";
    
        $h->pluginHook('who_voted_settings_form');
                
        echo "<br /><br />\n";    
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $h->lang["main_form_save"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form>\n";
    }
    
    
     /**
     * Save admin settings for who's online
     *
     * @return true
     */
    public function saveSettings($h)
    {
        // get settings again
        $who_voted_settings = $h->getSerializedSettings('who_voted');
        
        // number of users on the who's online widget:
        if ($h->cage->post->keyExists('limit')) { 
            $limit = $h->cage->post->testInt('limit');
        } else {
            $limit = 0; // existing setting
        }
        
        
        // show title on who's online widget:
        if ($h->cage->post->keyExists('show_title')) { 
            $show_title = 'checked';
        } else {
            $show_title = ''; 
        }

        
        // show avatars:
        if ($h->cage->post->keyExists('avatars')) { 
            $avatars = 'checked';
        } else {
            $avatars = ''; 
        }
        
        
        // avatar size:
        if ($h->cage->post->keyExists('avatar_size')) { 
            $avatar_size = $h->cage->post->testInt('avatar_size');
        } else {
            $avatar_size = $who_voted_settings['who_voted_avatar_size']; // existing setting
        }
        
        
        // show names:
        if ($h->cage->post->keyExists('names')) { 
            $names = 'checked';
        } else {
            $names = ''; 
        }
        
        
        $h->pluginHook('who_voted_save_settings');
        
        if (is_numeric($limit))
        { 
            $who_voted_settings['who_voted_num'] = $limit;
            $who_voted_settings['who_voted_widget_title'] = $show_title;
            $who_voted_settings['who_voted_avatars'] = $avatars;
            $who_voted_settings['who_voted_avatar_size'] = $avatar_size;
            $who_voted_settings['who_voted_names'] = $names;
            $h->updateSetting('who_voted_settings', serialize($who_voted_settings));
            
            $h->message = $h->lang["main_settings_saved"];
            $h->messageType = "green";
        } else {
            $h->message = $h->lang["main_settings_not_saved"];
            $h->messageType = "red";
        }
        $h->showMessage();
        
        return true;    
    }
    
}
?>
