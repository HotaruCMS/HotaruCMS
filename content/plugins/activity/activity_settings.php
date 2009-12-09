<?php
/**
 * File: /plugins/activity/activity_settings.php
 * Purpose: Admin settings for the activity plugin
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

class ActivitySettings extends Activity
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
        
        echo "<h1>" . $this->lang["activity_settings_header"] . "</h1>\n";
          
        // Get settings from database if they exist...
        $activity_settings = $this->getSerializedSettings('activity');
                            
        $avatar = $activity_settings['activity_sidebar_avatar'];
        $avatar_size = $activity_settings['activity_sidebar_avatar_size'];
        $user = $activity_settings['activity_sidebar_user'];
        $sb_number = $activity_settings['activity_sidebar_number'];
        $pg_number = $activity_settings['activity_number'];
        $time = $activity_settings['activity_time'];
    
        $this->pluginHook('activity_settings_get_values');
        
        //...otherwise set to blank:
        if (!$avatar) { $avatar = ''; }
        if (!$avatar_size) { $avatar_size = 0; }
        if (!$user) { $user = ''; }
        if (!$sb_number) { $sb_number = 5; }
        if (!$pg_number) { $pg_number = 20; }
        if (!$time) { $time = ''; }
        
        echo "<form name='activity_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=activity' method='post'>\n";
        
        echo "<p>" . $this->lang["activity_settings_instructions"] . "</p><br />";
        
        // show avatars?
        echo "<p><input type='checkbox' name='avatar' value='avatar' " . $avatar . " >&nbsp;&nbsp;" . $this->lang["activity_settings_avatar"] . "</p>\n"; 
    
        // avatar size
        echo "<p><input type='text' size=5 name='avatar_size' value='" . $avatar_size . "' /> " . $this->lang["activity_settings_avatar_size"] . "</p>\n";
        
        // show users?
        echo "<p><input type='checkbox' name='user' value='user' " . $user . " >&nbsp;&nbsp;" . $this->lang["activity_settings_user"] . "</p>\n"; 
        
        // show time?
        echo "<p><input type='checkbox' name='time' value='time' " . $time . " >&nbsp;&nbsp;" . $this->lang["activity_settings_time"] . "</p>\n"; 
        
        // number of items in the sidebar
        echo "<p><input type='text' size=5 name='sb_number' value='" . $sb_number . "' /> " . $this->lang["activity_settings_sidebar_number"] . "</p>\n";
        
        // number of items on the activity page
        echo "<p><input type='text' size=5 name='pg_number' value='" . $pg_number . "' /> " . $this->lang["activity_settings_number"] . "</p>\n";
        
        $this->pluginHook('activity_settings_form');
                        
        echo "<br /><br />\n";    
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $this->lang["activity_settings_save"] . "' />\n";
        echo "<input type='hidden' name='token' value='" . $this->hotaru->token . "' />\n";
        echo "</form>\n";
    }
    
    
     /**
     * Save admin settings for activity_sidebar
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
        
        // show users?
        if ($this->cage->post->keyExists('user')) { 
            $user = 'checked'; 
        } else { 
            $user = ''; 
        }
        
        // show time?
        if ($this->cage->post->keyExists('time')) { 
            $time = 'checked'; 
        } else { 
            $time = ''; 
        }

        // number of items in the sidebar
        if ($this->cage->post->keyExists('sb_number')) { 
            if ($this->cage->post->testInt('sb_number')) { 
                $sb_number = $this->cage->post->testInt('sb_number');
            } else { 
                $sb_number = 10; $error = 1;
            }
        } else { 
            $sb_number = 10; $error = 1;
        }
        
        // number of items on the activity page
        if ($this->cage->post->keyExists('pg_number')) { 
            if ($this->cage->post->testInt('pg_number')) { 
                $pg_number = $this->cage->post->testInt('pg_number');
            } else { 
                $pg_number = 10; $error = 1;
            }
        } else { 
            $pg_number = 10; $error = 1;
        }
        
        $this->pluginHook('activity_save_settings');
                
        if ($error == 1)
        {
            $this->hotaru->message = $this->lang["activity_settings_not_saved"];
            $this->hotaru->messageType = "red";
            $this->hotaru->showMessage();
            
            return false;
        } 
        else 
        {
            $activity_settings['activity_sidebar_avatar'] = $avatar;
            $activity_settings['activity_sidebar_avatar_size'] = $avatar_size;
            $activity_settings['activity_sidebar_user'] = $user;
            $activity_settings['activity_sidebar_number'] = $sb_number;
            $activity_settings['activity_number'] = $pg_number;
            $activity_settings['activity_time'] = $time;
        
            $this->updateSetting('activity_settings', serialize($activity_settings));
            
            $this->hotaru->message = $this->lang["activity_settings_saved"];
            $this->hotaru->messageType = "green";
            $this->hotaru->showMessage();
        
            return true;    
        }
    }
}
?>