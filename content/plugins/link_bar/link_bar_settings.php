<?php
/**
 * Link Bar Settings
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
 
class LinkBarSettings
{
     /**
     * Admin settings for Link Bar
     */
    public function settings($h)
    {
        // If the form has been submitted, go and save the data...
        if ($h->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings($h); 
        }
        
        echo "<h1>" . $h->lang["link_bar_settings_header"] . "</h1>\n";
        echo "<p>" . $h->lang["link_bar_settings_note"] . "</p>";
          
        // Get settings from database if they exist...
        $h->vars['link_bar_settings'] = $h->getSerializedSettings('link_bar');
            
        echo "<form name='link_bar_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=link_bar' method='post'>\n";
            
        echo "<p><input type='checkbox' name='logged_out' value='logged_out' " . $h->vars['link_bar_settings']['show_logged_out'] . " >&nbsp;&nbsp;" . $h->lang["link_bar_settings_show_logged_out"] . "<br />\n";
        echo "<input type='checkbox' name='logged_in' value='logged_in' " . $h->vars['link_bar_settings']['show_logged_in'] . " >&nbsp;&nbsp;" . $h->lang["link_bar_settings_show_logged_in"] . "</p>\n";
        
        $h->pluginHook('link_bar_settings_form');
                
        echo "<br /><br />\n";
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $h->lang["main_form_save"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form>\n";
    }
    
    
     /**
     * Save admin settings for link bar
     *
     * @return true
     */
    public function saveSettings($h)
    {
        // get settings again
        $h->vars['link_bar_settings'] = $h->getSerializedSettings('link_bar');
        $h->vars['link_bar_error'] = false;
        
        // show to logged in users?
        if ($h->cage->post->keyExists('logged_in')) {
        	$h->vars['link_bar_settings']['show_logged_in'] = 'checked';
        } else {
        	$h->vars['link_bar_settings']['show_logged_in'] = '';
        }
        
        // show to logged out users?
        if ($h->cage->post->keyExists('logged_out')) {
        	$h->vars['link_bar_settings']['show_logged_out'] = 'checked';
        } else {
        	$h->vars['link_bar_settings']['show_logged_out'] = '';
        }
        
        $h->pluginHook('link_bar_save_settings');
        
        // All settings get updated regardless of errors, so plugins should 
        // revert to previous values if there's an error.
        $h->updateSetting('link_bar_settings', serialize($h->vars['link_bar_settings']));
            
        if (!$h->vars['link_bar_error'])
        { 
            $h->message = $h->lang["main_settings_saved"];
            $h->messageType = "green";
            $h->showMessage();
        } else {
            // plugins should define their own error messages of the format:
            // $h->messages[$h->lang["plugin_name_setting_error"]] = "red";
            $h->showMessages();
        }
        
        return true;
    }
    
}
?>
