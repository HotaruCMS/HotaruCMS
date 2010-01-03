<?php
/**
 * Import a Pligg categories table into a Hotaru CMS one
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


class PliggImp1
{
    /**
     * Page 1 - Request categories file
     */
    public function page_1($h)
    {
        echo "<h2>Step 1/6 - Categories</h2>";
        echo "Please upload your <b>categories</b> XML file:<br />";
        echo "<form name='pligg_importer_form' enctype='multipart/form-data' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=pligg_importer' method='post'>\n";
        echo "<label for='file'>Exported Pligg Categories table (<span stye='color: red;'>.xml</span>):</label>\n";
        echo "<input type='file' name='file' id='file' />\n";
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='hidden' name='table' value='Categories' />\n";
        echo "<input type='submit' name='submit' value='Upload' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form>\n";
    }
    
    
    /**
     * Step 1 - Import Categories
     *
     * @param array $xml
     * @param string $file_name
     * @return bool
     */
    function step1($h, $xml, $file_name)
    {
        echo "<b>Table:</b> Categories...<br /><br />";
        
        $this_table = "categories";
        
        $h->db->query($h->db->prepare("TRUNCATE " . DB_PREFIX . $this_table));
    
        echo "<i>Adding...</i> ";
        
        $count = 0;
        
        foreach ($xml->children() as $child)
        {
            $count++;
                
            echo $child->category_name . " | ";
            
            // Store old id. We need this to work out category parents later...
            
            $cat[$count]['old_id'] = $child->category_id;
            $cat[$count]['old_id']['parent'] = $child->category_parent;
    
            $columns    = "category_name, category_safe_name, category_order, category_desc, category_keywords, category_updateby";
            
            $sql        = "INSERT INTO " . DB_PREFIX . $this_table . " (" . $columns . ") VALUES(%s, %s, %d, %s, %s, %d)";
            
            // Insert what we can so far...
            $h->db->query($h->db->prepare(
                $sql,
                urlencode($child->category_name),
                urlencode(strtolower($child->category_safe_name)),
                $child->category_order,
                urlencode($child->category_desc),
                urlencode($child->category_keywords),
                $h->currentUser->id));
                
            // Grab the ID of the last insert. We'll use this to match parents...
            $cat[$count]['old_id']['new_id'] = $h->db->get_var($h->db->prepare("SELECT LAST_INSERT_ID()"));
            
            $sql = "REPLACE INTO " . DB_PREFIX . "pliggimp_temp (pliggimp_setting, pliggimp_old_value, pliggimp_new_value) VALUES(%s, %d, %d)";
            
            $h->db->query($h->db->prepare($sql, 'category_id', $cat[$count]['old_id'], $cat[$count]['old_id']['new_id']));
        }
    
    
        foreach ($cat as $c) {
        
            /*
            echo "Old id: " . $c['old_id'] . "<br />";
            echo "Old id's parent: " . $c['old_id']['parent'] . "<br />";
            */
            
            // Find the new id of the old id's parent
            foreach  ($cat as $c2) 
            {
                // look for a match
                $a = (int)$c2['old_id'];            // old id's parent
                $b = (int)$c['old_id']['parent'];   // also old id's parent
                
                if ($a == $b) {
                        //echo "New id of old id's parent: " . $c2['old_id']['new_id'] . "<br />";
                        
                        $sql = "UPDATE " . DB_PREFIX . $this_table . " SET category_parent = %d WHERE category_id = %d";
    
                        // Set this parent to the new_id of the old_id's parent...
                        $h->db->query($h->db->prepare(
                            $sql, 
                            (int)$c2['old_id']['new_id'], 
                            (int)$c['old_id']['new_id']));
                        
                        break;
                }
            }
    
        }
        
        $this->rebuild_cat_tree($h, 1, 0, $this_table);
        
        echo "<br /><br />";
        echo "<span style='color: green;'><b>Categories table imported successfully!</b></span><br /><br />";
        
        echo "<a class='pliggimp_next' href='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=pligg_importer&amp;step=2'>Continue</a>";
        
        return true;
    }
    
    
    /**
     * Get new category id
     *
     * @param int $old_cat_id
     * @return int|false
     */
    function get_new_cat_id($h, $old_cat_id)
    {
        $sql = "SELECT pliggimp_new_value FROM " . DB_PREFIX . "pliggimp_temp WHERE pliggimp_setting = %s AND pliggimp_old_value = %d";
        
        $new_cat_id = $h->db->get_var($h->db->prepare($sql, 'category_id', $old_cat_id));
        
        if ($new_cat_id) { return $new_cat_id; } else { return false; }
    }
    
    
    /**
     * Rebuild the category tree (for right and left nodes)
     *
     * @param int $parent_id
     * @param int $left starting node
     * @return int
     */
    function rebuild_cat_tree($h, $parent_id, $left, $this_table)
    {
        $right = $left+1;
        
        // get all children of this node
        $sql = "SELECT category_id FROM " . DB_PREFIX . $this_table . " WHERE category_id != %d AND category_parent = %d ORDER BY category_order ASC";
        
        $categories = $h->db->get_results($h->db->prepare($sql, $parent_id, $parent_id));
        
        if ($categories) {
            foreach ($categories as $this_category) {
                 $right = $this->rebuild_cat_tree($h, $this_category->category_id, $right, $this_table);
            }
        }
        
        // we've got the left value, and now that we've processed
        // the children of this node we also know the right value
        $sql = "UPDATE " . DB_PREFIX . $this_table . " SET lft = %d, rgt = %d, category_updateby = %d WHERE category_id = %d";
        
        $h->db->query($h->db->prepare($sql, $left, $right, $h->currentUser->id, $parent_id));
        
        // return the right value of this node + 1
        return $right+1;
    }
}
?>
