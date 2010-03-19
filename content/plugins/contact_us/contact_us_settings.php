<?php
/**
 * Contact Us Settings
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
 
class ContactUsSettings
{
     /**
     * Admin settings for the Users plugin
     */
    public function settings($h)
    {
        // If the form has been submitted, go and save the data...
        if ($h->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings($h); 
        }    
        
        echo "<h1>" . $h->lang["contact_us_settings_header"] . "</h1>\n";
        
        // Get settings from database if they exist...
        $contact_us_settings = $h->getSerializedSettings();
        $recaptcha = $contact_us_settings['recaptcha'];
        
        //...otherwise set to blank:
        if (!$recaptcha) { $recaptcha = ''; }
        
        echo "<form name='contact_us_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=contact_us' method='post'>\n";
        
        echo "<input type='checkbox' name='recaptcha' value='recaptcha' " . $recaptcha . " >&nbsp;&nbsp;" . $h->lang["contact_us_settings_recaptcha"] . "<br /><br />\n";

        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $h->lang["main_form_save"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form><br />\n";
        
        echo "<p>" . $h->lang["contact_us_instruct"] . "<a href='" . $h->url(array('page'=>'contact')) . "'>" . $h->url(array('page'=>'contact')) . "</a></p>\n";

    }
    
    
    /**
     * Save Settings
     */
    public function saveSettings($h)
    {
        // get current settings
        $contact_us_settings = $h->getSerializedSettings();
        
        // Use reCaptcha?
        if ($h->cage->post->keyExists('recaptcha')) {
            $contact_us_settings['recaptcha'] = "checked";
        } else {
            $contact_us_settings['recaptcha'] = "";
        }
        
        $h->updateSetting('contact_us_settings', serialize($contact_us_settings));
        
        $h->message = $h->lang["main_settings_saved"];
        $h->messageType = "green";
        $h->showMessage();
        
        return true;    
    }
}
?>
