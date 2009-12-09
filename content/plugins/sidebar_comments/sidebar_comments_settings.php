<?php
/**
 * File: /plugins/sidebar_comments/sidebar_comments_settings.php
 * Purpose: Admin settings for the sidebar posts plugin
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

class SidebarCommentsSettings extends SidebarComments
{
     /**
     * Admin settings for Sidebar Comments
     */
    public function settings()
    {
        // If the form has been submitted, go and save the data...
        if ($this->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings(); 
        }
        
        echo "<h1>" . $this->lang["sidebar_comments_settings_header"] . "</h1>\n";
          
        // Get settings from database if they exist...
        $sb_comments_settings = $this->getSerializedSettings('sidebar_comments');
                            
        $avatar = $sb_comments_settings['sidebar_comments_avatar'];
        $avatar_size = $sb_comments_settings['sidebar_comments_avatar_size'];
        $author = $sb_comments_settings['sidebar_comments_author'];
        $length = $sb_comments_settings['sidebar_comments_length'];
        $number = $sb_comments_settings['sidebar_comments_number'];
    
        $this->pluginHook('sidebar_comments_settings_get_values');
        
        //...otherwise set to blank:
        if (!$avatar) { $avatar = ''; }
        if (!$avatar_size) { $avatar_size = 0; }
        if (!$author) { $author = ''; }
        if (!$length) { $length = 0; }
        if (!$number) { $number = 0; }
        
        echo "<form name='sidebar_comments_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=sidebar_comments' method='post'>\n";
        
        echo "<p>" . $this->lang["sidebar_comments_settings_instructions"] . "</p><br />";
        
        // show avatars?
        echo "<p><input type='checkbox' name='avatar' value='avatar' " . $avatar . " >&nbsp;&nbsp;" . $this->lang["sidebar_comments_settings_avatar"] . "</p>\n"; 
    
        // avatar size
        echo "<p><input type='text' size=5 name='avatar_size' value='" . $avatar_size . "' /> " . $this->lang["sidebar_comments_settings_avatar_size"] . "</p>\n";
        
        // show authors?
        echo "<p><input type='checkbox' name='author' value='author' " . $author . " >&nbsp;&nbsp;" . $this->lang["sidebar_comments_settings_author"] . "</p>\n"; 
        
        // length
        echo "<p><input type='text' size=5 name='length' value='" . $length . "' /> " . $this->lang["sidebar_comments_settings_length"] . "</p>\n";
        
        // number of comments
        echo "<p><input type='text' size=5 name='number' value='" . $number . "' /> " . $this->lang["sidebar_comments_settings_number"] . "</p>\n";
        
        $this->pluginHook('sidebar_comments_settings_form');
                        
        echo "<br /><br />\n";    
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $this->lang["sidebar_comments_settings_save"] . "' />\n";
        echo "<input type='hidden' name='token' value='" . $this->hotaru->token . "' />\n";
        echo "</form>\n";
    }
    
    
     /**
     * Save admin settings for sidebar_comments
     *
     * @return true
     */
    public function saveSettings()
    {
        $error = 0;
        
        // show avatars?
        if ($this->cage->post->keyExists('avatar')) { 
            $avatar = 'checked'; 
        } else { 
            $avatar = ''; 
        }
        
        // avatar size
        if ($this->cage->post->keyExists('avatar_size')) { 
            if ($this->cage->post->testInt('avatar_size')) { 
                $avatar_size = $this->cage->post->testInt('avatar_size');
            } else { 
                $avatar_size = 16; 
                $error = 1;
            }
        }
        
        // show authors?
        if ($this->cage->post->keyExists('author')) { 
            $author = 'checked'; 
        } else { 
            $author = ''; 
        }
        
        // length of comments
        if ($this->cage->post->keyExists('length')) { 
            if ($this->cage->post->testInt('length')) { 
                $length = $this->cage->post->testInt('length');
            } else { 
                $length = 100; 
                $error = 1;
            }
        }
        
        // number of comments
        if ($this->cage->post->keyExists('number')) { 
            if ($this->cage->post->testInt('number')) { 
                $number = $this->cage->post->testInt('number');
            } else { 
                $number = 10; 
                $error = 1;
            }
        }
        
        $this->pluginHook('sidebar_comments_save_settings');
                
        if ($error == 1)
        {
            $this->hotaru->message = $this->lang["sidebar_comments_settings_not_saved"];
            $this->hotaru->messageType = "red";
            $this->hotaru->showMessage();
            
            return false;
        } 
        else 
        {
            $sb_comments_settings['sidebar_comments_avatar'] = $avatar;
            $sb_comments_settings['sidebar_comments_avatar_size'] = $avatar_size;
            $sb_comments_settings['sidebar_comments_author'] = $author;
            $sb_comments_settings['sidebar_comments_length'] = $length;
            $sb_comments_settings['sidebar_comments_number'] = $number;
        
            $this->updateSetting('sidebar_comments_settings', serialize($sb_comments_settings));
            
            $this->hotaru->message = $this->lang["sidebar_comments_settings_saved"];
            $this->hotaru->messageType = "green";
            $this->hotaru->showMessage();
        
            return true;    
        }
    }
}
?>
