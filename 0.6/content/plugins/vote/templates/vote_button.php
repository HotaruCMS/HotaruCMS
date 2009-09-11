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
 */

 global $post, $current_user, $voted, $plugins, $lang, $cage;
 ?>
 
<?php /* The following VOTE-UNVOTE BUTTON code looks pretty ugly, but it's not quite as confusing as it first appears. Basically, it's in two blocks, one for "vote" and one for "un-vote". The reason it's so bulky is because we want users to be able to change their vote, so after voting, we need to enable the "un-vote" button and vice-versa. This is done by having two copies of the text blocks and switching the display to show or hide them. */ ?>
 
    <?php if ($post->vars['vote_type'] == "vote_unvote") { ?>
        <!-- Vote Button (type: vote_unvote) -->
        <div class='vote_vote_unvote_button'>
        
        <!-- VOTE COUNT -->
        <div id='vote_unvote_votes_<?php echo $post->id; ?>' class='vote_vote_unvote_button_top'><?php echo $post->votesUp; ?></div>
        
        <!-- VOTE OR UN-VOTE LINK -->
        <?php if (($current_user->logged_in || ($post->vars['vote_anonymous_votes'] == 'checked')) && !$voted) { ?>
            <!-- Logged in (or anonymous voter) and not voted yet -->
            <!-- Shown -->
            <div id='vote_unvote_text_vote_<?php echo $post->id; ?>' class='vote_vote_unvote_button_bottom'>
                <a href="#" onclick="vote('<?php echo BASEURL; ?>', '<?php echo $cage->server->testIp('REMOTE_ADDR') ?>', <?php echo $post->id; ?>, 'vote_unvote', 'positive'); return false;"><b><?php echo $lang["vote_button_vote"]; ?></b></a>
            </div>    
            <!-- Hidden -->
            <div id='vote_unvote_text_unvote_<?php echo $post->id; ?>' class='vote_vote_unvote_button_bottom' style="display: none;">
                <a href="#" onclick="vote('<?php echo BASEURL; ?>', '<?php echo $cage->server->testIp('REMOTE_ADDR') ?>', <?php echo $post->id; ?>, 'vote_unvote', 'negative'); return false;"><?php echo $lang["vote_button_unvote"]; ?></a>
            </div>        
        <?php } elseif (($current_user->logged_in || ($post->vars['vote_anonymous_votes'] == 'checked')) && $voted) { ?>
            <!-- Logged in (or anonymous voter) and already voted -->
            <!-- Hidden -->
            <div id='vote_unvote_text_vote_<?php echo $post->id; ?>' class='vote_vote_unvote_button_bottom' style="display: none;">
                <a href="#" onclick="vote('<?php echo BASEURL; ?>', '<?php echo $cage->server->testIp('REMOTE_ADDR') ?>', <?php echo $post->id; ?>, 'vote_unvote', 'positive'); return false;"><b><?php echo $lang["vote_button_vote"]; ?></b></a>
            </div>    
            <!-- Shown -->
            <div id='vote_unvote_text_unvote_<?php echo $post->id; ?>' class='vote_vote_unvote_button_bottom'>
                <a href="#" onclick="vote('<?php echo BASEURL; ?>', '<?php echo $cage->server->testIp('REMOTE_ADDR') ?>', <?php echo $post->id; ?>, 'vote_unvote', 'negative'); return false;"><?php echo $lang["vote_button_unvote"]; ?></a>
            </div>
        <?php } else { ?>
            <!-- Need to login -->
            <div id='vote_unvote_text_login_<?php echo $post->id; ?>' class='vote_vote_unvote_button_bottom'>
                <a href="<?php echo url(array('page'=>'login')); ?>"><?php echo $lang["vote_button_vote"]; ?></a>
            </div>
        <?php } ?>
        </div>
    <?php } ?>
    
    
    
    
    
