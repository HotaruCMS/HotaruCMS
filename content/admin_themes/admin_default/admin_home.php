<?php 
/**
 * Theme name: admin_default
 * Template name: main.php
 * Template author: Nick Ramsay
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

<table id='admin-home'>
<tr>

<td id='left'>
<!-- TITLE FOR ADMIN NEWS -->
    <h2>
        <a href="http://feeds2.feedburner.com/hotarucms"><img src="<?php echo BASEURL; ?>content/admin_themes/<?php echo ADMIN_THEME; ?>images/rss_16.png"></a>
        &nbsp;<?php echo $h->lang["admin_theme_main_latest"]; ?>
    </h2>
    
    <!-- Feed items, number to show content for, max characters for content -->
    <?php echo $h->adminNews(10, 3, 300); ?>
</td>

<td id='right'>
    <h2><?php echo SITE_NAME . " " . $h->lang["admin_theme_main_stats"]; ?></h2>
    <ul id="site-stats">
    <li>Hotaru CMS v.<?php echo $h->version; ?></li>
    <?php $h->pluginHook('admin_theme_main_stats_post_version'); ?>
    <?php $h->pluginHook('admin_theme_main_stats', 'users', array('total_users', 'admins', 'supermods', 'moderators')); ?>
    <?php $h->pluginHook('admin_theme_main_stats', 'users', array('approved_users', 'undermod_users', 'pending_users', 'banned_users', 'killspammed_users')); ?>
    <?php $h->pluginHook('admin_theme_main_stats', 'sb_base', array('total_posts', 'approved_posts', 'pending_posts', 'buried_posts', 'archived_posts')); ?>
    <?php $h->pluginHook('admin_theme_main_stats', 'comments', array('total_comments', 'approved_comments', 'pending_comments')); ?>
    </ul>
</td>

</tr>
</table>
