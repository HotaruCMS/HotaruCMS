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

$user = new UserBase($hotaru);

// Prepare filter and breadcrumbs
$stories = $hotaru->post->prepareList();

?>

<!-- BREADCRUMBS -->
<div id="breadcrumbs">
    <a href="<?php echo BASEURL; ?>"><?php echo $hotaru->lang['submit_form_home']; ?></a> &raquo; 
    <?php $hotaru->plugins->pluginHook('breadcrumbs'); ?> 
    <?php echo $hotaru->vars['page_title']; ?>
</div>

<?php $hotaru->plugins->pluginHook('submit_post_breadcrumbs'); ?> 

<?php 

if ($stories) {
    $pg = $hotaru->cage->get->getInt('pg');
    $pagedResults = new Paginated($stories, $hotaru->post->postsPerPage, $pg);
    while($story = $pagedResults->fetchPagedRow()) {    //when $story is false loop terminates    
        $hotaru->post->readPost($story->post_id);
        $user->getUserBasic($hotaru->post->author);
?>

<!-- POST -->
<?php $hotaru->plugins->pluginHook('submit_pre_show_post'); ?>

    <div class="show_post vote_button_space_<?php echo $hotaru->post->vars['vote_type']; ?>">
    
        <?php $hotaru->plugins->pluginHook('submit_show_post_pre_title'); ?>
        
        <div class="show_post_title"><a href='<?php echo $hotaru->post->origUrl; ?>'><?php echo $hotaru->post->title; ?></a></div>
    
        <?php if ($hotaru->post->useAuthor || $hotaru->post->useDate) { ?>
            <div class="show_post_author_date">    
                <?php echo $hotaru->lang["submit_post_posted"]; ?>
                <?php 
                if ($hotaru->post->useAuthor) { echo " " . $hotaru->lang["submit_post_by"] . " <a href='" . $hotaru->url(array('user' => $user->name)) . "'>" . $user->name . "</a>"; } 
                ?>
                <?php if ($hotaru->post->useDate) { echo time_difference(unixtimestamp($hotaru->post->date), $hotaru->lang) . " " . $hotaru->lang["submit_post_ago"]; } ?>
                <?php $hotaru->plugins->pluginHook('submit_show_post_author_date'); ?>
                <?php 
                    if ($hotaru->current_user->role == 'admin' || ($hotaru->current_user->id == $user->id)) { 
                        echo "<a class='show_post_edit' href='" . BASEURL . "index.php?page=edit_post&amp;post_id=" . $hotaru->post->id . "'>" . $hotaru->lang["submit_post_edit"] . "</a>"; 
                    }
                ?> 
            </div>
        <?php } ?>
            
        <?php if ($hotaru->post->useContent) { ?>
            <div class="show_post_content">
                <?php if ($hotaru->post->useSummary) { ?>
                    <?php echo substr(strip_tags($hotaru->post->content), 0, $hotaru->post->summaryLength) ?>
                    <?php if (strlen(strip_tags($hotaru->post->content)) >= $hotaru->post->summaryLength) { echo "..."; } ?>
                <?php } else { ?>
                    <?php echo $hotaru->post->content; ?>
                <?php } ?>    
                <small><a href='<?php echo $hotaru->url(array('page'=>$hotaru->post->id)); ?>'><?php echo $hotaru->lang['submit_post_read_more']; ?></a></small>
            </div>
        <?php } ?>
        
        <div class="show_post_extra_fields">
            <ul>
                <?php $hotaru->plugins->pluginHook('submit_show_post_extra_fields'); ?>
            </ul>
        </div>
        
        <div class="show_post_extras">
            <?php $hotaru->plugins->pluginHook('submit_show_post_extras'); ?>
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