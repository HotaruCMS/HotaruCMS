<?php
/* ********** PLUGIN *********************************************************************************
 * name: Categories
 * description: Enables categories for posts
 * version: 0.1
 * folder: categories
 * prefix: cts
 * requires: submit 0.1, category_manager 0.1
 * hooks: install_plugin, hotaru_header, header_include, submit_hotaru_header_1, submit_hotaru_header_2, submit_class_post_read_post_1, submit_class_post_read_post_2, submit_class_post_add_post, submit_class_post_update_post, submit_form_2_assign, submit_form_2_fields, submit_form_2_check_for_errors, submit_form_2_process_submission, submit_settings_get_values, submit_settings_form, submit_save_settings, submit_posts_list_filter, submit_show_post_author_date, submit_is_page_main, sidebar_top
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


 /* ******************************************************************** 
 * ********************************************************************* 
 * ********************* FUNCTIONS FOR POST CLASS ********************** 
 * *********************************************************************
 * ****************************************************************** */
 
 
/* ******************************************************************** 
 *  Function: cts_install_plugin
 *  Parameters: None
 *  Purpose: Adds default settings for Submit plugin
 *  Notes: This determines whether the Submit plugin should offer categories or not.
 ********************************************************************** */
 
function cts_install_plugin() {
	global $db, $plugin, $post;
	
	// Default settings (Note: we can't use $post->post_vars because it hasn't been filled yet.)
	$plugin->plugin_settings_update('submit', 'submit_categories', 'checked');

}


/* ******************************************************************** 
 *  Function: cts_hotaru_header
 *  Parameters: None
 *  Purpose: Defines db table and includes language file
 *  Notes: ---
 ********************************************************************** */
 
function cts_hotaru_header() {
	global $post, $hotaru, $cage, $plugin;
	
	// The categories table is defined 
	if(!defined('table_categories')) { define("table_categories", db_prefix . "categories"); }
	
	// include language file
	$plugin->include_language_file('categories');
	
	// Get page title	
	if($cage->get->keyExists('category')) {
		if(is_numeric($cage->get->notags('category'))) { 
			$hotaru->title = get_cat_name($cage->get->getInt('category')); // friendly URLs: FALSE
		} else {
			$hotaru->title = $hotaru->page_to_title_caps(($cage->get->notags('category'))); // friendly URLs: TRUE
		} 
	}
}


/* ******************************************************************** 
 *  Function: cts_submit_hotaru_header_1
 *  Parameters: None
 *  Purpose: Adds additional member variables when the $post object is read in the Submit plugin.
 *  Notes: ---
 ********************************************************************** */
 
function cts_submit_hotaru_header_1() {
	global $post, $plugin;
	
	// The categories table is defined 
	if(!defined('table_categories')) { define("table_categories", db_prefix . "categories"); }
	
	// include language file
	$plugin->include_language_file('categories');
	
	$post->post_vars['post_category'] = 1;	// default category ('all').
	$post->post_vars['post_cat_name'] = '';
	$post->post_vars['post_cat_safe_name'] = '';
	$post->post_vars['use_categories'] = true;
	
}


/* ******************************************************************** 
 *  Function: cts_submit_hotaru_header_2
 *  Parameters: None
 *  Purpose: Checks if url query string is /category_name/post_name/
 *  Notes: Only used for friendly urls. This is necessary because if a 
 *  url is /people/top-10-longest-beards/ there's no actual mention of "category" there!
 ********************************************************************** */
 
function cts_submit_hotaru_header_2() {
	global $db, $hotaru, $post, $plugin, $cage;
		
	if(friendly_urls == "true" && $post->post_id == 0) {
		// No post stored in post object, nothing was succesfully read by the Submit plugin		
				
		// Can't get keys from the url with Inspekt, so must get the whole query string instead.
		$query_string = $cage->server->getMixedString2('QUERY_STRING');
		
		if($query_string) {
			// we actually only need the first pair, so won't bother looping.
			$query_string = preg_replace('/&amp;/', '&', $query_string);
			$pairs = explode('&', $query_string); 
			if($pairs[0]) {
				list($key, $value) = explode('=', $pairs[0]);
				if($key) {
					// Using db_prefix because table_categories might not be defined yet (depends on plugin install order)
					$sql = "SELECT category_id FROM " . db_prefix . "categories WHERE category_safe_name = %s LIMIT 1";
					$exists = $db->get_var($db->prepare($sql, $key));		
					if($exists && $value) {
						// Now we know that $key is a category so $value must be the post name. Go get the post_id...
						$post->post_id = $post->is_post_url($value);
						$post->read_post($post->post_id);
						$post->post_vars['is_category_post'] = true; 
						return true;
					} 
				}
			}
		}
	}
		
	$post->post_vars['is_category_post'] = false;
	return false;
}


