<?php
/**
 *  File: plugins/category_manager/cat_man_engine.php
 *  Purpose: The functions that do the hard work such as adding, deleting and sorting categories.
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

/**
 * Main function that calls others
 *
 * @return bool
 */
function cat_man_main()
{    
    
    global $db, $hotaru, $cage, $lang, $the_cats;
    
    $action = $cage->get->testAlnumLines('action');
    if (!$action || $action == '') { $action = "home"; }

    if ($action == "home") {    
        $the_cats = get_categories();     // Get all the category info                
        $hotaru->display_template('cat_man_main', 'category_manager');
        return true;
    }
    
    if ($action == "order") { 
        $the_cats = get_categories();     // Get all the category info
        $hotaru->display_template('cat_man_order', 'category_manager');
        return true;
    }     
        
    if ($action == "order_alpha") { 
        order("category_name");     // ORDER ALPHABETICALLY PERMANENTLY IN THE DATABASE
        $hotaru->show_message($lang["cat_man_order_alpha"], 'green');
        $the_cats = get_categories();     // Get all the category info
        $hotaru->display_template('cat_man_order', 'category_manager');
        return true;
    } 
    
    if ($action == "order_length") { 
        order("length(category_name)");     // ORDER BY LENGTH PERMANENTLY IN THE DATABASE
        $hotaru->show_message($lang["cat_man_order_length"], 'green');
        $the_cats = get_categories();     // Get all the category info
        $hotaru->display_template('cat_man_order', 'category_manager');
        return true;
    }

    if ($action == "order_posts") { 
        order_by_posts();     // ORDER BY POSTS PERMANENTLY IN THE DATABASE
        $hotaru->show_message($lang["cat_man_order_posts"], 'green');
        $the_cats = get_categories();     // Get all the category info
        $hotaru->display_template('cat_man_order', 'category_manager');
        return true;
    }

    if ($action == "order_id") { 
        order("category_id");     // ORDER BY ID PERMANENTLY IN THE DATABASE
        $hotaru->show_message($lang["cat_man_order_id"], 'green');
        $the_cats = get_categories();     // Get all the category info
        $hotaru->display_template('cat_man_order', 'category_manager');
        return true;
    }
                
    if ($action == "edit") { 
        $the_cats = get_categories();     // Get all the category info
        $hotaru->display_template('cat_man_edit', 'category_manager');
        return true;
    } 
    
    if ($action == "edit_save") { 
        if ($cage->post->keyExists('save_all')) {
            update_category_names();
            $hotaru->show_message($lang["cat_man_changes_saved"], 'green');
        } else {
            $hotaru->show_message($lang["cat_man_changes_cancelled"], 'green');
        }
        $the_cats = get_categories();     // Get all the category info
        $hotaru->display_template('cat_man_edit', 'category_manager');
        return true;
    } 
    
    if ($action == "edit_meta") { 
        $the_cats = get_categories();     // Get all the category info
        $hotaru->display_template('cat_man_edit_meta', 'category_manager');
        return true;
    } 
    
    if ($action == "edit_meta_save") { 
        if ($cage->get->keyExists('id')){ 
            $category_meta_id = $cage->get->getInt('id');
            if ($cage->post->keyExists('save_edit_meta')) {
                $description = $cage->post->getMixedString2('description');
                $keywords = $cage->post->getMixedString2('keywords');
                save_meta($category_meta_id, $description, $keywords);
                $hotaru->show_message($lang["cat_man_changes_saved"], 'green');
            }
        } else {
            $hotaru->show_message($lang["cat_man_form_error"], 'red');
        }
        $the_cats = get_categories();     // Get all the category info
        $hotaru->display_template('cat_man_edit_meta', 'category_manager');
        return true;
    } 

    if ($action == "add") { 
        $the_cats = get_categories();     // Get all the category info
        $hotaru->display_template('cat_man_add', 'category_manager');
        return true;
    } 
    
    if ($action == "add_save") { 
        if ($cage->post->keyExists('save_new_category1')) {
            $parent = 1; // parent is "all" because this is a main category
            $new_cat_name = $cage->post->getMixedString2('new_category');
            if ($new_cat_name != "") {
                $result = add_new_category($parent, $new_cat_name);
                if ($result) {
                    $hotaru->show_message($lang["cat_man_category_added"], 'green');
                } else {
                    $hotaru->show_message($lang["cat_man_category_exists"], 'red');
                }
            } else {
                $hotaru->show_message($lang["cat_man_category_not_added"], 'red');
            }
        } elseif ($cage->post->keyExists('save_new_category2')) {
            $parent = $cage->post->getInt('parent');
            $new_cat_name = $cage->post->getMixedString2('new_category');
            if ($new_cat_name != "") {
                $result = add_new_category($parent, $new_cat_name);
                if ($result) {
                    $hotaru->show_message($lang["cat_man_category_added"], 'green');
                } else {
                    $hotaru->show_message($lang["cat_man_category_exists"], 'red');
                }
            } else {
                $hotaru->show_message($lang["cat_man_category_not_added"], 'red');
            }
        } elseif ($cage->post->keyExists('save_new_category3')) {
            $parent = $cage->post->getInt('parent');
            $new_cat_name = $cage->post->getMixedString2('new_category');
            if ($new_cat_name != "") {
                $result = add_new_category($parent, $new_cat_name);
                if ($result) {
                    $hotaru->show_message($lang["cat_man_category_added"], 'green');
                } else {
                    $hotaru->show_message($lang["cat_man_category_exists"], 'red');
                }
            } else {
                $hotaru->show_message($lang["cat_man_category_not_added"], 'red');
            }
        } else {
            $hotaru->show_message($lang["cat_man_form_error"], 'red');    
        }
        $the_cats = get_categories();     // Get all the category info
        $hotaru->display_template('cat_man_add', 'category_manager');
        return true;
    }
    
    if ($action == "move") { 
        $the_cats = get_categories();     // Get all the category info
        $hotaru->display_template('cat_man_move', 'category_manager');
        return true;
    } 
    
    if ($action == "move_save") {
        if ($cage->get->keyExists('id')) {
            $cat_to_move = $cage->get->getInt('id');
            //echo "Moving category " . $cat_to_move . "<br />";
            if ($cage->post->keyExists('save_form1')) {
                $placement = $cage->post->testAlpha('placement');
                $target = $cage->post->testAlnum('parents');
                $success = move($cat_to_move, $placement, $target);
                if ($success) { 
                    $hotaru->show_message($lang["cat_man_category_moved"], 'green');
                } else { 
                    $hotaru->show_message($lang["cat_man_category_not_moved"], 'red');
                }

            } elseif ($cage->post->keyExists('save_form2')) {
                $target = $cage->post->testAlnum('moveup');
                $success = move($cat_to_move, 'none', $target);
                if ($success) { 
                    $hotaru->show_message($lang["cat_man_category_moved"], 'green');
                } else { 
                    $hotaru->show_message($lang["cat_man_category_not_moved"], 'red');
                }
            } else {
                $hotaru->show_message($lang["cat_man_category_not_moved"], 'red');
            }
            
        } else { 
            $hotaru->show_message($lang["cat_man_category_not_moved"], 'red');
        }
        $the_cats = get_categories();     // Get all the category info
        $hotaru->display_template('cat_man_move', 'category_manager');
        return true;
    }

    if ($action == "delete") { 
        $the_cats = get_categories();     // Get all the category info
        $hotaru->display_template('cat_man_delete', 'category_manager');
        return true;
    } 
    
    if ($action == "delete_save") { 
        global $delete_list;
        $delete_list = array(); $del_count = 0;
        if ($cage->post->keyExists('delete') && $cage->post->keyExists('delete_cats')) {
            foreach ($cage->post->getInt('delete_cats') as $category_id=>$check) { 
                if ($check > 0) { 
                    $delete_list[$del_count]['del_id'] = $category_id;
                    $delete_list[$del_count]['del_name'] = get_name_for_delete_confirm($category_id);
                }
                $del_count++;
            }
            $the_cats = get_categories();     // Get all the category info
            $hotaru->display_template('cat_man_delete_confirm', 'category_manager');
            return true;
        } else {
            $hotaru->show_message($lang["cat_man_category_not_deleted"], 'red');
            $the_cats = get_categories();     // Get all the category info
            $hotaru->display_template('cat_man_delete', 'category_manager');
            return true;
        }
        
    }
    
    if ($action == "delete_confirm") {
        if ($cage->post->keyExists('delete_confirm_yes') && $cage->post->keyExists('delete_list')) {
            foreach ($cage->post->getInt('delete_list') as $cat_id) { 
                    delete_categories($cat_id); 
            }
            cat_man_rebuild_tree(1, 0);
            $hotaru->show_message($lang["cat_man_category_deleted"], 'green');
        } else {
            $hotaru->show_message($lang["cat_man_category_not_deleted"], 'red');
            $hotaru->show_message();
        }

        $the_cats = get_categories();     // Get all the category info
        $hotaru->display_template('cat_man_delete', 'category_manager');
        return true;
    }
            
    return false;
    
}


