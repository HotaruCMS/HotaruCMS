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

$user = new UserBase($hotaru);
$user->getUserBasic($hotaru->post->author);
if (isset($hotaru->currentUser->vars['settings']['new_tab'])) { $target = 'target="_blank"'; } else { $target = ''; }
if (strstr($hotaru->post->origUrl, BASEURL)) { $editorial = true; } else { $editorial = false; } // editorial (story with an internal link)
?>

<!-- POST -->
<?php   // This post is visible if it's not buried/pending OR if the viewer has edit post permissions...
        if ((($hotaru->post->status != 'buried') && ($hotaru->post->status != 'pending')) 
            || ($hotaru->currentUser->getPermission('can_edit_posts') == 'yes')) { ?>

    <?php $result = $hotaru->pluginHook('submit_pre_show_post'); 
        if (!isset($result) || !is_array($result)) {
        // if buried during that plugin call, the post won't show...
    ?>
    
        <div class="show_post vote_button_space">
        
            <?php $hotaru->pluginHook('submit_show_post_pre_title'); ?>
        
            <div class="show_post_title">
                <?php if (!$editorial) { ?> 
                    <a href='<?php echo $hotaru->post->origUrl; ?>' <?php echo $target; ?>><?php echo $hotaru->post->title; ?></a>
                <?php } else { ?>
                    <?php echo $hotaru->post->title; ?>
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
                        if (($hotaru->title != 'submit2') && (($hotaru->currentUser->getPermission('can_edit_posts') == 'yes') || ($hotaru->currentUser->id == $user->id))) { 
                            echo "<a class='show_post_edit' href='" . BASEURL . "index.php?page=edit_post&amp;post_id=" . $hotaru->post->id . "'>" . $hotaru->lang["submit_post_edit"] . "</a>"; 
                        }
                    ?> 
                </div>
            <?php } ?>
                
            <?php //if ($hotaru->post->useContent) { ?>
                <div class="show_post_content">
                    <?php echo nl2br($hotaru->post->content); ?>
                    <?php $hotaru->pluginHook('submit_show_post_content_post'); ?>
                </div>
            <?php //} ?>
            
            <div class="show_post_extra_fields">
                <ul>
                    <?php $hotaru->pluginHook('submit_show_post_extra_fields'); ?>
                </ul>
            </div>
                
            <div class="show_post_extras">
                <?php $hotaru->pluginHook('submit_show_post_extras'); ?>
            </div>
            
        </div>
        
        <?php $hotaru->pluginHook('submit_show_post_middle'); ?>
        
        <?php $hotaru->pluginHook('submit_post_show_post'); ?>
        
    <?php } ?>
    
<?php 
} else {
    if ($hotaru->post->status == 'pending') { 
        // Show "Post pending" message...
        $hotaru->message = $hotaru->lang["submit_post_pending"];
    } else {
        // Show "Post buried" message...
        $hotaru->message = $hotaru->lang["submit_post_buried"];
    }
    $hotaru->messageType = "red";
    $hotaru->showMessage();
}
?>

<!-- END POST --> 
