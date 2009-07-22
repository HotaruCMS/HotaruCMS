<?php

/* ******* PLUGIN TEMPLATE ************************************************************************** 
 * Plugin name: Category Manager
 * Template name: plugins/category_manager/cat_man_order.php
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
 
	<h2>Category Manager: Order</h2>

	<table class="cat_man_table">
	<tr><td class="cat_man_body">
		There are four ways to automatically sort your categories:<br />

		<h3>1. <a href="<?php echo baseurl ?>admin/admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=order_alpha">Order Alphabetically</a></h3>
		Order you categories alphabetically, from A-Z. Sub-categories will also be ordered within their parent category. <br />
		<h3>2. <a href="<?php echo baseurl ?>admin/admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=order_length">Order by Name Length</a></h3>
		This will order your categories by the number of characters in their titles, shortest first.  <br />
		<h3>3. <a href="<?php echo baseurl ?>admin/admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=order_id">Order by ID</a></h3>
		The ID of each character was assigned when you created it, so this will sort your categories by date of creation. <br />
		<h3>4. <a href="<?php echo baseurl ?>admin/admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=order_posts">Order by Posts</a></h3>
		Order your categories by the number of posts they have in them. The most popular categories go at the top.

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