/**
 * Get categories
 *
 * @return array
 */
function get_categories()
{
    global $db;
    
    // This function gets all categories from the database.
    $all_cats = array();
    $sql = "SELECT category_name, category_id, category_parent, category_desc, category_keywords FROM " . table_categories . " ORDER BY category_order ASC";
    $categories = $db->get_results($db->prepare($sql));
    $count = 0;
    foreach ($categories as $category) {
        $all_cats[$count]['category_name'] = urldecode($category->category_name);
        $all_cats[$count]['category_id'] = $category->category_id;
        $all_cats[$count]['category_parent'] = $category->category_parent;
        $all_cats[$count]['category_description'] = urldecode($category->category_desc);
        $all_cats[$count]['category_keywords'] = urldecode($category->category_keywords);
        $level = 1; 
        if ($category->category_parent == 1) { 
            $all_cats[$count]['category_level'] = $level;
        } else {
            $level = get_category_levels($category->category_parent, $level);
            $all_cats[$count]['category_level'] = $level;
        }
        if (is_empty($category->category_id)) {
            $all_cats[$count]['category_empty'] = true;
        } else {
            $all_cats[$count]['category_empty'] = false;
        }
        
        $count++;
    }
    
    return $all_cats;
}    


/**
 * Get category levels
 * 
 * @param int $parent
 * @param int $level
 * @return int
 */
