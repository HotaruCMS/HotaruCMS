<?php
/**
 * Template for sb_base plugin: sb_post
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

// get the user for this post:
$user = new UserBase($h);
$user->getUserBasic($h, $h->post->author);
?>

<?php $h->pluginHook('sb_base_pre_show_post'); ?>

<!-- POST -->
<div class="show_post vote_button_space" id="show_post_<?php echo $h->post->id ?>" >

    <?php $h->pluginHook('sb_base_show_post_pre_title'); ?>

    <?php   // Show avatars if enabled (requires an avatars plugin)
        if($h->isActive('avatar')) {
            $h->setAvatar($user->id, 32);
            echo $h->wrapAvatar();
        }
    ?>
        
    <div class="show_post_title">
        <?php if (!$h->vars['editorial']) { ?> 
            <a href='<?php echo $h->post->origUrl; ?>' <?php echo $h->vars['target']; ?> class="click_to_source"><?php echo $h->post->title; ?></a>
        <?php } else { ?>
            <?php echo $h->post->title; ?>
        <?php } ?>
        <?php $h->pluginHook('sb_base_show_post_title'); ?>
    </div>

    <div class="show_post_author_date">    
        <?php echo " " . $h->lang["sb_base_post_posted_by"] . " <a href='" . $h->url(array('user' => $user->name)) . "'>" . $user->name . "</a>"; ?>
        <?php echo time_difference(unixtimestamp($h->post->date), $h->lang) . " " . $h->lang["sb_base_post_ago"]; ?>
        <?php $h->pluginHook('sb_base_show_post_author_date'); ?>
        <?php
            if (($h->pageName != 'submit3') 
                && (($h->currentUser->getPermission('can_edit_posts') == 'yes') 
                || (($h->currentUser->getPermission('can_edit_posts') == 'own') && ($h->currentUser->id == $user->id)))) { 
                echo "<a class='show_post_edit' href='" . BASEURL . "index.php?page=edit_post&amp;post_id=" . $h->post->id . "'>" . $h->lang["sb_base_post_edit"] . "</a>"; 
            }
        ?> 
    </div>
        
    <?php if ($h->vars['use_content']) { ?>
        <div class="show_post_content">
            <?php echo nl2br($h->post->content); ?>
            <?php $h->pluginHook('sb_base_show_post_content_post'); ?>
        </div>
    <?php } ?>
    
    <div class="show_post_extra_fields">
        <ul>
            <?php $h->pluginHook('sb_base_show_post_extra_fields'); ?>
        </ul>
    </div>
        
    <div class="show_post_extras">
        <?php $h->pluginHook('sb_base_show_post_extras'); ?>
    </div>
    
</div>

<?php $h->pluginHook('sb_base_show_post_middle'); ?>

<?php $h->pluginHook('sb_base_post_show_post'); ?>

<!-- END POST --> 
