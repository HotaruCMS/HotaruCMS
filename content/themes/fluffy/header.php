<?php
/**
 * Theme name: default
 * Template name: header.php
 * Template author: Carlo Armanni
 * Template author website: http://www.tr3ndy.com
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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <title><?php echo $h->getTitle(); ?></title>
    
        <?php
            // plugin hook
            $result = $h->pluginHook('header_meta');
            if (!isset($result) || !is_array($result)) { ?>
                <meta name="description" content="<?php echo $h->lang['header_meta_description']; ?>" />
                <meta name="keywords" content="<?php echo $h->lang['header_meta_keywords']; ?>" />
        <?php } ?>
   
    <script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js?ver=1.4.2'></script>
    <script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js?ver=1.7.2'></script>
       
    <!-- Include merged files for all the plugin css and javascript (if any) -->
    <?php $h->doIncludes(); ?>
    <!-- End -->
    
	<link rel="stylesheet" href="<?php echo BASEURL . 'content/themes/' . THEME . 'css/reset.css'; ?>" type="text/css" />
	<link rel="stylesheet" href="<?php echo BASEURL . 'content/themes/' . THEME . 'css/960.css'; ?>" type="text/css" />
	<link rel="stylesheet" href="<?php echo BASEURL . 'content/themes/' . THEME . 'css/text.css'; ?>" type="text/css" />
	<link rel="stylesheet" href="<?php echo BASEURL . 'content/themes/' . THEME . 'css/style.css'; ?>" type="text/css" />
    <!-- <link rel="shortcut icon" href="<?php echo BASEURL; ?>favicon.ico" /> -->
   
    <?php $h->pluginHook('header_include_raw'); ?>
   
</head>
<body>
<div id="super">
<?php $h->pluginHook('post_open_body'); ?>

<?php if ($announcements = $h->checkAnnouncements()) { ?>
    <div id="announcement">
        <?php $h->pluginHook('announcement_first'); ?>
        <?php foreach ($announcements as $announcement) { echo $announcement . "<br />"; } ?>
        <?php $h->pluginHook('announcement_last'); ?>
    </div>
<?php } ?>

<div id="container" class="container_12">
	<div id="twobutton" class="grid_3 prefix_9 alpha omega"></div>
	<div class="clear"></div>
    <div id="headertop">
		<!-- LOGO -->
		<div id="logo" class="grid_4 alpha omega">&nbsp;</div>
        <!-- NAVIGATION -->
		<div id="navtop" class="grid_8 omega">
        <?php echo $h->displayTemplate('navigation'); ?>
		</div>
		<div class="clear"></div>
    </div>	