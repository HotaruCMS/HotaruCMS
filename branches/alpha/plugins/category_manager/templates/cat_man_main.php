<?php

/* ******* PLUGIN TEMPLATE ************************************************************************** 
 * Plugin name: Category Manager
 * Template name: plugins/category_manager/cat_man_main.php
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
 
	<h2>Category Manager Home</h2>

	<table class="cat_man_table">
	<tr><td class="cat_man_body">
		Use the links on the right to organize your categories.<br /><br />
		These are the things you can do with this module:<br />

		<h3>1. <a href = "<?php echo url(array('page'=>'plugin_settings', 'plugin'=>'category_manager', 'action'=>'order'), 'admin'); ?>">Order your categories</a></h3>
		Sort your main and sub-categories alphabetically, by ID, by the length of their names, or in order of most posts - all with just one click. <br />
		<h3>2. <a href = "<?php echo url(array('page'=>'plugin_settings', 'plugin'=>'category_manager', 'action'=>'add'), 'admin'); ?>">Add new categories</a></h3>
		Create as many new categories as you like. There's no limit to how many levels of sub-categories you can have. <br />
		<h3>3. <a href = "<?php echo url(array('page'=>'plugin_settings', 'plugin'=>'category_manager', 'action'=>'edit'), 'admin'); ?>">Edit category names, keywords and descriptions</a></h3>
		Batch edit the names of all your categories in one go. <br />
		<h3>4. <a href = "<?php echo url(array('page'=>'plugin_settings', 'plugin'=>'category_manager', 'action'=>'edit_meta'), 'admin'); ?>">Edit category keywords and descriptions</a></h3>
		Give your categories keywords and descriptions. These could be used by plugins and themes for a more user-friendly interface.<br />
		<h3>5. <a href = "<?php echo url(array('page'=>'plugin_settings', 'plugin'=>'category_manager', 'action'=>'move'), 'admin'); ?>">Move categories</a></h3>
		If you need to micro-manage the ordering of your categories, this section lets you move individual or whole branches of categories. 
		<h3>6. <a href = "<?php echo url(array('page'=>'plugin_settings', 'plugin'=>'category_manager', 'action'=>'delete'), 'admin'); ?>">Delete categories</a></h3>
		This module lets you delete multiple categories at once, but ensures you won't delete any posts by accident.
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
