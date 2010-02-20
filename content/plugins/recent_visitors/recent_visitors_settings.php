<?php
/**
 * File: /plugins/recent_visitors/recent_visitors_settings.php
 * Purpose: Admin settings for the Recent Visitors plugin
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
 
class RecentVisitorsSettings
{
     /**
     * Admin settings for recent visitors
     */
    public function settings($h)
    {
        // If the form has been submitted, go and save the data...
        if ($h->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings($h); 
        }
        
        echo "<h1>" . $h->lang["recent_visitors_settings_header"] . "</h1>\n";
        echo "<p>" . $h->lang["recent_visitors_settings_note"] . "</p>";
          
        // Get settings from database if they exist...
        $recent_visitors_settings = $h->getSerializedSettings('recent_visitors');
        $limit = $recent_visitors_settings['visitors_num'];
        $list = $recent_visitors_settings['visitors_list'];
        $avatars = $recent_visitors_settings['visitors_avatars'];
        $avatar_size = $recent_visitors_settings['visitors_avatar_size'];
        $avatar_filter = $recent_visitors_settings['visitors_avatar_filter'];
        $names = $recent_visitors_settings['visitors_names'];
        $show_title = $recent_visitors_settings['visitors_widget_title'];
        $show_get_avatar = $recent_visitors_settings['visitors_widget_get_avatar'];
        
        $h->pluginHook('recent_visitors_settings_get_values');
            
        echo "<form name='recent_visitors_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=recent_visitors' method='post'>\n";
        
        echo "<p><input type='checkbox' name='show_title' value='show_title' " . $show_title . ">&nbsp;&nbsp;";
            echo $h->lang["recent_visitors_settings_show_widget_title"] . "</p>\n";
        echo "<p><input type='text' size=5 name='limit' value='" . $limit . "'> - " . $h->lang["recent_visitors_settings_num_visitors"] . "</p>\n";
        echo "<p><input type='checkbox' name='list' value='list' " . $list . ">&nbsp;&nbsp;";
            echo $h->lang["recent_visitors_settings_list"] . "</p>\n";

        echo "<p><input type='checkbox' name='avatars' value='avatars' " . $avatars . ">&nbsp;&nbsp;";
            echo $h->lang["recent_visitors_settings_avatars"] . "&nbsp;&nbsp; ";
            echo $h->lang["recent_visitors_settings_avatar_size"];
            echo " <input type='text' size=5 name='avatar_size' value='" . $avatar_size . "'></p>\n";
            
        echo "<p><input type='checkbox' name='avatar_filter' value='avatar_filter' " . $avatar_filter . ">&nbsp;&nbsp;";
            echo $h->lang["recent_visitors_settings_avatar_filter"] . "</p>\n";
            
        echo "<p><input type='checkbox' name='names' value='names' " . $names . ">&nbsp;&nbsp;";
            echo $h->lang["recent_visitors_settings_names"] . "</p>\n";
            
        echo "<p><input type='checkbox' name='get_avatar' value='gat_avatar' " . $show_get_avatar . ">&nbsp;&nbsp;";
            echo $h->lang["recent_visitors_settings_show"] . " &quot;" . $h->lang["recent_visitors_widget_get_avatar"] . "&quot;</p>\n";
    
        $h->pluginHook('recent_visitors_settings_form');
                
        echo "<br /><br />\n";    
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $h->lang["main_form_save"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form>\n";
    }
    
    
     /**
     * Save admin settings for recent visitors
     *
     * @return true
     */
    public function saveSettings($h)
    {
        // get settings again
        $recent_visitors_settings = $h->getSerializedSettings('recent_visitors');
        
        // number of users on the recent visitors widget:
        if ($h->cage->post->keyExists('limit')) { 
            $limit = $h->cage->post->testInt('limit');
        } else {
            $limit = $recent_visitors_settings['visitors_num']; // existing setting
        }
        
        
        // show title on recent visitors widget:
        if ($h->cage->post->keyExists('show_title')) { 
            $show_title = 'checked';
        } else {
            $show_title = ''; 
        }
        
        
        // show as a list:
        if ($h->cage->post->keyExists('list')) { 
            $list = 'checked';
        } else {
            $list = ''; 
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
            $avatar_size = $recent_visitors_settings['visitors_avatar_size']; // existing setting
        }
        
        
        // filter users with no avatars:
        if ($h->cage->post->keyExists('avatar_filter')) { 
            $avatar_filter = 'checked';
        } else {
            $avatar_filter = ''; 
        }
        
        
        // show names:
        if ($h->cage->post->keyExists('names')) { 
            $names = 'checked';
        } else {
            $names = ''; 
        }
        
        
        $h->pluginHook('recent_visitors_save_settings');
        
        if (is_numeric($limit))
        { 
            $recent_visitors_settings['visitors_num'] = $limit;
            $recent_visitors_settings['visitors_widget_title'] = $show_title;
            $recent_visitors_settings['visitors_list'] = $list;
            $recent_visitors_settings['visitors_avatars'] = $avatars;
            $recent_visitors_settings['visitors_avatar_size'] = $avatar_size;
            $recent_visitors_settings['visitors_avatar_filter'] = $avatar_filter;
            $recent_visitors_settings['visitors_names'] = $names;
            $h->updateSetting('recent_visitors_settings', serialize($recent_visitors_settings));
            
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
