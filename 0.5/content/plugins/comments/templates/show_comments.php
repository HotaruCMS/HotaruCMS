<?php
/**
 * Disqus Comments
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
 
global $plugin, $comment, $lang, $userbase, $current_user;

?>

    <div class="comment" style="margin-left: <?php echo $comment->comment_depth * 2.0; ?>em;">
       
        <?php   // Show avatars if enabled (requires an avatars plugin)
                if ($comment->comment_avatars == 'checked') {
                    $plugin->check_actions('show_comments_avatar'); 
                }
        ?>
        
        <div class="comment_author">
            <?php echo $comment->comment_content; ?>
        </div>
        
        <?php   // Show votes if enabled (requires a comment voting plugin)
                if ($comment->comment_voting == 'checked') {
                    $plugin->check_actions('show_comments_votes'); 
                }
        ?>
        
        <div class="comment_author_date">
            <?php 
                $username = $userbase->get_username($comment->comment_author);
                echo $lang['comments_written_by'] . " ";
                echo "<a href='" . url(array('user' => $username)) . "'>" . $username . "</a>, ";
                echo time_difference(unixtimestamp($comment->comment_date)) . " ";
                echo $lang['comments_time_ago']; 
            ?>
            <?php if ($current_user->logged_in) { ?>
                <a href='#' class='comment_reply_link'><?php echo $lang['comments_reply_link']; ?></a>
            <?php } ?>
        </div>
        
        <div class="clear"></div>
            
    </div>
    
    <div class="clear"></div>
    
