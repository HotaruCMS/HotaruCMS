<?php 

/* ******* PLUGIN TEMPLATE ************************************************************************** 
 * Plugin name: Sidebar
 * Template name: plugins/sidebar/sidebar_ordering.php
 * Template author: Nick Ramsay
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

global $hotaru, $plugin, $lang, $sidebar; // don't remove
$widgets = $sidebar->get_sidebar_widgets();	// gets and sorts plugins by "order"
$sidebars = $sidebar->get_last_sidebar($widgets);

for($i=1; $i<=$sidebars; $i++) {
?>

	<div id="plugin_list">
	
	<table>
	
	<tr class='plugins_table_installed'><td colspan=6><?php echo $lang["sidebar_ordering_title"] . " " . $i; ?> </td></tr>
	<tr class='plugins_table_headers'>
	<td><?php echo $lang["sidebar_ordering_block_enabled"]; ?></td>
	<td><?php echo $lang["sidebar_ordering_block_name"]; ?></td>
	<td><?php echo $lang["sidebar_ordering_block_order"]; ?></td>
	</tr>
	
	<?php
		$alt = 0;
		foreach($widgets as $widget => $details) {
		
			if($details['sidebar'] == $i) {
				// For the enabled button...
				if($details['enabled']) {
					$enabled_output = "<a href='" . baseurl;
					$enabled_output .= "admin/admin_index.php?page=plugin_settings&amp;plugin=sidebar&amp;";
					$enabled_output .= "action=disable&amp;widget=". $widget . "'>";
					$enabled_output .= "<img src='" . baseurl . "content/admin_themes/" . admin_theme . "images/active.png'></a>";
				} else {
					$enabled_output = "<a href='" . baseurl;
					$enabled_output .= "admin/admin_index.php?page=plugin_settings&amp;plugin=sidebar&amp;";
					$enabled_output .= "action=enable&amp;widget=". $widget . "'>";
					$enabled_output .= "<img src='" . baseurl . "content/admin_themes/" . admin_theme . "images/inactive.png'></a>";
				}
				
				// For the up and down arrows...
				$order_output = "<a href='" . baseurl;
				$order_output .= "admin/admin_index.php?page=plugin_settings&amp;plugin=sidebar&amp;";
				$order_output .= "action=orderup&amp;widget=". $widget . "&amp;args=". $details['args'] . "&amp;sidebar=" . $details['sidebar'] . "&amp;order=" . $details['order'] . "'>";
				$order_output .= "<img src='" . baseurl . "content/admin_themes/" . admin_theme . "images/up.png'>";
				$order_output .= "</a> \n<a href='" . baseurl;
				$order_output .= "admin/admin_index.php?page=plugin_settings&amp;plugin=sidebar&amp;";
				$order_output .= "action=orderdown&amp;widget=". $widget . "&amp;args=". $details['args'] . "&amp;sidebar=" . $details['sidebar'] . "&amp;order=" . $details['order'] . "'>";
				$order_output .= "<img src='" . baseurl . "content/admin_themes/" . admin_theme . "images/down.png'>";
				$order_output .= "</a>\n";
							
				$alt++;
				echo "<tr id='plugins_tr' class='plugins_table_row_" . $alt % 2 . "'>\n";
					echo "<td class='plugins_active sidebar_active'>" . $enabled_output . "</td>\n";
					echo "<td class='plugins_name'>" . $hotaru->page_to_title_caps($widget) . " </td>\n";
					echo "<td class='plugins_order sidebar_order'>" . $order_output . "</td>\n";
				echo "</tr>\n";
			}
		}
	?>
	</table>
	<br />
	</div>
	
<?php } // End of for loop ?>

<div class="clear"></div>
<div id="plugin_management_notice" class="info_box gray_box" style="margin-top: 2.0em";>
	<p class="info_header"><?php echo $lang["sidebar_ordering_guide"]?></p>
	<?php $plugin->check_actions('plugins_guide_top'); ?>
	&raquo; <?php echo $lang["sidebar_ordering_guide_1"]?><br />
	<?php $plugin->check_actions('sidebar_ordering_guide_bottom'); ?>
</div>