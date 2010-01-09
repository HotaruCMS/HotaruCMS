<?php
/**
 * CATEGORY MANAGER LANGUAGE
 *
 * PHP version 5
 *
 * LICENSE: Hotaru CMS is free software: you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License as 
 * published by the Free Software Foundation, either version 3 of 
 * the License, or (at your option) any later version. 
 *
 * Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE. 
 *
 * You should have received a copy of the GNU General Public License along 
 * with Hotaru CMS. If not, see http://www.gnu.org/licenses/.
 * 
 * @category  Content Management System
 * @package   HotaruCMS
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

/* Main Page */
$lang["cat_man_title"] = "Category Manager";

/* Sidebar */
$lang["cat_man_admin_sidebar"] = "Category Manager";

/* Menu */
$lang["cat_man_menu_title"] = "Navigation";
$lang["cat_man_menu_home"] = "Category Manager";
$lang["cat_man_menu_order"] = "Order categories";
$lang["cat_man_menu_add"] = "Add categories";
$lang["cat_man_menu_edit"] = "Edit categories";
$lang["cat_man_menu_edit_meta"] = "Edit desc / keywords";
$lang["cat_man_menu_move"] = "Move Categories";
$lang["cat_man_menu_delete"] = "Delete Categories";

/* Success and Failure Messages */
$lang["cat_man_changes_saved"] = "Changes Saved";
$lang["cat_man_changes_cancelled"] = "Changes Cancelled";
$lang["cat_man_form_error"] = "Error retrieving form input";
$lang["cat_man_order_alpha"] = "Order By Alphabet: Completed";
$lang["cat_man_order_length"] = "Order By Name Length: Completed";
$lang["cat_man_order_posts"] = "Order By Posts: Completed";
$lang["cat_man_order_id"] = "Order By ID: Completed";
$lang["cat_man_edit_save"] = "Changes Saved (except for blank categories)";
$lang["cat_man_edit_meta_save"] = "Category Meta Saved";
$lang["cat_man_category_added"] = "Category Added";
$lang["cat_man_category_not_added"] = "Category Not Added: No Name Given";
$lang["cat_man_category_moved"] = "Category Moved";
$lang["cat_man_category_not_moved"] = "Error: Category Not Moved";
$lang["cat_man_category_deleted"] = "Categories Deleted Successfully";
$lang["cat_man_category_not_deleted"] = "No Categories Deleted";
$lang["cat_man_category_exists"] = "Sorry, that category already exists";

/* General */
$lang["cat_man_cancel"] = "Cancel";
$lang["cat_man_save"] = "Save";
$lang["cat_man_save_all"] = "Save All";
$lang["cat_man_go"] = "Go";
$lang["cat_man_update"] = "Update";
$lang["cat_man_category_tree"] = "Category Tree";

/* Category Manager Home */
$lang["cat_man_home"] = "Category Manager: Home";
$lang["cat_man_home_intro1"] = "Use the links on the right to organize your categories.";
$lang["cat_man_home_clear_cache"] = "<i>Note:</i> After editing categories, ";
$lang["cat_man_home_clear_cache2"] = "clear the database cache";
$lang["cat_man_home_intro2"] = "These are the things you can do with this plugin:";
$lang["cat_man_home_order_categories"] = "Order your categories";
$lang["cat_man_home_order_categories_desc"] = "Sort your main and sub-categories alphabetically, by ID, by the length of their names, or in order of most posts - all with just one click.";
$lang["cat_man_home_add_categories"] = "Add new categories";
$lang["cat_man_home_add_categories_desc"] = "Create as many new categories as you like. There's no limit to how many levels of sub-categories you can have. ";
$lang["cat_man_home_edit_categories"] = "Edit category names";
$lang["cat_man_home_edit_categories_desc"] = "Batch edit the names of all your categories in one go. ";
$lang["cat_man_home_edit_categories_meta"] = "Edit category keywords and descriptions";
$lang["cat_man_home_edit_categories_meta_desc"] = "Give your categories keywords and descriptions. These could be used by plugins and themes for a more user-friendly interface.";
$lang["cat_man_home_move_categories"] = "Move categories";
$lang["cat_man_home_move_categories_desc"] = "If you need to micro-manage the ordering of your categories, this section lets you move individual or whole branches of categories.";
$lang["cat_man_home_delete_categories"] = "Delete categories";
$lang["cat_man_home_delete_categories_desc"] = "This module lets you delete multiple categories at once, but ensures you won't delete any posts by accident. ";

