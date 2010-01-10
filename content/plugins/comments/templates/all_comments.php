<?php
/**
 * All comments
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
    <a id="c<?php echo $h->comment->id; ?>"></a>
    
    <div class="comment">

        <?php   // Show avatars if enabled (requires an avatars plugin)
                if ($h->comment->avatars == 'checked') {
                    if($h->isActive('avatar')) {
                        $h->setAvatar($h->comment->author, 16);
                        echo $h->wrapAvatar();
                    }
                }
        ?>
        
        <div class="comment_content">
            <?php 
                $result = $h->pluginHook('show_comments_content'); 
                if (!isset($result) || !is_array($result)) {   
                    echo nl2br($h->comment->content);
                }
            ?> 
        </div>
        
        <?php   // Show votes if enabled (requires a comment voting plugin)
                if ($h->comment->voting == 'checked') {
                    $h->pluginHook('show_comments_votes'); 
                }
        ?>
        
        <div class="comment_author_date">
            <?php 
                $username = $h->getUserNameFromId($h->comment->author);
                echo $h->lang['comments_written_by'] . " ";
                echo "<a href='" . $h->url(array('user' => $username)) . "'>" . $username . "</a>, ";
                echo time_difference(unixtimestamp($h->comment->date), $h->lang) . " " . $h->lang['comments_time_ago'];
                echo $h->lang['comments_posted_on'] . "<a href='" . $h->url(array('page'=>$h->post->id)) . "#c" . $h->comment->id . "'>" . $h->post->title . "</a>";
            ?>
            
            <?php   // EDIT LINK - (if comment owner AND permission to edit own comments) OR (permission to edit ALL comments)...
                if (($h->currentUser->id == $h->comment->author && ($h->currentUser->getPermission('can_edit_comments') == 'own'))
                    || ($h->currentUser->getPermission('can_edit_comments') == 'yes')) { ?>
                    <a href='#' class='comment_edit_link' onclick="edit_comment(
                        '<?php echo BASEURL; ?>', 
                        '<?php echo $h->comment->id; ?>', 
                        '<?php echo urlencode($h->comment->content); ?>', 
                        '<?php echo $h->lang['comments_form_edit']; ?>'); 
                        return false;" ><?php echo $h->lang['comments_edit_link']; ?></a>
            <?php } ?>
        </div>
        
        <div class="clear"></div>
            
    </div>
    
    <div class="clear"></div>
    
