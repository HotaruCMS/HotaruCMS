<?php
/**
 * name: Gravatar
 * description: Enables Gravatar avatars for users
 * version: 0.4
 * folder: gravatar
 * class: Gravatar
 * requires: users 0.5, submit 0.7
 * hooks: header_include, submit_show_post_pre_title, show_comments_avatar
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
     * Show gravatar in posts
     */
    public function submit_show_post_pre_title()
    {

        $user = new UserBase($this->hotaru);
        $user->getUserBasic($this->hotaru->post->author);
        $email = $user->email;
        $size = 32;
        
        $this->showGravatarLink($user->name, $email, $size);
    }
    
    
    /**
     * Show gravatar in comments
     */
    public function show_comments_avatar()
    {
        $sql = "SELECT user_username, user_email FROM " . TABLE_USERS . " WHERE user_id = %d";
        $commenter = $this->db->get_row($this->db->prepare($sql, $this->hotaru->comment->author));
        $size = 32;
        
        $this->showGravatarLink($commenter->user_username, $commenter->user_email, $size);
    }
    
    
    /**
     * Show Gravatar link
     *
     * @param string $username - user to link to
     */
    public function showGravatarLink($username, $email, $size)
    {
        echo "<div class='show_post_gravatar'>";
        echo "<a href='" . $this->hotaru->url(array('user' => $username)) . "'>";
        echo $this->buildGravatarImage($email, $size);
        echo "</a></div>";
    }
    
    
    /**
     * Build Gravatar image
     *
     * @param string $email - email of avatar user
     * @return string - html for image
     */
    public function buildGravatarImage($email, $size)
    {
        $default = BASEURL . "content/plugins/gravatar/images/default_32.png";
        
        $grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=".md5( strtolower($email) ).
            "&default=".urlencode($default).
            "&size=".$size; 
            
        $img_url = "<img class='gravatar' src='" . $grav_url . "'>";
        
        return $img_url;
    }

}

?>