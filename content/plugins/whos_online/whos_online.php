<?php
/**
 * name: Who's Online
 * description: Show who's online
 * version: 0.1
 * folder: whos_online
 * class: WhosOnline
 * requires: widgets 0.6, users 1.1
 * hooks: install_plugin, header_include, admin_plugin_settings, admin_sidebar_plugin_settings, userauth_checkcookie_success, 
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

class WhosOnline
{
     /**
     * ********************************************************************* 
     * ********************* FUNCTIONS FOR POST CLASS ********************** 
     * *********************************************************************
     * ****************************************************************** */
     
    /**
     * Install widget settings
     */
    public function install_plugin($h)
    {
        $whos_online_settings = $h->getSerializedSettings();
        
        if (!isset($whos_online_settings['online_num'])) { $whos_online_settings['online_num'] = 10; }
        if (!isset($whos_online_settings['online_list'])) { $whos_online_settings['online_list'] = ''; }
        if (!isset($whos_online_settings['online_avatars'])) { $whos_online_settings['online_avatars'] = ''; }
        if (!isset($whos_online_settings['online_avatar_size'])) { $whos_online_settings['online_avatar_size'] = '16'; }
        if (!isset($whos_online_settings['online_names'])) { $whos_online_settings['online_names'] = 'checked'; }
        if (!isset($whos_online_settings['online_widget_title'])) { $whos_online_settings['online_widget_title'] = 'checked'; }
        
        $h->updateSetting('whos_online_settings', serialize($whos_online_settings));

        // widget
        $h->addWidget('whos_online', 'whos_online', '');  // plugin name, function name, optional arguments
    }
    
    
    /**
     * Update last activity
     * This only keeps last activity records for the last 5 minutes and only one per user.
     *
     * @return bool
     */
    public function userauth_checkcookie_success($h)
    {
        if ($h->currentUser->id != 0) {
            // delete all user activity older than 5 minutes and any activity by this user
            $time = date('Y-m-d H:i:s', strtotime("-5 minutes"));
            $sql = "DELETE FROM " . TABLE_USERMETA . " WHERE (usermeta_key = %s AND usermeta_value < %s) OR (usermeta_userid = %d AND usermeta_key = %s)";
            $h->db->query($h->db->prepare($sql, 'last_activity', $time, $h->currentUser->id, 'last_activity'));
            
            // insert current time for this user
            $sql = "INSERT INTO " . TABLE_USERMETA . " SET usermeta_userid = %d, usermeta_key = %s, usermeta_value = CURRENT_TIMESTAMP";
            $h->db->query($h->db->prepare($sql, $h->currentUser->id, 'last_activity'));
            return true;
        } else {
            return false;
        }
    }


    /**
     * Widget Who's Online
     */
    public function widget_whos_online($h)
    {
        $whos_online_settings = $h->getSerializedSettings('whos_online');
        $limit = $whos_online_settings['online_num'];
        $list = $whos_online_settings['online_list'];
        $avatars = $whos_online_settings['online_avatars'];
        $avatar_size = $whos_online_settings['online_avatar_size'];
        $names = $whos_online_settings['online_names'];
        $show_title = $whos_online_settings['online_widget_title'];
        
        // build the who's online:
        $members = $this->getOnlineMembers($h, $limit);
        $guests = $this->getOnlineGuests($h);
        
        if ($show_title) {
            echo "<h2 class='widget_head widget_whos_online_title'>";
            echo $h->lang["whos_online_widget_title"];
            echo "</h2>\n";
        }
        
        echo "<div class='widget_body widget_whos_online'>";
        
        echo "<div id='whos_online_counts'>" . $h->lang["whos_online_currently"] . count($members) . " " . $h->lang['whos_online_members'] . " " . $guests . " " . $h->lang['whos_online_guests'] . "</div>";
        
        $need_cache = false;
        $label = 'whos_online';
        
        // check for a cached version and use it if no recent update:
        $output = $h->smartCache('html', 'usermeta', 10, '', $label);
        
        if ($output) {
            echo $output;
            echo "</div>"; return true;
        } else {
            $output = "";
            $need_cache = true;
        }
        
        if ($list) { $output .="<ul class='whos_online_list'>\n"; } 
        
        if ($members) {
        foreach ($members as $member) 
            {
                $userid = $member->usermeta_userid;
                $username = $h->getUserNameFromId($userid);
                
                if ($list) {
                    $output .="<li class='whos_online_item'>";
                }
                
                if ($avatars) {
                    $h->setAvatar($userid, $avatar_size);
                    $output .= $h->linkAvatar();
                }
                
                if ($names) {
                    $output .="<a href='" . $h->url(array('user' => $username)) . "'>" . $username . "</a>\n";
                }
                
                if ($list) { $output .="</li>"; } else { $output .="&nbsp;"; }
            }
        }
        
        if ($list) { $output .="</ul>"; }
        
        if ($need_cache) {
            $h->smartCache('html', 'users', 10, $output, $label); // make or rewrite the cache file
        }
        
        echo $output;
        
        echo "</div>\n";
    }
    
    
    /**
     * Get Online Members
     * Returns active members within the last 5 minutes (i.e. online now)
     * Anything older than 5 minutes would have already been deleted in userauth_checkcookie_success()
     *
     * @param int $limit number of users to show
     * @return array
     */
    public function getOnlineMembers($h, $limit)
    {
        $sql = "SELECT usermeta_userid FROM " . TABLE_USERMETA . " WHERE usermeta_key = %s ORDER BY usermeta_value DESC LIMIT " . $limit;
        $members = $h->db->get_results($h->db->prepare($sql, 'last_activity'));
       
        if ($members) { return $members; } else {return false; }
    }
    
    
    /**
     * Count online guests
     *
     * @link http://www.devarticles.com/c/a/PHP/The-Quickest-Way-To-Count-Users-Online-With-PHP/1/
     * @return array
     */
    public function getOnlineGuests($h)
    {
        /* Define how long the maximum amount of time the session can be inactive. */
        define("MAX_IDLE_TIME", 20);
        
        if ( $directory_handle = opendir( session_save_path() ) ) { 
            $count = 0;
            
            while ( false !== ( $file = readdir( $directory_handle ) ) ) {
                if($file != '.' && $file != '..') {
                    // Comment the 'if(...){' and '}' lines if you get a significant amount of traffic
                    // if(time()- fileatime(session_save_path() . '\\' . $file) < MAX_IDLE_TIME * 60) {
                        $count++;
                    // }
                }
            }
            closedir($directory_handle);
        }
        
        return $count;
    } 

}
?>