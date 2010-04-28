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
     * Admin settings for the Journal plugin
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
        
        $need_sb_post = $journal_settings['need_sb_post'];
        $items_per_page = $journal_settings['items_per_page'];
        $rss_items = $journal_settings['rss_items'];
        $content_length = $journal_settings['content_length'];
        $summary = $journal_settings['summary'];
        $summary_length = $journal_settings['summary_length'];
        $allowable_tags = $journal_settings['allowable_tags'];
    
        $h->pluginHook('journal_settings_get_values');
        
        //...otherwise set to blank:
        if (!$content_length) { $content_length = ''; }
        if (!$summary) { $summary = ''; }
        if (!$summary_length) { $summary_length = ''; }
        
        echo "<form name='journal_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=journal' method='post'>\n";

        echo "<p><input type='checkbox' name='need_sb_post' value='need_sb_post' " . $need_sb_post . ">&nbsp;&nbsp;" . $h->lang["journal_settings_need_sb_post"] . "</p>\n";
        
        echo "<p><input type='text' size=5 name='items_per_page' value='" . $items_per_page . "' /> " . $h->lang["journal_settings_items_per_page"] . "</p>\n";
        
        echo "<p><input type='text' size=5 name='rss_items' value='" . $rss_items . "' /> " . $h->lang["journal_settings_rss_items"] . "</p>\n";
        
        echo "<p><input type='text' size=5 name='content_length' value='" . $content_length . "' /> " . $h->lang["journal_settings_content_min_length"] . "</p>\n";
        
        echo "<p><input type='checkbox' name='summary' value='summary' " . $summary . ">&nbsp;&nbsp;" . $h->lang["journal_settings_summary"];
        echo "&nbsp;&nbsp;&nbsp;&nbsp;";
        echo $h->lang["journal_settings_summary_max_length"] . ": <input type='text' size=5 name='summary_length' value='" . $summary_length . "' />\n";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;";
        
        echo "<small>" . $h->lang["journal_settings_summary_instruct"] . "</small></p>\n";
    
        $h->pluginHook('journal_settings_form');
        
        echo "<p>" . $h->lang["journal_settings_allowable_tags"] . " <input type='text' size=40 name='allowable_tags' value='" . $allowable_tags . "' /><br />";
        echo $h->lang["journal_settings_allowable_tags_example"] . "</p>\n";
        
        $h->pluginHook('journal_settings_form2');
    
        echo "<br />\n";
        
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $h->lang["main_form_save"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form>\n";
    }
    
    
    /**
     * Save Journal Settings
     */
    public function saveSettings($h) 
    {
        // Get current settings 
        $journal_settings = $h->getSerializedSettings();
        
        // Need social bookmarking post before being able to start a journal?
        if ($h->cage->post->keyExists('need_sb_post')) { 
            $need_sb_post = 'checked'; 
        } else { 
            $need_sb_post = ''; 
        }
        
        // Items per page
        $items_per_page = $h->cage->post->getInt('items_per_page'); 
        if (!$items_per_page) { 
            $items_per_page = $journal_settings['items_per_page'];
        } 
        
        // Feed items
        $rss_items = $h->cage->post->getInt('rss_items'); 
        if (!$rss_items) { 
            $rss_items = $journal_settings['rss_items'];
        } 
        
        // Content length
        $content_length = $h->cage->post->getInt('content_length'); 
        if (!$content_length) { 
            $content_length = $journal_settings['content_length'];
        } 
        
        // Summary
        if ($h->cage->post->keyExists('summary')) { 
            $summary = 'checked'; 
        } else { 
            $summary = ''; 
        }
        
        // Summary length
        $summary_length = $h->cage->post->getInt('summary_length'); 
        if (!$summary_length) { 
            $summary_length = $journal_settings['summary_length'];
        } 
        
        // Allowable tags
        $allowable_tags = $h->cage->post->getRaw('allowable_tags'); 
        if (!$allowable_tags) { 
            $allowable_tags = $journal_settings['allowable_tags'];
        } 
        
        $h->pluginHook('journal_save_settings');
        
        $journal_settings['need_sb_post'] = $need_sb_post;
        $journal_settings['items_per_page'] = $items_per_page;
        $journal_settings['rss_items'] = $rss_items;
        $journal_settings['content_length'] = $content_length;
        $journal_settings['summary'] = $summary;
        $journal_settings['summary_length'] = $summary_length;
        $journal_settings['allowable_tags'] = $allowable_tags;
    
        $h->updateSetting('journal_settings', serialize($journal_settings));
        
        $h->message = $h->lang["main_settings_saved"];
        $h->messageType = "green";
        
        return true;    
    }
    
}
?>
