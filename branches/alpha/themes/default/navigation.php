<?php 

/* ******* TEMPLATE ******************************************************************************** 
 * Theme name: default
 * Template name: navigation.php
 * Template author: Nick Ramsay
 * Version: 0.1
 * License:
 *
 *   This file is part of Hotaru CMS (http://www.hotarucms.org/).
 *
 *   Hotaru CMS is free software: you can redistribute it and/or modify it under the terms of the 
 *   GNU General Public License as published by the Free Software Foundation, either version 3 of 
 *   the License, or (at your option) any later version.
 *
 *   Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 *   even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License along with Hotaru CMS. If not, 
 *   see http://www.gnu.org/licenses/.
 *   
 *   Copyright (C) 2009 Hotaru CMS - http://www.hotarucms.org/
 *
 **************************************************************************************************** */

global $hotaru, $plugin, $current_user, $lang; // don't remove
?>

<ul id="navigation">
	<?php $plugin->check_actions('navigation_first'); ?>
	<li><a href="<?php echo baseurl; ?>"><?php echo $lang["main_theme_navigation_home"] ?></a></li>
	<?php $plugin->check_actions('navigation'); ?>
	<?php 
		if(!$plugin->plugin_active('users')) { 
			echo "<li><a href='" . url(array(), 'admin') . "'>";
			if($current_user->logged_in == true) { 
				echo $lang["main_theme_navigation_admin"] . "</a></li>"; 
				echo "<li><a href='" . url(array('page'=>'admin_logout'), 'admin') . "'>";
				echo $lang["main_theme_navigation_logout"] . "</a></li>";
			} else { 
				echo $lang["main_theme_navigation_login"] . "</a></li>"; 
			}
		} else {
			$plugin->check_actions('navigation_users', true, 'users'); // ensures login/logout/register are last.
		}
	?>
</ul>