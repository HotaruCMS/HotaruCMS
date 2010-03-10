<?php
/**
 * File: /plugins/user_rankings/user_rankings_settings.php
 * Purpose: Admin settings for the User Rankings plugin
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

class UserRankingsSettings
{
     /**
     * Admin settings for User Rankings
     */
    public function settings($h)
    {
        // If the form has been submitted, go and save the data...
        if ($h->cage->post->getAlpha('submitted') == 'true') { 
            $this->saveSettings($h); 
        }
        
        echo "<h1>" . $h->lang["user_rankings_header"] . "</h1>\n";
          
        // Get settings from database if they exist...
        $user_rankings = $h->getSerializedSettings('user_rankings');

        $time_period_days = $user_rankings['time_period_days'];
        $points_post = $user_rankings['points_post'];
        $points_comment = $user_rankings['points_comment'];
        $points_vote = $user_rankings['points_vote'];
        $show_avatar = $user_rankings['show_avatar'];
        $avatar_size_widget = $user_rankings['avatar_size_widget'];
        $avatar_size_page = $user_rankings['avatar_size_page'];
        $show_name = $user_rankings['show_name'];
        $widget_number = $user_rankings['widget_number'];
        $page_number = $user_rankings['page_number'];
        $show_points = $user_rankings['show_points'];
        $points_post = $user_rankings['points_post'];
        $points_comment = $user_rankings['points_comment'];
        $points_vote = $user_rankings['points_vote'];
        $cache_duration = $user_rankings['cache_duration'];
        
        $h->pluginHook('user_rankings_get_values');
        
        //...otherwise set to blank:
        if (!$time_period_days) { $time_period_days = 30; }
        if (!$points_post) { $points_post = 100; }
        if (!$points_comment) { $points_comment = 50; }
        if (!$points_vote) { $points_vote = 20; }
        if (!$show_avatar) { $show_avatar = ''; }
        if (!$avatar_size_widget) { $avatar_size_widget = 16; }
        if (!$avatar_size_page) { $avatar_size_page = 16; }
        if (!$show_name) { $show_name = 'checked'; }
        if (!$widget_number) { $widget_number = 10; }
        if (!$page_number) { $page_number = 20; }
        if (!$show_points) { $show_points = 'checked'; }
        if (!$points_post) { $points_post = 100; }
        if (!$points_comment) { $points_comment = 50; }
        if (!$points_vote) { $points_vote = 20; }
        if (!$cache_duration) { $cache_duration = 240; } // 4 hours
        
        echo "<form name='user_rankings_form' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=user_rankings' method='post'>\n";
        
        // number of days of activity
        echo "<p><input type='text' size=5 name='time_period_days' value='" . $time_period_days . "' /> " . $h->lang["user_rankings_time_period_days"] . "</p>\n";
        
        // points per post
        echo "<p><input type='text' size=5 name='points_post' value='" . $points_post . "' /> " . $h->lang["user_rankings_points_post"] . "</p>\n";
        
        // points per comments
        echo "<p><input type='text' size=5 name='points_comment' value='" . $points_comment . "' /> " . $h->lang["user_rankings_points_comment"] . "</p>\n";
        
        // points per vote
        echo "<p><input type='text' size=5 name='points_vote' value='" . $points_vote . "' /> " . $h->lang["user_rankings_points_vote"] . "</p>\n";
        
        // number of items on the activity page
        echo "<p><input type='text' size=5 name='page_number' value='" . $page_number . "' /> " . $h->lang["user_rankings_page_number"] . "</p>\n";
        
        // number of items in the widget
        echo "<p><input type='text' size=5 name='widget_number' value='" . $widget_number . "' /> " . $h->lang["user_rankings_widget_number"] . "</p>\n";
        
        // show avatars?
        echo "<p><input type='checkbox' name='show_avatar' value='show_avatar' " . $show_avatar . " >&nbsp;&nbsp;" . $h->lang["user_rankings_show_avatar"] . "</p>\n"; 
    
        // avatar size widget
        echo "<p>&nbsp;&nbsp;<input type='text' size=5 name='avatar_size_widget' value='" . $avatar_size_widget . "' /> " . $h->lang["user_rankings_avatar_size_widget"] . "</p>\n";
        
        // avatar size page
        echo "<p>&nbsp;&nbsp;<input type='text' size=5 name='avatar_size_page' value='" . $avatar_size_page . "' /> " . $h->lang["user_rankings_avatar_size_page"] . "</p>\n";
        
        // show users?
        echo "<p><input type='checkbox' name='show_name' value='show_name' " . $show_name . " >&nbsp;&nbsp;" . $h->lang["user_rankings_show_name"] . "</p>\n"; 
        
        // show points?
        echo "<p><input type='checkbox' name='show_points' value='show_points' " . $show_points . " >&nbsp;&nbsp;" . $h->lang["user_rankings_show_points"] . "</p>\n"; 
        
        // cache duration
        echo "<p><input type='text' size=5 name='cache_duration' value='" . $cache_duration . "' /> " . $h->lang["user_rankings_cache_duration"] . "</p>\n";
        
        $h->pluginHook('user_rankings_form');
                        
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
        // Get settings from database if they exist...
        $user_rankings = $h->getSerializedSettings('user_rankings');
        
        // days activity
        $user_rankings['time_period_days'] = $h->cage->post->testInt('time_period_days');
        if (!$user_rankings['time_period_days']) {
            $time_period_days = 30; 
            $h->messages[$h->lang['user_rankings_time_period_days_error']] = 'red';
        }
        
        // points per post
        $user_rankings['points_post'] = $h->cage->post->testInt('points_post');
        if (!$user_rankings['points_post']) {
            $points_post = 100; 
            $h->messages[$h->lang['user_rankings_points_post_error']] = 'red';
        }
        
        // points per post
        $user_rankings['points_comment'] = $h->cage->post->testInt('points_comment');
        if (!$user_rankings['points_comment']) {
            $points_post = 100; 
            $h->messages[$h->lang['user_rankings_points_comment_error']] = 'red';;
        }
        
        // points per vote
        $user_rankings['points_vote'] = $h->cage->post->testInt('points_vote');
        if (!$user_rankings['points_vote']) {
            $points_post = 20; 
            $h->messages[$h->lang['user_rankings_points_vote_error']] = 'red';
        }
        
        // show avatars?
        if ($h->cage->post->keyExists('show_avatar')) { 
            $show_avatar = 'checked'; 
        } else { 
            $show_avatar = '';
        }
        
        // avatar size widget
        $user_rankings['avatar_size_widget'] = $h->cage->post->testInt('avatar_size_widget');
        if (!$user_rankings['avatar_size_widget']) {
            $user_rankings['avatar_size_widget'] = 16;
            $h->messages[$h->lang['user_rankings_avatar_size_widget_error']] = 'red';
        }
        
        // avatar size
        $user_rankings['avatar_size_page'] = $h->cage->post->testInt('avatar_size_page');
        if (!$user_rankings['avatar_size_page']) {
            $user_rankings['avatar_size_page'] = 16;
            $h->messages[$h->lang['user_rankings_avatar_size_page_error']] = 'red';
        }
        
        // show names?
        if ($h->cage->post->keyExists('show_name')) { 
            $show_name = 'checked'; 
        } else { 
            $show_name = ''; 
        }
        
        // show points?
        if ($h->cage->post->keyExists('show_points')) { 
            $show_points = 'checked'; 
        } else { 
            $show_points = ''; 
        }

        // number of items in the widget
        $user_rankings['widget_number'] = $h->cage->post->testInt('widget_number');
        if (!$user_rankings['widget_number']) {
            $user_rankings['widget_number'] = 10;
            $h->messages[$h->lang['user_rankings_widget_number_error']] = 'red';
        }
        
        // number of items on the user rankings page
        $user_rankings['page_number'] = $h->cage->post->testInt('page_number');
        if (!$user_rankings['page_number']) {
            $user_rankings['page_number'] = 20;
            $h->messages[$h->lang['user_rankings_page_number_error']] = 'red';
        }
        
        // cache duration
        $user_rankings['cache_duration'] = $h->cage->post->testInt('cache_duration');
        if (!$user_rankings['cache_duration']) {
            $points_post = 240; // 4 hours 
            $h->messages[$h->lang['user_rankings_cache_duration_error']] = 'red';
        }
        
        $h->pluginHook('user_rankings_save_settings');
        

        $user_rankings['time_period_days'] = $user_rankings['time_period_days'];
        $user_rankings['points_post'] = $user_rankings['points_post'];
        $user_rankings['points_comment'] = $user_rankings['points_comment'];
        $user_rankings['points_vote'] = $user_rankings['points_vote'];
        $user_rankings['show_avatar'] = $show_avatar;
        $user_rankings['avatar_size_widget'] = $user_rankings['avatar_size_widget'];
        $user_rankings['avatar_size_page'] = $user_rankings['avatar_size_page'];
        $user_rankings['show_name'] = $show_name;
        $user_rankings['widget_number'] = $user_rankings['widget_number'];
        $user_rankings['page_number'] = $user_rankings['page_number'];
        $user_rankings['show_points'] = $show_points;
        $user_rankings['cache_duration'] = $user_rankings['cache_duration'];
    
        $h->updateSetting('user_rankings_settings', serialize($user_rankings));
        
        $h->messages[$h->lang["main_settings_saved"]] = 'green';
        $h->showMessages();
    
        return true;
    }
}
?>