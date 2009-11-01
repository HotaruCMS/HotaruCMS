<?php
/**
 * File: plugins/category_manager/category_manager_settings.php
 * Purpose: The functions that do the hard work such as adding, deleting and sorting categories.
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
    
class CategoryManagerSettings extends CategoryManager
{
    /**
     * Main function that calls others
     *
     * @return bool
     */
    function settings()
    {    
        $action = $this->cage->get->testAlnumLines('action');
        if (!$action || $action == '') { $action = "home"; }
    
        if ($action == "home") {    
            $this->hotaru->vars['the_cats'] = $this->getCategories();     // Get all the category info                
            $this->hotaru->displayTemplate('cat_man_main', 'category_manager');
            return true;
        }
        
        if ($action == "order") { 
            $this->hotaru->vars['the_cats'] = $this->getCategories();     // Get all the category info
            $this->hotaru->displayTemplate('cat_man_order', 'category_manager');
            return true;
        }     
            
        if ($action == "order_alpha") { 
            $this->order("category_name");     // ORDER ALPHABETICALLY PERMANENTLY IN THE DATABASE
            $this->hotaru->showMessage($this->lang["cat_man_order_alpha"], 'green');
            $this->hotaru->vars['the_cats'] = $this->getCategories();     // Get all the category info
            $this->hotaru->displayTemplate('cat_man_order', 'category_manager');
            return true;
        } 
        
        if ($action == "order_length") { 
            $this->order("length(category_name)");     // ORDER BY LENGTH PERMANENTLY IN THE DATABASE
            $this->hotaru->showMessage($this->lang["cat_man_order_length"], 'green');
            $this->hotaru->vars['the_cats'] = $this->getCategories();     // Get all the category info
            $this->hotaru->displayTemplate('cat_man_order', 'category_manager');
            return true;
        }
    
        if ($action == "order_posts") { 
            $this->orderByPosts();     // ORDER BY POSTS PERMANENTLY IN THE DATABASE
            $this->hotaru->showMessage($this->lang["cat_man_order_posts"], 'green');
            $this->hotaru->vars['the_cats'] = $this->getCategories();     // Get all the category info
            $this->hotaru->displayTemplate('cat_man_order', 'category_manager');
            return true;
        }
    
        if ($action == "order_id") { 
            $this->order("category_id");     // ORDER BY ID PERMANENTLY IN THE DATABASE
            $this->hotaru->showMessage($this->lang["cat_man_order_id"], 'green');
            $this->hotaru->vars['the_cats'] = $this->getCategories();     // Get all the category info
            $this->hotaru->displayTemplate('cat_man_order', 'category_manager');
            return true;
        }
                    
        if ($action == "edit") { 
            $this->hotaru->vars['the_cats'] = $this->getCategories();     // Get all the category info
            $this->hotaru->displayTemplate('cat_man_edit', 'category_manager');
            return true;
        } 
        
        if ($action == "edit_save") { 
            if ($this->cage->post->keyExists('save_all')) {
                $this->updateCategoryNames();
                $this->hotaru->showMessage($this->lang["cat_man_changes_saved"], 'green');
            } else {
                $this->hotaru->showMessage($this->lang["cat_man_changes_cancelled"], 'green');
            }
            $this->hotaru->vars['the_cats'] = $this->getCategories();     // Get all the category info
            $this->hotaru->displayTemplate('cat_man_edit', 'category_manager');
            return true;
        } 
        
        if ($action == "edit_meta") { 
            $this->hotaru->vars['the_cats'] = $this->getCategories();     // Get all the category info
            $this->hotaru->displayTemplate('cat_man_edit_meta', 'category_manager');
            return true;
        } 
        
        if ($action == "edit_meta_save") { 
            if ($this->cage->get->keyExists('id')){ 
                $category_meta_id = $this->cage->get->getInt('id');
                if ($this->cage->post->keyExists('save_edit_meta')) {
                    $description = $this->cage->post->getMixedString2('description');
                    $keywords = $this->cage->post->getMixedString2('keywords');
                    $this->saveMeta($category_meta_id, $description, $keywords);
                    $this->hotaru->showMessage($this->lang["cat_man_changes_saved"], 'green');
                }
            } else {
                $this->hotaru->showMessage($this->lang["cat_man_form_error"], 'red');
            }
            $this->hotaru->vars['the_cats'] = $this->getCategories();     // Get all the category info
            $this->hotaru->displayTemplate('cat_man_edit_meta', 'category_manager');
            return true;
        } 
    
        if ($action == "add") { 
            $this->hotaru->vars['the_cats'] = $this->getCategories();     // Get all the category info
            $this->hotaru->displayTemplate('cat_man_add', 'category_manager');
            return true;
        } 
        
        if ($action == "add_save") { 
            if ($this->cage->post->keyExists('save_new_category1')) {
                $parent = 1; // parent is "all" because this is a main category
                $new_cat_name = $this->cage->post->getMixedString2('new_category');
                if ($new_cat_name != "") {
                    $result = $this->addNewCategory($parent, $new_cat_name);
                    if ($result) {
                        $this->hotaru->showMessage($this->lang["cat_man_category_added"], 'green');
                    } else {
                        $this->hotaru->showMessage($this->lang["cat_man_category_exists"], 'red');
                    }
                } else {
                    $this->hotaru->showMessage($this->lang["cat_man_category_not_added"], 'red');
                }
            } elseif ($this->cage->post->keyExists('save_new_category2')) {
                $parent = $this->cage->post->getInt('parent');
                $new_cat_name = $this->cage->post->getMixedString2('new_category');
                if ($new_cat_name != "") {
                    $result = $this->addNewCategory($parent, $new_cat_name);
                    if ($result) {
                        $this->hotaru->showMessage($this->lang["cat_man_category_added"], 'green');
                    } else {
                        $this->hotaru->showMessage($this->lang["cat_man_category_exists"], 'red');
                    }
                } else {
                    $this->hotaru->showMessage($this->lang["cat_man_category_not_added"], 'red');
                }
            } elseif ($this->cage->post->keyExists('save_new_category3')) {
                $parent = $this->cage->post->getInt('parent');
                $new_cat_name = $this->cage->post->getMixedString2('new_category');
                if ($new_cat_name != "") {
                    $result = $this->addNewCategory($parent, $new_cat_name);
                    if ($result) {
                        $this->hotaru->showMessage($this->lang["cat_man_category_added"], 'green');
                    } else {
                        $this->hotaru->showMessage($this->lang["cat_man_category_exists"], 'red');
                    }
                } else {
                    $this->hotaru->showMessage($this->lang["cat_man_category_not_added"], 'red');
                }
            } else {
                $this->hotaru->showMessage($this->lang["cat_man_form_error"], 'red');    
            }
            $this->hotaru->vars['the_cats'] = $this->getCategories();     // Get all the category info
            $this->hotaru->displayTemplate('cat_man_add', 'category_manager');
            return true;
        }
        
        if ($action == "move") { 
            $this->hotaru->vars['the_cats'] = $this->getCategories();     // Get all the category info
            $this->hotaru->displayTemplate('cat_man_move', 'category_manager');
            return true;
        } 
        
        if ($action == "move_save") {
            if ($this->cage->get->keyExists('id')) {
                $cat_to_move = $this->cage->get->getInt('id');
                //echo "Moving category " . $cat_to_move . "<br />";
                if ($this->cage->post->keyExists('save_form1')) {
                    $placement = $this->cage->post->testAlpha('placement');
                    $target = $this->cage->post->testAlnum('parents');
                    $success = $this->move($cat_to_move, $placement, $target);
                    if ($success) { 
                        $this->hotaru->showMessage($this->lang["cat_man_category_moved"], 'green');
                    } else { 
                        $this->hotaru->showMessage($this->lang["cat_man_category_not_moved"], 'red');
                    }
    
                } elseif ($this->cage->post->keyExists('save_form2')) {
                    $target = $this->cage->post->testAlnum('moveup');
                    $success = $this->move($cat_to_move, 'none', $target);
                    if ($success) { 
                        $this->hotaru->showMessage($this->lang["cat_man_category_moved"], 'green');
                    } else { 
                        $this->hotaru->showMessage($this->lang["cat_man_category_not_moved"], 'red');
                    }
                } else {
                    $this->hotaru->showMessage($this->lang["cat_man_category_not_moved"], 'red');
                }
                
            } else { 
                $this->hotaru->showMessage($this->lang["cat_man_category_not_moved"], 'red');
            }
            $this->hotaru->vars['the_cats'] = $this->getCategories();     // Get all the category info
            $this->hotaru->displayTemplate('cat_man_move', 'category_manager');
            return true;
        }
    
        if ($action == "delete") { 
            $this->hotaru->vars['the_cats'] = $this->getCategories();     // Get all the category info
            $this->hotaru->displayTemplate('cat_man_delete', 'category_manager');
            return true;
        } 
        
        if ($action == "delete_save") { 
            $this->hotaru->vars['delete_list'] = array(); $del_count = 0;
            if ($this->cage->post->keyExists('delete') && $this->cage->post->keyExists('delete_cats')) {
                foreach ($this->cage->post->getInt('delete_cats') as $category_id=>$check) { 
                    if ($check > 0) { 
                        $this->hotaru->vars['delete_list'][$del_count]['del_id'] = $category_id;
                        $this->hotaru->vars['delete_list'][$del_count]['del_name'] = $this->getNameForDeleteConfirm($category_id);
                    }
                    $del_count++;
                }
                $this->hotaru->vars['the_cats'] = $this->getCategories();     // Get all the category info
                $this->hotaru->displayTemplate('cat_man_delete_confirm', 'category_manager');
                return true;
            } else {
                $this->hotaru->showMessage($this->lang["cat_man_category_not_deleted"], 'red');
                $this->hotaru->vars['the_cats'] = $this->getCategories();     // Get all the category info
                $this->hotaru->displayTemplate('cat_man_delete', 'category_manager');
                return true;
            }
            
        }
        
        if ($action == "delete_confirm") {
            if ($this->cage->post->keyExists('delete_confirm_yes') && $this->cage->post->keyExists('delete_list')) {
                foreach ($this->cage->post->getInt('delete_list') as $cat_id) { 
                        $this->deleteCategories($cat_id); 
                }
                $this->rebuildTree(1, 0);
                $this->hotaru->showMessage($this->lang["cat_man_category_deleted"], 'green');
            } else {
                $this->hotaru->showMessage($this->lang["cat_man_category_not_deleted"], 'red');
                $this->hotaru->showMessage();
            }
    
            $this->hotaru->vars['the_cats'] = $this->getCategories();     // Get all the category info
            $this->hotaru->displayTemplate('cat_man_delete', 'category_manager');
            return true;
        }
                
        return false;
        
    }
    
    
    /**
     * Get categories
     *
     * @return array
     */
    function getCategories()
    {
        // This function gets all categories from the database.
        $all_cats = array();
        $sql = "SELECT category_name, category_id, category_parent, category_desc, category_keywords FROM " . TABLE_CATEGORIES . " ORDER BY category_order ASC";
        $categories = $this->db->get_results($this->db->prepare($sql));
        $count = 0;
        foreach ($categories as $category) {
            $all_cats[$count]['category_name'] = stripslashes(urldecode($category->category_name));
            $all_cats[$count]['category_id'] = $category->category_id;
            $all_cats[$count]['category_parent'] = $category->category_parent;
            $all_cats[$count]['category_description'] = stripslashes(urldecode($category->category_desc));
            $all_cats[$count]['category_keywords'] = stripslashes(urldecode($category->category_keywords));
            $level = 1; 
            if ($category->category_parent == 1) { 
                $all_cats[$count]['category_level'] = $level;
            } else {
                $level = $this->getCategoryLevels($category->category_parent, $level);
                $all_cats[$count]['category_level'] = $level;
            }
            if ($this->isEmpty($category->category_id)) {
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
    function getCategoryLevels($parent, $level)
    {
        $level++; 
        $sql = "SELECT category_parent FROM " . TABLE_CATEGORIES . " WHERE category_id = %d";
        $parent = $this->db->get_var($this->db->prepare($sql, $parent));
        if ($parent != 1) {
            $level = $this->getCategoryLevels($parent, $level); // recursive function to find level depth
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
        $order = 1;
        $sql = "SELECT * FROM " . TABLE_CATEGORIES . " WHERE category_id != %d AND category_parent = %d ORDER BY $order_type ASC";
        $categories = $this->db->get_results($this->db->prepare($sql, 1, 1));
        if ($categories) {
            foreach ( $categories as $category ) {
                $sql = "UPDATE " . TABLE_CATEGORIES . " SET category_order = %d, category_updateby = %d WHERE category_id = %d";
                $this->db->query($this->db->prepare($sql, $order, $current_user->id, $category->category_id));
                // get and update children of this category
                $order = $this->orderChildren($category, $order, $order_type);
                $order++;
            }
        }
        $this->rebuildTree(1, 0);
    }
    
    
    /**
     * Order children
     *
     * @param array $category
     * @param int $order
     * @param str $order_type
     * @return int
     */
    function orderChildren($category, $order, $order_type)
    {
        $sql = "SELECT * FROM " . TABLE_CATEGORIES . " WHERE category_parent = %d ORDER BY $order_type ASC";
        $children = $this->db->get_results($this->db->prepare($sql, $category->category_id));
        if ($children) {
            foreach ( $children as $child ) {
                $order++;
                $sql = "UPDATE " . TABLE_CATEGORIES . " SET category_order = %d, category_updateby = %d WHERE category_id = %d";
                $this->db->query($this->db->prepare($sql, $order, $current_user->id, $child->category_id));
                $order = $this->orderChildren($child, $order, $order_type);
            }
        }
        return $order;
    }
    
    
    /**
     * Order by posts
     */
    function orderByPosts()
    {
        $order = 1;
        $sql = "SELECT " . TABLE_CATEGORIES . ".*, COUNT(" . TABLE_POSTS . ".post_category) as count, " . TABLE_POSTS . ".post_category FROM " . TABLE_CATEGORIES . ", " . TABLE_POSTS . " WHERE " . TABLE_CATEGORIES . ".category_id != %d AND " . TABLE_CATEGORIES . ".category_parent = %d AND " . TABLE_CATEGORIES . ".category_id = " . TABLE_POSTS . ".post_category GROUP BY " . TABLE_POSTS . ".post_category ORDER BY count DESC";
        
        $categories = $this->db->get_results($this->db->prepare($sql, 1, 1));
        if ($categories) {
            foreach ( $categories as $category ) {
            
                $sql = "UPDATE " . TABLE_CATEGORIES . " SET category_order = %d, category_updateby = %d WHERE category_id = %d";
                $this->db->query($this->db->prepare($sql, $order, $current_user->id, $category->category_id));
                // get and update children of this category
                $order = $this->orderChildrenByPosts($category, $order);
                $order++;
            }
        } 
        
        //check for categories with zero posts
        $sql = "SELECT * FROM " . TABLE_CATEGORIES . " WHERE category_id != %d AND category_parent = %d ORDER BY category_name ASC";
        $categories = $this->db->get_results($this->db->prepare($sql, 1, 0));
        if ($categories) {
            foreach ( $categories as $category ) {
                $sql = "SELECT COUNT(post_category) as count, post_category FROM " . TABLE_POSTS . " WHERE post_category = %d GROUP BY post_category";
                $posts = $this->db->get_results($this->db->prepare($sql, $category->category_id));
                if ($posts) { 
                    // posts exist so we can ignore this category because it's already be done above
                } else {
                    $order++;
                    $sql = "UPDATE " . TABLE_CATEGORIES . " SET category_order = %d, category_updateby = %d WHERE category_id = %d";
                    $this->db->query($this->db->prepare($sql, $order, $current_user->id, $category->category_id));
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
    function orderChildrenByPosts($category, $order)
    {
        $sql = "SELECT " . TABLE_CATEGORIES . ".*, COUNT(" . TABLE_POSTS . ".post_category) as count, " . TABLE_POSTS . ".post_category FROM " . TABLE_CATEGORIES . ", " . TABLE_POSTS . " WHERE " . TABLE_CATEGORIES . ".category_parent = %d  AND " . TABLE_CATEGORIES . ".category_id = " . TABLE_POSTS . ".post_category GROUP BY " . TABLE_POSTS . ".post_category ORDER BY count DESC";
        $children = $this->db->get_results($this->db->prepare($sql, $category->category_id));
        if ($children) {
            foreach ( $children as $child ) {
                $order++;
                $sql = "UPDATE " . TABLE_CATEGORIES . " SET category_order = %d, category_updateby = %d WHERE category_id = %d";
                $this->db->query($this->db->prepare($sql, $order, $current_user->id, $child->category_id));
                $order = $this->orderChildrenByPosts($child, $order);
            }
        } 
        
        //check for children with zero posts
            $sql = "SELECT * FROM " . TABLE_CATEGORIES . " WHERE category_parent = %d ORDER BY category_name ASC";
            $children = $this->db->get_results($this->db->prepare($sql, $category->category_id));
            if ($children) {
                foreach ( $children as $child ) {
                    $sql = "SELECT COUNT(post_category) as count, post_category FROM " . TABLE_POSTS . " WHERE post_category = %d GROUP BY post_category";
                    $posts = $this->db->get_results($this->db->prepare($sql, $child->category_id));
                    if ($posts) { 
                        // posts exist so we can ignore this child because it's already be done above
                    } else {
                        $order++;
                        $sql = "UPDATE " . TABLE_CATEGORIES . " SET category_order = %d, category_updateby = %d WHERE category_id = %d";
                        $this->db->query($this->db->prepare($sql, $order, $current_user->id, $child->category_id));
                    }
                }
            }
        return $order;
    }
    
    
    /**
     * Update category names
     */
    function updateCategoryNames()
    {
        $sql = "SELECT category_id, category_name, category_safe_name FROM " . TABLE_CATEGORIES . " WHERE category_id != %d ORDER BY category_order ASC";
        $categories = $this->db->get_results($this->db->prepare($sql, 1));
        foreach ( $categories as $category ) {
            $new_name = $this->cage->post->getMixedString2($category->category_id);
            if ($new_name != "") {
                $sql = "UPDATE " . TABLE_CATEGORIES . " SET category_name = %s, category_safe_name = %s, category_updateby = %d WHERE category_id = %d";
                $this->db->query($this->db->prepare($sql, urlencode($new_name), urlencode(make_url_friendly($new_name)), $current_user->id, $category->category_id));
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
    function addNewCategory($parent, $new_cat_name)
    {
        $sql = "SELECT category_order FROM " . TABLE_CATEGORIES . " WHERE category_id = %d";
        $category_order = $this->db->get_var($this->db->prepare($sql, $parent));
        
        $position = $category_order + 1; // our new category will go right after the parent category
        
        // return false if duplicate name
        $sql = "SELECT category_name FROM " . TABLE_CATEGORIES . " WHERE category_name = %s";
        $exists = $this->db->get_var($this->db->prepare($sql, urlencode($new_cat_name)));
        if ($exists) { return false; }
        
        // increment category_order for all categories after the parent:
        $sql = "SELECT category_id, category_name, category_order FROM " . TABLE_CATEGORIES . " WHERE category_order > %d ORDER BY category_order ASC";
        $categories = $this->db->get_results($this->db->prepare($sql, $category_order));    
        if ($categories) {
            foreach ( $categories as $category ) {
                $sql = "UPDATE " . TABLE_CATEGORIES . " SET category_order = category_order+1, category_updateby = %d WHERE category_id = %d";
                $this->db->query($this->db->prepare($sql, $current_user->id, $category->category_id));
            }
        }
        
        //insert new category after parent category:
        $sql = "INSERT INTO " . TABLE_CATEGORIES . " (category_parent, category_name, category_safe_name, category_order, category_updateby) VALUES (%d, %s, %s, %d, %d)";
        $this->db->query($this->db->prepare($sql, $parent, urlencode($new_cat_name), urlencode(make_url_friendly($new_cat_name)), $position, $current_user->id));
            
        $this->rebuildTree(1, 0);
        
        return true;
    }
    
    
    /**
     * Check if category is empty
     *
     * @param int $cat_id
     * @return bool
     */
    function isEmpty($cat_id)
    {
        $sql = "SELECT count(*) FROM " . TABLE_POSTS . " WHERE post_category = %d ";
        $posts = $this->db->get_var($this->db->prepare($sql, $cat_id));
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
        if ($target) {
            if ($target == "top") { $target = 1; }
            $sql = "SELECT * FROM " . TABLE_CATEGORIES . " WHERE category_id = %d";
            $target = $this->db->get_row($this->db->prepare($sql, $target));
            if ($placement == "after") { // need to find the last child and assign that as the target.
                $skip_children = move_count($target->category_id, 0);
                $sql = "SELECT * FROM " . TABLE_CATEGORIES . " WHERE category_order = %d";
                  $target = $this->db->get_row($this->db->prepare($sql, ($target->category_order+$skip_children)));
            }
            $target_id = $target->category_id;
            $target_order = $target->category_order;
            $target_parent = $target->category_parent;
    
            //echo "Move " . $cat_to_move . " " . $placement . " " . $target->category_name . "<br />";
            if ($placement == "before") {
                $sql = "SELECT * FROM " . TABLE_CATEGORIES . " WHERE category_id != %d AND category_order >= %d";
                $categories = $this->db->get_results($this->db->prepare($sql, 1, $target_order));
            } elseif (($placement == "after") || ($placement == "aschild") || ($placement == "none")) {
                $sql = "SELECT * FROM " . TABLE_CATEGORIES . " WHERE category_id != %d AND category_order > %d";
                $categories = $this->db->get_results($this->db->prepare($sql, 1, $target_order));
            } 
            
            //Count all children under the category we want to move so we can jump that many spaces and fill them in later.
            $number = 1;
            $number = $this->moveCount($cat_to_move, $number);
            if ($placement == "before") { $number++; }
            
            // NOW WE KNOW THERE ARE "JUMP" CATEGORIES IN THIS BRANCH. 
            
            if ($categories) {
                foreach ( $categories as $category ) {
                    if ($this->descendant($category, $cat_to_move, "no") == "no") { //if not cat_to_move or one of its descendants...
                        $sql = "UPDATE " . TABLE_CATEGORIES . " SET category_order = category_order + %d, category_updateby = %d WHERE category_id = %d";
                        $this->db->query($this->db->prepare($sql, $number, $current_user->id, $category->category_id));
                    }
                    //echo "Update " . $category->category_id . ", " . $category->category_name . ", with parent " . $category->category_parent . ". Assign order " . ($category->category_order+1) . "<br />";
                }
            }
            if ($placement == "before") {
                $sql = "UPDATE " . TABLE_CATEGORIES . " SET category_order = %d, category_updateby = %d WHERE category_id = %d";
                $this->db->query($this->db->prepare($sql, $target_order, $current_user->id, $cat_to_move));
            } elseif ($placement == "after") {
                $sql = "UPDATE " . TABLE_CATEGORIES . " SET category_order = %d, category_updateby = %d WHERE category_id = %d";
                $this->db->query($this->db->prepare($sql, ($target_order+1), $current_user->id, $cat_to_move));
            } elseif (($placement == "aschild") || ($placement == "none")) {
                $sql = "UPDATE " . TABLE_CATEGORIES . " SET category_order = %d, category_parent = %d, category_updateby = %d WHERE category_id = %d";
                $this->db->query($this->db->prepare($sql, ($target_order+1), $target_id, $current_user->id, $cat_to_move));
            }
            
            $target_order = $target_order+1;
            $target_parent = $cat_to_move;
            $this->moveChildren($cat_to_move, $target_order, $target_parent); // update own children and their children etc.
            //echo "children moved to " . $target_parent . "<br />";
            $this->rebuildTree(1, 0);
            $success = true;
        } else {
            $success = false;
        }
        $this->cleanOrder();
        $this->rebuildTree(1, 0);
        return $success;
    }
    
    
    /**
     * Move count
     *
     * @param int $category
     * @param int $number
     * @return int
     */
    function moveCount($category, $number)
    {
        $sql = "SELECT category_parent, category_id FROM " . TABLE_CATEGORIES . " WHERE category_parent = %d";
        $children = $this->db->get_results($this->db->prepare($sql, $category));
        if ($children) {
            foreach ( $children as $child ) {
                $number++;
                $number = $this->moveCount($child->category_id, $number);
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
        if ($category->category_id == $cat_to_move) {
            $descendant = "yes";
        } else {
            $sql = "SELECT category_parent, category_id FROM " . TABLE_CATEGORIES . " WHERE category_parent = %d";
            $children = $this->db->get_results($this->db->prepare($sql, $cat_to_move));
            if ($children) {
                foreach ( $children as $child ) {
                    $descendant = $this->descendant($category, $child->category_id, $descendant); 
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
    function moveChildren($parent, $order, $target_parent)
    {
        $sql = "SELECT * FROM " . TABLE_CATEGORIES . " WHERE category_parent = %d";
        $children = $this->db->get_results($this->db->prepare($sql, $parent));
        if ($children) {
            foreach ( $children as $child ) {
                $order++;
                $sql = "UPDATE " . TABLE_CATEGORIES . " SET category_order = %d, category_parent = %d, category_updateby = %d WHERE category_id != %d AND category_id = %d";
                $this->db->query($this->db->prepare($sql, $order, $target_parent, $current_user->id, 1, $child->category_id));
                //echo "Update " . $child->category_parent . " with order " . $order . "<br />";
                
                $sql = "SELECT * FROM " . TABLE_CATEGORIES . " WHERE category_parent = %d";
                $children = $this->db->get_results($this->db->prepare($sql, $child->category_id));
                if ($children) {
                    $target_parent = $child->category_id;
                    $order = $this->moveChildren($child->category_id, $order, $target_parent); // target_parent is our current child.
                } else {
                    $order = $this->moveChildren($child->category_id, $order, $target_parent); // target_parent is our current parent.
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
    function getNameForDeleteConfirm($delete_id)
    {
        $sql = "SELECT category_name FROM " . TABLE_CATEGORIES . " WHERE category_id = %d";
        $cat_name = $this->db->get_var($this->db->prepare($sql, $delete_id));
        return urldecode($cat_name);
    }
    
    
    /**
     * Delete categories
     *
     * @param int $delete_category
     */
    function deleteCategories($delete_category)
    {
        // First, we need to get the parent of this category
        $sql = "SELECT category_parent FROM  " . TABLE_CATEGORIES . " WHERE category_id = %d";
        $grandparent = $this->db->get_var($this->db->prepare($sql, $delete_category));
        // Second, we need to find children of this category and assign them to their "grandparent" instead
        $sql = "SELECT category_id, category_parent FROM  " . TABLE_CATEGORIES . " WHERE category_parent = %d";
        $children = $this->db->get_results($this->db->prepare($sql, $delete_category));
        if ($children) {
            foreach ($children as $child) {
                $sql = "UPDATE " . TABLE_CATEGORIES . " SET category_parent = %d, category_updateby = %d WHERE category_id = %d";
                 $this->db->query($this->db->prepare($sql, $grandparent, $current_user->id, $child->category_id));
            }
        }    
        // Third, delete the category
        $sql = "DELETE FROM " . TABLE_CATEGORIES . " WHERE category_id = %d";
        $this->db->query($this->db->prepare($sql, $delete_category));
    }
    
    
    /**
     * Save meta
     *
     * @param int $id
     * @param str $desc
     * @param str $words
     */
    function saveMeta($id, $desc, $words)
    {
        $sql = "UPDATE " . TABLE_CATEGORIES . " SET category_desc = %s, category_keywords = %s, category_updateby = %d WHERE category_id = %d";
        $this->db->query($this->db->prepare($sql, urlencode($desc), urlencode($words), $current_user->id, $id)); 
    }
    
    
    /**
     * Remove gaps between order numbers
     */
    function cleanOrder()
    {    
        $sql = "SELECT category_order, category_id FROM " . TABLE_CATEGORIES . " WHERE category_id != %d ORDER BY category_order ASC";
        $categories = $this->db->get_results($this->db->prepare($sql, 1));
        if ($categories) {
            $count = 1;
            foreach ( $categories as $category ) {
            $sql = "UPDATE " . TABLE_CATEGORIES . " SET category_order = %d, category_updateby = %d WHERE category_id = %d";
            $this->db->query($this->db->prepare($sql, $count, $current_user->id, $category->category_id));
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
    function rebuildTree($parent_id, $left)
    {
        $right = $left+1;
        // get all children of this node
        $sql = "SELECT category_id FROM " . TABLE_CATEGORIES . " WHERE category_id != %d AND category_parent = %d ORDER BY category_order ASC";
        $categories = $this->db->get_results($this->db->prepare($sql, $parent_id, $parent_id));
        if ($categories) {
            foreach ($categories as $this_category) {
                 $right = $this->rebuildTree($this_category->category_id, $right);
            }
        }
        
        // we've got the left value, and now that we've processed
        // the children of this node we also know the right value
        $sql = "UPDATE " . TABLE_CATEGORIES . " SET lft = %d, rgt = %d, category_updateby = %d WHERE category_id = %d";
        $this->db->query($this->db->prepare($sql, $left, $right, $current_user->id, $parent_id));
        
        // return the right value of this node + 1
        return $right+1;
    }
    
    
     /**
     * Display category tree.
     *
     * @param array $the_cats
     */
    function tree($the_cats)
    {
        echo "<div class='tree'>";
        foreach ($the_cats as $cat) {
            if ($cat['category_name'] != "all") {
                if ($cat['category_parent'] > 1) {
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

}    
?>