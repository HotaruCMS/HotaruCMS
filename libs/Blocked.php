<?php
/**
 * Functions for the Blocked list
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
class Blocked
{
     /**
     * Prepare a list of blocked items for the Admin "Blocked List" page
     */
    public function buildBlockedList($h)
    {
        $safe = true; // CSRF flag
        
        if ($h->cage->post->keyExists('type')) {
            $safe = $h->csrf();
            if (!$safe) {
                $h->message = $h->lang['error_csrf'];
                $h->messageType = 'red';
            }
        }
        
        // if new item to block
        if ($safe && $h->cage->post->getAlpha('type') == 'new')
        {
            $type = $h->cage->post->testAlnumLines('blocked_type');
            $value = $h->cage->post->sanitizeTags('value');
            
            if (!$value) {
                $h->message = $h->lang['admin_blocked_list_empty'];
                $h->messageType = 'red';
            } else {
                $this->addToBlockedList($h, $type, $value);
            }
        }
        
        // if edit item
        if ($safe && $h->cage->post->getAlpha('type') == 'edit')
        {
            $id = $h->cage->post->testInt('id');
            $type = $h->cage->post->testAlnumLines('blocked_type');
            $value = $h->cage->post->sanitizeTags('value');
            $this->updateBlockedList($h, $id, $type, $value);
            $h->message = $h->lang['admin_blocked_list_updated'];
            $h->messageType = 'green';
        }
        
        // if remove item
        if ($safe && ($h->cage->get->getAlpha('action') == 'remove'))
        {
            $id = $h->cage->get->testInt('id');
            $this->removeFromBlockedList($h->db, $id);
            $h->message = $h->lang["admin_blocked_list_removed"];
            $h->messageType = 'green';
        }
        
        // GET CURRENTLY BLOCKED ITEMS...
        
        $where_clause = '';
        
        // if search
        if ($safe && $h->cage->post->getAlpha('type') == 'search') {
            $search_term = $h->cage->post->sanitizeTags('search_value');
            $where_clause = " WHERE blocked_value LIKE '%" . trim($h->db->escape($search_term)) . "%'";
        }
        
        // if filter
        if ($safe && $h->cage->post->getAlpha('type') == 'filter') {
            $filter = $h->cage->post->testAlnumLines('blocked_type');
            if ($filter == 'all') { $where_clause = ''; } else { $where_clause = " WHERE blocked_type = %s"; }
        }
        
        // SQL
        $sql = "SELECT * FROM " . TABLE_BLOCKED . $where_clause;

        if (isset($search_term)) { 
            $blocked_items = $h->db->get_results($sql);
        } elseif (isset($filter)) { 
            $blocked_items = $h->db->get_results($h->db->prepare($sql, $filter));
        } else {
            $blocked_items = $h->db->get_results($h->db->prepare($sql));
        }
        
        if (!$blocked_items) { return array(); }
        
        $pg = $h->cage->get->getInt('pg');
        $items = 20;
        $output = "";
        
        require_once(EXTENSIONS . 'Paginated/Paginated.php');
        require_once(EXTENSIONS . 'Paginated/DoubleBarLayout.php');
        $pagedResults = new Paginated($blocked_items, $items, $pg);
        
        $alt = 0;
        while($block = $pagedResults->fetchPagedRow()) {    //when $story is false loop terminates    
            $alt++;
            $output .= "<tr class='table_row_" . $alt % 2 . "'>\n";
            $output .= "<td>" . $block->blocked_type . "</td>\n";
            $output .= "<td>" . $block->blocked_value . "</td>\n";
            $output .= "<td>" . "<a class='table_drop_down' href='#'>\n";
            $output .= "<img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/edit.png'>" . "</a></td>\n";
            $output .= "<td>" . "<a href='" . BASEURL . "admin_index.php?page=blocked_list&amp;action=remove&amp;id=" . $block->blocked_id . "'>\n";
            $output .= "<img src='" . BASEURL . "content/admin_themes/" . ADMIN_THEME . "images/delete.png'>" . "</a></td>\n";
            $output .= "</tr>\n";
            $output .= "<tr class='table_tr_details' style='display:none;'>\n";
            $output .= "<td colspan=3 class='table_description'>\n";
            $output .= "<form name='blocked_list_edit_form' action='" . BASEURL . "admin_index.php' method='post'>\n";
            $output .= "<table><tr><td><select name='blocked_type'>\n";
            
            switch($block->blocked_type) { 
                case 'url':
                    $text = $h->lang["admin_theme_blocked_url"];
                    break;
                case 'email':
                    $text = $h->lang["admin_theme_blocked_email"];
                    break;
                default:
                    $text = $h->lang["admin_theme_blocked_ip"];
                    break;
            }
            
            $output .= "<option value='" . $block->blocked_type . "'>" . $text . "</option>\n";
            $output .= "<option value='ip'>" . $h->lang["admin_theme_blocked_ip"] . "</option>\n";
            $output .= "<option value='url'>" . $h->lang["admin_theme_blocked_url"] . "</option>\n";
            $output .= "<option value='email'>" . $h->lang["admin_theme_blocked_email"] . "</option>\n";
            $output .= "<option value='user'>" . $h->lang["admin_theme_blocked_username"] . "</option>\n";
            $output .= "</select></td>\n";
            $output .= "<td><input type='text' size=30 name='value' value='" . $block->blocked_value . "' /></td>\n";
            $output .= "<td><input class='submit' type='submit' value='" . $h->lang['admin_blocked_list_update'] . "' /></td>\n";
            $output .= "</tr></table>\n";
            $output .= "<input type='hidden' name='id' value='" . $block->blocked_id . "' />\n";
            $output .= "<input type='hidden' name='page' value='blocked_list' />\n";
            $output .= "<input type='hidden' name='type' value='edit' />\n";
            $output .= "<input type='hidden' name='csrf' value='" . $h->csrfToken . "' />";
            $output .= "</form>\n";
            $output .= "</td>";
            $output .= "<td class='table_description_close'><a class='table_hide_details' href='#'>" . $h->lang["admin_theme_plugins_close"] . "</a></td>";
            $output .= "</tr>";
        }

        $blocked_array = array('blocked_items' => $output, 'pagedResults' => $pagedResults);
        
        return $blocked_array;
    }
    
    
     /**
     * Add or update blocked items 
     *
     * @param string $type - e.g. url, email, ip
     * @param string $value - item to block
     * @param bool $msg - show a success/failure message on Maintenance page
     * @return bool
     */
    public function addToBlockedList($h, $type = '', $value = 0, $msg = true)
    {
        $sql = "SELECT blocked_id FROM " . TABLE_BLOCKED . " WHERE blocked_type = %s AND blocked_value = %s"; 
        $id = $h->db->get_var($h->db->prepare($sql, $type, $value));
        
        if ($id) { // already exists
            if ($msg) { 
                $h->message = $h->lang['admin_blocked_list_exists']; 
                $h->messageType = 'red';
            }
            return false;
        } 
        
        $sql = "INSERT INTO " . TABLE_BLOCKED . " (blocked_type, blocked_value, blocked_updateby) VALUES (%s, %s, %d)"; 
        $h->db->query($h->db->prepare($sql, $type, $value, $h->currentUser->id));
        if ($msg) { 
            $h->message = $h->lang['admin_blocked_list_added']; 
            $h->messageType = 'green';
        }
        
        return true;
    }
    
    
     /**
     * Add to or update items 
     *
     * @return array|false
     */
    public function updateBlockedList($h, $id = 0, $type = '', $value = 0)
    {
        $sql = "UPDATE " . TABLE_BLOCKED . " SET blocked_type = %s, blocked_value = %s, blocked_updateby = %d WHERE blocked_id = %d"; 
        $h->db->query($h->db->prepare($sql, $type, $value, $h->currentUser->id, $id));
    }
    
    
     /**
     * Remove from blocked list
     */
    public function removeFromBlockedList($db, $id = 0)
    {
        $sql = "DELETE FROM " . TABLE_BLOCKED . " WHERE blocked_id = %d"; 
        $db->get_var($db->prepare($sql, $id));
    }
    
    
     /**
     * Check if a value is blocked
     *
     * Note: Other methods for the Blocked List can be found in the Admin class
     *
     * @param string $type - i.e. ip, url, email, user
     * @param string $value
     * @param bool $like - used for LIKE sql if true
     * @return bool
     */
    public function isBlocked($db, $type = '', $value = '', $operator = '=')
    {
        $exists = 0;
        
        // if both type and value provided...
        if ($type && $value) {
            $sql = "SELECT blocked_value FROM " . TABLE_BLOCKED . " WHERE blocked_type = %s AND blocked_value " . $operator . " %s"; 
            $exists = $db->get_var($db->prepare($sql, $type, $value));
        } 
        // if only value provided...
        elseif ($value) 
        {
            $sql = "SELECT blocked_value FROM " . TABLE_BLOCKED . " WHERE blocked_value " . $operator . " %s"; 
            $exists = $db->get_var($db->prepare($sql, $value));
        }
        
        if ($exists) { return true; } else { return false; }
    }
}
?>
