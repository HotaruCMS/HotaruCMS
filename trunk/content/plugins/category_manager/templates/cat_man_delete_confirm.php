<?php

/* ******* PLUGIN TEMPLATE ************************************************************************** 
 * Plugin name: Category Manager
 * Template name: plugins/category_manager/cat_man_delete_confirm.php
 *  License:
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
 
 global $hotaru, $lang, $delete_list;
 ?>
 
	<h2>Category Manager: Delete</h2>

	<table class="cat_man_table">
	<tr><td class="cat_man_body">
		<form name='category_manager_delete_confirm_form' action='<?php echo baseurl ?>admin/admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=delete_confirm' method='post'>
		You are about to delete the following categories:<br /><br />
		<?php
		$counter = 0; 
		foreach($delete_list as $del) {
			echo "<input type='hidden' name='delete_list[" . $counter . "]' value='" . $del['del_id'] . "'>";
			echo $del['del_name'] . " <span style='font-size: 0.8em; color: #888;'>(" . $del['del_id'] . ")</span><br />";
			$counter++;
		}
		?>
		<br />
		<h3>Are you sure you want to delete the above?</h3>
		<div style="float: right;">
			<input style='padding: 2px;' type='submit' name='delete_confirm_no' value='No, cancel'>&nbsp;&nbsp;<input style='padding: 2px;' type='submit' name='delete_confirm_yes' value='Yes, delete'>
		</div>
		</form>
	</td>
	</tr></table>
