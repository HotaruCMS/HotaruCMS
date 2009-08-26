<?php
/**
 * name: Gravatar
 * description: Enables Gravatar avatars for users
 * version: 0.1
 * folder: gravatar
 * prefix: grav
 * requires: users 0.1, submit 0.1
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

/**
 * Include CSS
 */
function grav_header_include()
{
    global $plugin, $css_files;

    $plugin->include_css('gravatar');
}

/**
 * Show gravatar in posts
 */
function grav_submit_show_post_pre_title()
{
    
    global $post;
    
    $user = new UserBase;
    $user->get_user_basic($post->post_author);
    $email = $user->email;
    $size = 32;
    
    show_gravatar_link($user->username, $email, $size);
}


/**
 * Show gravatar in comments
 */
function grav_show_comments_avatar()
{
    global $db, $comment;
    
    $sql = "SELECT user_username, user_email FROM " . TABLE_USERS . " WHERE user_id = %d";
    $commenter = $db->get_row($db->prepare($sql, $comment->comment_author));
    $size = 32;
    
    show_gravatar_link($commenter->user_username, $commenter->user_email, $size);
}


/**
 * Show Gravatar link
 *
 * @param string $username - user to link to
 */
function show_gravatar_link($username, $email, $size)
{
    echo "<div class='show_post_gravatar'>";
    echo "<a href='" . url(array('user' => $username)) . "'>";
    echo build_gravatar_image($email, $size);
    echo "</a></div>";
}


/**
 * Build Gravatar image
 *
 * @param string $email - email of avatar user
 * @return string - html for image
 */
function build_gravatar_image($email, $size)
{
    $default = BASEURL . "content/plugins/gravatar/images/default_32.png";
    
    $grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=".md5( strtolower($email) ).
        "&default=".urlencode($default).
        "&size=".$size; 
        
    $img_url = "<img class='gravatar' src='" . $grav_url . "'>";
    
    return $img_url;
}

?>