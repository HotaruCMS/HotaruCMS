<?php
/**
 * Import a Pligg users table into a Hotaru CMS one
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


class PliggImp5
{
    /**
     * Page 5 - Request users file
     */
    public function page_5($h)
    {
        echo "<h2>Step 5/6 - Users</h2>";
        echo "Please upload your <b>users</b> XML file:<br />";
        echo "<form name='pligg_importer_form' enctype='multipart/form-data' action='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=pligg_importer' method='post'>\n";
        echo "<label for='file'>Exported Pligg Users table (<span stye='color: red;'>.xml</span>):</label>\n";
        echo "<input type='file' name='file' id='file' />\n";
        echo "<input type='hidden' name='submitted' value='true' />\n";
        echo "<input type='hidden' name='table' value='Users' />\n";
        echo "<input type='submit' name='submit' value='Upload' />\n";
        echo "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />\n";
        echo "</form>\n";
    }
    
    
    /**
     * Step 5 - Import Users
     *
     * @param array $xml
     * @param string $file_name
     * @return bool
     */
    function step5($h, $xml, $file_name)
    {
        echo "<b>Table:</b> Users...<br /><br />";
        
        $this_table = "users";
        
        $h->db->query($h->db->prepare("TRUNCATE " . DB_PREFIX . $this_table));
        
        echo "<i>Number of records added:</i> ";
        
        $count = 0;
        
        foreach ($xml->children() as $child)
        {
    
            // Skip any record that has no username
            if ($child->user_login != "") 
            {
                $count++;
                
                $users[$count]['old_id'] = $child->user_id;
                
                // Rename user levels
                switch ($child->user_level) {
                    case 'god':
                        $role = 'admin';
                        $child->user_level = 'admin';
                        break;
                    case 'admin':
                        $role = 'admin';
                        $child->user_level = 'admin';
                        break;
                    case 'normal':
                        $role = 'member';
                        $child->user_level = 'member';
                        break;
                    default:
                        break;
                }
                
                $columns    = "user_username, user_role, user_date, user_password, user_email, user_email_valid, user_email_conf, user_permissions, user_ip, user_lastlogin, user_updateby";
                
                $sql        = "INSERT INTO " . DB_PREFIX . $this_table . " (" . $columns . ") VALUES(%s, %s, %s, %s, %s, %d, %s, %s, %s, %s, %d)";
                
                //if not using SWCMS' email registration module, set to zero:
                if (!$child->valid_email) { $child->valid_email = 0;}
                if (!$child->email_conf) { $child->email_conf = 0; }
                
                        /*
                        echo "ROLE: " . $role . "<br />";
                        if (is_string($role)) {  echo "role is a string<br />"; }
                        if (is_array($role)) {  echo "role is an array<br />"; }
                        if (is_object($role)) {  echo "role is a object<br />"; }
                        
                        echo "<br />";
                        
                        echo "ROLE: " . $role . "<br />";
                        if (is_string($child->user_level)) {  echo "child->user_level is a string<br />"; }
                        if (is_array($child->user_level)) {  echo "child->user_level is an array<br />"; }
                        if (is_object($child->user_level)) {  echo "child->user_level is a object<br />"; }
                        
                        echo "<br />Using role<br /><br />";
                        */
        
                // get permissions
                $perms = $h->getDefaultPermissions($role);
                unset($perms['options']); // don't need options.
                
                // Insert into users table
                $h->db->query($h->db->prepare(
                    $sql,
                    $child->user_login,
                    $child->user_level,
                    $child->user_date,
                    $child->user_pass,
                    $child->user_email,
                    $child->valid_email,
                    $child->email_conf,
                    serialize($perms),
                    $child->user_ip,
                    $child->user_lastlogin,
                    $h->currentUser->id));
                    
                // Grab the ID of the last insert. 
                $users[$count]['old_id']['new_id'] = $h->db->get_var($h->db->prepare("SELECT LAST_INSERT_ID()"));
                
                $sql = "REPLACE INTO " . DB_PREFIX . "pliggimp_temp (pliggimp_setting, pliggimp_old_value, pliggimp_new_value) VALUES(%s, %d, %d)";
                
                $h->db->query($h->db->prepare($sql, 'user_id', $users[$count]['old_id'], $users[$count]['old_id']['new_id']));
            }
        }
        
        // Update post and comment authors with new user ids
        $sql = "SELECT * FROM " . DB_PREFIX . "pliggimp_temp WHERE pliggimp_setting = %s";
        $user_ids = $h->db->get_results($h->db->prepare($sql, 'user_id'));
        
        foreach ($user_ids as $author)
        {
            $sql = "UPDATE " . TABLE_POSTS . " SET post_author = %d WHERE post_author = %d";
            $h->db->query($h->db->prepare(
                $sql, 
                $author->pliggimp_new_value, 
                $author->pliggimp_old_value));
                
            $sql = "UPDATE " . TABLE_COMMENTS . " SET comment_user_id = %d WHERE comment_user_id = %d";
            $h->db->query($h->db->prepare(
                $sql, 
                $author->pliggimp_new_value, 
                $author->pliggimp_old_value));
        }
        // End of updating post and comment author fields
        
       
        //Output the number of records added
        echo $count . "<br /><br />";
        echo "<span style='color: green;'><b>Users table imported successfully!</b></span><br /><br />";
        
        echo "<a class='pliggimp_next' href='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=pligg_importer&amp;step=6'>Continue</a>";
        
        return true;
    }
    
    
    /**
     * Get new user id
     *
     * @param int $old_user_id
     * @return int|false
     */
    function get_new_user_id($h, $old_user_id)
    {
        $sql = "SELECT pliggimp_new_value FROM " . DB_PREFIX . "pliggimp_temp WHERE pliggimp_setting = %s AND pliggimp_old_value = %d";
        
        $new_user_id = $h->db->get_var($h->db->prepare($sql, 'user_id', $old_user_id));
        
        if ($new_user_id) { return $new_user_id; } else { return false; }
    }
}
?>
