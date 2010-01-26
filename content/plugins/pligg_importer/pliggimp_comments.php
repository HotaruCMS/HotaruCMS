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
    /**
     * Page 3 - Request Comments file
     */
    public function page_3($h)
    {
        echo "<h2>Step 3/6 - Comments</h2>";
        echo "Please upload your <b>comments</b> XML file:<br />";
        echo "<form name='pligg_importer_form' enctype='multipart/form-data' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=pligg_importer' method='post'>\n";
        echo "<label for='file'>Exported Pligg Comments table (<span stye='color: red;'>.xml</span>):</label>\n";
        echo "<input type='file' name='file' id='file' />\n";
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='hidden' name='table' value='Comments' />\n";
        echo "<input type='submit' name='submit' value='Upload' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form>\n";
    }
    
    
    /**
     * Step 3 - Import Comments
     *
     * @param array $xml
     * @param string $file_name
     * @return bool
     */
    function step3($h, $xml, $file_name)
    {
        echo "<b>Table:</b> Comments...<br /><br />";
        
        $this_table = "comments";
        
        $h->db->query($h->db->prepare("TRUNCATE " . DB_PREFIX . $this_table));
        
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
                
                if ($child->comment_votes < 0) { 
                    $cvotes_up = 0; $cvotes_down = abs($child->comment_votes);
                } else {
                    $cvotes_up = $child->comment_votes; $cvotes_down = 0;
                }

        
                $columns    = "comment_post_id, comment_user_id, comment_parent, comment_date, comment_content, comment_votes_up, comment_votes_down, comment_subscribe, comment_updateby";
                
                $sql        = "INSERT INTO " . DB_PREFIX . $this_table . " (" . $columns . ") VALUES(%d, %d, %d, %s, %s, %d, %d, %d, %d)";
                $lks = new PliggImp2();
                
                // Insert into Comments table
                $h->db->query($h->db->prepare(
                    $sql,
                    $lks->get_new_link_id($h, $child->comment_link_id),
                    $child->comment_user_id,
                    $child->comment_parent,
                    $child->comment_date,
                    urlencode(trim($child->comment_content)),
                    $cvotes_up,
                    $cvotes_down,
                    $child->comment_subscribe,
                    $h->currentUser->id));
                    
                // Grab the ID of the last insert. 
                $comment[$count]['old_id']['new_id'] = $h->db->get_var($h->db->prepare("SELECT LAST_INSERT_ID()"));
                
                $sql = "REPLACE INTO " . DB_PREFIX . "pliggimp_temp (pliggimp_setting, pliggimp_old_value, pliggimp_new_value) VALUES(%s, %d, %d)";
                $h->db->query($h->db->prepare($sql, 'comment_id', $comment[$count]['old_id'], $comment[$count]['old_id']['new_id']));
            }
        }
    
        $this->update_comment_parent_ids($h, $this_table);
        
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
    function update_comment_parent_ids($h, $this_table)
    {
        $sql = "SELECT comment_id, comment_parent FROM " . DB_PREFIX . $this_table;
        $comments = $h->db->get_results($h->db->prepare($sql));
        
        if ($comments) {
            foreach ($comments as $comment) {
                $sql = "SELECT pliggimp_new_value FROM " . DB_PREFIX . "pliggimp_temp WHERE pliggimp_setting = %s AND pliggimp_old_value = %d";
                $new_parent_id = $h->db->get_var($h->db->prepare($sql, 'comment_id', $comment->comment_parent));
                
                if($new_parent_id) {
                    $sql = "UPDATE " . DB_PREFIX . $this_table . " SET comment_parent = %d WHERE comment_id = %d";
                    $h->db->query($h->db->prepare($sql, $new_parent_id, $comment->comment_id));
                }
            }
        }
    
    }
    
    
    /**
     * Get new link id
     *
     * @param int $old_link_id
     * @return int|false
     */
    function get_new_comment_id($h, $old_comment_id)
    {
        $sql = "SELECT pliggimp_new_value FROM " . DB_PREFIX . "pliggimp_temp WHERE pliggimp_setting = %s AND pliggimp_old_value = %d";
        
        $new_comment_id = $h->db->get_var($h->db->prepare($sql, 'comment_id', $old_comment_id));
        
        if ($new_comment_id) { return $new_comment_id; } else { return false; }
    }
}

?>
