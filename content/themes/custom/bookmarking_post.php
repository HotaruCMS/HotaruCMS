<?php
/**
 * Template for bookmarking plugin: bookmarking_post
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
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

// get the user for this post:
//$user = new UserBase($h);
//$user->getUserBasic($h, $h->post->author);
?>

<?php $h->pluginHook('pre_show_post'); ?>

<div class='hidden-xs pull-right'>
    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- hotaru-singlepost -->
<ins class="adsbygoogle"
     style="display:inline-block;width:336px;height:280px"
     data-ad-client="ca-pub-5634143173853305"
     data-ad-slot="1149159024"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
</div>

<!-- POST -->
<div class="show_post vote_button_space media" id="show_post_<?php echo $h->post->id ?>" >

    <?php $h->pluginHook('show_post_pre_title'); ?>
    <div class="media-body">
        <?php   // Show avatars if enabled (requires an avatars plugin)
            if($h->isActive('avatar')) {
                $h->setAvatar($h->post->author, 36, 'g', 'img-circle', $h->post->email, $h->post->authorname);
                echo $h->wrapAvatar();
            }
        ?>
        
        <div class="show_post_title media-heading">
            <?php if (!$h->vars['editorial']) { ?> 
                <a data-bind='text: post_title' href='<?php echo $h->post->origUrl; ?>' <?php echo $h->vars['target']; ?> class="click_to_source" rel="nofollow"><?php echo $h->post->title; ?></a>
            <?php } else { ?>
                <span data-bind='text: post_title'><?php echo $h->post->title; ?></span>
            <?php } ?>
            <?php $h->pluginHook('show_post_title'); ?>
        </div>

        <div class="show_post_author_date">    
                <?php //echo " " . $h->lang["bookmarking_post_posted_by"] . " "; ?>
                <li class="fa fa-user"></li>
                <?php 
                if ($h->post->authorname) {
                    echo "<a href='" . $h->url(array('user' => $h->post->authorname)) . "'>" . $h->post->authorname . "</a>";
                } else {
                    echo $h->lang['main_anonymous'];
                }
                ?>

                <li class="fa fa-clock-o"></li>
                <?php echo time_difference(unixtimestamp($h->post->date), $h->lang) . " " . $h->lang["bookmarking_post_ago"]; ?>
                <?php //echo time_ago($h->post->date);?>
                <?php $h->pluginHook('show_post_author_date'); ?>
                
            </div>
        
    
    
        <?php if ($h->vars['use_content']) { ?>
            <div class="show_post_content">
                <?php echo nl2br($h->post->content); ?>
                <?php if ($h->debug && $h->isAdmin()) { echo '<span class="admin_guide label label-danger" style="display:none; position:absolute; left:-140px;">show_post_content_post</span>'; } ?>            
                <?php $h->pluginHook('show_post_content_post'); ?>
            </div>
        <?php } ?>

        <div class="show_post_extra_fields">
            <ul>
                <?php if ($h->debug && $h->isAdmin()) { echo '<span class="admin_guide label label-danger" style="display:none; position:absolute; left:-140px;">show_post_extra_fields</span>'; } ?>            
                <?php $h->pluginHook('show_post_extra_fields'); ?>
                
                <?php 
                        if ($h->currentUser->getPermission('can_edit_posts') == 'yes'
                            || (($h->currentUser->getPermission('can_edit_posts') == 'own') && ($h->currentUser->id == $h->post->author))) { 
                            echo "<li class=''><a class='show_post_edit btn btn-xs btn-default' href='" . BASEURL . "index.php?page=edit_post&amp;post_id=" . $h->post->id . "'><i class='fa fa-edit'></i> " . $h->lang("bookmarking_post_edit") . "</a></li>"; 
                        }
    //                    if ($h->currentUser->getPermission('can_delete_posts') == 'yes'
    //                        || ($h->currentUser->getPermission('can_delete_posts') == 'own' && $h->currentUser->id == $h->post->author)) { 
    //                            echo "&nbsp;<a class='show_post_delete btn btn-xs btn-danger' href='" . BASEURL . "index.php?page=delete__post&amp;post_id=" . $h->post->id . "'>" . $h->lang("bookmarking_post_delete") . "</a>"; 
    //                     
    //                    }
                    ?> 
            </ul>
        </div>
    
        <div class="clear"></div>
    </div>
    <div class="show_post_extras">

        <?php if ($h->debug && $h->isAdmin()) { echo '<span class="admin_guide label label-danger" style="display:none; position:absolute; left:-140px;">show_post_extras</span>'; } ?>
        <?php $h->pluginHook('show_post_extras'); ?>
        
        <div class="hidden-xs put this in plugin :)">
<!--        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
         narrow 
        <ins class="adsbygoogle"
             style="display:inline-block;width:728px;height:90px"
             data-ad-client="ca-pub-5634143173853305"
             data-ad-slot="1346783428"></ins>
        <script>
        (adsbygoogle = window.adsbygoogle || []).push({});
        </script>-->
        </div>
        
    </div>
    
</div>

<?php if ($h->debug && $h->isAdmin()) { echo '<span class="admin_guide label label-danger" style="display:none; position:absolute; left:-80px;">show_post_middle</span>'; } ?>            
<?php $h->pluginHook('show_post_middle'); ?>

<?php if ($h->debug && $h->isAdmin()) { echo '<span class="admin_guide label label-danger" style="display:none; position:absolute; left:-80px;">post_show_post</span>'; } ?>            
<?php $h->pluginHook('post_show_post'); ?>

<!-- END POST --> 