/* ******************************************************************** 
 *  Function: cts_submit_class_post_read_post_1
 *  Parameters: None
 *  Purpose: Read category settings
 *  Notes: ---
 ********************************************************************** */
 
function cts_submit_class_post_read_post_1() {
	global $plugin, $post;
	
	//categories
	if(($plugin->plugin_settings('submit', 'submit_categories') == 'checked') && ($plugin->plugin_active('categories'))) { 
		$post->post_vars['use_categories'] = true; 
	} else { 
		$post->post_vars['use_categories'] = false; 
	}
}


/* ******************************************************************** 
 *  Function: cts_submit_class_post_read_post_2
 *  Parameters: None
 *  Purpose: Read category from the post in the database.
 *  Notes: ---
 ********************************************************************** */
 
function cts_submit_class_post_read_post_2() {
	global $db, $post, $post_row;
	$post->post_vars['post_category'] = $post_row->post_category;
	
	$sql = "SELECT category_name, category_safe_name FROM " . table_categories . " WHERE category_id = %d";
	$cat = $db->get_row($db->prepare($sql, $post->post_vars['post_category']));
	$post->post_vars['post_cat_name'] = urldecode($cat->category_name);
	$post->post_vars['post_cat_safe_name'] = urldecode($cat->category_safe_name);
}


/* ******************************************************************** 
 *  Function: cts_submit_class_post_add_post
 *  Parameters: None
 *  Purpose: Adds category to the posts table
 *  Notes: ---
 ********************************************************************** */
 
function cts_submit_class_post_add_post() {
	global $post, $db, $last_insert_id;
	
	$sql = "UPDATE " . table_posts . " SET post_category = %d WHERE post_id = %d";
	$db->query($db->prepare($sql, $post->post_vars['post_category'], $last_insert_id));
}


/* ******************************************************************** 
 *  Function: cts_submit_class_post_update_post
 *  Parameters: None
 *  Purpose: Updates category in the posts table
 *  Notes: ---
 ********************************************************************** */
 
function cts_submit_class_post_update_post() {
	global $post, $db;
	
	$sql = "UPDATE " . table_posts . " SET post_category = %d WHERE post_id = %d";
	$db->query($db->prepare($sql, $post->post_vars['post_category'], $post->post_id));
}


 /* ******************************************************************** 
 * ********************************************************************* 
 * ********************* FUNCTIONS FOR SUBMIT FORM ********************* 
 * *********************************************************************
 * ****************************************************************** */
 

/* ******************************************************************** 
 *  Function: cts_submit_form_2_assign_from_cage
 *  Parameters: None
 *  Purpose: Sets $category_check to the value of the chosen category
 *  Notes: ---
 ********************************************************************** */
 
function cts_submit_form_2_assign() {
	global $cage, $category_check, $post;
	
	if($cage->post->getAlpha('submit2') == 'true') {
		// Submitted this form...
		$category_check = $cage->post->getInt('post_category');
		
	} elseif($cage->post->getAlpha('submit3') == 'edit') {
		// Come back from step 3 to make changes...
		$category_check = $post->post_vars['post_category'];
		
	} else {
		// First time here...
		$category_check = 1;
	}

}


/* ******************************************************************** 
 *  Function: cts_submit_form_2_fields
 *  Parameters: None
 *  Purpose: Adds a category drop-down box to submit form 2
 *  Notes: ---
 ********************************************************************** */
 
function cts_submit_form_2_fields() {
	global $db, $lang, $post, $category_check;

	if($post->post_vars['use_categories']) { 
		echo "<tr>\n";
			echo "<td>" . $lang["submit_form_category"] . ":&nbsp; </td>\n";
			echo "<td><select name='post_category'>\n";
			$sql = "SELECT category_name FROM " . table_categories . " WHERE category_id = %d";
			$category_name = $db->get_var($db->prepare($sql, $category_check));
			if($category_name == 'all') { $category_name = $lang['submit_form_category_select']; }
			echo "<option value=" . $category_check . ">" . urldecode($category_name) . "</option>\n";
			$sql = "SELECT category_id, category_name FROM " . table_categories . " ORDER BY category_order ASC";
			$cats = $db->get_results($db->prepare($sql));
			if($cats) {
				foreach($cats as $cat) {
					if($cat->category_id != 1) { 
						echo "<option value=" . $cat->category_id . ">" . urldecode($cat->category_name) . "</option>\n";
					}
				}
			}
			echo "</select></td>\n";
			echo "<td>&nbsp;</td>\n";
		echo "</tr>";
	}
}


