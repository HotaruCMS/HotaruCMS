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
   <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
   <title>
       <?php 
           if ($admin->hotaru->title != "")
           {
               echo $admin->hotaru->title . " &laquo; " . $admin->lang["admin"] . " &laquo; " . SITE_NAME;
           }
           elseif ($admin->hotaru->getPageName() != "main")
           {
               $admin->hotaru->title = $admin->hotaru->getPageName();
               echo $admin->hotaru->pageToTitleCaps($admin->hotaru->title) . " &laquo; " . $admin->lang["admin"] . " &laquo; " . SITE_NAME;
           }
           else
           { 
               echo $admin->lang["admin"] . " &laquo; " . SITE_NAME;
           } 
           
           $admin->hotaru->title = 'admin';    // highlights "Admin" in the navigation bar, for all pages in Admin
       ?>
   </title>
   <script language="JavaScript" src="<?php echo BASEURL . 'libs/extensions/jQuery/jquery.min.js'; ?>"></script>
   <script language="JavaScript" src="<?php echo BASEURL . 'libs/extensions/jQuery/jquery-ui.min.js'; ?>"></script>
   <script language="JavaScript" src="<?php echo BASEURL . 'javascript/hotaru.js'; ?>"></script>
   
    <!-- Include merged files for all the plugin css and javascript (if any) -->
    <?php 
        $version_js = $admin->hotaru->combineIncludes('js', 0, true);
        $version_css = $admin->hotaru->combineIncludes('css', 0, true);
        $admin->hotaru->includeCombined($version_js, $version_css, true);
    ?>
    <!-- End -->
    
   <link rel="stylesheet" href="<?php echo BASEURL . 'libs/extensions/YUI-CSS/reset-fonts-grids.css'; ?>" type="text/css">
   <link rel="stylesheet" href="<?php echo BASEURL . 'content/admin_themes/' . ADMIN_THEME . 'css/style.css'; ?>" type="text/css">
   <link rel="shortcut icon" href="<?php echo BASEURL; ?>favicon.ico">
   
   <?php $admin->plugins->pluginHook('admin_header_include_raw'); ?>
      
</head>
<body>
<?php 
    if ($admin->checkAdminAnnouncements() && ($admin->current_user->getPermission('can_access_admin') == 'yes')) { 
        $announcements = $admin->checkAdminAnnouncements();
?>
    <div id="announcement">
        <?php $admin->plugins->pluginHook('admin_announcement_first'); ?>
        <?php foreach ($announcements as $announcement) { echo $announcement . "<br />"; } ?>
        <?php $admin->plugins->pluginHook('admin_announcement_last'); ?>
    </div>
<?php } ?>
<div id="doc2" class="yui-t7">
    <div id="hd" role="banner">
        <h1><a href="<?php echo $admin->hotaru->url(array(), 'admin'); ?>"><?php echo SITE_NAME . " " . $admin->lang["admin"]; ?> </a></h1>
        <?php $admin->plugins->pluginHook('header_post_admin_title'); ?>
        
        <!-- NAVIGATION -->
        <?php echo $admin->displayAdminTemplate('navigation'); ?>
    </div>
