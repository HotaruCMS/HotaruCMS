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
$user->getUserBasic($post->getAuthor());
?>

<!-- BREADCRUMBS -->
<?php if($hotaru->title != 'submit2') { ?>
<div id="breadcrumbs">
    <a href="<?php echo BASEURL; ?>"><?php echo $lang['submit_form_home']; ?></a> &raquo; 
    <?php $plugins->pluginHook('breadcrumbs'); ?> 
    <?php echo $hotaru->title ?>
</div>
<?php } ?>

<?php $plugins->pluginHook('submit_post_breadcrumbs'); ?> 

<!-- POST -->
<?php if ($post->getStatus() != 'buried') { ?>

    <?php $result = $plugins->pluginHook('submit_pre_show_post'); 
        if (!isset($result) || !is_array($result)) {
        // if buried during that plugin call, the post won't show...
    ?>
    
        <div class="show_post vote_button_space_<?php echo $post->post_vars['vote_type']; ?>">
        
            <?php $plugins->pluginHook('submit_show_post_pre_title'); ?>
        
            <div class="show_post_title"><a href='<?php echo $post->getOrigUrl(); ?>'><?php echo $post->getTitle(); ?></a></div>
        
            <?php if ($post->getUseAuthor() || $post->getUseDate()) { ?>
                <div class="show_post_author_date">    
                    <?php echo $lang["submit_post_posted"]; ?>
                    <?php 
                    if ($post->getUseAuthor()) { echo " " . $lang["submit_post_by"] . " <a href='" . url(array('user' => $user->getName())) . "'>" . $user->getName() . "</a>"; }
                    ?>
                    <?php if ($post->getUseDate()) { echo time_difference(unixtimestamp($post->getDate())) . " " . $lang["submit_post_ago"]; } ?>
                    <?php $plugins->pluginHook('submit_show_post_author_date'); ?>
                    <?php
                        if (($hotaru->getTitle() != 'submit2') && ($current_user->getRole() == 'admin' || ($current_user->getId() == $user->getId()))) { 
                            echo "<a class='show_post_edit' href='" . BASEURL . "index.php?page=edit_post&amp;post_id=" . $post->getId() . "'>" . $lang["submit_post_edit"] . "</a>"; 
                        }
                    ?> 
                </div>
            <?php } ?>
                
            <?php if ($post->getUseContent()) { ?>
                <div class="show_post_content"><?php echo $post->getContent(); ?></div>
            <?php } ?>
            
            <div class="show_post_extra_fields">
                <ul>
                    <?php $plugins->pluginHook('submit_show_post_extra_fields'); ?>
                </ul>
            </div>
                
            <div class="show_post_extras">
                <?php $plugins->pluginHook('submit_show_post_extras'); ?>
            </div>
            
        </div>
        
        <?php $plugins->pluginHook('submit_show_post_middle'); ?>
        
        <?php $plugins->pluginHook('submit_post_show_post'); ?>
        
    <?php } ?>
    
<?php 
} else {
    // Show "Post buried" message...
    $hotaru->message = $lang["vote_alert_post_buried"];
    $hotaru->messageType = "red";
    $hotaru->showMessage();
}
?>

<!-- END POST --> 
