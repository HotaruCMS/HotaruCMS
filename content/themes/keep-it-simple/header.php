<?php
/**
 * Theme name: Keep it Simple
 * Template name: header.php
 * Original Template author: Nick Ramsay
 * Original Design: Erwin Aligam
 * Original Author URI : http://www.styleshout.com/ 
 * Template author: Carlo Armanni
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
 * @author    Carlo Armanni <admin@tr3ndy.com>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.tr3ndy.com/
 */

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $hotaru->getTitle(); ?></title>
    
        <?php
            // plugin hook
            $result = $hotaru->pluginHook('header_meta');
            if (!$result) { ?>
                <meta name="description" content="<?php echo $hotaru->lang['header_meta_description']; ?>" />
                <meta name="keywords" content="<?php echo $hotaru->lang['header_meta_keywords']; ?>" />
        <?php } ?>
   
    <script type="text/javascript" src="<?php echo BASEURL . 'libs/extensions/jQuery/jquery.min.js'; ?>"></script>
    <script type="text/javascript" src="<?php echo BASEURL . 'libs/extensions/jQuery/jquery-ui.min.js'; ?>"></script>
    <script type="text/javascript" src="<?php echo BASEURL . 'javascript/hotaru.js'; ?>"></script>

    <!-- Include merged files for all the plugin css and javascript (if any) -->
    <?php $hotaru->doIncludes(); ?>
    <!-- End -->
       
    <link rel="stylesheet" href="<?php echo BASEURL . 'content/themes/' . THEME . 'css/screen.css'; ?>" type="text/css" />
    <!-- <link rel="shortcut icon" href="<?php echo BASEURL; ?>favicon.ico" /> -->
   
    <?php $hotaru->pluginHook('header_include_raw'); ?>
   
</head>
<body>
<div id="header-wrap">
<div id="header" class="container_16">

<?php $hotaru->pluginHook('post_open_body'); ?>

<?php if ($announcements = $hotaru->checkAnnouncements()) { ?>
    <div id="announcement">
        <?php $hotaru->pluginHook('announcement_first'); ?>
        <?php foreach ($announcements as $announcement) { echo $announcement . "<br />"; } ?>
        <?php $hotaru->pluginHook('announcement_last'); ?>
    </div>
<?php } ?>


        <!-- NAVIGATION -->
        <?php echo $hotaru->displayTemplate('navigation'); ?>

        <!-- TITLE & AD BLOCKS -->
		<h1 id="logo-text"><a href="<?php echo BASEURL; ?>" style="margin-top: -20px;"><?php echo SITE_NAME; ?></a></h1>		
		<p id="intro" style="margin-top: -20px;">Put your favorite slogan here...</p>			

        <!-- CATEGORIES, ETC -->
		<div id="categories_kisp"><?php $hotaru->pluginHook('post_header'); ?></div>	 

</div> <!-- Fine div header --> 
</div> <!-- Fine div header-wrap -->