<?php /* Like above, the following UP-DOWN BUTTON code looks pretty ugly, but it's not quite as confusing as it first appears. Basically, it's in two main blocks, one for the top "up" button and one for the bottom "down" button. The reason it's so bulky is because we want users to be able to change their vote, so after voting "up", we need to re-enable the "down" button and vice-versa. This is done by having two copies of the text blocks and switching the display to show or hide them. */ ?>

     <?php if ($post->vars['vote_type'] == "up_down") { ?>
         <!-- Vote Button (type: up_down) -->
         
        <div class='vote_up_down_button'>
        
            <!-- UP LINK -->
            <?php if (($current_user->logged_in || ($post->vars['vote_anonymous_votes'] == 'checked')) && (!$voted || $voted == 'negative')) { ?> 
                <!-- Logged in (or anonymous voting ok), not voted, or voted negative -->
                <!-- Shown -->
                <div id='up_down_text_up_vote_<?php echo $post->id; ?>' class='vote_up_down_button_top'>
                     <a href="#" onclick="vote('<?php echo BASEURL; ?>', '<?php echo $cage->server->testIp('REMOTE_ADDR') ?>', <?php echo $post->id; ?>, 'up_down', 'positive'); return false;"><b><?php echo $lang["vote_button_up_link"]; ?></b></a>
                </div>
                <!-- Hidden -->
                <div id='up_down_text_up_voted_<?php echo $post->id; ?>' class='vote_up_down_button_top' style="display:none;">
                     <?php echo $lang["vote_button_up"]; ?>
                </div>
            <?php } elseif ($current_user->logged_in && (!$voted || $voted == 'positive')) { ?>
                <!-- Logged in, not voted, or voted positive -->
                <!-- Hidden -->
                 <div id='up_down_text_up_vote_<?php echo $post->id; ?>' class='vote_up_down_button_top' style="display:none;">
                     <a href="#" onclick="vote('<?php echo BASEURL; ?>', '<?php echo $cage->server->testIp('REMOTE_ADDR') ?>', <?php echo $post->id; ?>, 'up_down', 'positive'); return false;"><b><?php echo $lang["vote_button_up_link"]; ?></b></a>
                </div>
                <!-- Shown -->
                <div id='up_down_text_up_voted_<?php echo $post->id; ?>' class='vote_up_down_button_top'>
                     <?php echo $lang["vote_button_up"]; ?>
                </div>
            <?php } else { ?>
                <!-- Need to login -->
                <div id='up_down_text_up_login_<?php echo $post->id; ?>' class='vote_up_down_button_top'>
                     <a href="<?php echo url(array('page'=>'login')); ?>"><b><?php echo $lang["vote_button_up_link"]; ?></b></a>
                </div>
            <?php } ?>    
            
            <!-- VOTE COUNT -->
            <?php $vote_count = ($post->votesUp - $post->postVotesDown); ?>
  
            <div id='up_down_votes_<?php echo $post->id; ?>' class='vote_up_down_button_middle'><?php echo $vote_count; ?></div>
              
              <!-- DOWN LINK -->
             <?php if (($current_user->logged_in || ($post->vars['vote_anonymous_votes'] == 'checked')) && (!$voted || $voted == 'positive')) { ?> 
                 <!-- Logged in (or anonymous voting ok), not voted, or voted positive -->
                 <!-- Shown -->
                <div id='up_down_text_down_vote_<?php echo $post->id; ?>' class='vote_up_down_button_bottom'>
                    <a href="#" onclick="vote('<?php echo BASEURL; ?>', '<?php echo $cage->server->testIp('REMOTE_ADDR') ?>', <?php echo $post->id; ?>, 'up_down', 'negative'); return false;"><b><?php echo $lang["vote_button_down_link"]; ?></b></a>
                </div>
                <!-- Hidden -->
                <div id='up_down_text_down_voted_<?php echo $post->id; ?>' class='vote_up_down_button_bottom' style="display:none;">
                    <?php echo $lang["vote_button_down"]; ?>
                </div>
            <?php } elseif ($current_user->logged_in && (!$voted || $voted == 'negative')) { ?>
                <!-- Logged in, not voted, or voted negative -->
                <!-- Hidden -->
                <div id='up_down_text_down_vote_<?php echo $post->id; ?>' class='vote_up_down_button_bottom' style="display:none;">
                    <a href="#" onclick="vote('<?php echo BASEURL; ?>', '<?php echo $cage->server->testIp('REMOTE_ADDR') ?>', <?php echo $post->id; ?>, 'up_down', 'negative'); return false;"><b><?php echo $lang["vote_button_down_link"]; ?></b></a>
                </div>
                <!-- Shown -->
                <div id='up_down_text_down_voted_<?php echo $post->id; ?>' class='vote_up_down_button_bottom'>
                    <?php echo $lang["vote_button_down"]; ?>
                </div>
             <?php } else { ?>
                <!-- Need to login -->
                <div id='up_down_text_down_login_<?php echo $post->id; ?>' class='vote_up_down_button_bottom'>
                     <a href="<?php echo url(array('page'=>'login')); ?>"><b><?php echo $lang["vote_button_down_link"]; ?></b></a>
                </div>
            <?php } ?>
         </div>
    <?php } ?>
    
    
    
    
    
    
