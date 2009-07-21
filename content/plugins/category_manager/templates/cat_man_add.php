<?php

/* ******* PLUGIN TEMPLATE ************************************************************************** 
 * Plugin name: Category Manager
 * Template name: plugins/category_manager/cat_man_add.php
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
 
	<h2>Category Manager: Add</h2>

	<table class="cat_man_table">
	<tr><td class="cat_man_body">
	<h3>1. Add a Main Category</h3>
	<form name='category_manager_add_parent_form' action='<?php echo baseurl ?>admin/admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=add_save' method='post'>
	Add a new top-level category and name it <input name="new_category" type="text" value=""> &nbsp;&nbsp;<input style='padding: 2px;' type='submit' name='save_new_category1' value='Save'>
	</form>

	<h3>2. Add a Child Category to a Main Category</h3>
	<form name='category_manager_add_child_form' action='<?php echo baseurl ?>admin/admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=add_save' method='post'>
	Add to 
		<select name="parent">
		<?php
		foreach($the_cats as $cat) {
			if($cat['category_name'] != "all") {
				if($cat['category_parent'] == 1) {
	  				echo "<option value='" . $cat['category_id'] . "'>" . $cat['category_name'] . "</option>";
				}
			}
	  	}
	  	?>
	  	</select>
	and name it <input name="new_category" type="text" value=""> &nbsp;&nbsp;<input style='padding: 2px;' type='submit' name='save_new_category2' value='Save'>
	</form>

	<h3>3. Add a Child Category to a Child Category</h3>
	<form name='category_manager_add_child2_form' action='<?php echo baseurl ?>admin/admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=add_save' method='post'>
	Add to 
		<select name="parent">
		<?php
		foreach($the_cats as $cat) {
			if($cat['category_name'] != "all") {
				if($cat['category_parent']  > 1) {
	  				echo "<option value='" . $cat['category_id'] . "'>" . $cat['category_name'] . "</option>";
				}	
			}
	  	}
	  	?>
	  	</select>
	and name it <input name="new_category" type="text" value=""> &nbsp;&nbsp;<input style='padding: 2px;' type='submit' name='save_new_category3' value='Save'>
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
