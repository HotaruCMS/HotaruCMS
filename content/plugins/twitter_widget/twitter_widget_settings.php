<?php
/**
 * File: /plugins/twitter_widget/twitter_widget_settings.php
 * Purpose: Admin settings for the Twitter Widget plugin
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
 
 
class TwitterWidgetSettings
{
	/**
     * Admin settings for twitter_widget
     */
	public function settings($h)
	{
		
		// If the form has been submitted, go and save the data...
		if ($h->cage->post->getAlpha('submitted') == 'true') { 
			$this->saveSettings($h); 
		}    
		
		echo "<h1>" . $h->lang["twitter_widget_settings_header"] . "</h1>\n";
		
		$h->showMessage(); // Saved / Error message
		
		// Get settings from the database if they exist...
		$twitter_widget_username = $h->getSetting('twitter_widget_username');
		$twitter_widget_password = $h->getSetting('twitter_widget_password');

		//...otherwise set to blank:
		if (!$twitter_widget_username) { $twitter_widget_username = ''; }
		if (!$twitter_widget_password) { $twitter_widget_password = ''; }

		// A plugin hook so other plugin developers can add settings
		$h->pluginHook('twitter_widget_settings_get_values');

		// The form should be submitted to the admin_index.php page:
		echo "<form name='twitter_widget_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=twitter_widget' method='post'>\n";
        
        echo "<p>" . $h->lang["twitter_widget_settings_instructions"] . "</p><br />";
       
        echo "<p>" . $h->lang["twitter_widget_settings_username"] . " <input type='text' size=25 name='twitter_widget_username' value='" . $twitter_widget_username . "'></p>\n";    
        echo "<p>" . $h->lang["twitter_widget_settings_password"] . " <input type='password' size=25 name='twitter_widget_password' value='" . $twitter_widget_password . "'></p>\n";    

		// A plugin hook so other plugin developers can show settings		
		$h->pluginHook('twitter_widget_settings_form');
                
        echo "<br /><br />\n";    
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $h->lang["main_form_save"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form>\n";
	
}  

public function saveSettings($h)
{
        $error = 0;
        
        // twitter_widget check name
        if ($h->cage->post->keyExists('twitter_widget_username')) { 
            $twitter_widget_username = $h->cage->post->testAlnumLines('twitter_widget_username');
            $error = 0;
        } else {
            $twitter_widget_username = ''; 
            $error = 1;
            $h->message = $h->lang["twitter_widget_settings_no_username"];
            $h->messageType = "red";
        }
       
	   // twitter_widget check password
	   if ($h->cage->post->keyExists('twitter_widget_password')) { 
            $twitter_widget_password = $h->cage->post->testAlnumLines('twitter_widget_password');
            $error = 0;
        } else {
            $twitter_widget_password = ''; 
            $error = 1;
            $h->message = $h->lang["twitter_widget_settings_no_password"];
            $h->messageType = "red";
        }

	   
        $h->pluginHook('twitter_widget_save_settings');
        
        if ($error == 0) {
            // save settings
            $h->updateSetting('twitter_widget_username', $twitter_widget_username);
            $h->updateSetting('twitter_widget_password', $twitter_widget_password);
            $h->message = $h->lang["twitter_widget_main_settings_saved"];
            $h->messageType = "green";
        }
        $h->showMessage();
		
    return true;    
}  

}
?>