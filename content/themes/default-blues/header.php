<?php
/**
 * Theme name: Default Blues
 * Template name: header.php
 * Template author: Jason F. Irwin
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

<!-- BEGIN html head -->
<head profile="http://gmpg.org/xfn/11">
	<link rel="shortcut icon" href="<?php echo BASEURL . 'content/themes/' . THEME; ?>images/favicon.ico" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	
	<title><?php echo $h->getTitle(); ?></title>
	<?php
		// plugin hook
		$result = $h->pluginHook('header_meta');
		if (!isset($result) || !is_array($result)) { ?>
			<meta name="description" content="<?php echo $h->lang['header_meta_description']; ?>" />
			<meta name="keywords" content="<?php echo $h->lang['header_meta_keywords']; ?>" />
	<?php } ?>
	
	<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.4.0/jquery.min.js?ver=1.4.0'></script>
	<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js?ver=1.7.2'></script>

	<link rel="stylesheet" href="<?php echo BASEURL . 'content/themes/' . THEME . 'css/style.css'; ?>" type="text/css" />
	
	<!-- Include merged files for all the plugin css and javascript (if any) -->
	<?php $h->doIncludes(); ?>
	<!-- End -->
	
	<?php $h->pluginHook('header_include_raw'); ?>
   
</head>
<body>

<?php $h->pluginHook('post_open_body'); ?>

<!-- ANNOUNCEMENTS (If Available) -->
<?php if ($announcements = $h->checkAnnouncements()) { ?>
    <div id="announcement">
        <?php $h->pluginHook('announcement_first'); ?>
        <?php foreach ($announcements as $announcement) { echo $announcement . "<br />"; } ?>
        <?php $h->pluginHook('announcement_last'); ?>
    </div>
<?php } ?>

<!-- WRAPPER -->
<div id="wrapper">

	<!-- HEADER -->
	<div id="header">
		<h1><a href="<?php echo BASEURL; ?>"><?php echo SITE_NAME; ?></a></h1>

		<div class="break"></div>

		<!-- NAVIGATION -->
    	<ul>
    	<?php if ($h->pageName == 'index') { $status = "class='navigation_active'"; } else { $status = ""; } ?>
		<li <?php echo $status; ?>><a <?php echo $status; ?> href="<?php echo BASEURL; ?>"><?php echo $h->lang["main_theme_navigation_home"]; ?></a></li>
    
		<?php $h->pluginHook('navigation'); ?>

    	<?php
			if ($h->currentUser->loggedIn) { ?>
        <?php if (($h->pageType == 'user') && ($h->vars['user']->id == $h->currentUser->id)) { $status = "class='navigation_active'"; } else { $status = ""; } ?>
        	<li <?php echo $status; ?>><a <?php echo $status; ?> href='<?php echo $h->url(array('user' => $h->currentUser->name)); ?>' title='<?php echo $h->lang["users_profile"]; ?>'>
            <?php echo "My Settings"; ?>
        	</a></li>
		<?php  } ?>

		<?php 
        	if (!$h->isActive('signin')) { 

            if ($h->currentUser->loggedIn == true) { 
                if ($h->pageName == 'admin') { $status = "class='navigation_active'"; } else { $status = ""; }
                echo "<li " . $status . "><a " . $status . " href='" . $h->url(array(), 'admin') . "'>" . $h->lang["main_theme_navigation_admin"] . "</a></li>"; 

                if ($h->pageName == 'logout') { $status = "class='navigation_active'"; } else { $status = ""; }
                echo "<li " . $status . "><a " . $status . " href='" . $h->url(array('page'=>'admin_logout'), 'admin') . "'>" . $h->lang["main_theme_navigation_logout"] . "</a></li>";
            } else { 
                if ($h->pageName == 'login') { $status = "class='navigation_active'"; } else { $status = ""; }
                echo "<li " . $status . "><a " . $status . " href='" . $h->url(array(), 'admin') . "'>" . $h->lang["main_theme_navigation_login"] . "</a></li>"; 
            }
        } else {
            $h->pluginHook('navigation_users'); // ensures login/logout/register are last.
        }
	    ?>
		</ul>

		<a class="rss" href="<?php echo $h->url(array('page'=>'rss')); ?>">RSS</a>
		<p><a href="<?php echo $h->url(array('page'=>'rss')); ?>">Posts</a> | <a href="<?php echo $h->url(array('page'=>'rss_comments')); ?>">Comments</a></p>
	</div>
	<!-- End Header -->
