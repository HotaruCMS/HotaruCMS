<?php
/**
 * File: /plugins/comments/comments_settings.php
 * Purpose: Admin settings for the comments plugin
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
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

class CommentsSettings
{
     /**
     * Admin settings for Comments
     */
    public function settings($h)
    {
        // If the form has been submitted, go and save the data...
        if ($h->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings($h); 
        }
        
        // Get settings from database if they exist...
        $comments_settings = $h->getSerializedSettings();
        
        // Create a new global object called "comment".
        require_once(LIBS . 'Comment.php');
        $h->comment = new Comment();
        
        // Assign settings to class member
        $h->comment->allForms = $comments_settings['comment_all_forms'];
        $h->comment->avatars = $comments_settings['comment_avatars'];
        $h->comment->avatarSize = $comments_settings['comment_avatar_size'];
        $h->comment->voting = $comments_settings['comment_voting'];
        $h->comment->email = $comments_settings['comment_email'];
        $h->comment->allowableTags = $comments_settings['comment_allowable_tags'];
        $h->comment->levels = $comments_settings['comment_levels'];
        $h->comment->itemsPerPage = $comments_settings['comment_items_per_page'];
        $h->comment->order = $comments_settings['comment_order'];
        $h->comment->pagination = $comments_settings['comment_pagination'];
        $set_pending = $comments_settings['comment_set_pending'];
        $x_comments = $comments_settings['comment_x_comments'];
        $email_notify = $comments_settings['comment_email_notify'];
        $email_mods = $comments_settings['comment_email_notify_mods'];
        $url_limit = $comments_settings['comment_url_limit'];
        $daily_limit = $comments_settings['comment_daily_limit'];
        $hide = $comments_settings['comment_hide'];
        $bury = $comments_settings['comment_bury'];
        
        echo "<h1>" . $h->lang["comments_settings_header"] . "</h1>\n";
          
        // Set defaults for empty values:
        if (!$h->comment->allForms) { $h->comment->allForms = ''; }
        if (!$h->comment->avatars) { $h->comment->avatars = ''; }
        if (!$h->comment->avatarSize) { $h->comment->avatarSize = 16; }
        if (!$h->comment->voting) { $h->comment->voting = ''; }
        if (!$h->comment->levels) { $h->comment->levels = 5; }
        if (!$h->comment->email) { $h->comment->email = ''; }
        if (!$h->comment->allowableTags) { $h->comment->allowableTags = ''; }
        if (!$h->comment->itemsPerPage) { $h->comment->itemsPerPage = 20; }
        if (!$h->comment->order) { $h->comment->order = 'asc'; }
        if (!$h->comment->pagination) { $h->comment->pagination = ''; }
        if (!$set_pending) { $set_pending = 'auto_approve'; }
        if (!$url_limit) { $url_limit = 0; }
        if (!$daily_limit) { $daily_limit = 0; }
        if (!$x_comments) { $x_comments = 1; }
        if (!$hide) { $hide = 3; }
        if (!$bury) { $bury = 10; }
    
        // Determine if checkboxes are checked or not
        if ($h->comment->allForms == 'checked') { $check_form = 'checked'; } else { $check_form = ''; }
        if ($h->comment->avatars == 'checked') { $check_avatars = 'checked'; } else { $check_avatars = ''; }
        if ($h->comment->voting == 'checked') { $check_votes = 'checked'; } else { $check_votes = ''; }
        if ($h->comment->pagination == 'checked') { $check_pagination = 'checked'; } else { $check_pagination = ''; }
        if ($h->comment->order == 'asc') { 
            $ascending = 'checked'; $descending = ''; 
        } else { 
            $ascending = ''; $descending = 'checked'; 
        }
        
        $h->pluginHook('comments_settings_get_values');
               
        echo "<form name='comments_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=comments' method='post'>\n";
        
        echo "<p>" . $h->lang["comments_settings_instructions"] . "</p><br />";
            
        echo "<p><input type='checkbox' name='comment_form' value='comment_form' " . $check_form . " >&nbsp;&nbsp;" . $h->lang["comments_settings_form"] . "</p>\n";    
        echo "<p><input type='checkbox' name='comment_avatars' value='comment_avatars' " . $check_avatars . " >&nbsp;&nbsp;" . $h->lang["comments_settings_avatars"] . "</p>\n";
        echo "<p>" . " <input type='text' size=5 name='avatar_size' value='" . $h->comment->avatarSize . "' /> " . $h->lang["comments_settings_avatar_size"] . "</p>";
        echo "<p><input type='checkbox' name='comment_voting' value='comment_voting' " . $check_votes . " >&nbsp;&nbsp;" . $h->lang["comments_settings_votes"] . "</p>\n";
        echo "<p>" . " <input type='text' size=5 name='hide' value='" . $hide . "' /> " . $h->lang["comments_settings_hide"] . "</p>";
        echo "<p>" . " <input type='text' size=5 name='bury' value='" . $bury . "' /> " . $h->lang["comments_settings_bury"] . "</p>";
    
        echo "<p>" . " <input type='text' size=5 name='levels' value='" . $h->comment->levels . "' /> " . $h->lang["comments_settings_levels"] . "</p>";
        echo "<p><input type='checkbox' name='comment_pagination' value='comment_pagination' " . $check_pagination . " >&nbsp;&nbsp;" . $h->lang["comments_settings_pagination"] . "</p>\n"; 
        echo "<p>" . " <input type='text' size=5 name='itemsperpage' value='" . $h->comment->itemsPerPage . "' /> " . $h->lang["comments_settings_per_page"] . "</p>";
        echo "<p>" . $h->lang["comments_settings_per_page_note"] . "</p>";
        
        echo "<p><input type='radio' name='comment_order' value='asc' " . $ascending . " >&nbsp;" . $h->lang["comments_settings_ascending"] . "&nbsp;&nbsp;\n"; 
        echo "<input type='radio' name='comment_order' value='desc' " . $descending . " >&nbsp;" . $h->lang["comments_settings_descending"] . "</p>\n"; 
        
        echo "<p>" . $h->lang["comments_settings_allowable_tags"] . " <input type='text' size=40 name='allowabletags' value='" . $h->comment->allowableTags . "' /><br />";
        echo $h->lang["comments_settings_allowable_tags_example"] . "</p>\n";
        
        echo "<p>" . " <input type='text' size=5 name='url_limit' value='" . $url_limit . "' /> " . $h->lang["comments_settings_url_limit"] . "</p>";
        echo "<p>" . " <input type='text' size=5 name='daily_limit' value='" . $daily_limit . "' /> " . $h->lang["comments_settings_daily_limit"] . "</p>";
        
        echo "<p>" . $h->lang["comments_settings_limit_note"] . "</p>";
        
        $h->pluginHook('comments_settings_form');
        
        echo "<p>" . $h->lang["comments_settings_email"] . " <input type='text' size=30 name='email' value='" . $h->comment->email . "' /> ";
        echo $h->lang["comments_settings_email_desc"] . "</p>";
        
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
        
        echo "<input type='radio' name='set_pending' value='auto_approve' " . $auto_approve . " >&nbsp;&nbsp;" . $h->lang["comments_settings_auto_approve"] . "<br />\n"; 
        echo "<input type='radio' name='set_pending' value='some_pending' " . $some_pending . " >&nbsp;&nbsp;" . $h->lang["comments_settings_some_pending_1"] . "\n"; 
        echo "<select name='first_x_comments'>\n";
            echo "<option>" . $x_comments . "</option>\n";
            echo '<option disabled>-----</option>';
            echo "<option>1</option>\n";
            echo "<option>2</option>\n";
            echo "<option>3</option>\n";
            echo "<option>4</option>\n";
            echo "<option>5</option>\n";
            echo "<option>10</option>\n";
            echo "<option>20</option>\n";
        echo "</select>\n";
        echo $h->lang["comments_settings_some_pending_2"] . "<br />\n"; 
        echo "<input type='radio' name='set_pending' value='all_pending' " . $all_pending . " >&nbsp;&nbsp;" . $h->lang["comments_settings_all_pending"] . "\n"; 
                
        echo "<br /><br />\n";
                
        // email notify options
            
        echo "<input type='checkbox' name='email_notify' value='email_notify' id='email_notify' " . $email_notify . ">&nbsp;&nbsp;" ;
        echo $h->lang["comments_settings_email_notify"] . "<br /><br />\n";
    
        $admins = $h->getMods('can_comment_manager_settings', 'yes');
        if (!$email_notify) { $show_admins = 'display: none;'; } else { $show_admins = ''; }
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
                echo " " . $h->lang["comments_settings_email_notify_all"] . "</td>\n";
                
                echo "<td><input type='radio' name='emailmod[" . $ad['id'] . "][" . $ad['email'] . "]' value='pending' " . $checked_pend . ">";
                echo " " . $h->lang["comments_settings_email_notify_pending"] . "</td>\n";
                
                echo "<td><input type='radio' name='emailmod[" . $ad['id'] . "][" . $ad['email'] . "]' value='none' " . $checked_none . ">";
                echo " " . $h->lang["comments_settings_email_notify_none"] . "</td>\n";
                echo "</tr>\n";
            }
            echo "</table>\n";
        }
        echo "</div><br />";
        
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $h->lang["comments_settings_save"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form>\n";
    }
    
    
     /**
     * Save admin settings for Comments
     *
     * @return true
     */
    public function saveSettings($h)
    {
        // Create a new global object called "comment".
        require_once(LIBS . 'Comment.php');
        $h->comment = new Comment();
        
        // enable/disable comment form globally
        if ($h->cage->post->keyExists('comment_form')) { 
            $h->comment->allForms = 'checked';
        } else {
            $h->comment->allForms = '';
        }
        
        // enable avatars on comments
        if ($h->cage->post->keyExists('comment_avatars')) { 
            $h->comment->avatars = 'checked';
        } else {
            $h->comment->avatars = '';
        }
        
        // avatar size
        if ($h->cage->post->keyExists('avatar_size')) { 
            $avatar_size = $h->cage->post->testInt('avatar_size'); 
            if (empty($avatar_size)) { $avatar_size = $h->comment->avatarSize; }
        } else { 
            $avatar_size = $h->comment->avatarSize; 
        }
        
        // enable votes on comments
        if ($h->cage->post->keyExists('comment_voting')) { 
            $h->comment->voting = 'checked';
        } else {
            $h->comment->voting = '';
        }
        
        // Number of down votes to hide a comment
        $hide = $h->cage->post->testInt('hide'); 
        if (!$hide) { $hide = 3; } // default
        
        // Number of down votes to bury a comment
        $bury = $h->cage->post->testInt('bury'); 
        if (!$bury) { $bury = 10; } // default
        
        // levels
        if ($h->cage->post->keyExists('levels')) { 
            $levels = $h->cage->post->testInt('levels'); 
            if (empty($levels)) { $levels = $h->comment->levels; }
        } else { 
            $levels = $h->comment->levels; 
        }
        
        // email
        if ($h->cage->post->keyExists('email')) { 
            $email = $h->cage->post->testEmail('email'); 
            if (empty($email)) { $email = $h->comment->email; }
        } else { 
            $email = $h->comment->email; 
        }
        
        // Allowable tags
        if ($h->cage->post->keyExists('allowabletags')) { 
            $allowable_tags = $h->cage->post->getRaw('allowabletags'); 
            if (empty($allowable_tags)) { $allowable_tags = $h->comment->allowableTags; }
        } else { 
            $allowable_tags = $h->comment->allowableTags; 
        }
        
        // Items per page
        if ($h->cage->post->keyExists('itemsperpage')) { 
            $items_per_page = $h->cage->post->testInt('itemsperpage'); 
            if (!$items_per_page) { $items_per_page = $h->comment->itemsPerPage; }
        } else { 
            $items_per_page = $h->comment->itemsPerPage; 
        }
        
        // Pagination
        if ($h->cage->post->keyExists('comment_pagination')) { 
            $h->comment->pagination = 'checked';
        } else {
            $h->comment->pagination = '';
        }
        
        
        // Comment order
        if ($h->cage->post->keyExists('comment_order')) { 
            $h->comment->order = $h->cage->post->testAlpha('comment_order');
        } else {
            $h->comment->order = 'asc'; // default
        }
        
        // Url limit
        if ($h->cage->post->keyExists('url_limit')) { 
            $url_limit = $h->cage->post->testInt('url_limit'); 
            if (!is_numeric($url_limit)) { $url_limit = 0; }
        } else { 
            $url_limit = 0; 
        }
        
        // Daily limit
        if ($h->cage->post->keyExists('daily_limit')) { 
            $daily_limit = $h->cage->post->testInt('daily_limit'); 
            if (!is_numeric($daily_limit)) { $daily_limit = 0; }
        } else { 
            $daily_limit = 0; 
        }
        
        // Set pending
        if ($h->cage->post->keyExists('set_pending')) { 
            $set_pending = $h->cage->post->testAlnumLines('set_pending');
        } else {
            $set_pending = 'auto_approve';
        }
        
        // First X comments
        if ($h->cage->post->keyExists('first_x_comments')) { 
            $x_comments = $h->cage->post->testInt('first_x_comments');
        } else {
            $x_comments = 1; //default
        }
        
        // Send email notification about new comments
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
        }
        
        $h->pluginHook('comments_save_settings');
        
        $comments_settings['comment_all_forms'] = $h->comment->allForms;
        $comments_settings['comment_avatars'] = $h->comment->avatars;
        $comments_settings['comment_avatar_size'] = $avatar_size;
        $comments_settings['comment_voting'] = $h->comment->voting;
        $comments_settings['comment_levels'] = $levels;
        $comments_settings['comment_email'] = $email;
        $comments_settings['comment_pagination'] = $h->comment->pagination;
        $comments_settings['comment_order'] = $h->comment->order;
        $comments_settings['comment_allowable_tags'] = $allowable_tags;
        $comments_settings['comment_items_per_page'] = $items_per_page;
        $comments_settings['comment_url_limit'] = $url_limit;
        $comments_settings['comment_daily_limit'] = $daily_limit;
        $comments_settings['comment_set_pending'] = $set_pending;
        $comments_settings['comment_x_comments'] = $x_comments;
        $comments_settings['comment_email_notify'] = $email_notify;
        $comments_settings['comment_email_notify_mods'] = $email_mods; //array
        $comments_settings['comment_hide'] = $hide;
        $comments_settings['comment_bury'] = $bury;
        
        $h->updateSetting('comments_settings', serialize($comments_settings));
        
        $h->message = $h->lang["comments_settings_saved"];
        $h->messageType = "green";
        $h->showMessage();
        
        return true;    
    }

}
?>