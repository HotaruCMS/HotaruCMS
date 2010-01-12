<?php
/**
 * File: plugins/user_manager/user_manager_settings.php
 * Purpose: The functions that do the hard work such as adding, deleting and sorting categories.
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
    
class UserManagerSettings
{
    /**
     * Main function that calls others
     *
     * @return bool
     */
    public function settings($h)
    {
        // grab the number of pending users:
        $sql = "SELECT COUNT(user_id) FROM " . TABLE_USERS . " WHERE user_role = %s";
        $num_pending = $h->db->get_var($h->db->prepare($sql, 'pending'));
        if (!$num_pending) { $num_pending = "0"; } 
        $h->vars['num_pending'] = $num_pending; 
        
        
        // check if all new users are automatically set to pending or not
        $user_signin_settings = $h->getSerializedSettings('user_signin');
        $h->vars['regStatus'] = $user_signin_settings['registration_status'];
        $h->vars['useEmailConf'] = $user_signin_settings['emailconf_enabled'];
            
        // clear variables:
        $h->vars['search_term'] = '';
        if ($h->vars['regStatus'] == 'pending') { 
            $h->vars['user_filter'] = 'pending';
        } else {
            $h->vars['user_filter'] = 'all';
        }
        
        // Get unique statuses for Filter form:
        $h->vars['roles'] = $h->getUniqueRoles(); 
        
        $u = new UserBase();
        
        // if checkboxes
        if (($h->cage->get->getAlpha('type') == 'checkboxes') && ($h->cage->get->keyExists('user_man'))) 
        {
            foreach ($h->cage->get->keyExists('user_man') as $id => $checked) {
                $h->message = $h->lang["user_man_checkboxes_role_changed"]; // default "Changed role" message
                $u->id = $id;
                $u->getUserBasic($h, $id);
                $new_role = $h->cage->get->testAlnumLines('checkbox_action');
                if ($new_role != $u->role) { 
                    // change role:
                    $u->role = $new_role;
                    $new_perms = $u->getDefaultPermissions($h, $new_role);
                    $u->setAllPermissions($h, $new_perms);
                    $u->updatePermissions($h);
                    $u->updateUserBasic($h, $id);
                    $h->message = $h->lang["user_man_checkboxes_role_changed"];
                    
                    if ($new_role == 'killspammed' || $new_role == 'deleted') {
                        $h->deleteComments($u->id); // includes child comments from *other* users
                        $h->deletePosts($u->id); // includes tags and votes for self-submitted posts
                        if ($h->cage->get->keyExists('addblockedlist')) { 
                            $h->addToBlockedList($type = 'user', $value = $u->name, false);
                            $h->addToBlockedList($type = 'email', $value = $u->email, false);
                        }
                        $h->pluginHook('user_man_killspam_delete', '', array($u));
                        if ($new_role == 'deleted') { $u->deleteUser($h); }
                    }
                }
                
            }
        }
        
        
        // if search
        $search_term = '';
        if ($h->cage->get->getAlpha('type') == 'search') {
            $search_term = $h->cage->get->getMixedString2('search_value');        
            if (strlen($search_term) < 3) {
                $h->message = $h->lang["user_man_search_too_short"];
                $h->messageType = 'red';
            } else {
                $h->vars['search_term'] = $search_term; // used to refill the search box after a search
                $where_clause = " WHERE user_username LIKE %s OR user_email LIKE %s"; 
                $sort_clause = ' ORDER BY user_lastlogin DESC'; // ordered last logged in for convenience
                $sql = "SELECT * FROM " . TABLE_USERS . $where_clause . $sort_clause;
                $search_term = '%' . $search_term . '%';
                $results = $h->db->get_results($h->db->prepare($sql, $search_term, $search_term)); 
            }
            
            if (isset($results)) { $users = $results; } else {  $users = array(); }
        }
        
        
        // if filter
        $filter = '';
        if ($h->cage->get->getAlpha('type') == 'filter') {
            $filter = $h->cage->get->testAlnumLines('user_filter');
            $h->vars['user_filter'] = $filter;  // used to refill the filter box after use
            switch ($filter) {
                case 'all': 
                    $sort_clause = ' ORDER BY user_lastlogin DESC'; // ordered last logged in for convenience
                    $sql = "SELECT * FROM " . TABLE_USERS . $sort_clause;
                    $filtered_results = $h->db->get_results($h->db->prepare($sql)); 
                    break;
                case 'not_killspammed': 
                    $where_clause = " WHERE user_role != %s"; 
                    $sort_clause = ' ORDER BY user_lastlogin DESC'; // ordered last logged in for convenience
                    $sql = "SELECT * FROM " . TABLE_USERS . $where_clause . $sort_clause;
                    $filtered_results = $h->db->get_results($h->db->prepare($sql, 'killspammed')); 
                    break;
                case 'admin': 
                    $where_clause = " WHERE user_role = %s"; 
                    $sort_clause = ' ORDER BY user_lastlogin DESC'; // ordered last logged in for convenience
                    $sql = "SELECT * FROM " . TABLE_USERS . $where_clause . $sort_clause;
                    $filtered_results = $h->db->get_results($h->db->prepare($sql, 'admin')); 
                    break;
                case 'supermod': 
                    $where_clause = " WHERE user_role = %s"; 
                    $sort_clause = ' ORDER BY user_lastlogin DESC'; // ordered last logged in for convenience
                    $sql = "SELECT * FROM " . TABLE_USERS . $where_clause . $sort_clause;
                    $filtered_results = $h->db->get_results($h->db->prepare($sql, 'mod')); 
                    break;
                case 'moderator': 
                    $where_clause = " WHERE user_role = %s"; 
                    $sort_clause = ' ORDER BY user_lastlogin DESC'; // ordered last logged in for convenience
                    $sql = "SELECT * FROM " . TABLE_USERS . $where_clause . $sort_clause;
                    $filtered_results = $h->db->get_results($h->db->prepare($sql, 'mod')); 
                    break;
                case 'member': 
                    $where_clause = " WHERE user_role = %s"; 
                    $sort_clause = ' ORDER BY user_lastlogin DESC'; // ordered last logged in for convenience
                    $sql = "SELECT * FROM " . TABLE_USERS . $where_clause . $sort_clause;
                    $filtered_results = $h->db->get_results($h->db->prepare($sql, 'member')); 
                    break;
                case 'pending': 
                    $where_clause = " WHERE user_role = %s"; 
                    $sort_clause = ' ORDER BY user_lastlogin DESC'; // ordered last logged in for convenience
                    $sql = "SELECT * FROM " . TABLE_USERS . $where_clause . $sort_clause;
                    $filtered_results = $h->db->get_results($h->db->prepare($sql, 'pending')); 
                    break;
                case 'undermod': 
                    $where_clause = " WHERE user_role = %s"; 
                    $sort_clause = ' ORDER BY user_lastlogin DESC'; // ordered last logged in for convenience
                    $sql = "SELECT * FROM " . TABLE_USERS . $where_clause . $sort_clause;
                    $filtered_results = $h->db->get_results($h->db->prepare($sql, 'undermod')); 
                    break;
                case 'suspended': 
                    $where_clause = " WHERE user_role = %s"; 
                    $sort_clause = ' ORDER BY user_lastlogin DESC'; // ordered last logged in for convenience
                    $sql = "SELECT * FROM " . TABLE_USERS . $where_clause . $sort_clause;
                    $filtered_results = $h->db->get_results($h->db->prepare($sql, 'suspended')); 
                    break;
                case 'banned': 
                    $where_clause = " WHERE user_role = %s"; 
                    $sort_clause = ' ORDER BY user_lastlogin DESC'; // ordered last logged in for convenience
                    $sql = "SELECT * FROM " . TABLE_USERS . $where_clause . $sort_clause;
                    $filtered_results = $h->db->get_results($h->db->prepare($sql, 'banned')); 
                    break;
                case 'killspammed': 
                    $where_clause = " WHERE user_role = %s"; 
                    $sort_clause = ' ORDER BY user_lastlogin DESC'; // ordered last logged in for convenience
                    $sql = "SELECT * FROM " . TABLE_USERS . $where_clause . $sort_clause;
                    $filtered_results = $h->db->get_results($h->db->prepare($sql, 'killspammed')); 
                    break;
                case 'newest':
                    $sort_clause = ' ORDER BY user_date DESC';  // same as "all"
                    $sql = "SELECT * FROM " . TABLE_USERS . $sort_clause;
                    $filtered_results = $h->db->get_results($h->db->prepare($sql)); 
                    break;
                case 'oldest':
                    $sort_clause = ' ORDER BY user_date ASC';
                    $sql = "SELECT * FROM " . TABLE_USERS . $sort_clause;
                    $filtered_results = $h->db->get_results($h->db->prepare($sql)); 
                    break;
                default:
                    $where_clause = " WHERE user_role = %s"; $sort_clause = ' ORDER BY user_lastlogin DESC'; // ordered newest first for convenience
                    $sql = "SELECT * FROM " . TABLE_USERS . $where_clause . $sort_clause;
                    $filtered_results = $h->db->get_results($h->db->prepare($sql, $filter)); // filter = new, top, or other post status
                    break;
            }
            
            if (isset($filtered_results)) { $users = $filtered_results; } else {  $users = array(); }
        }

        if(!isset($users)) {
            // default list
            
            // if all new users are set to 'pending' show pending list as default...
            if ($h->vars['regStatus'] == 'pending') {
                $where_clause = " WHERE user_role = %s"; 
                $sort_clause = ' ORDER BY user_lastlogin DESC';
                $sql = "SELECT * FROM " . TABLE_USERS . $where_clause . $sort_clause;
                $users = $h->db->get_results($h->db->prepare($sql, 'pending')); 
            }
            // else show all users by last login...
            else
            {
                $sort_clause = ' ORDER BY user_lastlogin DESC'; // ordered by lastlogin for convenience
                $sql = "SELECT * FROM " . TABLE_USERS . $sort_clause;
                $users = $h->db->get_results($h->db->prepare($sql)); 
            }
        }
        
        if ($users) { 
            $h->vars['user_man_rows'] = $this->drawRows($h, $users, $filter, $search_term);
        } elseif ($h->vars['user_filter'] == 'pending') {
            $h->message = $h->lang['user_man_no_pending_users'];
            $h->messageType = 'green';
        }
        
        // Show template:
        $h->displayTemplate('user_man_main', 'user_manager');
    }
    
    
    public function drawRows($h, $users, $filter = '', $search_term = '')
    {
        // prepare for showing posts, 20 per page
        $pg = $h->cage->get->getInt('pg');
        $items = 20;
        
        $pagedResults = $h->pagination($users, $items, $pg);
        
        $output = "";
        $alt = 0;
        while($user = $pagedResults->fetchPagedRow()) {    //when $story is false loop terminates    
            $alt++;

            $account_link = BASEURL . "index.php?page=account&amp;user=" . $user->user_username; 
            $perms_link = BASEURL . "index.php?page=permissions&amp;user=" . $user->user_username; 
            if ($user->user_role == 'admin') { $disable = 'disabled'; } else { $disable = ''; } 
            
            $output .= "<tr class='table_row_" . $alt % 2 . "'>\n";
            $output .= "<td class='um_id'>" . $user->user_id . "</td>\n";
            $output .= "<td class='um_role'>" . $user->user_role . "</td>\n";
            $output .= "<td class='um_username'><a class='table_drop_down' href='#' title='" . $h->lang["user_man_show_content"] . "'>";
            $output .= $user->user_username . "</a></td>\n";
            $output .= "<td class='um_joined'>" . date('d M y', strtotime($user->user_date)) . "</a></td>\n";
            $output .= "<td class='um_account'>" . "<a href='" . $account_link . "'>" . $h->lang["user_man_account"] . "</a>\n";
            $output .= "<td class='um_perms'>" . "<a href='" . $perms_link . "'>" . $h->lang["user_man_perms"] . "</a>\n";
            $output .= "<td class='um_check'><input type='checkbox' name='user_man[" . $user->user_id . "]' value='" . $user->user_id . "' " . $disable . "></td>\n";
            $output .= "</tr>\n";

            $output .= "<tr class='table_tr_details' style='display:none;'>\n";
            $output .= "<td colspan=7 class='table_description um_description'>\n";
            $output .= "<a class='table_hide_details' style='float: right;' href='#'>[" . $h->lang["admin_theme_plugins_close"] . "]</a>";
            
            if ($user->user_role == 'pending') { 
                // show register date info:
                $output .= $user->user_username . " " . $h->lang["user_man_user_registered_on"] ." " . date('H:i:s \o\n l, F jS Y', strtotime($user->user_date));
                if ($h->vars['useEmailConf']) {
                    if ($user->user_email_valid == 0) {
                        $output .= $h->lang["user_man_user_email_not_validated"] . "\n";
                    } else {
                        $output .= $h->lang["user_man_user_email_validated"] . "\n";
                    }
                }
                
                // plugin hook (StopSpam plugin adds a note about whya user is pending)
                $h->vars['user_manager_pending'] = array($output, $user);
                $h->pluginHook('user_manager_details_pending');
                $output = $h->vars['user_manager_pending'][0]; // $output
                $output .= "<br />";
                
            } else {
                // show last login amd submissions info:
                $output .= $user->user_username . " " . $h->lang["user_man_user_last_logged_in"] ." " . date('H:i:s \o\n l, F jS Y', strtotime($user->user_lastlogin)) . ".<br />\n";
            $output .= $h->lang["user_man_user_submissions_1"] . " " . $user->user_username . $h->lang["user_man_user_submissions_2"] . " <a href='" . $h->url(array('user'=>$user->user_username)) . "'>" . $h->lang['user_man_here'] . ".</a><br />\n";
            }
    
            $output .= "<i>" . $h->lang['user_man_email'] . "</i> <a href='mailto:" . $user->user_email . "'>$user->user_email</a>";
            $output .= "</td></tr>";
        }
        
        if ($pagedResults) {
            $h->vars['user_man_navi'] = $h->pageBar($pagedResults);
        }
        
        return $output;
    }
}
?>