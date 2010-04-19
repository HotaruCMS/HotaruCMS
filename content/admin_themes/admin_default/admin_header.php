<?php 
/**
 * Theme name: admin_default
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
 * @copyright Copyright (c) 2009, Hotaru CMS
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
	
	<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js?ver=1.4.2'></script>
	<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.0/jquery-ui.min.js?ver=1.8.0'></script>
	
	<!-- Include merged files for all the plugin css and javascript (if any) -->
	<?php $h->doIncludes(); ?>
	<!-- End -->
	
	<link rel="stylesheet" href="<?php echo BASEURL . 'content/admin_themes/' . ADMIN_THEME . 'css/reset-fonts-grids.css'; ?>" type="text/css">
	<link rel="stylesheet" href="<?php echo BASEURL . 'content/admin_themes/' . ADMIN_THEME . 'css/style.css'; ?>" type="text/css">
	<!-- <link rel="shortcut icon" href="<?php echo BASEURL; ?>favicon.ico"> -->
	
	<?php $h->pluginHook('admin_header_include_raw'); ?>

</head>
<body>
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
	<div id="hd" role="banner">
		<h1><a href="<?php echo $h->url(array(), 'admin'); ?>"><?php echo SITE_NAME . " " . $h->lang["admin"]; ?> </a></h1>
		<?php $h->pluginHook('header_post_admin_title'); ?>
		
		<!-- NAVIGATION -->
		<?php echo $h->displayTemplate('admin_navigation'); ?>
	</div>