/* ******************************************************************** 
 *  Function: cts_submit_form_2_check_for_errors
 *  Parameters: None
 *  Purpose: Checks for category error from submit form 2
 *  Notes: ---
 ********************************************************************** */
 
function cts_submit_form_2_check_for_errors() {
	global $hotaru, $lang, $post, $cage, $category_check;
	
	// ******** CHECK CATEGORY ********
	if($post->post_vars['use_categories']) {
		$category_check = $cage->post->getInt('post_category');	
		if(!$category_check) {
			// No category present...
			$hotaru->messages[$lang['submit_form_category_error']] = "red";
			$error_category = 1;
		} else {
			// category is okay.
			$error_category = 0;
		}
	}
	
	return $error_category;
}


/* ******************************************************************** 
 *  Function: cts_submit_form_2_process_submission
 *  Parameters: None
 *  Purpose: Sets $post->post_category to submitted category id
 *  Notes: ---
 ********************************************************************** */
 
function cts_submit_form_2_process_submission() {
	global $db, $cage, $post;
	$post->post_vars['post_category'] = $cage->post->getInt('post_category');
	
	$sql = "SELECT category_name, category_safe_name FROM " . table_categories . " WHERE category_id = %d";
	$cat = $db->get_row($db->prepare($sql, $post->post_vars['post_category']));
	$post->post_vars['post_cat_name'] = urldecode($cat->category_name);
	$post->post_vars['post_cat_safe_name'] = urldecode($cat->category_safe_name);
}


 /* ******************************************************************** 
 * ********************************************************************* 
 * ******************* FUNCTIONS FOR SHOWING POSTS ********************* 
 * *********************************************************************
 * ****************************************************************** */
 

/* ******************************************************************** 
 *  Function: cts_header_include
 *  Parameters: None
 *  Purpose: Includes css file.
 *  Notes: ---
 ********************************************************************** */
 
function cts_header_include() {
	global $plugin;
	$plugin->include_css_file('categories');
}


 /* ******************************************************************** 
 *  Function: cts_submit_is_page_main
 *  Parameters: None
 *  Purpose: Checks is the url is a category->post name pair and displays the post
 *  Notes: ---
 ********************************************************************** */
 
function cts_submit_is_page_main() {
	global $db, $post, $plugin, $cage, $hotaru;
	
	if($post->post_vars['is_category_post']) {
		$hotaru->display_template('post_page', 'submit');
		return true;
	} else {
		return false;
	}
}


/* ******************************************************************** 
 *  Function: cts_submit_posts_list_filter
 *  Parameters: None
 *  Purpose: Gets a category from the url and sets the filter for get_posts
 *  Notes: This hook is at the top of posts_list.php in the Submit plugin.
 ********************************************************************** */
 
function cts_submit_posts_list_filter() {
	global $post, $cage, $filter;
	
	if(friendly_urls == "true") {
		$category = $cage->get->noTags('category'); 
		if($category) { 
			$filter = array('post_category = %d' => get_cat_id($category)); 
			return true;	
		} 
	} else {
		$category = $cage->get->getInt('category'); 
		if($category) {
			$filter = array('post_category = %d' => $category); 
			return true;	
		}
	}
		
	return false;	
}


/* ******************************************************************** 
 *  Function: cts_submit_show_post_author_date
 *  Parameters: None
 *  Purpose: Shows tags in each post
 *  Notes: echos "in" before the category name.
 ********************************************************************** */
 
function cts_submit_show_post_author_date() { 
	global $post, $lang;
	
	if($post->post_vars['use_categories'] && $post->post_vars['post_category']) { 
	
		$category =  $post->post_vars['post_category'];
		$cat_name = $post->post_vars['post_cat_name'];
		
		echo " " . $lang["submit_show_post_in_category"] . " ";
		echo "<a href='" . url(array('category'=>$category)) . "'>" . $cat_name . "</a></li>\n";
	}		
}


 /* ******************************************************************** 
 *  Function: cts_sidebar_top
 *  Parameters: None
 *  Purpose: Displays categories as a tree
 *  Notes: ---
 ********************************************************************** */

