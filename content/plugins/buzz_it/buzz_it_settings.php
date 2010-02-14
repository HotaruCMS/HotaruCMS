<?php
/**
 * Buzz It Settings
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
 
class BuzzItSettings
{
     /**
     * Admin settings for the Buzz It plugin
     */
    public function settings($h)
    {
        // include language file
        $h->includeLanguage();
        
        // show header
        echo "<h1>" . $h->lang["buzz_it_settings_header"] . "</h1>\n";

        // If the form has been submitted, go and save the data...
        if ($h->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings($h); 
        }

        // Get settings from database if they exist...
        $buzz_it_settings = $h->getSerializedSettings();

        // start form
        echo "<form name='buzz_it_settings_form' ";
        echo "action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=buzz_it' method='post'>\n";
        
        // use Google Analytics tracking tags?
        echo "<p>" . $h->lang['buzz_it_settings_use_GA_tracking'];
		echo ": <input type='checkbox' name='test' " . $buzz_it_settings['bi_use_GA_tracking'] . ">";
		echo " <a href='http://www.google.com/support/googleanalytics/bin/answer.py?hl=en&answer=55518' target='_blank' >" . $h->lang['buzz_it_settings_what_is_GA_tracking'] . "</a>";
        
        // end form
        echo "<br /><br />";
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $h->lang["main_form_save"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form>\n";
    }
    
    
    /**
     * Save Buzz It Settings
     */
    public function saveSettings($h)
    {
        // Include language file
        $h->includeLanguage();

        // Get settings from database if they exist...
        $buzz_it_settings = $h->getSerializedSettings();
        
		if ($h->cage->post->keyExists('test')) {
	        $buzz_it_settings['bi_use_GA_tracking'] = 'checked'; } 
	    else { 
		    $buzz_it_settings['bi_use_GA_tracking'] = ''; 
		}
	    	    
        // update settings and set message
        $h->updateSetting('buzz_it_settings', serialize($buzz_it_settings));
        $h->message = $h->lang["main_settings_saved"];
        $h->messageType = "green";
        
        // show message
        $h->showMessage();
        
        return true;
    }
}
?>
