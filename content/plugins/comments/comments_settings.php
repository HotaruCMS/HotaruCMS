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
 
 /**
 * Admin settings for Comments
 */
function cmmts_settings()
{
    global $hotaru, $plugin, $cage, $lang, $comment;
    
    // If the form has been submitted, go and save the data...
    if ($cage->post->getAlpha('submitted') == 'true') { 
        cmmts_save_settings(); 
    }
    
    // Get settings from database if they exist...
    $comment_settings = $comment->get_comment_settings();

    // Assign settings to class member
    $comment->comment_form = $comment_settings['comment_form'];
    $comment->comment_avatars = $comment_settings['comment_avatars'];
    $comment->comment_voting = $comment_settings['comment_voting'];
    $comment->comment_email = $comment_settings['comment_email'];
    $comment->comment_allowable_tags = $comment_settings['comment_allowable_tags'];
    $comment->comment_levels = $comment_settings['comment_levels'];
    
    echo "<h1>" . $lang["comments_settings_header"] . "</h1>\n";
      
    // Set defaults for empty values:
    if (!$comment->comment_form) { $comment->comment_form = ''; }
    if (!$comment->comment_avatars) { $comment->comment_avatars = ''; }
    if (!$comment->comment_voting) { $comment->comment_voting = ''; }
    if (!$comment->comment_levels) { $comment->comment_levels = 5; }
    if (!$comment->comment_email) { $comment->comment_email = ''; }
    if (!$comment->comment_allowable_tags) { $comment->comment_allowable_tags = ''; }

    // Determine if checkboxes are checked or not
    if ($comment->comment_form == 'checked') { $check_form = 'checked'; } else { $check_form = ''; }
    if ($comment->comment_avatars == 'checked') { $check_avatars = 'checked'; } else { $check_avatars = ''; }
    if ($comment->comment_voting == 'checked') { $check_votes = 'checked'; } else { $check_votes = ''; }
    
     
    $plugin->check_actions('comments_settings_get_values');
           
    echo "<form name='comments_settings_form' action='" . BASEURL . "admin/admin_index.php?page=plugin_settings&amp;plugin=comments' method='post'>\n";
    
    echo "<p>" . $lang["comments_settings_instructions"] . "</p><br />";
        
    echo "<p><input type='checkbox' name='comment_form' value='comment_form' " . $check_form . " >&nbsp;&nbsp;" . $lang["comments_settings_form"] . "</p>\n";    
    echo "<p><input type='checkbox' name='comment_avatars' value='comment_avatars' " . $check_avatars . " >&nbsp;&nbsp;" . $lang["comments_settings_avatars"] . "</p>\n"; 
    echo "<p><input type='checkbox' name='comment_voting' value='comment_voting' " . $check_votes . " >&nbsp;&nbsp;" . $lang["comments_settings_votes"] . "</p>\n"; 

    echo "<br />" . $lang["comments_settings_levels"] . " <input type='text' size=5 name='levels' value='" . $comment->comment_levels . "' /><br />";
    echo "<br />" . $lang["comments_settings_email"] . " <input type='text' size=30 name='email' value='" . $comment->comment_email . "' /> ";
    echo $lang["comments_settings_email_desc"] . "<br />";
    echo "<br />" . $lang["comments_settings_allowable_tags"] . " <input type='text' size=40 name='allowabletags' value='" . $comment->comment_allowable_tags . "' /><br />";
    echo $lang["comments_settings_allowable_tags_example"] . "\n";
    
    $plugin->check_actions('comments_settings_form');
            
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
function cmmts_save_settings()
{
    global $cage, $hotaru, $plugin, $lang, $comment;

    // enable comment form globally
    if ($cage->post->keyExists('comment_form')) { 
        $comment->comment_form = 'checked';
    } else {
        $comment->comment_form = '';
    }
    
    // enable avatars on comments
    if ($cage->post->keyExists('comment_avatars')) { 
        $comment->comment_avatars = 'checked';
    } else {
        $comment->comment_avatars = '';
    }
    
    // enable votes on comments
    if ($cage->post->keyExists('comment_voting')) { 
        $comment->comment_voting = 'checked';
    } else {
        $comment->comment_voting = '';
    }
    
    // levels
    if ($cage->post->keyExists('levels')) { 
        $levels = $cage->post->testInt('levels'); 
        if (empty($levels)) { $levels = $comment->comment_levels; }
    } else { 
        $levels = $comment->comment_levels; 
    }
    
    // email
    if ($cage->post->keyExists('email')) { 
        $email = $cage->post->testEmail('email'); 
        if (empty($email)) { $email = $comment->comment_email; }
    } else { 
        $email = $comment->comment_email; 
    }
    
    // Allowable tags
    if ($cage->post->keyExists('allowabletags')) { 
        $allowable_tags = $cage->post->getRaw('allowabletags'); 
        if (empty($allowable_tags)) { $allowable_tags = $comment->comment_allowable_tags; }
    } else { 
        $allowable_tags = $comment->comment_allowable_tags; 
    }
    
    $plugin->check_actions('comments_save_settings');
    
    $comment_settings['comment_form'] = $comment->comment_form;
    $comment_settings['comment_avatars'] = $comment->comment_avatars;
    $comment_settings['comment_voting'] = $comment->comment_voting;
    $comment_settings['comment_levels'] = $levels;
    $comment_settings['comment_email'] = $email;
    $comment_settings['comment_allowable_tags'] = $allowable_tags;
    $plugin->plugin_settings_update('comments', 'comment_settings', serialize($comment_settings));
    
    $hotaru->message = $lang["comments_settings_saved"];
    $hotaru->message_type = "green";
    $hotaru->show_message();
    
    return true;    
}
?>