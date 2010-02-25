<?php
/**
 * File: /plugins/tos_antispam/tos_antispam_settings.php
 * Purpose: Admin settings for the TOS AntiSpam plugin
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
 
class TosAntispamSettings
{
     /**
     * Admin settings for akismet
     */
    public function settings($h)
    {
        // If the form has been submitted, go and save the data...
        if ($h->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings($h); 
        }
        
        echo "<h1>" . $h->lang["tos_antispam_settings_header"] . "</h1>\n";
          
        // Get settings from database if they exist...
        $tos_antispam_settings = $h->getSerializedSettings();
        $registration = $tos_antispam_settings['registration'];
        $post_submission = $tos_antispam_settings['post_submission'];
        $question = $tos_antispam_settings['question'];
        $choices = $tos_antispam_settings['choices'];
        $answer = $tos_antispam_settings['answer'];
        $first_x_posts = $tos_antispam_settings['first_x_posts'];
    
        $h->pluginHook('tos_antispam_settings_get_values');
        
        //...otherwise set to blank:
        if (!isset($registration)) { $registration = 'checked'; }
        if (!isset($post_submission)) { $post_submission = ''; }
        if (!isset($first_x_posts)) { $first_x_posts = 1; }
            
        echo "<form name='tos_antispam_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=tos_antispam' method='post'>\n";
            
        echo "<p><input type='checkbox' name='register' value='tos_antispam_register' " . $registration . " >&nbsp;&nbsp;";
        echo $h->lang["tos_antispam_settings_registration"] . "</p>\n"; 
        echo "<p><input type='checkbox' name='post_submission' value='tos_antispam_post_submission' " . $post_submission . " >&nbsp;&nbsp;";
        echo $h->lang["tos_antispam_settings_post_submission"] . "</p>\n"; 

        echo "<br />\n";
        echo "<p><input type='text' size='5' name='first_x_posts' value='" . $first_x_posts . "'>&nbsp;&nbsp;" . $h->lang["tos_antispam_settings_first_x_posts"] . "</p>\n";
        
        echo "<br />\n";
        echo "<p>" . $h->lang["tos_antispam_settings_question"] . " <input type='text' size='60' name='question' value='" . sanitize($question, 'ents') . "'></p>\n";
        echo "<p>" . $h->lang["tos_antispam_settings_choices"] . " <input type='text' size='60' name='choices' value='" . $this->show_list($choices) . "'></p>\n";
        echo "<p>" . $h->lang["tos_antispam_settings_answer"] . " <input type='text' size='20' name='answer' value='" . sanitize($answer, 'ents') . "'></p>\n";
    
        $h->pluginHook('tos_antispam_settings_form');
                
        echo "<br /><br />\n";    
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $h->lang["main_form_save"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form>\n";
    }
    
    
     /**
     * Save admin settings for akismet
     *
     * @return true
     */
    public function saveSettings($h)
    {
        $error = 0;
            
        // use TOS AntiSpam on registration
        if ($h->cage->post->keyExists('register')) { 
            $registration = "checked";
        } else {
            $registration = "";
        }
        
        // use TOS AntiSpam on post submission
        if ($h->cage->post->keyExists('post_submission')) { 
            $post_submission = "checked";
        } else {
            $post_submission = "";
        }
        
        // Anti-spam question
        if ($h->cage->post->keyExists('question')) { 
            $question = sanitize($h->cage->post->getHtmLawed('question'), 'tags', '<b><i><u>');
            $error = 0;
        } else {
            $question = ''; 
            $error = 1;
            $h->message = $h->lang["tos_antispam_settings_no_question"];
            $h->messageType = "red";
        }
        
        // Anti-spam choices
        if ($h->cage->post->keyExists('choices')) { 
            $answer_string = $h->cage->post->sanitizeTags('choices');
            $choices = explode(',', $answer_string);
            shuffle($choices);
            $choices = array_map('trim', $choices);
            foreach ($choices as $choice) {
                $new_choices[make_url_friendly($choice)] = $choice;
            }
            if (isset($new_choices)) { $choices = $new_choices; }
            $error = 0;
        } else {
            $choices = array(); 
            $error = 1;
            $h->message = $h->lang["tos_antispam_settings_no_choices"];
            $h->messageType = "red";
        }
        
        // Anti-spam correct answer
        $answer = trim($h->cage->post->sanitizeTags('answer'));
        if ($answer && isset($choices[make_url_friendly($answer)])) {
            $error = 0;
        } else {
            $answer = ''; 
            $error = 1;
            $h->message = $h->lang["tos_antispam_settings_no_answer"];
            $h->messageType = "red";
        }
        
        // first_x_posts
        $first_x_posts = trim($h->cage->post->testInt('first_x_posts'));
        if (!$first_x_posts) {
            $first_x_posts = 1; 
        }
        
        
        $h->pluginHook('tos_antispam_save_settings');
        
        if ($error == 0) {
            // save settings
            $tos_antispam_settings['registration'] = $registration;
            $tos_antispam_settings['post_submission'] = $post_submission;
            $tos_antispam_settings['question'] = $question;
            $tos_antispam_settings['choices'] = $choices;
            $tos_antispam_settings['answer'] = $answer;
            $tos_antispam_settings['first_x_posts'] = $first_x_posts;
            $h->updateSetting('tos_antispam_settings', serialize($tos_antispam_settings));
            
            $h->message = $h->lang["main_settings_saved"];
            $h->messageType = "green";
        }
        $h->showMessage();
        
        return true;    
    }
    
    /**
     * HTML for pre-filling the choices
     *
     * @param array $choices
     * @return string $output
     */
    public function show_list($choices = array()) 
    {
        $output = '';
        foreach ($choices as $key => $value) {
            if ($value) {
                $output .= sanitize($value, 'ents') . ", ";
            }
        }
        $output = rstrtrim($output, ", ");
        
        return $output;
    }
}
?>