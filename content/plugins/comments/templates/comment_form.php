<?php
/**
 * Comment Form
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

<?php if ($h->comment->id != 0) { // IF COMMENT REPLY ?>
<div class="comment_form comment_reply" id="<?php echo "comment_" . $h->comment->id; ?>" style="margin-left: <?php echo $h->comment->depth * 2.0; ?>em; display: none;">

<?php } else { // STANDARD COMMENT FORM ?>
<div class="comment_form">
    
<?php } // JavaScript changes this form! See comments.js ?>

    <form name='comment_form' action='<?php echo $h->url(array('page' => $h->post->id)); ?>' method='post' onsubmit="document.getElementById('comment_submit_<?php echo $h->comment->id; ?>').disabled = true; return true;">
        <textarea name="comment_content" id="comment_content_<?php echo $h->comment->id; ?>" rows="6" cols="50"></textarea><br />
        <div class="comment_instructions"><small><?php echo $h->lang['comments_form_allowable_tags']; ?><?php echo htmlentities($h->comment->allowableTags); ?></small></div>
        <div class="comment_subscribe"><input id="comment_subscribe" name="comment_subscribe" type="checkbox" <?php echo $h->vars['subscribe']; ?> /> <?php echo $h->lang['comments_form_subscribe']; ?><?php if ($h->vars['subscribe']) { echo " <small>(" . $h->lang['comments_form_unsubscribe'] . ")</small>"; } ?></div>
        <div class="comment_extras"><?php echo $h->pluginHook('comment_form_extras'); ?>
            <?php if (($h->comment->setPending == "checked") || 
                    ($h->currentUser->getPermission('can_comment') == 'mod')) {
                    echo $h->lang['comments_form_moderation_on']; } ?>
        </div>
        
        <input type="submit" name="submit" id="comment_submit_<?php echo $h->comment->id; ?>" value="<?php echo $h->lang['comments_form_submit']; ?>" class="submit" />
        <input type="hidden" name="comment_process" id="comment_process_<?php echo $h->comment->id; ?>" value="newcomment" />
        <input type="hidden" name="comment_parent" value="<?php echo $h->comment->id; ?>" />
        <input type="hidden" name="comment_post_id" value="<?php echo $h->post->id; ?>" />
        <input type="hidden" name="comment_user_id" value="<?php echo $h->currentUser->id; ?>" />
        
        <?php if (($h->comment->id != 0) && ($h->currentUser->getPermission('can_set_comments_pending') == 'yes')) { ?>
            <div class='comment_status' style='display: none;'>
                <a href="<?php echo BASEURL; ?>index.php?page=comments&amp;action=setpending&amp;cid=<?php echo $h->comment->id; ?>&amp;pid=<?php echo $h->post->id; ?>">
                    <?php echo $h->lang['comments_form_set_pending']; ?>
                </a><br />
                
                <?php if ($h->currentUser->getPermission('can_delete_comments') == 'yes') { ?>
                <a class="bold_red" href="<?php echo BASEURL; ?>index.php?page=comments&amp;action=delete&amp;cid=<?php echo $h->comment->id; ?>&amp;pid=<?php echo $h->post->id; ?>">
                    <?php echo $h->lang['comments_form_delete']; ?>
                </a>
                <?php } ?>
            </div>
        <?php } ?>
        
    </form>
</div>

