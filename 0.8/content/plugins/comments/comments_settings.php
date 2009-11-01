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
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

class CommentsSettings extends Comments
{
     /**
     * Admin settings for Comments
     */
    public function settings()
    {
        // If the form has been submitted, go and save the data...
        if ($this->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings(); 
        }
        
        // Get settings from database if they exist...
        $comments_settings = $this->getSerializedSettings();
        
        // Assign settings to class member
        $this->hotaru->comment->allForms = $comments_settings['comment_all_forms'];
        $this->hotaru->comment->avatars = $comments_settings['comment_avatars'];
        $this->hotaru->comment->voting = $comments_settings['comment_voting'];
        $this->hotaru->comment->email = $comments_settings['comment_email'];
        $this->hotaru->comment->allowableTags = $comments_settings['comment_allowable_tags'];
        $this->hotaru->comment->levels = $comments_settings['comment_levels'];
        $this->hotaru->comment->itemsPerPage = $comments_settings['comment_items_per_page'];
        $this->hotaru->comment->order = $comments_settings['comment_order'];
        $this->hotaru->comment->pagination = $comments_settings['comment_pagination'];
        $set_pending = $comments_settings['comment_set_pending'];
        $x_comments = $comments_settings['comment_x_comments'];
        $email_notify = $comments_settings['comment_email_notify'];
        $email_mods = $comments_settings['comment_email_notify_mods'];
        
        echo "<h1>" . $this->lang["comments_settings_header"] . "</h1>\n";
          
        // Set defaults for empty values:
        if (!$this->hotaru->comment->allForms) { $this->hotaru->comment->allForms = ''; }
        if (!$this->hotaru->comment->avatars) { $this->hotaru->comment->avatars = ''; }
        if (!$this->hotaru->comment->voting) { $this->hotaru->comment->voting = ''; }
        if (!$this->hotaru->comment->levels) { $this->hotaru->comment->levels = 5; }
        if (!$this->hotaru->comment->email) { $this->hotaru->comment->email = ''; }
        if (!$this->hotaru->comment->allowableTags) { $this->hotaru->comment->allowableTags = ''; }
        if (!$this->hotaru->comment->itemsPerPage) { $this->hotaru->comment->itemsPerPage = 20; }
        if (!$this->hotaru->comment->order) { $this->hotaru->comment->order = 'asc'; }
        if (!$this->hotaru->comment->pagination) { $this->hotaru->comment->pagination = ''; }
        if (!$set_pending) { $set_pending = 'auto_approve'; }
        if (!$x_comments) { $x_comments = 1; }
    
        // Determine if checkboxes are checked or not
        if ($this->hotaru->comment->allForms == 'checked') { $check_form = 'checked'; } else { $check_form = ''; }
        if ($this->hotaru->comment->avatars == 'checked') { $check_avatars = 'checked'; } else { $check_avatars = ''; }
        if ($this->hotaru->comment->voting == 'checked') { $check_votes = 'checked'; } else { $check_votes = ''; }
        if ($this->hotaru->comment->pagination == 'checked') { $check_pagination = 'checked'; } else { $check_pagination = ''; }
        if ($this->hotaru->comment->order == 'asc') { 
            $ascending = 'checked'; $descending = ''; 
        } else { 
            $ascending = ''; $descending = 'checked'; 
        }
        
        $this->pluginHook('comments_settings_get_values');
               
        echo "<form name='comments_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=comments' method='post'>\n";
        
        echo "<p>" . $this->lang["comments_settings_instructions"] . "</p><br />";
            
        echo "<p><input type='checkbox' name='comment_form' value='comment_form' " . $check_form . " >&nbsp;&nbsp;" . $this->lang["comments_settings_form"] . "</p>\n";    
        echo "<p><input type='checkbox' name='comment_avatars' value='comment_avatars' " . $check_avatars . " >&nbsp;&nbsp;" . $this->lang["comments_settings_avatars"] . "</p>\n"; 
        echo "<p><input type='checkbox' name='comment_voting' value='comment_voting' " . $check_votes . " >&nbsp;&nbsp;" . $this->lang["comments_settings_votes"] . "</p>\n"; 
    
        echo "<p>" . " <input type='text' size=5 name='levels' value='" . $this->hotaru->comment->levels . "' /> " . $this->lang["comments_settings_levels"] . "</p>";
        echo "<p><input type='checkbox' name='comment_pagination' value='comment_pagination' " . $check_pagination . " >&nbsp;&nbsp;" . $this->lang["comments_settings_pagination"] . "</p>\n"; 
        echo "<p>" . " <input type='text' size=5 name='itemsperpage' value='" . $this->hotaru->comment->itemsPerPage . "' /> " . $this->lang["comments_settings_per_page"] . "</p>";
        echo "<p>" . $this->lang["comments_settings_per_page_note"] . "</p>";
        
        echo "<p><input type='radio' name='comment_order' value='asc' " . $ascending . " >&nbsp;" . $this->lang["comments_settings_ascending"] . "&nbsp;&nbsp;\n"; 
        echo "<input type='radio' name='comment_order' value='desc' " . $descending . " >&nbsp;" . $this->lang["comments_settings_descending"] . "</p>\n"; 
        
        echo "<p>" . $this->lang["comments_settings_email"] . " <input type='text' size=30 name='email' value='" . $this->hotaru->comment->email . "' /> ";
        echo $this->lang["comments_settings_email_desc"] . "</p>";
        echo "<p>" . $this->lang["comments_settings_allowable_tags"] . " <input type='text' size=40 name='allowabletags' value='" . $this->hotaru->comment->allowableTags . "' /><br />";
        echo $this->lang["comments_settings_allowable_tags_example"] . "</p>\n";
        
        $this->pluginHook('comments_settings_form');
        
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
        
        echo "<input type='radio' name='set_pending' value='auto_approve' " . $auto_approve . " >&nbsp;&nbsp;" . $this->lang["comments_settings_auto_approve"] . "<br />\n"; 
        echo "<input type='radio' name='set_pending' value='some_pending' " . $some_pending . " >&nbsp;&nbsp;" . $this->lang["comments_settings_some_pending_1"] . "\n"; 
        echo "<select name='first_x_posts'>\n";
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
        echo $this->lang["comments_settings_some_pending_2"] . "<br />\n"; 
        echo "<input type='radio' name='set_pending' value='all_pending' " . $all_pending . " >&nbsp;&nbsp;" . $this->lang["comments_settings_all_pending"] . "\n"; 
                
        echo "<br /><br />\n";
                
        // email notify options - only show if the UserFunctions file exists
        if (file_exists(PLUGINS . 'users/libs/UserFunctions.php')) {
            require_once(PLUGINS . 'users/libs/UserFunctions.php');
            $uf = new UserFunctions($this->hotaru);
            
            echo "<input type='checkbox' name='email_notify' value='email_notify' id='email_notify' " . $email_notify . ">&nbsp;&nbsp;" ;
            echo $this->lang["comments_settings_email_notify"] . "<br /><br />\n";
        
            $admins = $uf->getMods('can_access_admin', 'yes');
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
                    echo " " . $this->lang["comments_settings_email_notify_all"] . "</td>\n";
                    
                    echo "<td><input type='radio' name='emailmod[" . $ad['id'] . "][" . $ad['email'] . "]' value='pending' " . $checked_pend . ">";
                    echo " " . $this->lang["comments_settings_email_notify_pending"] . "</td>\n";
                    
                    echo "<td><input type='radio' name='emailmod[" . $ad['id'] . "][" . $ad['email'] . "]' value='none' " . $checked_none . ">";
                    echo " " . $this->lang["comments_settings_email_notify_none"] . "</td>\n";
                    echo "</tr>\n";
                }
                echo "</table>\n";
            }
            echo "</div><br />";
        }
        
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $this->lang["comments_settings_save"] . "' />\n";
        echo "</form>\n";
    }
    
    
     /**
     * Save admin settings for Comments
     *
     * @return true
     */
    public function saveSettings()
    {
        // enable/disable comment form globally
        if ($this->cage->post->keyExists('comment_form')) { 
            $this->hotaru->comment->allForms = 'checked';
        } else {
            $this->hotaru->comment->allForms = '';
        }
        
        // enable avatars on comments
        if ($this->cage->post->keyExists('comment_avatars')) { 
            $this->hotaru->comment->avatars = 'checked';
        } else {
            $this->hotaru->comment->avatars = '';
        }
        
        // enable votes on comments
        if ($this->cage->post->keyExists('comment_voting')) { 
            $this->hotaru->comment->voting = 'checked';
        } else {
            $this->hotaru->comment->voting = '';
        }
        
        // levels
        if ($this->cage->post->keyExists('levels')) { 
            $levels = $this->cage->post->testInt('levels'); 
            if (empty($levels)) { $levels = $this->hotaru->comment->levels; }
        } else { 
            $levels = $this->hotaru->comment->levels; 
        }
        
        // email
        if ($this->cage->post->keyExists('email')) { 
            $email = $this->cage->post->testEmail('email'); 
            if (empty($email)) { $email = $this->hotaru->comment->email; }
        } else { 
            $email = $this->hotaru->comment->email; 
        }
        
        // Allowable tags
        if ($this->cage->post->keyExists('allowabletags')) { 
            $allowable_tags = $this->cage->post->getRaw('allowabletags'); 
            if (empty($allowable_tags)) { $allowable_tags = $this->hotaru->comment->allowableTags; }
        } else { 
            $allowable_tags = $this->hotaru->comment->allowableTags; 
        }
        
        // Items per page
        if ($this->cage->post->keyExists('itemsperpage')) { 
            $items_per_page = $this->cage->post->testInt('itemsperpage'); 
            if (!$items_per_page) { $items_per_page = $this->hotaru->comment->itemsPerPage; }
        } else { 
            $items_per_page = $this->hotaru->comment->itemsPerPage; 
        }
        
        // Pagination
        if ($this->cage->post->keyExists('comment_pagination')) { 
            $this->hotaru->comment->pagination = 'checked';
        } else {
            $this->hotaru->comment->pagination = '';
        }
        
        
        // Pagination
        if ($this->cage->post->keyExists('comment_order')) { 
            $this->hotaru->comment->order = $this->cage->post->testAlpha('comment_order');
        } else {
            $this->hotaru->comment->order = 'asc'; // default
        }
        
        // Set pending
        if ($this->cage->post->keyExists('set_pending')) { 
            $set_pending = $this->cage->post->testAlnumLines('set_pending');
        } else {
            $set_pending = 'auto_approve';
        }
        
        // First X comments
        if ($this->cage->post->keyExists('first_x_comments')) { 
            $x_comments = $this->cage->post->testInt('first_x_comments');
        } else {
            $x_comments = 1; //default
        }
        
        // Send email notification about new comments
        if ($this->cage->post->keyExists('email_notify')) { 
            $email_notify = 'checked'; 
        } else { 
            $email_notify = ''; 
        }
        
        // admins to receive above email notification
        if ($this->cage->post->keyExists('emailmod')) 
        {
            $email_mods = array();
            foreach ($this->cage->post->keyExists('emailmod') as $id => $array) {
                $email_mods[$id]['id'] = $id;
                $email_mods[$id]['email'] = key($array);
                $email_mods[$id]['type'] = $array[$email_mods[$id]['email']];
            }
        }
        
        $this->pluginHook('comments_save_settings');
        
        $comments_settings['comment_all_forms'] = $this->hotaru->comment->allForms;
        $comments_settings['comment_avatars'] = $this->hotaru->comment->avatars;
        $comments_settings['comment_voting'] = $this->hotaru->comment->voting;
        $comments_settings['comment_levels'] = $levels;
        $comments_settings['comment_email'] = $email;
        $comments_settings['comment_pagination'] = $this->hotaru->comment->pagination;
        $comments_settings['comment_order'] = $this->hotaru->comment->order;
        $comments_settings['comment_allowable_tags'] = $allowable_tags;
        $comments_settings['comment_items_per_page'] = $items_per_page;
        $comments_settings['comment_set_pending'] = $set_pending;
        $comments_settings['comment_x_comments'] = $x_comments;
        $comments_settings['comment_email_notify'] = $email_notify;
        $comments_settings['comment_email_notify_mods'] = $email_mods; //array
        
        $this->updateSetting('comments_settings', serialize($comments_settings));
        
        $this->hotaru->message = $this->lang["comments_settings_saved"];
        $this->hotaru->messageType = "green";
        $this->hotaru->showMessage();
        
        return true;    
    }

}
?>