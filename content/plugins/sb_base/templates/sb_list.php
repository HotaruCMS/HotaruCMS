<?php
/**
 * Template for sb_base plugin: sb_list
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

$user = new UserBase($hotaru);
if (isset($hotaru->currentUser->vars['settings']['new_tab'])) { $target = 'target="_blank"'; } else { $target = ''; }
if (isset($hotaru->currentUser->vars['settings']['link_action'])) { $open = 'source'; } else { $open = ''; }
// Prepare filter and breadcrumbs

?>

<?php $hotaru->pluginHook('submit_post_breadcrumbs'); ?> 

<?php $hotaru->pluginHook('submit_pre_list'); ?> 

<?php 

if ($hotaru->vars['posts']) {
    $pg = $hotaru->cage->get->getInt('pg');
    
    $pagedResults = $hotaru->pagination($hotaru->vars['posts'], $hotaru->post->postsPerPage, $pg);
    while($post = $pagedResults->fetchPagedRow()) {
        $hotaru->post->readPost(0, $post);
        $user->getUserBasic($hotaru->post->author);
?>

<!-- POST -->
<?php $hotaru->pluginHook('submit_pre_show_post'); ?>

    <div class="show_post vote_button_space">
    
        <?php $hotaru->pluginHook('submit_show_post_pre_title'); ?>
        
        <div class="show_post_title">
            <?php if ($open == 'source') { ?>
                <a href='<?php echo $hotaru->post->origUrl; ?>' <?php echo $target; ?>><?php echo $hotaru->post->title; ?></a>
            <?php } else { ?>
                <a href='<?php echo $hotaru->url(array('page'=>$hotaru->post->id)); ?>' <?php echo $target; ?>><?php echo $hotaru->post->title; ?></a>
            <?php } ?>
            <?php $hotaru->pluginHook('submit_show_post_title'); ?>
        </div>
    
        <?php if ($hotaru->post->useAuthor || $hotaru->post->useDate) { ?>
            <div class="show_post_author_date">    
                <?php echo $hotaru->lang["submit_post_posted"]; ?>
                <?php 
                if ($hotaru->post->useAuthor) { echo " " . $hotaru->lang["submit_post_by"] . " <a href='" . $hotaru->url(array('user' => $user->name)) . "'>" . $user->name . "</a>"; } 
                ?>
                <?php if ($hotaru->post->useDate) { echo time_difference(unixtimestamp($hotaru->post->date), $hotaru->lang) . " " . $hotaru->lang["submit_post_ago"]; } ?>
                <?php $hotaru->pluginHook('submit_show_post_author_date'); ?>
                <?php 
                    if (($hotaru->currentUser->getPermission('can_edit_posts') == 'yes') || ($hotaru->currentUser->id == $user->id)) { 
                        echo "<a class='show_post_edit' href='" . BASEURL . "index.php?page=edit_post&amp;post_id=" . $hotaru->post->id . "'>" . $hotaru->lang["submit_post_edit"] . "</a>"; 
                    }
                ?> 
            </div>
        <?php } ?>
            
        <?php if ($hotaru->post->useContent) { ?>
            <div class="show_post_content">
                <?php $hotaru->pluginHook('submit_show_post_content_list'); ?>
                <?php if ($hotaru->post->useSummary) { ?>
                    <?php echo truncate($hotaru->post->content, $hotaru->post->summaryLength); ?>
                <?php } else { ?>
                    <?php echo $hotaru->post->content; ?>
                <?php } ?>    
                <small><a href='<?php echo $hotaru->url(array('page'=>$hotaru->post->id)); ?>'><?php echo $hotaru->lang['submit_post_read_more']; ?></a></small>
            </div>
        <?php } ?>
        
        <div class="show_post_extra_fields">
            <ul>
                <?php $hotaru->pluginHook('submit_show_post_extra_fields'); ?>
            </ul>
        </div>
        
        <div class="show_post_extras">
            <?php $hotaru->pluginHook('submit_show_post_extras'); ?>
        </div>
            
    </div>
    
    <!-- END POST --> 

<?php    
    }
    
    echo $hotaru->pageBar($pagedResults);
}
    
?>