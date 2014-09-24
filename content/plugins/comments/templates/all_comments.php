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
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

$comments_settings = $h->getSerializedSettings();

if (1==0) {
$display = ($h->comment->votes_down >= $h->vars['comment_hide']) ? 'display: none;' : ''; // comments are shown unless they have X negative votes
?>
    <a id="c<?php echo $h->comment->id; ?>"></a>

    <?php if ($h->comment->avatarSize < 16) {$comment_header_size=16;} else { $comment_header_size= $h->comment->avatarSize; } ?>
    <div class="comment" style="margin-left: <?php echo $h->comment->depth * 2.0; ?>em;">
        <div class="comment_header" style="height:<?php echo $comment_header_size; ?>px;">
            <div class="comment_header_left">
                <?php   // Show avatars if enabled (requires an avatars plugin)
                        if ($h->comment->avatars == 'checked') {
                            if($h->isActive('avatar')) {
                                $h->setAvatar($h->comment->author, $h->comment->avatarSize);
                                echo $h->wrapAvatar();
                            }
                        }
                ?>
                <div class="comment_author">
                <?php
                        $username = $h->getUserNameFromId($h->comment->author);
                        echo $h->lang['comments_written_by'] . " ";
                        echo "<a href='" . $h->url(array('user' => $username)) . "'>" . $username . "</a>, ";
                        echo time_difference(unixtimestamp($h->comment->date), $h->lang) . " ";                        
                        //echo time_ago($h->comment->date);                        
                        if ($display) { echo "<a href='#' class='comment_show_hide'>" . $h->lang['comments_show_hide'] . "</a>"; }
                ?>
                </div>
            </div>   <!-- close comment_header_left-->

        <?php   // Show votes if enabled (requires a comment voting plugin)
                if ($h->comment->voting == 'checked') {
                    $h->pluginHook('show_comments_votes');
                }
        ?>
        </div>  <!-- close comment_header -->

        <div class="clear"></div>

        <div class="comment_main" style="<?php echo $display; ?>">
            <div class="comment_content">
                <?php
                    $result = $h->pluginHook('show_comments_content');
                    if (!$result) {
                        echo nl2br($h->comment->content);
                    }
                ?>
                    
                <div class="comment_post_link">
                <a href="<?php echo $h->url(array('page'=>$h->post->id)); ?>">
                <?php echo $h->post->title; ?>
                </a>

            <?php 
                // EDIT LINK - (if comment owner AND permission to edit own comments) OR (permission to edit ALL comments)...
                if (($h->currentUser->id == $h->comment->author && ($h->currentUser->getPermission('can_edit_comments') == 'own'))
                  || ($h->currentUser->getPermission('can_edit_comments') == 'yes')) { 
            ?>
                <a href='#' class='comment_edit_link' onclick="edit_comment(
                    '<?php echo BASEURL; ?>',
                    '<?php echo $h->comment->id; ?>',
                    '<?php echo urlencode($h->comment->content); ?>',
                    '<?php echo $h->lang['comments_form_edit']; ?>');
                    return false;" ><?php echo $h->lang['comments_edit_link']; ?></a>
                <?php } ?>
                
                </div> <!-- close comment_post_link -->
            </div> <!-- close comment_content -->
        </div> <!-- close comment_main -->

    </div> <!-- close comment -->

    <div class="clear"></div>
    
    <?php } else { ?>
    
        <div id='activity'>
        <ul class='activity_items'>
            <?php 

                    //print_r($h->comment);
                $user_id = $h->comment->author;
                $username = $h->comment->authorname;

            ?>
        
            <li class="info-box gray">
            <div class="avatarBox">
                
                <?php if($h->comment->avatars == 'checked' && $h->isActive('avatar')) { ?>
                            <div class='avatar_small'>
                                <?php $h->setAvatar($h->comment->author, 32, 'g', 'img-circle'); echo $h->linkAvatar(); ?>
                            </div>
                        <?php } ?>
                
            </div>
            <div class="info">
                <div class="name media">
                    <div class="media-body pull-left">
                        <strong class=""><?php echo ucfirst($username); ?></strong> <?php echo nl2br($h->comment->content); ?>                        
                        <div>in <a href="<?php echo $h->url(array('page'=>$h->comment->postId)); ?>">
                              <?php echo $h->comment->postTitle; ?>
                              </a>
                          </div>
                    </div>
                        <div class="pull-right">
                            <div class="time pull-right"><i class="icon-time"></i> <?php echo time_ago($h->comment->date); ?></div>
                            <div>
                                 <?php   // Show votes if enabled (requires a comment voting plugin)
                                        if ($h->comment->voting == 'checked') {
                                            $h->pluginHook('show_comments_votes'); 
                                        }
                                ?>
                            </div>
                        </div>
                    
                  </div>
            </div>
          </li>
             
        </ul>
    </div>
     
 <?php } ?>