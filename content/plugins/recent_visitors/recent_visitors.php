<?php
/**
 * name: Recent Visitors
 * description: Show recent visitors in a widget
 * version: 0.1
 * folder: recent_visitors
 * class: RecentVisitors
 * requires: widgets 0.6, users 1.1
 * hooks: install_plugin, admin_plugin_settings, admin_sidebar_plugin_settings
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

class RecentVisitors
{
     /**
     * ********************************************************************* 
     * ********************* FUNCTIONS FOR POST CLASS ********************** 
     * *********************************************************************
     * ****************************************************************** */
     
    /**
     * Add a post_tags field to posts table if it doesn't alredy exist
     */
    public function install_plugin($h)
    {
        $recent_visitors_settings = $h->getSerializedSettings();
        
        if (!isset($recent_visitors_settings['visitors_num'])) { $recent_visitors_settings['visitors_num'] = 10; }
        if (!isset($recent_visitors_settings['visitors_list'])) { $recent_visitors_settings['visitors_list'] = ''; }
        if (!isset($recent_visitors_settings['visitors_avatars'])) { $recent_visitors_settings['visitors_avatars'] = ''; }
        if (!isset($recent_visitors_settings['visitors_avatar_size'])) { $recent_visitors_settings['visitors_avatar_size'] = '16'; }
        if (!isset($recent_visitors_settings['visitors_names'])) { $recent_visitors_settings['visitors_names'] = 'checked'; }
        if (!isset($recent_visitors_settings['visitors_widget_title'])) { $recent_visitors_settings['visitors_widget_title'] = 'checked'; }
        
        $h->updateSetting('recent_visitors_settings', serialize($recent_visitors_settings));

        // widget
        $h->addWidget('recent_visitors', 'recent_visitors', '');  // plugin name, function name, optional arguments
    }


    /**
     * Widget Recent Visitors
     */
    public function widget_recent_visitors($h)
    {
        $need_cache = false;
        $label = 'recent_visitors';
        
        // check for a cached version and use it if no recent update:
        $output = $h->smartCache('html', 'users', 10, '', $label);
        if ($output) {
            echo $output; return true;
        } else {
            $need_cache = true;
        }
        
        $recent_visitors_settings = $h->getSerializedSettings('recent_visitors');
        $limit = $recent_visitors_settings['visitors_num'];
        $list = $recent_visitors_settings['visitors_list'];
        $avatars = $recent_visitors_settings['visitors_avatars'];
        $avatar_size = $recent_visitors_settings['visitors_avatar_size'];
        $names = $recent_visitors_settings['visitors_names'];
        $show_title = $recent_visitors_settings['visitors_widget_title'];
        
        // build the recent visitors:
        $visitors = $this->getRecentVisitors($h, $limit);
        if (!$visitors) { return false; }
        
        $output = '';
        
        if ($show_title) {
            $output .="<h2 class='widget_head widget_recent_visitors_title'>";
            $output .=$h->lang["recent_visitors_widget_title"];
            $output .="</h2>\n";
        }
        
        // if using avatars, set them up here:
        if ($avatars) {
            $avatar = new Avatar($h);
            $avatar->size = $avatar_size;
            //$avatar->rating = "pg"; // optional - defaults to "g" if not used
        }
        
        $output .= "<div class='widget_body widget_recent_visitors'>";
        
        if ($list) { $output .="<ul class='recent_visitors_list'>\n"; } 
        
        foreach ($visitors as $visitor) 
        {
            if ($list) {
                $output .="<li class='recent_visitors_item'>";
            }
            
            if ($avatars) {
                $avatar->user_id = $visitor->user_id;
                $avatar->user_email = $visitor->user_email;
                $avatar->user_name = $visitor->user_username;
                $avatar->setVars($h);
                $output .= $avatar->linkAvatar($h) . " \n";
            }
            
            if ($names) {
                $output .="<a href='" . $h->url(array('user' => $visitor->user_username)) . "'>" . $visitor->user_username . "</a>\n";
            }
            
            if ($list) { $output .="</li>"; } else { $output .="&nbsp;"; }
        }
        if ($list) { $output .="</ul>"; }
        $output .="</div>";
        
        if ($need_cache) {
            $h->smartCache('html', 'users', 10, $output, $label); // make or rewrite the cache file
        }
        
        echo $output;
    }
    
    
    /**
     * Get Recent Visitors
     *
     * @param int $limit number of users to show
     * @return array
     */
    public function getRecentVisitors($h, $limit)
    {
        $sql = "SELECT user_id, user_username, user_email FROM " . TABLE_USERS . " ORDER BY user_lastvisit DESC LIMIT " . $limit;
        $visitors = $h->db->get_results($h->db->prepare($sql));
       
        if ($visitors) { return $visitors; } else {return false; }
    }

}
?>