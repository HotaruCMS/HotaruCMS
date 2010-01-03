<?php
/**
 * File: /plugins/comments_widget/comments_widget_settings.php
 * Purpose: Admin settings for the comments widget plugin
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

class CommentsWidgetSettings
{
     /**
     * Admin settings for Comments Widget
     */
    public function settings($h)
    {
        // If the form has been submitted, go and save the data...
        if ($h->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings($h); 
        }
        
        echo "<h1>" . $h->lang["comments_widget_settings_header"] . "</h1>\n";
          
        // Get settings from database if they exist...
        $comments_widget_settings = $h->getSerializedSettings('comments_widget');
                            
        $avatar = $comments_widget_settings['avatar'];
        $avatar_size = $comments_widget_settings['avatar_size'];
        $author = $comments_widget_settings['author'];
        $length = $comments_widget_settings['length'];
        $number = $comments_widget_settings['number'];
    
        $h->pluginHook('comments_widget_settings_get_values');
        
        //...otherwise set to blank:
        if (!$avatar) { $avatar = ''; }
        if (!$avatar_size) { $avatar_size = 0; }
        if (!$author) { $author = ''; }
        if (!$length) { $length = 0; }
        if (!$number) { $number = 0; }
        
        echo "<form name='comments_widget_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=comments_widget' method='post'>\n";
        
        echo "<p>" . $h->lang["comments_widget_settings_instructions"] . "</p><br />";
        
        // show avatars?
        echo "<p><input type='checkbox' name='avatar' value='avatar' " . $avatar . " >&nbsp;&nbsp;" . $h->lang["comments_widget_settings_avatar"] . "</p>\n"; 
    
        // avatar size
        echo "<p><input type='text' size=5 name='avatar_size' value='" . $avatar_size . "' /> " . $h->lang["comments_widget_settings_avatar_size"] . "</p>\n";
        
        // show authors?
        echo "<p><input type='checkbox' name='author' value='author' " . $author . " >&nbsp;&nbsp;" . $h->lang["comments_widget_settings_author"] . "</p>\n"; 
        
        // length
        echo "<p><input type='text' size=5 name='length' value='" . $length . "' /> " . $h->lang["comments_widget_settings_length"] . "</p>\n";
        
        // number of comments
        echo "<p><input type='text' size=5 name='number' value='" . $number . "' /> " . $h->lang["comments_widget_settings_number"] . "</p>\n";
        
        $h->pluginHook('comments_widget_settings_form');
                        
        echo "<br /><br />\n";    
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $h->lang["main_form_save"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form>\n";
    }
    
    
     /**
     * Save admin settings for comments_widget
     *
     * @return true
     */
    public function saveSettings($h)
    {
        $error = 0;
        
        // show avatars?
        if ($h->cage->post->keyExists('avatar')) { 
            $avatar = 'checked'; 
        } else { 
            $avatar = ''; 
        }
        
        // avatar size
        if ($h->cage->post->keyExists('avatar_size')) { 
            if ($h->cage->post->testInt('avatar_size')) { 
                $avatar_size = $h->cage->post->testInt('avatar_size');
            } else { 
                $avatar_size = 16; 
                $error = 1;
            }
        }
        
        // show authors?
        if ($h->cage->post->keyExists('author')) { 
            $author = 'checked'; 
        } else { 
            $author = ''; 
        }
        
        // length of comments
        if ($h->cage->post->keyExists('length')) { 
            if ($h->cage->post->testInt('length')) { 
                $length = $h->cage->post->testInt('length');
            } else { 
                $length = 100; 
                $error = 1;
            }
        }
        
        // number of comments
        if ($h->cage->post->keyExists('number')) { 
            if ($h->cage->post->testInt('number')) { 
                $number = $h->cage->post->testInt('number');
            } else { 
                $number = 10; 
                $error = 1;
            }
        }
        
        $h->pluginHook('comments_widget_save_settings');
                
        if ($error == 1)
        {
            $h->message = $h->lang["comments_widget_settings_not_saved"];
            $h->messageType = "red";
            $h->showMessage();
            
            return false;
        } 
        else 
        {
            $comments_widget_settings['avatar'] = $avatar;
            $comments_widget_settings['avatar_size'] = $avatar_size;
            $comments_widget_settings['author'] = $author;
            $comments_widget_settings['length'] = $length;
            $comments_widget_settings['number'] = $number;
        
            $h->updateSetting('comments_widget_settings', serialize($comments_widget_settings));
            
            $h->message = $h->lang["main_settings_saved"];
            $h->messageType = "green";
            $h->showMessage();
        
            return true;    
        }
    }
}
?>
