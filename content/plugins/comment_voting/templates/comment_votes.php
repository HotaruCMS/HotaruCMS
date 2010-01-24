<?php
/**
 * Comment Voting Buttons
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

$user_ip = $h->cage->server->testIp('REMOTE_ADDR');
?>
 
<!-- Vote Button -->
<div id='comment_votes_<?php echo $h->comment->id; ?>' class='comment_votes'>

    <!-- VOTE COUNTS -->
    <div id='comment_votes_up_<?php echo $h->comment->id; ?>' class='comment_votes_up'><?php echo $h->comment->votes_up; ?></div>

    <!-- UP BUTTON -->
    <?php // determine whether the vote button is a link or plain text
        if ($h->currentUser->loggedIn && ($h->comment->author != $h->currentUser->id) && !$h->vars['already_voted']) { 
            $link_display = "style=''"; $text_display = "style='display: none;'";
        } else {
            $text_display = "style=''"; $link_display = "style='display: none;'";
        } 
    ?>
    
    <div id='comment_votes_up_link_<?php echo $h->comment->id; ?>' class='comment_votes_up_link' <?php echo $link_display; ?>>
        <a href="#" onclick="comment_voting('<?php echo BASEURL; ?>', '<?php echo $user_ip; ?>', <?php echo $h->post->id; ?>, <?php echo $h->comment->id; ?>, 10); return false;">
        <img src="<?php echo BASEURL; ?>content/plugins/comment_voting/images/thumbsup.png" title="<?php echo $h->lang['comment_voting_button_vote_up']; ?>">
        </a>
    </div>

    <div id='comment_votes_up_text_<?php echo $h->comment->id; ?>' class='comment_votes_up_text' <?php echo $text_display; ?>>
        <img src="<?php echo BASEURL; ?>content/plugins/comment_voting/images/thumbsup.png" title="<?php echo $h->lang['comment_voting_button_vote_up']; ?>">
    </div>
    
    
    <!-- DOWN BUTTON -->
    <div id='comment_votes_down_<?php echo $h->comment->id; ?>' class='comment_votes_down'><?php echo $h->comment->votes_down; ?></div>
    
    <?php // determine whether the vote button is a link or plain text
        if ($h->currentUser->loggedIn && ($h->comment->author != $h->currentUser->id) && !$h->vars['already_voted']) { 
            $link_display = "style=''"; $text_display = "style='display: none;'";
        } else {
            $text_display = "style=''"; $link_display = "style='display: none;'";
        } 
    ?>
    
    <div id='comment_votes_down_link_<?php echo $h->comment->id; ?>' class='comment_votes_down_link' <?php echo $link_display; ?>>
        <a href="#" onclick="comment_voting('<?php echo BASEURL; ?>', '<?php echo $user_ip; ?>', <?php echo $h->post->id; ?>, <?php echo $h->comment->id; ?>, -10); return false;">
        <img src="<?php echo BASEURL; ?>content/plugins/comment_voting/images/thumbsdown.png" title="<?php echo $h->lang['comment_voting_button_vote_down']; ?>">
        </a>
    </div>

    <div id='comment_votes_down_text_<?php echo $h->comment->id; ?>' class='comment_votes_down_text' <?php echo $text_display; ?>>
        <img src="<?php echo BASEURL; ?>content/plugins/comment_voting/images/thumbsdown.png" title="<?php echo $h->lang['comment_voting_button_vote_down']; ?>">
    </div>
    
</div>
