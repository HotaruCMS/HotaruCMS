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

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <title>
       <?php 
           if ($hotaru->title != "")
           {
               echo $hotaru->title . " &laquo; " . SITE_NAME;
           }
           elseif ($hotaru->getPageName() != "main")
           {
               $hotaru->title = $hotaru->getPageName();
               echo $hotaru->pageToTitleCaps($hotaru->title) . " &laquo; " . SITE_NAME;
           }
           else
           { 
               $hotaru->title = 'top'; // don't change this
               echo SITE_NAME; 
           } 
       ?>
    </title>
    
        <?php
            // plugin hook
            $result = $hotaru->plugins->pluginHook('header_meta');
            if (!isset($result) || !is_array($result)) { ?>
                <meta name="description" content="<?php echo $hotaru->lang['header_meta_description']; ?>" />
                <meta name="keywords" content="<?php echo $hotaru->lang['header_meta_keywords']; ?>" />
        <?php } ?>
   
    <script type="text/javascript" src="<?php echo BASEURL . 'libs/extensions/jQuery/jquery.min.js'; ?>"></script>
    <script type="text/javascript" src="<?php echo BASEURL . 'libs/extensions/jQuery/jquery-ui.min.js'; ?>"></script>
    <script type="text/javascript" src="<?php echo BASEURL . 'javascript/hotaru.js'; ?>"></script>

    <!-- Include merged files for all the plugin css and javascript (if any) -->
    <?php 
        $version_js = $hotaru->combineIncludes('js');
        $version_css = $hotaru->combineIncludes('css');
        $hotaru->includeCombined($version_js, $version_css);
    ?>
    <!-- End -->
       
    <link rel="stylesheet" href="<?php echo BASEURL . 'content/themes/' . THEME . 'css/reset-fonts-grids.css'; ?>" type="text/css" />
    <link rel="stylesheet" href="<?php echo BASEURL . 'content/themes/' . THEME . 'css/style.css'; ?>" type="text/css" />
    <!-- <link rel="shortcut icon" href="<?php echo BASEURL; ?>favicon.ico" /> -->
   
    <?php $hotaru->plugins->pluginHook('header_include_raw'); ?>
   
</head>
<body>
<?php $hotaru->plugins->pluginHook('post_open_body'); ?>

<?php if ($announcements = $hotaru->checkAnnouncements()) { ?>
    <div id="announcement">
        <?php $hotaru->plugins->pluginHook('announcement_first'); ?>
        <?php foreach ($announcements as $announcement) { echo $announcement . "<br />"; } ?>
        <?php $hotaru->plugins->pluginHook('announcement_last'); ?>
    </div>
<?php } ?>

<div id="doc2" class="yui-t7">
    <div id="nav">
        <!-- NAVIGATION -->
        <?php echo $hotaru->displayTemplate('navigation'); ?>
    </div>
    <div class="clear">&nbsp;</div>
    
    <div id="hd">
        <!-- TITLE & AD BLOCKS -->
        <div id="hd_title">
            <h1><a href="<?php echo BASEURL; ?>"><?php echo SITE_NAME; ?></a></h1>
        </div>
        <div id="hd_block2">
        </div>
    </div>
    <div class="clear">&nbsp;</div>
    
    <div id="post_header">
        <!-- CATEGORIES, ETC -->
        <?php $hotaru->plugins->pluginHook('post_header'); ?>
    </div>