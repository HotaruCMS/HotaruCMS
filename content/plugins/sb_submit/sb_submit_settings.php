<?php
/**
 *  File: /plugins/submit/submit_settings.php
 * Admin settings for the Submit plugin
 *  Notes: This file is part of the Submit plugin. The main file is /plugins/submit/submit.php
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

class SbSubmitSettings
{
     /**
     * Admin settings for the Submit plugin
     */
    public function settings($hotaru)
    {
        // If the form has been submitted, go and save the data...
        if ($hotaru->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings($hotaru); 
        }    
        
        echo "<h1>" . $hotaru->lang["submit_settings_header"] . "</h1>\n";
        
        $hotaru->showMessage(); // Saved / Error message
        
        // Get settings from database if they exist...
        $submit_settings = $hotaru->getSerializedSettings();
        
        //$enabled = $submit_settings['enabled'];
        $content_length = $submit_settings['content_length'];
        $summary = $submit_settings['summary'];
        $summary_length = $submit_settings['summary_length'];
        //$posts_per_page = $submit_settings['posts_per_page'];
        $allowable_tags = $submit_settings['allowable_tags'];
        $set_pending = $submit_settings['set_pending'];
        $x_posts = $submit_settings['x_posts'];
        $email_notify = $submit_settings['email_notify'];
        $email_mods = $submit_settings['email_notify_mods'];
        //$archive = $submit_settings['archive'];
        $url_limit = $submit_settings['url_limit'];
        $daily_limit = $submit_settings['daily_limit'];
        $freq_limit = $submit_settings['freq_limit'];
    
        $hotaru->pluginHook('submit_settings_get_values');
        
        //...otherwise set to blank:
        if (!$content_length) { $content_length = ''; }
        if (!$summary) { $summary = ''; }
        if (!$summary_length) { $summary_length = ''; }
        if (!$set_pending) { $set_pending = 'auto_approve'; }
        if (!$x_posts) { $x_posts = 1; }
        //if (!$archive) { $archive = 'no_archive'; }
        if (!$url_limit) { $url_limit = 0; }
        if (!$daily_limit) { $daily_limit = 0; }
        if (!$freq_limit) { $freq_limit = 0; }
        
        echo "<form name='submit_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=sb_submit' method='post'>\n";

        //echo "<input type='checkbox' name='enabled' value='enabled' " . $enabled . " >&nbsp;&nbsp;" . $this->lang["submit_settings_enable"] . "<br /><br />\n"; 
        
        echo $hotaru->lang["submit_settings_post_components"] . "<br /><br />\n";
           
        echo $hotaru->lang["submit_settings_content_min_length"] . ": <input type='text' size=5 name='content_length' value='" . $content_length . "' /><br /><br />\n";
        echo "<input type='checkbox' name='summary' value='summary' " . $summary . ">&nbsp;&nbsp;" . $hotaru->lang["submit_settings_summary"];
        echo "&nbsp;&nbsp;&nbsp;&nbsp;";
        echo $hotaru->lang["submit_settings_summary_max_length"] . ": <input type='text' size=5 name='summary_length' value='" . $summary_length . "' />\n";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;";
        echo $hotaru->lang["submit_settings_summary_instruct"] . "<br />\n";
    
        $hotaru->pluginHook('submit_settings_form');
        
        /*
        echo "<br /><input type='text' size=5 name='posts_per_page' value='" . $posts_per_page . "' /> ";
        echo $hotaru->lang["submit_settings_posts_per_page"] . "<br /><br />\n";
        */
        
        echo $hotaru->lang["submit_settings_allowable_tags"] . " <input type='text' size=40 name='allowable_tags' value='" . $allowable_tags . "' /><br />";
        echo $hotaru->lang["submit_settings_allowable_tags_example"] . "<br />\n";
        
        $hotaru->pluginHook('submit_settings_form2');
    
        echo "<br />\n";
        
        echo "<b>Submission Settings</b> (for users with 'member' roles)<br /><br />";
        
        echo "<p>" . " <input type='text' size=5 name='url_limit' value='" . $url_limit . "' /> " . $hotaru->lang["submit_settings_url_limit"] . "</p>";
        echo "<p>" . " <input type='text' size=5 name='daily_limit' value='" . $daily_limit . "' /> " . $hotaru->lang["submit_settings_daily_limit"] . "</p>";
        echo "<p>" . " <input type='text' size=5 name='freq_limit' value='" . $freq_limit . "' /> " . $hotaru->lang["submit_settings_frequency_limit"] . "</p>";
        
        echo "<p>" . $hotaru->lang["submit_settings_limit_note"] . "</p>";
        
        switch ($set_pending) {
            case 'some_pending':
                $auto_approve = ''; $some_pending = 'checked'; $all_pending = '';
                break;
            case 'all_pending':
                $auto_approve = ''; $some_pending = ''; $all_pending = 'checked';
                break;
            default:
                $auto_approve = 'checked'; $some_pending = ''; $all_pending = '';
        }
        
        echo "<br />";
        echo "<input type='radio' name='set_pending' value='auto_approve' " . $auto_approve . " >&nbsp;&nbsp;" . $hotaru->lang["submit_settings_auto_approve"] . "<br />\n"; 
        echo "<input type='radio' name='set_pending' value='some_pending' " . $some_pending . " >&nbsp;&nbsp;" . $hotaru->lang["submit_settings_some_pending_1"] . "\n"; 
        echo "<select name='first_x_posts'>\n";
            echo "<option>" . $x_posts . "</option>\n";
            echo '<option disabled>-----</option>';
            echo "<option>1</option>\n";
            echo "<option>2</option>\n";
            echo "<option>3</option>\n";
            echo "<option>4</option>\n";
            echo "<option>5</option>\n";
            echo "<option>10</option>\n";
            echo "<option>20</option>\n";
        echo "</select>\n";
        echo $hotaru->lang["submit_settings_some_pending_2"] . "<br />\n"; 
        echo "<input type='radio' name='set_pending' value='all_pending' " . $all_pending . " >&nbsp;&nbsp;" . $hotaru->lang["submit_settings_all_pending"] . "\n"; 
                
        echo "<br /><br />\n";

        // email notify options
      
        echo "<input type='checkbox' name='email_notify' value='email_notify' id='email_notify' " . $email_notify . ">&nbsp;&nbsp;" ;
        echo $hotaru->lang["submit_settings_email_notify"] . "<br /><br />\n";
    
        $admins = $hotaru->getMods('can_edit_posts', 'yes');
        if (!$email_notify) { $show_admins = 'display: none;'; }
        echo "<div id='email_notify_options' style='margin-left: 2.0em; " . $show_admins . "'>"; 
        
        if ($admins) {
            echo "<table>\n";
            foreach ($admins as $ad) {
                if (array_key_exists($ad['id'], $email_mods)) { 
                    switch ($email_mods[$ad['id']]['type']) {
                        case 'all':
                            $checked_all = 'checked'; $checked_pend = ''; $checked_none = '';
                            break;
                        case 'pending':
                            $checked_all = ''; $checked_pend = 'checked'; $checked_none = '';
                            break;
                        default:
                            $checked_all = ''; $checked_pend = ''; $checked_none = 'checked';
                    }
                }
                else
                {
                    $checked_all = ''; $checked_pend = ''; $checked_none = 'checked';
                }
                
                echo "<tr>\n";
                echo "<td><b>" . ucfirst($ad['name']) . "</b></td>\n";
                
                echo "<td><input type='radio' name='emailmod[" . $ad['id'] . "][" . $ad['email'] . "]' value='all' " . $checked_all . ">";
                echo " " . $hotaru->lang["submit_settings_email_notify_all"] . "</td>\n";
                
                echo "<td><input type='radio' name='emailmod[" . $ad['id'] . "][" . $ad['email'] . "]' value='pending' " . $checked_pend . ">";
                echo " " . $hotaru->lang["submit_settings_email_notify_pending"] . "</td>\n";
                
                echo "<td><input type='radio' name='emailmod[" . $ad['id'] . "][" . $ad['email'] . "]' value='none' " . $checked_none . ">";
                echo " " . $hotaru->lang["submit_settings_email_notify_none"] . "</td>\n";
                echo "</tr>\n";
            }
            echo "</table><br />\n";
        }
        echo "</div>";
        
        /*
        echo $hotaru->lang["submit_settings_post_archiving"] . "<br /><br />\n";
        echo $hotaru->lang["submit_settings_post_archive_desc"] . "<br /><br />\n";
        echo "<select name='post_archive'>\n";
            echo "<option value='" . $archive . "'>" . $hotaru->lang["submit_settings_post_archive_$archive"] . "</option>\n";
            echo '<option disabled>-----</option>';
            echo "<option value='no_archive'>" . $hotaru->lang["submit_settings_post_archive_no_archive"] . "</option>\n";
            echo "<option value='180'>" . $hotaru->lang["submit_settings_post_archive_180"] . "</option>\n";
            echo "<option value='365'>" . $hotaru->lang["submit_settings_post_archive_365"] . "</option>\n";
            echo "<option value='730'>" . $hotaru->lang["submit_settings_post_archive_730"] . "</option>\n";
            echo "<option value='1095'>" . $hotaru->lang["submit_settings_post_archive_1095"] . "</option>\n";
        echo "</select>\n";
        echo $hotaru->lang["submit_settings_post_archive"] . "<br /><br />\n";
        */
                
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $hotaru->lang["submit_settings_save"] . "' />\n";
        echo "<input type='hidden' name='token' value='" . $hotaru->token . "' />\n";
        echo "</form>\n";
    }
    
    
    /**
     * Save Submit Settings
     */
    public function saveSettings($hotaru) 
    {
        // Enabled
        /*
        if ($hotaru->cage->post->keyExists('enabled')) { 
            $enabled = 'checked'; 
        } else { 
            $enabled = ''; 
        }
        */
        
        // Content length
        if ($hotaru->cage->post->keyExists('content_length')) { 
            $content_length = $hotaru->cage->post->getInt('content_length'); 
            if (empty($content_length)) { $content_length = $hotaru->post->contentLength; }
        } else { 
            $content_length = $hotaru->post->contentLength; 
        } 
        
        // Summary
        if ($hotaru->cage->post->keyExists('summary')) { 
            $summary = 'checked'; 
        } else { 
            $summary = ''; 
        }
        
        // Summary length
        if ($hotaru->cage->post->keyExists('summary_length')) { 
            $summary_length = $hotaru->cage->post->getInt('summary_length'); 
            if (empty($summary_length)) { $summary_length = $hotaru->post->summaryLength; }
        } else { 
            $summary_length = $hotaru->post->summaryLength; 
        } 
        
        // Posts per page
        /*
        if ($hotaru->cage->post->keyExists('posts_per_page')) { 
            $posts_per_page = $hotaru->cage->post->testInt('posts_per_page'); 
            if (empty($posts_per_page) || $posts_per_page == 0) { $posts_per_page = $hotaru->post->postsPerPage; }
        } else { 
            $posts_per_page = $hotaru->post->postsPerPage; 
        } 
        */
        
        // Allowable tags
        if ($hotaru->cage->post->keyExists('allowable_tags')) { 
            $allowable_tags = $hotaru->cage->post->getRaw('allowable_tags'); 
            if (empty($allowable_tags)) { $allowable_tags = $hotaru->post->allowableTags; }
        } else { 
            $allowable_tags = $hotaru->post->allowableTags; 
        }
        
        // Url limit
        if ($hotaru->cage->post->keyExists('url_limit')) { 
            $url_limit = $hotaru->cage->post->testInt('url_limit'); 
            if (!is_numeric($url_limit)) { $url_limit = 0; }
        } else { 
            $url_limit = 0; 
        }
        
        // Daily limit
        if ($hotaru->cage->post->keyExists('daily_limit')) { 
            $daily_limit = $hotaru->cage->post->testInt('daily_limit'); 
            if (!is_numeric($daily_limit)) { $daily_limit = 0; }
        } else { 
            $daily_limit = 0; 
        }
        
        // Frequency limit
        if ($hotaru->cage->post->keyExists('freq_limit')) { 
            $freq_limit = $hotaru->cage->post->testInt('freq_limit'); 
            if (!is_numeric($freq_limit)) { $freq_limit = 0; }
        } else { 
            $freq_limit = 0; 
        }
        
        // Set pending
        if ($hotaru->cage->post->keyExists('set_pending')) { 
            $set_pending = $hotaru->cage->post->testAlnumLines('set_pending');
        } else {
            $set_pending = 'auto_approve';
        }
        
        // First X posts
        if ($hotaru->cage->post->keyExists('first_x_posts')) { 
            $x_posts = $hotaru->cage->post->testInt('first_x_posts');
        } else {
            $x_posts = 1; //default
        }
        
        // Send email notification about new posts
        if ($hotaru->cage->post->keyExists('email_notify')) { 
            $email_notify = 'checked'; 
        } else { 
            $email_notify = ''; 
        }
        
        // admins to receive above email notification
        if ($hotaru->cage->post->keyExists('emailmod')) 
        {
            $email_mods = array();
            foreach ($hotaru->cage->post->keyExists('emailmod') as $id => $array) {
                $email_mods[$id]['id'] = $id;
                $email_mods[$id]['email'] = key($array);
                $email_mods[$id]['type'] = $array[$email_mods[$id]['email']];
            }
        } else {
            $email_mods = array();
        }
        
        // Post Archiving
        /*
        if ($hotaru->cage->post->keyExists('post_archive')) { 
            $archive = $hotaru->cage->post->testAlnumLines('post_archive'); 
        } 
        */
        
        $hotaru->pluginHook('submit_save_settings');
        
        //$submit_settings['enabled'] = $enabled;
        $submit_settings['content_length'] = $content_length;
        $submit_settings['summary'] = $summary;
        $submit_settings['summary_length'] = $summary_length;
        //$submit_settings['posts_per_page'] = $posts_per_page;
        $submit_settings['allowable_tags'] = $allowable_tags;
        $submit_settings['url_limit'] = $url_limit;
        $submit_settings['daily_limit'] = $daily_limit;
        $submit_settings['freq_limit'] = $freq_limit;
        $submit_settings['set_pending'] = $set_pending;
        $submit_settings['x_posts'] = $x_posts;
        $submit_settings['email_notify'] = $email_notify;
        $submit_settings['email_notify_mods'] = $email_mods; //array
        //$submit_settings['archive'] = $archive;
    
        $hotaru->updateSetting('sb_submit_settings', serialize($submit_settings));
        
        $hotaru->message = $hotaru->lang["submit_settings_saved"];
        $hotaru->messageType = "green";
        
        return true;    
    }
    
}
?>