function get_category_levels($parent, $level)
{
    global $db;
    
    $level++; 
    $sql = "SELECT category_parent FROM " . table_categories . " WHERE category_id = %d";
    $parent = $db->get_var($db->prepare($sql, $parent));
    if ($parent != 1) {
        $level = get_category_levels($parent, $level); // recursive function to find level depth
    }
    return $level;
}


/**
 * Order categories
 *
 * @param str $order_type
 */
function order($order_type)
{
    global $db, $current_user;
    
    $order = 1;
    $sql = "SELECT * FROM " . table_categories . " WHERE category_id != %d AND category_parent = %d ORDER BY $order_type ASC";
    $categories = $db->get_results($db->prepare($sql, 1, 1));
    if ($categories) {
        foreach ( $categories as $category ) {
            $sql = "UPDATE " . table_categories . " SET category_order = %d, category_updateby = %d WHERE category_id = %d";
            $db->query($db->prepare($sql, $order, $current_user->id, $category->category_id));
            // get and update children of this category
            $order = order_children($category, $order, $order_type);
            $order++;
        }
    }
    cat_man_rebuild_tree(1, 0);
}


/**
 * Order children
 *
 * @param array $category
 * @param int $order
 * @param str $order_type
 * @return int
 */
function order_children($category, $order, $order_type)
{
    global $db, $current_user;
    
    $sql = "SELECT * FROM " . table_categories . " WHERE category_parent = %d ORDER BY $order_type ASC";
    $children = $db->get_results($db->prepare($sql, $category->category_id));
    if ($children) {
        foreach ( $children as $child ) {
            $order++;
            $sql = "UPDATE " . table_categories . " SET category_order = %d, category_updateby = %d WHERE category_id = %d";
            $db->query($db->prepare($sql, $order, $current_user->id, $child->category_id));
            $order = order_children($child, $order, $order_type);
        }
    }
    return $order;
}


