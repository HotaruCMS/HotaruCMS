<?php
/**
 * File: /plugins/tag_cloud/tag_cloud_settings.php
 * Purpose: Admin settings for the Tag Cloud plugin
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
 
class TagCloudSettings
{
     /**
     * Admin settings for tag cloud
     */
    public function settings($h)
    {
        // If the form has been submitted, go and save the data...
        if ($h->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings($h); 
        }
        
        echo "<h1>" . $h->lang["tag_cloud_settings_header"] . "</h1>\n";
        echo "<p>" . $h->lang["tag_cloud_settings_note"] . "</p>";
          
        // Get settings from database if they exist...
        $tag_cloud_settings = $h->getSerializedSettings();
        $num_tags_page = $tag_cloud_settings['tags_num_tags_page'];
        $num_tags_widget = $tag_cloud_settings['tags_num_tags_widget'];
        $show_widget_title = $tag_cloud_settings['tags_widget_title'];
        
        if (!$num_tags_page) { $num_tags_page = 100; }
        if (!$num_tags_widget) { $num_tags_widget = 25; }
        if (!$show_widget_title) { $show_widget_title = ''; }
    
        $h->pluginHook('tag_cloud_settings_get_values');
        
        //...otherwise set to blank:
        if (!$num_tags_page) { $num_tags_page = 'subconcious'; }  // This is the default in tags' generic code
            
        echo "<form name='tag_cloud_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=tag_cloud' method='post'>\n";
        
        echo "<h3>" . $h->lang["tag_cloud_settings_main_cloud"] . "</h3><br />";
            
        echo "<input type='text' size=5 name='num_tags_page' value='" . $num_tags_page . "'> - " . $h->lang["tag_cloud_settings_num_tags_page"] . "\n";    
        
        echo "<br /><br />";
        
        echo "<h3>" . $h->lang["tag_cloud_settings_widget_cloud"] . "</h3><br />";
            
        echo "<input type='checkbox' name='show_widget_title' value='show_widget_title' " . $show_widget_title . ">&nbsp;&nbsp;" ;
            echo $h->lang["tag_cloud_settings_show_widget_title"] . "<br /><br />\n";
        echo "<input type='text' size=5 name='num_tags_widget' value='" . $num_tags_widget . "'> - " . $h->lang["tag_cloud_settings_num_tags_widget"] . "\n";    
    
        $h->pluginHook('tag_cloud_settings_form');
                
        echo "<br /><br />\n";    
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $h->lang["main_form_save"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form>\n";
    }
    
    
     /**
     * Save admin settings for tag cloud
     *
     * @return true
     */
    public function saveSettings($h)
    {
        // number of tags on the main tag cloud page:
        if ($h->cage->post->keyExists('num_tags_page')) { 
            $num_tags_page = $h->cage->post->testInt('num_tags_page');
        } else {
            $num_tags_page = 100; // default
        }
        
        // show title on tag cloud widget:
        if ($h->cage->post->keyExists('show_widget_title')) { 
            $show_widget_title = 'checked';
        } else {
            $show_widget_title = ''; 
        }
        
        // number of tags on the tag cloud widget:
        if ($h->cage->post->keyExists('num_tags_widget')) { 
            $num_tags_widget = $h->cage->post->testInt('num_tags_widget');
        } else {
            $num_tags_widget = 25; // default
        }
        
        $h->pluginHook('tag_cloud_save_settings');
        
        if (is_numeric($num_tags_page) && is_numeric($num_tags_widget))
        { 
            $tag_cloud_settings = $h->getSerializedSettings();
            $tag_cloud_settings['tags_num_tags_page'] = $num_tags_page;
            $tag_cloud_settings['tags_num_tags_widget'] = $num_tags_widget;
            $tag_cloud_settings['tags_widget_title'] = $show_widget_title;
            $h->updateSetting('tag_cloud_settings', serialize($tag_cloud_settings));
            
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
