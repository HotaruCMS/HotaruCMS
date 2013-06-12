<?php 
/**
 * name: classic
 * description: Classic default theme for Hotaru CMS
 * version: 0.3
 * author: Nick Ramsay
 * authorurl: http://hotarucms.org/member.php?1-Nick
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
 * @link      http://hotarucms.org/
 */

// set a custom home page:
$h->setHome();

// get language
$h->includeThemeLanguage();

// get announcements
$announcements = $h->checkAnnouncements();

// get settings:
$h->vars['theme_settings'] = $h->getThemeSettings();

// plugins work here before anything is displayed. Return if overriding.
if ($h->pluginHook('theme_index_top')) { return false; };

// display header if not overriden by a plugin
if (!$h->pluginHook('theme_index_header')) { $h->template('header'); }
?>

<body>

	<?php $h->pluginHook('post_open_body'); ?>	
	<?php if ($announcements) { ?>
		<div id="announcement">
			<?php $h->pluginHook('announcement_first'); ?>
			<?php foreach ($announcements as $announcement) { echo $announcement . "<br />"; } ?>
			<?php $h->pluginHook('announcement_last'); ?>
		</div>
	<?php } ?>

	<div class="container_12">

	<div id="navigation" class="grid_12">
		<!-- NAVIGATION -->
		<?php echo $h->template('navigation'); ?>
	</div>
		
	<div id="header" class="grid_12">
		<!-- TITLE & AD BLOCKS -->
		<div id="hd_title">
			<h1><a href="<?php echo SITEURL; ?>"><?php echo SITE_NAME; ?></a></h1>
			<h3 class="subtitle"><?php echo $h->vars['theme_settings']['tagline']; ?></h3>
		</div>
		<div class="clear"></div>
	</div>
		
	<div id="header_end" class="grid_12">
		<!-- CATEGORIES, ETC -->
		<?php $h->pluginHook('header_end'); ?>
	</div>

		<div id="content">

			<?php $width = ($h->sidebars) ? '8' : '12'; ?>
			<div id="main_container" class="grid_<?php echo $width; ?>">
				<div id="main">

					<!-- BREADCRUMBS -->
					<div id='breadcrumbs'>
						<?php echo $h->breadcrumbs(); ?>
					</div>
					
					<!-- POST BREADCRUMBS -->
					<?php $h->pluginHook('theme_index_post_breadcrumbs'); ?>
					
					<!-- FILTER TABS -->
					<?php $h->pluginHook('theme_index_pre_main'); ?>
					
					<!-- MAIN -->
					<?php if (!$h->pluginHook('theme_index_main')) { $h->template($h->pageName, 'pages'); } ?>

					<div class="clear"></div>
				</div>
			</div>

			<!-- SIDEBAR -->
			<?php if ($h->sidebars) { ?>
				<div id="sidebar_container" class="grid_4">
					<div id="sidebar">
						<?php if (!$h->pluginHook('theme_index_sidebar')) { $h->template('sidebar'); } ?>
					</div>
				</div> 
			<?php } ?>

		</div> <!-- close "content" -->


		<!-- FOOTER -->
		<div id="footer" class="grid_12">
			<?php if (!$h->pluginHook('theme_index_footer')) { $h->template('footer'); } ?>
		</div>

	</div> <!-- close "container_12" -->

	<?php $h->pluginHook('pre_close_body'); ?>

</body>
</html>