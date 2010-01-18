<?php
/**
 * name: Who Voted
 * description: Show a list of who voted
 * version: 0.1
 * folder: who_voted
 * class: WhoVoted
 * requires: sb_base 0.1, vote 1.2
 * hooks: install_plugin, header_include, sb_base_show_post_middle, admin_plugin_settings, admin_sidebar_plugin_settings
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

class WhoVoted
{
    /**
     * Install settings
     */
    public function install_plugin($h)
    {
        $who_voted_settings = $h->getSerializedSettings();
        
        if (!isset($who_voted_settings['who_voted_num'])) { $who_voted_settings['who_voted_num'] = 0; }
        if (!isset($who_voted_settings['who_voted_avatars'])) { $who_voted_settings['who_voted_avatars'] = ''; }
        if (!isset($who_voted_settings['who_voted_avatar_size'])) { $who_voted_settings['who_voted_avatar_size'] = '16'; }
        if (!isset($who_voted_settings['who_voted_names'])) { $who_voted_settings['who_voted_names'] = 'checked'; }
        if (!isset($who_voted_settings['who_voted_widget_title'])) { $who_voted_settings['who_voted_widget_title'] = 'checked'; }
        
        $h->updateSetting('who_voted_settings', serialize($who_voted_settings));
    }
    
    
    /**
     * Show who voted on a post page
     */
    public function sb_base_show_post_middle($h)
    { 
        if ($h->isPage('submit3')) { return false; }
        
        echo $this->showWhoVoted($h);
    }

    
    /**
     * Show who voted
     */
    public function showWhoVoted($h)
    {
        $who_voted_settings = $h->getSerializedSettings();
        $limit = $who_voted_settings['who_voted_num'];
        $avatars = $who_voted_settings['who_voted_avatars'];
        $avatar_size = $who_voted_settings['who_voted_avatar_size'];
        $names = $who_voted_settings['who_voted_names'];
        $show_title = $who_voted_settings['who_voted_widget_title'];
        
        $results = $this->getWhoVoted($h, $limit);
        
        $output = '';
        
        if ($results) 
        {
            if ($show_title) {
                $output .= "<h2 id='who_voted_title'>" . $h->lang['who_voted'] . "</h2>";
            }
        
            $output .= "<div id='who_voted_content'>\n";
            foreach ($results as $item) {
                $h->setAvatar($item->user_id, $avatar_size);
                if ($avatars) {
                    $output .= $h->linkAvatar(); 
                }
                if ($names) {
                    $output .="<a href='" . $h->url(array('user' => $item->user_username)) . "'>" . $item->user_username . "</a> &nbsp;\n";
                }
            }
            $output .= "</div>\n";
        }
        else 
        {
            // Show "No other posts found with matching tags"
            $output = "<div id='who_voted_content'>\n";
            $output .= $h->lang['who_voted_no_results'];
            $output .= "</div>\n";
        }

        return $output;
    }
    
    /**
     * Get related results from the database
     *
     * return array|false
     */
    public function getWhoVoted($h, $limit)
    {
        if ($limit) { $limit_text = " LIMIT " . $limit; } else { $limit_text = ''; }
        
        $sql = "SELECT " . TABLE_USERS . ".user_id, " . TABLE_USERS . ".user_username, " . TABLE_POSTVOTES . ".vote_user_id FROM " . TABLE_USERS . ", " . TABLE_POSTVOTES . " WHERE (" . TABLE_USERS . ".user_id = " . TABLE_POSTVOTES . ".vote_user_id) AND (" . TABLE_POSTVOTES . ".vote_rating > %d) AND (" . TABLE_POSTVOTES . ".vote_post_id = %d) ORDER BY " . TABLE_POSTVOTES . ".vote_date ASC" . $limit_text;
        $results = $h->db->get_results($h->db->prepare($sql, 0, $h->post->id));
        
        return $results;
    }

}
?>