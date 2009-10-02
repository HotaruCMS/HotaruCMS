<?php
/**
 * Users Settings
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
 
class UsersSettings extends Users
{
     /**
     * Admin settings for the Users plugin
     */
    public function settings()
    {
        // If the form has been submitted, go and save the data...
        if ($this->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings(); 
        }    
        
        echo "<h1>" . $this->lang["users_settings_header"] . "</h1>\n";
        
        // Get settings from database if they exist...
        $recaptcha_enabled = $this->getSetting('users_recaptcha_enabled');
        $recaptcha_pubkey = $this->getSetting('users_recaptcha_pubkey');
        $recaptcha_privkey = $this->getSetting('users_recaptcha_privkey');
        $emailconf_enabled = $this->getSetting('users_emailconf_enabled');
    
    
        $this->pluginHook('users_settings_get_values');
        
        //...otherwise set to blank:
        if (!$recaptcha_enabled) { $recaptcha_enabled = ''; }
        if (!$recaptcha_pubkey) { $recaptcha_pubkey = ''; }
        if (!$recaptcha_privkey) { $recaptcha_privkey = ''; }
        if (!$emailconf_enabled) { $emailconf_enabled = ''; }
        
        echo "<form name='users_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=users' method='post'>\n";
        
        echo "<p>" . $this->lang["users_settings_instructions"] . "</p><br />";
        
        echo "<b>" . $this->lang["users_settings_registration"] . "</b><br /><br />";
        
        $thisdomain =  rstrtrim(str_replace("http://", "", BASEURL), '/');
        echo "<input type='checkbox' name='rc_enabled' value='enabled' " . $recaptcha_enabled . " >&nbsp;&nbsp;" . $this->lang["users_settings_recaptcha_enable"] . " <a href='http://recaptcha.net/api/getkey?domain=" . $thisdomain . "&app=HotaruCMS'>reCAPTCHA.net</a><br /><br />\n";    
        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $this->lang["users_settings_recaptcha_public_key"] . ": <input type='text' name='rc_pubkey' size=50 value='" . $recaptcha_pubkey . "'><br /><br />\n";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $this->lang["users_settings_recaptcha_private_key"] . ": <input type='text' name='rc_privkey' size=50 value='" . $recaptcha_privkey . "'><br /><br />\n";
        echo "<input type='checkbox' name='emailconf' value='emailconf' " . $emailconf_enabled . ">&nbsp;&nbsp;" . $this->lang["users_settings_email_conf"] . "<br />\n";
    
        $this->pluginHook('users_settings_form');
                
        echo "<br /><br />\n";    
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $this->lang["users_settings_save"] . "' />\n";
        echo "</form>\n";
    }
    
    
    /**
     * Save Users Settings
     */
    public function saveSettings()
    {
        global $userbase;
    
        // Recaptcha Enabled
        if ($this->cage->post->keyExists('rc_enabled')) { 
            $recaptcha_enabled = 'checked'; 
            $userbase->vars['useRecaptcha'] = true;
        } else { 
            $recaptcha_enabled = ''; 
            $userbase->vars['useRecaptcha'] = false;
        }
        
        // Email Confirmation Enabled
        if ($this->cage->post->keyExists('emailconf')) { 
            $emailconf_enabled = 'checked'; 
            $userbase->vars['useEmailConf'] = true;
        } else { 
            $emailconf_enabled = ''; 
            $userbase->vars['useEmailConf'] = false;
        }
        
        // ReCaptcha Public Key
        if ($this->cage->post->keyExists('rc_pubkey')) { 
            $recaptcha_pubkey = $this->cage->post->testAlnumLines('rc_pubkey');
        } else { 
            $recaptcha_pubkey = "";
        }
        
        // ReCaptcha Private Key
        if ($this->cage->post->keyExists('rc_privkey')) {     
            $recaptcha_privkey = $this->cage->post->testAlnumLines('rc_privkey');
        } else { 
            $recaptcha_privkey = ""; 
        }
        
        
        $this->pluginHook('users_save_settings');
        
        $this->updateSetting('users_recaptcha_enabled', $recaptcha_enabled);    
        $this->updateSetting('users_recaptcha_pubkey', $recaptcha_pubkey);    
        $this->updateSetting('users_recaptcha_privkey', $recaptcha_privkey);
        $this->updateSetting('users_emailconf_enabled', $emailconf_enabled);        
        
        if (($recaptcha_enabled == 'checked') && ($recaptcha_pubkey == "" || $recaptcha_privkey == "")) {
            $this->hotaru->message = $this->lang["users_settings_no_keys"];
            $this->hotaru->messageType = "red";
            $this->hotaru->showMessage();
        } else {
            $this->hotaru->message = $this->lang["users_settings_saved"];
            $this->hotaru->messageType = "green";
            $this->hotaru->showMessage();
        }
        
        return true;    
    }
}
?>
