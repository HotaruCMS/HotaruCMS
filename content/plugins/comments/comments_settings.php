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
    /* Allows us to call functions without specifying what plugin this is. */
    public function __construct($folder) { $this->folder = $folder; }
    
    
     /**
     * Admin settings for Comments
     */
    public function settings()
    {
        global $hotaru, $cage, $lang, $comment;
        
        // If the form has been submitted, go and save the data...
        if ($cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings(); 
        }
        
        // Get settings from database if they exist...
        $comments_settings = $this->getSerializedSettings();
        
        // Assign settings to class member
        $comment->setForm($comments_settings['comment_form']);
        $comment->setAvatars($comments_settings['comment_avatars']);
        $comment->setVoting($comments_settings['comment_voting']);
        $comment->setEmail($comments_settings['comment_email']);
        $comment->setAllowableTags($comments_settings['comment_allowable_tags']);
        $comment->setLevels($comments_settings['comment_levels']);
        
        echo "<h1>" . $lang["comments_settings_header"] . "</h1>\n";
          
        // Set defaults for empty values:
        if (!$comment->getForm()) { $comment->setForm(); }
        if (!$comment->getAvatars()) { $comment->setAvatars(); }
        if (!$comment->getVoting()) { $comment->setVoting(); }
        if (!$comment->getLevels()) { $comment->setLevels(5); }
        if (!$comment->getEmail()) { $comment->setEmail(); }
        if (!$comment->getAllowableTags()) { $comment->setAllowableTags(); }
    
        // Determine if checkboxes are checked or not
        if ($comment->getForm() == 'checked') { $check_form = 'checked'; } else { $check_form = ''; }
        if ($comment->getAvatars() == 'checked') { $check_avatars = 'checked'; } else { $check_avatars = ''; }
        if ($comment->getVoting() == 'checked') { $check_votes = 'checked'; } else { $check_votes = ''; }
        
         
        $this->pluginHook('comments_settings_get_values');
               
        echo "<form name='comments_settings_form' action='" . BASEURL . "admin/admin_index.php?page=plugin_settings&amp;plugin=comments' method='post'>\n";
        
        echo "<p>" . $lang["comments_settings_instructions"] . "</p><br />";
            
        echo "<p><input type='checkbox' name='comment_form' value='comment_form' " . $check_form . " >&nbsp;&nbsp;" . $lang["comments_settings_form"] . "</p>\n";    
        echo "<p><input type='checkbox' name='comment_avatars' value='comment_avatars' " . $check_avatars . " >&nbsp;&nbsp;" . $lang["comments_settings_avatars"] . "</p>\n"; 
        echo "<p><input type='checkbox' name='comment_voting' value='comment_voting' " . $check_votes . " >&nbsp;&nbsp;" . $lang["comments_settings_votes"] . "</p>\n"; 
    
        echo "<br />" . $lang["comments_settings_levels"] . " <input type='text' size=5 name='levels' value='" . $comment->getLevels() . "' /><br />";
        echo "<br />" . $lang["comments_settings_email"] . " <input type='text' size=30 name='email' value='" . $comment->getEmail() . "' /> ";
        echo $lang["comments_settings_email_desc"] . "<br />";
        echo "<br />" . $lang["comments_settings_allowable_tags"] . " <input type='text' size=40 name='allowabletags' value='" . $comment->getAllowableTags() . "' /><br />";
        echo $lang["comments_settings_allowable_tags_example"] . "\n";
        
        $this->pluginHook('comments_settings_form');
                
        echo "<br /><br />\n";    
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $lang["comments_settings_save"] . "' />\n";
        echo "</form>\n";
    }
    
    
     /**
     * Save admin settings for Comments
     *
     * @return true
     */
    public function saveSettings()
    {
        global $cage, $hotaru, $lang, $comment;
    
        // enable comment form globally
        if ($cage->post->keyExists('comment_form')) { 
            $comment->setForm('checked');
        } else {
            $comment->setForm();
        }
        
        // enable avatars on comments
        if ($cage->post->keyExists('comment_avatars')) { 
            $comment->setAvatars('checked');
        } else {
            $comment->setAvatars();
        }
        
        // enable votes on comments
        if ($cage->post->keyExists('comment_voting')) { 
            $comment->setVoting('checked');
        } else {
            $comment->setVoting();
        }
        
        // levels
        if ($cage->post->keyExists('levels')) { 
            $levels = $cage->post->testInt('levels'); 
            if (empty($levels)) { $levels = $comment->getLevels(); }
        } else { 
            $levels = $comment->getLevels(); 
        }
        
        // email
        if ($cage->post->keyExists('email')) { 
            $email = $cage->post->testEmail('email'); 
            if (empty($email)) { $email = $comment->getEmail(); }
        } else { 
            $email = $comment->getEmail(); 
        }
        
        // Allowable tags
        if ($cage->post->keyExists('allowabletags')) { 
            $allowable_tags = $cage->post->getRaw('allowabletags'); 
            if (empty($allowable_tags)) { $allowable_tags = $comment->getAllowableTags(); }
        } else { 
            $allowable_tags = $comment->getAllowableTags(); 
        }
        
        $this->pluginHook('comments_save_settings');
        
        $comments_settings['comment_form'] = $comment->getForm();
        $comments_settings['comment_avatars'] = $comment->getAvatars();
        $comments_settings['comment_voting'] = $comment->getVoting();
        $comments_settings['comment_levels'] = $levels;
        $comments_settings['comment_email'] = $email;
        $comments_settings['comment_allowable_tags'] = $allowable_tags;
        $this->updateSetting('comments_settings', serialize($comments_settings));
        
        $hotaru->message = $lang["comments_settings_saved"];
        $hotaru->messageType = "green";
        $hotaru->showMessage();
        
        return true;    
    }

}
?>
