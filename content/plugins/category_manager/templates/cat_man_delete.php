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
 
 global $lang, $the_cats;
 ?>
 
 	<h2>Category Manager: Delete</h2>

	<table class="cat_man_table">
	<tr><td class="cat_man_body">
		Check the boxes below for the categories you wish to delete.
		<h3><i>Notes:</i></h3>
		1. If a category contains links, it can't be deleted and is grayed out. <br />
		2. If you delete a category with children, they will be assigned to their grandparent or otherwise become top-level categories.<br /><br />
	
		<form name='category_manager_delete_form' action='<?php echo url(array('page'=>'plugin_settings', 'plugin'=>'category_manager', 'action'=>'delete_save'), 'admin'); ?>' method='post'>
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
			<input type='submit' name='cancel_all' value='Cancel'>&nbsp;&nbsp;<input type='submit' name='delete' value='Delete Selected'>
		</div>
		</form>
	</td>
	
	<td class="cat_man_menu_holder">
		<div>
			<h3>Navigation</h3>
			<?php cat_man_menu(); ?>
			
			<h3>Category Tree</h3>
			<?php cat_man_tree($the_cats); ?>

		</div>
	</td>
	</tr></table>
