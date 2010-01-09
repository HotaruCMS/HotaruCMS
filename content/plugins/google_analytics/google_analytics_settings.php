<?php
/**
 * File: /plugins/google_analytics/google_analytics_settings.php
 * Purpose: Admin settings for the Google Analytics plugin
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
 * @author    Carlo Armanni <carlo.armanni@libero.it>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.tr3ndy.com/
 */
 
class GoogleAnalyticsSettings
{
	/**
     * Admin settings for google_analytics
     */
	public function settings($h)
	{
		
		// If the form has been submitted, go and save the data...
		if ($h->cage->post->getAlpha('submitted') == 'true') { 
			$this->saveSettings($h); 
		}    
		
		echo "<h1>" . $h->lang["google_analytics_settings_header"] . "</h1>\n";
		
		$h->showMessage(); // Saved / Error message
		
		// Get settings from the database if they exist...
		$google_analytics_key = $h->getSetting('google_analytics_key');

		//...otherwise set to blank:
		if (!$google_analytics_key) { $google_analytics_key = ''; } 
		
		// A plugin hook so other plugin developers can add settings
		$h->pluginHook('google_analytics_settings_get_values');

		// The form should be submitted to the admin_index.php page:
		echo "<form name='google_analytics_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=google_analytics' method='post'>\n";
        
        echo "<p>" . $h->lang["google_analytics_settings_instructions"] . "</p><br />";
       
        echo "<p>" . $h->lang["google_analytics_settings_key"] . " <input type='text' size=25 name='google_analytics_key' value='" . $google_analytics_key . "'></p>\n";    

		// A plugin hook so other plugin developers can show settings		
		$h->pluginHook('google_analytics_settings_form');
                
        echo "<br /><br />\n";    
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $h->lang["main_form_save"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form>\n";
	
}  

public function saveSettings($h)
{
        $error = 0;
        
        // Google Analytics key
        if ($h->cage->post->keyExists('google_analytics_key')) { 
            $google_analytics_key = $h->cage->post->testAlnumLines('google_analytics_key');
            $error = 0;
        } else {
            $google_analytics_key = ''; 
            $error = 1;
            $h->message = $h->lang["google_analytics_settings_no_key"];
            $h->messageType = "red";
        }
       
        $h->pluginHook('google_analytics_save_settings');
        
        if ($error == 0) {
            // save settings
            $h->updateSetting('google_analytics_key', $google_analytics_key);

            $h->message = $h->lang["main_settings_saved"];
            $h->messageType = "green";
        }
        
    return true;    
}  

}
