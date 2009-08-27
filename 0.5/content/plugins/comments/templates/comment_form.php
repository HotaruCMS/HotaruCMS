<?php
/**
 * Disqus Footer
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

global $plugin, $current_user, $lang, $post, $comment;
?>

<?php // NOT LOGGED IN
if (!$current_user->logged_in && $comment->comment_id == 0) { ?>

<div class="comments_please_login">
    <?php echo $lang['comments_please_login']; ?>
</div>
    
<?php } else { // LOGGED IN?>

<?php // CHECK SUBSCRIBED
if ($current_user->userbase_vars['post_subscribed']) { $subscribe_check = 'checked'; } else { $subscribe_check = ''; } ?>

<?php if ($comment->comment_id != 0) { // IF COMMENT REPLY ?>
<div class="comment_form comment_reply" style="margin-left: <?php echo $comment->comment_depth * 2.0; ?>em; display: none;">

<?php } else { // STANDARD COMMENT FORM ?>
<div class="comment_form">
    
<?php } ?>

    <form name='comment_form' action='<?php echo BASEURL; ?>index.php?page=comments' method='post'>
        <textarea name="comment_content" id="comment" rows="6" cols="50"/></textarea><br />
        <div class="comment_instructions"><?php echo $lang['comments_comment_form_allowable_tags']; ?><?php echo htmlentities($comment->comment_allowable_tags); ?></div>
        <div class="comment_subscribe"><input id="comment_subscribe" name="comment_subscribe" type="checkbox" <?php echo $subscribe_check; ?>> <?php echo $lang['comments_comment_form_subscribe']; ?></div>
        <div class="comment_extras"><?php echo $plugin->check_actions('comment_form_extras'); ?></div>
        <input type="submit" name="submit" value="<?php echo $lang['comments_comment_form_submit']; ?>" class="submit" />
        <input type="hidden" name="comment_process" value="newcomment" />
        <input type="hidden" name="comment_parent" value="<?php echo $comment->comment_id; ?>" />
        <input type="hidden" name="comment_post_id" value="<?php echo $post->post_id; ?>" />
        <input type="hidden" name="comment_user_id" value="<?php echo $current_user->id; ?>" />
    </form>
</div>
    
<?php } ?>