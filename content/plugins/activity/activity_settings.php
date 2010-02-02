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

class ActivitySettings
{
     /**
     * Admin settings for Activity
     */
    public function settings($h)
    {
        // If the form has been submitted, go and save the data...
        if ($h->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings($h); 
        }
        
        echo "<h1>" . $h->lang["activity_settings_header"] . "</h1>\n";
          
        // Get settings from database if they exist...
        $activity_settings = $h->getSerializedSettings('activity');
                            
        $avatar = $activity_settings['widget_avatar'];
        $avatar_size = $activity_settings['widget_avatar_size'];
        $user = $activity_settings['widget_user'];
        $widget_number = $activity_settings['widget_number'];
        $pg_number = $activity_settings['number'];
        $time = $activity_settings['time'];
    
        $h->pluginHook('activity_settings_get_values');
        
        //...otherwise set to blank:
        if (!$avatar) { $avatar = ''; }
        if (!$avatar_size) { $avatar_size = 0; }
        if (!$user) { $user = ''; }
        if (!$widget_number) { $widget_number = 5; }
        if (!$pg_number) { $pg_number = 20; }
        if (!$time) { $time = ''; }
        
        echo "<form name='activity_settings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=activity' method='post'>\n";
        
        // number of items on the activity page
        echo "<p><input type='text' size=5 name='pg_number' value='" . $pg_number . "' /> " . $h->lang["activity_settings_number"] . "</p>\n";
        
        echo "<br /><p>" . $h->lang["activity_settings_instructions"] . "</p>";
        
        // show avatars?
        echo "<p><input type='checkbox' name='avatar' value='avatar' " . $avatar . " >&nbsp;&nbsp;" . $h->lang["activity_settings_avatar"] . "</p>\n"; 
    
        // avatar size
        echo "<p><input type='text' size=5 name='avatar_size' value='" . $avatar_size . "' /> " . $h->lang["activity_settings_avatar_size"] . "</p>\n";
        
        // show users?
        echo "<p><input type='checkbox' name='user' value='user' " . $user . " >&nbsp;&nbsp;" . $h->lang["activity_settings_user"] . "</p>\n"; 
        
        // show time?
        echo "<p><input type='checkbox' name='time' value='time' " . $time . " >&nbsp;&nbsp;" . $h->lang["activity_settings_time"] . "</p>\n"; 
        
        // number of items in the widget
        echo "<p><input type='text' size=5 name='widget_number' value='" . $widget_number . "' /> " . $h->lang["activity_settings_widget_number"] . "</p>\n";
        
        $h->pluginHook('activity_settings_form');
                        
        echo "<br /><br />\n";    
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='submit' value='" . $h->lang["main_form_save"] . "' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form>\n";
    }
    
    
     /**
     * Save admin settings for activity_widget
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
        
        // show users?
        if ($h->cage->post->keyExists('user')) { 
            $user = 'checked'; 
        } else { 
            $user = ''; 
        }
        
        // show time?
        if ($h->cage->post->keyExists('time')) { 
            $time = 'checked'; 
        } else { 
            $time = ''; 
        }

        // number of items in the widget
        if ($h->cage->post->keyExists('widget_number')) { 
            if ($h->cage->post->testInt('widget_number')) { 
                $widget_number = $h->cage->post->testInt('widget_number');
            } else { 
                $widget_number = 10; $error = 1;
            }
        } else { 
            $widget_number = 10; $error = 1;
        }
        
        // number of items on the activity page
        if ($h->cage->post->keyExists('pg_number')) { 
            if ($h->cage->post->testInt('pg_number')) { 
                $pg_number = $h->cage->post->testInt('pg_number');
            } else { 
                $pg_number = 10; $error = 1;
            }
        } else { 
            $pg_number = 10; $error = 1;
        }
        
        $h->pluginHook('activity_save_settings');
                
        if ($error == 1)
        {
            $h->message = $h->lang["activity_settings_not_saved"];
            $h->messageType = "red";
            $h->showMessage();
            
            return false;
        } 
        else 
        {
            $activity_settings['widget_avatar'] = $avatar;
            $activity_settings['widget_avatar_size'] = $avatar_size;
            $activity_settings['widget_user'] = $user;
            $activity_settings['widget_number'] = $widget_number;
            $activity_settings['number'] = $pg_number;
            $activity_settings['time'] = $time;
        
            $h->updateSetting('activity_settings', serialize($activity_settings));
            
            $h->message = $h->lang["main_settings_saved"];
            $h->messageType = "green";
            $h->showMessage();
        
            return true;    
        }
    }
}
?>