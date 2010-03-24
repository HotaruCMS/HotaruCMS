<?php
/**
 * Category Headlines Box
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
 * @author    shibuya246 <blog@shibuya246.com>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
 
?>
<link type="text/css" href="<?php echo BASEURL; ?>content/plugins/category_headlines/css/category_headlines.css" rel="stylesheet">
 


<div class="category_headlines_box">
    <div class="category_headlines_box_title"><h3>Category Name</h3><div class="more">more</div></div>
    <div class="clear"></div>
    <?php
        foreach($h->vars['category_headlines']['posts'] as $post) {
            $h->readPost(0,$post);
            ?>
            <div class="category_headlines_post">
               <a href='<?php echo $h->url(array('page'=>$h->post->id)); ?>' title='<?php echo $h->post->title; ?>'>
                    <?php echo $h->post->title; ?>
               </a><br/><br/>
            </div>
            <?php
        }     
    ?>
    

</div>