/**
 * Order by posts
 */
function order_by_posts()
{
    global $db, $current_user;
    
    $order = 1;
    $sql = "SELECT " . table_categories . ".*, COUNT(" . table_posts . ".post_category) as count, " . table_posts . ".post_category FROM " . table_categories . ", " . table_posts . " WHERE " . table_categories . ".category_id != %d AND " . table_categories . ".category_parent = %d AND " . table_categories . ".category_id = " . table_posts . ".post_category GROUP BY " . table_posts . ".post_category ORDER BY count DESC";
    
    $categories = $db->get_results($db->prepare($sql, 1, 1));
    if ($categories) {
        foreach ( $categories as $category ) {
        
            $sql = "UPDATE " . table_categories . " SET category_order = %d, category_updateby = %d WHERE category_id = %d";
            $db->query($db->prepare($sql, $order, $current_user->id, $category->category_id));
            // get and update children of this category
            $order = order_children_by_posts($category, $order);
            $order++;
        }
    } 
    
    //check for categories with zero posts
    $sql = "SELECT * FROM " . table_categories . " WHERE category_id != %d AND category_parent = %d ORDER BY category_name ASC";
    $categories = $db->get_results($db->prepare($sql, 1, 0));
    if ($categories) {
        foreach ( $categories as $category ) {
            $sql = "SELECT COUNT(post_category) as count, post_category FROM " . table_posts . " WHERE post_category = %d GROUP BY post_category";
            $posts = $db->get_results($db->prepare($sql, $category->category_id));
            if ($posts) { 
                // posts exist so we can ignore this category because it's already be done above
            } else {
                $order++;
                $sql = "UPDATE " . table_categories . " SET category_order = %d, category_updateby = %d WHERE category_id = %d";
                $db->query($db->prepare($sql, $order, $current_user->id, $category->category_id));
            }
        }
    }
}


/**
 * Order children by posts
 *
 * @param array $category
 * @param int $order
 * @return int
 */
function order_children_by_posts($category, $order)
{
    global $db, $current_user;
    
    $sql = "SELECT " . table_categories . ".*, COUNT(" . table_posts . ".post_category) as count, " . table_posts . ".post_category FROM " . table_categories . ", " . table_posts . " WHERE " . table_categories . ".category_parent = %d  AND " . table_categories . ".category_id = " . table_posts . ".post_category GROUP BY " . table_posts . ".post_category ORDER BY count DESC";
    $children = $db->get_results($db->prepare($sql, $category->category_id));
    if ($children) {
        foreach ( $children as $child ) {
            $order++;
            $sql = "UPDATE " . table_categories . " SET category_order = %d, category_updateby = %d WHERE category_id = %d";
            $db->query($db->prepare($sql, $order, $current_user->id, $child->category_id));
            $order = order_children_by_posts($child, $order);
        }
    } 
    
    //check for children with zero posts
        $sql = "SELECT * FROM " . table_categories . " WHERE category_parent = %d ORDER BY category_name ASC";
        $children = $db->get_results($db->prepare($sql, $category->category_id));
        if ($children) {
            foreach ( $children as $child ) {
                $sql = "SELECT COUNT(post_category) as count, post_category FROM " . table_posts . " WHERE post_category = %d GROUP BY post_category";
                $posts = $db->get_results($db->prepare($sql, $child->category_id));
                if ($posts) { 
                    // posts exist so we can ignore this child because it's already be done above
                } else {
                    $order++;
                    $sql = "UPDATE " . table_categories . " SET category_order = %d, category_updateby = %d WHERE category_id = %d";
                    $db->query($db->prepare($sql, $order, $current_user->id, $child->category_id));
                }
            }
        }
    return $order;
}


/**
 * Update category names
 */
