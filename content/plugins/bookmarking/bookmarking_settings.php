<?php
/**
 *  Bookmarking Settings
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

class BookmarkingSettings
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
        
        echo "<h1>" . $h->lang["bookmarking_settings_header"] . "</h1>\n";
        
        $h->showMessage(); // Saved / Error message
        
        // Get settings from database if they exist...
        $bookmarking_settings = $h->getSerializedSettings();
        
        $posts_per_page = $bookmarking_settings['posts_per_page'];
        $rss_redirect = $bookmarking_settings['rss_redirect'];
        $archive = $bookmarking_settings['archive'];
    
        $h->pluginHook('bookmarking_settings_get_values');
        
        //...otherwise set to blank:
        if (!$posts_per_page) { $posts_per_page = 10; }
        if (!$rss_redirect) { $rss_redirect = ''; }
        if (!$archive) { $archive = 'no_archive'; }
        
        echo "<form name='bookmarking_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=bookmarking' method='post'>\n";

        // posts per page
        echo "<p><input type='text' size=5 name='posts_per_page' value='" . $posts_per_page . "' /> ";
        echo $h->lang["bookmarking_settings_posts_per_page"] . "</p>\n";

        // rss redirecting?
        echo "<p><input type='checkbox' name='rss_redirect' value='rss_redirect' " . $rss_redirect . " >&nbsp;&nbsp;" . $h->lang["bookmarking_settings_rss_redirect"] . "<br />\n"; 
    
        $h->pluginHook('bookmarking_settings_form');
    
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
        $bookmarking_settings = $h->getSerializedSettings();
        
        // Posts per page
        $posts_per_page = $h->cage->post->testInt('posts_per_page'); 
        if (!$posts_per_page) { 
            $posts_per_page = $bookmarking_settings['posts_per_page']; 
        }
        
    
        // RSS Redirecting
        if ($h->cage->post->keyExists('rss_redirect')) { 
            $rss_redirect = 'checked'; 
        } else { 
            $rss_redirect = ''; 
        }
    
        $h->pluginHook('bookmarking_save_settings');
        
        $bookmarking_settings['posts_per_page'] = $posts_per_page;
        $bookmarking_settings['rss_redirect'] = $rss_redirect;
    
        $h->updateSetting('bookmarking_settings', serialize($bookmarking_settings));
        
        $h->message = $h->lang["main_settings_saved"];
        $h->messageType = "green";
        
        return true;    
    }
    
}
?>
