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
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2009, Hotaru CMS
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
        $cat_id = $h->db->get_var($h->db->prepare($sql, urlencode($cat_safe_name)));
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
        if ($cat_id == 0 && $cat_safe_name != '') {
            // Use safe name
            $sql = "SELECT category_name FROM " . TABLE_CATEGORIES . " WHERE category_safe_name = %s";
            $cat_name = $h->db->get_var($h->db->prepare($sql, $cat_safe_name));
        } else {
            // Use id
            $sql = "SELECT category_name FROM " . TABLE_CATEGORIES . " WHERE category_id = %d";
            $cat_name = $h->db->get_var($h->db->prepare($sql, $cat_id));
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
}

?>