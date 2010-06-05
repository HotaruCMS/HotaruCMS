<?php
/**
 * ReCaptcha Settings
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
 
class ReCaptchaSettings
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
        
        echo "<h1>" . $h->lang["recaptcha_settings_header"] . "</h1>\n";
        
        // Get settings from database if they exist...
        $recaptcha_settings = $h->getSerializedSettings();
        $pubkey = $recaptcha_settings['pubkey'];
        $privkey = $recaptcha_settings['privkey'];
        
        //...otherwise set to blank:
        if (!$pubkey) { $pubkey = ''; }
        if (!$privkey) { $privkey = ''; }
        
        echo "<form name='recaptcha_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=recaptcha' method='post'>\n";
        
        $thisdomain =  rstrtrim(str_replace("http://", "", BASEURL), '/');
        echo "<p>" . $h->lang["recaptcha_settings_desc"] . " <a href='http://recaptcha.net/api/getkey?domain=" . $thisdomain . "&app=HotaruCMS'>reCAPTCHA.net</a>.</p><br />\n";
        
        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $h->lang["recaptcha_settings_public_key"] . ": <input type='text' name='rc_pubkey' size=50 value='" . $pubkey . "'><br /><br />\n";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $h->lang["recaptcha_settings_private_key"] . ": <input type='text' name='rc_privkey' size=50 value='" . $privkey . "'><br /><br />\n";

        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $h->lang["main_form_save"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form><br />\n";
        
        /* *************************************
         * RECAPTCHA TEST START
         * ********************************** */
         
        echo $h->lang["recaptcha_settings_example"] . "<br /><br />";
        
        if ($h->cage->post->getAlpha('submitted') == 'test')
        {
            $result = $h->pluginHook('check_recaptcha'); // This hook checks the captcha
            
            if ($result['ReCaptcha_check_recaptcha'] == 'success')
            {
                $h->showMessage($h->lang["recaptcha_success"], 'green');    // success message
            } 
            elseif ($result['ReCaptcha_check_recaptcha'] == 'empty')
            {
                $h->showMessage($h->lang["recaptcha_empty"], 'red');    // empty message
            } 
            else 
            {
                $h->showMessage($h->lang["recaptcha_error"], 'red');    // error message
            }
            echo "<br />";
        }

        echo "<form name='recaptcha_settings_test' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=recaptcha' method='post'>\n";
        $h->pluginHook('show_recaptcha');
        echo "<input type='hidden' name='submitted' value='test' />\n";
        echo "<input type='submit' value='" . $h->lang["recaptcha_settings_do_test"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form><br />\n";
        
        /* *************************************
         * RECAPTCHA TEST END
         * ********************************** */
    }
    
    
    /**
     * Save Settings
     */
    public function saveSettings($h)
    {
        // ReCaptcha Public Key
        $pubkey = $h->cage->post->testAlnumLines('rc_pubkey');
        if (!$pubkey) {
            $pubkey = "";
        }
        
        // ReCaptcha Private Key
        $privkey = $h->cage->post->testAlnumLines('rc_privkey');
        if (!$privkey) {
            $privkey = ""; 
        }
        
        $recaptcha_settings['pubkey'] = $pubkey;
        $recaptcha_settings['privkey'] = $privkey;
        
        $h->updateSetting('recaptcha_settings', serialize($recaptcha_settings));
        
        if (!$pubkey || !$privkey) {
            $h->message = $h->lang["recaptcha_settings_error"];
            $h->messageType = "red";
            $h->showMessage();
        } else {
            $h->message = $h->lang["main_settings_saved"];
            $h->messageType = "green";
            $h->showMessage();
        }
        
        return true;    
    }
}
?>
