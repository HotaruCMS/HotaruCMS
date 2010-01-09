<?php
/**
 * RPX Settings
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
 
class RpxSettings
{
     /**
     * Admin settings for the Users plugin
     */
    public function settings($h)
    {
        require_once(PLUGINS . 'users/libs/UserFunctions.php');
        $uf = new UserFunctions($this->hotaru);
        
        // If the form has been submitted, go and save the data...
        if ($h->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings($h); 
        }    
        
        echo "<h1>" . $h->lang["rpx_settings_header"] . "</h1>\n";
        
        // Get settings from database if they exist...
        $rpx_settings = $h->getSerializedSettings();
        
        $rpx_application = $rpx_settings['application'];
        $rpx_api_key = $rpx_settings['api_key'];
        $rpx_language = $rpx_settings['language'];
        $rpx_account = $rpx_settings['account'];
        $rpx_display = $rpx_settings['display'];
    
        $h->pluginHook('rpx_settings_get_values');
        
        //...otherwise set to defaults:
        if (!$rpx_application) { $rpx_application = ''; }
        if (!$rpx_api_key) { $rpx_api_key = ''; }
        if (!$rpx_language) { $rpx_language = 'en'; }
        if (!$rpx_account) { $rpx_account = 'basic'; }
        if (!$rpx_display) { $rpx_display = 'embed'; }
        
        switch ($rpx_account) {
            case 'plus':
                $selected_basic = ""; $selected_plus = "selected"; $selected_pro = "";
                break;
            case 'pro':
                $selected_basic = ""; $selected_plus = ""; $selected_pro = "selected";
                break;
            default:
                $selected_basic = "selected"; $selected_plus = ""; $selected_pro = "";
        }
        switch ($rpx_display) {
            case 'replace':
                $selected_embed = ""; $selected_overlay = ""; $selected_replace = "selected";
                break;
            case 'overlay':
                $selected_embed = ""; $selected_overlay = "selected"; $selected_replace = "";
                break;
            default:
                $selected_embed = "selected"; $selected_overlay = ""; $selected_replace = "";
        }

        
        echo "<form name='rpx_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=rpx' method='post'>\n";
        
        echo "<p>" . $h->lang["rpx_settings_instructions"] . "</p><br />\n";

        echo "<p>" . $h->lang["rpx_settings_application"] . "</p>";
        echo "<p><input type='text' name='rpx_application' size=50 value='" . $rpx_application . "'></p>\n";
        echo "<p>" . $h->lang["rpx_settings_api_key"] . "</p>";
        echo "<p><input type='text' name='rpx_api_key' size=50 value='" . $rpx_api_key . "'></p>\n";
        echo "<p>" . $h->lang["rpx_settings_language"] . "</p>";
        echo "<p><input type='text' name='rpx_language' size=50 value='" . $rpx_language . "'>&nbsp;" . $h->lang["rpx_settings_language_default"] . "</p>\n";
        
        echo "<p>" . $h->lang["rpx_settings_account"] . "&nbsp;\n";
        echo "<select name='rpx_account'>\n";
        echo "<option value='basic' " . $selected_basic . ">Basic</option>\n";
        echo "<option value='plus' " . $selected_plus . ">Plus</option>\n";
        echo "<option value='pro' " . $selected_pro . ">Pro</option>\n";
        echo "</select>";
        echo "&nbsp;" . $h->lang["rpx_settings_account_desc"] . "</p>";
        
        echo "<p>" . $h->lang["rpx_settings_display_desc"] . "</p>";
        
        echo "<p>" . $h->lang["rpx_settings_display"] . "&nbsp;\n";
        echo "<select name='rpx_display'>\n";
        echo "<option value='embed' " . $selected_embed . ">Embed</option>\n";
        echo "<option value='overlay' " . $selected_overlay . ">Overlay</option>\n";
        echo "<option value='replace' " . $selected_replace . ">Replace</option>\n";
        echo "</select></p>";
                
        $h->pluginHook('rpx_settings_form');
                
        echo "<br /><br />\n";    
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $h->lang["main_form_save"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form>\n";
    }
    
    
    /**
     * Save Users Settings
     */
    public function saveSettings($h)
    {
        $error = array();
        
        $rpx_settings = $h->getSerializedSettings();
        
        $rpx_application = $h->cage->post->testAlnumLines('rpx_application');
        if ($rpx_application) { 
            $rpx_settings['application'] = $rpx_application;
        } else {
            array_push($error, "application");
        }
        
        $rpx_api_key = $h->cage->post->testAlnum('rpx_api_key');
        if ($rpx_api_key) { 
            $rpx_settings['api_key'] = $rpx_api_key;
        } else {
            array_push($error, "api_key");
        }
        
        $rpx_language = $h->cage->post->testAlpha('rpx_language');
        if ($rpx_language) { 
            $rpx_settings['language'] = $rpx_language;
        } else {
            array_push($error, "language");
        }
        
        $rpx_settings['account'] = $h->cage->post->testAlpha('rpx_account');
        
        $rpx_settings['display'] = $h->cage->post->testAlpha('rpx_display');
                
        $h->pluginHook('rpx_save_settings');
        
        $h->updateSetting('rpx_settings', serialize($rpx_settings));
        
        if ($error) {
            foreach ($error as $err) {
                $lang_var = "rpx_settings_error_" . $err;
                $h->messages[$h->lang[$lang_var]] = "red";
            }
        } else {
            $h->messages[$h->lang["main_settings_saved"]] = "green";
        }
        
        $h->showMessages();
        
        return true;    
    }
}
?>