function update_category_names()
{
    global $db, $cage, $current_user;
    
    $sql = "SELECT category_id, category_name, category_safe_name FROM " . table_categories . " WHERE category_id != %d ORDER BY category_order ASC";
    $categories = $db->get_results($db->prepare($sql, 1));
    foreach ( $categories as $category ) {
        $new_name = $cage->post->getMixedString2($category->category_id);
        if ($new_name != "") {
            $sql = "UPDATE " . table_categories . " SET category_name = %s, category_safe_name = %s, category_updateby = %d WHERE category_id = %d";
            $db->query($db->prepare($sql, urlencode($new_name), urlencode(make_url_friendly($new_name)), $current_user->id, $category->category_id));
        }
    }
}


/**
 * Add a new category
 *
 * @param int $parent
 * @param str $new_cat_name
 * @return bool
 */
function add_new_category($parent, $new_cat_name)
{
    global $db, $current_user;
    
    $sql = "SELECT category_order FROM " . table_categories . " WHERE category_id = %d";
    $category_order = $db->get_var($db->prepare($sql, $parent));
    
    $position = $category_order + 1; // our new category will go right after the parent category
    
    // return false if duplicate name
    $sql = "SELECT category_name FROM " . table_categories . " WHERE category_name = %s";
    $exists = $db->get_var($db->prepare($sql, urlencode($new_cat_name)));
    if ($exists) { return false; }
    
    // increment category_order for all categories after the parent:
    $sql = "SELECT category_id, category_name, category_order FROM " . table_categories . " WHERE category_order > %d ORDER BY category_order ASC";
    $categories = $db->get_results($db->prepare($sql, $category_order));    
    if ($categories) {
        foreach ( $categories as $category ) {
            $sql = "UPDATE " . table_categories . " SET category_order = category_order+1, category_updateby = %d WHERE category_id = %d";
            $db->query($db->prepare($sql, $current_user->id, $category->category_id));
        }
    }
    
    //insert new category after parent category:
    $sql = "INSERT INTO " . table_categories . " (category_parent, category_name, category_safe_name, category_order, category_updateby) VALUES (%d, %s, %s, %d, %d)";
    $db->query($db->prepare($sql, $parent, urlencode($new_cat_name), urlencode(make_url_friendly($new_cat_name)), $position, $current_user->id));
        
    cat_man_rebuild_tree(1, 0);
    
    return true;
}


/**
 * Check if category is empty
 *
 * @param int $cat_id
 * @return bool
 */
function is_empty($cat_id)
{
    global $db;
    
    $sql = "SELECT count(*) FROM " . table_posts . " WHERE post_category = %d ";
    $posts = $db->get_var($db->prepare($sql, $cat_id));
    if ($posts == 0) {
        return true;    //empty
    } else {
        return false;    //not empty
    }
}


/**
 * Move category
 *
 * @param int $cat_to_move
 * @param str $placement
 * @param str $target
 * @return bool
 */
