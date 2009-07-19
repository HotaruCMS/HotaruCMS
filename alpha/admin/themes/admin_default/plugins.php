<?php 

/* ******* ADMIN TEMPLATE ************************************************************************** 
 * Theme name: admin_default
 * Template name: plugins.php
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
$the_plugins = $plugin->get_plugins(); // don't remove
?>

<p class="breadcrumbs"><a href="<?php echo url(array(), 'admin'); ?>"><?php echo site_name;?> Admin Control Panel</a> &raquo; Plugin Management</p>
	
<?php $hotaru->show_message(); ?>

<div id="plugin_list">

<?php $plugin->check_actions('plugins_top'); ?>

<table>

<tr class='plugins_table_headers'>
<td>Active</td>
<td>Switch</td>
<td>Plugin</td>
<td>Requires</td>
<td>Install</td>
<td>Details</td>
</tr>

<?php
	$alt = 0;
	foreach($the_plugins as $plug) {
		$alt++;

		
		echo "<tr id='plugins_tr' class='plugins_table_row_" . $alt % 2 . "'>\n";
		echo "<td class='plugins_active'>" . $plug['active'] . "</td>\n";
		echo "<td class='plugins_status'>" . $plug['status'] . "</td>\n";
		echo "<td class='plugins_name'>" . $plug['name'] . " " . $plug['version'] . "</td>\n";
		echo "<td class='plugins_requires'>" . $plug['requires'] . "</td>\n";
		echo "<td class='plugins_install'>" . $plug['install'] . "</td>\n";
		echo "<td class='plugins_details'><a class='plugin_drop_down' href='#'><img src='" . baseurl . "admin/themes/" . admin_theme . "images/info.png'></a></td>\n";
		echo "</tr>\n";
		echo "<tr id='plugins_tr_details' style='display:none;'><td colspan=5 class='plugin_description'>" . $plug['description'] . "</td>";
		echo "<td class='plugin_description_close'><a class='plugin_hide_details' href='#'>Close</a></td></tr>\n";
	}
?>
</table>
</div>
<div class="clear"></div>
<div id="plugin_management_notice" class="info_box gray_box" style="margin-top: 2.0em";>
	<p class="info_header">Plugin Management Guide</p>
	<?php $plugin->check_actions('plugins_guide_top'); ?>
	&raquo; Uninstalling a plugin will delete it from the <i>plugins</i>, <i>plugin_hooks</i> and <i>plugin_settings</i> tables.<br />
	&raquo; Any other database entries created by the plugin will not be removed.<br />
	<?php $plugin->check_actions('plugins_guide_bottom'); ?>
</div>


<?php $plugin->check_actions('plugins_bottom'); ?>
