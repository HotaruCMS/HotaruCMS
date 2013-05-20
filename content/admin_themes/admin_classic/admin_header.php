<?php 
/**
 * Theme name: admin_classic
 * Template name: header.php
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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
 "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv=Content-Type content="text/html; charset=UTF-8" />
	
	<title><?php echo $h->getTitle(); ?></title>
	
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
	
        
	<!-- Include merged files for all the plugin css and javascript (if any) -->
	<?php $h->doIncludes(); ?>
	<!-- End -->
	
	<link rel="stylesheet" href="<?php echo SITEURL . 'content/admin_themes/' . ADMIN_THEME . 'css/reset-fonts-grids.css'; ?>" type="text/css">
	<link rel="stylesheet" href="<?php echo SITEURL . 'content/admin_themes/' . ADMIN_THEME . 'css/style.css'; ?>" type="text/css">
	<!-- <link rel="shortcut icon" href="<?php echo SITEURL; ?>favicon.ico"> -->
	
	<?php $h->pluginHook('admin_header_include_raw'); ?>

</head>
<body>


    <div class="admin-header">
	<div class="admin-frame">
            	<div class="admin-header-title">
                    <img class="admin-header-logo" src="<?php echo SITEURL; ?>content/admin_themes/admin_classic/images/hotaru-80px.png"/>
            		<div class="admin-header-version"><?php echo $h->lang("admin_theme_header_hotarucms"); ?><?php echo $h->version; ?></div>
			<div class="admin-header-admin"><a href="<?php echo $h->url(array(), 'admin'); ?>"><?php echo $h->lang("admin_theme_header_admin"); ?></a></div>
            	</div>
            	<div class="admin-top-menu">
		    <div class="admin-menu-item">
			<a href="<?php echo SITEURL; ?>">
			    <div class="admin-menu-item-icon">
				<img src="<?php echo SITEURL; ?>content/admin_themes/admin_classic/images/home_icon.png" />
			    </div>
			    <div class="admin-menu-item-text"><?php echo $h->lang("admin_theme_menu_site_home"); ?></div>
			</a>
		    </div>
			<?php if ($h->currentUser->getPermission('can_access_admin') == 'yes') { ?>
			    <div  class="admin-menu-item">
				<a href="<?php echo $h->url(array(), 'admin'); ?>">
				    <div class="admin-menu-item-icon">
					<img src="<?php echo SITEURL; ?>content/admin_themes/admin_classic/images/admin_icon.png">
				    </div>
				    <div class="admin-menu-item-text"><?php echo $h->lang("admin_theme_menu_admin_home"); ?></div>
				</a>
			    </div>
			<?php } ?>
		    <div class="admin-menu-item">
			<a href="http://forums.hotarucms.org">
			    <div class="admin-menu-item-icon">
				<img src="<?php echo SITEURL; ?>content/admin_themes/admin_classic/images/forum_icon.png "/>
			    </div>
			    <div class="admin-menu-item-text"><?php echo $h->lang("admin_theme_menu_hotaru_forums"); ?></div>
			</a>
		    </div>

		    <div class="admin-menu-item">
			<a href="http://docs.hotarucms.org">
			    <div class="admin-menu-item-icon">
				<img src="<?php echo SITEURL; ?>content/admin_themes/admin_classic/images/docs_icon.png" />
			    </div>
			    <div class="admin-menu-item-text"><?php echo $h->lang("admin_theme_menu_help"); ?></div>
			</a>
		    </div>
			<?php if ($h->currentUser->loggedIn) { ?>
			    <div class="admin-menu-item">
				<a href="<?php echo $h->url(array('page'=>'admin_logout'), 'admin'); ?>">
				    <div class="admin-menu-item-icon">
					<img src="<?php echo SITEURL; ?>content/admin_themes/admin_classic/images/logout_icon.png" />
				    </div>
				    <div class="admin-menu-item-text"><?php echo $h->lang("admin_theme_menu_logout"); ?></div>
				</a>
			    </div>
			<?php } ?>
		</div>
	    <div class="clear_both">&nbsp;</div>
        </div>
    </div>


<?php
	$announcements = $h->checkAnnouncements();
	if ($announcements && ($h->currentUser->getPermission('can_access_admin') == 'yes')) { 
	?>
	<div id="announcement">
		<?php $h->pluginHook('admin_announcement_first'); ?>
		<?php foreach ($announcements as $announcement) { echo $announcement . "<br />"; } ?>
		<?php $h->pluginHook('admin_announcement_last'); ?>
	</div>
<?php } ?>
<div id="doc2" class="yui-t7">
<!--	<div id="hd" role="banner">
		<h1>
		 <?php   if($h->isActive('avatar')) {
                    $h->setAvatar($h->currentUser->id, 16);
					echo $h->linkAvatar();
                } ?>
		    &nbsp;<a href="<?php echo $h->url(array(), 'admin'); ?>"><?php echo SITE_NAME; ?> </a></h1>
		<?php $h->pluginHook('header_post_admin_title'); ?>
		
		 NAVIGATION 
		<?php //echo $h->template('admin_navigation'); ?>
	</div>-->

	<br/>
