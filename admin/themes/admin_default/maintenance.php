<?php 

/* ******* ADMIN TEMPLATE ************************************************************************** 
 * Theme name: admin_default
 * Template name: maintenance.php
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

global $plugin, $admin, $cage, $lang; // don't remove
?>

<p class="breadcrumbs"><a href="<?php echo url(array(), 'admin'); ?>"><?php echo site_name;?> Admin Control Panel</a> &raquo; Maintenance</p>

	
<h1>Hotaru Maintenance</h1>
	
<?php $plugin->check_actions('admin_maintenance_top'); ?>

<?php
	if($action = $cage->get->testAlnumLines('action')) {
		if($action == 'clear_db_cache') { $admin->clear_cache('ezSQL'); }
		if($action == 'clear_rss_cache') { $admin->clear_cache('SimplePie'); }
	}
	
?>
<br />
<ul>
	<li><a href="<?php echo url(array('page'=>'maintenance', 'action'=>'clear_db_cache'), 'admin'); ?>">Clear database cache</a> - deletes cached database queries.</li>
	<li><a href="<?php echo url(array('page'=>'maintenance', 'action'=>'clear_rss_cache'), 'admin'); ?>">Clear RSS cache</a> - deletes cached RSS feeds.</li>
</ul>	
	
<?php $plugin->check_actions('admin_maintenance_bottom'); ?>

