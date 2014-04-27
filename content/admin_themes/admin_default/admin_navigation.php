<?php 
/**
 * Theme name: admin_default
 * Template name: navigation.php
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
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
?>

<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">   
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <div >
                <a class="navbar-brand" href="<?php echo SITEURL; ?>"><?php echo SITE_NAME; ?></a>
                <span class="navbar-brand btn-navbar">
                  <?php	if ($h->currentUser->loggedIn) {
                                      if($h->isActive('avatar')) {
                                              $h->setAvatar($h->currentUser->id, 24, 'g', 'img-circle');
                                              echo  $h->linkAvatar();
                                      }
                              } ?>
                </span>
            </div>
        </div>
        <div class="navbar-collapse collapse">         
            <ul class="nav navbar-nav">
                <li class="active"><a href="<?php echo $h->url(array(), 'admin'); ?>"><?php echo $h->lang("admin_theme_menu_admin_home"); ?></a></li>
                <li><a href="<?php echo SITEURL; ?>"><?php echo $h->lang("admin_theme_menu_site_home"); ?></a></li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $h->lang("admin_theme_menu_hotaru_forums"); ?> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                      <li><a href="http://forums.hotarucms.org/">Top</a></li>
                      <li><a href="http://forums.hotarucms.org/forums/news-and-announcements.2/">News</a></li>
                      <li><a href="http://forums.hotarucms.org/find-new/posts?recent=1">Latest</a></li>
                      <li class="divider"></li>
                      <li class="dropdown-header">Github</li>
                      <li><a href="http://github.com/hotarucms/hotarucms">HotaruCMS</a></li>
                      <li><a href="http://github.com/hotarucms/coreplugins">Core Plugins</a></li>
                    </ul>
                </li>
                <li><a href="http://docs.hotarucms.org"><?php echo $h->lang("admin_theme_menu_help"); ?></a></li>
                <li><a href="<?php echo $h->url(array('page'=>'admin_logout'), 'admin'); ?>"><?php echo $h->lang("admin_theme_menu_logout"); ?></a></li>
            </ul>
            <div class="navbar-header navbar-right">
                <a href="#" class="navbar-brand">
                    <span class="small"><?php echo $h->lang("admin_theme_header_hotarucms"); ?><?php echo $h->version; ?></span>
                </a>
            </div>
        </div><!--/.nav-collapse -->
    </div>
</div>