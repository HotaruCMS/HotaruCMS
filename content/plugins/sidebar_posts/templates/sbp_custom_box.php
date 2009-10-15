<?php
/**
 * Template for Sidebar Posts tabbed box
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
 
$sbp = new SidebarPosts('sidebar_posts', $hotaru);  // provides access to SidebarPosts functions

$top_posts = $sbp->getSidebarPosts('top'); // array of top stories
$new_posts =  $sbp->getSidebarPosts('new'); // array of latest stories

$top_title = $hotaru->lang['sidebar_posts_top_posts'];  // "Top Stories"
$new_title = $hotaru->lang['sidebar_posts_latest_posts']; // "Latest Stories"

/* Customize your sidebar posts box:

1. Admin -> Plugin Settings -> Sidebar Posts: Select "Custom box"
2. Admin -> Plugin Settings -> Sidebar Widgets: Deactivate "Sidebar Posts Latest"
3. Edit this file. E.g., make a box with tabs for top or latest posts.

*/

?>

<div style='border: 1px solid #999; padding: 1.0em;'>

    <h2><?php echo $top_title; ?></h2>
    
    <ul class='sidebar_widget_body sidebar_posts_items'>
        <?php echo $sbp->getSidebarPostItems($top_posts); ?>
    </ul>
    
    <h2><?php echo $new_title; ?></h2>
    
    <ul class='sidebar_widget_body sidebar_posts_items'>
        <?php echo $sbp->getSidebarPostItems($new_posts); ?>
    </ul>
    
</div>
