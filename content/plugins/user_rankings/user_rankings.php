<?php
/**
 * name: User Rankings
 * description: List your site's top users
 * version: 0.1
 * folder: user_rankings
 * class: UserRankings
 * requires: activity 0.7
 * hooks: install_plugin, header_include, admin_sidebar_plugin_settings, admin_plugin_settings, theme_index_top, theme_index_main
 * author: Nick Ramsay
 * authorurl: http://hotarucms.org/member.php?1-Nick
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


class UserRankings
{
    /**
     *  Add default settings for Sidebar Comments plugin on installation
     */
    public function install_plugin($h)
    {
        // Default settings
        $user_rankings_settings = $h->getSerializedSettings();
        
        if ($h->isActive('avatar')) {
            if (!isset($user_rankings_settings['show_avatar'])) { $user_rankings_settings['show_avatar'] = "checked"; }
        } else {
            if (!isset($user_rankings_settings['show_avatar'])) { $user_rankings_settings['show_avatar'] = ""; }
        }
        if (!isset($user_rankings_settings['time_period_days'])) { $user_rankings_settings['time_period_days'] = 30; }
        if (!isset($user_rankings_settings['avatar_size_widget'])) { $user_rankings_settings['avatar_size_widget'] = 16; }
        if (!isset($user_rankings_settings['avatar_size_page'])) { $user_rankings_settings['avatar_size_page'] = 16; }
        if (!isset($user_rankings_settings['show_name'])) { $user_rankings_settings['show_name'] = 'checked'; }
        if (!isset($user_rankings_settings['show_points'])) { $user_rankings_settings['show_points'] = "checked"; }
        if (!isset($user_rankings_settings['widget_number'])) { $user_rankings_settings['widget_number'] = 10; }
        if (!isset($user_rankings_settings['page_number'])) { $user_rankings_settings['page_number'] = 20; }
        if (!isset($user_rankings_settings['points_post'])) { $user_rankings_settings['points_post'] = 100; }
        if (!isset($user_rankings_settings['points_comment'])) { $user_rankings_settings['points_comment'] = 50; }
        if (!isset($user_rankings_settings['points_vote'])) { $user_rankings_settings['points_vote'] = 20; }
        if (!isset($user_rankings_settings['cache_duration'])) { $user_rankings_settings['cache_duration'] = 240; } // 12 hours
        
        $h->updateSetting('user_rankings_settings', serialize($user_rankings_settings));
        
        // widget
        $h->addWidget('user_rankings', 'user_rankings', '');  // plugin name, function name, optional arguments
    }
    
    
    /**
     *  Set up the User Rankings page
     */
    public function theme_index_top($h)
    {
        if ($h->pageName != 'user_rankings') { return false; }
        
        $h->pageTitle = $h->lang["user_rankings_title"];
        $h->pageType = 'rankings';
    }


    /**
     *  Display the User Rankings page
     */
    public function theme_index_main($h)
    {
        if ($h->pageName != 'user_rankings') { return false; }

        $h->displayTemplate('user_rankings_page');
        return true;
    }
    
    
    /**
     * Display the latest user rankings in a widget block
     */
    public function widget_user_rankings($h)
    {
        // build link that will link the widget title to user_rankings page...
        $anchor_title = sanitize($h->lang["user_rankings_title_anchor_title"], 'ents');
        $title = "<a href='" . $h->url(array('page'=>'user_rankings')) . "' title='" . $anchor_title . "'>";
        $title .= $h->lang['user_rankings_widget_title'] . "</a>";

        $output = "<h2 class='widget_head user_rankings_widget_title'>\n";
        $link = BASEURL;
        $output .= $title;
        $output .= $h->lang['user_rankings_widget_subtitle'];
        $output .= "</h2>\n"; 
            
        $output .= "<ul class='widget_body user_rankings_widget_items'>\n";
        
        $output .= $this->displayUserRankings($h, true); // 'widget' = true
        $output .= "</ul>\n\n";
        
        // Display the whole thing:
        if (isset($output) && $output != '') { echo $output; }
    }
    
    
    /**
     * Get user rankings <li> items
     *
     * @param array $users
     * @param bool $widget
     * return string $output
     */
    public function displayUserRankings($h, $widget = false)
    {
        // get settings from the database
        $ur_settings = $h->getSerializedSettings('user_rankings');
        if (!$ur_settings) { return false; }
        
        if ($widget) { 
            $limit = "widget_number";
            $css = 'widget';
        } else { 
            $limit = "page_number";
            $css = 'page';
        }
        
        $need_cache = false;
        $label = 'user_rankings_' . $css;
        
        // check for a cached version and use it if no recent update:
        $output = $h->cacheHTML($ur_settings['cache_duration'], '', $label);
        if ($output) {
            return $output;
        } else {
            $need_cache = true;
        }

        // get all users with activity in the last X days, ordered by points
        $users = $this->generateUserRankings($h);
        if (!$users) { return false; }
        
        $output = '';
        $i = 0;
        foreach ($users as $id => $points)
        {
            $user = new UserAuth();
            $user->getUserBasic($h, $id);

            if (!$user->id) { continue; } // i.e. if this user doesn't exist anymore, skip to the next one
            
            $i++;
            $output .= "<li class='user_rankings_" . $css . "_item user_rankings_clearfix'>\n";
            
            if ($ur_settings['show_avatar'] && $h->isActive('avatar')) {
                $size = 'avatar_size_' . $css;
                $h->setAvatar($user->id, $ur_settings[$size]);
                $output .= "<div class='user_rankings_" . $css . "_avatar'>\n";
                $output .= $h->linkAvatar();
                $output .= "</div> \n";
            }
            
            if ($ur_settings['show_name']) {
                $output .= "<a class='user_rankings_" . $css . "_name' href='" . $h->url(array('user' => $user->name)) . "'>" . $user->name . "</a> \n";
            }
            
            $output .= "<div class='user_rankings_" . $css . "_points'>" . $points . "</div>\n";
            $output .= "</li>\n\n";

            if ($i >= $ur_settings[$limit]) { break; }
        }
        
        if ($need_cache) {
            $h->cacheHTML($ur_settings['cache_duration'], $output, $label); // make or rewrite the cache file
        }
        
        return $output;
    }
    
    
    /**
     *  Generate User Rankings
     */
    public function generateUserRankings($h)
    {
        // get settings from the database
        $ur_settings = $h->getSerializedSettings('user_rankings');
        if (!$ur_settings) { return false; }
        
        $time_ago = "- " . $ur_settings['time_period_days'] . " Days";
        $time_ago = date('YmdHis', strtotime($time_ago));
        $sql = "SELECT useract_userid, useract_key FROM " . TABLE_USERACTIVITY . " WHERE useract_archived = %s AND useract_status = %s AND useract_date > %s";
        $query = $h->db->prepare($sql, 'N', 'show', $time_ago);
        $results = $h->db->get_results($query);
        
        if (!$results) { return false; }
        
        foreach ($results as $action)
        {
            if (!isset($user[$action->useract_userid])) { $user[$action->useract_userid] = 0; } // zero points to start with
            
            switch ($action->useract_key)
            {
                case 'vote':
                    $user[$action->useract_userid] += $ur_settings['points_vote'];
                    break;
                case 'comment':
                    $user[$action->useract_userid] += $ur_settings['points_comment'];
                    break;
                case 'post':
                    $user[$action->useract_userid] += $ur_settings['points_post'];
                    break;
            }
        }
        
        // order by points - highest first
        arsort($user);
        $ordered_users = $user;
        
        return $ordered_users;
    }
}