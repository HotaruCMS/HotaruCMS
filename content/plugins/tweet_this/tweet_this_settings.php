<?php
/**
 * Tweet This Settings
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
 
class TweetThisSettings
{
     /**
     * Admin settings for the Tweet This plugin
     */
    public function settings($hotaru)
    {
        // include language file
        $hotaru->includeLanguage();
        
        // show header
        echo "<h1>" . $hotaru->lang["tweet_this_settings_header"] . "</h1>\n";
        
        // If the form has been submitted, go and save the data...
        if ($hotaru->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings($hotaru); 
        }
        
        // Get settings from database if they exist...
        $tweet_this_settings = $hotaru->getSerializedSettings();

        // set choices to blank
        $tinyurl = "";
        $isgd = "";
        $bitly = "";

        // determine which is selected
        switch($tweet_this_settings['tt_shortener']) {
            case 'tinyurl':
                $tinyurl = "checked";
                break;
            case 'bitly':
                $bitly = "checked";
                break;
            default:
                $isgd = "checked";
        }
        
        // start form
        echo "<form name='tweet_this_settings_form' ";
        echo "action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=tweet_this' method='post'>\n";
        
        // instructions
        echo "<p>" . $hotaru->lang['tweet_this_settings_shortener'] . "</p>\n";
        
        // radio buttons
        
        // is.gd
        echo "<p><input type='radio' name='tt_shortener' value='isgd' " . $isgd . " >";
        echo "&nbsp;&nbsp;" . $hotaru->lang["tweet_this_settings_isgd"] . "</p>\n"; 
        
        // tinyurl
        echo "<p><input type='radio' name='tt_shortener' value='tinyurl' " . $tinyurl . " >";
        echo "&nbsp;&nbsp;" . $hotaru->lang["tweet_this_settings_tinyurl"] . "</p>\n"; 
        
        // bit.ly
        echo "<p><input type='radio' name='tt_shortener' value='bitly' " . $bitly . " >";
        echo "&nbsp;&nbsp;" . $hotaru->lang["tweet_this_settings_bitly"] . "</p>\n"; 
        
        // input fields
        
        // bitly login
        echo "<p>" . $hotaru->lang['tweet_this_settings_bitly_login'];
        echo ": <input type='text' size=30 name='tt_bitly_login' value='" . $tweet_this_settings['tt_bitly_login'] . "' /></p>\n";
        
        // bit.ly api key
        echo "<p>" . $hotaru->lang['tweet_this_settings_bitly_api_key'];
        echo ": <input type='text' size=30 name='tt_bitly_api_key' value='" . $tweet_this_settings['tt_bitly_api_key'] . "' /></p>\n";
        
        // end form
        echo "<br />";
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $hotaru->lang["main_form_save"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $hotaru->csrfToken . "' />\n";
        echo "</form>\n";
    }
    
    
    /**
     * Save Tweet This Settings
     */
    public function saveSettings($hotaru)
    {
        // Include language file
        $hotaru->includeLanguage();
                
        // Get settings from database if they exist...
        $tweet_this_settings = $hotaru->getSerializedSettings();
        
        // get result of radio buttons and bitly fields
        $tweet_this_settings['tt_shortener'] = $hotaru->cage->post->testAlpha('tt_shortener');
        $tweet_this_settings['tt_bitly_login'] = $hotaru->cage->post->testAlnumLines('tt_bitly_login');
        $tweet_this_settings['tt_bitly_api_key'] = $hotaru->cage->post->testAlnumLines('tt_bitly_api_key');
        
        // if bitly is chosen but either of the login or api key fields are empty, set error, don't save
        if ($tweet_this_settings['tt_shortener'] == 'bitly' &&
            (!$tweet_this_settings['tt_bitly_login'] || !$tweet_this_settings['tt_bitly_api_key']))
        {
            // error message
            $hotaru->message = $hotaru->lang["tweet_this_settings_error"];
            $hotaru->messageType = "red";
        } 
        else 
        {
            // update settings and set message
            $hotaru->updateSetting('tweet_this_settings', serialize($tweet_this_settings));
            $hotaru->message = $hotaru->lang["main_settings_saved"];
            $hotaru->messageType = "green";
        }
        
        // show message
        $hotaru->showMessage();
        
        return true;
    }
}
?>