<?php

/* ******* PLUGIN TEMPLATE ************************************************************************** 
 * Plugin name: Submit
 * Template name: plugins/submit/posts_list.php
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

global $plugin, $post, $cage;
$userbase = new UserBase();
		
$stories = $post->get_posts();
if($stories) {
	$pg = $cage->get->getInt('pg');
	$pagedResults = new Paginated($stories, 10, $pg);
	while($story = $pagedResults->fetchPagedRow()) {	//when $story is false loop terminates	
		$post->read_post($story->post_id);

?>

<!-- ************ POST **************** -->

<div class="posts_list">

	<?php $plugin->check_actions('submit_posts_list_1'); ?>
	
	<div class="show_post_title"><a href='<?php echo $post->post_orig_url; ?>'><?php echo $post->post_title; ?></a></div>

	<?php if($post->use_author || $post->use_date) { ?>
		<div class="show_post_author_date">	
			Posted
			<?php if($post->use_author) { echo " by " . $userbase->get_username($post->post_author); } ?>
			<?php if($post->use_date) { echo time_difference(unixtimestamp($post->post_date)) . " ago"; } ?>
		</div>
	<?php } ?>
		
	<?php if($post->use_content) { ?>
		<div class="show_post_content"><?php echo $post->post_content; ?></div>
	<?php } ?>
	
	<?php $plugin->check_actions('submit_posts_list_extra_fields_1'); ?>
		
	<div class="show_permalink"><a href='<?php echo url(array('page'=>$post->post_id)); ?>'>Permalink</a></div>
	
	<?php $plugin->check_actions('submit_posts_list_2'); ?>
	
</div>

<!-- ************ END POST **************** -->

<?php	
	}
	
	//important to set the strategy to be used before a call to fetchPagedNavigation
	$pagedResults->setLayout(new DoubleBarLayout());
	echo $pagedResults->fetchPagedNavigation();
}
	
?>
 
 