<?php

/* ******* PLUGIN TEMPLATE ************************************************************************** 
 * Plugin name: Category Manager
 * Template name: plugins/category_manager/cat_man_edit.php
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
 
 global $hotaru, $lang, $the_cats;
 ?>
 
	<h2>Category Manager: Edit</h2>

	<table class="cat_man_table">
	<tr><td class="cat_man_body">
		Edit the names of your categories below and <b>click "Save All"</b>. <br /><br />
	
		<form name='category_manager_edit_form' action='<?php echo baseurl ?>admin/admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=edit_save' method='post'>
		<?php
		foreach($the_cats as $cat) {
			if($cat['category_name'] != "all") {
				if($cat['category_parent']  > 1) {
					for($i=1; $i<$cat['category_level']; $i++) {
						echo "--- ";
					}
					echo "<input name='" . $cat['category_id'] . "' type='text' value='" . $cat['category_name'] . "'> <span style='font-size: 0.8em; color: #888;'>(" . $cat['category_name'] . ")</span><br />";
				} else {
					echo "<input name='" . $cat['category_id'] . "' type='text' value='" . $cat['category_name'] . "'> <span style='font-size: 0.8em; color: #888;'>(" . $cat['category_name'] . ")</span><br />";
				}
			}
		}
		?>
		<br />
		<div style="text-align: center;">
			<input type='submit' name='cancel_all' value='Cancel'>&nbsp;&nbsp;<input style='padding: 2px;' type='submit' name='save_all' value='Save All'>
		</div>
		</form>
	</td>
	
	<td class="cat_man_menu_holder">
		<div>
			<h3>Navigation</h3>
			<?php $hotaru->display_template('cat_man_menu', 'category_manager'); ?>
			
			<h3>Category Tree</h3>
			<?php cat_man_tree($the_cats); ?>

		</div>
	</td>
	</tr></table>
