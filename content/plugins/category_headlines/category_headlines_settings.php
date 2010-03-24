<?php
/**
 * Settings page for the Category Headlines Plugin
 */
class CategoryHeadlinesSettings
{
     /**
     * Admin settings for the Category Headlines Plugin
     */
    public function settings($h)
    {
	echo "<h1>" . $h->lang["category_headlines_settings_header"] . "</h1>\n";

	// If the form has been submitted, go and save the data...
        if ($h->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings($h); 
        }
		
	// Get settings from database if they exist...
        $category_headlines_settings = $h->getSerializedSettings();
		
	// set choices to blank
        $new = "";
        $latest = "";

        // determine which is selected
        switch($category_headlines_settings['type']) {
            case 'new':
                $new = "checked";
                break;
            default:
                $latest = "checked";
        }		
        
	// start form
        echo "<form name='category_headlines_settings_form' ";
        echo "action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=category_headlines' method='post'>\n";
        // instructions
        echo "<p>" . $h->lang['category_headlines_settings_default'] . "</p>";
        // input fields
        // categories to be shown in widget
        echo "<p>" . $h->lang['category_headlines_settings_cats'];
        echo "<br /><input type='text' size=4 name='cats' value='" . $category_headlines_settings['cats'] . "' /></p>";
        // limit
        echo "<p>" . $h->lang['category_headlines_settings_limit'];
        echo "<br /><input type='text' size=4 name='limit' value='" . $category_headlines_settings['limit'] . "' /></p>";
        // radio buttons for type
        echo "<p><label><input type='radio' name='type' value='new' " . $new . " >";
        echo "&nbsp;&nbsp;" . $h->lang["category_headlines_settings_new"] . "</label></p>\n";

	echo "<p><input type='radio' name='type' value='latest' " . $latest . " >";
        echo "&nbsp;&nbsp;" . $h->lang["category_headlines_settings_latest"] . "</p>\n";
                        
       // end form
        echo "<br />";
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $h->lang["main_form_save"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form>\n";
    }

     /**
     * Save Post Images settings
     */
    public function saveSettings($h)
    {
	// Get settings from database if they exist...
        $category_headlines_settings = $h->getSerializedSettings();
        $category_headlines_settings['cats'] = $h->cage->post->getAlnum('cats');
        $category_headlines_settings['limit'] = $h->cage->post->getInt('limit');
        $category_headlines_settings['type'] = $h->cage->post->getAlnum('type');
	
        // update settings and set message
        $h->updateSetting('category_headlines_settings', serialize($category_headlines_settings));
        $h->message = $h->lang["main_settings_saved"];
        $h->messageType = "green";
                
        // show message
        $h->showMessage();
        
        return true;
    }
}
?>