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

global $plugins, $current_user, $lang, $post, $comment;
?>

<?php // CHECK SUBSCRIBED
if ($current_user->vars['postSubscribed']) { $subscribe_check = 'checked'; } else { $subscribe_check = ''; } ?>

<?php if ($comment->getId() != 0) { // IF COMMENT REPLY ?>
<div class="comment_form comment_reply" id="<?php echo "comment_" . $comment->getId(); ?>" style="margin-left: <?php echo $comment->getDepth() * 2.0; ?>em; display: none;">

<?php } else { // STANDARD COMMENT FORM ?>
<div class="comment_form">
    
<?php } // JavaScript changes this form! See comments.js ?>

    <form name='comment_form' action='<?php echo BASEURL; ?>index.php?page=comments' method='post'>
        <textarea name="comment_content" id="comment_content_<?php echo $comment->getId(); ?>" rows="6" cols="50"/></textarea><br />
        <div class="comment_instructions"><?php echo $lang['comments_form_allowable_tags']; ?><?php echo htmlentities($comment->getAllowableTags()); ?></div>
        <div class="comment_subscribe"><input id="comment_subscribe" name="comment_subscribe" type="checkbox" <?php echo $subscribe_check; ?>> <?php echo $lang['comments_form_subscribe']; ?><?php if ($subscribe_check) { echo " <small>(" . $lang['comments_form_unsubscribe'] . ")</small>"; } ?></div>
        <div class="comment_extras"><?php echo $plugins->pluginHook('comment_form_extras'); ?></div>
        <input type="submit" name="submit" id="comment_submit_<?php echo $comment->getId(); ?>" value="<?php echo $lang['comments_form_submit']; ?>" class="submit" />
        <input type="hidden" name="comment_process" id="comment_process_<?php echo $comment->getId(); ?>" value="newcomment" />
        <input type="hidden" name="comment_parent" value="<?php echo $comment->getId(); ?>" />
        <input type="hidden" name="comment_post_id" value="<?php echo $post->getId(); ?>" />
        <input type="hidden" name="comment_user_id" value="<?php echo $current_user->getId(); ?>" />
    </form>
</div>

