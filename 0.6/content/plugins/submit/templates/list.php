<?php
/**
 * Template for Submit: LIST
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

global $hotaru, $plugin, $post, $cage, $filter, $lang, $page_title, $current_user;

$user = new UserBase();

// Prepare filter and breadcrumbs
$stories = sub_prepare_list();
?>

<!-- BREADCRUMBS -->
<div id="breadcrumbs">
    <a href="<?php echo BASEURL; ?>"><?php echo $lang['submit_form_home']; ?></a> &raquo; 
    <?php $plugin->check_actions('breadcrumbs'); ?> 
    <?php echo $page_title; ?>
</div>

<?php 

if ($stories) {
    $pg = $cage->get->getInt('pg');
    $pagedResults = new Paginated($stories, $post->posts_per_page, $pg);
    while($story = $pagedResults->fetchPagedRow()) {    //when $story is false loop terminates    
        $post->read_post($story->post_id);
        $user->get_user_basic($post->post_author);
?>

<!-- POST -->
<?php $plugin->check_actions('submit_pre_show_post'); ?>

    <div class="show_post vote_button_space_<?php echo $post->post_vars['vote_type']; ?>">
    
        <?php $plugin->check_actions('submit_show_post_pre_title'); ?>
        
        <div class="show_post_title"><a href='<?php echo $post->post_orig_url; ?>'><?php echo $post->post_title; ?></a></div>
    
        <?php if ($post->use_author || $post->use_date) { ?>
            <div class="show_post_author_date">    
                Posted
                <?php 
                if ($post->use_author) { echo " by <a href='" . url(array('user' => $user->username)) . "'>" . $user->username . "</a>"; } 
                ?>
                <?php if ($post->use_date) { echo time_difference(unixtimestamp($post->post_date)) . " ago"; } ?>
                <?php $plugin->check_actions('submit_show_post_author_date'); ?>
                <?php 
                    if ($current_user->role == 'admin' || ($current_user->id == $user->id)) { 
                        echo "<a class='show_post_edit' href='" . url(array('page'=>'edit_post', 'post_id'=>$post->post_id)) . "'>" . $lang["submit_post_edit"] . "</a>"; 
                    }
                ?> 
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
                <small><a href='<?php echo url(array('page'=>$post->post_id)); ?>'><?php echo $lang['submit_post_read_more']; ?></a></small>
            </div>
        <?php } ?>
        
        <div class="show_post_extra_fields">
            <ul>
                <?php $plugin->check_actions('submit_show_post_extra_fields'); ?>
            </ul>
        </div>
        
        <div class="show_post_extras">
            <?php $plugin->check_actions('submit_show_post_extras'); ?>
        </div>
            
    </div>
    
    <!-- END POST --> 

<?php    
    }
    
    //important to set the strategy to be used before a call to fetchPagedNavigation
    $pagedResults->setLayout(new DoubleBarLayout());
    echo $pagedResults->fetchPagedNavigation();
}
    
?>
 