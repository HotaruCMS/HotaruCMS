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
 

$h->readPost($h->comment->postId);

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
                        echo $h->lang['comments_time_ago'] . ".";
                ?>
                </div>
            </div>

        <?php   // Show votes if enabled (requires a comment voting plugin)
                if ($h->comment->voting == 'checked') {
                    $h->pluginHook('show_comments_votes');
                }
        ?>
        </div>

        <div class="clear"></div>

        <div class="comment_main">
            <div class="comment_content">
                <?php
               
                    $result = $h->pluginHook('show_comments_content');
                    if (!isset($result) || !is_array($result)) {
                        echo nl2br($h->comment->content);
                        echo '<div class="comment_post_link">';
                        echo '<a href="' . $h->url(array('page'=>$h->post->id)) . '">';
                        echo $h->post->title;
                        echo '</a>';
                        echo '</div>';
                    }
                ?>
            </div>
        </div>

    </div>

    <div class="clear"></div>