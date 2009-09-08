<?php
/**
 * Import a Pligg links table into a Hotaru CMS one
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
 * Page 2 - Request links file
 */
function pliggimp_page_2() {
    global $plugin;
    
    echo "<h2>Step 2/5 - Links</h2>";
    echo "Please upload your <b>links</b> XML file:<br />";
    echo "<form name='pligg_importer_form' enctype='multipart/form-data' action='" . baseurl . "admin/admin_index.php?page=plugin_settings&amp;plugin=pligg_importer' method='post'>\n";
    echo "<label for='file'>Exported Pligg Links table (<span stye='color: red;'>.xml</span>):</label>\n";
    echo "<input type='file' name='file' id='file' />\n";
    echo "<input type='hidden' name='submitted' value='true' />\n";
    echo "<input type='hidden' name='table' value='Links' />\n";
    echo "<input type='submit' name='submit' value='Upload' />\n";
    echo "</form>\n";
}


/**
 * Step 2 - Import Links
 * @param array $xml
 * @param string $file_name
 * @return bool
 */
function step2($xml, $file_name)
{
    global $db, $current_user, $cage, $links;
    
    echo "<b>Table:</b> Links...<br /><br />";
    
    $this_table = "posts";
    if(!$db->table_empty($this_table)) {
        if(!$cage->get->getAlpha('overwrite') == 'true') {
            echo "<h2><span style='color: red';>WARNING!</h2></span>The target table, <i>" . table_posts . "</i>, is not empty. Clicking \"Continue\" will overwrite the existing data.<br />";
            echo "<a class='next' href='" . url(array('page'=>'plugin_settings', 'plugin'=>'pligg_importer', 'file_name'=>$file_name, 'step'=>2, 'overwrite'=>'true'), 'admin') . "'>Continue</a>";
            return false;
        } else {
            $db->query($db->prepare("TRUNCATE " . db_prefix . $this_table));
        }
    }
    
    echo "<i>Number of records added:</i> ";
    
    $count = 0;
    
    foreach ($xml->children() as $child)
    {

        // Skip any record that has no title
        if($child->link_title != "") 
        {
            $count++;
            
            // Store old id. We need this to work out authors and categories later...
            
            $link[$count]['old_id'] = $child->link_id;
            $link[$count]['old_id']['category'] = $child->link_category;
            $link[$count]['old_id']['author'] = $child->link_author;
    
            switch ($child->link_status) {
                case 'discard':
                    $child->link_status = 'buried';
                    break;
                case 'queued':
                    $child->link_status = 'new';
                    break;
                case 'published':
                    $child->link_status = 'top';
                    break;
                case 'processing':  // JapanSoc custom status
                    $child->link_status = 'buried';
                    break;
                case 'pending':  // SWCMS Submission Approval module
                    $child->link_status = 'pending';
                    break;
                case 'page':  // SWCMS Pages module
                    $child->link_status = 'page';
                    break;
                default:
                    break;
            }
            
            // Get the original domain
            $parsed = parse_url($child->link_url); 
            $domain = $parsed['scheme'] . "://" . $parsed['host'];
            
            $columns    = "post_author, post_category, post_status, post_date, post_title, post_orig_url, post_domain, post_url, post_content, post_votes_up, post_votes_down, post_tags, post_updateby";
            
            $sql        = "INSERT INTO " . db_prefix . $this_table . " (" . $columns . ") VALUES(%d, %d, %s, %s, %s, %s, %s, %s, %s, %d, %d, %s, %d)";
            
            // Insert into links table
            $db->query($db->prepare(
                $sql,
                urlencode($child->link_author),
                get_new_cat_id($child->link_category),
                $child->link_status,
                $child->link_date,
                urlencode(trim($child->link_title)),
                urlencode($child->link_url),
                urlencode($domain),
                urlencode(strtolower(str_replace('_' , '-' , $child->link_title_url))),
                urlencode(trim($child->link_content)),
                $child->link_votes,
                $child->link_reports,
                urlencode($child->link_tags),
                $current_user->id));
                
            // Grab the ID of the last insert. 
            $link[$count]['old_id']['new_id'] = $db->get_var($db->prepare("SELECT LAST_INSERT_ID()"));
            
            $sql = "REPLACE INTO " . db_prefix . "pliggimp_temp (pliggimp_setting, pliggimp_old_value, pliggimp_new_value) VALUES(%s, %d, %d)";
            
            $db->query($db->prepare($sql, 'link_id', $link[$count]['old_id'], $link[$count]['old_id']['new_id']));
        }
    }

    //Output the number of records added
    echo $count . "<br /><br />";
    echo "<span style='color: green;'><b>Links table imported successfully!</b></span><br /><br />";
    
    echo "<a class='next' href='" . url(array('page'=>'plugin_settings', 'plugin'=>'pligg_importer', 'step'=>3), 'admin') . "'>Continue</a>";
    
    return true;
}


/**
 * Get new link id
 *
 * @param int $old_link_id
 * @return int|false
 */
function get_new_link_id($old_link_id)
{
    global $db;
    
    $sql = "SELECT pliggimp_new_value FROM " . db_prefix . "pliggimp_temp WHERE pliggimp_setting = %s AND pliggimp_old_value = %d";
    
    $new_link_id = $db->get_var($db->prepare($sql, 'link_id', $old_link_id));
    
    if($new_link_id) { return $new_link_id; } else { return false; }
}

?>