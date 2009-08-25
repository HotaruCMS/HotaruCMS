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


/**
 * Page 3 - Request tags file
 */
function pliggimp_page_3()
{
    global $plugin;
    
    echo "<h2>Step 3/5 - Tags</h2>";
    echo "Please upload your <b>tags</b> XML file:<br />";
    echo "<form name='pligg_importer_form' enctype='multipart/form-data' action='" . BASEURL . "admin/admin_index.php?page=plugin_settings&amp;plugin=pligg_importer' method='post'>\n";
    echo "<label for='file'>Exported Pligg Tags table (<span stye='color: red;'>.xml</span>):</label>\n";
    echo "<input type='file' name='file' id='file' />\n";
    echo "<input type='hidden' name='submitted' value='true' />\n";
    echo "<input type='hidden' name='table' value='Tags' />\n";
    echo "<input type='submit' name='submit' value='Upload' />\n";
    echo "</form>\n";
}


/**
 * Step 3 - Import Tags
 *
 * @param array $xml
 * @param string $file_name
 * @return bool
 */
function step3($xml, $file_name)
{
    global $db, $current_user, $cage, $links;
    
    echo "<b>Table:</b> Tags...<br /><br />";
    
    $this_table = "tags";
    if (!$db->table_empty($this_table)) {
        if (!$cage->get->getAlpha('overwrite') == 'true') {
            echo "<h2><span style='color: red';>WARNING!</h2></span>The target table, <i>" . TABLE_TAGS . "</i>, is not empty. Clicking \"Continue\" will overwrite the existing data.<br />";
            echo "<a class='next' href='" . url(array('page'=>'plugin_settings', 'plugin'=>'pligg_importer', 'file_name'=>$file_name, 'step'=>3, 'overwrite'=>'true'), 'admin') . "'>Continue</a>";
            return false;
        } 
    }
    
    $db->query($db->prepare("TRUNCATE " . DB_PREFIX . $this_table));
    
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
            
            // Insert into tags table
            $db->query($db->prepare(
                $sql,
                get_new_link_id($child->tag_link_id),
                $child->tag_date,
                urlencode(str_replace(' ', '_', trim($child->tag_words))),
                $current_user->id));
        }
    }

    //Output the number of records added
    echo $count . " minus duplicate entries.<br /><br />";
    echo "<span style='color: green;'><b>Tags table imported successfully!</b></span><br /><br />";
    
    echo "<a class='next' href='" . url(array('page'=>'plugin_settings', 'plugin'=>'pligg_importer', 'step'=>4), 'admin') . "'>Continue</a>";
    
    return true;
}

?>