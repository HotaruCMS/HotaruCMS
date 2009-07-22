<?php

/* ******* PLUGIN TEMPLATE ************************************************************************** 
 * Plugin name: Category Manager
 * Template name: plugins/category_manager/cat_man_edit_meta.php
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
		Click a category and enter a description and some keywords (comma separated) to describe it. Save after editing each category.<br /><br />
	
		<?php
		foreach($the_cats as $cat) {
			if($cat['category_name'] != "all") {
				if($cat['category_parent'] > 1) {
					for($i=1; $i<$cat['category_level']; $i++) {
						echo "--- ";
					}
					echo "<a href='#' class='cat_man_drop_down' title='Edit Meta'>" . $cat['category_name'] . "</a><br />";
				} else {
					echo "<a href='#' class='cat_man_drop_down' title='Edit Meta'>" . $cat['category_name'] . "</a><br />";
				}
				echo "<div id='" . $cat['category_id'] . "' style='display: none;'>";
					echo "<form class='cat_man_edit_meta_form' style='margin-bottom: 0px;' name='category_manager_edit_meta_form' action='" . baseurl . "admin/admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=edit_meta_save&amp;id=" . $cat['category_id'] . "' method='post'>";
					echo "Description: <input size='40' name='description' type='text' value='" . $cat['category_description'] . "'>";
					echo "<br />";
					echo "Keywords: &nbsp;&nbsp;<input size='40' name='keywords' type='text' value='" . $cat['category_keywords'] . "'>";
					echo "&nbsp;&nbsp;<input type='submit' name='save_edit_meta' value='Save'>";
					echo "</form>";
				echo "</div>";
			}
		}
		?>
		<br />
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
