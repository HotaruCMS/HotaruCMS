<?php
/**
 * File: /plugins/sidebar_posts/sidebar_posts_settings.php
 * Purpose: Admin settings for the sidebar posts plugin
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

class SidebarPostsSettings extends SidebarPosts
{
     /**
     * Admin settings for Sidebar Posts
     */
    public function settings()
    {
        // If the form has been submitted, go and save the data...
        if ($this->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings(); 
        }
        
        echo "<h1>" . $this->lang["sidebar_posts_settings_header"] . "</h1>\n";
          
        // Get settings from database if they exist...
        $box = $this->getSetting('sidebar_posts_box');
    
        $this->pluginHook('sidebar_posts_settings_get_values');
        
        //...otherwise set to blank:
        if (!$box) { $default_box = 'checked'; $custom_box = ''; }
        
        // which is checked?
        if ($box == 'default') { $default_box = 'checked'; $custom_box = ''; }
        if ($box == 'custom') { $default_box = ''; $custom_box = 'checked'; }
        
        echo "<form name='sidebar_posts_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=sidebar_posts' method='post'>\n";
        
        echo "<p>" . $this->lang["sidebar_posts_settings_instructions"] . "</p><br />";
        
        echo "<p><input type='radio' name='box' value='defaultbox' " . $default_box . " >&nbsp;&nbsp;" . $this->lang["sidebar_posts_settings_default_box"] . "</p>\n";    
        echo "<p><input type='radio' name='box' value='custombox' " . $custom_box . " >&nbsp;&nbsp;" . $this->lang["sidebar_posts_settings_custom_box"] . "</p>\n"; 
        
        $this->pluginHook('sidebar_posts_settings_form');
        
        echo "<br />" . $this->lang["sidebar_posts_settings_custom_box_note"]; 
                
        echo "<br /><br />\n";    
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $this->lang["sidebar_posts_settings_save"] . "' />\n";
        echo "</form>\n";
    }
    
    
     /**
     * Save admin settings for sidebar_posts
     *
     * @return true
     */
    public function saveSettings()
    {
        global $post;
    
        // bar
        if ($this->cage->post->keyExists('box')) { 
                if ($this->cage->post->getAlpha('box') == 'defaultbox') { 
                    $box = 'default'; 
                } else { 
                    $box = 'custom'; 
                }
        }
        
        $this->pluginHook('sidebar_posts_save_settings');
        
        $this->updateSetting('sidebar_posts_box', $box);
        
        $this->hotaru->message = $this->lang["sidebar_posts_settings_saved"];
        $this->hotaru->messageType = "green";
        $this->hotaru->showMessage();
        
        return true;    
    }
}
?>
