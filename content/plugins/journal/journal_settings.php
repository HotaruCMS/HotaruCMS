<?php
/**
 *  Journal Settings
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

class JournalSettings
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
        
        echo "<h1>" . $h->lang["journal_settings_header"] . "</h1>\n";
        
        $h->showMessage(); // Saved / Error message
        
        // Get settings from database if they exist...
        $journal_settings = $h->getSerializedSettings();
        
        $items_per_page = $journal_settings['items_per_page'];
        $allowable_tags_posts = $journal_settings['allowable_tags_posts'];
        $allowable_tags_replies = $journal_settings['allowable_tags_replies'];
    
        $h->pluginHook('journal_settings_get_values');
        
        //...otherwise set to blank:
        if (!$items_per_page) { $items_per_page = 10; }
        
        echo "<form name='journal_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=journal' method='post'>\n";

        // items per page
        echo "<p><input type='text' size=5 name='items_per_page' value='" . $items_per_page . "' /> ";
        echo $h->lang["journal_settings_items_per_page"] . "</p><br />\n";
        
        // allowable tags
        
        echo "<p><b>" . $h->lang["journal_settings_allowable_tags"] . "</b> " . $h->lang["journal_settings_allowable_tags_example"] . "</p>\n";
        echo "<p>" . $h->lang["journal_settings_allowable_tags_posts"] . " <input type='text' size=40 name='allowabletags_posts' value='" . $allowable_tags_posts . "' /><br />";
        echo "<p>" . $h->lang["journal_settings_allowable_tags_replies"] . " <input type='text' size=40 name='allowabletags_replies' value='" . $allowable_tags_replies . "' /><br />";
        
    
        $h->pluginHook('journal_settings_form');
    
        echo "<br />\n";

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
        $journal_settings = $h->getSerializedSettings();
        
        // Items per page
        $items_per_page = $h->cage->post->testInt('items_per_page'); 
        if (!$items_per_page) { 
            $items_per_page = $journal_settings['items_per_page']; 
        }
        
        // Allowable tags
        $allowable_tags = $h->cage->post->getRaw('allowabletags'); 
        if (!$allowable_tags) { 
            $allowable_tags = $journal_settings['allowableTags']; 
        }
    
        $h->pluginHook('journal_save_settings');
        
        $journal_settings['items_per_page'] = $items_per_page;
        $journal_settings['allowable_tags'] = $allowable_tags;
    
        $h->updateSetting('journal_settings', serialize($journal_settings));
        
        $h->message = $h->lang["main_settings_saved"];
        $h->messageType = "green";
        
        return true;
    }
    
}
?>
