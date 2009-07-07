<?php

/* ******* PLUGIN TEMPLATE ************************************************************************** 
 * Plugin name: Submit
 * Template name: plugins/submit/show_post.php
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

global $plugin, $post;
		
$stories = $post->get_posts();
if($stories) {
	$pagedResults = new Paginated($stories, 10, $page);
	while($story = $pagedResults->fetchPagedRow()) {	//when $story is false loop terminates	
?>

<!-- ************ POST **************** -->

<div class="show_post">

	<?php $plugin->check_actions('show_post_1'); ?>
	
	<div class="show_post_title"><a href='<?php echo $story->post_orig_url; ?>'><?php echo $story->post_title; ?></a></div>
	
	<?php if($post->use_content) { ?>
		<div class="show_post_content"><?php echo $story->post_content; ?></div>
	<?php } ?>
	
	<?php if($post->use_tags) { ?>
		<div class="show_post_tags"><?php echo $story->post_tags; ?></div>
	<?php } ?>
	
	<?php $plugin->check_actions('show_post_2'); ?>
	
</div>

<!-- ************ END POST **************** -->

<?php	
	}
	
	//important to set the strategy to be used before a call to fetchPagedNavigation
	$pagedResults->setLayout(new DoubleBarLayout());
	echo $pagedResults->fetchPagedNavigation();
}
	
?>
 
 