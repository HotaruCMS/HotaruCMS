<?php
/**
 * Import a Pligg votes table into a Hotaru CMS one
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


class PliggImp6
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
     * Page 6 - Request votes file
     */
    public function page_6()
    {
        echo "<h2>Step 6/6 - Votes</h2>";
        echo "Please upload your <b>votes</b> XML file:<br />";
        echo "<form name='pligg_importer_form' enctype='multipart/form-data' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=pligg_importer' method='post'>\n";
        echo "<label for='file'>Exported Pligg Votes table (<span stye='color: red;'>.xml</span>):</label>\n";
        echo "<input type='file' name='file' id='file' />\n";
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='hidden' name='table' value='Votes' />\n";
        echo "<input type='submit' name='submit' value='Upload' />\n";
        echo "</form>\n";
    }
    
    
    /**
     * Step 6 - Import Votes
     * @param array $xml
     * @param string $file_name
     * @return bool
     */
    function step6($xml, $file_name)
    {
        echo "<b>Table:</b> Votes...<br /><br />";
        
        $this_table = "postvotes";
        if (!$this->db->table_empty($this_table)) {
            if (!$this->cage->get->getAlpha('overwrite') == 'true') {
                echo "<h2><span style='color: red';>WARNING!</h2></span>The target table, <i>" . DB_PREFIX . $this_table . "</i>, is not empty. Clicking \"Continue\" will overwrite the existing data, and also any data in the <i>commentvotes</i> table.<br />";
                echo "<a class='next' href='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=pligg_importer&amp;file_name=" . $file_name . "&amp;step=6&amp;overwrite=true'>Continue</a>";
                return false;
            } 
        }
        
        $this->db->query($this->db->prepare("TRUNCATE " . DB_PREFIX . 'postvotes'));
        $this->db->query($this->db->prepare("TRUNCATE " . DB_PREFIX . 'commentvotes'));
        
        echo "<i>Number of records added:</i> ";
        
        $count = 0;
        
        foreach ($xml->children() as $child)
        {
    
            // Skip all comment votes...
            if ($child->vote_type == 'links') 
            {
                $count++;
    
                if ($child->vote_value > 0) { 
                    $rating = 'positive'; 
                } else {
                    $rating = 'negative';
                }
                
                $columns    = "vote_post_id, vote_user_id, vote_user_ip, vote_date, vote_type, vote_rating, vote_reason, vote_updateby";
                
                $sql        = "INSERT IGNORE " . DB_PREFIX . $this_table . " (" . $columns . ") VALUES(%d, %d, %s, %s, %s, %s, %d, %d)";
                
                $lks = new PliggImp2($this->hotaru);
                $usr = new PliggImp5($this->hotaru);
                
                // Insert into postvotes table
                $this->db->query($this->db->prepare(
                    $sql,
                    $lks->get_new_link_id($child->vote_link_id),
                    $usr->get_new_user_id($child->vote_user_id),
                    $child->vote_ip,
                    $child->vote_date,
                    'vote_simple',
                    $rating,
                    0,
                    $this->current_user->id));
            }
            
            // Now do comment votes...
            if ($child->vote_type == 'comments') 
            {
                $count++;
    
                if ($child->vote_value > 0) { 
                    $rating = 'positive'; 
                } else {
                    $rating = 'negative';
                }
                
                $columns    = "cvote_post_id, cvote_comment_id, cvote_user_id, cvote_user_ip, cvote_date, cvote_rating, cvote_reason, cvote_updateby";
                
                $sql        = "INSERT IGNORE " . DB_PREFIX . "commentvotes (" . $columns . ") VALUES(%d, %d, %d, %s, %s, %s, %d, %d)";
                
                // Insert into commentvotes table
                $this->db->query($this->db->prepare(
                    $sql,
                    $lks->get_new_link_id($child->vote_link_id),
                    0,
                    $usr->get_new_user_id($child->vote_user_id),
                    $child->vote_ip,
                    $child->vote_date,
                    $rating,
                    0,
                    $this->current_user->id));
            }
        }
    
        //Output the number of records added
        echo $count . " minus duplicate entries.<br /><br />";
        echo "<span style='color: green;'><b>Votes table imported successfully!</b></span><br /><br />";
        
        echo "<a class='next' href='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=pligg_importer&amp;step=7'>Continue</a>";
        
        return true;
    }
}

?>