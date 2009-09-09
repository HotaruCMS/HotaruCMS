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
    public function getCatId($cat_name)
    {
        global $db;
        
        $sql = "SELECT category_id FROM " . TABLE_CATEGORIES . " WHERE category_safe_name = %s";
        $cat_id = $db->get_var($db->prepare($sql, urlencode($cat_name)));
        return $cat_id;
    }
    

    /**
     * Returns the category name for a give category id.
     *
     * @param int $cat_id
     * @return string
     */
    public function getCatName($cat_id) {
        global $db;
        
        $sql = "SELECT category_name FROM " . TABLE_CATEGORIES . " WHERE category_id = %d";
        $cat_name = $db->get_var($db->prepare($sql, $cat_id));
        return urldecode($cat_name);
    }
    

    /**
     * Returns the category safe name for a give category id.
     *
     * @param int $cat_id
     * @return string
     */
    public function getCatSafeName($cat_id)
    {
        global $db;
        
        $sql = "SELECT category_safe_name FROM " . TABLE_CATEGORIES . " WHERE category_id = %d";
        $cat_safe_name = $db->get_var($db->prepare($sql, $cat_id));
        return urldecode($cat_safe_name);
    }
    
    
    /**
     * Recursive public function to find level depth
     *
     * @param int $cat_id
     * @return int
     */
    public function getCatLevel($cat_id)
    {
        global $cat_level, $the_cats;
            
        foreach ($the_cats as $cat) {
            if (($cat->category_id == $cat_id) && $cat->category_parent > 1) {
                $cat_level++;
                $this->getCatLevel($cat->category_parent);
            }
        }
    
        return $cat_level;
    }
}

?>