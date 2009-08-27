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

global $hotaru, $admin, $plugin, $lang; // don't remove
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
 "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
   <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
   <title>
       <?php 
           if ($hotaru->title != "")
           {
               echo $hotaru->title . " &laquo; " . $lang["admin"] . " &laquo; " . SITE_NAME;
           }
           elseif ($hotaru->get_page_name() != "main")
           {
               $hotaru->title = $hotaru->get_page_name();
               echo $hotaru->page_to_title_caps($hotaru->title) . " &laquo; " . $lang["admin"] . " &laquo; " . SITE_NAME;
           }
           else
           { 
               echo $lang["admin"] . " &laquo; " . SITE_NAME;
           } 
           
           $hotaru->title = 'admin';    // highlights "Admin" in the navigation bar, for all pages in Admin
       ?>
   </title>
   <script language="JavaScript" src="<?php echo BASEURL . '3rd_party/jQuery/jquery.min.js'; ?>"></script>
   <script language="JavaScript" src="<?php echo BASEURL . '3rd_party/jQuery/jquery-ui.min.js'; ?>"></script>
   <script language="JavaScript" src="<?php echo BASEURL . 'javascript/hotaru_ajax.js'; ?>"></script>
   <script language="JavaScript" src="<?php echo BASEURL . 'javascript/hotaru_jquery.js'; ?>"></script>
   
    <!-- Include merged files for all the plugin css and javascript (if any) -->
    <?php 
        $version_js = $hotaru->combine_includes('js');
        $version_css = $hotaru->combine_includes('css');
        $hotaru->include_combined($version_js, $version_css, $hotaru->get_page_name(), $plugin->folder);
    ?>
    <!-- End -->
    
   <link rel="stylesheet" href="<?php echo BASEURL . '3rd_party/YUI-CSS/reset-fonts-grids.css'; ?>" type="text/css">
   <link rel="stylesheet" href="<?php echo BASEURL . 'content/admin_themes/' . ADMIN_THEME . 'css/style.css'; ?>" type="text/css">
   <link rel="shortcut icon" href="<?php echo BASEURL; ?>favicon.ico">
   
   <?php $plugin->check_actions('admin_header_include_raw'); ?>
      
</head>
<body>
<?php if ($announcements = $admin->check_admin_announcements()) { ?>
    <div id="announcement">
        <?php $plugin->check_actions('admin_announcement_first'); ?>
        <?php foreach ($announcements as $announcement) { echo $announcement . "<br />"; } ?>
        <?php $plugin->check_actions('admin_announcement_last'); ?>
    </div>
<?php } ?>
<div id="doc2" class="yui-t7">
    <div id="hd" role="banner">
        <a href="<?php echo BASEURL; ?>"><img src="<?php echo BASEURL; ?>content/admin_themes/<?php echo ADMIN_THEME; ?>images/hotaru_468x60.png"></a>
        <?php $plugin->check_actions('header_post_logo'); ?>
        
        <!-- NAVIGATION -->
        <?php echo $admin->display_admin_template('navigation'); ?>
    </div>