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

<div class="navbar navbar-inverse navbar-fixed-top" role="navigation" style="background-color: #1aaada; border:none;">   
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <div >
                <a href="#" class="navbar-brand">                    
                    <span class="small">                        
                            <?php echo $h->lang("admin_theme_header_hotarucms"); ?><?php echo $h->version; ?>
                    </span>
                </a>
            </div>
        </div>
        <div class="navbar-collapse collapse">         
            <ul class="nav navbar-nav">
                <li class="active"><a href="<?php echo $h->url(array(), 'admin'); ?>"><?php echo $h->lang("admin_theme_menu_admin_home"); ?></a></li>
                <li><a href="<?php echo SITEURL; ?>"><?php echo $h->lang("admin_theme_menu_site_home"); ?></a></li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle active" data-toggle="dropdown"><?php echo $h->lang("admin_theme_menu_help"); ?> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li class="dropdown-header"><?php echo $h->lang("admin_theme_menu_hotaru_forums"); ?></li>
                      <li><a href="http://forums.hotarucms.org/">Top</a></li>
                      <li><a href="http://forums.hotarucms.org/forums/news-and-announcements.2/">News</a></li>
                      <li><a href="http://forums.hotarucms.org/find-new/posts?recent=1">Latest</a></li>
                      <li class="divider"></li>
                      <li class="dropdown-header">Github</li>
                      <li><a href="http://github.com/hotarucms/hotarucms">HotaruCMS</a></li>
                      <li><a href="https://github.com/hotarucms/hotarucms/issues">Issue Reports</a></li>
                    </ul>
                </li>
                <li><a href="http://docs.hotarucms.org"><?php echo $h->lang("admin_theme_menu_docs"); ?></a></li>
               
            </ul> 
            <ul class="nav navbar-header navbar-nav navbar-right">
		
                <?php
                $announcements = $h->checkAnnouncements();
                
		if ($announcements != null  && $h->currentUser->adminAccess) { ?>
		    <?php if (isset($h->vars['upgradeButtonShow']) && $h->vars['upgradeButtonShow']) { ?>
                        <li>
                            <a href="/install/index.php?action=upgrade&step=1" style="padding-bottom:14px; padding-top: 14px;"><span class="btn btn-warning btn-xs">Run upgrade script</span></a>
                        </li>
                    <?php } elseif (isset($h->vars['installFilesButtonShow']) && $h->vars['installFilesButtonShow']) { ?>
                        <li >
                            <h4 style="margin-top:18px;"><span class="label label-danger"><i class="fa fa-warning"></i> Delete Install Folder</span>
                            </h4>
                        </li>
                    <?php } ?>
                    
		    <li class="dropdown">
			<a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
			    <i class="fa fa-bell"></i>  <span class="label label-danger"><?php echo count($announcements)?></span>
			</a>
			<ul class="dropdown-menu dropdown-alerts">

			    <?php
			     $h->pluginHook('admin_announcement_first');
			    foreach ($announcements as $announcement) { ?>
                                <li>
                                    <a href="/admin_index.php?page=settings">
                                        <div>
                                            <i class="fa fa-info-circle fa-fw"></i> <?php echo $announcement; ?>
        <!--                                    <span class="pull-right text-muted small">4 minutes ago</span>-->
                                        </div>
                                    </a>
                                </li>				
                                 <li class="divider"></li>
                            <?php }
			    $h->pluginHook('admin_announcement_last');
			    ?>			
			</ul>
		    </li>
		    <?php
		} else { ?>
		    <li class="">
                        <a href="#"><i class="fa fa-bell"></i></a>                  
		    </li>
		    <?php
		} ?>
		    
		<li class="dropdown">
		    <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="user-dropdown-toggle">
			<?php   if($h->isActive('avatar')) {
					    $h->setAvatar($h->currentUser->id, 20, 'g', 'img-circle');
					    echo  $h->getAvatar();                                       
				    }
				?>
		    </a>
		    <ul class="dropdown-menu">
			<li class="dropdown-caret">
			  <span class="caret-outer"></span>
			  <span class="caret-inner"></span>
			</li>

			<li class="current-user" data-name="profile">			    
			    <b class="fullname" style="padding:8px;"><?php echo $h->currentUser->name; ?></b>				      
			</li>
			<li class="divider"></li>
			<li>
			    <a href="<?php echo $h->url(array('page'=>'admin'), 'user'); ?>">Profile</a>
			</li>
			<li>                    
			    <a href="/admin_index.php?page=admin_account">Account</a>
			</li>
			<li class="divider"></li>
			<li>
			    <a href="<?php echo $h->url(array('page'=>'admin_logout'), 'admin'); ?>"><i class="fa fa-sign-out"></i>&nbsp;Sign out</a>                   
			</li>
		    </ul>
		</li>
		    
                <li>
		    <a class="" href="<?php echo SITEURL; ?>"><?php echo SITE_NAME; ?></a>
		</li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</div>
