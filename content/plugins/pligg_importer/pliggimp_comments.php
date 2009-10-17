<?php
/**
 * Import a Pligg Comments table into a Hotaru CMS one
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


class PliggImp3
{
    public $db;                         // database object
    public $cage;                       // Inspekt object
    public $hotaru;                     // Hotaru object
    public $current_user;               // UserBase object
    
    
    /**
     * Build a $plugins object containing $db and $cage
     */
    public function __construct($hotaru)
    {
        $this->hotaru           = $hotaru;
        $this->db               = $hotaru->db;
        $this->cage             = $hotaru->cage;
        $this->current_user     = $hotaru->current_user;
    }
    
    
    /**
     * Page 3 - Request Comments file
     */
    public function page_3()
    {
        echo "<h2>Step 3/6 - Comments</h2>";
        echo "Please upload your <b>comments</b> XML file:<br />";
        echo "<form name='pligg_importer_form' enctype='multipart/form-data' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=pligg_importer' method='post'>\n";
        echo "<label for='file'>Exported Pligg Comments table (<span stye='color: red;'>.xml</span>):</label>\n";
        echo "<input type='file' name='file' id='file' />\n";
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='hidden' name='table' value='Comments' />\n";
        echo "<input type='submit' name='submit' value='Upload' />\n";
        echo "</form>\n";
    }
    
    
    /**
     * Step 3 - Import Comments
     *
     * @param array $xml
     * @param string $file_name
     * @return bool
     */
    function step3($xml, $file_name)
    {
        echo "<b>Table:</b> Comments...<br /><br />";
        
        $this_table = "comments";
        if (!$this->db->table_empty($this_table)) {
            if (!$this->cage->get->getAlpha('overwrite') == 'true') {
                echo "<h2><span style='color: red';>WARNING!</h2></span>The target table, <i>" . DB_PREFIX . $this_table . "</i>, is not empty. Clicking \"Continue\" will overwrite the existing data.<br />";
                echo "<a class='pliggimp_next' href='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=pligg_importer&amp;file_name=" . $file_name . "&amp;step=3&amp;overwrite=true'>Continue</a>";
                return false;
            } 
        }
        
        $this->db->query($this->db->prepare("TRUNCATE " . DB_PREFIX . $this_table));
        
        echo "<i>Number of records added:</i> ";
        
        $count = 0;
        
        foreach ($xml->children() as $child)
        {
    
            // Skip any record that has no title
            if ($child->comment_content != "") 
            {
                $count++;
                
                // Store old id. We need this to work out authors and categories later...
                
                $comment[$count]['old_id'] = $child->comment_id;
                $comment[$count]['old_id']['parent'] = $child->comment_parent;
                $comment[$count]['old_id']['post_id'] = $child->comment_link_id;
                $comment[$count]['old_id']['author'] = $child->comment_author_id;
                
                if(!isset($child->comment_subscribe)) { $child->comment_subscribe = 0; }
        
                $columns    = "comment_post_id, comment_user_id, comment_parent, comment_date, comment_content, comment_votes, comment_subscribe, comment_updateby";
                
                $sql        = "INSERT INTO " . DB_PREFIX . $this_table . " (" . $columns . ") VALUES(%d, %d, %d, %s, %s, %d, %d, %d)";
                $lks = new PliggImp2($this->hotaru);
                
                // Insert into Comments table
                $this->db->query($this->db->prepare(
                    $sql,
                    $lks->get_new_link_id($child->comment_link_id),
                    $child->comment_user_id,
                    $child->comment_parent,
                    $child->comment_date,
                    urlencode(trim($child->comment_content)),
                    $child->comment_votes,
                    $child->comment_subscribe,
                    $this->current_user->id));
                    
                // Grab the ID of the last insert. 
                $comment[$count]['old_id']['new_id'] = $this->db->get_var($this->db->prepare("SELECT LAST_INSERT_ID()"));
                
                $sql = "REPLACE INTO " . DB_PREFIX . "pliggimp_temp (pliggimp_setting, pliggimp_old_value, pliggimp_new_value) VALUES(%s, %d, %d)";
                $this->db->query($this->db->prepare($sql, 'comment_id', $comment[$count]['old_id'], $comment[$count]['old_id']['new_id']));
            }
        }
    
        $this->update_comment_parent_ids($this_table);
        
        //Output the number of records added
        echo $count . "<br /><br />";
        echo "<span style='color: green;'><b>Comments table imported successfully!</b></span><br /><br />";
        
        echo "<a class='pliggimp_next' href='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=pligg_importer&amp;step=4'>Continue</a>";
        
        return true;
    }
    
    
    /**
     * Update comment parent ids
     *
     * @param string $this_table
     */
    function update_comment_parent_ids($this_table)
    {
        $sql = "SELECT comment_id, comment_parent FROM " . DB_PREFIX . $this_table;
        $comments = $this->db->get_results($this->db->prepare($sql));
        
        if ($comments) {
            foreach ($comments as $comment) {
                $sql = "SELECT pliggimp_new_value FROM " . DB_PREFIX . "pliggimp_temp WHERE pliggimp_setting = %s AND pliggimp_old_value = %d";
                $new_parent_id = $this->db->get_var($this->db->prepare($sql, 'comment_id', $comment->comment_parent));
                
                if($new_parent_id) {
                    $sql = "UPDATE " . DB_PREFIX . $this_table . " SET comment_parent = %d WHERE comment_id = %d";
                    $this->db->query($this->db->prepare($sql, $new_parent_id, $comment->comment_id));
                }
            }
        }
    
    }
}

?>
