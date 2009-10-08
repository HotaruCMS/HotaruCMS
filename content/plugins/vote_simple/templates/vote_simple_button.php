<?php
/**
 * Vote Button
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

The following code looks pretty ugly, but it's not quite as confusing as it first appears. Basically, it's in two blocks, one for "vote" and one for "un-vote". The reason it's so bulky is because we want users to be able to change their vote, so after voting, we need to enable the "un-vote" button and vice-versa. This is done by having two copies of the text blocks and switching the display to show or hide them.
*/

$user_ip = $hotaru->cage->server->testIp('REMOTE_ADDR');
?>
 
<!-- Vote Button -->
<div class='vote_button'>

<!-- VOTE COUNT -->
<div id='votes_<?php echo $hotaru->post->id; ?>' class='vote_button_top'><?php echo $hotaru->vars['votesUp']; ?></div>

<!-- VOTE OR UN-VOTE LINK -->
<?php if ($hotaru->current_user->loggedIn && !$hotaru->vars['voted']) { ?>
    <!-- Logged in and not voted yet -->
    
    <!-- Shown -->
    <div id='text_vote_<?php echo $hotaru->post->id; ?>' class='vote_button_bottom'>
        <a href="#" onclick="vote('<?php echo BASEURL; ?>', '<?php echo $user_ip; ?>', <?php echo $hotaru->post->id; ?>, 'positive'); return false;"><b><?php echo $hotaru->lang["vote_button_vote"]; ?></b></a>
    </div>    
    
    <!-- Hidden -->
    <div id='text_unvote_<?php echo $hotaru->post->id; ?>' class='vote_button_bottom' style="display: none;">
        <a href="#" onclick="vote('<?php echo BASEURL; ?>', '<?php echo $user_ip; ?>', <?php echo $hotaru->post->id; ?>, 'negative'); return false;"><?php echo $hotaru->lang["vote_button_unvote"]; ?></a>
    </div>        
    
<?php } elseif ($hotaru->current_user->loggedIn && $hotaru->vars['voted']) { ?>
    <!-- Logged in and already voted -->
    
    <!-- Hidden -->
    <div id='text_vote_<?php echo $hotaru->post->id; ?>' class='vote_button_bottom' style="display: none;">
        <a href="#" onclick="vote('<?php echo BASEURL; ?>', '<?php echo $user_ip; ?>', <?php echo $hotaru->post->id; ?>, 'positive'); return false;"><b><?php echo $hotaru->lang["vote_button_vote"]; ?></b></a>
    </div>
    
    <!-- Shown -->
    <div id='text_unvote_<?php echo $hotaru->post->id; ?>' class='vote_button_bottom'>
        <a href="#" onclick="vote('<?php echo BASEURL; ?>', '<?php echo $user_ip; ?>', <?php echo $hotaru->post->id; ?>, 'negative'); return false;"><?php echo $hotaru->lang["vote_button_unvote"]; ?></a>
    </div>
    
<?php } else { ?>
    <!-- Need to login -->
    
    <div id='text_login_<?php echo $hotaru->post->id; ?>' class='vote_button_bottom'>
        <a href="<?php echo $hotaru->url(array('page'=>'login')); ?>"><?php echo $hotaru->lang["vote_button_vote"]; ?></a>
    </div>
<?php } ?>

</div>
