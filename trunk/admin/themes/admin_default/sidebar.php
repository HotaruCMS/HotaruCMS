<?php 

/* ******* ADMIN TEMPLATE ************************************************************************** 
 * Theme name: admin_default
 * Template name: sidebar.php
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

global $hotaru, $plugin; // don't remove
?>
<?php $plugin->check_actions('admin_sidebar_top'); ?>

<ul id="sidebar">
	<li><a href="<?php echo url(array(), 'admin'); ?>">Admin Home</a></li>
	<li><a href="<?php echo url(array('page' => 'settings'), 'admin'); ?>">Settings</a></li>
	<li><a href="<?php echo url(array('page' => 'plugins'), 'admin'); ?>">Plugin Management</a></li>
	<li><a class="dropdown" href="#">Plugin Settings</a></li>
	<ul id="plugin_settings_list" style="display: none;">
		<?php $plugin->check_actions('admin_sidebar_plugin_settings'); ?>
	</ul>
	
	<?php $plugin->check_actions('admin_sidebar'); ?>
</ul>

<?php $plugin->check_actions('admin_sidebar_bottom'); ?>