<?php

class FacebookShareSettings
{
    /**
    * Admin settings for the Facebook Share plugin
    */
    public function settings($h)
    {
        // include language file
        $h->includeLanguage();
	    
	    // show header
		echo "<h1>" . $h->lang["facebook_share_settings_header"] . "</h1>\n";
		
		if ($h->cage->post->getAlpha('submitted') == 'true') { 
			$this->saveSettings($h); 
		}
		
		// Get settings from database if they exist...
        $facebook_share_settings = $h->getSerializedSettings();

        // set choices to blank
        $tinyurl = "";
        $isgd = "";
        $bitly = "";

        // determine which URL shortener is selected
        switch($facebook_share_settings['fb_shortener']) {
            case 'tinyurl':
                $tinyurl = "checked";
                break;
            case 'bitly':
                $bitly = "checked";
                break;
            default:
                $isgd = "checked";
        }      
     
		// set link type choices to blank
        $icon_link = "";
        $button = "";

        // determine which is selected
        switch($facebook_share_settings['icon_or_button']) {
            case 'icon_link':
                $icon_link = 'checked="checked"';
                break;
            case 'button':
                $button = 'checked="checked"';
                break;
            default:
                $icon_link = 'checked="checked"';
				$button = "";
        }
		
		switch($facebook_share_settings['button_counter']) {
            case 'true':
                $checked = 'checked="checked"';
                break;
            case 'false':
                $checked = "";
                break;
            default:
                $checked = "";
        }

		// start form
        echo "<form name='facebook_share_settings_form' ";
        echo "action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=facebook_share' method='post'>\n";

        // instructions
        echo "<p>" . $h->lang['facebook_share_settings_shortener'] . "</p>\n";
        
        // radio buttons
        
        // is.gd
        echo "<p><input type='radio' name='fb_shortener' value='isgd' " . $isgd . " >";
        echo "&nbsp;&nbsp;" . $h->lang["facebook_share_settings_isgd"] . "</p>\n"; 
        
        // tinyurl
        echo "<p><input type='radio' name='fb_shortener' value='tinyurl' " . $tinyurl . " >";
        echo "&nbsp;&nbsp;" . $h->lang["facebook_share_settings_tinyurl"] . "</p>\n"; 
        
        // bit.ly
        echo "<p><input type='radio' name='fb_shortener' value='bitly' " . $bitly . " >";
        echo "&nbsp;&nbsp;" . $h->lang["facebook_share_settings_bitly"] . "</p>\n"; 
        
        // bitly login
        echo "<p>" . $h->lang['facebook_share_settings_bitly_login'];
        echo ": <input type='text' size=30 name='fb_bitly_login' value='" . $facebook_share_settings['fb_bitly_login'] . "' /></p>\n";
        
        // bit.ly api key
        echo "<p>" . $h->lang['facebook_share_settings_bitly_api_key'];
        echo ": <input type='text' size=30 name='fb_bitly_api_key' value='" . $facebook_share_settings['fb_bitly_api_key'] . "' /></p>\n";
        
        // use Google Analytics tracking tags?
        echo "<p>" . $h->lang['facebook_share_settings_use_GA_tracking'];
		echo ": <input type='checkbox' name='test' " . $facebook_share_settings['fb_use_GA_tracking'] . ">";
		echo " <a href='http://www.google.com/support/googleanalytics/bin/answer.py?hl=en&answer=55518' target='_blank' >" . $h->lang['facebook_share_settings_what_is_GA_tracking'] . "</a>";
                
		// instructions
        echo "<p>" . $h->lang['facebook_share_settings_notice'] . "</p>";
		
        // radio buttons
        echo "<ul id='facebook_share_container'>";
		echo "<li><input type='radio' name='icon_or_button' id='facebook_share_icon_link' value='icon_link' " . $icon_link . ">";
        echo "&nbsp;&nbsp;" . $h->lang["facebook_share_settings_icon_link"] . "</li>\n"; 

        echo "<li><input type='radio' name='icon_or_button' id='facebook_share_button' value='button' " . $button . ">";
        echo "&nbsp;&nbsp;" . $h->lang["facebook_share_settings_button"] . "\n"; 
		
		// Use counter?
		echo "<ul><li>&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' id='button_counter' name='button_counter' $checked>";
        echo "&nbsp;&nbsp;" . $h->lang["facebook_share_settings_button_counter"] . "</li>\n";
		echo "</li></ul>";

        // end form
        echo "<br />";
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $h->lang["facebook_share_form_save"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form>\n";
    }

     /**
     * Save Facebook Share settings
     */
    public function saveSettings($h)
    {
        // Include language file
        $h->includeLanguage();
        
		$facebook_share_settings = $h->getSerializedSettings(); 
		
		$facebook_share_settings['icon_or_button'] = $h->cage->post->testAlnumLines('icon_or_button');
		
		$button_counter = 'false';
		if ($h->cage->post->keyExists('button_counter')) { 
            $button_counter = 'true'; 
        } 
        
		$facebook_share_settings['button_counter'] = $button_counter;
		$facebook_share_settings['icon_or_button'] = $h->cage->post->testAlnumLines('icon_or_button');
		
        // get result of radio buttons and bitly fields
        $facebook_share_settings['fb_shortener'] = $h->cage->post->testAlpha('fb_shortener');
        $facebook_share_settings['fb_bitly_login'] = $h->cage->post->testAlnumLines('fb_bitly_login');
        $facebook_share_settings['fb_bitly_api_key'] = $h->cage->post->testAlnumLines('fb_bitly_api_key');

		if ($h->cage->post->keyExists('test')) {
	        $facebook_share_settings['fb_use_GA_tracking'] = 'checked'; } 
	    else { 
		    $facebook_share_settings['fb_use_GA_tracking'] = ''; 
		}		
		
        // if bitly is chosen but either of the login or api key fields are empty, set error, don't save
        if ($facebook_share_settings['fb_shortener'] == 'bitly' &&
            (!$facebook_share_settings['fb_bitly_login'] || !$facebook_share_settings['fb_bitly_api_key']))
        {
            // error message
            $h->message = $h->lang["facebook_share_settings_error"];
            $h->messageType = "red";
        } 
        else 
        {
            // update settings and set message		
			$h->updateSetting('facebook_share_settings', serialize($facebook_share_settings));
			$h->message = $h->lang["facebook_share_settings_saved"];
			$h->messageType = "green";
	}	

        // show message
        $h->showMessage();

        return true;
    }
}