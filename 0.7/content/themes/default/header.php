<?php
/**
 * Theme name: default
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

global $hotaru, $plugins, $lang; // don't remove
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
    <meta name="Language" content="en-us" />

    <title>
       <?php 
           if ($hotaru->getTitle() != "")
           {
               echo $hotaru->getTitle() . " &laquo; " . SITE_NAME;
           }
           elseif ($hotaru->getPageName() != "main")
           {
               $hotaru->setTitle($hotaru->getPageName());
               echo $hotaru->pageToTitleCaps($hotaru->getTitle()) . " &laquo; " . SITE_NAME;
           }
           else
           { 
               $hotaru->setTitle('top'); // don't change this
               echo SITE_NAME; 
           } 
       ?>
    </title>
   
    <script language="JavaScript" src="<?php echo BASEURL . 'libs/extensions/jQuery/jquery.min.js'; ?>"></script>
    <script language="JavaScript" src="<?php echo BASEURL . 'libs/extensions/jQuery/jquery-ui.min.js'; ?>"></script>
    <script language="JavaScript" src="<?php echo BASEURL . 'javascript/hotaru_ajax.js'; ?>"></script>
    <script language="JavaScript" src="<?php echo BASEURL . 'javascript/hotaru_jquery.js'; ?>"></script>

    <!-- Include merged files for all the plugin css and javascript (if any) -->
    <?php 
        $version_js = $hotaru->combineIncludes('js');
        $version_css = $hotaru->combineIncludes('css');
        $hotaru->includeCombined($version_js, $version_css);
    ?>
    <!-- End -->
       
    <link rel="stylesheet" href="<?php echo BASEURL . 'libs/extensions/YUI-CSS/reset-fonts-grids.css'; ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo BASEURL . 'content/themes/' . THEME . 'css/style.css'; ?>" type="text/css">
    <link rel="shortcut icon" href="<?php echo BASEURL; ?>favicon.ico">
   
    <?php $plugins->pluginHook('header_include_raw'); ?>
   
</head>
<body>
<?php $plugins->pluginHook('post_open_body'); ?>

<?php if ($announcements = $hotaru->checkAnnouncements()) { ?>
    <div id="announcement">
        <?php $plugins->pluginHook('announcement_first'); ?>
        <?php foreach ($announcements as $announcement) { echo $announcement . "<br />"; } ?>
        <?php $plugins->pluginHook('announcement_last'); ?>
    </div>
<?php } ?>

<div id="doc2" class="yui-t7">
    <div id="hd">
        <div id="hd_title">
            <h1><a href="<?php echo BASEURL; ?>"><?php echo SITE_NAME; ?></a></h1>
            <?php $plugins->pluginHook('header_post_title'); ?>
        </div>
        <!-- NAVIGATION -->
        <?php echo $hotaru->displayTemplate('navigation'); ?>
    </div>
