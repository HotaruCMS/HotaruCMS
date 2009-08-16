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

global $hotaru, $plugin, $post, $cage, $filter, $lang, $page_title, $userbase;

// Prepare filter and breadcrumbs
$stories = sub_prepare_posts_list();
?>

<!-- BREADCRUMBS -->
<div id="breadcrumbs">
    <a href="<?php echo baseurl; ?>"><?php echo $lang['submit_form_home']; ?></a> &raquo; 
    <?php $plugin->check_actions('breadcrumbs'); ?> 
    <?php echo $page_title; ?>
</div>

<?php 

if ($stories) {
    $pg = $cage->get->getInt('pg');
    $pagedResults = new Paginated($stories, 10, $pg);
    while($story = $pagedResults->fetchPagedRow()) {    //when $story is false loop terminates    
        $post->read_post($story->post_id);

?>

<!-- POST -->
<?php $plugin->check_actions('submit_pre_show_post'); ?>

    <div class="show_post vote_button_space_<?php echo $post->post_vars['vote_type'] ?>">
        
        <div class="show_post_title"><a href='<?php echo $post->post_orig_url; ?>'><?php echo $post->post_title; ?></a></div>
    
        <?php if ($post->use_author || $post->use_date) { ?>
            <div class="show_post_author_date">    
                Posted
                <?php 
                if ($post->use_author) { 
                    echo " by <a href='" . url(array('user' => $userbase->get_username($post->post_author))) . "'>" . $userbase->get_username($post->post_author) . "</a>";
                }
                ?>
                <?php if ($post->use_date) { echo time_difference(unixtimestamp($post->post_date)) . " ago"; } ?>
                <?php $plugin->check_actions('submit_show_post_author_date'); ?>
            </div>
        <?php } ?>
            
        <?php if ($post->use_content) { ?>
            <div class="show_post_content">
                <?php if ($post->use_summary) { ?>
                    <?php echo substr(strip_tags($post->post_content), 0, $post->post_summary_length) ?>
                    <?php if (strlen(strip_tags($post->post_content)) >= $post->post_summary_length) { echo "..."; } ?>
                <?php } else { ?>
                    <?php echo $post->post_content; ?>
                <?php } ?>    
                <small><a href='<?php echo url(array('page'=>$post->post_id)) ?>'><?php echo $lang['submit_post_read_more'] ?></a></small>
            </div>
        <?php } ?>
        
        <div class="show_post_extra_fields">
            <?php $plugin->check_actions('submit_show_post_extra_fields'); ?>
        </div>
            
        <div class="show_post_extras">
            <?php $plugin->check_actions('submit_show_post_extras'); ?>
        </div>
            
    </div>
    
    <?php $plugin->check_actions('submit_post_show_post'); ?>

    <!-- END POST --> 

<?php    
    }
    
    //important to set the strategy to be used before a call to fetchPagedNavigation
    $pagedResults->setLayout(new DoubleBarLayout());
    echo $pagedResults->fetchPagedNavigation();
}
    
?>
 
 