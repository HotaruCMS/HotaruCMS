<?php
/**
 * File: /plugins/tags/tags_settings.php
 * Purpose: Admin settings for the tags plugin
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
 
class tagsSettings extends tags
{
     /**
     * Admin settings for tags
     */
    public function settings()
    {
        $this->includeLanguage();
        
        // If the form has been submitted, go and save the data...
        if ($this->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings(); 
        }
        
        echo "<h1>" . $this->lang["tags_settings_header"] . "</h1>\n";
        echo "<p>" . $this->lang["tags_settings_note"] . "</p>";
          
        // Get settings from database if they exist...
        $tags_settings = $this->getSerializedSettings();
        $num_tags_page = $tags_settings['tags_num_tags_page'];
        $num_tags_widget = $tags_settings'tags_num_tags_widget'];
        $show_widget_title = $tags_settings'tags_widget_title'];
        
        if (!$num_tags_page) { $num_tags_page = 100; }
        if (!$num_tags_widget) { $num_tags_widget = 25; }
        if (!$show_widget_title) { $show_widget_title = ''; }
    
        $this->pluginHook('tags_settings_get_values');
        
        //...otherwise set to blank:
        if (!$num_tags_page) { $num_tags_page = 'subconcious'; }  // This is the default in tags' generic code
            
        echo "<form name='tags_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=tags' method='post'>\n";
        
        echo "<h3>" . $this->lang["tags_settings_main_cloud"] . "</h3><br />";
            
        echo "<input type='text' size=5 name='num_tags_page' value='" . $num_tags_page . "'> - " . $this->lang["tags_settings_num_tags_page"] . "\n";    
        
        echo "<br /><br />";
        
        echo "<h3>" . $this->lang["tags_settings_widget_cloud"] . "</h3><br />";
            
        echo "<input type='checkbox' name='show_widget_title' value='show_widget_title' " . $show_widget_title . ">&nbsp;&nbsp;" ;
            echo $this->lang["tags_settings_show_widget_title"] . "<br /><br />\n";
        echo "<input type='text' size=5 name='num_tags_widget' value='" . $num_tags_widget . "'> - " . $this->lang["tags_settings_num_tags_widget"] . "\n";    
    
        $this->pluginHook('tags_settings_form');
                
        echo "<br /><br />\n";    
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $this->lang["tags_settings_save"] . "' />\n";
        echo "</form>\n";
    }
    
    
     /**
     * Save admin settings for tags
     *
     * @return true
     */
    public function saveSettings()
    {
        // number of tags on the main tag cloud page:
        if ($this->cage->post->keyExists('num_tags_page')) { 
            $num_tags_page = $this->cage->post->testInt('num_tags_page');
        } else {
            $num_tags_page = 100; // default
        }
        
        // show title on tag cloud widget:
        if ($this->cage->post->keyExists('show_widget_title')) { 
            $show_widget_title = 'checked';
        } else {
            $show_widget_title = ''; 
        }
        
        // number of tags on the tag cloud widget:
        if ($this->cage->post->keyExists('num_tags_widget')) { 
            $num_tags_widget = $this->cage->post->testInt('num_tags_widget');
        } else {
            $num_tags_widget = 25; // default
        }
        
        $this->pluginHook('tags_save_settings');
        
        if (is_numeric($num_tags_page) && is_numeric($num_tags_widget))
        { 
            $tags_settings = $this->getSerializedSettings();
            $tags_settings'tags_num_tags_page'] = $num_tags_page;
            $tags_settings'tags_num_tags_widget'] = $num_tags_widget;
            $tags_settings'tags_widget_title'] = $show_widget_title;
            $this->updateSetting('tags_settings', serialize($tags_settings));
            
            $this->hotaru->message = $this->lang["tags_settings_saved"];
            $this->hotaru->messageType = "green";
        } else {
            $this->hotaru->message = $this->lang["tags_settings_not_saved"];
            $this->hotaru->messageType = "red";
        }
        $this->hotaru->showMessage();
        
        return true;    
    }
    
}
?>
