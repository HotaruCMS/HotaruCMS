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
$h->showMessages();
?>

<div class='journal_post_wrapper'>

	<!-- POST -->
	<div class="show_post" id="show_post_<?php echo $h->post->id ?>" >
	
	    <?php $h->pluginHook('sb_base_show_post_pre_title'); ?>
	
	    <?php   // Show avatars if enabled (requires an avatars plugin)
	        if($h->isActive('avatar')) {
	            $h->setAvatar($user->id, 32);
	            echo $h->wrapAvatar();
	        }
	    ?>
	        
	    <div class="show_post_title">
	    	<?php echo $h->post->title; ?>
	        <?php $h->pluginHook('sb_base_show_post_title'); ?>
	    </div>
	
	    <div class="show_post_author_date">    
	        <?php echo " " . $h->lang["sb_base_post_posted_by"] . " <a href='" . $h->url(array('user' => $user->name)) . "'>" . $user->name . "</a>"; ?>
	        <?php echo time_difference(unixtimestamp($h->post->date), $h->lang) . " " . $h->lang["sb_base_post_ago"]; ?>
	        <?php $h->pluginHook('sb_base_show_post_author_date'); ?>
	        <?php
	            if ($h->currentUser->id == $user->id) { ?>
	                <a href='#' class='show_post_edit' onclick="edit_post(
	                    '<?php echo $h->post->id; ?>',
	                    '<?php echo urlencode($h->post->content); ?>',
	                    '<?php echo $h->lang['journal_form_edit']; ?>');
	                    return false;" ><?php echo $h->lang['journal_edit_link']; ?></a>
	        <?php } ?> 
	    </div>
	        
	    <div class="show_post_content">
	        <?php echo nl2br($h->post->content); ?>
	        <?php $h->pluginHook('sb_base_show_post_content_post'); ?>
	    </div>
	    
	    <div class="show_post_extra_fields">
	        <ul>
				<li><a class="comment_link" href="<?php echo $h->url(array('page'=>$h->post->id, 'journal'=>$h->post->id)); ?>"><?php echo $h->countComments(false, $h->lang['comments_leave_comment']); ?></a></li>
	            <?php $h->pluginHook('sb_base_show_post_extra_fields', '', array(), array('comments', 'vote', 'updown_voting')); ?>
	        </ul>
	    </div>
	
		<?php $h->displayTemplate('journal_edit_post', 'journal', false); ?>
	
	    <div class="show_post_extras">
	        <?php $h->pluginHook('sb_base_show_post_extras'); ?>
	    </div>
	    
	</div>
	
	<?php $h->pluginHook('sb_base_show_post_middle', '', array(), array('who_voted', 'related_posts')); ?>
	
	<?php $h->pluginHook('journal_post_show_post'); ?>

	<!-- END POST --> 
</div>
