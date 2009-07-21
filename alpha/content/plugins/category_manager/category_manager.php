<?php

/* ********** PLUGIN *********************************************************************************
 * name: Category Manager
 * description: Manage categories.
 * version: 0.1
 * folder: category_manager
 * prefix: cats
 * requires: submit 0.1
 * hooks: hotaru_header, admin_index, install_plugin, admin_header_include, admin_plugin_settings, admin_sidebar_plugin_settings
 *
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
 
return false; die(); // die on direct access.

/* ******************************************************************** 
 *  Function: cats_install_plugin
 *  Parameters: None
 *  Purpose: If it doesn't already exist, a categories table is created
 *  Notes: Happens when the plugin is installed. 
 ********************************************************************** */
 
function cats_install_plugin() {
	global $db, $plugin, $post;
	
	// Create a new empty table called "categories"
	$exists = $db->table_exists('categories');
	if(!$exists) {
		//echo "table doesn't exist. Stopping before creation."; exit;
		$sql = "CREATE TABLE `" . db_prefix . "categories` (
		  `category_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  `category_parent` int(11) NOT NULL DEFAULT '1',
		  `category_name` varchar(64) NOT NULL DEFAULT '',
		  `category_safe_name` varchar(64) NOT NULL DEFAULT '',
		  `rgt` int(11) NOT NULL DEFAULT '0',
		  `lft` int(11) NOT NULL DEFAULT '0',
		  `category_order` int(11) NOT NULL DEFAULT '0',
		  `category_desc` text NULL,
		  `category_keywords` varchar(255) NOT NULL,
		  `category_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
 		  `category_updateby` int(20) NOT NULL DEFAULT 0, 
		  UNIQUE KEY `key` (`category_name`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Categories';";
		$db->query($sql); 
	
	// Insert default settings...
		
	$sql = "INSERT INTO " . db_prefix . "categories (category_name, category_safe_name) VALUES (%s, %s)";
	$db->query($db->prepare($sql, urlencode('all'), urlencode('all')));
	
	}
		
	// 'checked' means that categories are enabled by the Submit plugin.
	$plugin->plugin_settings_update('submit', 'submit_categories', 'checked');	

	
}


/* ******************************************************************** 
 *  Function: cats_hotaru_header
 *  Parameters: None
 *  Purpose: Defines a global "table_categories" for referring to the db table
 *  Notes: ---
 ********************************************************************** */
 
function cats_hotaru_header() {
	global $hotaru, $lang, $plugin;
	if(!defined('table_categories')) { define("table_categories", db_prefix . 'categories'); }
	return true;
}


/* ******************************************************************** 
 *  Function: cats_admin_header_include
 *  Parameters: None
 *  Purpose: Includes jquery for Category Manager
 *  Notes: ---
 ********************************************************************** */
 
function cats_admin_header_include() {
	echo "<link rel='stylesheet' href='" . baseurl . "content/plugins/category_manager/css/cat_man_style.css' type='text/css'>\n";
	echo "<script language='JavaScript' src='" . baseurl . "content/plugins/category_manager/javascript/cat_man_jquery.js'></script>\n";
}


/* ******************************************************************** 
 *  Function: cats_admin_sidebar_plugin_settings
 *  Parameters: None
 *  Purpose: Puts a link to the settings page in the Admin sidebar under Plugin Settings
 *  Notes: ---
 ********************************************************************** */
 
function cats_admin_sidebar_plugin_settings() {
	echo "<li><a href='" . url(array('page'=>'plugin_settings', 'plugin'=>'category_manager'), 'admin') . "'>Category Manager</a></li>";
}


 /* ******************************************************************** 
 *  Function: cats_admin_plugin_settings
 *  Parameters: None
 *  Purpose: Displays pages from Category Manager
 *  Notes: ---
 ********************************************************************** */
 
function cats_admin_plugin_settings() {
	global $hotaru, $plugin, $lang;
	
	$plugin->include_language_file('category_manager');
	
	require_once(plugins . 'category_manager/cat_man_engine.php');
	cat_man_main();
	return true;
}



 /* ******************************************************************** 
 * ********************************************************************* 
 * ********************** FUNCTIONS FOR DISPLAY ************************ 
 * *********************************************************************
 * ****************************************************************** */
 
 /* ******************************************************************** 
 *  Function: cat_man_menu
 *  Parameters: None
 *  Purpose: Displays navigation for Category Manager.
 *  Notes: ---
 ********************************************************************** */
 
function cat_man_menu() {

	$home_link = url(array('page'=>'plugin_settings', 'plugin'=>'category_manager'), 'admin');
	$order_link = url(array('page'=>'plugin_settings', 'plugin'=>'category_manager', 'action'=>'order'), 'admin');
	$add_link = url(array('page'=>'plugin_settings', 'plugin'=>'category_manager', 'action'=>'add'), 'admin');
	$edit_link = url(array('page'=>'plugin_settings', 'plugin'=>'category_manager', 'action'=>'edit'), 'admin');
	$edit_meta_link = url(array('page'=>'plugin_settings', 'plugin'=>'category_manager', 'action'=>'edit_meta'), 'admin');
	$move_link = url(array('page'=>'plugin_settings', 'plugin'=>'category_manager', 'action'=>'move'), 'admin');
	$delete_link = url(array('page'=>'plugin_settings', 'plugin'=>'category_manager', 'action'=>'delete'), 'admin');
	
	$menu = "<div class='cat_man_menu'>";
	$menu .= "&raquo; <a href = '" . $home_link . "'>Category Manager</a><br />";
	$menu .= "&raquo; <a href = '" . $order_link . "'>Order Categories</a><br />";
	$menu .= "&raquo; <a href = '" . $add_link . "'>Add Categories</a><br />";
	$menu .= "&raquo; <a href = '" . $edit_link . "'>Edit Categories</a><br />";
	$menu .= "&raquo; <a href = '" . $edit_meta_link . "'>Edit Desc / Keywords</a><br />";
	$menu .= "&raquo; <a href = '" . $move_link . "'>Move Categories</a><br />";
	$menu .= "&raquo; <a href = '" . $delete_link . "'>Delete Categories</a><br />";
	$menu .= "</div>";
	
	echo $menu;
}


 /* ******************************************************************** 
 *  Function: cat_man_tree
 *  Parameters: None
 *  Purpose: Displays category tree.
 *  Notes: ---
 ********************************************************************** */
 
function cat_man_tree($the_cats) {
	echo "<div class='cat_man_tree'>";
	foreach($the_cats as $cat) {
		if($cat['category_name'] != "all") {
			if($cat['category_parent'] > 1) {
				for($i=1; $i<$cat['category_level']; $i++) {
					echo "--- ";
				}
				 echo $cat['category_name'] . " <span style='font-size: 0.7em; color: #888;'>[" . $cat['category_id'] . "]</span><br />";
			} else {
				 echo $cat['category_name'] . " <span style='font-size: 0.7em; color: #888;'>[" . $cat['category_id'] . "]</span><br />";
			}
		}
	}
	echo "</div>";
}

?>