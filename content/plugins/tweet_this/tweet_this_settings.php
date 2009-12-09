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
 
class TweetThisSettings extends TweetThis
{
     /**
     * Admin settings for the Tweet This plugin
     */
    public function settings()
    {
        // include language file
        $this->includeLanguage();
        
        // show header
        echo "<h1>" . $this->lang["tweet_this_settings_header"] . "</h1>\n";
        
        // If the form has been submitted, go and save the data...
        if ($this->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings(); 
        }
        
        // Get settings from database if they exist...
        $tweet_this_settings = $this->getSerializedSettings();

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
        echo "<p>" . $this->lang['tweet_this_settings_shortener'] . "</p>\n";
        
        // radio buttons
        
        // is.gd
        echo "<p><input type='radio' name='tt_shortener' value='isgd' " . $isgd . " >";
        echo "&nbsp;&nbsp;" . $this->lang["tweet_this_settings_isgd"] . "</p>\n"; 
        
        // tinyurl
        echo "<p><input type='radio' name='tt_shortener' value='tinyurl' " . $tinyurl . " >";
        echo "&nbsp;&nbsp;" . $this->lang["tweet_this_settings_tinyurl"] . "</p>\n"; 
        
        // bit.ly
        echo "<p><input type='radio' name='tt_shortener' value='bitly' " . $bitly . " >";
        echo "&nbsp;&nbsp;" . $this->lang["tweet_this_settings_bitly"] . "</p>\n"; 
        
        // input fields
        
        // bitly login
        echo "<p>" . $this->lang['tweet_this_settings_bitly_login'];
        echo ": <input type='text' size=30 name='tt_bitly_login' value='" . $tweet_this_settings['tt_bitly_login'] . "' /></p>\n";
        
        // bit.ly api key
        echo "<p>" . $this->lang['tweet_this_settings_bitly_api_key'];
        echo ": <input type='text' size=30 name='tt_bitly_api_key' value='" . $tweet_this_settings['tt_bitly_api_key'] . "' /></p>\n";
        
        // end form
        echo "<br />";
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $this->lang["tweet_this_settings_save"] . "' />\n";
        echo "<input type='hidden' name='token' value='" . $this->hotaru->token . "' />\n";
        echo "</form>\n";
    }
    
    
    /**
     * Save Tweet This Settings
     */
    public function saveSettings()
    {
        // Include language file
        $this->includeLanguage();
                
        // Get settings from database if they exist...
        $tweet_this_settings = $this->getSerializedSettings();
        
        // get result of radio buttons and bitly fields
        $tweet_this_settings['tt_shortener'] = $this->cage->post->testAlpha('tt_shortener');
        $tweet_this_settings['tt_bitly_login'] = $this->cage->post->testAlnumLines('tt_bitly_login');
        $tweet_this_settings['tt_bitly_api_key'] = $this->cage->post->testAlnumLines('tt_bitly_api_key');
        
        // if bitly is chosen but either of the login or api key fields are empty, set error, don't save
        if ($tweet_this_settings['tt_shortener'] == 'bitly' &&
            (!$tweet_this_settings['tt_bitly_login'] || !$tweet_this_settings['tt_bitly_api_key']))
        {
            // error message
            $this->hotaru->message = $this->lang["tweet_this_settings_error"];
            $this->hotaru->messageType = "red";
        } 
        else 
        {
            // update settings and set message
            $this->updateSetting('tweet_this_settings', serialize($tweet_this_settings));
            $this->hotaru->message = $this->lang["submit_settings_saved"];
            $this->hotaru->messageType = "green";
        }
        
        // show message
        $this->hotaru->showMessage();
        
        return true;
    }
}
?>