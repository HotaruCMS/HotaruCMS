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
    public function settings($h)
    {
        // include language file
        $h->includeLanguage();
        
        // show header
        echo "<h1>" . $h->lang["tweet_this_settings_header"] . "</h1>\n";
        
        // If the form has been submitted, go and save the data...
        if ($h->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings($h); 
        }
        
        // Get settings from database if they exist...
        $tweet_this_settings = $h->getSerializedSettings();

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
        echo "<p>" . $h->lang['tweet_this_settings_shortener'] . "</p>\n";
        
        // radio buttons
        
        // is.gd
        echo "<p><input type='radio' name='tt_shortener' value='isgd' " . $isgd . " >";
        echo "&nbsp;&nbsp;" . $h->lang["tweet_this_settings_isgd"] . "</p>\n"; 
        
        // tinyurl
        echo "<p><input type='radio' name='tt_shortener' value='tinyurl' " . $tinyurl . " >";
        echo "&nbsp;&nbsp;" . $h->lang["tweet_this_settings_tinyurl"] . "</p>\n"; 
        
        // bit.ly
        echo "<p><input type='radio' name='tt_shortener' value='bitly' " . $bitly . " >";
        echo "&nbsp;&nbsp;" . $h->lang["tweet_this_settings_bitly"] . "</p>\n"; 
        
        // input fields
        
        // bitly login
        echo "<p>" . $h->lang['tweet_this_settings_bitly_login'];
        echo ": <input type='text' size=30 name='tt_bitly_login' value='" . $tweet_this_settings['tt_bitly_login'] . "' /></p>\n";
        
        // bit.ly api key
        echo "<p>" . $h->lang['tweet_this_settings_bitly_api_key'];
        echo ": <input type='text' size=30 name='tt_bitly_api_key' value='" . $tweet_this_settings['tt_bitly_api_key'] . "' /></p>\n";
        
        // use Google Analytics tracking tags?
        echo "<p><input type='checkbox' name='test' " . $tweet_this_settings['tt_use_GA_tracking'] . ">&nbsp;&nbsp;" . $h->lang['tweet_this_settings_use_GA_tracking'];
        echo " <a href='http://www.google.com/support/googleanalytics/bin/answer.py?hl=en&answer=55518' target='_blank' >" . $h->lang['tweet_this_settings_what_is_GA_tracking'] . "</a></p>";
        
        // end form
        echo "<br />";
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $h->lang["main_form_save"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form>\n";
    }
    
    
    /**
     * Save Tweet This Settings
     */
    public function saveSettings($h)
    {
        // Include language file
        $h->includeLanguage();
                
        // Get settings from database if they exist...
        $tweet_this_settings = $h->getSerializedSettings();
        
        // get result of radio buttons and bitly fields
        $tweet_this_settings['tt_shortener'] = $h->cage->post->testAlpha('tt_shortener');
        $tweet_this_settings['tt_bitly_login'] = $h->cage->post->testAlnumLines('tt_bitly_login');
        $tweet_this_settings['tt_bitly_api_key'] = $h->cage->post->testAlnumLines('tt_bitly_api_key');
        
        // check whether to use GA tracking or not
        if ($h->cage->post->keyExists('test')) {
            $tweet_this_settings['tt_use_GA_tracking'] = 'checked'; } 
        else { 
            $tweet_this_settings['tt_use_GA_tracking'] = ''; 
        }
        
        // if bitly is chosen but either of the login or api key fields are empty, set error, don't save
        if ($tweet_this_settings['tt_shortener'] == 'bitly' &&
            (!$tweet_this_settings['tt_bitly_login'] || !$tweet_this_settings['tt_bitly_api_key']))
        {
            // error message
            $h->message = $h->lang["tweet_this_settings_error"];
            $h->messageType = "red";
        } 
        else 
        {
            // update settings and set message
            $h->updateSetting('tweet_this_settings', serialize($tweet_this_settings));
            $h->message = $h->lang["main_settings_saved"];
            $h->messageType = "green";
        }
        
        // show message
        $h->showMessage();
        
        return true;
    }
}
?>
