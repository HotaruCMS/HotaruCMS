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
 

?>

    <div class="comment" style="margin-left: <?php echo $hotaru->comment->depth * 2.0; ?>em;">

        <?php   // Show avatars if enabled (requires an avatars plugin)
                if ($hotaru->comment->avatars == 'checked') {
                    $hotaru->plugins->pluginHook('show_comments_avatar'); 
                }
        ?>
        
        <div class="comment_author">
            <?php $hotaru->plugins->pluginHook('show_comments_content'); ?> 
            <?php echo $hotaru->comment->content; ?>
        </div>
        
        <?php   // Show votes if enabled (requires a comment voting plugin)
                if ($hotaru->comment->voting == 'checked') {
                    $hotaru->plugins->pluginHook('show_comments_votes'); 
                }
        ?>
        
        <div class="comment_author_date">
            <?php 
                $username = $hotaru->current_user->getUserNameFromId($hotaru->comment->author);
                echo $hotaru->lang['comments_written_by'] . " ";
                echo "<a href='" . $hotaru->url(array('user' => $username)) . "'>" . $username . "</a>, ";
                echo time_difference(unixtimestamp($hotaru->comment->date), $hotaru->lang) . " ";
                echo $hotaru->lang['comments_time_ago']; 
            ?>
            <?php   // REPLY LINK - (if logged in) AND (can comment) AND (form is turned on)...
                if ($hotaru->current_user->loggedIn 
                    && ($hotaru->current_user->getPermission('can_comment') != 'no')
                    && ($hotaru->comment->form == 'checked')) { ?>
                        
                <?php if ($hotaru->comment->depth < $hotaru->comment->levels-1) { // No nesting after X levels (minus 1 because nestings tarts at 0) ?>
                    <a href='#' class='comment_reply_link' onclick="reply_comment(
                        '<?php echo BASEURL; ?>', 
                        '<?php echo $hotaru->comment->id; ?>', 
                        '<?php echo $hotaru->lang['comments_form_submit']; ?>'); 
                        return false;" ><?php echo $hotaru->lang['comments_reply_link']; ?></a>
                <?php } ?>
            <?php } ?>
            
            <?php   // EDIT LINK - (if comment owner AND permission to edit own comments) OR (permission to edit ALL comments)...
                if (($hotaru->current_user->id == $hotaru->comment->author && ($hotaru->current_user->getPermission('can_edit_comments') == 'own'))
                    || ($hotaru->current_user->getPermission('can_edit_comments') == 'yes')) { ?>
                    <a href='#' class='comment_edit_link' onclick="edit_comment(
                        '<?php echo BASEURL; ?>', 
                        '<?php echo $hotaru->comment->id; ?>', 
                        '<?php echo urlencode($hotaru->comment->content); ?>', 
                        '<?php echo $hotaru->lang['comments_form_edit']; ?>'); 
                        return false;" ><?php echo $hotaru->lang['comments_edit_link']; ?></a>
            <?php } ?>
        </div>
        
        <div class="clear"></div>
            
    </div>
    
    <div class="clear"></div>
    
