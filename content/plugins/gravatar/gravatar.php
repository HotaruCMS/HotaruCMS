<?php
/**
 * name: Gravatar
 * description: Enables Gravatar avatars for users
 * version: 0.1
 * folder: gravatar
 * prefix: grav
 * requires: users 0.1, submit 0.1
 * hooks: header_include, submit_show_post_pre_title
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

/**
 * Include CSS
 */
function grav_header_include() {
    global $plugin;
    $plugin->include_css_file('gravatar');
}


/**
 * Show gravatar
 */
function grav_submit_show_post_pre_title() {
    
    global $post;
    
    $user = new UserBase;
    $user->get_user_basic($post->post_author);
    $email = $user->email;
    $default = baseurl . "content/plugins/gravatar/images/default_32.png";
    $size = 32;
    
    $grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=".md5( strtolower($email) ).
        "&default=".urlencode($default).
        "&size=".$size; 
        
    echo "<div class='show_post_gravatar'>";
    echo "<a href='" . url(array('user' => $user->username)) . "'>";
    echo "<img  src='" . $grav_url . "'>";
    echo "</a></div>";
}

?>