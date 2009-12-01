<?php
/**
 * File: /plugins/categories/categories_settings.php
 * Purpose: Admin settings for the Categories plugin
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

class CategoriesSettings extends Categories
{
     /**
     * Admin settings for Categories
     */
    public function settings()
    {
        // If the form has been submitted, go and save the data...
        if ($this->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings(); 
        }
        
        echo "<h1>" . $this->lang["categories_settings_header"] . "</h1>\n";
          
        // Get settings from database if they exist...
        $bar = $this->getSetting('categories_bar');
    
        $this->pluginHook('categories_settings_get_values');
        
        //...otherwise set to blank:
        if (!$bar) { $menubar = 'checked'; }
        
        // which is checked?
        if ($bar == 'menu') { $menubar = 'checked'; $sidebar = ''; }
        if ($bar == 'side') { $menubar = ''; $sidebar = 'checked'; }
        
        echo "<form name='categories_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=categories' method='post'>\n";
        
        echo "<p>" . $this->lang["categories_settings_instructions"] . "</p><br />";
        
        echo "<p><input type='radio' name='bar' value='menubar' " . $menubar . " >&nbsp;&nbsp;" . $this->lang["categories_settings_menubar"] . "</p>\n";    
        echo "<p><input type='radio' name='bar' value='sidebar' " . $sidebar . " >&nbsp;&nbsp;" . $this->lang["categories_settings_sidebar"] . "</p>\n"; 
        
        echo "<p>" . $this->lang["categories_settings_note"] . "</p><br />";
    
        $this->pluginHook('categories_settings_form');
                
        echo "<br /><br />\n";    
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $this->lang["categories_settings_save"] . "' />\n";
        echo "</form>\n";
    }
    
    
     /**
     * Save admin settings for Categories
     *
     * @return true
     */
    public function saveSettings()
    {
        global $post;
    
        // bar
        if ($this->cage->post->keyExists('bar')) { 
                if ($this->cage->post->getAlpha('bar') == 'menubar') { 
                    $bar = 'menu'; 
                } else { 
                    $bar = 'side'; 
                }
        }
        
        $this->pluginHook('categories_save_settings');
        
        $this->updateSetting('categories_bar', $bar);
        
        $this->hotaru->message = $this->lang["categories_settings_saved"];
        $this->hotaru->messageType = "green";
        $this->hotaru->showMessage();
        
        return true;    
    }
}
?>
