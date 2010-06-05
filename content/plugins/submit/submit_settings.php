<?php
/**
 *  Submit Settings
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

class SubmitSettings
{
     /**
     * Admin settings for the Submit plugin
     */
    public function settings($h)
    {
        // If the form has been submitted, go and save the data...
        if ($h->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings($h); 
        }    
        
        echo "<h1>" . $h->lang["submit_settings_header"] . "</h1>\n";
        
        $h->showMessage(); // Saved / Error message
        
        // Get settings from database if they exist...
        $submit_settings = $h->getSerializedSettings();
        
        $enabled = $submit_settings['enabled'];
        $content = $submit_settings['content'];
        $content_length = $submit_settings['content_length'];
        $summary = $submit_settings['summary'];
        $summary_length = $submit_settings['summary_length'];
        $allowable_tags = $submit_settings['allowable_tags'];
        $set_pending = $submit_settings['set_pending'];
        $x_posts = $submit_settings['x_posts'];
        $email_notify = $submit_settings['email_notify'];
        $email_mods = $submit_settings['email_notify_mods'];
        $url_limit = $submit_settings['url_limit'];
        $daily_limit = $submit_settings['daily_limit'];
        $freq_limit = $submit_settings['freq_limit'];
        $categories = $submit_settings['categories'];
        $tags = $submit_settings['tags'];
        $max_tags = $submit_settings['max_tags'];
    
        $h->pluginHook('submit_settings_get_values');
        
        //...otherwise set to blank:
        if (!$content) { $content = ''; }
        if (!$content_length) { $content_length = ''; }
        if (!$summary) { $summary = ''; }
        if (!$summary_length) { $summary_length = ''; }
        if (!$set_pending) { $set_pending = 'auto_approve'; }
        if (!$x_posts) { $x_posts = 1; }
        if (!$url_limit) { $url_limit = 0; }
        if (!$daily_limit) { $daily_limit = 0; }
        if (!$freq_limit) { $freq_limit = 0; }
        if (!$categories) { $categories = ''; }
        if (!$tags) { $tags = ''; }
        if (!$max_tags) { $max_tags = 100; }
        
        echo "<form name='submit_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=submit' method='post'>\n";

        echo "<p><input type='checkbox' name='enabled' value='enabled' " . $enabled . " >&nbsp;&nbsp;" . $h->lang["submit_settings_enable"] . "<br />\n"; 
        echo $h->lang["submit_settings_enable_instruct"] . "</p><br />";
        
        echo $h->lang["submit_settings_post_components"] . "<br /><br />\n";
           
        echo "<p><input type='checkbox' name='content' value='content' " . $content . ">&nbsp;&nbsp;" . $h->lang["submit_settings_content"];
        echo "&nbsp;&nbsp;&nbsp;&nbsp;";
        echo $h->lang["submit_settings_content_min_length"] . ": <input type='text' size=5 name='content_length' value='" . $content_length . "' /></p>\n";
        
        echo "<p><input type='checkbox' name='summary' value='summary' " . $summary . ">&nbsp;&nbsp;" . $h->lang["submit_settings_summary"];
        echo "&nbsp;&nbsp;&nbsp;&nbsp;";
        echo $h->lang["submit_settings_summary_max_length"] . ": <input type='text' size=5 name='summary_length' value='" . $summary_length . "' />\n";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;";
        
        echo "<small>" . $h->lang["submit_settings_summary_instruct"] . "</small></p>\n";
    
        $h->pluginHook('submit_settings_form');
        
        echo "<p>" . $h->lang["submit_settings_allowable_tags"] . " <input type='text' size=40 name='allowable_tags' value='" . $allowable_tags . "' /><br />";
        echo $h->lang["submit_settings_allowable_tags_example"] . "</p>\n";
        
        echo "<p><input type='checkbox' name='categories' value='categories' " . $categories . ">&nbsp;&nbsp;" . $h->lang["submit_settings_categories"] . "</p>";
        echo "<p><input type='checkbox' name='tags' value='tags' " . $tags . ">&nbsp;&nbsp;" . $h->lang["submit_settings_tags"];
        echo "&nbsp;&nbsp;&nbsp;&nbsp;";
        echo $h->lang["submit_settings_max_tags"] . ": <input type='text' size=5 name='max_tags' value='" . $max_tags . "' /></p>\n";
        
        $h->pluginHook('submit_settings_form2');
    
        echo "<br />\n";
        
        echo "<b>Submission Settings</b> (for users with 'member' roles)<br /><br />";
        
        echo "<p>" . " <input type='text' size=5 name='url_limit' value='" . $url_limit . "' /> " . $h->lang["submit_settings_url_limit"] . "</p>";
        echo "<p>" . " <input type='text' size=5 name='daily_limit' value='" . $daily_limit . "' /> " . $h->lang["submit_settings_daily_limit"] . "</p>";
        echo "<p>" . " <input type='text' size=5 name='freq_limit' value='" . $freq_limit . "' /> " . $h->lang["submit_settings_frequency_limit"] . "</p>";
        
        echo "<p>" . $h->lang["submit_settings_limit_note"] . "</p>";
        
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
        echo "<input type='radio' name='set_pending' value='auto_approve' " . $auto_approve . " >&nbsp;&nbsp;" . $h->lang["submit_settings_auto_approve"] . "<br />\n"; 
        echo "<input type='radio' name='set_pending' value='some_pending' " . $some_pending . " >&nbsp;&nbsp;" . $h->lang["submit_settings_some_pending_1"] . "\n"; 
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
        echo $h->lang["submit_settings_some_pending_2"] . "<br />\n"; 
        echo "<input type='radio' name='set_pending' value='all_pending' " . $all_pending . " >&nbsp;&nbsp;" . $h->lang["submit_settings_all_pending"] . "\n"; 
                
        echo "<br /><br />\n";

        // email notify options
      
        echo "<input type='checkbox' name='email_notify' value='email_notify' id='email_notify' " . $email_notify . ">&nbsp;&nbsp;" ;
        echo $h->lang["submit_settings_email_notify"] . "<br /><br />\n";
    
        $admins = $h->getMods('can_edit_posts', 'yes');
        $show_admins = '';
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
                echo " " . $h->lang["submit_settings_email_notify_all"] . "</td>\n";
                
                echo "<td><input type='radio' name='emailmod[" . $ad['id'] . "][" . $ad['email'] . "]' value='pending' " . $checked_pend . ">";
                echo " " . $h->lang["submit_settings_email_notify_pending"] . "</td>\n";
                
                echo "<td><input type='radio' name='emailmod[" . $ad['id'] . "][" . $ad['email'] . "]' value='none' " . $checked_none . ">";
                echo " " . $h->lang["submit_settings_email_notify_none"] . "</td>\n";
                echo "</tr>\n";
            }
            echo "</table><br />\n";
        }
        echo "</div>";
        
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $h->lang["main_form_save"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form>\n";
    }
    
    
    /**
     * Save Submit Settings
     */
    public function saveSettings($h) 
    {
        // Get current settings 
        $submit_settings = $h->getSerializedSettings();
        
        // Enabled
        if ($h->cage->post->keyExists('enabled')) { 
            $enabled = 'checked'; 
        } else { 
            $enabled = ''; 
        }
    
        // Content
        if ($h->cage->post->keyExists('content')) { 
            $content = 'checked'; 
        } else { 
            $content = ''; 
        }
        
        // Content length
        $content_length = $h->cage->post->getInt('content_length'); 
        if (!$content_length) { 
            $content_length = $submit_settings['content_length'];
        } 
        
        // Summary
        if ($h->cage->post->keyExists('summary')) { 
            $summary = 'checked'; 
        } else { 
            $summary = ''; 
        }
        
        // Summary length
        $summary_length = $h->cage->post->getInt('summary_length'); 
        if (!$summary_length) { 
            $summary_length = $submit_settings['summary_length'];
        } 
        
        // Allowable tags
        $allowable_tags = $h->cage->post->getRaw('allowable_tags'); 
        if (!$allowable_tags) { 
            $allowable_tags = $submit_settings['allowable_tags'];
        } 
        
        // Url limit
        $url_limit = $h->cage->post->testInt('url_limit'); 
        if (!is_numeric($url_limit)) { 
            $url_limit = $submit_settings['url_limit'];
        }
        
        // Daily limit
        $daily_limit = $h->cage->post->testInt('daily_limit'); 
        if (!is_numeric($daily_limit)) { 
            $daily_limit = $submit_settings['daily_limit'];
        }
        
        // Frequency limit
        $freq_limit = $h->cage->post->testInt('freq_limit'); 
        if (!is_numeric($freq_limit)) { 
            $freq_limit = $submit_settings['freq_limit'];
        }
        
        // Set pending
        $set_pending = $h->cage->post->testAlnumLines('set_pending');
        if (!$set_pending) {
            $set_pending = $submit_settings['set_pending'];
        }
        
        // First X posts
        $x_posts = $h->cage->post->testInt('first_x_posts');
        if (!$x_posts) {
            $x_posts = $submit_settings['x_posts'];
        }
        
        // Send email notification about new posts
        if ($h->cage->post->keyExists('email_notify')) { 
            $email_notify = 'checked'; 
        } else { 
            $email_notify = ''; 
        }
        
        // admins to receive above email notification
        if ($h->cage->post->keyExists('emailmod')) 
        {
            $email_mods = array();
            foreach ($h->cage->post->keyExists('emailmod') as $id => $array) {
                $email_mods[$id]['id'] = $id;
                $email_mods[$id]['email'] = key($array);
                $email_mods[$id]['type'] = $array[$email_mods[$id]['email']];
            }
        } else {
            $email_mods = $submit_settings['email_notify_mods'];
        }
        
        // Categories
        if ($h->cage->post->keyExists('categories')) { 
            $categories = 'checked'; 
        } else { 
            $categories = ''; 
        }
        
        // Tags
        if ($h->cage->post->keyExists('tags')) { 
            $tags = 'checked'; 
        } else { 
            $tags = ''; 
        }
        
        // Max Tags
        $max_tags = $h->cage->post->testInt('max_tags'); 
        if (!is_numeric($max_tags)) { 
            $max_tags = $submit_settings['max_tags'];
        }
        
        
        
        $h->pluginHook('submit_save_settings');
        
        $submit_settings['enabled'] = $enabled;
        $submit_settings['content'] = $content;
        $submit_settings['content_length'] = $content_length;
        $submit_settings['summary'] = $summary;
        $submit_settings['summary_length'] = $summary_length;
        $submit_settings['allowable_tags'] = $allowable_tags;
        $submit_settings['url_limit'] = $url_limit;
        $submit_settings['daily_limit'] = $daily_limit;
        $submit_settings['freq_limit'] = $freq_limit;
        $submit_settings['set_pending'] = $set_pending;
        $submit_settings['x_posts'] = $x_posts;
        $submit_settings['email_notify'] = $email_notify;
        $submit_settings['email_notify_mods'] = $email_mods; //array
        $submit_settings['categories'] = $categories;
        $submit_settings['tags'] = $tags;
        $submit_settings['max_tags'] = $max_tags;
    
        $h->updateSetting('submit_settings', serialize($submit_settings));
        
        $h->message = $h->lang["main_settings_saved"];
        $h->messageType = "green";
        
        return true;    
    }
    
}
?>
