<?php 
/**
 * Theme name: admin_default
 * Template name: header.php
 * Template author: shibuya246
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
 * @copyright Copyright (c) 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

?>
<?php header('Content-type: text/html; charset=utf-8'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
<head profile="http://gmpg.org/xfn/11">

        <title><?php echo $h->getTitle(); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
        
        <!-- Bootstrap -->
        <?php $h->getFramework(); ?>	
        
        <!-- Theme -->
        <!-- Include merged files for all the plugin css and javascript (if any) -->
	<?php $h->getThemeCss('admin'); ?>
	
	<!-- include this CSS last so it gets priority -->
        <!-- Bootstrap Responsive -->
        <?php $h->getFramework('bootstrap-responsive'); ?>

        <!-- <link rel="shortcut icon" href="<?php echo SITEURL; ?>favicon.ico"> -->
	
	<?php $h->pluginHook('admin_header_include_raw'); ?>

</head>

<body>

    <div id="wrap">
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="/"><?php echo SITE_NAME; ?></a>
          <span class="btn-navbar">
            <?php	if ($h->currentUser->loggedIn) {
				if($h->isActive('avatar')) {
					$h->setAvatar($h->currentUser->id, 24, 'g', 'img-circle');
					echo  $h->linkAvatar();
				}
			} ?>
            </span>
          <div class="nav-collapse collapse">
            <p class="navbar-text pull-right">
              <a href="#" class="navbar-link">
                  <?php echo $h->lang["admin_theme_header_hotarucms"]; ?><?php echo $h->version; ?>
              </a>
            </p>
            <ul class="nav">
              <li class="active"><a href="<?php echo $h->url(array(), 'admin'); ?>"><?php echo $h->lang["admin_theme_menu_admin_home"]; ?></a></li>
              <li><a href="<?php echo SITEURL; ?>"><?php echo $h->lang["admin_theme_menu_site_home"]; ?></a></li>
              
              <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $h->lang["admin_theme_menu_hotaru_forums"]; ?> <b class="caret"></b></a>
                  <ul class="dropdown-menu">
                    <li><a href="http://forums.hotarucms.org/">Top</a></li>
                    <li><a href="http://forums.hotarucms.org/forumdisplay.php?2-News-and-Announcements">News</a></li>
                    <li><a href="http://forums.hotarucms.org/search.php?do=getnew&contenttype=vBForum_Post">Latest</a></li>
                    <li class="divider"></li>
                    <li class="nav-header">Github</li>
                    <li><a href="http://github.com/hotarucms/hotarucms">HotaruCMS</a></li>
                    <li><a href="http://github.com/hotarucms/coreplugins">Core Plugins</a></li>
                  </ul>
                </li>
              
              <li><a href="http://docs.hotarucms.org"><?php echo $h->lang["admin_theme_menu_help"]; ?></a></li>
              <li><a href="<?php echo $h->url(array('page'=>'admin_logout'), 'admin'); ?>"><?php echo $h->lang["admin_theme_menu_logout"]; ?></a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
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
        <br/>
<?php } ?>

	
