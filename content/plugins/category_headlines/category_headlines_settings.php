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
	// If the form has been submitted, go and save the data...
        if ($h->cage->post->getAlpha('submitted') == 'true') { 
            $data = $this->saveSettings($h);
            print $data;
            die;  // this is an ajax return call, so we don't want any html echoing to the screen
        }
		
	// Get settings from database if they exist...
        $category_headlines_settings = $h->getSerializedSettings();
		 print_r($category_headlines_settings['cats'])     ;

        $cats = parse_object_to_array($category_headlines_settings['cats']);
print_r($cats);
	// set choices to blank
        $new = "";
        $top = "";
        $yes = "";
        $no = "";
        $option1 = "";
        $option2 = "";
        $option3 = "";
        $option4 = "";


        // determine which is selected
        switch($category_headlines_settings['type']) {
            case 'new':
                $new = "checked";
                break;
            default:
                $top = "checked";
        }

        // determine which is selected
        switch($category_headlines_settings['image_icon']) {
            case 'true':
                $yes = "checked";
                break;
            default:
                $no = "checked";
        }

         // determine which is selected
        switch($category_headlines_settings['options']) {
            case '1':
                $option1 = "checked";
                break;
            case '2':
                $option2 = "checked";
                break;
            case '3':
                $option3 = "checked";
                break;
            default:
                $option4 = "checked";
        }

        $cats = $h->getCategories();
        echo "<h1>" . $h->lang["category_headlines_settings_header"] . "</h1>\n";
        
	// start form
        echo "<form id='category_headlines_settings_form' action='' method='post' accept-charset='utf-8'>";        
        // instructions
        echo "<p>" . $h->lang['category_headlines_settings_default'] . "</p>";
        // input fields
        // categories to be shown in widget
        echo "<p>" . $h->lang['category_headlines_settings_cats'];
        echo "<br /><select style='width: 250px; height: 120px;' multiple='multiple' name='show_cats[]'>";       
        
        foreach ($cats as $cat) {
            if ($cat->category_safe_name != "all") {
                     echo "<option value='" . $cat->category_id . "' ";
                     if (in_array($cat->category_id, $cats)) echo "SELECTED";
                     echo ">" . $cat->category_safe_name . "</option>";
            }
        }
        echo "</select>";
       
        // limit
        echo "<p>" . $h->lang['category_headlines_settings_limit'];
        echo "<br /><input type='text' size=4 name='limit' value='" . $category_headlines_settings['limit'] . "' /></p>";
        
        // radio buttons for type
        echo "<p>" . $h->lang['category_headlines_settings_post_type'];
        echo "<p><label><input type='radio' name='type' value='new' " . $new . " >";
        echo "&nbsp;&nbsp;" . $h->lang["category_headlines_settings_new"] . "</label></p>\n";
	echo "<p><input type='radio' name='type' value='top' " . $top . " >";
        echo "&nbsp;&nbsp;" . $h->lang["category_headlines_settings_top"] . "</p>\n";

        // radio buttons for image icon
        echo "<p>" . $h->lang['category_headlines_settings_post_icon'];
        echo "<p><label><input type='radio' name='image_icon' value='true' " . $yes . " >";
        echo "&nbsp;&nbsp;" . $h->lang["category_headlines_settings_yes"] . "</label></p>\n";
	echo "<p><input type='radio' name='image_icon' value='false' " . $no . " >";
        echo "&nbsp;&nbsp;" . $h->lang["category_headlines_settings_no"] . "</p>\n";

        // radio buttons for options
        echo "<p>" . $h->lang['category_headlines_settings_post_options'];
        echo "<p><label><input type='radio' name='options' value='1' " . $option1 . " >";
        echo "&nbsp;&nbsp;" . $h->lang["category_headlines_settings_option1"] . "</label></p>\n";
	echo "<p><label><input type='radio' name='options' value='2' " . $option2 . " >";
        echo "&nbsp;&nbsp;" . $h->lang["category_headlines_settings_option2"] . "</label></p>\n";
        echo "<p><label><input type='radio' name='options' value='3' " . $option3 . " >";
        echo "&nbsp;&nbsp;" . $h->lang["category_headlines_settings_option3"] . "</label></p>\n";
        echo "<p><label><input type='radio' name='options' value='4' " . $option4 . " >";
        echo "&nbsp;&nbsp;" . $h->lang["category_headlines_settings_option4"] . "</label></p>\n";
                        
       // end form
        echo "<br />";
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' id='submit_button'  value='" . $h->lang["main_form_save"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form>\n";
        echo "<br/><br/>";
        echo "<div id='error_message'></div>";
    }

     /**
     * Save Post Images settings
     */
    public function saveSettings($h)
    {
	// Get settings from database if they exist...
        $category_headlines_settings = $h->getSerializedSettings();
        $category_headlines_settings['cats'] = $h->cage->post->getHtmLawed('show_cats');
        $category_headlines_settings['limit'] = $h->cage->post->getInt('limit');
        $category_headlines_settings['type'] = $h->cage->post->getAlnum('type');
        $category_headlines_settings['image_icon'] = $h->cage->post->getAlnum('image_icon');
        $category_headlines_settings['options'] = parse_object_to_array($h->cage->post->getInt('options'));

        //print_r($category_headlines_settings['options']);
        
         // options
//        if($h->cage->post->keyExists('options') )
//        {
//           foreach($h->cage->post->keyExists('options') as $key => $value) {
//            $category_headlines_settings['options'][] = $value;
//         }
//        }
	
        // update settings and set message
        $h->updateSetting('category_headlines_settings', serialize($category_headlines_settings));
        $result = array('message'=>$h->lang["main_settings_saved"]);
                
        return json_encode($result);
    }

}
?>