function cts_sidebar_top() {
	global $db, $the_cats, $cat_level, $lang;
	
	$sql = "SELECT * FROM " . table_categories . " ORDER BY category_order ASC";
	$the_cats = $db->get_results($db->prepare($sql));
	
	echo "<h2>" . $lang["sidebar_categories"] . "</h2>";
	echo "<ul class='sidebar_categories'>\n";
	foreach($the_cats as $cat) {
		$cat_level = 1;	// top level category.
		if($cat->category_name != "all") {
			echo "<li>";
			if($cat->category_parent > 1) {
				$depth = cat_level($cat->category_id);
				for($i=1; $i<$depth; $i++) {
					echo "--- ";
				}
			} 
			echo "<a href='" . url(array('category'=>$cat->category_id)) . "'>";
			echo urldecode($cat->category_name) . "</a></li>\n";
		}
	}
	echo "</ul>\n";
}


 /* ******************************************************************** 
 *  Function: cts_sidebar_top
 *  Parameters: None
 *  Purpose: Recursive function to find level depth
 *  Notes: Starting level is 0
 ********************************************************************** */
 
function cat_level($cat_id) {
	global $cat_level, $the_cats;
		
	foreach($the_cats as $cat) {
		if(($cat->category_id == $cat_id) && $cat->category_parent > 1) {
			$cat_level++;
			cat_level($cat->category_parent);
		}
	}

	return $cat_level;
}


 /* ******************************************************************** 
 * ********************************************************************* 
 * ****************** FUNCTIONS FOR SUBMIT SETTINGS ******************** 
 * *********************************************************************
 * ****************************************************************** */
 

/* ******************************************************************** 
 *  Function: cts_submit_settings_get_values
 *  Parameters: None
 *  Purpose: Gets current tag settings from the database
 *  Notes: ---
 ********************************************************************** */
 
function cts_submit_settings_get_values() {
	global $plugin, $categories;
	
	// Get settings from database if they exist... should return 'checked'
	$categories = $plugin->plugin_settings('submit', 'submit_categories');
	
	// otherwise set to blank...
	if(!$categories) { $categories = ''; }

}


/* ******************************************************************** 
 *  Function: cts_submit_settings_form
 *  Parameters: None
 *  Purpose: Add tags field to the submit settings form
 *  Notes: ---
 ********************************************************************** */
 
function cts_submit_settings_form() {
	global $plugin, $lang, $categories;
	
	echo "<input type='checkbox' name='categories' value='categories' " . $categories . ">&nbsp;&nbsp;" . $lang["submit_settings_categories"] . "<br />";

}


/* ******************************************************************** 
 *  Function: cts_submit_save_settings
 *  Parameters: None
 *  Purpose: Save tag settings.
 *  Notes: ---
 ********************************************************************** */
 
function cts_submit_save_settings() {
	global $plugin, $cage, $lang, $categories;
	
	// Categories
	if($cage->post->keyExists('categories')) { 
		$categories = 'checked'; 
		$post->post_vars['use_categories'] = true;
	} else { 
		$categories = ''; 
		$post->post_vars['use_categories'] = false;
	}
		
	$plugin->plugin_settings_update('submit', 'submit_categories', $categories);

}


 /* ******************************************************************** 
 * ********************************************************************* 
 * ************************* EXTRA FUNCTIONS *************************** 
 * *********************************************************************
 * ****************************************************************** */
 
 /* ******************************************************************** 
 *  Function: get_cat_safe_name
 *  Parameters: Category ID
 *  Purpose: Returns the category safe name for a give category id.
 *  Notes: Used in /funcs.urls.php
 ********************************************************************** */
 
function get_cat_safe_name($cat_id) {
	global $db;
	
	$sql = "SELECT category_safe_name FROM " . table_categories . " WHERE category_id = %d";
	$cat_safe_name = $db->get_var($db->prepare($sql, $cat_id));
	return urldecode($cat_safe_name);
}


 /* ******************************************************************** 
 *  Function: get_cat_name
 *  Parameters: Category ID
 *  Purpose: Returns the category name for a give category id.
 *  Notes: Used in this file (cts_hotaru_header function) for header's title tags
 ********************************************************************** */
 
function get_cat_name($cat_id) {
	global $db;
	
	$sql = "SELECT category_name FROM " . table_categories . " WHERE category_id = %d";
	$cat_name = $db->get_var($db->prepare($sql, $cat_id));
	return urldecode($cat_name);
}


 /* ******************************************************************** 
 *  Function: get_cat_id
 *  Parameters: Category safe name
 *  Purpose: Returns the category id for a given category safe name.
 *  Notes: ---
 ********************************************************************** */
 
function get_cat_id($cat_name) {
	global $db;
	
	$sql = "SELECT category_id FROM " . table_categories . " WHERE category_safe_name = %s";
	$cat_id = $db->get_var($db->prepare($sql, urlencode($cat_name)));
	return $cat_id;
}
 
?>