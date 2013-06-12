<?php 
/**
 * Theme name: classic
 * Template name: navigation.php
 * Template author: Nick Ramsay
 *
 * This file looks a bit ugly because whitespace between LI tags 
 * renders as spaces, so I had to squash all the lines together
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
 * @link      http://hotarucms.org/
 */
?>

<!-- Navigation Bar -->
<ul class="navigation">
	<?php	if ($h->currentUser->loggedIn) {
				if($h->isActive('avatar')) {
					$h->setAvatar($h->currentUser->id, 16);
					echo '<li>' . $h->linkAvatar() . '</li>';
				}
			} ?>
<?php if ($h->pageName == $h->home) { $status = "id='navigation_active'"; } else { $status = ""; } ?>
<li <?php echo $status; ?>><a href="<?php echo SITEURL; ?>"><?php echo $h->lang("main_theme_navigation_home"); ?></a></li><?php $h->pluginHook('navigation'); ?><?php 
		if (!$h->isActive('signin')) { 
		
			if ($h->currentUser->loggedIn == true) { 							
				echo "<li " . $status . "><a href='" . $h->url(array(), 'admin') . "'>" . $h->lang("main_theme_navigation_admin") . "</a></li>";
			
				if ($h->pageName == 'logout') { $status = "id='navigation_active'"; } else { $status = ""; }
				echo "<li " . $status . "><a href='" . $h->url(array('page'=>'admin_logout'), 'admin') . "'>" . $h->lang("main_theme_navigation_logout") . "</a></li>";
			} else { 
				if ($h->pageName == 'login') { $status = "id='navigation_active'"; } else { $status = ""; }
				echo "<li " . $status . "><a href='" . $h->url(array(), 'admin') . "'>" . $h->lang("main_theme_navigation_login") . "</a></li>";
			}
		} else {
			$h->pluginHook('navigation_users'); // ensures login/logout/register are last.
		}
	?>
</ul>

<ul class="navigation nav_right">
	<?php     // RSS Link and icon if a type "base" plugin is active
		if ($h->isActive('base')) { ?>
		<li>
		<a href="<?php echo $h->url(array('page'=>'rss')); ?>">RSS 
			<img id="rss_icon" src="<?php echo SITEURL; ?>content/themes/<?php echo THEME; ?>images/rss_16.png" alt="RSS" />
		</a>
		</li>
	<?php } ?>
</ul>

<div class="clear"></div>