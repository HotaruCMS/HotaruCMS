<?php
/**
 * name: Gravatar
 * description: Enables Gravatar avatars for users
 * version: 0.6
 * folder: gravatar
 * class: Gravatar
 * requires: users 0.5, submit 0.7
 * hooks: install_plugin, hotaru_header, header_include, submit_show_post_pre_title, show_comments_avatar
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

class Gravatar extends PluginFunctions
{
    /**
     * Default settings on install
     */
    public function install_plugin()
    {
        // !!! EDIT HERE !!!
        
        $size = 32;     // 1 ~ 512px;
        $rating = "g";  // g, pg, r or x
        
        // !!! STOP EDITING HERE !!!
        
        $this->updateSetting('gravatar_size', $size);
        $this->updateSetting('gravatar_rating', $rating);
    }
    
    
    /**
     * Default settings on install
     */
    public function hotaru_header()
    {
        // Get settings from database if they exist...
        $this->hotaru->vars['gravatar_size'] = $this->getSetting('gravatar_size');
        $this->hotaru->vars['gravatar_rating'] = $this->getSetting('gravatar_rating');
        
    }
    
    
    /**
     * Show gravatar in posts
     */
    public function submit_show_post_pre_title()
    {

        $user = new UserBase($this->hotaru);
        $user->getUserBasic($this->hotaru->post->author);
        $email = $user->email;
        
        echo "<div class='show_post_gravatar'>";
        $this->showGravatarLink($user->name, $email);
        echo "</div>";
    }
    
    
    /**
     * Show gravatar in comments
     */
    public function show_comments_avatar()
    {
        $sql = "SELECT user_username, user_email FROM " . TABLE_USERS . " WHERE user_id = %d";
        $commenter = $this->db->get_row($this->db->prepare($sql, $this->hotaru->comment->author));
        $size = $this->hotaru->vars['gravatar_size'];
        $rating = $this->hotaru->vars['gravatar_rating'];
        
        echo "<div class='show_comments_gravatar'>";
        $this->showGravatarLink($commenter->user_username, $commenter->user_email);
        echo "</div>";
    }
    
    
    /**
     * Show Gravatar link
     *
     * @param string $username - user to link to
     * @param string $email - email of avatar user
     * @return string $output optional
     */
    public function showGravatarLink($username, $email, $return = false)
    {
        $output = "<a href='" . $this->hotaru->url(array('user' => $username)) . "' title='" . $username . "'>";
        $output .= $this->buildGravatarImage($email);
        $output .= "</a>";
        
        if ($return == true) { return $output; } else { echo $output; }
    }
    
    
    /**
     * Build Gravatar image
     *
     * @param string $email - email of avatar user
     * @param int $size - size (1 ~ 512 pixels)
     * @param string $rating - g, pg, r or x
     * @return string - html for image
     */
    public function buildGravatarImage($email)
    {
        $default = BASEURL . "content/plugins/gravatar/images/default_32.png";
        
        $grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=".md5( strtolower($email) ).
            "&default=".urlencode($default).
            "&size=" . $this->hotaru->vars['gravatar_size'] . 
            "&r=" . $this->hotaru->vars['gravatar_rating'];
            
        $img_url = "<img class='gravatar' src='" . $grav_url . "'>";
        
        return $img_url;
    }

}

?>