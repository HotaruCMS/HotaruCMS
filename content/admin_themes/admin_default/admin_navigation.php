<?php 
/**
 * Theme name: admin_default
 * Template name: navigation.php
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
 * @copyright Copyright (c) 2010, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
?>

<ul id="navigation">
	<?php
		if ($h->currentUser->loggedIn) {
			if($h->isActive('avatar')) {
				$h->setAvatar($h->currentUser->id, 16);
				echo $h->linkAvatar();
			}
		} ?>
	
	<li><a href="<?php echo BASEURL; ?>"><?php echo $h->lang["admin_theme_navigation_home"]; ?></a></li>
	<?php $h->pluginHook('navigation'); ?>
	<?php 
		if (!$h->isActive('signin')) { 
			if ($h->currentUser->loggedIn == true) { 
				echo "<li><a id='navigation_active' href='" . $h->url(array(), 'admin') . "'>" . $h->lang["admin_theme_navigation_admin"] . "</a></li>"; 
				echo "<li><a href='" . $h->url(array('page'=>'admin_logout'), 'admin') . "'>" . $h->lang["admin_theme_navigation_logout"] . "</a></li>";
			} else { 
				echo "<li><a href='" . $h->url(array(), 'admin') . "'>" . $h->lang["admin_theme_navigation_login"] . "</a></li>"; 
			}
		} else {
			$h->pluginHook('navigation_users'); // ensures login/logout/register are last.
		}
	?>
</ul>