/* Order categories */
$lang["cat_man_order"] = "Category Manager: Order";
$lang["cat_man_order_instruct"] = "There are four ways to automatically sort your categories:";
$lang["cat_man_order_alpha"] = "Order Alphabetically";
$lang["cat_man_order_alpha_desc"] = "Order you categories alphabetically, from A-Z. Sub-categories will also be ordered within their parent category. ";
$lang["cat_man_order_length"] = "Order by Name Length";
$lang["cat_man_order_length_desc"] = "This will order your categories by the number of characters in their titles, shortest first. ";
$lang["cat_man_order_id"] = "Order by ID";
$lang["cat_man_order_id_desc"] = "The ID of each character was assigned when you created it, so this will sort your categories by date of creation. ";
$lang["cat_man_order_posts"] = "Order by Posts";
$lang["cat_man_order_posts_desc"] = "Order your categories by the number of posts they have in them. The most popular categories go at the top. ";

/* Add a category */
$lang["cat_man_add"] = "Category Manager: Add";
$lang["cat_man_add_main"] = "Add a Main Category";
$lang["cat_man_add_child_to_main"] = "Add a Child Category to a Main Category";
$lang["cat_man_add_child_to_child"] = "Add a Child Category to a Child Category";
$lang["cat_man_add_top_level"] = "Add a new top-level category and name it";
$lang["cat_man_add_add_to"] = "Add to";
$lang["cat_man_add_name_it"] = "and name it";

/* Edit a category */
$lang["cat_man_edit"] = "Category Manager: Edit";
$lang["cat_man_edit_instruct"] = "Edit the names of your categories below and <b>click \"Save All\".</b>";

/* Edit a description or keyword */
$lang["cat_man_edit_meta_instruct"] = "Click a category and enter a description and some keywords (comma separated) to describe it. Save after editing each category.";
$lang["cat_man_edit_meta_keywords"] =  "Keywords:";
$lang["cat_man_edit_meta_description"] = "Description:"; 
$lang["cat_man_edit_meta_anchor_title"] = "Edit Meta"; 

/* Move a category */
$lang["cat_man_move"] = "Category Manager: Move";
$lang["cat_man_move_instruct"] = "Click the name of the category you want to move and choose how to move it. Saving takes place when you click \"Go\".";
$lang["cat_man_move_put"] = "Put";
$lang["cat_man_move_or"] = "OR...";
$lang["cat_man_move_move"] = "Move";
$lang["cat_man_move_top_level"] = "Top-level";
$lang["cat_man_move_to"] = "to";
$lang["cat_man_move_after"] = "after";
$lang["cat_man_move_before"] = "before";
$lang["cat_man_move_in"] = "in";

/* Delete a category */
$lang["cat_man_delete"] = "Category Manager: Delete";
$lang["cat_man_delete_instruct"] = "Check the boxes below for the categories you wish to delete.";
$lang["cat_man_delete_notes"] = "Notes:";
$lang["cat_man_delete_note1"] = "If a category contains links, it can't be deleted and is grayed out.";
$lang["cat_man_delete_note2"] = "If you delete a category with children, they will be assigned to their grandparent or otherwise become top-level categories.";
$lang["cat_man_delete_selected"] = "Delete Selected";
$lang["cat_man_delete_following"] = "You are about to delete the following categories:";
$lang["cat_man_delete_are_you_sure"] = "Are you sure you want to delete the above?";
$lang["cat_man_delete_yes_delete"] = "Yes, delete";
$lang["cat_man_delete_no_cancel"] = "No, cancel";

?>