function move($cat_to_move, $placement, $target)
{

    global $db, $current_user;
        
    if ($target) {
        if ($target == "top") { $target = 1; }
        $sql = "SELECT * FROM " . table_categories . " WHERE category_id = %d";
        $target = $db->get_row($db->prepare($sql, $target));
        if ($placement == "after") { // need to find the last child and assign that as the target.
            $skip_children = move_count($target->category_id, 0);
            $sql = "SELECT * FROM " . table_categories . " WHERE category_order = %d";
              $target = $db->get_row($db->prepare($sql, ($target->category_order+$skip_children)));
        }
        $target_id = $target->category_id;
        $target_order = $target->category_order;
        $target_parent = $target->category_parent;

        //echo "Move " . $cat_to_move . " " . $placement . " " . $target->category_name . "<br />";
        if ($placement == "before") {
            $sql = "SELECT * FROM " . table_categories . " WHERE category_id != %d AND category_order >= %d";
            $categories = $db->get_results($db->prepare($sql, 1, $target_order));
        } elseif (($placement == "after") || ($placement == "aschild") || ($placement == "none")) {
            $sql = "SELECT * FROM " . table_categories . " WHERE category_id != %d AND category_order > %d";
            $categories = $db->get_results($db->prepare($sql, 1, $target_order));
        } 
        
        //Count all children under the category we want to move so we can jump that many spaces and fill them in later.
        $number = 1;
        $number = move_count($cat_to_move, $number);
        if ($placement == "before") { $number++; }
        
        // NOW WE KNOW THERE ARE "JUMP" CATEGORIES IN THIS BRANCH. 
        
        if ($categories) {
            foreach ( $categories as $category ) {
                if (descendant($category, $cat_to_move, "no") == "no") { //if not cat_to_move or one of its descendants...
                    $sql = "UPDATE " . table_categories . " SET category_order = category_order + %d, category_updateby = %d WHERE category_id = %d";
                    $db->query($db->prepare($sql, $number, $current_user->id, $category->category_id));
                }
                //echo "Update " . $category->category_id . ", " . $category->category_name . ", with parent " . $category->category_parent . ". Assign order " . ($category->category_order+1) . "<br />";
            }
        }
        if ($placement == "before") {
            $sql = "UPDATE " . table_categories . " SET category_order = %d, category_updateby = %d WHERE category_id = %d";
            $db->query($db->prepare($sql, $target_order, $current_user->id, $cat_to_move));
        } elseif ($placement == "after") {
            $sql = "UPDATE " . table_categories . " SET category_order = %d, category_updateby = %d WHERE category_id = %d";
            $db->query($db->prepare($sql, ($target_order+1), $current_user->id, $cat_to_move));
        } elseif (($placement == "aschild") || ($placement == "none")) {
            $sql = "UPDATE " . table_categories . " SET category_order = %d, category_parent = %d, category_updateby = %d WHERE category_id = %d";
            $db->query($db->prepare($sql, ($target_order+1), $target_id, $current_user->id, $cat_to_move));
        }
        
        $target_order = $target_order+1;
        $target_parent = $cat_to_move;
        move_children($cat_to_move, $target_order, $target_parent); // update own children and their children etc.
        //echo "children moved to " . $target_parent . "<br />";
        cat_man_rebuild_tree(1, 0);
        $success = true;
    } else {
        $success = false;
    }
    clean_order();
    cat_man_rebuild_tree(1, 0);
    return $success;
}


/**
 * Move count
 *
 * @param int $category
 * @param int $number
 * @return int
 */
function move_count($category, $number)
{
    global $db;
    
    $sql = "SELECT category_parent, category_id FROM " . table_categories . " WHERE category_parent = %d";
    $children = $db->get_results($db->prepare($sql, $category));
    if ($children) {
        foreach ( $children as $child ) {
            $number++;
            $number = move_count($child->category_id, $number);
        }
    }
    return $number;
}


/**
 * Find children
 *
 * @param int $category
 * @param int $cat_to_move
 * @param str $descendant
 * @return int
 */
function descendant($category, $cat_to_move, $descendant)
{
    global $db;
    
    if ($category->category_id == $cat_to_move) {
        $descendant = "yes";
    } else {
        $sql = "SELECT category_parent, category_id FROM " . table_categories . " WHERE category_parent = %d";
        $children = $db->get_results($db->prepare($sql, $cat_to_move));
        if ($children) {
            foreach ( $children as $child ) {
                $descendant = descendant($category, $child->category_id, $descendant); 
            }
        }
    }
    if ($descendant != "yes") { $descendant = "no"; }
    return $descendant;
}


/**
 * Move children
 *
 * @param int $parent
 * @param int $order
 * @param int $target_parent
 * @return int
 */
function move_children($parent, $order, $target_parent)
{
    global $db, $current_user;
    
    $sql = "SELECT * FROM " . table_categories . " WHERE category_parent = %d";
    $children = $db->get_results($db->prepare($sql, $parent));
    if ($children) {
        foreach ( $children as $child ) {
            $order++;
            $sql = "UPDATE " . table_categories . " SET category_order = %d, category_parent = %d, category_updateby = %d WHERE category_id != %d AND category_id = %d";
            $db->query($db->prepare($sql, $order, $target_parent, $current_user->id, 1, $child->category_id));
            //echo "Update " . $child->category_parent . " with order " . $order . "<br />";
            
            $sql = "SELECT * FROM " . table_categories . " WHERE category_parent = %d";
            $children = $db->get_results($db->prepare($sql, $child->category_id));
            if ($children) {
                $target_parent = $child->category_id;
                $order = move_children($child->category_id, $order, $target_parent); // target_parent is our current child.
            } else {
                $order = move_children($child->category_id, $order, $target_parent); // target_parent is our current parent.
            }
        }
    }
    return $order;
}