<?php /* As above, the following YES-NO BUTTON code looks pretty ugly, but it's not quite as confusing as it first appears. Basically, it's in two main blocks, one for the left "yes" button and one for the right "no" button. The reason it's so bulky is because we want users to be able to change their vote, so after voting "yes", we need to re-enable the "no" button and vice-versa. This is done by having two copies of the text blocks and switching the display to show or hide them. */ ?>
  
      <?php if ($post->vars['vote_type'] == "yes_no") { ?>
          <!-- Vote Button (type: yes_no) -->
          
          <!-- YES BUTTON -->
        <div class='vote_yes_no_button'>
        
            <!-- Vote count -->
             <div id='yes_no_votes_yes_<?php echo $post->id; ?>' class='vote_yes_no_button_top bg_yes'><?php echo $post->votesUp; ?></div>
             
            <?php if (($current_user->logged_in || ($post->vars['vote_anonymous_votes'] == 'checked')) && (!$voted || $voted == 'negative')) { ?> 
                <!-- Logged in (or anonymous voter), not voted, or voted negative -->
                <!-- Shown -->
                <div id='yes_no_text_yes_vote_<?php echo $post->id; ?>' class='vote_yes_no_button_bottom'>
                    <a href="#" onclick="vote('<?php echo BASEURL; ?>', '<?php echo $cage->server->testIp('REMOTE_ADDR') ?>', <?php echo $post->id; ?>, 'yes_no', 'positive'); return false;"><b><?php echo $lang["vote_button_yes_link"]; ?></b></a>
                </div>
                <!-- Hidden -->
                <div id='yes_no_text_yes_voted_<?php echo $post->id; ?>' class='vote_yes_no_button_bottom' style="display:none;">
                    <?php echo $lang["vote_button_yes"]; ?>
                </div>
            <?php } elseif (($current_user->logged_in || ($post->vars['vote_anonymous_votes'] == 'checked')) && (!$voted || $voted == 'positive')) { ?>
                <!-- Logged in (or anonymous voter), not voted, or voted positive -->
                <!-- Hidden -->
                <div id='yes_no_text_yes_vote_<?php echo $post->id; ?>' class='vote_yes_no_button_bottom' style="display:none;">
                    <a href="#" onclick="vote('<?php echo BASEURL; ?>', '<?php echo $cage->server->testIp('REMOTE_ADDR') ?>', <?php echo $post->id; ?>, 'yes_no', 'positive'); return false;"><b><?php echo $lang["vote_button_yes_link"]; ?></b></a>
                </div>
                <!-- Shown -->
                <div id='yes_no_text_yes_voted_<?php echo $post->id; ?>' class='vote_yes_no_button_bottom'>
                    <?php echo $lang["vote_button_yes"]; ?>
                </div>
            <?php } else { ?>
                <!-- Need to login -->
                <div id='yes_no_text_yes_login_<?php echo $post->id; ?>' class='vote_yes_no_button_bottom'>
                     <a href="<?php echo url(array('page'=>'login')); ?>"><b><?php echo $lang["vote_button_yes_link"]; ?></b></a>
                </div>
            <?php } ?>
        </div>
        
        <!-- NO BUTTON -->
         <div class='vote_yes_no_button'>
         
             <!-- Vote count -->
            <div id='yes_no_votes_no_<?php echo $post->id; ?>' class='vote_yes_no_button_top bg_no'><?php echo $post->votesDown; ?></div>
            
            <?php if (($current_user->logged_in || ($post->vars['vote_anonymous_votes'] == 'checked')) && (!$voted || $voted == 'positive')) { ?> 
                <!-- Logged in (or anonymous voter), not voted, or voted positive -->
                <!-- Shown -->
                <div id='yes_no_text_no_vote_<?php echo $post->id; ?>' class='vote_yes_no_button_bottom'>
                    <a href="#" onclick="vote('<?php echo BASEURL; ?>', '<?php echo $cage->server->testIp('REMOTE_ADDR') ?>', <?php echo $post->id; ?>, 'yes_no', 'negative'); return false;"><b><?php echo $lang["vote_button_no_link"]; ?></b></a>
                </div>
                <!-- Hidden -->
                <div id='yes_no_text_no_voted_<?php echo $post->id; ?>' class='vote_yes_no_button_bottom' style="display:none;">
                    <?php echo $lang["vote_button_no"]; ?>
                </div>
            <?php } elseif (($current_user->logged_in || ($post->vars['vote_anonymous_votes'] == 'checked')) && (!$voted || $voted == 'negative')) { ?>
                <!-- Logged in (or anonymous voter), not voted, or voted negative -->
                <!-- Hidden -->
                <div id='yes_no_text_no_vote_<?php echo $post->id; ?>' class='vote_yes_no_button_bottom' style="display:none;">
                    <a href="#" onclick="vote('<?php echo BASEURL; ?>', '<?php echo $cage->server->testIp('REMOTE_ADDR') ?>', <?php echo $post->id; ?>, 'yes_no', 'negative'); return false;"><b><?php echo $lang["vote_button_no_link"]; ?></b></a>
                </div>
                <!-- Shown -->
                <div id='yes_no_text_no_voted_<?php echo $post->id; ?>' class='vote_yes_no_button_bottom'>
                    <?php echo $lang["vote_button_no"]; ?>
                </div>
            <?php } else { ?>
                <!-- Need to login -->
                <div id='yes_no_text_no_login_<?php echo $post->id; ?>' class='vote_yes_no_button_bottom'>
                     <a href="<?php echo url(array('page'=>'login')); ?>"><b><?php echo $lang["vote_button_no_link"]; ?></b></a>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
 