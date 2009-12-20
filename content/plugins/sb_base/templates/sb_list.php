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

?>

<?php $hotaru->pluginHook('sb_base_pre_list'); ?> 

<?php 

if ($hotaru->vars['posts']) {
    $pg = $hotaru->cage->get->getInt('pg');
    
    $pagedResults = $hotaru->pagination($hotaru->vars['posts'], 10, $pg);
    while($post = $pagedResults->fetchPagedRow()) {
        $hotaru->readPost(0, $post);
        $user = new UserAuth();
        $user->getUserBasic($hotaru, $hotaru->post->author);
?>

<!-- POST -->
<?php $hotaru->pluginHook('sb_base_pre_show_post'); ?>

    <div class="show_post vote_button_space">
    
        <?php $hotaru->pluginHook('sb_base_show_post_pre_title'); ?>
        
        <div class="show_post_title">
            <?php if ($hotaru->vars['link_action'] == 'source') { ?>
                <a href='<?php echo $hotaru->post->origUrl; ?>' <?php echo $target; ?>><?php echo $hotaru->post->title; ?></a>
            <?php } else { ?>
                <a href='<?php echo $hotaru->url(array('page'=>$hotaru->post->id)); ?>' <?php echo $hotaru->vars['target']; ?>><?php echo $hotaru->post->title; ?></a>
            <?php } ?>
            <?php $hotaru->pluginHook('sb_base_show_post_title'); ?>
        </div>
    
        <div class="show_post_author_date">    
            <?php echo $hotaru->lang["sb_base_post_posted"]; ?>
            <?php echo " " . $hotaru->lang["sb_base_post_by"] . " <a href='" . $hotaru->url(array('user' => $user->name)) . "'>" . $user->name . "</a>"; ?>
            <?php echo time_difference(unixtimestamp($hotaru->post->date), $hotaru->lang) . " " . $hotaru->lang["sb_base_post_ago"]; ?>
            <?php $hotaru->pluginHook('sb_base_post_author_date'); ?>
            <?php 
                if (($hotaru->currentUser->getPermission('can_edit_posts') == 'yes') || ($hotaru->currentUser->id == $user->id)) { 
                    echo "<a class='show_post_edit' href='" . BASEURL . "index.php?page=edit_post&amp;post_id=" . $hotaru->post->id . "'>" . $hotaru->lang["sb_base_post_edit"] . "</a>"; 
                }
            ?> 
        </div>
            
        <?php if ($hotaru->vars['use_content']) { ?>
            <div class="show_post_content">
                <?php $hotaru->pluginHook('sb_base_show_post_content_list'); ?>
                <?php if ($hotaru->post->useSummary) { ?>
                    <?php echo truncate($hotaru->post->content, $hotaru->vars['summary_length']); ?>
                <?php } else { ?>
                    <?php echo $hotaru->post->content; ?>
                <?php } ?>    
                <small><a href='<?php echo $hotaru->url(array('page'=>$hotaru->post->id)); ?>'><?php echo $hotaru->lang['sb_base_post_read_more']; ?></a></small>
            </div>
        <?php } ?>
        
        <div class="show_post_extra_fields">
            <ul>
                <?php $hotaru->pluginHook('sb_base_show_post_extra_fields'); ?>
            </ul>
        </div>
        
        <div class="show_post_extras">
            <?php $hotaru->pluginHook('sb_base_show_post_extras'); ?>
        </div>
            
    </div>
    
    <!-- END POST --> 

<?php    
    }
    echo $hotaru->pageBar($pagedResults);
}
?>