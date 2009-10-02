<?php
/**
 * Blocked List functions
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

class BlockedList
{
    public $db;                             // database object
    public $cage;                           // Inspekt object
    public $hotaru;                         // Hotaru object
    public $lang            = array();      // stores language file content

    protected $output = '';
    
    
     /**
     * Return outout as a string from teh constructor
     *
     * @return string
     */
    public function __toString()
    {
        return $this->output;
    }
    
    
     /**
     * Prepare a list of blocked items for the Admin "Blocked List" page
     */
    public function __construct($object, $execute = false, $lang_pack = 'admin')
    {
        global $pagedResults;
        
        $this->db           = $object->db;
        $this->cage         = $object->cage;
        $this->lang         = $object->lang;
        
        if(!$execute) { return false; }
        
        // if new item to block
        if ($this->cage->post->getAlpha('type') == 'new') {
            $type = $this->cage->post->testAlnumLines('blocked_type');
            $value = $this->cage->post->getMixedString2('value');
            
            if (!$value) {
                $this->hotaru->showMessage($this->lang['admin_blocked_list_empty'], 'red');
            } else {
                $this->addToBlockedList($type, $value);
            }
        }
        
        // if edit item
        if ($this->cage->post->getAlpha('type') == 'edit') {
            $id = $this->cage->post->testInt('id');
            $type = $this->cage->post->testAlnumLines('blocked_type');
            $value = $this->cage->post->getMixedString2('value');
            $this->updateBlockedList($id, $type, $value);
            $this->hotaru->showMessage($this->lang['admin_blocked_list_updated'], 'green');
        }
        
        // if remove item
        if ($this->cage->get->getAlpha('action') == 'remove') {
            $id = $this->cage->get->testInt('id');
            $this->removeFromBlockedList($id);
            $this->hotaru->showMessage($this->lang["admin_blocked_list_removed"], 'green');
        }
        
        // GET CURRENTLY BLOCKED ITEMS...
        
        $where_clause = '';
        
        // if search
        if ($this->cage->post->getAlpha('type') == 'search') {
            $search_term = $this->cage->post->getMixedString2('search_value');
            $where_clause = " WHERE blocked_value LIKE '%" . trim($this->db->escape($search_term)) . "%'";
        }
        
        // if filter
        if ($this->cage->post->getAlpha('type') == 'filter') {
            $filter = $this->cage->post->testAlnumLines('blocked_type');
            if ($filter == 'all') { $where_clause = ''; } else { $where_clause = " WHERE blocked_type = %s"; }
        }
        
        // SQL
        $sql = "SELECT * FROM " . TABLE_BLOCKED . $where_clause;

        if (isset($search_term)) { 
            $blocked_items = $this->db->get_results($sql);
        } elseif (isset($filter)) { 
            $blocked_items = $this->db->get_results($this->db->prepare($sql, $filter));
        } else {
            $blocked_items = $this->db->get_results($this->db->prepare($sql));
        }
        
        if ($blocked_items) {
            $pg = $this->cage->get->getInt('pg');
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
                        $text = $this->lang["admin_theme_blocked_url"];
                        break;
                    case 'email':
                        $text = $this->lang["admin_theme_blocked_email"];
                        break;
                    default:
                        $text = $this->lang["admin_theme_blocked_ip"];
                        break;
                }
                
                $output .= "<option value='" . $block->blocked_type . "'>" . $text . "</option>\n";
                $output .= "<option value='ip'>" . $this->lang["admin_theme_blocked_ip"] . "</option>\n";
                $output .= "<option value='url'>" . $this->lang["admin_theme_blocked_url"] . "</option>\n";
                $output .= "<option value='email'>" . $this->lang["admin_theme_blocked_email"] . "</option>\n";
                $output .= "<option value='user'>" . $this->lang["admin_theme_blocked_username"] . "</option>\n";
                $output .= "</select></td>\n";
                $output .= "<td><input type='text' size=30 name='value' value='" . $block->blocked_value . "' /></td>\n";
                $output .= "<td><input class='submit' type='submit' value='" . $this->lang['admin_blocked_list_update'] . "' /></td>\n";
                $output .= "</tr></table>\n";
                $output .= "<input type='hidden' name='id' value='" . $block->blocked_id . "' />\n";
                $output .= "<input type='hidden' name='page' value='blocked_list' />\n";
                $output .= "<input type='hidden' name='type' value='edit' />\n";
                $output .= "</form>\n";
                $output .= "</td>";
                $output .= "<td class='table_description_close'><a class='table_hide_details' href='#'>" . $this->lang["admin_theme_plugins_close"] . "</a></td>";
                $output .= "</tr>";
            }
            
            $this->output = $output;
        }
    }
    
    
     /**
     * Add to or update items 
     *
     * @return array|false
     */
    public function addToBlockedList($type = '', $value = 0, $msg = true)
    {
        $current_user = $this->getCurrentUser();
        
        $sql = "SELECT blocked_id FROM " . TABLE_BLOCKED . " WHERE blocked_type = %s AND blocked_value = %s"; 
        $id = $this->db->get_var($this->db->prepare($sql, $type, $value));
        
        if ($id) { // already exists
            if ($msg) { $this->hotaru->showMessage($this->lang['admin_blocked_list_exists'], 'red'); }
            return false;
        } 
        
        $sql = "INSERT INTO " . TABLE_BLOCKED . " (blocked_type, blocked_value, blocked_updateby) VALUES (%s, %s, %d)"; 
        $this->db->query($this->db->prepare($sql, $type, $value, $current_user->getId()));
        if ($msg) { $this->hotaru->showMessage($this->lang['admin_blocked_list_added'], 'green'); }
        
        return true;
    }
    
    
     /**
     * Add to or update items 
     *
     * @return array|false
     */
    public function updateBlockedList($id = 0, $type = '', $value = 0)
    {
        $current_user = $this->getCurrentUser();
        
        $sql = "UPDATE " . TABLE_BLOCKED . " SET blocked_type = %s, blocked_value = %s, blocked_updateby = %d WHERE blocked_id = %d"; 
        $this->db->query($this->db->prepare($sql, $type, $value, $current_user->getId(), $id));
    }
    
    
     /**
     * Remove from blocked list
     */
    public function removeFromBlockedList($id = 0)
    {
        $sql = "DELETE FROM " . TABLE_BLOCKED . " WHERE blocked_id = %d"; 
        $this->db->get_var($this->db->prepare($sql, $id));
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
    public function isBlocked($type = '', $value = '', $operator = '=')
    {
        $exists = 0;
        
        // if both type and value provided...
        if ($type && $value) {
            $sql = "SELECT blocked_value FROM " . TABLE_BLOCKED . " WHERE blocked_type = %s AND blocked_value " . $operator . " %s"; 
            $exists = $this->db->get_var($this->db->prepare($sql, $type, $value));
        } 
        // if only value provided...
        elseif ($value) 
        {
            $sql = "SELECT blocked_value FROM " . TABLE_BLOCKED . " WHERE blocked_value " . $operator . " %s"; 
            $exists = $this->db->get_var($this->db->prepare($sql, $value));
        }
        
        if ($exists) { return true; } else { return false; }
    }


    /**
     * Get Current User
     * Needed in situations where the current user is editing another user
     *
     * @return object $current_user
     */
    public function getCurrentUser() 
    {
        // Check for a cookie. If present then the user is logged in.
        $hotaru_user = $this->cage->cookie->testUsername('hotaru_user');
        
        if((!$hotaru_user) || (!$this->cage->cookie->keyExists('hotaru_key'))) { return false; }
        
        $user_info=explode(":", base64_decode($this->cage->cookie->getRaw('hotaru_key')));
        
        if (($hotaru_user != $user_info[0]) || (crypt($user_info[0], 22) != $user_info[1])) { return false; }

        $current_user->setName($hotaru_user);
        $current_user->getUserBasic(0, $current_user->getName());
        return $current_user;
    }
    
    
}

?>
