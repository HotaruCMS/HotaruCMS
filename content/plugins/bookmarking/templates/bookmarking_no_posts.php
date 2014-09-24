<?php
/**
 * Template for bookmarking plugin: bookmarking_no_posts
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

$linkArray = array('popular', 'latest', 'upcoming', 'all');

?>
<div style="padding:15px 25px;" class="hero-unit">    
        <?php if (1==0) {  // !isset($postCount) || $postCount < 1 // need to find postCount for total posts not just this section?>
    
            <h2>Bookmarking with Hotaru CMS</h2>
            <p>It looks like you are just getting started with your bookmarking<p/>
            <?php if ($h->isActive('submit')) { ?>
                <p>Why not submit your first post and publish it to the homepage straight away</p>            
                <p><a href="<?php echo $h->url(array(), 'submit'); ?>" class="btn btn-primary">Submit Your First Post</a></p>
            <?php } else { ?>
                <p>You will need to turn on the 'Submit' plugin to begin adding content to your site</p>
                 <p><br/><a href="<?php echo $h->url(array(), 'admin'); ?>" class="btn btn-primary"><?php echo $h->lang['main_theme_button_admin_login']; ?></a></p>
            <?php }
        } else { ?>
            <div id='bookmarking_no_posts'>
                <h3><?php echo $h->lang('bookmarking_cant_find_anything'); ?></h3> 
                <p><?php echo $h->lang("bookmarking_extend_your_search"); ?></p>
                <ul>
                    <?php foreach ($linkArray as $link) {
                        $href = $link . '_link';
                        $name = 'bookmarking_sort_' . $link;
                        echo '<li><a href="' . $h->vars[$href] . '">' . $h->lang($name) . '</a>';
                        if ($link == 'all' && isset($h->vars['bookmarking']['filter'])) echo' <span class="label label-info">' . $h->countPostsFilter(0,0,$h->vars['bookmarking']['filter'], $h->vars['bookmarking']['filterText'], $link) . '</span>';
                            echo '</li>';
                    } ?>
                </ul>
                <?php if ($h->isActive('submit')) { ?>
                    <br/>
                    <p>You can also add new posts to this section here</p>            
                    <p><a href="<?php echo $h->url(array('page'=>'submit')); ?>" class="btn btn-primary">Submit</a></p>
            <?php } ?>
            </div>
        <?php }
?>
    </div>

