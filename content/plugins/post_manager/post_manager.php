<?php
/**
 * name: Post Manager
 * description: Manage posts.
 * version: 0.6
 * folder: post_manager
 * class: PostManager
 * requires: submit 1.9
 * hooks: hotaru_header, install_plugin, admin_header_include, admin_plugin_settings, admin_sidebar_plugin_settings, user_manager_role, user_manager_details
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
 
class PostManager
{
    // Most work is done in post_manager_settings.php
    
    /**
     * Adds an icon in User Manager about the user having pending or buried posts
     */
    public function user_manager_role($h)
    {
        list ($icons, $user_role, $user) = $h->vars['user_manager_role'];
        
        // Check to see if this user has any pending or buried posts:
        $sql = "SELECT post_id, post_status FROM " . TABLE_POSTS . " WHERE post_author = %d AND (post_status = %s OR post_status = %s) ORDER BY post_date DESC";
        $flags = $h->db->get_results($h->db->prepare($sql, $user->user_id, 'pending', 'buried'));
        $h->vars['post_manager_flags'] = $flags;
        
        if ($flags) {
            $unique_array = array();
            $title = $h->lang["post_man_flagged_reasons"];
            foreach ($flags as $flag) {
                if (!in_array($flag->post_status, $unique_array)) {
                    $title .= $flag->post_status . ", ";
                    array_push($unique_array, $flag->post_status);
                }
            }
            $title = rstrtrim($title, ", ");
            $icons .= " <img src = '" . BASEURL . "content/plugins/user_manager/images/flag_red.png' title='" . $title . "'>";
            $h->vars['user_manager_role'] = array($icons, $user_role, $user);
        }
    }
    
    
    /**
     * Adds a note in User Manager about the user having pending or buried posts
     */
    public function user_manager_details($h)
    {
        list ($output, $user) = $h->vars['user_manager_details'];
        
        // Check to see if this user has any pending or buried posts:
        $sql = "SELECT post_id, post_status FROM " . TABLE_POSTS . " WHERE post_author = %d AND (post_status = %s OR post_status = %s) ORDER BY post_date DESC";
        
        if (!isset($h->vars['post_manager_flags'])) {
            $flags = $h->db->get_results($h->db->prepare($sql, $user->user_id, 'pending', 'buried'));
        } else {
            $flags = $h->vars['post_manager_flags']; // retrieve from memory
        }
        
        if ($flags) {
            $output .= "<br /><b>" . $h->lang["post_man_flagged_reasons"] . "</b>";
            foreach ($flags as $flag) {
                $h->readPost($flag->post_id);
                $output .= "<a href='" . $h->url(array('page'=>$flag->post_id)) . "' title='" . $h->lang["post_man_flags_title"] . $h->post->title . "'>" . $flag->post_status . "</a>, ";
            }
            $output = rstrtrim($output, ", ");
            $h->vars['user_manager_details'] = array($output, $user);
        }
    }
}

?>