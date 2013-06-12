<?php 
/**
 * name: Default
 * description: Default theme for Hotaru CMS
 * version: 0.1
 * author: shibuya246
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
 * @author    shibuya246 <admin@hotarucms.org>
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

// check whether we have the fluid setting. If not make false
$fluid = isset($h->vars['theme_settings']['fullWidth']) ? '-fluid' : '';

// check for span from settings. if none then make default of 9
$leftSpan = isset($h->vars['theme_settings']['leftSpan']) ? $h->vars['theme_settings']['leftSpan'] : 9;

$width = ($h->sidebars) ? $leftSpan : 12;
$sideBarWidth = 12 - $leftSpan;

?>

<body>

	<?php $h->pluginHook('post_open_body'); ?>	

        <!-- NAVIGATION -->
        <?php echo $h->template('navigation'); ?>
	
        <?php if ($announcements) { ?>
		<div id="announcement">
			<?php $h->pluginHook('announcement_first'); ?>
			<?php foreach ($announcements as $announcement) { echo $announcement . "<br/>"; } ?>
			<?php $h->pluginHook('announcement_last'); ?>
		</div>
	<?php } ?>
		
        <div id="header_end">
            <?php if (!$h->isActive('categories')) echo '<br/>'; ?>
                <!-- CATEGORIES, ETC --> 
                <?php $h->pluginHook('header_end'); ?>
        </div>
        
	<div class="container<?php echo $fluid; ?>">
            <div class="row<?php echo $fluid; ?>">                

		<div id="content">
			
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
					<?php if (!$h->pluginHook('theme_index_main')) { $h->template($h->pageName, 'pages'); } ?>

					<div class="clear"></div>
				</div>
			</div>

			<!-- SIDEBAR -->
			<?php if ($h->sidebars) { ?>
                            <div class="span<?php echo $sideBarWidth; ?>">
                            <?php if (!$h->pluginHook('theme_index_sidebar')) { $h->template('sidebar'); } ?>					
                            </div>
                        <?php } ?>

		</div> <!-- close "content" -->
                
            </div>

            <hr/>
		<!-- FOOTER -->
		<footer>
			<?php if (!$h->pluginHook('theme_index_footer')) { $h->template('footer'); } ?>
		</footer>
        </div>
	

	<?php $h->pluginHook('pre_close_body'); ?>

</body>
</html>