/**
 * Get name for category to delete
 *
 * @param int $delete_id
 * @return string
 */
function get_name_for_delete_confirm($delete_id)
{
    global $db;
        
    $sql = "SELECT category_name FROM " . table_categories . " WHERE category_id = %d";
    $cat_name = $db->get_var($db->prepare($sql, $delete_id));
    return urldecode($cat_name);
}


/**
 * Delete categories
 *
 * @param int $delete_category
 */
function delete_categories($delete_category)
{
    global $db, $current_user;
    
    // First, we need to get the parent of this category
    $sql = "SELECT category_parent FROM  " . table_categories . " WHERE category_id = %d";
    $grandparent = $db->get_var($db->prepare($sql, $delete_category));
    // Second, we need to find children of this category and assign them to their "grandparent" instead
    $sql = "SELECT category_id, category_parent FROM  " . table_categories . " WHERE category_parent = %d";
    $children = $db->get_results($db->prepare($sql, $delete_category));
    if ($children) {
        foreach ($children as $child) {
            $sql = "UPDATE " . table_categories . " SET category_parent = %d, category_updateby = %d WHERE category_id = %d";
             $db->query($db->prepare($sql, $grandparent, $current_user->id, $child->category_id));
        }
    }    
    // Third, delete the category
    $sql = "DELETE FROM " . table_categories . " WHERE category_id = %d";
    $db->query($db->prepare($sql, $delete_category));
}


/**
 * Save meta
 *
 * @param int $id
 * @param str $desc
 * @param str $words
 */
function save_meta($id, $desc, $words)
{
    global $db, $current_user;
    
    $sql = "UPDATE " . table_categories . " SET category_desc = %s, category_keywords = %s, category_updateby = %d WHERE category_id = %d";
    $db->query($db->prepare($sql, urlencode($desc), urlencode($words), $current_user->id, $id)); 
}


/**
 * Remove gaps between order numbers
 */
function clean_order()
{    
    global $db, $current_user;
    
    $sql = "SELECT category_order, category_id FROM " . table_categories . " WHERE category_id != %d ORDER BY category_order ASC";
    $categories = $db->get_results($db->prepare($sql, 1));
    if ($categories) {
        $count = 1;
        foreach ( $categories as $category ) {
        $sql = "UPDATE " . table_categories . " SET category_order = %d, category_updateby = %d WHERE category_id = %d";
        $db->query($db->prepare($sql, $count, $current_user->id, $category->category_id));
        $count++;
        }
    }
}

/**
 * Rebuild category tree
 *
 * @param int $parent_id
 * @param int $left
 * @return int
 * @link http://www.sitepoint.com/article/hierarchical-data-database/3/
 */
function cat_man_rebuild_tree($parent_id, $left)
{
    global $db, $current_user;
    
    $right = $left+1;
    // get all children of this node
    $sql = "SELECT category_id FROM " . table_categories . " WHERE category_id != %d AND category_parent = %d ORDER BY category_order ASC";
    $categories = $db->get_results($db->prepare($sql, $parent_id, $parent_id));
    if ($categories) {
        foreach ($categories as $this_category) {
             $right = cat_man_rebuild_tree($this_category->category_id, $right);
        }
    }
    
    // we've got the left value, and now that we've processed
    // the children of this node we also know the right value
    $sql = "UPDATE " . table_categories . " SET lft = %d, rgt = %d, category_updateby = %d WHERE category_id = %d";
    $db->query($db->prepare($sql, $left, $right, $current_user->id, $parent_id));
    
    // return the right value of this node + 1
    return $right+1;
}

?>
