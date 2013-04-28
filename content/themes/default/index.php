<?php 
/**
 * name: Default
 * description: Default theme for Hotaru CMS
 * version: 0.2
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
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2010, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://hotarucms.org/
 */

// set a custom home page:
$h->setHome();

// get language
$h->includeThemeLanguage();

// get settings:
$h->vars['theme_settings'] = $h->getThemeSettings();

// plugins work here before anything is displayed. Return if overriding.
if ($h->pluginHook('theme_index_top')) { return false; };

// display header if not overriden by a plugin
if (!$h->pluginHook('theme_index_header')) { $h->displayTemplate('header'); }
?>

<body>

	<?php $h->pluginHook('post_open_body'); ?>	
	<?php if ($announcements = $h->checkAnnouncements()) { ?>
		<div id="announcement">
			<?php $h->pluginHook('announcement_first'); ?>
			<?php foreach ($announcements as $announcement) { echo $announcement . "<br />"; } ?>
			<?php $h->pluginHook('announcement_last'); ?>
		</div>
	<?php } ?>

	
        <!-- NAVIGATION -->
        <?php echo $h->displayTemplate('navigation'); ?>
	
		
	<div class="container-fluid">
            <div class="row-fluid">
		<!-- TITLE & AD BLOCKS -->
                <!--
		<div id="hd_title">
			<h1><a href="<?php //echo SITEURL; ?>"><?php //echo SITE_NAME; ?></a></h1>
			<h3 class="subtitle"><?php //echo $h->vars['theme_settings']['tagline']; ?></h3>
		</div>
                -->
		
            
		
                <div id="header_end" class="">
                        <!-- CATEGORIES, ETC -->
                        <?php $h->pluginHook('header_end'); ?>
                </div>
                
            

		<div id="content">

			<?php $width = ($h->sidebars) ? '8' : '12'; ?>
			<div id="main_container" class="span<?php echo $width; ?>">
				<div id="main">

					<!-- BREADCRUMBS -->
					<ul class='breadcrumb'>
						<?php echo $h->breadcrumbs("/"); ?>
					</ul>
					
					<!-- POST BREADCRUMBS -->
					<?php $h->pluginHook('theme_index_post_breadcrumbs'); ?>
					
					<!-- FILTER TABS -->
					<?php $h->pluginHook('theme_index_pre_main'); ?>
					
					<!-- MAIN -->
					<?php if (!$h->pluginHook('theme_index_main')) { $h->displayTemplate($h->pageName); } ?>
                                        <?php 
                                        
                                        $postCount = $h->postStats('total');
                                        
                                        if ($postCount && $postCount < 1) { ?>
                                            <div style="padding:15px 25px;" class="hero-unit">
                                                    <h2>Welcome to Hotaru CMS</h2>
                                                    <p>It looks like you are just getting started with your new Hotaru CMS website. Why not submit your first post and publish it to the homepage straight away.</p>
                                                    <p><a href="/submit/" class="btn btn-primary">Submit Your First Post</a></p>
                                            </div>
                                        <?php } ?>
                                        
					<div class="clear"></div>
				</div>
			</div>

			<!-- SIDEBAR -->
			<?php if ($h->sidebars) { ?>
                            <?php if (!$h->pluginHook('theme_index_sidebar')) { $h->displayTemplate('sidebar'); } ?>					
			<?php } ?>

		</div> <!-- close "content" -->
                
            </div>

            <hr/>
		<!-- FOOTER -->
		<footer>
			<?php if (!$h->pluginHook('theme_index_footer')) { $h->displayTemplate('footer'); } ?>
		</footer>
        </div>
	

	<?php $h->pluginHook('pre_close_body'); ?>

</body>
</html>