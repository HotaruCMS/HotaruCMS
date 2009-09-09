<?php
/**
 * Template for Submit: POST
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

global $hotaru, $plugins, $post, $current_user, $lang;

$user = new UserBase();
$user->get_user_basic($post->getAuthor());
?>

<!-- BREADCRUMBS -->
<?php if($hotaru->title != 'submit2') { ?>
<div id="breadcrumbs">
    <a href="<?php echo BASEURL; ?>"><?php echo $lang['submit_form_home']; ?></a> &raquo; 
    <?php $plugins->checkActions('breadcrumbs'); ?> 
    <?php echo $hotaru->title ?>
</div>
<?php } ?>

<!-- POST -->
<?php if ($post->getStatus() != 'buried') { ?>

    <?php $result = $plugins->checkActions('submit_pre_show_post'); 
        if (!isset($result) || !is_array($result)) {
        // if buried during that plugin call, the post won't show...
    ?>
    
        <div class="show_post vote_button_space_<?php echo $post->post_vars['vote_type']; ?>">
        
            <?php $plugins->checkActions('submit_show_post_pre_title'); ?>
        
            <div class="show_post_title"><a href='<?php echo $post->getOrigUrl(); ?>'><?php echo $post->getTitle(); ?></a></div>
        
            <?php if ($post->getUseAuthor() || $post->getUseDate()) { ?>
                <div class="show_post_author_date">    
                    Posted
                    <?php 
                    if ($post->getUseAuthor()) { echo " by <a href='" . url(array('user' => $user->username)) . "'>" . $user->username . "</a>"; } 
                    ?>
                    <?php if ($post->getUseDate()) { echo time_difference(unixtimestamp($post->getDate())) . " ago"; } ?>
                    <?php $plugins->checkActions('submit_show_post_author_date'); ?>
                    <?php 
                        if ($current_user->role == 'admin' || ($current_user->id == $user->id)) { 
                            echo "<a class='show_post_edit' href='" . url(array('page'=>'edit_post', 'post_id'=>$post->getId())) . "'>" . $lang["submit_post_edit"] . "</a>"; 
                        }
                    ?> 
                </div>
            <?php } ?>
                
            <?php if ($post->getUseContent()) { ?>
                <div class="show_post_content"><?php echo $post->getContent(); ?></div>
            <?php } ?>
            
            <div class="show_post_extra_fields">
                <ul>
                    <?php $plugins->checkActions('submit_show_post_extra_fields'); ?>
                </ul>
            </div>
                
            <div class="show_post_extras">
                <?php $plugins->checkActions('submit_show_post_extras'); ?>
            </div>
            
        </div>
        
        <?php $plugins->checkActions('submit_show_post_middle'); ?>
        
        <?php $plugins->checkActions('submit_post_show_post'); ?>
        
    <?php } ?>
    
<?php 
} else {
    // Show "Post buried" message...
    $hotaru->message = $lang["vote_alert_post_buried"];
    $hotaru->message_type = "red";
    $hotaru->show_message();
}
?>

<!-- END POST --> 