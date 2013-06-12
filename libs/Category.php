<?php
/**
 * The Category class contains some useful methods for using Categories
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
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

class Category
{
	/**
	 * Returns the category id for a given category safe name.
	 *
	 * @param string $cat_name
	 * @return int
	 */
	public function getCatId($h, $cat_safe_name)
	{
		$sql = "SELECT category_id FROM " . TABLE_CATEGORIES . " WHERE category_safe_name = %s";
		$cat_id = $h->db->get_var($h->db->prepare($sql, $cat_safe_name));
		return $cat_id;
	}
	
	
	/**
	 * Returns the category name for a given category id or safe name.
	 *
	 * @param int $cat_id
	 * @param string $cat_safe_name
	 * @return string
	 */
	public function getCatName($h, $cat_id = 0, $cat_safe_name = '')
	{
                if (!ACTIVERECORD) {
                    if ($cat_id == 0 && $cat_safe_name != '') {
                            // Use safe name
                            $sql = "SELECT category_name FROM " . TABLE_CATEGORIES . " WHERE category_safe_name = %s";
                            $cat_name = $h->db->get_var($h->db->prepare($sql, $cat_safe_name));
                    } else {
                            // Use id
                            $sql = "SELECT category_name FROM " . TABLE_CATEGORIES . " WHERE category_id = %d";
                            $cat_name = $h->db->get_var($h->db->prepare($sql, $cat_id));
                    }
                } else {
                    
                }
		return urldecode($cat_name);
	}
	
	
	/**
	 * Returns the category safe name for a given category id 
	 *
	 * @param int $cat_id
	 * @return string
	 */
	public function getCatSafeName($h, $cat_id = 0)
	{
		// Build SQL
		$query = "SELECT category_safe_name FROM " . TABLE_CATEGORIES . " WHERE category_id = %d";
		$sql = $h->db->prepare($query, $cat_id);
		
		// Create temp cache array
		if (!isset($h->vars['tempCategoryCache'])) { $h->vars['tempCategoryCache'] = array(); }
		
		// If this query has already been read once this page load, we should have it in memory...
		if (array_key_exists($sql, $h->vars['tempCategoryCache'])) {
			// Fetch from memory
			$cat_safe_name = $h->vars['tempCategoryCache'][$sql];
		} else {
			// Fetch from database
			$cat_safe_name = $h->db->get_var($sql);
			$h->vars['tempCategoryCache'][$sql] = $cat_safe_name;
		}
		
		return urldecode($cat_safe_name);
	}
	
	
	/**
	 * Recursive public function to find level depth
	 *
	 * @param int $cat_id
	 * @return int
	 */
	public function getCatLevel($h, $cat_id, $cat_level, $the_cats)
	{
		foreach ($the_cats as $cat) {
			if (($cat->category_id == $cat_id) && $cat->category_parent > 1) {
				$cat_level++;
				$this->getCatLevel($h, $cat->category_parent, $cat_level, $the_cats);
			}
		}
		
		return $cat_level;
	}
	
	
	/**
	 * Returns parent id
	 *
	 * @param int $cat_id
	 * @return int
	 */
	public function getCatParent($h, $cat_id)
	{
		$sql = "SELECT category_parent FROM " . TABLE_CATEGORIES . " WHERE category_id = %d";
		$cat_id = $h->db->get_var($h->db->prepare($sql, $cat_id));
		if ($cat_id) { return $cat_id; } else { return false; }
	}
	
	
	/**
	 * Returns child ids
	 *
	 * @param int $cat_parent_id
	 * @return int
	 */
	public function getCatChildren($h, $cat_parent_id)
	{
		$sql = "SELECT category_id FROM " . TABLE_CATEGORIES . " WHERE category_parent = %d";
		$cat_children_ids = $h->db->get_results($h->db->prepare($sql, $cat_parent_id));
		if ($cat_children_ids) { return $cat_children_ids; } else { return false; }
	}
	
	
	/**
	 * Returns meta description and keywords for the category (if available)
	 *
	 * @param int $cat_id
	 * @return array|false
	 */
	public function getCatMeta($h, $cat_id)
	{
		$sql = "SELECT category_desc, category_keywords FROM " . TABLE_CATEGORIES . " WHERE category_id = %d";
		$cat_meta = $h->db->get_row($h->db->prepare($sql, $cat_id));
		if ($cat_meta) { return $cat_meta; } else { return false; }
	}
	
	
	 /**
	 * Returns all categories
	 *
	 * @param array $args
	 * @return int
	 */
	public function getCategories($h, $args = array())
	{
		if (isset($args['cat_parent'])) {
			$where = " WHERE category_parent = %d" ;
			$where_d = $args['cat_parent'];
		}
		else {
			$where = '';
			$where_d = '';
		}
		
		if (isset($args["orderby"])) {
			$orderBy = " ORDER BY " . $args["orderby"] . " ";
			if (isset($args['order'])) {
				if ($args["order"] == 'ASC' | $args["order"] == 'DESC') {
					$orderBy .= $args["order"];
					} else {$orderby .= 'ASC'; }}
		}
		else
		{ 
			$orderBy = ''; 
		}
		
		$sql = "SELECT * FROM " . TABLE_CATEGORIES . $where . $orderBy ;
		
		$categories = $h->db->get_results($h->db->prepare($sql, $where_d));

		// if asked to retrieve Levels and Empty status as well
		if (isset($args['levels'])) {		

			$count = 0;
			foreach ($categories as $category) {
			if ($category->category_parent == 1) {
				$category->category_level = 1;
			} else {
				$level = $this->getCatLevel($h, $category->category_id, 1, $categories);
				$category->category_level = $level;
			}
			if ($this->isCatEmpty($h, $category->category_id)) {
				$category->category_empty = true;
			} else {
				$category->category_empty = false;
			}
			}
		}

		if ($categories) { return $categories; } else { return false; }
	}


	/**
	 * Check if category is empty
	 *
	 * @param int $cat_id
	 * @return bool
	 */
	public function isCatEmpty($h, $cat_id = 0)
	{
		$sql = "SELECT count(post_id) FROM " . TABLE_POSTS . " WHERE post_category = %d ";
		$posts = $h->db->get_var($h->db->prepare($sql, $cat_id));
		if ($posts == 0) {
			return true;	//empty
		} else {
			return false;	//not empty
		}
	}


	/**
	 * Add a new category
	 *
	 * @param int $parent
	 * @param str $new_cat_name
	 * @return bool
	 */
	public function addCategory($h, $parent = 0, $new_cat_name = '')
	{
		$sql = "SELECT category_order FROM " . TABLE_CATEGORIES . " WHERE category_id = %d";
		$category_order = $h->db->get_var($h->db->prepare($sql, $parent));

		$position = $category_order + 1; // our new category will go right after the parent category

		// return false if duplicate name
		$sql = "SELECT category_name FROM " . TABLE_CATEGORIES . " WHERE category_name = %s";
		$exists = $h->db->get_var($h->db->prepare($sql, $new_cat_name));
		if ($exists) { return false; }

		// increment category_order for all categories after the parent:
		$sql = "SELECT category_id, category_name, category_order FROM " . TABLE_CATEGORIES . " WHERE category_order > %d ORDER BY category_order ASC";
		$categories = $h->db->get_results($h->db->prepare($sql, $category_order));
		if ($categories) {
			foreach ( $categories as $category ) {
				$sql = "UPDATE " . TABLE_CATEGORIES . " SET category_order = category_order+1, category_updateby = %d WHERE category_id = %d";
				$h->db->query($h->db->prepare($sql, $h->currentUser->id, $category->category_id));
			}
		}
		
		//insert new category after parent category:
		$sql = "INSERT INTO " . TABLE_CATEGORIES . " (category_parent, category_name, category_safe_name, category_order, category_updateby) VALUES (%d, %s, %s, %d, %d)";
		$h->db->query($h->db->prepare($sql, $parent, $new_cat_name, make_url_friendly($new_cat_name), $position, $h->currentUser->id));
		
		$this->rebuildTree($h, 1, 0);
		
		return true;
	}


	 /**
	 * Rebuild category tree
	 *
	 * @param int $parent_id
	 * @param int $left
	 * @return int
	 * @link http://www.sitepoint.com/article/hierarchical-data-database/3/
	 */
	public function rebuildTree($h, $parent_id = 0, $left = 0)
	{
		$right = $left+1;
		// get all children of this node
		$sql = "SELECT category_id FROM " . TABLE_CATEGORIES . " WHERE category_id != %d AND category_parent = %d ORDER BY category_order ASC";
		$categories = $h->db->get_results($h->db->prepare($sql, $parent_id, $parent_id));
		if ($categories) {
			foreach ($categories as $this_category) {
				$right = $this->rebuildTree($h, $this_category->category_id, $right);
			}
		}

		// we've got the left value, and now that we've processed
		// the children of this node we also know the right value
		$sql = "UPDATE " . TABLE_CATEGORIES . " SET lft = %d, rgt = %d, category_updateby = %d WHERE category_id = %d";
		$h->db->query($h->db->prepare($sql, $left, $right, $h->currentUser->id, $parent_id));
		
		// return the right value of this node + 1
		return $right+1;
	}

	/**
	 * Delete a category
	 *
	 * @param int $delete_category
	 * @return bool
	 */
	public function deleteCategory($h, $delete_category = 0)
	{
		// First, we need to get the parent of this category
		$sql = "SELECT category_parent FROM  " . TABLE_CATEGORIES . " WHERE category_id = %d";
		$grandparent = $h->db->get_var($h->db->prepare($sql, $delete_category));
		
		// Second, we need to find children of this category and assign them to their "grandparent" instead
		$sql = "SELECT category_id, category_parent FROM  " . TABLE_CATEGORIES . " WHERE category_parent = %d";
		$children = $h->db->get_results($h->db->prepare($sql, $delete_category));
		if ($children) {
			foreach ($children as $child) {
				$sql = "UPDATE " . TABLE_CATEGORIES . " SET category_parent = %d, category_updateby = %d WHERE category_id = %d";
				$h->db->query($h->db->prepare($sql, $grandparent, $h->currentUser->id, $child->category_id));
			}
		}
		
		// Third, delete the category
		$sql = "DELETE FROM " . TABLE_CATEGORIES . " WHERE category_id = %d";
		$h->db->query($h->db->prepare($sql, $delete_category));
		return true;
	} 

}
?>
