<?php
/**
 * Import a Pligg tags table into a Hotaru CMS one
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


class PliggImp4
{
    /**
     * Page 4 - Request tags file
     */
    public function page_4($h)
    {
        echo "<h2>Step 4/6 - Tags</h2>";
        echo "Please upload your <b>tags</b> XML file:<br />";
        echo "<form name='pligg_importer_form' enctype='multipart/form-data' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=pligg_importer' method='post'>\n";
        echo "<label for='file'>Exported Pligg Tags table (<span stye='color: red;'>.xml</span>):</label>\n";
        echo "<input type='file' name='file' id='file' />\n";
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='hidden' name='table' value='Tags' />\n";
        echo "<input type='submit' name='submit' value='Upload' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form>\n";
    }
    
    
    /**
     * Step 4 - Import Tags
     *
     * @param array $xml
     * @param string $file_name
     * @return bool
     */
    function step4($h, $xml, $file_name)
    {
        echo "<b>Table:</b> Tags...<br /><br />";
        
        $this_table = "tags";
        
        $h->db->query($h->db->prepare("TRUNCATE " . DB_PREFIX . $this_table));
        
        echo "<i>Number of records added:</i> ";
        
        $count = 0;
        
        foreach ($xml->children() as $child)
        {
    
            // Skip any record that has no words
            if ($child->tag_words != "") 
            {
                $count++;
                
                $columns    = "tags_post_id, tags_date, tags_word, tags_updateby";
                
                $sql        = "INSERT IGNORE " . DB_PREFIX . $this_table . " (" . $columns . ") VALUES(%d, %s, %s, %d)";
                
                $lks = new PliggImp2();
                
                // Insert into tags table
                $h->db->query($h->db->prepare(
                    $sql,
                    $lks->get_new_link_id($h, $child->tag_link_id),
                    $child->tag_date,
                    urlencode(str_replace(' ', '_', trim($child->tag_words))),
                    $h->currentUser->id));
            }
        }
    
        //Output the number of records added
        echo $count . " minus duplicate entries.<br /><br />";
        echo "<span style='color: green;'><b>Tags table imported successfully!</b></span><br /><br />";
        
        echo "<a class='pliggimp_next' href='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=pligg_importer&amp;step=5'>Continue</a>";
        
        return true;
    }
}
?>
