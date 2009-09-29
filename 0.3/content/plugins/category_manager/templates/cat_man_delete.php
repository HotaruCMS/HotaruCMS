<?php

/* ******* PLUGIN TEMPLATE ************************************************************************** 
 * Plugin name: Category Manager
 * Template name: plugins/category_manager/cat_man_delete.php
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
 
 	<h2><?php echo $lang["cat_man_delete"] ?></h2>

	<table class="cat_man_table">
	<tr><td class="cat_man_body">
		<?php echo $lang["cat_man_delete_instruct"] ?>
		<h3><i><?php echo $lang["cat_man_delete_notes"] ?></i></h3>
		1. <?php echo $lang["cat_man_delete_note1"] ?> <br />
		2. <?php echo $lang["cat_man_delete_note2"] ?><br /><br />
	
		<form name='category_manager_delete_form' action='<?php echo baseurl ?>admin/admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=delete_save' method='post'>
		<?php 
		foreach($the_cats as $cat) {
			if($cat['category_name'] != "all") {
			
				if($cat['category_empty']) {	// safe to delete
					echo "<input type='checkbox' name='delete_cats[" . $cat['category_id'] . "]' value='" . $cat['category_id']. "'>&nbsp;&nbsp;";
				} else {
					echo "<input type='checkbox' name='delete_cats[" . $cat['category_id'] . "]' value='" . $cat['category_id']. "' DISABLED>&nbsp;&nbsp;";
				}
				
				if($cat['category_parent'] > 1) {
					for($i=1; $i<$cat['category_level']; $i++) {
						echo "--- ";
					}
				}
				
				if($cat['category_empty']) {
					echo $cat['category_name'] . " <span style='font-size: 0.8em; color: #888;'>(" . $cat['category_id'] . ")</span><br />";
				} else {
					echo "<span style='color: #888;'>" . $cat['category_name'] . "</span> <span style='font-size: 0.8em; color: #888;'>(" . $cat['category_id'] . ")</span><br />";
				}
			}
		}
		?>
		<br />
		<div style="text-align: center;">
			<input type='submit' name='cancel_all' value='<?php echo $lang["cat_man_cancel"] ?>'>&nbsp;&nbsp;<input type='submit' name='delete' value='<?php echo $lang["cat_man_delete_selected"] ?>'>
		</div>
		</form>
	</td>
	
	<td class="cat_man_menu_holder">
		<div>
			<h3><?php echo $lang["cat_man_menu_title"] ?></h3>
			<?php $hotaru->display_template('cat_man_menu', 'category_manager'); ?>
			
			<h3><?php echo $lang["cat_man_category_tree"] ?></h3>
			<?php cat_man_tree($the_cats); ?>

		</div>
	</td>
	</tr></table>