<?php
/**
 *  SB Base Settings
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

class SbBaseSettings
{
     /**
     * Admin settings for the Submit plugin
     */
    public function settings($h)
    {
        // If the form has been submitted, go and save the data...
        if ($h->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings($h); 
        }    
        
        echo "<h1>" . $h->lang["sb_base_settings_header"] . "</h1>\n";
        
        $h->showMessage(); // Saved / Error message
        
        // Get settings from database if they exist...
        $sb_base_settings = $h->getSerializedSettings();
        
        $posts_per_page = $sb_base_settings['posts_per_page'];
        $archive = $sb_base_settings['archive'];
    
        $h->pluginHook('sb_base_settings_get_values');
        
        //...otherwise set to blank:
        if (!$posts_per_page) { $posts_per_page = 10; }
        if (!$archive) { $archive = 'no_archive'; }
        
        echo "<form name='sb_base_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=sb_base' method='post'>\n";

        echo "<p><input type='text' size=5 name='posts_per_page' value='" . $posts_per_page . "' /> ";
        echo $h->lang["sb_base_settings_posts_per_page"] . "</p>\n";
    
        $h->pluginHook('sb_base_settings_form');
    
        echo "<br />\n";

        echo $h->lang["sb_base_settings_post_archiving"] . "<br /><br />\n";
        echo $h->lang["sb_base_settings_post_archive_desc"] . "<br /><br />\n";
        echo "<select name='post_archive'>\n";
            echo "<option value='" . $archive . "'>" . $h->lang["sb_base_settings_post_archive_$archive"] . "</option>\n";
            echo '<option disabled>-----</option>';
            echo "<option value='no_archive'>" . $h->lang["sb_base_settings_post_archive_no_archive"] . "</option>\n";
            echo "<option value='180'>" . $h->lang["sb_base_settings_post_archive_180"] . "</option>\n";
            echo "<option value='365'>" . $h->lang["sb_base_settings_post_archive_365"] . "</option>\n";
            echo "<option value='730'>" . $h->lang["sb_base_settings_post_archive_730"] . "</option>\n";
            echo "<option value='1095'>" . $h->lang["sb_base_settings_post_archive_1095"] . "</option>\n";
        echo "</select>\n";
        echo $h->lang["sb_base_settings_post_archive"] . "<br /><br />\n";

        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $h->lang["main_form_save"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form>\n";
    }
    
    
    /**
     * Save Submit Settings
     */
    public function saveSettings($h) 
    {
        // Get current settings 
        $sb_base_settings = $h->getSerializedSettings();
        
        // Posts per page
        $posts_per_page = $h->cage->post->testInt('posts_per_page'); 
        if (!$posts_per_page) { 
            $posts_per_page = $sb_base_settings['posts_per_page']; 
        }
    
        // Post Archiving
        $archive = $h->cage->post->testAlnumLines('post_archive'); 
        if (!$archive) { 
            $archive = $sb_base_settings['archive']; 
        } 
        
        $h->pluginHook('sb_base_save_settings');
        
        $sb_base_settings['posts_per_page'] = $posts_per_page;
        $sb_base_settings['archive'] = $archive;
    
        $h->updateSetting('sb_base_settings', serialize($sb_base_settings));
        
        $h->message = $h->lang["main_settings_saved"];
        $h->messageType = "green";
        
        return true;    
    }
    
}
?>
