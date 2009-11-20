<?php
/**
 * The UserFunctions class contains some more useful methods for working with users
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
    
class UserFunctions extends UserBase
{    
    /**
     * Get all users with permission to access admin
     */
    public function getMods($permission = 'can_access_admin', $value = 'yes')
    {
        $sql = "SELECT * FROM " . TABLE_USERS . " WHERE (user_role = %s) || (user_role = %s) || (user_role = %s)";
        $users = $this->db->get_results($this->db->prepare($sql, 'admin', 'supermod', 'moderator'));
        
        if (!$users) { return false; }
        
        foreach ($users as $user) {
            $this->getUserBasic($user->user_id);
            if ($this->getPermission($permission) == $value) {
                $admins[$this->id]['id'] = $this->id;
                $admins[$this->id]['role'] = $this->role;
                $admins[$this->id]['name'] = $this->name;
                $admins[$this->id]['email'] = $this->email;
            }
        }
        return $admins;
    }


    /**
     * Send an email to admins/supermods chosen to receive emails about new user signups
     *
     * @param string $type - notification type, e.g. 'post', 'user', 'comment'
     * @param string $status - role or status new user, post or comment
     * @param string $id - post or user id
     * @param string $commentid - comment id
     */
    public function notifyMods($type, $status, $id = 0, $commentid = 0)
    {
        $line_break = "\r\n\r\n";
        $next_line = "\r\n";
        
        // build email ($url is the link admins can click to go to to approve the user/post/comment)
        switch ($type) {
            case 'user':
                $users_settings = $this->plugins->getSerializedSettings('users');
                $email_mods = $users_settings['users_email_notify_mods'];
                $subject = $this->lang['userfunctions_notifymods_subject_user'];
                $about = $this->lang['userfunctions_notifymods_body_about_user'];
                $url = BASEURL . "admin_index.php?page=plugin_settings&plugin=user_manager&page=plugin_settings&type=filter&user_filter=pending";
                break;
            case 'post':
                $submit_settings = $this->plugins->getSerializedSettings('submit');
                $email_mods = $submit_settings['post_email_notify_mods'];
                $subject = $this->lang['userfunctions_notifymods_subject_post'];
                $about = $this->lang['userfunctions_notifymods_body_about_post'];
                $url = BASEURL . "index.php?page=edit_post&post_id=" . $id;
                break;
            case 'comment':
                $comments_settings = $this->plugins->getSerializedSettings('comments');
                $email_mods = $comments_settings['comment_email_notify_mods'];
                $subject = $this->lang['userfunctions_notifymods_subject_comment'];
                $about = $this->lang['userfunctions_notifymods_body_about_comment'];
                $url = BASEURL . "admin_index.php?page=plugin_settings&plugin=comment_manager&page=plugin_settings&type=filter&comment_status_filter=pending";
                break;
            default:
        }
        
        // send email

        foreach ($email_mods as $mod)
        {
            if ($mod['type'] == 'none') { continue; } // skip rest of this iteration
            if (($mod['type'] == 'pending') && ($status != 'pending')) { continue; } // skip rest of this iteration
            
            $body = $this->lang['userfunctions_notifymods_hello'] . $this->getUserNameFromId($mod['id']);
            $body .= $line_break;
            $body .= $about;
            $body .= $line_break;
            $body .= $this->lang['userfunctions_notifymods_body_click'];
            $body .= $line_break;
            $body .= $url;
            $body .= $line_break;
            $body .= $this->lang['userfunctions_notifymods_body_regards'];
            $body .= $next_line;
            $body .= $this->lang['userfunctions_notifymods_body_sign'];
            $to = $mod['email'];
            $headers = "From: " . SITE_EMAIL . "\r\nReply-To: " . SITE_EMAIL . "\r\nX-Priority: 3\r\n";
        
            mail($to, $subject, $body, $headers);    
        }
        
        return true;
    }
    

    /**
     * Get the ids and names of all users or those with a specified role, sorted alphabetically
     *
     * @return array
     */
    public function userIdNameList($role = '')
    {
        if ($role) { 
            $sql = "SELECT user_id, user_username FROM " . TABLE_USERS . " WHERE user_role = %s ORDER BY user_username ASC";
            $results = $this->db->get_results($this->db->prepare($sql, $role));
        } else {
            $sql = "SELECT user_id, user_username FROM " . TABLE_USERS . $where . " ORDER BY user_username ASC";
            $results = $this->db->get_results($sql);
        }
        
        return $results;
    }
    
    
    /**
     * Get the ids and names of all users or those with a specified role, sorted alphabetically
     *
     * @return array
     */
    public function userSettingsList($userid = 0)
    {
        if ($userid) { 
            $settings = $this->getProfileSettingsData($type = 'user_settings', $userid);
            return $settings;
        } else {
            $sql = "SELECT usermeta_userid, usermeta_value FROM " . DB_PREFIX . "usermeta WHERE usermeta_key = %s";
            $results = $this->db->get_results($this->db->prepare($sql, 'user_settings'));
        }
        
        return $results;
    }
  
    
    /**
     * Get full details of all users or batches of users, sorted alphabetically
     *
     * @return array
     */
    public function userListFull($id_array = array(), $start = 0, $range = 0)
    {
        if (!$id_array) {
            // get all users
            $sql = "SELECT * FROM " . TABLE_USERS . " ORDER BY user_username ASC";
            $results = $this->db->get_results($sql);
        } else {
            // for grabbing 
            if ($range) { $limit = " LIMIT " . $start . ", " . $range; }
            $sql = "SELECT * FROM " . TABLE_USERS . " WHERE ";
            for ($i=0; $i < count($id_array); $i++) {
                $sql .= "user_id = %d OR ";
            }
            $sql = rstrtrim($sql, "OR "); // strip trailing OR
            $sql .= " ORDER BY user_username ASC" . $limit;

            $prepare_array[0] = $sql;
            $prepare_array = array_merge($prepare_array, $id_array);
            $results = $this->db->get_results($this->db->prepare($prepare_array));
        }
        return $results;
    }
    
    
    /**
     * Get a user's profile or settings data
     *
     * @return array|false
     */
    public function getProfileSettingsData($type = 'user_profile', $userid = 0, $save = false)
    {
        if (!$userid) { $userid = $this->current_user->id; }

        $query = "SELECT usermeta_value FROM " . DB_PREFIX . "usermeta WHERE usermeta_userid = %d AND usermeta_key = %s";
        $sql = $this->db->prepare($query, $userid, $type);

        if (isset($this->hotaru->vars[$sql])) { 
            $result = $this->hotaru->vars[$sql]; 
        } else {
            $result = $this->db->get_var($sql);
            $this->hotaru->vars[$sql] = $result;    // cache result
        }
        
        /* when saving, we just want to test if settings already exist. 
           Returning the defaults, looks like they exist when they don't,
           so we don't return the defaults when saving. */
           
        if ($result) { 
            $result = unserialize($result);
        } elseif (($type == 'user_settings') && !$save) { 
            $result = $this->getDefaultSettings();
        } else {
            return false;
        }
        
        return $result; 
    }
    
    /**
     * Save a user's profile or settings data
     *
     * @return array|false
     */
    public function saveProfileSettingsData($data = array(), $type = 'user_profile', $userid = 0)
    {
        if (!$data) { return false; }
        if (!$userid) { $userid = $this->current_user->id; }

        $result = $this->getProfileSettingsData($type, $userid, true);
        
        if (!$result) {
            $sql = "INSERT INTO " . TABLE_USERMETA . " (usermeta_userid, usermeta_key, usermeta_value, usermeta_updateby) VALUES(%d, %s, %s, %d)";
            $this->db->get_row($this->db->prepare($sql, $userid, $type, serialize($data), $this->current_user->id));
        } else {
            $sql = "UPDATE " . TABLE_USERMETA . " SET usermeta_value = %s, usermeta_updateby = %d WHERE usermeta_userid = %d AND usermeta_key = %s";
            $this->db->get_row($this->db->prepare($sql, serialize($data), $this->current_user->id, $userid, $type));
        }
        
        return true;
    }
    

    /**
     * Get the default user settings
     *
     * @param string $type either 'site' or 'base' (base for the originals)
     * @return array
     */
    public function getDefaultSettings($type = 'site')
    {
        if ($type == 'site') { 
            $field = 'miscdata_value'; 
        } elseif ($type == 'base') { 
            $field = 'miscdata_default';
        } else { 
            return false;
        }
        
        $query = "SELECT " . $field . " FROM " . TABLE_MISCDATA . " WHERE miscdata_key = %s";
        $sql = $this->db->prepare($query, 'user_settings');

        if (isset($this->hotaru->vars[$sql])) { 
            $result = $this->hotaru->vars[$sql]; 
        } else {
            $result = $this->db->get_var($sql);
            $this->hotaru->vars[$sql] = $result;    // cache result
        }
        
        if ($result) {
            return unserialize($result);
        } else {
            return false;
        }
    }
    
    
    /**
     * Update the default user settings
     *
     * @param array $settings 
     * @param string $type either 'site' or 'base' (base for the originals)
     * @return array
     */
    public function updateDefaultSettings($settings, $type = 'site')
    {
        if (!$settings) { return false; } else { $settings = serialize($settings); }
        
        $result = $this->getDefaultSettings($type);
        
        if (!$result) {
            // insert settings for the first time
            $sql = "INSERT INTO " . TABLE_MISCDATA . " (miscdata_key, miscdata_value, miscdata_default, miscdata_updateby) VALUES (%s, %s, %s, %d)";
            $this->db->query($this->db->prepare($sql, 'user_settings', $settings, $settings, $this->id));
        } elseif ($type == 'site') {
            // update the site defaults
            $sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_value = %s, miscdata_updateby = %d WHERE miscdata_key = %s";
            $this->db->query($this->db->prepare($sql, $settings, $this->id, 'user_settings'));
        } elseif ($type == 'base') {
            // update the base defaults
            $sql = "UPDATE " . TABLE_MISCDATA . " SET miscdata_default = %s, miscdata_updateby = %d WHERE miscdata_key = %s";
            $this->db->query($this->db->prepare($sql, $settings, $this->id, 'user_settings'));
        }
    }
    
    
    /**
     * Stats for Admin homepage
     *
     * @param string $stat_type
     * @return int
     */
    public function stats($stat_type = '')
    {
        switch ($stat_type) {
            case 'admins':
                $sql = "SELECT count(user_id) FROM " . TABLE_USERS . " WHERE user_role = %s";
                $users = $this->db->get_var($this->db->prepare($sql, 'admin'));
                break;
            case 'supermods':
                $sql = "SELECT count(user_id) FROM " . TABLE_USERS . " WHERE user_role = %s";
                $users = $this->db->get_var($this->db->prepare($sql, 'supermod'));
                break;
            case 'moderators':
                $sql = "SELECT count(user_id) FROM " . TABLE_USERS . " WHERE user_role = %s";
                $users = $this->db->get_var($this->db->prepare($sql, 'moderator'));
                break;
            case 'members':
                $sql = "SELECT count(user_id) FROM " . TABLE_USERS . " WHERE user_role = %s";
                $users = $this->db->get_var($this->db->prepare($sql, 'member'));
                break;
            case 'total_users':
                $sql = "SELECT count(user_id) FROM " . TABLE_USERS;
                $users = $this->db->get_var($sql);
                break;
            case 'approved_users':
                $sql = "SELECT count(user_id) FROM " . TABLE_USERS . " WHERE user_role = %s OR user_role = %s OR user_role = %s OR  user_role = %s";
                $users = $this->db->get_var($this->db->prepare($sql, 'admin', 'supermod', 'moderator', 'member'));
                break;
            case 'undermod_users':
                $sql = "SELECT count(user_id) FROM " . TABLE_USERS . " WHERE user_role = %s";
                $users = $this->db->get_var($this->db->prepare($sql, 'undermod'));
                break;
            case 'banned_users':
                $sql = "SELECT count(user_id) FROM " . TABLE_USERS . " WHERE user_role = %s";
                $users = $this->db->get_var($this->db->prepare($sql, 'banned'));
                break;
            case 'killspammed_users':
                $sql = "SELECT count(user_id) FROM " . TABLE_USERS . " WHERE user_role = %s";
                $users = $this->db->get_var($this->db->prepare($sql, 'killspammed'));
                break;
            default:
                $users = 0;
        }
        
        return $users;
    }
}

?>