<?php

/* ******* PLUGIN TEMPLATE ************************************************************************** 
 * Plugin name: Vote
 * Template name: plugins/vote/vote_button.php
 * Template author: Nick Ramsay
 * Version: 0.1
 * License:
 *
 *   This file is part of Hotaru CMS (http://www.hotarucms.org/).
 *
 *   Hotaru CMS is free software: you can redistribute it and/or modify it under the terms of the 
 *   GNU General Public License as published by the Free Software Foundation, either version 3 of 
 *   the License, or (at your option) any later version.
 *
 *   Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 *   even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License along with Hotaru CMS. If not, 
 *   see http://www.gnu.org/licenses/.
 *   
 *   Copyright (C) 2009 Hotaru CMS - http://www.hotarucms.org/
 *
 **************************************************************************************************** */

 global $post, $current_user, $voted, $plugin;
 ?>
 	<?php if($post->post_vars['vote_type'] == "vote_bury") { ?>
 		<!-- Vote Button (type: vote_bury) -->
 		<div class='vote_vote_bury_button'>
 		<div id='vote_bury_votes_<?php echo $post->post_id ?>' class='vote_vote_bury_button_top'><?php echo $post->post_votes_up ?></div>
		<div id='vote_bury_text_<?php echo $post->post_id ?>' class='vote_vote_bury_button_bottom'>
			<?php if($current_user->logged_in && !$voted) { ?>
				<a href="#" onclick="vote('<?php echo baseurl ?>', <?php echo $current_user->id ?>, <?php echo $post->post_id ?>, 'vote_bury', 'positive');"><b>Vote!</b></a>
			<?php } elseif($current_user->logged_in && $voted) { ?>
				Voted
			<?php } else { ?>
				<a href="<?php echo url(array('page=login')); ?>">Vote!</a>
			<?php } ?>
		</div>
 		</div>
 	<?php } ?>
 	
  	<?php if($post->post_vars['vote_type'] == "up_down") { ?>
  		<!-- Vote Button (type: up_down) -->
 		<div class='vote_up_down_button'>
 		<?php if(!$voted || $voted == 'negative') { ?> 
 			<div class='vote_up_down_button_top'>Up!</div> 
 		<?php } ?>		
 		<div class='vote_up_down_button_middle'><?php echo $post->post_votes_up ?></div>
 		<?php if(!$voted || $voted == 'positive') { ?> 
 			<div class='vote_up_down_button_bottom'>Down!</div> 
 		<?php } ?> 		
 		</div>
 	<?php } ?>
 	
   	<?php if($post->post_vars['vote_type'] == "yes_no") { ?>
   		<!-- Vote Button (type: yes_no) -->
 		<div class='vote_yes_no_button'>
  		<div class='vote_yes_no_button_top bg_yes'><?php echo $post->post_votes_up ?></div>
 		<?php if(!$voted || $voted == 'negative') { ?> 
 			<div class='vote_yes_no_button_bottom'>Vote!</div> 
 		<?php } ?>
 		</div>
  		<div class='vote_yes_no_button'>
 		<div class='vote_yes_no_button_top bg_no'><?php echo $post->post_votes_down ?></div>
 		<?php if(!$voted || $voted == 'positive') { ?> 
 			<div class='vote_yes_no_button_bottom'>Vote!</div> 
 		<?php } ?>
 		</div>
 	<?php } ?>
 