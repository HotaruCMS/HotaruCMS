<?php 
/**
 * Theme name: admin_classic
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
 * @copyright Copyright (c) 2010, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

?>

<table id='admin-home'>
<tr>

<td id='left'>
	 
<!-- TITLE FOR ADMIN NEWS -->
	<h2>
		<a href="http://feeds2.feedburner.com/hotarucms"><img src="<?php echo SITEURL; ?>content/admin_themes/<?php echo ADMIN_THEME; ?>images/rss_16.png" alt="rss" /></a>
		&nbsp;<?php echo $h->lang("admin_theme_main_latest"); ?>
	</h2>
	
	<h3><?php echo $h->lang("admin_theme_main_help"); ?></h3>
	
	<!-- Feed items, number to show content for, max characters for content -->
	<?php echo $h->adminNews(10, 3, 300); ?>
	
	<br/>
	 <h2><?php echo $h->lang("admin_theme_main_join_us"); ?></h2>
</td>

<td id='right'>
	<h2><?php echo SITE_NAME . " " . $h->lang("admin_theme_main_stats"); ?></h2>
	<ul id="site-stats">
		<li>Hotaru CMS <?php echo $h->version; ?></li>

		<?php $h->pluginHook('admin_theme_main_stats_post_version'); ?>
		<?php $h->pluginHook('admin_theme_main_stats', 'users', array('users' => array('all', 'admin', 'supermod', 'moderator', 'member', 'undermod', 'pending', 'banned', 'killspammed'))); ?>
		<?php $h->pluginHook('admin_theme_main_stats', 'post_manager', array('posts' => array('all', 'approved', 'pending', 'buried', 'archived'))); ?>
		<?php $h->pluginHook('admin_theme_main_stats', 'comments', array('comments' => array('all', 'approved', 'pending', 'archived'))); ?>
	</ul>
</td>

</tr>
</table>
